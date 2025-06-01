<?php session_start();
 $userID = $_SESSION['userID'] ?? '';
$pageID = 'nm0001832'; // Assuming nm0001832 is replaced with the actual page ID in the template, IGNORE THE WARNING

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
// Database connection
try {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$stmt = $db->prepare('SELECT likes FROM name_basics_trim WHERE nconst = \'nm0001832\'');
$stmt->execute();
$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam Waterston | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Check out Sam Waterston on IMDB2, your home of all things media." />
    <meta name="keywords" content="Sam Waterston, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="person-name">Sam Waterston</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=person&id=nm0001832" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <main class="main-content">
        <div id="person-name"><h1>Sam Waterston</h1></div>
        <div class="blurb-image">
            <div>
                <aside id="blurb">
                    <p id="blurb-text">No biography available yet, but you can add one!</p>
                    <ul>
                        <li>Born: <span id="person-year"></span>1940</span></li>
                        <li>Name: <span id="person-name">Sam Waterston</span></li>
                        <li>Votes: <span><?php echo htmlspecialchars($likes); ?></span></li>
                    </ul>
                </aside>
                <div id="like-controls">

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
            </div>

            <div class="svg-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="19vw" height="19vw" viewBox="0 0 24 24" fill="black">
                    <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.2c-3.1 0-9.3 1.6-9.3 4.9v2.7h18.6v-2.7c0-3.3-6.2-4.9-9.3-4.9z"/>
                </svg>
            </div>
        </div>
        <div id="roles" class="roles">
            <h2>Roles</h2>
            <ul class="roles-list">
                <li><a href="../resources/page.php?q=tt0077742">Interiors (actor)</a></li><li><a href="../resources/page.php?q=tt0080889">Hopscotch (actor)</a></li><li><a href="../resources/page.php?q=tt0083468">Q.E.D. (actor)</a></li><li><a href="../resources/page.php?q=tt0087553">The Killing Fields (actor)</a></li><li><a href="../resources/page.php?q=tt0094089">Swimming to Cambodia (archive_footage)</a></li><li><a href="../resources/page.php?q=tt0098844">Law &amp; Order (actor)</a></li><li><a href="../resources/page.php?q=tt0098844">Law &amp; Order (actor)</a></li><li><a href="../resources/page.php?q=tt0098844">Law &amp; Order (actor)</a></li><li><a href="../resources/page.php?q=tt0100151">Mindwalk (actor)</a></li><li><a href="../resources/page.php?q=tt0101124">I&#039;ll Fly Away (actor)</a></li><li><a href="../resources/page.php?q=tt0102388">The Man in the Moon (actor)</a></li><li><a href="../resources/page.php?q=tt0112054">Lost Civilizations (self)</a></li><li><a href="../resources/page.php?q=tt0155676">A Dog Race in Alaska (actor)</a></li><li><a href="../resources/page.php?q=tt0159369">Cooper and Hemingway: The True Gen (actor)</a></li><li><a href="../resources/page.php?q=tt0406821">Hammer and Cycle (actor)</a></li><li><a href="../resources/page.php?q=tt0468989">Character Studies (self)</a></li><li><a href="../resources/page.php?q=tt0875612">Rain Falls from Earth: Surviving Cambodia&#039;s Darkest Hour (self)</a></li><li><a href="../resources/page.php?q=tt14493970">Merchant Ivory (self)</a></li><li><a href="../resources/page.php?q=tt1870479">The Newsroom (actor)</a></li><li><a href="../resources/page.php?q=tt19223402">Liv Ullmann: A Road Less Travelled (TV) (self)</a></li><li><a href="../resources/page.php?q=tt3609352">Grace and Frankie (actor)</a></li><li><a href="../resources/page.php?q=tt4669788">On the Basis of Sex (actor)</a></li><li><a href="../resources/page.php?q=tt5128292">Dateline: Saigon (actor)</a></li><li><a href="../resources/page.php?q=tt5958312">Monkey Business: The Adventures of Curious George&#039;s Creators (actor)</a></li>
            </ul>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>