<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>"<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"" | IMDB2</title>
  <link rel="stylesheet" href="resources/style.css" />
</head>
<body>
  <header class="header">
    <h1>IMDB2.0</h1>
    <p>Results for "<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"</p>
  </header>

<nav class="navbar">
    <div class="nav-left">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
    </div>
    <div class="nav-right">
      <a href="signin.php">Sign In</a>
      <p class="account-preview">👤 Guest</p>
    </div>
  </nav>
  <section class="search-bar">
  <form action="find.php" method="GET">
    <input type="text" id="find-search-input" name="q" placeholder="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
    <button id="find-search-button" type="submit">Search</button>
  </form>
  </section>
  <main class="query-results">
    <p id="search-output">
        <?php
        if (isset($_GET['q'])) {
            $searchQuery = htmlspecialchars($_GET['q']);
            echo "<h4>You searched for: " . "{$searchQuery}" . "</h4><br><br>";
            try {
                // Connect to SQLite database
                $db = new PDO('sqlite:./resources/imdb-2.sqlite3');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Prepare SQL for both tables, include table name and primary field
                $sql = "
                    SELECT tconst AS id, primaryTitle AS primary_name, 'title_basics_trim' AS table_name, startYear AS year
                    FROM title_basics_trim
                    WHERE primaryTitle LIKE :query
                    UNION ALL
                    SELECT nconst AS id, primaryName AS primary_name, 'name_basics_trim' AS table_name, birthYear AS year
                    FROM name_basics_trim
                    WHERE primaryName LIKE :query
                ";

                $stmt = $db->prepare($sql);
                $likeQuery = "%{$searchQuery}%";
                $stmt->bindValue(':query', $likeQuery, PDO::PARAM_STR);
                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "Found " . count($results) . " results:<br><br>";
                foreach ($results as $row) {
                    echo "<div class='result-item'>";
                    echo "<strong>Name:</strong> " . htmlspecialchars($row['primary_name']) . "<br>";
                    echo "<strong>Type:</strong> " . ($row['table_name'] === 'title_basics_trim' ? 'TV/Movie' : 'Person') . "<br>";
                    echo "<strong> ". ($row['table_name'] === 'title_basics_trim' ? 'Year:' : 'Born:') ." </strong> " . htmlspecialchars($row['year']) . "<br>";
                    if ($row['table_name'] === 'title_basics_trim') {
                        // Fetch the image URL from cover-image.php?q=ID (returns JSON)
                        $coverApiUrl = "resources/cover-image.php?q=" . urlencode($row['id']);
                        $coverJson = @file_get_contents($coverApiUrl);
                        if ($coverJson !== false) {
                            $coverData = json_decode($coverJson, true);
                            if (
                                json_last_error() === JSON_ERROR_NONE &&
                                isset($coverData['cover_image']) &&
                                !empty($coverData['cover_image']) &&
                                filter_var($coverData['cover_image'], FILTER_VALIDATE_URL)
                            ) {
                                echo "<img src='" . htmlspecialchars($coverData['cover_image']) . "' alt='Cover Image'><br>";
                            }
                        }
                    }
                    echo "</div><br><hr><br>";
                }
            } catch (Exception $e) {
                echo "Database error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "No search query provided.";
        }
        ?>
    </p>
  </main>

  <footer class="footer">
    <p>&copy; 2025 Test Website. All rights Test.</p>
  </footer>
</body>
</html>