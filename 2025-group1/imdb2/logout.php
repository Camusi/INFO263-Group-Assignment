<?php
session_start();

// Unset variables
$_SESSION = [];

// Destroy the cookie
if (ini_get("session.use_cookies")) {
    setcookie(
        session_name(),
        '',
        time() - 42000,
        ini_get("session.cookie_path"),
        ini_get("session.cookie_domain"),
        ini_get("session.cookie_secure"),
        ini_get("session.cookie_httponly")
    );
}

// Destroy the session
session_destroy();
// Redirect to home
header("Location: index.php");
exit;
?>