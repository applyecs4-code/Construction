<?php
session_start();

// सभी session variables हटाओ
$_SESSION = [];

// अगर cookie में session id है तो उसे भी expire कर दो
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// अब session पूरी तरह destroy करो
session_destroy();

// Login page पर redirect
header("Location: login.php");
exit;
?>
