<?php session_start();
$userID = $_SESSION['userID'] ?? '';
$pageID = 'tt0087553'; // Assuming tt0087553 is replaced with the actual page ID in the template, IGNORE THE WARNING

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
    <title>The Killing Fields (1984) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Checkout The Killing Fields on IMDB2, your home of all things media." />
    <meta name="keywords" content="The Killing Fields, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="movie-title">The Killing Fields (1984)</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=tt0087553" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <div id="title"><h1>The Killing Fields (1984)</h1></div>

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

                    $stmt = $db->prepare('SELECT averageRating FROM title_ratings_trim WHERE tconst = \'tt0087553\'');
                    $stmt->execute();
                    $averageRating = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $averageRating = $averageRating[0]['averageRating'] ?? '?';

                    $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'tt0087553\'');
                    $stmt->execute();
                    $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
                    ?>
                <span>Rating: <?php echo htmlspecialchars($averageRating); ?>/10</span><br>
                <span>Likes: <?php echo htmlspecialchars($likes); ?></span>
            </div>
            <div id="people">
                <h2>Notable People:</h2>
                    <strong>Director(s):</strong> <span id='director'><a href="../resources/page.php?q=nm0423646">Roland Joffé</a></span>
                    <br><strong>Writer(s):</strong> <span id='writers'><a href="../resources/page.php?q=nm0732430">Bruce Robinson</a></span>
                    <br><strong>Starring:</strong> <span id='stars'><a href="../resources/page.php?q=nm0001832">Sam Waterston</a>, <a href="../resources/page.php?q=nm0628955">Haing S. Ngor</a>, <a href="../resources/page.php?q=nm0000518">John Malkovich</a>, <a href="../resources/page.php?q=nm0001696">Julian Sands</a>, <a href="../resources/page.php?q=nm0005266">Craig T. Nelson</a>, <a href="../resources/page.php?q=nm0336960">Spalding Gray</a>, <a href="../resources/page.php?q=nm0665473">Bill Paterson</a>, <a href="../resources/page.php?q=nm0297538">Athol Fugard</a>, <a href="../resources/page.php?q=nm0448058">Graham Kennedy</a>, <a href="../resources/page.php?q=nm0156827">Katherine Krapum Chey</a></span>
                    <br><strong>Other Notable People:</strong> <span id='notable'><a href="../resources/page.php?q=nm0701298">David Puttnam (producer)</a>, <a href="../resources/page.php?q=nm0646131">Mike Oldfield (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0579580">Chris Menges (director of photography)</a>, <a href="../resources/page.php?q=nm0164083">Jim Clark (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0234864">Marion Dougherty (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0007109">Susie Figgis (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0325479">Pat Golden (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0852681">Juliet Taylor (casting_director)</a>, <a href="../resources/page.php?q=nm0908178">Roy Walker (Unknown Job (You can add this!))</a></span>
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
                <p id="plot-text">A journalist is trapped in Cambodia during Pol Pot&#x27;s bloody Year Zero cleansing campaign.</p>
            </details>
        </div>

        <figure id="poster"><img src="../resources/img/load.gif" width="50" alt="Poster for The Killing Fields" title="Poster for The Killing Fields from imdb.com"></figure>

        <aside id="blurb">
            <p id="blurb-text">A journalist is trapped in Cambodia during Pol Pot&#x27;s bloody Year Zero cleansing campaign.</p>
            <ul>
                <li>Title: <span id="movie-title">The Killing Fields</span></li>
                <li>Year: <span id="movie-year">1984</span></li>
                <li>Rating: <span id="movie-rating"><?php echo $averageRating;?>/10</span></li>
                <li>Runtime: <span id="movie-runtime">141 minutes</span></li>
                <li>Genres: <span id="movie-genres">Biography,Drama,History</span></li>
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
      url: `../resources/cover-image.php?q=tt0087553`,
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