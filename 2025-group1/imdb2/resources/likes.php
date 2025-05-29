<?php
session_start();
if (!empty($_SESSION['userID'])) {
    http_response_code(403);
    header('Location: ../index.php?error=403');
}

$userID = $_SESSION['userID'];
$pageID = $_GET['id'] ?? '';
$ld = $_GET['ld'] ?? 'like';

if (empty($pageID)) {
    http_response_code(400);
    exit("Missing page ID.");
}

// Determine value to store
$valueMap = ['like' => 1, 'dislike' => -1, 'unlike' => 0, 'undislike' => 0];
if (!isset($valueMap[$ld])) {
    http_response_code(400);
    exit("Invalid action.");
}
$value = $valueMap[$ld];

try {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Determine which table this is (title or person)
    $table = str_starts_with($pageID, 'tt') ? 'title_basics_trim' : 'name_basics_trim';
    $id_col = str_starts_with($pageID, 'tt') ? 'tconst' : 'nconst';

    // Update like count if like/dislike
    $inc = ($ld === 'like') ? 1 : (($ld === 'dislike') ? -1 : 0);
    if ($inc !== 0) {
        $stmt = $db->prepare("UPDATE $table SET likes = COALESCE(likes, 0) + :inc WHERE $id_col = :id");
        $stmt->bindValue(':inc', $inc, PDO::PARAM_INT);
        $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Update user-level like tracking
    if ($value !== 0) {
        $likeID = $userID . '_' . $pageID;
        $stmt = $db->prepare("
            INSERT INTO likes (likeID, userID, pageID, value)
            VALUES (:lid, :uid, :pid, :val)
            ON CONFLICT(likeID) DO UPDATE SET value = :val
        ");
        $stmt->bindValue(':lid', $likeID, PDO::PARAM_STR);
        $stmt->bindValue(':uid', $userID, PDO::PARAM_STR);
        $stmt->bindValue(':pid', $pageID, PDO::PARAM_STR);
        $stmt->bindValue(':val', $value, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Remove user’s like/dislike
        $stmt = $db->prepare("DELETE FROM likes WHERE userID = :uid AND pageID = :pid");
        $stmt->bindValue(':uid', $userID, PDO::PARAM_STR);
        $stmt->bindValue(':pid', $pageID, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Return updated like count
    $stmt = $db->prepare("SELECT likes FROM $table WHERE $id_col = :id");
    $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
    $stmt->execute();
    $likes = $stmt->fetch(PDO::FETCH_ASSOC)['likes'] ?? '?';

    echo json_encode(['likes' => $likes]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
