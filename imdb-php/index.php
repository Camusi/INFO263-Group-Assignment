<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IMDB 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<main role="main" class="container bg-light">
    <?php include_once 'navigation.php' ?>
    <?php
    $php = "/Applications/AMPPS/apps/php82/bin/php";
    $lastIndex = @file_get_contents('last_index.txt');
    $runIndex = "$php " . __DIR__ . "/build_index.php >> " . __DIR__ . "/indexlog.log 2>&1 &";
    if (!$lastIndex || time() - $lastIndex > 86400) {
        exec($runIndex);
        file_put_contents('last_index.txt', time());
    }
    ?>

    <div class="row justify-content-center my-4">
        <img class="img-thumbnail img-banner" src="images/yoda.jpeg" alt="Yoda image not found :("/>
        <h4 class="text-center">Welcome, please enter your query</h4>
    </div>

    <div class="row align-middle align-items-center py-2">
        <div class="offset-2 col-7 align-middle">
            <input id="search-input" class="form-control" type="text" name="search" placeholder="Search for a Film, Series, Person, ..." />
        </div>

        <div class="col-2 d-grid col-2">
            <button id="search-button" type="submit" class="btn btn-warning" formaction="api.php" name="schbtn">Search</button>
        </div>
    </div>

</main>

<!-- JS scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="js/home.js"></script>
</body>
</html>