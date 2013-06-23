<?php
//Starting the session
session_start();
//import of the header
include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - About</title>

<?php  
// import of menu
include_once("./include/_menu.php");  
?>  
  
    <!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>Impressum</h2>
        <p>Hier ist impressum, contacts etc</p>
        <br />
    </div>
    <!-- / Main Block -->
    
    <hr>

<?php 
   //import of the login window to authentificate
  include_once("./include/_login.php");
 
  //import of the footer to affich the year of project
  include_once("./include/_footer.php");  
?>