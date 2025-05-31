<?php session_start();
$userID = $_SESSION['userID'] ?? '';
$pageID = 'tt0076759'; // Assuming tt0076759 is replaced with the actual page ID in the template, IGNORE THE WARNING

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
    <title>Star Wars: Episode IV - A New Hope (1977) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Checkout Star Wars: Episode IV - A New Hope on IMDB2, your home of all things media." />
    <meta name="keywords" content="Star Wars: Episode IV - A New Hope, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="movie-title">Star Wars: Episode IV - A New Hope</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=tt0076759" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <div id="title"><h1>Star Wars: Episode IV - A New Hope</h1></div>

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

                    $stmt = $db->prepare('SELECT averageRating FROM title_ratings_trim WHERE tconst = \'tt0076759\'');
                    $stmt->execute();
                    $averageRating = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $averageRating = $averageRating[0]['averageRating'] ?? '?';

                    $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'tt0076759\'');
                    $stmt->execute();
                    $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
                    ?>
                <span>Rating: <?php echo htmlspecialchars($averageRating); ?>/10</span><br>
                <span>Likes: <?php echo htmlspecialchars($likes); ?></span>
            </div>
            <div id="people">
                <h2>Notable People:</h2>
                    <strong>Director(s):</strong> <span id='director'><a href="../resources/page.php?q=nm0000184">George Lucas</a></span>
                    <br><strong>Writer(s):</strong> <span id='writers'><a href="../resources/page.php?q=nm0000184">George Lucas</a></span>
                    <br><strong>Starring:</strong> <span id='stars'><a href="../resources/page.php?q=nm0000434">Mark Hamill</a>, <a href="../resources/page.php?q=nm0000148">Harrison Ford</a>, <a href="../resources/page.php?q=nm0000402">Carrie Fisher</a>, <a href="../resources/page.php?q=nm0000027">Alec Guinness</a>, <a href="../resources/page.php?q=nm0001088">Peter Cushing</a>, <a href="../resources/page.php?q=nm0000355">Anthony Daniels</a>, <a href="../resources/page.php?q=nm0048652">Kenny Baker</a>, <a href="../resources/page.php?q=nm0562679">Peter Mayhew</a>, <a href="../resources/page.php?q=nm0001190">David Prowse</a>, <a href="../resources/page.php?q=nm0114436">Phil Brown</a></span>
                    <br><strong>Other Notable People:</strong> <span id='notable'><a href="../resources/page.php?q=nm0476030">Gary Kurtz (producer)</a>, <a href="../resources/page.php?q=nm0564768">Rick McCallum (producer)</a>, <a href="../resources/page.php?q=nm0002354">John Williams (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0852405">Gilbert Taylor (director of photography)</a>, <a href="../resources/page.php?q=nm0156816">Richard Chew (film editor)</a>, <a href="../resources/page.php?q=nm0160628">T.M. Christopher (editor)</a>, <a href="../resources/page.php?q=nm0386532">Paul Hirsch (film editor)</a>, <a href="../resources/page.php?q=nm0524235">Marcia Lucas (film editor)</a>, <a href="../resources/page.php?q=nm0188240">Dianne Crittenden (casting_director)</a>, <a href="../resources/page.php?q=nm0482961">Irene Lamb (casting_director)</a>, <a href="../resources/page.php?q=nm0708525">Vic Ramos (casting_director)</a>, <a href="../resources/page.php?q=nm0058045">John Barry (Unknown Job (You can add this!))</a></span>
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
                <p id="plot-text">Luke Skywalker joins forces with a Jedi Knight, a cocky pilot, a Wookiee and two awesome droids to save the galaxy from the Empire&amp;#x27;s world-destroying battle station, while also attempting to rescue Princess Leia from the mysterious Darth Vader.</p>
            </details>
        </div>

        <figure id="poster"><img src="../resources/img/load.gif" width="50" alt="Poster for Star Wars: Episode IV - A New Hope" title="Poster for Star Wars: Episode IV - A New Hope from imdb.com"></figure>

        <aside id="blurb">
            <p id="blurb-text">Luke Skywalker joins forces with a Jedi Knight, a cocky pilot, a Wookiee and two droids to save the galaxy from the Empire&amp;#x27;s world-destroying battle station, while also attempting to rescue Princess Leia from the mysterious Darth Vader.</p>
            <ul>
                <li>Title: <span id="movie-title">Star Wars: Episode IV - A New Hope</span></li>
                <li>Year: <span id="movie-year">1977</span></li>
                <li>Rating: <span id="movie-rating"><?php echo $averageRating;?>/10</span></li>
                <li>Runtime: <span id="movie-runtime">121 minutes</span></li>
                <li>Genres: <span id="movie-genres">Action,Adventure,Fantasy</span></li>
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
      url: `../resources/cover-image.php?q=tt0076759`,
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
<!-- This page was recently edited by 'hsa85': The droids are pretty awesome. -->