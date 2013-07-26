<?php
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");

// If the email is unique, 0 is returned
// if the email is already contained in the database (someone used it already) 1 is returned
$logger->logInfo(" ###################### content_provider.php request for only checking the email ############################## ");
if(isEmailUnique($_POST["isEmailUnique"], $CONFIG, $db, $logger)){
    die("0"); // no users with such email found, the email is thus unique
}
else
    die("1"); // a user has already confirmed this email, the email is thus NOT unique
?>