<?php
// start the session
session_start();
// get the address of this site
$REFERRER = $_SERVER['HTTP_REFERER'];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();  //getting parametres of cookies
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// initilize a new session
$_SESSION = array();
// destroy the old session
session_destroy();

header("Location:".$REFERRER);

?>