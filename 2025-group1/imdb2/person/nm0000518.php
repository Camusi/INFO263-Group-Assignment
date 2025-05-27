<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Malkovich | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p>John Malkovich</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=nm0000518" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"><br><span id="page-warning">This page is automatically generated based on data from an IMDB database export. Information may be out of date or not accurately reflect reality.</span><br><br></p>
        </div>
    </section>
    <main class="main-content">
        <div id="title"><h1>John Malkovich</h1></div>
        <aside id="blurb">
            No biography available yet, but you can add one!
            <br>Born in 1953.
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
                

                $stmt = $db->prepare('SELECT likes FROM name_basics_trim WHERE nconst = \'nm0000518\'');
                $stmt->execute();
                $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $likes = $likes[0]['likes'] ?? '?'; // Default to ? if no likes found
                ?>
            <p><span><?php echo $likes; ?></span> Likes!</p>
            <button id="like-button">I like this!</button>
            <button id="dislike-button">I dislike this!</button>
        </div>
        <div id="roles" class="roles">
            <h2>Roles</h2>
            <ul class="roles-list">
                <li><a href="../resources/page.php?q=tt0087553">The Killing Fields (actor)</a></li><li><a href="../resources/page.php?q=tt0087921">Places in the Heart (actor)</a></li><li><a href="../resources/page.php?q=tt0092965">Empire of the Sun (actor)</a></li><li><a href="../resources/page.php?q=tt0093093">The Glass Menagerie (actor)</a></li><li><a href="../resources/page.php?q=tt0094947">Dangerous Liaisons (actor)</a></li><li><a href="../resources/page.php?q=tt0105046">Of Mice and Men (actor)</a></li><li><a href="../resources/page.php?q=tt0107206">In the Line of Fire (actor)</a></li><li><a href="../resources/page.php?q=tt0120601">Being John Malkovich (actor)</a></li><li><a href="../resources/page.php?q=tt0128442">Rounders (actor)</a></li><li><a href="../resources/page.php?q=tt0162346">Ghost World (producer)</a></li><li><a href="../resources/page.php?q=tt0300149">The Loner (producer)</a></li><li><a href="../resources/page.php?q=tt0467406">Juno (producer)</a></li><li><a href="../resources/page.php?q=tt0887883">Burn After Reading (actor)</a></li><li><a href="../resources/page.php?q=tt0961109">Bloody Mondays &amp; Strawberry Pies (actor)</a></li><li><a href="../resources/page.php?q=tt1028576">Secretariat (actor)</a></li><li><a href="../resources/page.php?q=tt10751170">All Noncombatants Please Clear the Set (self)</a></li><li><a href="../resources/page.php?q=tt12545180">Ten Year Old Tom (actor)</a></li><li><a href="../resources/page.php?q=tt13108124">Unsinkable (actor)</a></li><li><a href="../resources/page.php?q=tt1659337">The Perks of Being a Wallflower (producer)</a></li><li><a href="../resources/page.php?q=tt18177528">The New Look (actor)</a></li><li><a href="../resources/page.php?q=tt1860357">Deepwater Horizon (actor)</a></li><li><a href="../resources/page.php?q=tt30253218">John Malkovich: The Music Critic (actor)</a></li><li><a href="../resources/page.php?q=tt3672994">Le paradoxe de John Malkovich (actor)</a></li><li><a href="../resources/page.php?q=tt6543762">Psychogenic Fugue (actor)</a></li><li><a href="../resources/page.php?q=tt7165196">Hell (actor)</a></li><li><a href="../resources/page.php?q=tt8870836">Russia from Above (actor)</a></li><li><a href="../resources/page.php?q=tt8870836">Russia from Above (actor)</a></li>
            </ul>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>