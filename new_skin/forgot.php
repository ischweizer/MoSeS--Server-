<?php
  
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - Registration</title>

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
                        <h2 class="title">Have you forgot your data?</h2>
                        <div class="entry">
                            <form class="forgot_pass_form" action="./forgot.php" method="post" accept-charset="UTF-8">
                                <fieldset>
                                    <legend>Forgot your password? You will receive new one via E-mail.</legend>
                                    <label for="email" >Fill your E-mail here: </label>
                                    <input type="text" name="email" id="email" maxlength="50" />
                                    <input type="hidden" name="submitted" id="submitted" value="1" />
                                    <input type="submit" name="submit" value="Finish" />
                                </fieldset>
                            </form>
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
  include_once("./include/_footer.php");  
?>
