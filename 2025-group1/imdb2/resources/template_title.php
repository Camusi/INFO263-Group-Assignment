<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{NAME} ({YEAR}) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
    <script src="../resources/like.js"></script>
</head>
<body>
    <header class="header">
    <h1>IMDB2.0</h1>
    <p>{NAME} ({YEAR})</p>
  </header>
  <?php include '../resources/navbar.php'; ?>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <section class="edit-page-button">
        <a href="../edit.php?type=title&id={ID}" class="edit-button"><button>Edit This Page</button></a>
        <div id="warnings">
            <p id="warning-text">{WARNINGS}</p>
        </div>
    </section>
    <main class="main-content">
        <div id="title"><h1>{NAME} ({YEAR})</h1></div>
        <aside id="blurb"><p>{BLURB}</p></aside>
        <figure id="poster"><img src="{POSTER}" width="250" alt="Poster for {NAME}" title="Poster for {NAME} from imdb.com"></figure>
        <div id="rating">
            <p><span>{VOTES}</span> Likes!</p>
            <button id="like-button">I like this!</button>
            <button id="dislike-button">I dislike this!</button>
        </div>
        <div id="people">
            <h2>Notable People:</h2>
                <strong>Director:</strong> {DIRECTOR}
                <br><strong>Writers:</strong> {WRITERS}
                <br><strong>Stars:</strong> {STARS}</strong>
                <br><strong>Other Notable People:</strong> {NOTABLE}
        </div>
        <details id="plot" title="Plot Summary">
            <summary>Spoiler: Plot Summary</summary>
            <h2>Plot:</h2>
            <p>{PLOT}</p>
        </details>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>