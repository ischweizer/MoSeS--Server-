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
 * Registration Form
 * Validation: Highlighting of empty fields or fields that are not filled correctly
 */
$("#registerHere").validate({
    rules:{
        firstname:"required",
        lastname:"required",
        email:{
                required:true,
                email: true
            },
        password:{
            required:true,
            minlength: 6
        },
        password_repeat:{
            required:true,
            equalTo: "#password"
        }
    },
    
    messages:{
        firstname:"Enter your firstname",
        lastname:"Enter your lastname",
        email:{
            required:"Enter your email address",
            email:"Enter valid email address"
        },
        password:{
            required:"Enter your password",
            minlength:"Password must be minimum 6 characters"
        },
        password_repeat:{
            required:"Confirm password",
            equalTo:"Password and Confirm Password must match"
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
 * Validating the uniqueness of the email.
 * Done separately, because validation framework above is buggy
 * when using ajax
 */
$("#email").blur(function(){
    if($(this).valid()){
        /*
         * Check the uniqueness of the email only if previous validation found no errors
         */
        var enteredText = $(this).val();
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: {"isEmailUnique":enteredText},
            success: function(result){
                if(result == '0'){
                    // nothing to do here
                    }
                else{
                    if(result == '1'){
                        $("#email").parents(".control-group").removeClass('success');
                        $("#email").parents(".control-group").addClass('error');
                        // add the span
                        var errorSpan = document.createElement("span");
                        errorSpan.setAttribute("id", "tempErrorSpan");
                        errorSpan.setAttribute("for", "email");
                        errorSpan.setAttribute("generated", "true");
                        errorSpan.setAttribute("class", "help-inline");
                        errorSpan.setAttribute("style", "display: inline-block;");
                        errorSpan.innerHTML="Email is already in use, please choose another one";
                        var emailInput = document.getElementById("email");
                        var oldChild = document.getElementById("tempErrorSpan");
                        if(oldChild != null)
                            emailInput.parentNode.replaceChild(errorSpan, oldChild);
                        else
                            emailInput.parentNode.appendChild(errorSpan);
                        }
                    }
                }
            });
    }
});

/**
 * make the custom error message for email uniqueness disappear
 * after the user starts typing again
 */
$('#email').keyup(function(){
    var emailInput = document.getElementById("email");
    var errorSpan = document.getElementById("tempErrorSpan");
    if(errorSpan != null)
        emailInput.parentNode.removeChild(errorSpan);
});

/**
 * Registering when Create account button is clicked
 */
$("#registerHere :submit").click(function(e){
    
    var clickedButton = $(this);
    clickedButton.removeClass('btn-success');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    if($("#registerHere").valid()){
        /*
         * Check the uniqueness of the email only if previous validation found no errors
         */
        var emailAddress = $('#email').val();
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: $('#registerHere').serialize(),
            success: function(result){
                switch(result){
                case '0':
                    // the email is unique, the email has been sent
                    // inform the user
                    var fieldset = document.getElementById("registration_fieldset");
                    // remove all children except legend
                    while(fieldset.lastChild && fieldset.lastChild.tagName != "LEGEND")
                        fieldset.removeChild(fieldset.lastChild);
                    // add the new child containing the message
                    var message = document.createElement("h4");
                    message.innerHTML="We have sent an email for confirmation to <i>" + emailAddress +"</i>";
                    fieldset.appendChild(message);
                    break;
                case '1':
                    // the email is not unique
                    $("#email").parents(".control-group").removeClass('success');
                    $("#email").parents(".control-group").addClass('error');
                    // add the span
                    var errorSpan = document.createElement("span");
                    errorSpan.setAttribute("id", "tempErrorSpan");
                    errorSpan.setAttribute("for", "email");
                    errorSpan.setAttribute("generated", "true");
                    errorSpan.setAttribute("class", "help-inline");
                    errorSpan.setAttribute("style", "display: inline-block;");
                    errorSpan.innerHTML="Email is already in use, please choose another one";
                    var emailInput = document.getElementById("email");
                    var oldChild = document.getElementById("tempErrorSpan");
                    if(oldChild != null)
                        emailInput.parentNode.replaceChild(errorSpan, oldChild);
                    else
                        emailInput.parentNode.appendChild(errorSpan);
                    
                    // reenable the button
                    clickedButton.addClass('btn-success');
                    clickedButton.attr('disabled', false);
                    clickedButton.text("Create account");
                    break;
                case '2':
                    // problem sending the email
                    var fieldset = document.getElementById("registration_fieldset");
                    // remove all children except legend
                    while(fieldset.lastChild && fieldset.lastChild.tagName != "LEGEND")
                        fieldset.removeChild(fieldset.lastChild);
                    // add the new child containing the message
                    var message = document.createElement("h4");
                    message.innerHTML="There was a problem sending an email for confirmation. We are sorry :(";
                    fieldset.appendChild(message);
                    break;
                default:
                    // unknown error
                    var fieldset = document.getElementById("registration_fieldset");
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
        clickedButton.text("Create account");
    }
    e.preventDefault();
});
