<?php
if (!defined("CONNECTION_STRING")) {
    define("CONNECTION_STRING", "sqlite:imdb.db");
}

if (!defined("CONNECTION_USER")) {
    define("CONNECTION_USER", "");
}

if (!defined("CONNECTION_PASSWORD")) {
    define("CONNECTION_PASSWORD", "");
}

if (!defined("CONNECTION_OPTIONS")) {
    define("CONNECTION_OPTIONS", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
?>
