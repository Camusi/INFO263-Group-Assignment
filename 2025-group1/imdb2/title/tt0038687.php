<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let There Be Light (1980) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="movie-title">Let There Be Light (1980)</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=tt0038687" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <div id="title"><h1>Let There Be Light (1980)</h1></div>

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


                    $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'tt0038687\'');
                    $stmt->execute();
                    $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $likes = $likes[0]['likes'] ?? '?'; // Default to ? if no likes found
                    ?>
            </div>
            <div id="people">
                <h2>Notable People:</h2>
                    <strong>Director(s):</strong> <span id='director'><a href="../resources/page.php?q=nm0001379">John Huston</a></span>
                    <br><strong>Writer(s):</strong> <span id='writers'><a href="../resources/page.php?q=nm0001379">John Huston</a></span>
                    <br><strong>Starring:</strong> <span id='stars'>N/A</span>
                    <br><strong>Other Notable People:</strong> <span id='notable'>Either all the people who worked on this title are categorized, or we're missing someone. Feel free to correct this by editing the page.</span>
            </div>
            <p><span id="like-count"><?php echo $likes; ?></span> Likes</p>
            <div>
                <?php if (!isset($_SESSION['userID'])){echo '<button id="rate-login-prompt">Login to rate "Let There Be Light (1980)"!</button>';} else{echo '<button id="like-button">👍 Like</button><button id="dislike-button">👎 Dislike</button>';} ?> 
            </div>
            <details id="plot" title="Plot Summary">
                <summary><h2>Plot:</h2></summary>
                <p id="plot-text">A group of mentally traumatized veteran patients is followed as they go through psychiatric treatment.</p>
            </details>
        </div>

        <figure id="poster"><img src="../resources/img/load.gif" width="50" alt="Poster for Let There Be Light" title="Poster for Let There Be Light from imdb.com"></figure>

        <aside id="blurb">
            <p id="blurb-text"></p>
            <ul>
                <li>Title: <span id="movie-title">Let There Be Light</span></li>
                <li>Year: <span id="movie-year">1980</span></li>
                <li>Runtime: <span id="movie-runtime">58 minutes</span></li>
                <li>Genres: <span id="movie-genres" class="genre-list">Documentary,War<li>Documentary,War</li></span></li>
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
      url: `../resources/cover-image.php?q=tt0038687`,
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