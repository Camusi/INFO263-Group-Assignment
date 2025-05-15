<?php
require_once 'connection.php';
require_once 'database.php';
require_once './objects/Title.php';

$title = $_GET['title'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pageSize = 10;

// Fetch results
$results = getTitles($page, $pageSize, $title);
$totalCount = getTitleCount($title);
$totalPages = ceil($totalCount / $pageSize);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Search Results for "<?= htmlspecialchars($title) ?>"</title>
</head>
<body>
    <h1>Search Results for "<?= htmlspecialchars($title) ?>"</h1>

    <?php if (empty($results)):  ?>
        <p>No titles found.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($results as $title): ?>
                <li>
                    <strong><?= htmlspecialchars((string)$title->getPrimaryTitle()) ?></strong>
                    (<?= htmlspecialchars((string)$title->getRating()) ?>)
                    - Rating: <?= htmlspecialchars((string)$title->getRating()) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Pagination -->
        <div>
            <?php if ($page > 1): ?>
                <a href="?title=<?= urlencode($title) ?>$page=<?= $page - 1 ?>">Previous</a>
            <?php endif; ?>

            Page <?= $page ?> of <?= $totalPages ?>

            <?php if ($page < $totalPages): ?>
                <a href="?title=<?= urlencode($title) ?>&page=<?= $page + 1 ?>">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
