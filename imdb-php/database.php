<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'connection.php';
require_once './objects/ArrayValue.php';
require_once './objects/Title.php';

/**
 * Create connection to the database
 *
 * @return PDO (PHP Data Objects) provides access to the database
 */
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
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    return $pdo;
}

/**
 * Fetch paginated titles
 */
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

/**
 * Get total number of titles for pagination
 */
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

/**
 * Create genres table with pagination processing
 */
function createGenres(): void
{
    $pdo = openConnection();

    // Drop and create the 'genres' table
    $pdo->exec("DROP TABLE IF EXISTS genres;");
    $pdo->exec("CREATE TABLE genres (genre_id INTEGER PRIMARY KEY AUTOINCREMENT, genre_name TEXT UNIQUE);");

    // Fetch genres in batches
    $batchSize = 1000;
    $offset = 0;

    while (true) {
        $stmt = $pdo->prepare("SELECT genres FROM title_basics_trim WHERE genres IS NOT NULL LIMIT :batchSize OFFSET :offset");
        $stmt->bindValue(':batchSize', $batchSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) break;

        $allGenres = [];
        foreach ($rows as $row) {
            $genres = explode(',', $row['genres']);
            foreach ($genres as $genre) {
                $trimmed = trim($genre);
                if ($trimmed !== '' && !in_array($trimmed, $allGenres)) {
                    $allGenres[] = $trimmed;
                }
            }
        }

        // Insert genres
        $insert = $pdo->prepare("INSERT OR IGNORE INTO genres (genre_name) VALUES (:genre)");
        foreach ($allGenres as $genre) {
            $insert->execute([':genre' => $genre]);
        }

        $offset += $batchSize;
    }
}

/**
 * Create profession table with batch processing
 */
function createProfession(): void
{
    $pdo = openConnection();

    // Drop and create the tables
    $pdo->exec("DROP TABLE IF EXISTS profession;");
    $pdo->exec("DROP TABLE IF EXISTS name_profession;");
    $pdo->exec(file_get_contents('qryCreateProfession.sql'));
    $pdo->exec(file_get_contents('qryCreateNamePro.sql'));

    $batchSize = 1000;
    $offset = 0;

    while (true) {
        $stmt = $pdo->prepare("SELECT nconst, primaryProfession FROM name_basics_trim LIMIT :batchSize OFFSET :offset");
        $stmt->bindValue(':batchSize', $batchSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) break;

        foreach ($rows as $row) {
            processProfessions($pdo, $row['nconst'], $row['primaryProfession']);
        }

        $offset += $batchSize;
    }
}

/**
 * Process professions separately to minimize memory usage
 */
function processProfessions($pdo, $name_id, $primaryProfession)
{
    $professions = explode(',', $primaryProfession);

    foreach ($professions as $profession) {
        $profession = trim($profession);

        // Insert unique profession
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO profession (name) VALUES (:name)");
        $stmt->execute([':name' => $profession]);

        // Fetch profession ID
        $stmt = $pdo->prepare("SELECT id FROM profession WHERE name = :name");
        $stmt->execute([':name' => $profession]);
        $profession_id = $stmt->fetchColumn();

        if ($profession_id === false) {
            return;
        }

        // Insert into name_profession table
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO name_profession (name_id, profession_id) VALUES (:name_id, :profession_id)");
        $stmt->execute([
            ':name_id' => $name_id,
            ':profession_id' => $profession_id
        ]);
    }
}

/** Execute table creation */
createGenres();
createProfession();