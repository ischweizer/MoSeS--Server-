<?php
// start the session
session_start();

if(isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");  

//Import of the header
include_once("./include/_header.php");
?>
  
<title>Hauptseite von MoSeS - forgetful</title>

<?php  
   //Import of menu
  include_once("./include/_menu.php");  
?>  
  
<!-- Shown to the user when he has to enter his email address -->
    <div class="hero-unit">
		<form class="form-horizontal" action="./forgot.php" method="post" accept-charset="UTF-8" id="forgotEmailForm">
			<fieldset id="forgot_fieldset">
				<legend>Forgot your credentials? Your are on the right place!</legend>
                <div class="control-group">
                	<label for="email" class="control-label">Ener your Email here</label>
                    <div class="controls">
                    	<input type="text" name="email" id="email" maxlength="50" />
                    </div>
				</div>
				<div class="clear"></div>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
                    	<input type="hidden" name="submitted" id="submitted" value="1" />
                    	<button type="submit" name="submit" class="btn btn-success">I am not a bot</button>
                    </div>
				</div>
			</fieldset>
		</form>
	</div>
        <br />
        
    <hr>

<?php 
   //Import of slider to login
  include_once("./include/_login.php");
 //Import of footer
  include_once("./include/_footer.php");  
?>
