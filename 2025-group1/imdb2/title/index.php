<?php session_start();
$userID = $_SESSION['userID'] ?? '';
$pageID = $id ?? ''; // $id should be the tconst of the title

$userLikeStatus = 0; // Default: not liked/disliked

if ($userID && $pageID) {
    try {
        $db = new PDO('sqlite:../resources/imdb2-user.sqlite3');
        $stmt = $db->prepare("SELECT value FROM likes WHERE userID = :userID AND pageID = :pageID");
        $stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
        $stmt->bindValue(':pageID', $pageID, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $userLikeStatus = (int)$result['value']; // 1=like, -1=dislike, 0=none
        }
    } catch (Exception $e) {
        $userLikeStatus = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Page</title>
    <link rel="stylesheet" href="../resources/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<?php
require '../resources/navbar.php';
header("Refresh: 15; URL=../");
require('../resources/likes.php')?>
Oops, you've been misled, this isn't a real place!
</body>
</html>