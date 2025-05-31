<?php session_start();
$userID = $_SESSION['userID'] ?? '';
$pageID = 'tt0120601'; // Assuming tt0120601 is replaced with the actual page ID in the template, IGNORE THE WARNING

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
    <title>Being John Malkovich (1999) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Checkout Being John Malkovich on IMDB2, your home of all things media." />
    <meta name="keywords" content="Being John Malkovich, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="movie-title">Being John Malkovich</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=tt0120601" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <div id="title"><h1>Being John Malkovich</h1></div>

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

                    $stmt = $db->prepare('SELECT averageRating FROM title_ratings_trim WHERE tconst = \'tt0120601\'');
                    $stmt->execute();
                    $averageRating = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $averageRating = $averageRating[0]['averageRating'] ?? '?';

                    $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'tt0120601\'');
                    $stmt->execute();
                    $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
                    ?>
                <span>Rating: <?php echo htmlspecialchars($averageRating); ?>/10</span><br>
                <span>Likes: <?php echo htmlspecialchars($likes); ?></span>
            </div>
            <div id="people">
                <h2>Notable People:</h2>
                    <strong>Director(s):</strong> <span id='director'><a href="../resources/page.php?q=nm0005069">Spike Jonze</a></span>
                    <br><strong>Writer(s):</strong> <span id='writers'><a href="../resources/page.php?q=nm0442109">Charlie Kaufman</a></span>
                    <br><strong>Starring:</strong> <span id='stars'><a href="../resources/page.php?q=nm0000131">John Cusack</a>, <a href="../resources/page.php?q=nm0000139">Cameron Diaz</a>, <a href="../resources/page.php?q=nm0001416">Catherine Keener</a>, <a href="../resources/page.php?q=nm0000518">John Malkovich</a>, <a href="../resources/page.php?q=nm0068709">Ned Bellamy</a>, <a href="../resources/page.php?q=nm0918435">Eric Weinstein</a>, <a href="../resources/page.php?q=nm0484099">Madison Lanc</a>, <a href="../resources/page.php?q=nm0818055">Octavia Spencer</a>, <a href="../resources/page.php?q=nm0005316">Mary Kay Place</a>, <a href="../resources/page.php?q=nm0004730">Orson Bean</a></span>
                    <br><strong>Other Notable People:</strong> <span id='notable'><a href="../resources/page.php?q=nm0326512">Steve Golin (producer)</a>, <a href="../resources/page.php?q=nm0484504">Vincent Landay (producer)</a>, <a href="../resources/page.php?q=nm0827840">Sandy Stern (producer)</a>, <a href="../resources/page.php?q=nm0005468">Michael Stipe (producer)</a>, <a href="../resources/page.php?q=nm0001980">Carter Burwell (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0010139">Lance Acord (director of photography)</a>, <a href="../resources/page.php?q=nm0958641">Eric Zumbrunnen (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0045970">Justine Arteta (casting_director)</a>, <a href="../resources/page.php?q=nm0204979">Kim Davis-Wagner (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0057187">K.K. Barrett (Unknown Job (You can add this!))</a></span>
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
                <p id="plot-text">A male puppeteer discovers a portal that leads literally into the head of movie star John Malkovich.</p>
            </details>
        </div>

        <figure id="poster"><img src="../resources/img/load.gif" width="50" alt="Poster for Being John Malkovich" title="Poster for Being John Malkovich from imdb.com"></figure>

        <aside id="blurb">
            <p id="blurb-text">A puppeteer discovers a portal that leads literally into the head of movie star John Malkovich.</p>
            <ul>
                <li>Title: <span id="movie-title">Being John Malkovich</span></li>
                <li>Year: <span id="movie-year">1999</span></li>
                <li>Rating: <span id="movie-rating"><?php echo $averageRating;?>/10</span></li>
                <li>Runtime: <span id="movie-runtime">113 minutes</span></li>
                <li>Genres: <span id="movie-genres">Comedy,Drama,Fantasy</span></li>
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
      url: `../resources/cover-image.php?q=tt0120601`,
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
<!-- This page was recently edited by 'ChrisAkroyd': John is a male. -->