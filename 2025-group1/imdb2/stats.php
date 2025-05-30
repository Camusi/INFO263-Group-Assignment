<?php
try {
    $pdo = new PDO('sqlite:./resources/imdb-2.sqlite3');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Film count per genre
    $stmt = $pdo->query("SELECT genres FROM title_basics_trim WHERE genres IS NOT NULL AND genres != ''");

    $genreCounts = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $genres = explode(',', $row['genres']);
        foreach ($genres as $genre) {
            $genre = trim($genre);
            if ($genre !== '') {
                if (!isset($genreCounts[$genre])) {
                    $genreCounts[$genre] = 0;
                }
                $genreCounts[$genre]++;
            }
        }
    }

    arsort($genreCounts);

    $topN = 8;
    $topGenres = array_slice($genreCounts, 0, $topN, true);
    $otherCount = array_sum(array_slice($genreCounts, $topN));

    if ($otherCount > 0) {
        $topGenres['Other'] = $otherCount;
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Genre Statistics | IMDB2</title>
    <link rel="stylesheet" href="resources/style.css" />
    <title>Genre Distribution Pie Chart</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="resources/search.js"></script>
</head>
<body>

<header class="header">
    <h1>IMDB2.0</h1>
    <p>Some featured titles from our database</p>
</header>

<?php include 'resources/navbar.php'; ?>
<div class="search-results">
    <p id="search-output"></p>
</div>

<div style="display: flex; justify-content: center; text-align: center; flex-direction: column">
    <div>
        <h1>Film Genre Distribution</h1>
        <p>Number of films by genre in the database</p>
    </div>
    <div style="display: flex; justify-content: center;">
        <div style="height: 40vw; width: 40vw; margin-bottom: 10vw;">
            <canvas id="genrePieChart"></canvas>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('genrePieChart').getContext('2d');

    const data = {
        labels: <?php echo json_encode(array_keys($topGenres)); ?>,
        datasets: [{
            label: 'Number of Films',
            data: <?php echo json_encode(array_values($topGenres)); ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#66FF66', '#FF6666', '#CCCCCC'
            ],
            hoverOffset: 30
        }]
    };

    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                },
                title: {
                    display: true,
                }
            }
        }
    };

    new Chart(ctx, config);
</script>

<br><?php include 'resources/footer.php'; ?>
</body>
</html>
