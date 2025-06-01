<?php session_start(); ?>
<?php 
$userID = $_SESSION['userID'] ?? '';
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
header("Refresh: 0; URL=../user/". $userID .".php");
?>
Oops, you've been misled, this isn't a real place!
</body>
</html>