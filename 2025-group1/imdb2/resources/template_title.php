<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{NAME} ({YEAR}) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p>{NAME} ({YEAR})</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id={ID}" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text">{WARNINGS}</p>
        </div>
    </section>
    <div id="title"><h1>{NAME} ({YEAR})</h1></div>

    <main class="title-page-info">
        <div class="left-column">
            <div id="rating">
                <?php
                    // Database connection
                    try {
                        $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
                        exit;
                    }


                    $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'{ID}\'');
                    $stmt->execute();
                    $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $likes = $likes[0]['likes'] ?? '?'; // Default to ? if no likes found
                    ?>
            </div>
            <div id="people">
                <h2>Notable People:</h2>
                    <strong>Director(s):</strong> <span id='director'>{DIRECTOR}</span>
                    <br><strong>Writer(s):</strong> <span id='writers'>{WRITERS}</span>
                    <br><strong>Starring:</strong> <span id='stars'>{STARS}</span>
                    <br><strong>Other Notable People:</strong> <span id='notable'>{NOTABLE}</span>
            </div>
            <p><span><?php echo $likes; ?></span> Likes</p>
            <div>
                <button id="like-button">👍 Like</button>
                <button id="dislike-button">👎 Dislike</button>
            </div>
            <details id="plot" title="Plot Summary">
                <summary><h2>Plot:</h2></summary>
                <p id="plot-text">{PLOT}</p>
            </details>
        </div>

        <figure id="poster"><img src="{POSTER}" width="50" alt="Poster for {NAME}" title="Poster for {NAME} from imdb.com"></figure>

        <aside id="blurb">
            <p id="blurb-text">{BLURB}</p>
            <ul>
                <li>Title: <span id="movie-title">{NAME}</span></li>
                <li>Year: <span id="movie-year">{YEAR}</span></li>
                <li>Runtime: <span id="movie-runtime">{RUNTIME}</span></li>
                <li>Genres: <span id="movie-genres" class="genre-list">{GENRES}</span></li>
            </ul>
        </aside>
    </main>
    <div id="comments"></div>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const images = document.querySelectorAll("figure img");
  images.forEach(img => {
    $.ajax({
      url: `../resources/cover-image.php?q={ID}`,
      method: 'GET',
      dataType: 'json',
      async: false,
      success: function (imgData) {
        if (imgData && imgData.cover_image) {
          img.src = imgData.cover_image;
          img.width = "250";
        }
      },
      error: function () {
        img.src = "../resources/img/load.gif";
      }
    });
  });
});
</script>