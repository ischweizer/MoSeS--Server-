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
                    	<button type="submit" name="submit" class="btn btn-success" id="button_forgot">I am not a bot</button>
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
				}
		},
		
		messages:{
			email_for:{
				required:"Enter your email address",
				email:"Enter a valid email address"
			}
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
	            		clickedButton.text("I am not a bot");
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
    		clickedButton.text("I am not a bot");
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
	
    </script>
