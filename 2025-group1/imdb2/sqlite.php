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
    $db = new PDO('sqlite:resources/imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

try {
    #$stmt = $db->query("SELECT job, nconst FROM title_principals_trim WHERE tconst = 'tt0072567'");
    $stmt = $db->query("PRAGMA table_info(title_director_trim)");
    #$stmt = $db->query("SELECT name from sqlite_master WHERE type='table';");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        #echo "<br>" . $row['nconst'] . " " . $row['job'] . "\n";
        echo "<br>" . $row['name'] . " (" . $row['type'] . ")\n";
        #echo "<br>" . $row['name'] . "\n";
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}

?>