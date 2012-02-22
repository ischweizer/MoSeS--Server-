<?php
session_start();
  
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - Download</title>

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
                        <h2 class="title">App download</h2>
                        <div class="entry">
                            <p><img src="images/moses_logo2.png" width="" alt="logo" class="alignleft border" />You can download our app <a href="#">here</a></p>
                        </div>
                        <div style="clear: both;">&nbsp;</div>
                        <p class="meta">Posted by Admin on February 15, 2012</p>
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