<?php
session_start();
if (!isset($_SESSION['userID']) || empty($_SESSION['userID'])) {
    http_response_code(403);
    exit("Please log in.");
}

$userID = $_SESSION['userID'];
$type = $_GET['type'] ?? '';
$pageID = $_GET['id'] ?? '';
$ld = $_GET['ld'] ?? 'like';

if (!in_array($type, ['person', 'title']) || empty($pageID)) {
    http_response_code(400);
    exit("Invalid request.");
}

$valueMap = ['like' => 1, 'dislike' => -1, 'unlike' => 0, 'undislike' => 0];
if (!isset($valueMap[$ld])) {
    http_response_code(400);
    exit("Invalid like/dislike action.");
}
$value = $valueMap[$ld];

$db = new PDO('../resources/imdb-2.sqlite3');

// Create the `likes` table if it doesn't exist (optional safety)
$db->exec("
    CREATE TABLE IF NOT EXISTS likes (
        likeID INTEGER PRIMARY KEY AUTOINCREMENT,
        userID TEXT NOT NULL,
        pageID TEXT NOT NULL,
        type TEXT CHECK(type IN ('title', 'person')),
        value INTEGER CHECK(value IN (-1, 1)),
        UNIQUE(userID, pageID)
    );
");

// 1. Update global like count if adding/removing
$table = $type === 'person' ? 'name_basics_trim' : 'title_basics_trim';
$id_col = $type === 'person' ? 'nconst' : 'tconst';
$inc = ($ld === 'like') ? 1 : (($ld === 'dislike') ? -1 : 0);

if ($inc !== 0) {
    $stmt = $db->prepare("UPDATE $table SET likes = COALESCE(likes, 0) + :inc WHERE $id_col = :id");
    $stmt->bindValue(':inc', $inc, SQLITE3_INTEGER);
    $stmt->bindValue(':id', $pageID, SQLITE3_TEXT);
    $stmt->execute();
}

// 2. Update the per-user like tracking
if ($value !== 0) {
    $stmt = $db->prepare("
        INSERT OR REPLACE INTO likes (userID, pageID, type, value)
        VALUES (:uid, :pid, :type, :val)
    ");
    $stmt->bindValue(':uid', $userID, SQLITE3_TEXT);
    $stmt->bindValue(':pid', $pageID, SQLITE3_TEXT);
    $stmt->bindValue(':type', $type, SQLITE3_TEXT);
    $stmt->bindValue(':val', $value, SQLITE3_INTEGER);
    $stmt->execute();
} else {
    // Removing like/dislike
    $stmt = $db->prepare("DELETE FROM likes WHERE userID = :uid AND pageID = :pid");
    $stmt->bindValue(':uid', $userID, SQLITE3_TEXT);
    $stmt->bindValue(':pid', $pageID, SQLITE3_TEXT);
    $stmt->execute();
}

// 3. Return updated total likes count
$query = $db->prepare("SELECT likes FROM $table WHERE $id_col = :id");
$query->bindValue(':id', $pageID, SQLITE3_TEXT);
$res = $query->execute();
$likes = $res->fetchArray(SQLITE3_ASSOC)['likes'] ?? '?';

echo json_encode(['likes' => $likes]);
$db->close();
?>
