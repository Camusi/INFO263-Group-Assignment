<?php ini_set('max_execution_time', 90); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>"<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"" | IMDB2</title>
  <link rel="stylesheet" href="resources/style.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
  <header class="header">
    <h1>IMDB2.0</h1>
    <p>Results for "<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ' '; ?>"</p>
  </header>

<?php include 'resources/navbar.php'; ?>
  <section class="search-bar">
  <form action="find.php" method="GET">
    <input type="text" id="find-search-input" name="q" placeholder="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
    <button id="find-search-button" type="submit">Search</button>
    <script>
      document.querySelector('form').addEventListener('submit', function(e) {
      var input = document.getElementById('find-search-input').value.trim();
      if (!input) {
        e.preventDefault();
        location.href = location.pathname;
      } else {
        e.preventDefault();
        location.href = location.pathname + '?q=' + encodeURIComponent(input);
      }
      });
    </script>
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
                $maxResults = 5;
                foreach ($results as $i => $row) {
                  if ($i >= $maxResults) break;
                  echo "<div class='result-item'>";
                  echo "<strong>Name:</strong> <a href=\"./resources/page.php?q=" . ($row['id']) . "\" target=\"_blank\">" . htmlspecialchars($row['primary_name']) . "</a><br>";
                  echo "<strong>Type:</strong> " . ($row['table_name'] === 'title_basics_trim' ? 'TV/Movie' : 'Person') . "<br>";
                  echo "<strong> ". ($row['table_name'] === 'title_basics_trim' ? 'Year:' : 'Born:') ." </strong> " . htmlspecialchars($row['year']) . "<br>";
                  if ($row['table_name'] === 'title_basics_trim') {
                    echo "<img data-imdb-id=\"" . htmlspecialchars($row['id']) . "\" class=\"cover-image\" width=\"100\" src=\"resources/img/load.gif\" alt=\"Loading...\" /><br>";
                  }
                  echo "</div><br><hr><br>";
                }
                if (count($results) > $maxResults) {
                  echo "<button id='view-more-btn'>View More</button>";
                  echo '<script>
                    const allResults = ' . json_encode($results) . ';
                    let shown = ' . $maxResults . ';
                    document.getElementById("view-more-btn").onclick = function() {
                      let html = "";
                      for (let i = shown; i < shown + ' . $maxResults . ' && i < allResults.length; i++) {
                        let row = allResults[i];
                        html += `<div class="result-item">`;
                        html += `<strong>Name:</strong> <a href="./resources/page.php?q=${row.id}" target="_blank">${row.primary_name.replace(/</g,"&lt;").replace(/>/g,"&gt;")}</a><br>`;
                        html += `<strong>Type:</strong> ${(row.table_name === "title_basics_trim" ? "TV/Movie" : "Person")}<br>`;
                        html += `<strong> ${(row.table_name === "title_basics_trim" ? "Year:" : "Born:")} </strong> ${row.year ? row.year.replace(/</g,"&lt;").replace(/>/g,"&gt;") : ""}<br>`;
                        if (row.table_name === "title_basics_trim") {
                          html += `<img data-imdb-id="${row.id}" class="cover-image" width="100" src="resources/img/load.gif" alt="Loading..." /><br>`;
                        }
                        html += `</div><br><hr><br>`;
                      }
                      shown += ' . $maxResults . ';
                      document.getElementById("view-more-btn").insertAdjacentHTML("beforebegin", html);
                      if (shown >= allResults.length) {
                        document.getElementById("view-more-btn").remove();
                      }
                      // Re-trigger image loading for new images
                      document.querySelectorAll("img[data-imdb-id]").forEach(img => {
                        if (!img.src.includes("cover_image")) {
                          $.ajax({
                            url: `resources/cover-image.php?q=${img.getAttribute("data-imdb-id")}`,
                            method: "GET",
                            dataType: "json",
                            async: false,
                            success: function (imgData) {
                              if (imgData && imgData.cover_image) {
                                img.src = imgData.cover_image;
                              }
                            },
                            error: function () {
                              img.src = "resources/img/load.gif";
                            }
                          });
                        }
                      });
                    };
                  </script>';
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

<br><?php include 'resources/footer.php'; ?>
</body>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const images = document.querySelectorAll("img[data-imdb-id]");
  images.forEach(img => {
    $.ajax({
      url: `resources/cover-image.php?q=${img.getAttribute('data-imdb-id')}`,
      method: 'GET',
      dataType: 'json',
      async: false,
      success: function (imgData) {
        if (imgData && imgData.cover_image) {
          img.src = imgData.cover_image;
          console.log("found cover image for " + img.getAttribute('data-imdb-id') + " at " + imgData.cover_image);
        }
      },
      error: function () {
        img.src = "resources/img/load.gif";
      }
    });
  });
});
</script>

</html>