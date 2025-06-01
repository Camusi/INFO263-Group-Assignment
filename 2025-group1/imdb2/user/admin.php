<?php 
session_start();



$id = 'admin';

$db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//find liked pages
$stmt = $db->prepare('SELECT pageID FROM likes WHERE userID = :userID AND value = 1');
$stmt->bindParam(':userID', $id, type: PDO::PARAM_STR);
$stmt->execute();
$likedPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $db->prepare('SELECT pageID FROM likes WHERE userID = :userID AND value = -1');
$stmt->bindParam(':userID', $id, PDO::PARAM_STR);
$stmt->execute();
$dislikedPages = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getPageName($pageID) {
    $pageDB = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $stmt = $pageDB->prepare("
      SELECT primaryTitle as pageName FROM title_basics_trim WHERE tconst = :pageID
      UNION
      SELECT primaryName as pageName FROM name_basics_trim WHERE nconst = :pageID
      ");
    $stmt->bindParam(':pageID', $pageID, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function generatePageLinks($pages) {
    $html = '<ul>';
    foreach ($pages as $page) {
        $pageID = $page['pageID'] ?? null;
        if ($pageID) {
            $pageData = getPageName($pageID);
            $pageName = $pageData['pageName'] ?? 'Unknown';
            $html .= '<li><a href="../resources/page.php?q=' . htmlspecialchars($pageID) . '" target="_blank">' . htmlspecialchars($pageName) . '</a></li>';
        }
    }
    $html .= '</ul>';
    return $html;
}

$likedPagesHTML = generatePageLinks($likedPages);
$dislikedPagesHTML = generatePageLinks($dislikedPages);

// Count liked pages
$pagesLikeCount = count($likedPages);
$pagesDislikeCount = count($dislikedPages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin | IMDB2.0</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <meta type="robots" content="noindex, nofollow">
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="../resources/style.css">
</head>
<body>
    <header class="header">
        <h1>IMDB2.0</h1>
        <p id="user-name">admin's Profile</p>
    </header>
    <?php include '../resources/navbar.php'; ?>
    <main class="title-page-info">
      <div id="warnings">
        <p id="page-warning">This account (admin) is a superUser and will not show any likes/dislikes.</p>
      </div>
        <div class="left-column">
            <h5>Trying to contact us?</h5>
            <p>For any issues, feedback, or edit suggestions, please contact us via real@realemail.com.</p>
            <figure><img src="../resources/img/favicon.ico" alt="IMDB2.0 Logo" width="250" height="250"></figure>
        </div>

        <aside id="blurb">
            <p id="blurb-text">admin has liked <?php echo $pagesLikeCount; ?> pages and dislikes <?php echo $pagesDislikeCount; ?> pages.</p>
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
      url: `../resources/cover-image.php?q=admin`,
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