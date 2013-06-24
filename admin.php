<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    if(!(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"))
        header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    
include_once("./include/functions/func.php");
include_once("./config.php");

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Devices</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit" style="font-family: 'Myriad Pro', 'Gill Sans', 'Gill Sans MT', Calibri, sans-serif;">
        <h2>Admin</h2>
       
    <hr>

 <?php
 
//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>