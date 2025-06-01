<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../signin.php?error=You%20must%20be%20logged%20in%20to%20rank%20pages.");
    exit;
}

$userID = $_SESSION['userID'];
$reqpageID = $_GET['id'];
$ld = $_GET['ld'] ?? 'like';
$type = substr($reqpageID, 0, 2) === 'tt' ? 'title' : 'person';

// Value logic
$value = match($ld) {
    'like' => 1,
    'dislike' => -1,
    'unlike', 'undislike' => 0,
    default => null
};

if ($value === null || empty($reqpageID)) {
    header("Location: ../index.php?error=Invalid+like+action");
    exit;
}

function getLikes($pageID) {
    $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
    $stmt = $db->prepare("SELECT SUM(value) as total FROM likes WHERE pageID = :id");
    $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
}

function updateLikes($type, $pageID) {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $table = $type === 'title' ? 'title_basics_trim' : 'name_basics_trim';
    $id_col = $type === 'title' ? 'tconst' : 'nconst';
    
    $totalLikes = getLikes($pageID);
    
    $stmt = $db->prepare("UPDATE $table SET likes = :likes WHERE $id_col = :id");
    $stmt->bindValue(':likes', $totalLikes, PDO::PARAM_INT);
    $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
    return $stmt->execute();
}

function updateUserLikes($userID, $pageID, $value) {
    $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
    $likeID = $userID . '_' . $pageID;
    
    if ($value === 0) {
        $stmt = $db->prepare("DELETE FROM likes WHERE likeID = :lid");
        $stmt->bindValue(':lid', $likeID, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    $stmt = $db->prepare("
        INSERT INTO likes (likeID, userID, pageID, value)
        VALUES (:lid, :uid, :pid, :val)
        ON CONFLICT(likeID) DO UPDATE SET value = :val
    ");
    $stmt->bindValue(':lid', $likeID, PDO::PARAM_STR);
    $stmt->bindValue(':uid', $userID, PDO::PARAM_STR);
    $stmt->bindValue(':pid', $pageID, PDO::PARAM_STR);
    $stmt->bindValue(':val', $value, PDO::PARAM_INT);
    return $stmt->execute();
}

// Process request
$returnTo = $_GET['return_to'] ?? null;
$requestActions = isset($_GET['q']) ? str_split($_GET['q']) : [];

if ($returnTo) {
    foreach ($requestActions as $action) {
        switch ($action) {
            case '2':
                if (!updateUserLikes($userID, $reqpageID, $value)) {
                    header("Location: ../index.php?error=Failed+to+update+like");
                    exit;
                }
                updateLikes($type, $reqpageID);
                break;
        }
    }
    header("Location: $returnTo");
    exit;
}

// API responses
foreach ($requestActions as $action) {
    switch ($action) {
        case '0': echo getLikes($reqpageID); break;
        case '2': updateLikes($type, $reqpageID); break;
        case '3': 
            updateUserLikes($userID, $reqpageID, $value); 
            echo "ok"; 
            break;
        default: echo "Invalid action"; break;
    }
}
?>