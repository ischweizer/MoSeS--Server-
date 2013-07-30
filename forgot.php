<?php
// start the session
session_start();

if(isset($_SESSION['USER_LOGGED_IN'])){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");  
    exit;
}

//Import of the header
include_once("./include/_header.php");
?>
  
<title>Hauptseite von MoSeS - forgetful</title>

<?php  
   //Import of menu
  include_once("./include/_menu.php");
  $PASSWORD_RESET = false;
  //Check if somebody is trying to reset his password
  if(isset($_GET["newpassword"]) && strlen(trim($_GET["newpassword"])) == 32){
  	//Import of configurations file
  	include_once("./config.php");
  	//Import of connections file to database
  	include_once("./include/functions/dbconnect.php");
  	 
  	$sql = "SELECT userid, confirmed
           FROM ". $CONFIG['DB_TABLE']['USER'] ."
           WHERE hash = '". $_GET["newpassword"] ."'";
  	 
  	$result = $db->query($sql);
  	$row = $result->fetch();
  	 
  	// allow password change only if user is confirmed (prevents missuse)
  	if(!empty($row) && $row['confirmed'] != 0){
  		 
//   		$sql = "UPDATE ". $CONFIG['DB_TABLE']['USER'] ."
//               SET confirmed=1, usergroupid=1
//               WHERE userid=". $row["userid"];
  
//   		$db->exec($sql);
  
  		$PASSWORD_RESET = true;
  		 
  	}
  }
?>  

    <div class="hero-unit">
		<form class="form-horizontal" action="./forgot.php" method="post" accept-charset="UTF-8" id="forgotEmailForm">
			<fieldset id="forgot_fieldset">
<?php
			  if(!$PASSWORD_RESET){
?>
			<!-- Shown to the user when he has to enter his email address -->
							<legend>Forgot your credentials? Your are on the right place!</legend>
			                <div class="control-group">
			                	<label for="email_for" class="control-label">Enter your Email here</label>
			                    <div class="controls">
			                    	<input type="email" name="email_for" id="email_for" maxlength="50" />
			                    </div>
							</div>
							<div class="clear"></div>
							<div class="control-group">
								<label class="control-label"></label>
								<div class="controls">
			                    	<input type="hidden" name="submitted_forgot" id="submitted_forgot" value="1" />
			                    	<button type="submit" name="submit" class="btn btn-success" id="button_forgot">Yes send me an email</button>
			                    </div>
							</div>

<?php 
}
else{
?>

<!-- Shown to the user when he has to reset the password (he gets here by following a link from an email) -->
				<legend>Enter a new password</legend>
                <div class="control-group">
                	<label for="password_reset" class="control-label">Your new password</label>
                    <div class="controls">
                    	<input type="password" name="password_reset" id="password_reset" maxlength="50" />
                    </div>
				</div>
				<div class="control-group">
                    	<label for="password_reset_repeat" class="control-label">Confirm the password</label>
                   			<div class="controls">
                    			<input type="password" name="password_reset_repeat" id="password_reset_repeat" maxlength="50"/>
                    		</div>
                    </div>
				<div class="clear"></div>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
                    	<input type="hidden" name="reset_password" id="reset_password" value="<?php echo $_GET["newpassword"]; ?>" />
                    	<button type="reset" name="submit" class="btn btn-success" id="button_forgot">Change password</button>
                    </div>
				</div>
<?php
}
?>
		</fieldset>
		</form>
		</div>
		<br />
		<hr>
<?php
  include_once("./include/_login.php");
 //Import of footer
  include_once("./include/_footer.php");  
?>
<script src="js/forgot.js"></script>