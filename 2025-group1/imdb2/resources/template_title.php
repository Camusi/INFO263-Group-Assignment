<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{NAME} ({YEAR}) on IMDB2.0</title>
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
        <div id="title"></div>
        <div id="poster"></div>
        <div id="rating"></div>
        <div id="people"></div>
        <div id="plot"></div>
        <div id="comments"></div>
    </main>
    <footer class="footer">
        <p>&copy; 2025 IMDB2.0. All rights reserved.</p>
        <p>Contact: <a href="mailto:constatine.zakkaroff@canterbury.ac.nz">Constatine Zakkaroff</a></p>
      </body>
</html>