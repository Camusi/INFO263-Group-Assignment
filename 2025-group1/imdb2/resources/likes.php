<?php
$type = $_GET['type'] ?? '';
$ID = $_GET['ID'] ?? '';
$ld = $_GET['ld'] ?? 'like';

if ($type !== 'person' && $type !== 'title') {
    http_response_code(400);
    echo "Invalid type parameter.";
    exit;
}

$dbname = $type === 'person' ? 'name_basics_trim' : 'title_basics_trim';
$id_column = $type === 'person' ? 'nconst' : 'tconst';

$conn = new mysqli('localhost', 'root', '', $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed: " . $conn->connect_error;
    exit;
}

$updateOp = ($ld === 'dislike') ? 'likes = GREATEST(likes - 1, 0)' : 'likes = likes + 1';

$stmt = $conn->prepare("UPDATE $dbname SET $updateOp WHERE $id_column = ?");
$stmt->bind_param('s', $ID);

if ($stmt->execute()) {
    echo ($ld === 'dislike') ? "Dislike registered." : "Like updated successfully.";
} else {
    http_response_code(500);
    echo "Error updating like.";
}

$stmt->close();
$conn->close();
?>
