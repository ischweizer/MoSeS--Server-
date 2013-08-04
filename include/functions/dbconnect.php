<?php
include_once("config.php");

$user = $CONFIG['DB']['USER'];    
$pass = $CONFIG['DB']['PASSWORD'];    
$db = "";

try {
$db = new PDO("mysql:host=".$CONFIG['DB']['HOST'].";dbname=". $CONFIG['DB']['DBNAME'], $user, $pass);

} catch (PDOException $e) {
    die("Error!: " . $e->getMessage() . "<br/>");
}
    
?>