<?php

/*
 * @author: Wladimir Schmidt
 */

//Starting the session
session_start();
//import of the header
include_once("./include/_header.php");
  
?>
  
<title>The Mobile Sensing System - About the project</title>

<?php  
// import of menu
include_once("./include/_menu.php");  
?>  
  
<!-- Main Block -->
    <div class="hero-unit">
        <h2>About</h2>
        <p>The Mobile Sensing System (MoSeS) offers researchers a platform for distribution of non-comercial Android apps, that are used for research purposes.</p>
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
<script type="text/javascript">
    // iterate through all menus and remove selection
    $('.dropdown').each(function(){
        $(this).removeClass('active');   
    });
    // add selection for this page
    $('.nav-menu6').addClass('active');
</script>