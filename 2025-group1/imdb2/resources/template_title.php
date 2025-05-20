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

  <nav class="navbar">
    <div class="nav-left">
      <a href="/">Home</a>
      <a href="about.php">About</a>
    </div>
    <div class="nav-center">
      <input type="text" id="search-input" name="search-input" placeholder="Start typing to search..." />
    </div>
    <div class="nav-right">
      <a href="signin.php">Sign In</a>
      <p class="account-preview">👤 Guest</p>
    </div>
  </nav>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
    <main class="main-content">
        <h2>Movie Overview</h2>
        <div class="movie-list">
        <div class="movie-card">
            <img src="../resources/img/load.gif" alt="{NAME} Poster" class="movie-poster">
            <div class="movie-details">
            <h3>{NAME}</h3>
            <p><strong>Genre:</strong> {GENRES}</p>
            <p><strong>Running Years:</strong> {YEAR}</p>
            <p><strong>Runtime:</strong> 999 mins</p>
            <p><strong>Director:</strong> John Doe</p>
            <p><strong>Writer:</strong> John Doe</p>
            <p><strong>Actors:</strong> Actor A, Actor B, Actor C</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
        </div>
        </div>
</body>
</html>