<?php

$user = "root";    
$pass = "";    
$db = "";

try {
$db = new PDO("mysql:host=localhost;dbname=boinc4android", $user, $pass);


} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
    
?>