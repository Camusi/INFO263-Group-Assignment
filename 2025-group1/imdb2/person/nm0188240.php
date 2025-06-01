<?php session_start();
 $userID = $_SESSION['userID'] ?? '';
$pageID = 'nm0188240'; // Assuming nm0188240 is replaced with the actual page ID in the template, IGNORE THE WARNING

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

$stmt = $db->prepare('SELECT likes FROM name_basics_trim WHERE nconst = \'nm0188240\'');
$stmt->execute();
$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dianne Crittenden | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Check out Dianne Crittenden on IMDB2, your home of all things media." />
    <meta name="keywords" content="Dianne Crittenden, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="person-name">Dianne Crittenden</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=person&id=nm0188240" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <main class="main-content">
        <div id="person-name"><h1>Dianne Crittenden</h1></div>
        <div class="blurb-image">
            <div>
                <aside id="blurb">
                    <p id="blurb-text">No biography available yet, but you can add one!</p>
                    <ul>
                        <li>Born: <span id="person-year"></span>1941</span></li>
                        <li>Name: <span id="person-name">Dianne Crittenden</span></li>
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
                <li><a href="../resources/page.php?q=tt0069762">Badlands (casting_director)</a></li><li><a href="../resources/page.php?q=tt0076759">Star Wars: Episode IV - A New Hope (casting_director)</a></li><li><a href="../resources/page.php?q=tt0077405">Days of Heaven (casting_director)</a></li><li><a href="../resources/page.php?q=tt0078326">Straight Time (casting_director)</a></li><li><a href="../resources/page.php?q=tt0079219">Going in Style (casting_director)</a></li><li><a href="../resources/page.php?q=tt0079336">The In-Laws (casting_director)</a></li><li><a href="../resources/page.php?q=tt0080025">Time After Time (casting_director)</a></li><li><a href="../resources/page.php?q=tt0082846">On Golden Pond (casting_director)</a></li><li><a href="../resources/page.php?q=tt0089643">Murphy&#039;s Romance (casting_director)</a></li><li><a href="../resources/page.php?q=tt0090329">Witness (casting_director)</a></li><li><a href="../resources/page.php?q=tt0100405">Pretty Woman (casting_director)</a></li><li><a href="../resources/page.php?q=tt0120863">The Thin Red Line (casting_director)</a></li><li><a href="../resources/page.php?q=tt0146309">Thirteen Days (casting_director)</a></li><li><a href="../resources/page.php?q=tt0316654">Spider-Man 2 (casting_director)</a></li><li><a href="../resources/page.php?q=tt0412080">The World&#039;s Fastest Indian (casting_director)</a></li><li><a href="../resources/page.php?q=tt1810683">Little Boy (casting_director)</a></li>
            </ul>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>