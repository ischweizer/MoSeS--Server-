<?php
// start the session
session_start();

if(isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");  

//Import of the header
include_once("./include/_header.php");
?>
  
<title>Hauptseite von MoSeS - Registration</title>

<?php  
   //Import of menu
  include_once("./include/_menu.php");  
?>  
  
<!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>Forgot credentials? Your are right here!</h2>
        <p>
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
        </p>
        <br />
    </div>
    <!-- / Main Block -->
    
    <hr>

<?php 
   //Import of slider to login
  include_once("./include/_login.php");
 //Import of footer
  include_once("./include/_footer.php");  
?>
