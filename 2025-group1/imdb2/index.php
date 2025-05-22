<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>IMDB2.0 by Group 1 2025S1</title>
  <!--JS Files-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="resources/search.js"></script>
  <!-- External CSS Stylesheet Import-->
  <link rel="stylesheet" href="resources/style.css" />
</head>
<!-- Consequences of CSS Styling-->
<body>
  <header class="header">
    <h1>IMDB2.0</h1>
    <p>RAHHHHH</p>
  </header>

  <?php include 'resources/navbar.php'; ?>
  </nav>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
  

  <main class="main-content">
    <h2>Welcome to the testing Site</h2>
    <p>This is a testing homepage.</p>

    <h2>Movie Overview</h2>
    <div class="movie-list">
      <div class="movie-card">
        <img src="resources/img/load.gif" alt="Movie 1 Poster" class="movie-poster">
        <div class="movie-details">
          <h3>Movie Title 1</h3>
          <p><strong>Genre:</strong> Example, Example</p>
          <p><strong>Running Years:</strong> 0000-9999</p>
          <p><strong>Runtime:</strong> 999 mins</p>
          <p><strong>Director:</strong> John Doe</p>
          <p><strong>Writer:</strong> John Doe</p>
          <p><strong>Actors:</strong> Actor A, Actor B, Actor C</p>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </div>
      </div>
      <div class="movie-card">
        <img src="resources/img/load.gif" alt="Movie 2 Poster" class="movie-poster">
        <div class="movie-details">
          <h3>Movie Title 2</h3>
          <p><strong>Genre:</strong> Example, Example</p>
          <p><strong>Running Years:</strong> 0000-9999</p>
          <p><strong>Runtime:</strong> 999 mins</p>
          <p><strong>Director:</strong> John Doe</p>
          <p><strong>Writer:</strong> John Doe</p>
          <p><strong>Actors:</strong> Actor D, Actor E, Actor F</p>
          <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>
        </div>
      </div>
      <div class="movie-card">
        <img src="resources/img/load.gif" alt="Movie 3 Poster" class="movie-poster">
        <div class="movie-details">
          <h3>Movie Title 3</h3>
          <p><strong>Genre:</strong> Example, Example</p>
          <p><strong>Running Years:</strong> 0000-9999</p>
          <p><strong>Runtime:</strong> 999 mins</p>
          <p><strong>Director:</strong> John Doe</p>
          <p><strong>Writer:</strong> John Doe</p>
          <p><strong>Actors:</strong> Actor G, Actor H, Actor I</p>
          <p>Duis aute irure dolor in reprehenderit in voluptate velit esse.</p>
        </div>
      </div>
    </div>
  </main>

  <?php include 'resources/footer.php'; ?>
</body>
</html>