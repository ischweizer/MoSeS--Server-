<?php
//Starting a new session
session_start();
  //Import of the header
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - Download</title>

<?php  
  //Import of menu
  include_once("./include/_menu.php");  
?>  
 
<!--<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>--> 

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="content">
                    <div class="post">
                        <h2 class="title">MoSeS for Android</h2>
                        <div class="entry">
                            <p><img src="img/android_with_moses.png" alt="logo" class="alignleft" />Download MoSeS-client <a href="./setup/MoSeS.apk">here.</a> <br/>
                            It works on all Android devices with API-Version 8 and above.</p>
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
//Import a slider
  include_once("./include/_login_slider.php");
//Import a footer 
  include_once("./include/_footer.php");  
?>