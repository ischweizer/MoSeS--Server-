/*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/
/*
 * @author: Zijad Maksuti
 * @author: Wladimir Schmidt
 */

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
