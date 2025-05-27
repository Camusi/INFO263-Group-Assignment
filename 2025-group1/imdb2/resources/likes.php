<?php
if (!empty($_SESSION['userID'])) {
    session_start();
} else {
    http_response_code(403);
    header("Location: ../signin.php?error=Please%20log%20in%20to%20like%20or%20dislike%20content.");
    exit;
}

$type = $_GET['type'] ?? '';
$ID = $_GET['ID'] ?? '';
$ld = $_GET['ld'] ?? 'like';

if ($type !== 'person' && $type !== 'title') {
    http_response_code(400);
    echo "Invalid type parameter.";
    exit;
}

$db = new SQLite3('../resources/imdb-2.sqlite3');

$table = $type === 'person' ? 'name_basics_trim' : 'title_basics_trim';
$id_col = $type === 'person' ? 'nconst' : 'tconst';
$increment = ($ld === 'like') ? 1 : -1;

// Prepare and execute the update statement
$stmt = $db->prepare("UPDATE $table SET likes = likes + :inc WHERE $id_col = :id");
$stmt->bindValue(':inc', $increment, SQLITE3_INTEGER);
$stmt->bindValue(':id', $ID, SQLITE3_TEXT);

if ($stmt->execute()) {
    echo "Success";
} else {
    http_response_code(500);
    echo "Database update failed.";
}
$db->close();
?>
