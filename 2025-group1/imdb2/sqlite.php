<?php
/*

   _____  ____  _      _____ _______ ______   _____  _    _ _____  
  / ____|/ __ \| |    |_   _|__   __|  ____| |  __ \| |  | |  __ \ 
 | (___ | |  | | |      | |    | |  | |__    | |__) | |__| | |__) |
  \___ \| |  | | |      | |    | |  |  __|   |  ___/|  __  |  ___/ 
  ____) | |__| | |____ _| |_   | |  | |____ _| |    | |  | | |     
 |_____/ \___\_\______|_____|  |_|  |______(_)_|    |_|  |_|_|     
                                                                   
                                                                   
 This file lets you connect to the SQLite database and perform a simple query.
 Basically because sqlite3 CLI terminal commands are not installed on GoStudent VMs.

*/
// SQLite database connection
try {
    $db = new PDO('sqlite:resources/imdb2-user.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

try {
    $stmt = $db->prepare("SELECT * from sqlite_master WHERE type='table' AND name='likes'");
    $userID = 'cj'; // Replace with actual userID and pageID
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        foreach ($result as $row) {
            echo implode(", ", $row) . "<br>";
        }
    } else {
        echo "No data found in 'likes' table.\n";
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}

?>