<?php

const CONNECTION_STRING = "sqlite:dashboard/imdb-php/resources/imdb.2.sqlite3";
const CONNECTION_USER = "";
const CONNECTION_PASSWORD = "";

const CONNECTION_OPTIONS = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
?>

