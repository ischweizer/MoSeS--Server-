<?php

$user = "usn";    
$pass = ":R9#k;}2?Lg1";    
$db = "";

try {
$db = new PDO("mysql:host=localhost;dbname=moses", $user, $pass);


} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
    
?>