<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fred Astaire | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p>Fred Astaire</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=person&id=nm0000001" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"><br><span id="page-warning">This page is automatically generated based on data from an IMDB database export. Information may be out of date or not accurately reflect reality.</span><br><br></p>
        </div>
    </section>
    <main class="main-content">
        <div id="person-name"><h1>Fred Astaire</h1></div>
        <div class="blurb-image">
            <div>
                <aside id="blurb">
                    <p id="blurb-text">No biography available yet, but you can add one!</p>
                    <ul>
                        <li>Born: <span id="person-year"></span>1899</span></li>
                        <li>Name: <span id="person-name">Fred Astaire</span></li>
                    </ul>
                </aside>
                <div id="rating">
                    <?php
                        try {
                            $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
                            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
                            exit;
                        }


                        $stmt = $db->prepare('SELECT likes FROM name_basics_trim WHERE nconst = \'nm0000001\'');
                        $stmt->execute();
                        $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $likes = $likes[0]['likes'] ?? '?'; // Default to ? if no likes found
                        ?>
                    <p><span><?php echo $likes; ?></span> Likes</p>
                    <?php if (!isset($_SESSION['userID'])){echo '<button id="rate-login-prompt">Login to rate "Fred Astaire (1899)"!</button>';} else{echo '<button id="like-button">👍 Like</button><button id="dislike-button">👎 Dislike</button>';} ?> 
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
                <li><a href="../resources/page.php?q=tt0072272">That's Entertainment! (self)</a></li><li><a href="../resources/page.php?q=tt0072272">That's Entertainment! (self)</a></li><li><a href="../resources/page.php?q=tt0072272">That's Entertainment! (self)</a></li><li><a href="../resources/page.php?q=tt0072308">The Towering Inferno (actor)</a></li><li><a href="../resources/page.php?q=tt0072742">Brother Can You Spare a Dime (archive_footage)</a></li><li><a href="../resources/page.php?q=tt0073122">Hooray for Hollywood (archive_footage)</a></li><li><a href="../resources/page.php?q=tt0075323">That's Entertainment, Part II (self)</a></li><li><a href="../resources/page.php?q=tt0075323">That's Entertainment, Part II (self)</a></li><li><a href="../resources/page.php?q=tt0087322">George Stevens: A Filmmaker's Journey (self)</a></li><li><a href="../resources/page.php?q=tt0133943">The Magic of Dance (self)</a></li><li><a href="../resources/page.php?q=tt0208054">Le cinéma dans les yeux (archive_footage)</a></li><li><a href="../resources/page.php?q=tt0322431">Going Hollywood: The '30s (archive_footage)</a></li><li><a href="../resources/page.php?q=tt0360833">Nelson Freire (archive_footage)</a></li><li><a href="../resources/page.php?q=tt10267396">Sid &amp; Judy (archive_footage)</a></li><li><a href="../resources/page.php?q=tt15000932">All About Yves Montand (archive_footage)</a></li><li><a href="../resources/page.php?q=tt1906353">D'un film à l'autre (archive_footage)</a></li><li><a href="../resources/page.php?q=tt5845320">Leslie Caron: The Reluctant Star (archive_footage)</a></li>
            </ul>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>