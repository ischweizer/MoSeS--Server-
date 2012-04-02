<?php
session_start();
  
include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - About</title>

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
                        <h2 class="title">About MoSeS</h2>
                        <div class="entry">
                            <p>MoSeS offers researchers a platform for distribution of non-comercial Android apps, that are used for research purposes.</p>
                            <br />
                            <p>Project MoSeS is developed at <a href="http://www.tk.informatik.tu-darmstadt.de/de/telecooperation-group/" title="Telecooperation Group" target="_blank">Telecooperation Group</a> by <a href="https://github.com/maksuti" target="_blank">Zijad Maksuti</a>, <a href="https://github.com/wlsch" target="_blank">Wladimir Schmidt</a>, 
                            <a href="https://github.com/simlei" target="_blank">Simon Leisching</a> and <a href="https://github.com/jahofmann" target="_blank">Jaco Hofmann</a> under supervision of <a href="http://www.tk.informatik.tu-darmstadt.de/?id=1699" title="Immanuel Schweizer" target="_blank">Immanuel Schweizer</a>.</p>
                        </div>
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