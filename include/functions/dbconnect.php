<?php

$user = "moses";    
$pass = "mosespassworddasense";    
$db = "";

try {
$db = new PDO("mysql:host=212.72.183.108;dbname=moses", $user, $pass);


} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
    
?>