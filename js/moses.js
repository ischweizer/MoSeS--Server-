$(document).ready(function() {
    
    /* Scroll to TOP */
    $('#scrollToTop').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });    
    
    /* menu dropdown */
    $('.dropdown-toggle').dropdown();

    
    /* Login lightbox */    
    $('body').bind('keydown', function(e) {
        if(e.keyCode==27){
            // dim back when "escape" is pressed
            $("#dim_back").fadeOut();
                return false;
        }
    });
    
    /**
     * Lightbox Closing X
     * When Users clicks on X on lightbox, the lightbox disappears
     */
    $('#boxclose').click(function(){
    	$("#dim_back").fadeOut();
    	});
    
    //Adjust height of overlay to fill screen when page loads
    $("#dim_back").css("height", $(document).height());

    //When the link that triggers the message is clicked fade in overlay/msgbox
    $("#btn_login").click(function(){
        $("#dim_back").fadeIn();
        $('#login_error_message').hide(); // prevent all errors from showing on the lighbox
        $('input[type=text]').focus();
        
        // reads cookies and sets to fields
        var cookie = readCookie('moses_l');
        if(cookie != ''){
            $('input[type=text]').val(cookie);
            $('input[type=password]').focus();
            $('#rememberme').attr('checked', true);
        }else{
            $('input[type=text]').val('');   
            $('input[type=password]').val('');
            $('input[type=text]').focus();   
            $('#rememberme').attr('checked', false);
        } 

    });

    //When the message box is closed, fade out
    $("#lightbox :submit").click(function(e){
        
        
        /* AJAX Login request*/
        $.ajax({
        	type: "POST",
        	url: "content_provider.php",
        	data: $('#lightbox').serialize(), 
        	success: function(result){
        		switch(result)
	        		{
	        		case '0':
	        			//login is succesfull, refresh the page
	        			//and set the cookie if desired
	        			$('#login_error_message').hide();
	        			if($('#rememberme').is(':checked')){
	        	            setCookie('moses_l', $('input[type=text]').val(), 31);
	        	        }else{
	        	            // if unchecked, just delete cookie
	        	            var cookie = readCookie('moses_l');
	        	            if(cookie != ''){
	        	                delCookie('moses_l');
	        	            } 
	        	        }
	        			$("#dim_back").fadeOut();
	        			document.location.reload();
	        		  break;
	        		case '1':
	        			$('#login_error_message').text('You have not confirmed your E-Mail address!');
	        			$('#login_error_message').show();
	        		  break;
	        		case '2':
	        			$('#login_error_message').text('Wrong email or password!');
	        			$('#login_error_message').show();
	        			break;
	        		case '3':
	        			$('#login_error_message').text('Missing password!');
	        			$('#login_error_message').show();
	        			break;
	        		case '4':
	        			$('#login_error_message').text('Invalid email!');
	        			$('#login_error_message').show();
	        			break;
	        		default:
	        			$('#login_error_message').text('An unknown error has occured. Giovani Giorgo will be informed about this!');
	        			$('#login_error_message').show();
	        		}        		
        	}
        });
        
        /*
         * Prevents the page from refreshing when button is clicked or enter is pressed;
         * Before that, the login credentials have to be validated
         */
        e.preventDefault();
    });
    

    /* Sets cookies for N days */
    function setCookie(cookieName,cookieValue,nDays) {
     var today = new Date();
     var expire = new Date();
     if (nDays==null || nDays==0) nDays=1;
     expire.setTime(today.getTime() + 3600000*24*nDays);
     document.cookie = cookieName+"="+escape(cookieValue)
                     + ";expires="+expire.toGMTString();
    }
    
    /* Reads cookies */
    function readCookie(cookieName) {
     var theCookie=" "+document.cookie;
     var ind=theCookie.indexOf(" "+cookieName+"=");
     
     if (ind==-1) ind=theCookie.indexOf(";"+cookieName+"=");
     if (ind==-1 || cookieName=="") return "";
     var ind1=theCookie.indexOf(";",ind+1);
     if (ind1==-1) ind1=theCookie.length; 
     return unescape(theCookie.substring(ind+cookieName.length+2,ind1));
    }      
    
    /* Deletes a cookie */
    function delCookie(name) {
       document.cookie = name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
    }
    
    /************** Navigation Bar Logic ******************************************************************/
    $clickedFromSubmenu = false; // this variable is  set to true only when an item in the Navigation Bar is clicked
    $('.navbar li').click(function(e) {
        // emulate a flash when an item in the Navigation Bar is clicked
        if(!$clickedFromSubmenu){
        var $this = $(this);
        $this.removeClass('active');
    
        if (!$this.hasClass('active')) {
            $this.addClass('active');
            setTimeout(function(){$this.removeClass('active');},50);
        } 
        // go to the webpage
        document.location = $(this).find('a').attr('href'); 
        }
        $clickedFromSubmenu = false; // reset the variable, next click may be made directly on the item and not on the submenu
        return false;
    });
    
    // prevent items in Navigation Bar from flashing when a submenu is clicked
    $('.dropdown-menu a').click(function(e){
        // goto webpage from dropdown menu
        document.location = $(this).attr('href');   
        $clickedFromSubmenu = true; // the flash should not be performed
    }); 
    /************** Navigation Bar Logic END END END END*********************************************************/
    
    
    
    /***************** REGISTRATION FORM ************************************************************************/
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
			},
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


/**************** REGISTRATION FORM END END END END END END ****************************/
    
    // transform orange button to disabled gray with waiting text on click
    $('#allowAccessForm :submit').click(function(e){        
        
        var clickedButton = $(this);
        
        clickedButton.removeClass('btn-warning');
        clickedButton.attr('disabled', true);
        clickedButton.text('Working...');
        
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: { 'hash': clickedButton.val() }
        }).done(function(result) {
            if(result == '0'){
                clickedButton.addClass('btn-success');
                clickedButton.text('Success');
                clickedButton.html(clickedButton.html()+' <i class="icon-white icon-ok"></i>');
                
            }
        });
        
        e.preventDefault();
    });
    
    /* ************************** */
});

//Adjust height of dim overlay to fill screen when browser gets resized
$(window).bind("resize", function(){
    $("#dim_back").css("height", $(window).innerHeight);
});
