<?php

/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */

include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");
$logger->logInfo(" ###################### content_provider.php request for only checking the email AND registering ############################## ");

$USER_CREATED = 0;

// init
$FIRSTNAME = $_POST["firstname"];
$LASTNAME = $_POST["lastname"];
$EMAIL = $_POST["email"];
$PASSWORD = $_POST["password"];
$URL = $_POST["url"];
$CUR_TIME = time();
$CONFIRM_CODE = md5($EMAIL);
if(isEmailUnique($EMAIL, $CONFIG, $db, $logger)){
    
    // we have no duplicate emails
    // so we can insert new entry
    $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['USER'] ." (usergroupid, firstname, lastname, password,
        hash, email, ipaddress, lastactivity, joindate, passworddate)
        VALUES
        (0, '". $FIRSTNAME ."', '". $LASTNAME ."', '". $PASSWORD ."', '". $CONFIRM_CODE ."','". $EMAIL ."',
                '". $_SERVER["REMOTE_ADDR"] ."', ". $CUR_TIME .", ". $CUR_TIME .", ". $CUR_TIME .")";
    $db->exec($sql);
    $USER_CREATED = 1;
    
    // compose email to user
    $to = $EMAIL;
    $subject = "MoSeS: Please confirm your registration";
    $from = "developer@tk.informatik.tu-darmstadt.de";
    
    $message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
    $message .= "Thank you for your registration with MoSeS - Mobile Sensing System.\n";
    $message .= "Please follow this link to confirm your registration: \n";
    $message .= $URL ."?confirm=". $CONFIRM_CODE;
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
    die("1"); // the email was not unique
?>