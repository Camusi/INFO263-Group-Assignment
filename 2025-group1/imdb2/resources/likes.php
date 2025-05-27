<?php
$id = isset($_GET['id']) ? trim($_GET['id']) : '';

try {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($type === 'title') {
    $type_sql = 'SELECT likes FROM title_basics_trim WHERE tconst = :id';
} elseif ($type === 'person') {
    $type_sql = 'SELECT likes FROM name_basics_trim WHERE nconst = :id';
}

$stmt = $db->prepare($type_sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
    <title>Likes</title>
</head>
<body>
    <p><span><?php echo $likes; ?></span> Likes!</p>
            <button id="like-button">I like this!</button>
            <button id="dislike-button">I dislike this!</button>
</body>
</html>