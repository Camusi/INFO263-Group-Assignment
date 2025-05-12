<?php
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 60); // 1 minute
set_time_limit(120);   // 2 minutes           
error_reporting(E_ALL);         // Enable error reporting
ini_set('display_errors', 1);

// Require supp code
require_once 'connection.php';
require_once './objects/ArrayValue.php';
require_once './objects/Title.php';

function openConnection(): PDO
{
    try {
        $pdo = new PDO(
            CONNECTION_STRING,
            CONNECTION_USER,
            CONNECTION_PASSWORD,
            CONNECTION_OPTIONS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_TIMEOUT, 10); // 🆕 Add this
        $pdo->exec("PRAGMA busy_timeout = 10000");  // 🆕 Add this
        $pdo->exec("PRAGMA journal_mode = WAL");    // 🆕 Add this
        $pdo->exec("PRAGMA synchronous = NORMAL");  // 🆕 Recommended
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    return $pdo;
}

function getTitles($page, $pageSize, $title)
{
    $offset = ($page - 1) * $pageSize;

    $query = "SELECT t.tconst AS id, titleType AS title_type, primaryTitle AS primary_title, 
                     originalTitle AS original_title, isAdult AS is_adult, startYear AS start_year, 
                     endYear AS end_year, runtimeMinutes AS runtime_minutes, t.genres, 
                     r.averageRating AS rating, r.numVotes AS votes
              FROM title_basics_trim t
              JOIN title_ratings_trim r ON r.tconst = t.tconst
              WHERE 1 = 1 ";

    if (!empty($title)) {
        $query .= "AND (primaryTitle LIKE :title OR originalTitle LIKE :title) ";
    }

    $query .= "LIMIT :pageSize OFFSET :offset";

    try {
        $pdo = openConnection();
        $stmt = $pdo->prepare($query);

        if (!empty($title)) {
            $title = "%" . $title . "%";
            $stmt->bindParam(':title', $title);
        }

        $stmt->bindParam(':pageSize', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Title::class);
    } catch (PDOException $e) {
        die("Error fetching paginated titles: " . $e->getMessage());
    }
}

function getTitleCount($title)
{
    $query = "SELECT COUNT(*) AS title_count
              FROM title_basics_trim t
              JOIN title_ratings_trim r ON r.tconst = t.tconst
              WHERE 1 = 1 ";

    if (!empty($title)) {
        $query .= "AND (primaryTitle LIKE :title OR originalTitle LIKE :title) ";
    }

    try {
        $pdo = openConnection();
        $stmt = $pdo->prepare($query);

        if (!empty($title)) {
            $title = "%" . $title . "%";
            $stmt->bindParam(':title', $title);
        }

        $stmt->execute();
        $row = $stmt->fetch();

        return $row["title_count"];
    } catch (PDOException $e) {
        die("Error fetching title count: " . $e->getMessage());
    }
}

function createGenres($pdo): void
{
    $pdo->exec("DROP TABLE IF EXISTS genres;");
    $pdo->exec("CREATE TABLE genres (genre_id INTEGER PRIMARY KEY AUTOINCREMENT, genre_name TEXT UNIQUE);");

    $batchSize = 250;
    $offset = 0;

    $insert = $pdo->prepare("INSERT OR IGNORE INTO genres (genre_name) VALUES (:genre)");

    while (true) {
        $stmt = $pdo->prepare("SELECT genres FROM title_basics_trim WHERE genres IS NOT NULL LIMIT :batchSize OFFSET :offset");
        $stmt->bindValue(':batchSize', $batchSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) break;

        $pdo->beginTransaction();
        $seen = [];
        foreach ($rows as $row) {
            $genres = explode(',', $row['genres']);
            foreach ($genres as $genre) {
                $trimmed = trim($genre);
                if ($trimmed !== '' && !isset($seen[$trimmed])) {
                    $insert->execute([':genre' => $trimmed]);
                    $seen[$trimmed] = true;
                }
            }
        }
        $pdo->commit();

        $offset += $batchSize;
    }
    createTitleGenre($pdo);
}

function createTitleGenre($pdo): void
{
    $pdo->exec("DROP TABLE IF EXISTS title_genre;");
    $pdo->exec(file_get_contents("qryCreateTGenre.sql"));

    populateTitleGenre($pdo);
}

function populateTitleGenre($pdo): void
{
    $pdo->exec(file_get_contents("qryPopulateTGenre.sql"));
}

function createProfession(PDO $pdo): void
{
    $pdo->exec("DROP TABLE IF EXISTS profession;");
    $pdo->exec("DROP TABLE IF EXISTS name_profession;");
    $pdo->exec(file_get_contents('qryCreateProfession.sql'));
    $pdo->exec(file_get_contents('qryCreateNamePro.sql'));

    $batchSize = 250;
    $lastNconst = ''; // Start from the beginning

    $insertProfession = $pdo->prepare("INSERT OR IGNORE INTO profession (name) VALUES (:name)");
    $selectProfession = $pdo->prepare("SELECT id FROM profession WHERE name = :name");
    $insertRelation = $pdo->prepare("INSERT OR IGNORE INTO name_profession (name_id, profession_id) VALUES (:name_id, :profession_id)");

    $selectBatch = $pdo->prepare("
        SELECT nconst, primaryProfession
        FROM name_basics_trim
        WHERE primaryProfession IS NOT NULL AND nconst > :lastNconst
        ORDER BY nconst ASC
        LIMIT :batchSize
    ");

    while (true) {
        $selectBatch->bindValue(':lastNconst', $lastNconst, PDO::PARAM_STR);
        $selectBatch->bindValue(':batchSize', $batchSize, PDO::PARAM_INT);
        $selectBatch->execute();

        $rows = $selectBatch->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) break;

        $pdo->beginTransaction();

        foreach ($rows as $row) {
            $professions = explode(',', $row['primaryProfession']);
            foreach ($professions as $profession) {
                $profession = trim($profession);
                if ($profession === '') continue;

                $insertProfession->execute([':name' => $profession]);

                $selectProfession->execute([':name' => $profession]);
                $profession_id = $selectProfession->fetchColumn();

                if ($profession_id !== false) {
                    $insertRelation->execute([
                        ':name_id' => $row['nconst'],
                        ':profession_id' => $profession_id
                    ]);
                }
            }

            // Update checkpoint
            $lastNconst = $row['nconst'];
        }

        $pdo->commit();

        // For debug:
        // echo "Processed up to nconst: $lastNconst" . PHP_EOL;
    }
}
function indexDB($pdo): void
{
    $pdo->exec(file_get_contents('qryIndex.sql'));
}

// Main execution

// Connection
//$pdo = openConnection();

// Create tables and index
//createGenres($pdo);
//createProfession($pdo);
//indexDB($pdo);
