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
        <h2>MoSeS Developers</h2>
        <p>Project MoSeS is developed at <a href="http://www.tk.informatik.tu-darmstadt.de/" title="Telecooperation Group" target="_blank">Telecooperation Group</a> by <a href="https://github.com/maksuti" target="_blank">Zijad Maksuti</a>, <a href="https://github.com/wlsch" target="_blank">Wladimir Schmidt</a>, 
                            <a href="https://github.com/simlei" target="_blank">Simon Leisching</a>, <a href="https://github.com/jahofmann" target="_blank">Jaco Hofmann</a>, <a href="https://github.com/scalaina" target="_blank">Sandra Christina Amend</a>, <a href="https://github.com/alyahya" target="_blank">Ibrahim Alyahya</a>, 
                            <a href="https://github.com/fahouma" target="_blank">Fehmi Belhadj</a> and <a href="https://github.com/FSchnell" target="_blank">Florian Schnell</a> under supervision of <a href="http://www.tk.informatik.tu-darmstadt.de/?id=1699" title="Immanuel Schweizer" target="_blank">Immanuel Schweizer</a>.</p>
    </div>
    <!-- / Main Block -->
    
    <hr>

<?php 
   //import of the login window to authentificate
  include_once("./include/_login.php");
 
  //import of the footer to affich the year of project
  include_once("./include/_footer.php");  
?>