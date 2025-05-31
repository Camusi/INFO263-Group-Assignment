<?php
session_start();
if (!isset($_SESSION["userID"] )) {
    header("Location: signin.php?error=You%20must%20be%20logged%20in%20to%20rank%20pages.");
    exit;
}

$userID = $_SESSION['userID'];
$pageID = $_GET['id'] ?? '';
// ld = like/dislike/unlike/undislike
$ld = $_GET['ld'] ?? 'like';
$type = substr($pageID, 0, 2) === 'tt' ? 'title' : 'person';
$table = substr($pageID, 0, 2) === 'tt' ? 'title_basics_trim' : 'name_basics_trim';
$id_col = substr($pageID, 0, 2) === 'tt' ? 'tconst' : 'nconst';

// Value logic
$value = 0;
if ($ld === 'like' || $ld === 'undislike') {
    $value = 1;
} elseif ($ld === 'dislike' || $ld === 'unlike') {
    $value = -1;
} else {
    http_response_code(400);
    header("Location: index.php?error=Sorry, but you cannot rate the page at this time. (invalid like action [23])");
    exit;
}
// Check if the page ID is valid
if (empty($pageID)) {
    http_response_code(400);
    header("Location: index.php?error=A fatal error occurred when liking a page. Please try again later. (page disappeared [29])");
    exit;
}




function getLikes($type, $pageID) {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $table = $type === 'title' ? 'title_basics_trim' : 'name_basics_trim';
    $id_col = $type === 'title' ? 'tconst' : 'nconst';
    $query = 'SELECT likes FROM ' . $table . ' WHERE ' . $id_col . ' = :id';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['likes'] ?? 0;
}

function getLikers($pageID) {
    $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
    $table = 'likes';
    $query = 'SELECT userID, value FROM ' . $table . ' WHERE pageID = :id';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
    $stmt->execute();
    $likers = [];
    $likedBy = [];
    $dislikedBy = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['value'] == 1) {
            $likedBy[] = $row['userID'];
        } elseif ($row['value'] == -1) {
            $dislikedBy[] = $row['userID'];
        }
    }
    return "<b>Liked by:</b> " . implode(', ', $likedBy) . 
           "<br><b>Disliked by:</b> " . implode(', ', $dislikedBy);
}

function updateLikes($type, $pageID, $value) {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $table = $type === 'title' ? 'title_basics_trim' : 'name_basics_trim';
    $id_col = $type === 'title' ? 'tconst' : 'nconst';
    $query = 'UPDATE ' . $table . ' SET likes = :likes WHERE ' . $id_col . ' = :id';
    $stmt = $db->prepare($query);
    $likes = getLikes($type, $pageID) + $value;
    $stmt->bindValue(':likes', $likes, PDO::PARAM_INT);
    $stmt->bindValue(':id', $pageID, PDO::PARAM_STR);
    return $stmt->execute();
}

function updateUserLikes($userID, $pageID, $value) {
    $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
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
    return $stmt->execute();
}

function checkUserLike($userID, $pageID) {
    $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
    $likeID = $userID . '_' . $pageID;
    $stmt = $db->prepare("SELECT value FROM likes WHERE likeID = :lid");
    $stmt->bindValue(':lid', $likeID, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['value'] ?? 0;
}


// WHAT: Manage request.
$requestManage = isset($_GET['q']) ? str_split(preg_replace('/[^0-9]/', '', $_GET['q'])) : [];
if (empty($requestManage)) {
    http_response_code(400);
    header("Location: ../index.php?error=Invalid or missing action parameter.");
    exit;
}
foreach ($requestManage as $action) {
    switch ($action) {
        case 0:
            echo getLikes($type, $pageID);
            break;
        case 1:
            echo getLikers($pageID);
            break;
        case 2:
            if (updateLikes($type, $pageID, $value)) {
            } else {
                http_response_code(500);
                header("Location: index.php?error=Sorry, but you cannot rate the page at this time. (failed to update likes [111])");
                exit;
            }
            break;
        case 3:
            if (updateUserLikes($userID, $pageID, $value)) {
            } else {
                http_response_code(500);
                header("Location: index.php?error=Sorry, but you cannot rate the page at this time. (failed to update user likes [119])");
                exit;
            }
            break;
        case 4:
            if (checkUserLike($userID, $pageID)) {
            } else {
                http_response_code(500);
                header("Location: index.php?error=Sorry, but you cannot rate the page at this time. (failed to update user likes [119])");
                exit;
            }
            break;
        default:
            echo "Invalid action.";
            break;
    }
}
$returnTo = $_GET['return_to'] ?? '../index.php';
header("Location: $returnTo");
exit;