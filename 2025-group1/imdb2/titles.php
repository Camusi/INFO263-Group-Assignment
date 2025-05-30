<?php
session_start();
ini_set('max_execution_time', 90);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Featured Titles | IMDB2</title>
    <link rel="stylesheet" href="resources/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="resources/search.js"></script>
</head>
<body>
<header class="header">
    <h1>IMDB2.0</h1>
    <p>Some featured titles from our database</p>
</header>

<?php include 'resources/navbar.php'; ?>
<div class="search-results">
    <p id="search-output"></p>
</div>

<main>
    <h2 style="text-align: center; margin: 2rem;">Featured Titles</h2>
    <div class="title-query-results" id="results-container">
        <?php
        try {
            $db = new PDO('sqlite:./resources/imdb-2.sqlite3');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "
                SELECT tconst AS id, primaryTitle AS primary_name, startYear AS year
                FROM title_basics_trim
                WHERE primaryTitle IS NOT NULL
                ORDER BY startYear ASC
            ";
            $stmt = $db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $maxResults = 6;
            $totalResults = count($results);

            if ($totalResults > 0) {
                for ($i = 0; $i < min($maxResults, $totalResults); $i++) {
                    $row = $results[$i];
                    echo "<div class='result-item'>";
                    echo "<strong>Name:</strong> <a href=\"./resources/page.php?q=" . htmlspecialchars($row['id']) . "\" target=\"_blank\">" . htmlspecialchars($row['primary_name']) . "</a><br>";
                    echo "<strong>Year:</strong> " . htmlspecialchars($row['year']) . "<br>";
                    echo "<img data-imdb-id=\"" . htmlspecialchars($row['id']) . "\" class=\"cover-image\" src=\"resources/img/load.gif\" alt=\"Loading...\" />";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align:center;'>No titles found.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='text-align:center;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
    <?php if ($totalResults > $maxResults): ?>
        <button id="title-view-more-btn">View 6 More</button>
    <?php endif; ?>
</main>

<br><?php include 'resources/footer.php'; ?>
</body>

<script>
    const allResults = <?php echo json_encode($results); ?>;
    let shown = <?php echo $maxResults; ?>;
    const maxResults = <?php echo $maxResults; ?>;

    document.addEventListener("DOMContentLoaded", function () {
        function loadImages() {
            document.querySelectorAll("img[data-imdb-id]").forEach(img => {
                if (!img.src.includes("cover_image")) {
                    $.ajax({
                        url: `resources/cover-image.php?q=${img.getAttribute("data-imdb-id")}`,
                        method: "GET",
                        dataType: "json",
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
        }

        loadImages();

        const viewMoreBtn = document.getElementById("view-more-btn");
        const container = document.getElementById("results-container");

        if (viewMoreBtn) {
            viewMoreBtn.addEventListener("click", function () {
                let html = "";
                for (let i = shown; i < shown + maxResults && i < allResults.length; i++) {
                    const row = allResults[i];
                    html += `
                        <div class="result-item">
                            <strong>Name:</strong> <a href="./resources/page.php?q=${row.id}" target="_blank">${row.primary_name.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</a><br>
                            <strong>Year:</strong> ${row.year ? String(row.year).replace(/</g, "&lt;").replace(/>/g, "&gt;") : ""}<br>
                            <img data-imdb-id="${row.id}" class="cover-image" src="resources/img/load.gif" alt="Loading..." />
                        </div>
                    `;
                }

                container.insertAdjacentHTML("beforeend", html);
                shown += maxResults;

                if (shown >= allResults.length) {
                    viewMoreBtn.remove();
                }

                loadImages();
            });
        }
    });
</script>
</html>
