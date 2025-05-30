<?php 
session_start();



$id = '<ID>';

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


foreach ($pages as $page){
    $pdb = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $stmt = $pdb->prepare("
        SELECT primaryTitle AS primary_name, 'title_basics_trim' AS table_name
        FROM title_basics_trim
        WHERE tconst = :id
        UNION ALL
        SELECT primaryName AS primary_name, 'name_basics_trim' AS table_name
        FROM name_basics_trim
        WHERE nconst = :id
    ");
    $stmt->bindParam(':id', $page['pageID'], PDO::PARAM_STR);
    $stmt->execute();
    $page['pageName'] = $stmt->fetchColumn();
}

function generatePageLinks($pages) {
    $html = '<ul>';
    foreach ($pages as $page) {
        $html .= '<li><a href="../resources/page.php?q=' . htmlspecialchars($page['pageID']) . '" target="_blank">' . htmlspecialchars($page['pageName']) . '</a></li>';
    }
    $html .= '</ul>';
    return $html;
}

$likedTitlesHTML = generateTitleLinks($likedTitles);
$dislikedTitlesHTML = generateTitleLinks($dislikedTitles);
$likedPeopleHTML = generatePersonLinks($likedPeople);
$dislikedPeopleHTML = generatePersonLinks($dislikedPeople);

echo '<div id="liked-titles">' . $likedTitlesHTML . '</div>';
echo '<div id="disliked-titles">' . $dislikedTitlesHTML . '</div>';
echo '<div id="liked-people">' . $likedPeopleHTML . '</div>';
echo '<div id="disliked-people">' . $dislikedPeopleHTML . '</div>';

// Count liked pages
$pagesLikeCount = count($likedPages);
$pagesDislikeCount = count($dislikedPages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{USER} | IMDB2.0</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <meta type="description" content="Checkout {NAME} on IMDB2, your home of all things media." />
    <meta name="keywords" content="{NAME}, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
        <h1>IMDB2.0</h1>
        <p id="user-name">{USER}'s Profile</p>
    </header>
    <?php include '../resources/navbar.php'; ?>
    <main class="title-page-info">
        <div class="left-column">
            <div id="title-likes">
                <h1>Titles {USER} LOVES</h1>
                    <?php echo $likedTitlesHTML; ?>
            </div>
            <div id="person-likes">
                <h1>People {USER} LOVES</h1>
                    <?php echo $likedPeopleHTML; ?>
            </div>
        </div>

        <aside id="blurb">
            <p id="blurb-text">{USER} has liked <?php echo $pagesLikeCount; ?> pages and dislikes <?php echo $pagesDislikeCount; ?> pages.</p>
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