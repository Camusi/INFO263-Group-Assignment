<?php
session_start();

// Connect to SQLite database
try {
    $pdo = new PDO('sqlite:./resources/imdb-2.sqlite3');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch top 3 liked movies
    $stmt = $pdo->query("SELECT primaryTitle, startYear, genres, image_url, tconst
                        FROM title_basics_trim
                        ORDER BY IFNULL(likes, 0) DESC
                        LIMIT 3");
    $topMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch top 3 liked people
    $stmt = $pdo->query("
        SELECT
            primaryName,
            birthYear,
            deathYear,
            primaryProfession,
            likes,
            nconst
        FROM name_basics_trim
        ORDER BY IFNULL(likes, 0) DESC
        LIMIT 3
    ");

    $topPeople = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    $topMovies = [];
    $topPeople = [];
    exit();
}

function safe($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES);
}
?>
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
<div class="search-results">
    <p id="search-output"></p>
</div>
<div id="warnings">
    <?php if (!empty($_GET['error'])): ?>
        <p id="page-warning"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    </div>
<main class="main-content">
    <h2>Welcome to IMDB2.0</h2>
    <p>Your one-stop destination for all things movies! Search for a movie above or browse through our extensive database of over 211,000 titles and 3 million people.</p>
    <hr>
    <section class="featured-content">
        <h2>Featured Movies/Shows</h2>
        <p>These are the all time top movies and shows on IMDB2.0!</p><br>
        <div class="movie-list">
            <?php for ($i = 0; $i < 3; $i++): ?>
                <?php if (isset($topMovies[$i])): ?>
                    <div class="movie-card" id="topMovie<?= $i + 1 ?>">
                        <img id="top-movie-poster-<?= $i ?>" src="resources/img/load.gif" alt="Movie <?= $i + 1 ?> Poster" class="movie-poster" data-tconst="<?= safe($topMovies[$i]['tconst']) ?>">
                        <div class="movie-details">
                            <h3><a href="resources/page.php?q=<?= safe($topMovies[$i]['tconst']) ?>"><?= safe($topMovies[$i]['primaryTitle']) ?></a></h3>
                            <p><strong>Genre:</strong> <?= safe($topMovies[$i]['genres']) ?></p>
                            <p><strong>Year:</strong> <?= safe($topMovies[$i]['startYear']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div><br>
        <hr>
        <h2>Featured People</h2>
        <p>These are the all time top people on IMDB2.0!</p>
        <div class="person-list">
            <?php foreach ($topPeople as $index => $person): ?>
                <div class="person-card" id="topPerson<?= $index + 1 ?>">
                    <div class="person-details">
                        <h3><a href="resources/page.php?q=<?= safe($person['nconst']) ?>"><?= htmlspecialchars($person['primaryName']) ?></a></h3>
                        <p><strong>Known For:</strong> <?= htmlspecialchars($person['primaryProfession']) ?></p>
                        <p><strong>Birth Year:</strong> <?= $person['birthYear'] ?: 'N/A' ?></p>
                        <p><strong>Death Year:</strong> <?= $person['deathYear'] ?: 'N/A' ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'resources/footer.php'; ?>


</body>
<script>
	document.addEventListener("DOMContentLoaded", function () {
        console.log("DOM fully loaded and parsed");
        const images = document.querySelectorAll(".movie-poster");
        console.log("Found images:", images);
        images.forEach((img, index) => {
            $.ajax({
                url: `resources/cover-image.php?q=${img.dataset.tconst}`,
                method: 'GET',
                dataType: 'json',
                success: function (imgData) {
                    if (imgData && imgData.cover_image) {
                        img.src = imgData.cover_image;
                    }
                },
                error: function () {
                    img.src = "resources/img/load.gif";
                    console.error("Error loading image for movie:", img.dataset.tconst);
                }
            });
        });
	});
</script>
</html>