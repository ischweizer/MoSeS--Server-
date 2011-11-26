<?php
session_start();
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
?>

<title>Hauptseite von Boinc4Android - Logout</title>

<?php  
  include_once("./include/_menu.php");
   
?>  
  
<div class="heading_text">You will be logged out right now...</div>

<?php

$REFERRER = $_SERVER['HTTP_REFERER'];

echo $REFERRER;

session_destroy();

header("Location:".$REFERRER);

?>

<?php  
  include_once("./include/_bottom.php");  
?>