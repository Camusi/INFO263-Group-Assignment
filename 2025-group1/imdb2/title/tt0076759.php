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
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p>Star Wars: Episode IV - A New Hope (1977)</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id=tt0076759" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text"><br><span id="page-warning">The synopsis for this page is a stub. Help improve this page by adding more details!</span><br><br><br><span id="page-warning">This page is automatically generated based on data from an IMDB database export. Information may be out of date or not accurately reflect reality.</span><br><br></p>
        </div>
    </section>
    <main class="main-content">
        <div id="title"><h1>Star Wars: Episode IV - A New Hope (1977)</h1></div>
        <aside id="blurb"><p>Luke Skywalker joins forces with a Jedi Knight, a cocky pilot, a Wookiee and two droids to save the galaxy from the Empire&#x27;s world-destroying battle station, while also attempting to rescue Princess Leia from the mysterious Darth Vader.</p></aside>
        <figure id="poster"><img src="https://m.media-amazon.com/images/M/MV5BOGUwMDk0Y2MtNjBlNi00NmRiLTk2MWYtMGMyMDlhYmI4ZDBjXkEyXkFqcGc@._V1_.jpg" width="250" alt="Poster for Star Wars: Episode IV - A New Hope" title="Poster for Star Wars: Episode IV - A New Hope from imdb.com"></figure>
        <div id="rating">
            <?php
                try {
                    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
                    exit;
                }
                

                $stmt = $db->prepare('SELECT likes FROM title_basics_trim WHERE tconst = \'tt0076759\'');
                $stmt->execute();
                $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $likes = $likes[0]['likes'] ?? '?'; // Default to ? if no likes found
                ?>
            <p><span><?php echo $likes; ?></span> Likes!</p>
            <button id="like-button">I like this!</button>
            <button id="dislike-button">I dislike this!</button>
        </div>
        <div id="people">
            <h2>Notable People:</h2>
                <strong>Director(s):</strong> <a href="../resources/page.php?q=nm0000184">George Lucas</a>
                <br><strong>Writer(s):</strong> <a href="../resources/page.php?q=nm0000184">George Lucas</a>
                <br><strong>Starring:</strong> <a href="../resources/page.php?q=nm0000434">Mark Hamill</a>, <a href="../resources/page.php?q=nm0000148">Harrison Ford</a>, <a href="../resources/page.php?q=nm0000402">Carrie Fisher</a>, <a href="../resources/page.php?q=nm0000027">Alec Guinness</a>, <a href="../resources/page.php?q=nm0001088">Peter Cushing</a>, <a href="../resources/page.php?q=nm0000355">Anthony Daniels</a>, <a href="../resources/page.php?q=nm0048652">Kenny Baker</a>, <a href="../resources/page.php?q=nm0562679">Peter Mayhew</a>, <a href="../resources/page.php?q=nm0001190">David Prowse</a>, <a href="../resources/page.php?q=nm0114436">Phil Brown</a></strong>
                <br><strong>Other Notable People:</strong> <a href="../resources/page.php?q=nm0476030">Gary Kurtz (producer)</a>, <a href="../resources/page.php?q=nm0564768">Rick McCallum (producer)</a>, <a href="../resources/page.php?q=nm0002354">John Williams (Unknown Job (You can add this!))</a>, <a href="../resources/page.php?q=nm0852405">Gilbert Taylor (director of photography)</a>, <a href="../resources/page.php?q=nm0156816">Richard Chew (film editor)</a>, <a href="../resources/page.php?q=nm0160628">T.M. Christopher (editor)</a>, <a href="../resources/page.php?q=nm0386532">Paul Hirsch (film editor)</a>, <a href="../resources/page.php?q=nm0524235">Marcia Lucas (film editor)</a>, <a href="../resources/page.php?q=nm0188240">Dianne Crittenden (casting_director)</a>, <a href="../resources/page.php?q=nm0482961">Irene Lamb (casting_director)</a>, <a href="../resources/page.php?q=nm0708525">Vic Ramos (casting_director)</a>, <a href="../resources/page.php?q=nm0058045">John Barry (Unknown Job (You can add this!))</a>
        </div>
        <details id="plot" title="Plot Summary">
            <summary><h2>Plot:</h2></summary>
            <p>Luke Skywalker joins forces with a Jedi Knight, a cocky pilot, a Wookiee and two droids to save the galaxy from the Empire&#x27;s world-destroying battle station, while also attempting to rescue Princess Leia from the mysterious Darth Vader.</p>
        </details>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>