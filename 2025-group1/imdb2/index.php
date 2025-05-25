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
<body>
  <header class="header">
    <h1>IMDB2.0</h1>
    <p>Your new home of all things media!</p>
  </header>

  <?php include 'resources/navbar.php'; ?>
  </nav>
  <div class="search-results">
    <p id="search-output"></p>
  </div>
  

  <main class="main-content">
    <h2>Welcome to IMDB2.0</h2>
    <p>Your one-stop destination for all things movies! Search for a movie above or browse through our extensive database of over 211,000 titles and 3 million people.</p>
    <hr>
    <section class="featured-content">
      <h2>Featured Movies/Shows</h2>
      <p>These are the all time top movies and shows on IMDB2.0!</p>
    <div class="movie-list">
      <div class="movie-card" id="topMovie1">
        <img id="topMovie1_img" src="resources/img/load.gif" alt="Movie 1 Poster" class="movie-poster">
        <div class="movie-details">
          <h3>{topMovie1_title}</h3>
          <p><strong>Genre:</strong>{topMovie1_genre}</p>
          <p><strong>Year:</strong>{topMovie1_year}</p>
          <p><strong>Director:</strong>{topMovie1_director}</p>
          <p><strong>Writer:</strong>{topMovie1_writer}</p>
          <p><strong>Actors:</strong>{topMovie1_talent}</p>
          <p>{topMovie1_blurb}</p>
        </div>
      </div>
      <div class="movie-card" id="TopMovie2">
        <img id="TopMovie2_img" src="resources/img/load.gif" alt="Movie 2 Poster" class="movie-poster">
        <div class="movie-details">
          <h3>{TopMovie2_title}</h3>
          <p><strong>Genre:</strong>{TopMovie2_genre}</p>
          <p><strong>Year:</strong>{TopMovie2_year}</p>
          <p><strong>Director:</strong>{TopMovie2_director}</p>
          <p><strong>Writer:</strong>{TopMovie2_writer}</p>
          <p><strong>Actors:</strong>{TopMovie2_talent}</p>
          <p>{TopMovie2_blurb}</p>
        </div>
      </div>
      <div class="movie-card" id="topMovie3">
        <img id="topMovie3_img" src="resources/img/load.gif" alt="Movie 3 Poster" class="movie-poster">
        <div class="movie-details">
          <h3>{topMovie3_title}</h3>
          <p><strong>Genre:</strong>{topMovie3_genre}</p>
          <p><strong>Year:</strong>{topMovie3_year}</p>
          <p><strong>Director:</strong>{topMovie3_director}</p>
          <p><strong>Writer:</strong>{topMovie3_writer}</p>
          <p><strong>Actors:</strong>{topMovie3_talent}</p>
          <p>{topMovie3_blurb}</p>
        </div>
      </div>
    </div>
    <hr>
    <h2>Featured People</h2>
    <p>These are the all time top people on IMDB2.0!</p>
    <div class="person-list">
      <div class="person-card" id="topPerson1">
        <div class="person-details">
          <h3>{topPerson1_name}</h3>
          <p><strong>Known For:</strong>{topPerson1_knownFor}</p>
          <p><strong>Birth Year:</strong>{topPerson1_birthYear}</p>
          <p><strong>Death Year:</strong>{topPerson1_deathYear}</p>
        </div>
      </div>
      <div class="person-card" id="topPerson2">
        <div class="person-details">
          <h3>{topPerson2_name}</h3>
          <p><strong>Known For:</strong>{topPerson2_knownFor}</p>
          <p><strong>Birth Year:</strong>{topPerson2_birthYear}</p>
          <p><strong>Death Year:</strong>{topPerson2_deathYear}</p>
        </div>
      </div>
      <div class="person-card" id="topPerson3">
        <div class="person-details">
          <h3>{topPerson3_name}</h3>
          <p><strong>Known For:</strong>{topPerson3_knownFor}</p>
          <p><strong>Birth Year:</strong>{topPerson3_birthYear}</p>
          <p><strong>Death Year:</strong>{topPerson3_deathYear}</p>
        </div>
      </div>
    </div>
    </section>
  </main>

  <?php include 'resources/footer.php'; ?>
</body>
</html>