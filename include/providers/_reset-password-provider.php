<?php

/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */

include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");
$logger->logInfo(" ###################### content_provider.php request for checking the email AND sending password forgot email ############################## ");

// init
$FIRSTNAME;
$LASTNAME;
$EMAIL = $_POST["email_for"];
$CONFIRM_CODE = md5($EMAIL);
$URL = $_POST["url"];

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
    $from = "developer@tk.informatik.tu-darmstadt.de";
    $message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
    $message .= "Please follow this link to reset your password: \n";
    $message .= $URL ."?newpassword=". $CONFIRM_CODE;
    $message .= "\n\n - MoSeS Team";
    
    $headers  = "From: $from\r\n";
    $headers .= "Reply-To: $mailFrom\r\n";
    $headers .= "X-Sender-IP: {$_SERVER['REMOTE_ADDR']}\r\n";
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