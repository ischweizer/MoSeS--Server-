<?php
session_start();
  
include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - Docs</title>

<?php  
include_once("./include/_menu.php");  
?>  
  
<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>
<!-- <div id="splash">&nbsp;</div> -->
<!-- end #header -->

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="content">
                    <div class="post">
                        <h2 class="title">Docs</h2>
                        <div class="entry">
                            <p>This is a place for some manuals, FAQs, etc...</p>
                        </div>
                        <div style="clear: both;">&nbsp;</div>
                        <p class="meta">Posted by Admin on February 12, 2012</p>
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
  include_once("./include/_login_slider.php");
 
  include_once("./include/_footer.php");  
?>
