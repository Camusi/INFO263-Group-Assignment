<?php
require_once 'database.php';

$title_id = $_GET['tconst'] ?? null;
if (!$title_id) {
    die("Invalid Title ID :(");
}

$pdo = openConnection();

$stmt = $pdo->prepare("SELECT * FROM title_basics_trim WHERE tconst = ?");
$stmt->execute([$title_id]);
$title = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$title) {
    die("Title not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title['name']) ?></title>
</head>
</html>
