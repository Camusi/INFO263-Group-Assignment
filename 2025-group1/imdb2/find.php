<?php
session_start();
ini_set('max_execution_time', 90); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>"<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"" | IMDB2</title>
    <link rel="stylesheet" href="resources/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="resources/search.js"></script>

</head>
<body>
  <header class="header">
    <h1>IMDB2.0</h1>
    <p>Results for "<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ' '; ?>"</p>
  </header>

<?php include 'resources/navbar.php'; ?>
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
                $maxResults = 8;
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
                  echo "<button id='view-more-btn'>View 8 More</button>";
                  ?>
                  <script>
                    const allResults = <?php echo json_encode($results); ?>;
                    let shown = <?php echo $maxResults; ?>;
                    document.getElementById("view-more-btn").onclick = function() {
                      let html = "";
                      for (let i = shown; i < shown + <?php echo $maxResults; ?> && i < allResults.length; i++) {
                        let row = allResults[i];
                        html += `<div class="result-item">`;
                        html += `<strong>Name:</strong> <a href="./resources/page.php?q=${row.id}" target="_blank">${row.primary_name.replace(/</g,"&lt;").replace(/>/g,"&gt;")}</a><br>`;
                        html += `<strong>Type:</strong> ${(row.table_name === "title_basics_trim" ? "TV/Movie" : "Person")}<br>`;
                        html += `<strong> ${(row.table_name === "title_basics_trim" ? "Year:" : "Born:")} </strong> ${row.year ? String(row.year).replace(/</g,"&lt;").replace(/>/g,"&gt;") : ""}<br>`;
                        if (row.table_name === "title_basics_trim") {
                          html += `<img data-imdb-id="${row.id}" class="cover-image" width="100" src="resources/img/load.gif" alt="Loading..." /><br>`;
                        }
                        html += `</div><br><hr><br>`;
                      }
                      shown += <?php echo $maxResults; ?>;
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
                            async: true,
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
                  </script>
                  <script>
                    document.getElementById("view-more-btn").addEventListener("click", function () {
                      const button = this;
                      const loadingImg = document.createElement("img");
                      loadingImg.src = "resources/img/load.gif";
                      loadingImg.alt = "Loading...";
                      loadingImg.width = 24;
                      loadingImg.height = 24;
                      button.parentNode.replaceChild(loadingImg, button);
                      setTimeout(function() {
                        loadingImg.parentNode.replaceChild(button, loadingImg);
                      }, 2000); // Adjust the delay time as needed
                    });
                  </script>
                  <?php
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