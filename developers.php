<?php

/*
 * @author: Wladimir Schmidt
 */

//Starting the session
session_start();
//import of the header
include_once("./include/_header.php");
  
?>
  
<title>The Mobile Sensing System - Developers information</title>

<?php  
// import of menu
include_once("./include/_menu.php");  
?>  
  
<!-- Main Block -->
    <div class="hero-unit">
        <h2>Developers</h2>
        <p>Mobile Sensing System (MoSeS) was founded and originally developed in Telecooperation Lab of TU Darmstadt in 2011 by:</p>
        <p>
            <ul style="list-style-type: none;">
                <li><a href="https://github.com/wlsch" target="_blank">Wladimir Schmidt</a></li> 
                <li><a href="https://github.com/maksuti" target="_blank">Zijad Maksuti</a></li> 
                <li><a href="https://github.com/jahofmann" target="_blank">Jaco Hofmann</a></li>
                <li><a href="https://github.com/simlei" target="_blank">Simon Leisching</a></li>
            </ul>
        </p>

        <p>The further development of the MoSeS platform was accomplished in 2012 by:</p>

        <p>
            <ul style="list-style-type: none;">
                <li><a href="https://github.com/scalaina" target="_blank">Sandra Christina Amend</a></li>
                <li><a href="https://github.com/alyahya" target="_blank">Ibrahim Alyahya</a></li>
                <li><a href="https://github.com/fahouma" target="_blank">Fehmi Belhadj</a></li>
                <li><a href="https://github.com/FSchnell" target="_blank">Florian Schnell</a></li>
            </ul>
        </p>

        <p>In the summer of 2013 <a href="https://github.com/wlsch" target="_blank">Wladimir Schmidt</a> and <a href="https://github.com/maksuti" target="_blank">Zijad Maksuti</a> started working on improving of MoSeS platform.</p>

        <p>The MoSeS project started and being developed under supervision of Smart Urban Networks's area head <a href="http://www.tk.informatik.tu-darmstadt.de/?id=1699" title="Immanuel Schweizer" target="_blank">Dr. Immanuel Schweizer</a>.</p>

        <p>More info about Telecooperation Lab on the website: <a href="http://www.tk.informatik.tu-darmstadt.de/" target="_blank">http://www.tk.informatik.tu-darmstadt.de</a></p>
    </div>
    <!-- / Main Block -->
    
    <hr>

<?php 
  include_once("./include/_login.php");
  include_once("./include/_footer.php");  
?>
<script type="text/javascript">
    // iterate through all menus and remove selection
    $('.dropdown').each(function(){
        $(this).removeClass('active');   
    });
    // add selection for this page
    $('.nav-menu6').addClass('active');
</script>