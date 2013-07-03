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

   //Import of slider to login
  include_once("./include/_login.php");
 //Import of footer
  include_once("./include/_footer.php");  
?>

<script type="text/javascript">
    
    /**
     * Validate the email entered by the user.
     * It has to look like an email.
     */
     
    $("#forgotEmailForm").validate({
		rules:{
			email_for:{
					required:true,
					email: true
				},
				password_reset:{
					required:true,
					minlength: 6
				},
				password_reset_repeat:{
					required:true,
					equalTo: "#password_reset"
				},
		},
		
		messages:{
			email_for:{
				required:"Enter your email address",
				email:"Enter a valid email address"
			},
			password_reset:{
				required:"Enter your new password",
				minlength:"Password must be minimum 6 characters"
			},
			password_reset_repeat:{
				required:"Confirm your new password",
				equalTo:"Password and Confirm Password must match"
			},
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('success');
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});

    /**
     * Sending email when the button is clicked
     */
	$("#forgotEmailForm :submit").click(function(e){
		
		var clickedButton = $("#button_forgot");
        clickedButton.removeClass('btn-success');
        clickedButton.attr('disabled', true);
        clickedButton.text('Working...');
		
		if($("#forgotEmailForm").valid()){
			/*
			 * Check the uniqueness of the email only if previous validation found no errors
			 */
			var emailAddress = $('#email_for').val();
			$.ajax({
	            type: "POST",
	            url: "content_provider.php",
	            data: $('#forgotEmailForm').serialize(),
	            success: function(result){
	            	switch(result){
	            	case '0':
	            		// the email is unique, the email has been sent
	            		// inform the user
	            		var fieldset = document.getElementById("forgot_fieldset");
	            		// remove all children except legend
	            		while(fieldset.lastChild && fieldset.lastChild.tagName != "LEGEND")
	            			fieldset.removeChild(fieldset.lastChild);
	            		// add the new child containing the message
	            		var message = document.createElement("h4");
	            		message.innerHTML="We have sent you an email for reseting your password to <i>" + emailAddress +"</i>";
	            		fieldset.appendChild(message);
	            		break;
	        		case '1':
            			// the email is not unique
            			$("#email_for").parents(".control-group").removeClass('success');
                		$("#email_for").parents(".control-group").addClass('error');
                		// add the span
                		var errorSpan = document.createElement("span");
                		errorSpan.setAttribute("id", "tempErrorSpan");
                		errorSpan.setAttribute("for", "email");
                		errorSpan.setAttribute("generated", "true");
                		errorSpan.setAttribute("class", "help-inline");
                		errorSpan.setAttribute("style", "display: inline-block;");
                		errorSpan.innerHTML="Old MoSeS does not know this email.";
                		var emailInput = document.getElementById("email_for");
                		var oldChild = document.getElementById("tempErrorSpan");
                		if(oldChild != null)
                			emailInput.parentNode.replaceChild(errorSpan, oldChild);
                		else
                			emailInput.parentNode.appendChild(errorSpan);
                		
                		// reenable the button
                		clickedButton.addClass('btn-success');
	            		clickedButton.attr('disabled', false);
	            		clickedButton.text("Yes send me an email");
	            		break;
	        		case '2':
	        			// problem sending the email
	        			var fieldset = document.getElementById("forgot_fieldset");
	            		// remove all children except legend
	            		while(fieldset.lastChild && fieldset.lastChild.tagName != "LEGEND")
	            			fieldset.removeChild(fieldset.lastChild);
	            		// add the new child containing the message
	            		var message = document.createElement("h4");
	            		message.innerHTML="There was a problem sending an email for reseting of your password. We are sorry :(";
	            		fieldset.appendChild(message);
	        			break;
	        		default:
	        			// unknown error
	        			var fieldset = document.getElementById("forgot_fieldset");
	            		// remove all children except legend
	            		while(fieldset.lastChild && fieldset.lastChild.tagName != "LEGEND")
	            			fieldset.removeChild(fieldset.lastChild);
	            		// add the new child containing the message
	            		var message = document.createElement("h4");
	            		message.innerHTML="An unknown error has occured. We are sorry :(";
	            		}     
	            	}
	            });
			}
		else{
			// the validation has failed, reenable the button
    		clickedButton.addClass('btn-success');
    		clickedButton.attr('disabled', false);
    		clickedButton.text("Yes send me an email");
		}
		e.preventDefault();
	});

	/**
     * make the custom error message for an unknown email disappear
     * after the user starts typing again
     */
    $('#email_for').keyup(function(){
    	var emailInput = document.getElementById("email_for");
    	var errorSpan = document.getElementById("tempErrorSpan");
    	if(errorSpan != null)
    		emailInput.parentNode.removeChild(errorSpan);
    });


    /**
     * Changing email password
     */
	$("#forgotEmailForm :reset").click(function(e){
		
		var clickedButton = $("#button_forgot");
        clickedButton.removeClass('btn-success');
        clickedButton.attr('disabled', true);
        clickedButton.text('Working...');
		
		if($("#forgotEmailForm").valid()){
			/*
			 * Update the new password, if previous validation returned no errors
			 */
			var hash = $("#reset_password").val(); // get the hash
			var newPassword = $('#password_reset').val();
			$.ajax({
	            type: "POST",
	            url: "content_provider.php",
	            data: {"hash":hash, "newPassword":newPassword},
	            success: function(result){
	            	var fieldset = document.getElementById("forgot_fieldset");
            		// remove all children except legend
            		while(fieldset.lastChild && fieldset.lastChild.tagName != "LEGEND")
            			fieldset.removeChild(fieldset.lastChild);
            		// add the new child containing the message
            		var message = document.createElement("h4");
            		
	            	switch(result){
	            	case '0':
	            		// the password has been updated
	            		message.innerHTML="MoSeS acknowledged your new password. You can now log in.";
	            		break;
	        		default:
	        			// unknown error
	            		// add the new child containing the message
	            		message.innerHTML="An unknown error has occured. We are sorry :(";
	            		}
	            	fieldset.appendChild(message);     
	            	}
	            });
			}
		else{
			// the validation has failed, reenable the button
    		clickedButton.addClass('btn-success');
    		clickedButton.attr('disabled', false);
    		clickedButton.text("Change password");
		}
		e.preventDefault();
	});
    
	
    </script>
