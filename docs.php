<?php
//Starting session
session_start();
//Import of header 
include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - Docs</title>

<?php  
//Import of the header
include_once("./include/_menu.php");  
?>  
  
 

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="content">
                    <div class="post">
                        <h2 class="title">How to contribute?</h2>
                        <div class="entry">
                            <p>1. <a href="./registration.php">Register</a>.</p>
                            <p>2. <a href="./download.php">Download</a> and install the client.</p>
                            <p>3. That's it!</p>
                        </div>
                        <div style="clear: both;">&nbsp;</div>
                        <p class="meta">Posted on September 2012</p>
                    </div>
                    <div style="clear: both;">&nbsp;</div>
                </div>
                <!-- end #content -->
                <div style="clear: both;">&nbsp;</div>
            </div>
        </div>
    </div>
    <!-- end #page -->
</div>

<?php 
//Import of the slider
  include_once("./include/_login_slider.php");
 //Import of the footer
  include_once("./include/_footer.php");  
?>
