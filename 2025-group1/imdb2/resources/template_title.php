<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{NAME} ({YEAR}) | IMDB2.0</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../resources/search.js"></script>
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
    <main class="main-content">
        <h2>Movie Overview</h2>
        <div id="title"><h1>{NAME} ({YEAR})</h1></div>
        <div id="poster"><img src="{POSTER}" width="250" alt="Poster for {NAME}" title="Poster for {NAME} from imdb.com"></div>
        <div id="rating"></div>
        <div id="people">
            <h2>Notable People:</h2>
                <strong>Director:</strong> {DIRECTOR}
                <br><strong>Writers:</strong> {WRITERS}
                <br><strong>Stars:</strong> {STARS}</strong>
        </div>
        <div id="plot">
            <h2>Plot:</h2>
            <p>{PLOT}</p>
        </div>
        <div id="comments"></div>
    </main>
    <?php include '../resources/footer.php'; ?>
  </body>
</html>