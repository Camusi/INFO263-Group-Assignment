<?php session_start();
$userID = $_SESSION['userID'] ?? '';
$pageID = 'tt1870479'; // Assuming tt1870479 is replaced with the actual page ID in the template, IGNORE THE WARNING

$userLikeStatus = 0;
if ($userID && $pageID) {
    $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
    $stmt = $db->prepare("SELECT value FROM likes WHERE userID = :userID AND pageID = :pageID");
    $stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindValue(':pageID', $pageID, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $userLikeStatus = (int)$result['value'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Newsroom (2012) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Checkout The Newsroom on IMDB2, your home of all things media." />
    <meta name="keywords" content="The Newsroom, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="movie-title">The Newsroom (2012)</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=tt1870479" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <div id="title"><h1>The Newsroom (2012)</h1></div>

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

                    $stmt = $db->prepare('SELECT averageRating FROM title_ratings_trim WHERE tconst = \'tt1870479\'');
                    $stmt->execute();
                    $averageRating = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $averageRating = $averageRating[0]['averageRating'] ?? '?';

                    $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'tt1870479\'');
                    $stmt->execute();
                    $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
                    ?>
                <span>Rating: <?php echo htmlspecialchars($averageRating); ?>/10</span><br>
                <span>Likes: <?php echo htmlspecialchars($likes); ?></span>
            </div>
            <div id="people">
                <h2>Notable People:</h2>
                    <strong>Director(s):</strong> <span id='director'><a href="../resources/page.php?q=nm0002083">Carl Franklin</a>, <a href="../resources/page.php?q=nm0258128">Jason Ensler</a>, <a href="../resources/page.php?q=nm0267497">Julian Farino</a>, <a href="../resources/page.php?q=nm0322128">Lesli Linka Glatter</a>, <a href="../resources/page.php?q=nm0336241">Alex Graves</a>, <a href="../resources/page.php?q=nm0376006">Anthony Hemingway</a>, <a href="../resources/page.php?q=nm0509425">Paul Lieberstein</a>, <a href="../resources/page.php?q=nm0551358">Joshua Marston</a>, <a href="../resources/page.php?q=nm0590889">Daniel Minahan</a>, <a href="../resources/page.php?q=nm0609549">Greg Mottola</a>, <a href="../resources/page.php?q=nm0693561">Alan Poul</a></span>
                    <br><strong>Writer(s):</strong> <span id='writers'><a href="../resources/page.php?q=nm0714864">Paul Redford</a>, <a href="../resources/page.php?q=nm0815070">Aaron Sorkin</a>, <a href="../resources/page.php?q=nm1110665">Amy Rice</a>, <a href="../resources/page.php?q=nm1123621">Gideon Yago</a>, <a href="../resources/page.php?q=nm1283212">David Handelman</a>, <a href="../resources/page.php?q=nm1330716">Corinne Kingsbury</a>, <a href="../resources/page.php?q=nm2003798">Brendan Fehily</a>, <a href="../resources/page.php?q=nm2042817">Ian Reichbach</a>, <a href="../resources/page.php?q=nm2074215">Camilla Blackett</a>, <a href="../resources/page.php?q=nm2322409">Cinque Henderson</a>, <a href="../resources/page.php?q=nm2447764">Michael Russell Gunn</a>, <a href="../resources/page.php?q=nm2834849">Dana Ledoux Miller</a>, <a href="../resources/page.php?q=nm3150287">Deborah Schoeneman</a>, <a href="../resources/page.php?q=nm3933675">Adam R. Perlman</a>, <a href="../resources/page.php?q=nm4922739">Jon Lovett</a>, <a href="../resources/page.php?q=nm5509776">Alena Smith</a>, <a href="../resources/page.php?q=nm5745999">Elizabeth Peterson</a>, <a href="../resources/page.php?q=nm6902047">John Musero</a></span>
                    <br><strong>Starring:</strong> <span id='stars'><a href="../resources/page.php?q=nm0001099">Jeff Daniels</a>, <a href="../resources/page.php?q=nm0607865">Emily Mortimer</a>, <a href="../resources/page.php?q=nm0302330">John Gallagher Jr.</a>, <a href="../resources/page.php?q=nm0683467">Alison Pill</a>, <a href="../resources/page.php?q=nm0755603">Thomas Sadoski</a>, <a href="../resources/page.php?q=nm2353862">Dev Patel</a>, <a href="../resources/page.php?q=nm1601397">Olivia Munn</a>, <a href="../resources/page.php?q=nm0001832">Sam Waterston</a>, <a href="../resources/page.php?q=nm1663252">Chris Chalk</a>, <a href="../resources/page.php?q=nm4622699">Margaret Judson</a></span>
                    <br><strong>Other Notable People:</strong> <span id='notable'>Either all the people who worked on this title are categorized, or we're missing someone. Feel free to correct this by editing the page.</span>
            </div>
            <div class="like-controls">
                <?php 
                if ($userLikeStatus == 0){
                    echo "
                    <form action='../resources/likes.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($pageID)."'>
                        <input type='hidden' name='ld' value='like'>
                        <input type='hidden' name='q' value='23'>
                        <input type='hidden' name='return_to' value='".htmlspecialchars($_SERVER['REQUEST_URI'])."'>
                        <button type='submit'>👍 Like</button>
                    </form>
                    <form action='../resources/likes.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($pageID)."'>
                        <input type='hidden' name='ld' value='dislike'>
                        <input type='hidden' name='q' value='23'>
                        <input type='hidden' name='return_to' value='".htmlspecialchars($_SERVER['REQUEST_URI'])."'>
                        <button type='submit'>👎 Dislike</button>
                    ";
                } elseif ($userLikeStatus == -1) {
                    echo "
                    <form action='../resources/likes.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($pageID)."'>
                        <input type='hidden' name='ld' value='like'>
                        <input type='hidden' name='q' value='23'>
                        <input type='hidden' name='return_to' value='".htmlspecialchars($_SERVER['REQUEST_URI'])."'>
                        <button type='submit' disabled>👍 Like</button>
                    </form>
                    <form action='../resources/likes.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($pageID)."'>
                        <input type='hidden' name='ld' value='undislike'>
                        <input type='hidden' name='q' value='23'>
                        <input type='hidden' name='return_to' value='".htmlspecialchars($_SERVER['REQUEST_URI'])."'>
                        <button type='submit'>👎 Remove Dislike</button>
                    </form>
                    ";
                } elseif ($userLikeStatus == 1) {
                    echo "
                    <form action='../resources/likes.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($pageID)."'>
                        <input type='hidden' name='ld' value='unlike'>
                        <input type='hidden' name='q' value='23'>
                        <input type='hidden' name='return_to' value='".htmlspecialchars($_SERVER['REQUEST_URI'])."'>
                        <button type='submit'>👍 Remove Like</button>
                    </form>
                    <form action='../resources/likes.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($pageID)."'>
                        <input type='hidden' name='ld' value='dislike'>
                        <input type='hidden' name='q' value='23'>
                        <input type='hidden' name='return_to' value='".htmlspecialchars($_SERVER['REQUEST_URI'])."'>
                        <button type='submit' disabled>👎 Dislike</button>
                    </form>
                    ";
                }
                ?>
                <?php 
                if ($userLikeStatus == 1) {
                    echo '<span style="color:green">You liked this.</span>';
                } elseif ($userLikeStatus == -1) {
                    echo '<span style="color:red">You disliked this.</span>';
                } else {
                    echo '<span></span>';
                }
                ?>
            </div>
            <details id="plot" title="Plot Summary">
                <summary><h2>Plot:</h2></summary>
                <p id="plot-text">A newsroom undergoes some changes in its workings and morals as a new team is brought in, bringing unexpected results for its existing news anchor.</p>
            </details>
        </div>

        <figure id="poster"><img src="../resources/img/load.gif" width="50" alt="Poster for The Newsroom" title="Poster for The Newsroom from imdb.com"></figure>

        <aside id="blurb">
            <p id="blurb-text">A newsroom undergoes some changes in its workings and morals as a new team is brought in, bringing unexpected results for its existing news anchor.</p>
            <ul>
                <li>Title: <span id="movie-title">The Newsroom</span></li>
                <li>Year: <span id="movie-year">2012</span></li>
                <li>Rating: <span id="movie-rating"><?php echo $averageRating;?>/10</span></li>
                <li>Runtime: <span id="movie-runtime">60 minutes</span></li>
                <li>Genres: <span id="movie-genres">Drama</span></li>
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
      url: `../resources/cover-image.php?q=tt1870479`,
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