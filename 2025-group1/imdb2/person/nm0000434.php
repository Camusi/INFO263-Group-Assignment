<?php session_start();
 $userID = $_SESSION['userID'] ?? '';
$pageID = 'nm0000434'; // Assuming nm0000434 is replaced with the actual page ID in the template, IGNORE THE WARNING

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

$stmt = $db->prepare('SELECT likes FROM name_basics_trim WHERE nconst = \'nm0000434\'');
$stmt->execute();
$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$likes = $likes[0]['likes'] ?? '0'; // Default to 0 if no likes found
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Hamill | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
    <meta type="description" content="Check out Mark Hamill on IMDB2, your home of all things media." />
    <meta name="keywords" content="Mark Hamill, IMDB, movies, shows, people, media, database" />
    <meta name="author" content="Group 1, 2025S1" />
    <link rel="icon" href="../resources/img/favicon.ico" type="image/x-icon" />
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p id="person-name">Mark Hamill</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=person&id=nm0000434" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"></p>
        </div>
    </section>
    <main class="main-content">
        <div id="person-name"><h1>Mark Hamill</h1></div>
        <div class="blurb-image">
            <div>
                <aside id="blurb">
                    <p id="blurb-text">No biography available yet, but you can add one!</p>
                    <ul>
                        <li>Born: <span id="person-year"></span></span></li>
                        <li>Name: <span id="person-name">Mark Hamill</span></li>
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
                <li><a href="../resources/page.php?q=tt0071064">The Texas Wheelers (actor)</a></li><li><a href="../resources/page.php?q=tt0076759">Star Wars: Episode IV - A New Hope (actor)</a></li><li><a href="../resources/page.php?q=tt0080437">The Big Red One (actor)</a></li><li><a href="../resources/page.php?q=tt0080684">Star Wars: Episode V - The Empire Strikes Back (actor)</a></li><li><a href="../resources/page.php?q=tt0086190">Star Wars: Episode VI - Return of the Jedi (actor)</a></li><li><a href="../resources/page.php?q=tt0103359">Batman: The Animated Series (actor)</a></li><li><a href="../resources/page.php?q=tt0103359">Batman: The Animated Series (actor)</a></li><li><a href="../resources/page.php?q=tt0103359">Batman: The Animated Series (actor)</a></li><li><a href="../resources/page.php?q=tt0105928">2 Stupid Dogs (actor)</a></li><li><a href="../resources/page.php?q=tt0110825">Phantom 2040 (actor)</a></li><li><a href="../resources/page.php?q=tt0118266">The New Batman Adventures (actor)</a></li><li><a href="../resources/page.php?q=tt0118266">The New Batman Adventures (actor)</a></li><li><a href="../resources/page.php?q=tt0126173">Swat Kats: The Radical Squadron (actor)</a></li><li><a href="../resources/page.php?q=tt0126173">Swat Kats: The Radical Squadron (actor)</a></li><li><a href="../resources/page.php?q=tt0126173">Swat Kats: The Radical Squadron (actor)</a></li><li><a href="../resources/page.php?q=tt0147752">Biker Mice from Mars (actor)</a></li><li><a href="../resources/page.php?q=tt0147752">Biker Mice from Mars (actor)</a></li><li><a href="../resources/page.php?q=tt0175317">Walking Across Egypt (actor)</a></li><li><a href="../resources/page.php?q=tt0212693">The Sci-Fi Files (actor)</a></li><li><a href="../resources/page.php?q=tt0275137">Justice League (actor)</a></li><li><a href="../resources/page.php?q=tt0275137">Justice League (actor)</a></li><li><a href="../resources/page.php?q=tt0275137">Justice League (actor)</a></li><li><a href="../resources/page.php?q=tt0278881">Super Structures of the World (self)</a></li><li><a href="../resources/page.php?q=tt0291672">Time Squad (actor)</a></li><li><a href="../resources/page.php?q=tt0291672">Time Squad (actor)</a></li><li><a href="../resources/page.php?q=tt0291672">Time Squad (actor)</a></li><li><a href="../resources/page.php?q=tt0312437">Baxter and Bananas in Monkey See Monkey Don&#039;t (actor)</a></li><li><a href="../resources/page.php?q=tt0398602">Warp (self)</a></li><li><a href="../resources/page.php?q=tt0805369">Ancient Voices (actor)</a></li><li><a href="../resources/page.php?q=tt0839188">Metalocalypse (actor)</a></li><li><a href="../resources/page.php?q=tt0839188">Metalocalypse (actor)</a></li><li><a href="../resources/page.php?q=tt0839188">Metalocalypse (actor)</a></li><li><a href="../resources/page.php?q=tt0923157">Danger Rangers (actor)</a></li><li><a href="../resources/page.php?q=tt0923157">Danger Rangers (actor)</a></li><li><a href="../resources/page.php?q=tt0923157">Danger Rangers (actor)</a></li><li><a href="../resources/page.php?q=tt1204935">Mythic Journeys (actor)</a></li><li><a href="../resources/page.php?q=tt1310992">Tasty Time with ZeFronk (actor)</a></li><li><a href="../resources/page.php?q=tt15222206">Adventure Thru the Walt Disney Archives (self)</a></li><li><a href="../resources/page.php?q=tt1683048">De Palma (archive_footage)</a></li><li><a href="../resources/page.php?q=tt1710308">Regular Show (actor)</a></li><li><a href="../resources/page.php?q=tt1710308">Regular Show (actor)</a></li><li><a href="../resources/page.php?q=tt1710308">Regular Show (actor)</a></li><li><a href="../resources/page.php?q=tt1854531">Finnigan&#039;s War (actor)</a></li><li><a href="../resources/page.php?q=tt1984058">Unnatural History (actor)</a></li><li><a href="../resources/page.php?q=tt19881256">Disney Gallery: Star Wars: The Book of Boba Fett (self)</a></li><li><a href="../resources/page.php?q=tt2392143">Motorcity (actor)</a></li><li><a href="../resources/page.php?q=tt2392143">Motorcity (actor)</a></li><li><a href="../resources/page.php?q=tt2488496">Star Wars: Episode VII - The Force Awakens (actor)</a></li><li><a href="../resources/page.php?q=tt27052533">Masters of the Universe: Revolution (actor)</a></li><li><a href="../resources/page.php?q=tt27052533">Masters of the Universe: Revolution (actor)</a></li><li><a href="../resources/page.php?q=tt27052533">Masters of the Universe: Revolution (actor)</a></li><li><a href="../resources/page.php?q=tt2915480">Go Far: The Christopher Rush Story (actor)</a></li><li><a href="../resources/page.php?q=tt29623480">The Wild Robot (actor)</a></li><li><a href="../resources/page.php?q=tt2972098">A Fuller Life (self)</a></li><li><a href="../resources/page.php?q=tt4201180">Regular Show (actor)</a></li><li><a href="../resources/page.php?q=tt4920274">Regular Show: The Movie (actor)</a></li><li><a href="../resources/page.php?q=tt5419200">Justice League Action (actor)</a></li><li><a href="../resources/page.php?q=tt5419200">Justice League Action (actor)</a></li><li><a href="../resources/page.php?q=tt5419200">Justice League Action (actor)</a></li><li><a href="../resources/page.php?q=tt5532610">Secrets of the Force Awakens: A Cinematic Journey (self)</a></li><li><a href="../resources/page.php?q=tt5775214">Mark Hamill&#039;s Pop Culture Quest (self)</a></li><li><a href="../resources/page.php?q=tt5805752">Brigsby Bear (actor)</a></li><li><a href="../resources/page.php?q=tt6933208">Best Fiends: Boot Camp (actor)</a></li><li><a href="../resources/page.php?q=tt8080556">The Director and the Jedi (self)</a></li><li><a href="../resources/page.php?q=tt8722180">Best Fiends: Fort of Hard Knocks (actor)</a></li><li><a href="../resources/page.php?q=tt8914012">The Last Kids on Earth (actor)</a></li><li><a href="../resources/page.php?q=tt8914012">The Last Kids on Earth (actor)</a></li><li><a href="../resources/page.php?q=tt8992494">Best Fiends: Baby Slug&#039;s Big Day Out (actor)</a></li><li><a href="../resources/page.php?q=tt9020558">Hollywood Burn (archive_footage)</a></li><li><a href="../resources/page.php?q=tt9555800">Heart of Batman (archive_footage)</a></li><li><a href="../resources/page.php?q=tt9578822">Best Fiends: Howie&#039;s Gift (actor)</a></li>
            </ul>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>