<?php
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");
$logger->logInfo(" ###################### content_provider.php request for checking the email AND sending password forgot email ############################## ");

// init
$FIRSTNAME;
$LASTNAME;
$EMAIL = $_POST["email_for"];
$CONFIRM_CODE = md5($EMAIL);
if(!isEmailUnique($EMAIL, $CONFIG, $db, $logger)){

    // the server knows the email, get the first and last name
    $sql = "SELECT firstname, lastname FROM ". $CONFIG['DB_TABLE']['USER'] ." WHERE email='".$EMAIL."'";
    $result = $db->query($sql);
    $fsname = $result->fetch();
    $FIRSTNAME = $fsname['firstname'];
    $LASTNAME = $fsname['lastname'];

    // compose email to user
    $to = $EMAIL;
    $subject = "MoSeS: Password reset";
    $from = "admin@moses.tk.informatik.tu-darmstadt.de";
    $message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
    $message .= "Please follow this link to enter a new password: ";
    $message .= "http://". $_SERVER["SERVER_NAME"] . "/moses/forgot.php" ."?newpassword=". $CONFIRM_CODE;

    $headers = "From: $from";
    $sent = mail($to, $subject, $message, $headers);

    // sending was successful?
    if(!$sent) { // there was a problem sending email
        die("2");
    }
    else
        die("0");
}
else
    die("1"); // the email was not found in the database
?>