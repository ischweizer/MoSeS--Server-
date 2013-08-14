$(document).ready(function() {
    
    /* Scroll to TOP */
    $('.scrollToTop').click(function () {
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
        $('input[type=email]').focus();
        
        // reads cookies and sets to fields
        var cookie = readCookie('moses_l');
        if(cookie != ''){
            $('input[type=email]').val(cookie);
            $('input[type=password]').focus();
            $('#rememberme').attr('checked', true);
        }else{
            $('input[type=email]').val('');   
            $('input[type=password]').val('');
            $('input[type=email]').focus();   
            $('#rememberme').attr('checked', false);
        } 

    });

    //When the message box is closed, fade out
    $("#lightbox :submit").click(function(e){
        
        /*
         * Prevents the page from refreshing when button is clicked or enter is pressed
         */
        e.preventDefault();
        
    	var clickedButton = $(this);
        
        clickedButton.removeClass('btn-success');
        clickedButton.attr('disabled', true);
        clickedButton.text('Working...');
        
        /* AJAX Login request*/
        $.ajax({
        	type: "POST",
        	url: "content_provider.php",
        	data: $('#lightbox').serialize(), 
        	success: function(result){
        		if(result != '0'){
        			// reenable the button
                    clickedButton.addClass('btn-success');
                    clickedButton.attr('disabled', false);
                    clickedButton.text('Sign in');
        		}
        		switch(result)
	        		{
	        		case '0':
	        			//login is succesfull, refresh the page
	        			//and set the cookie if desired
	        			$('#login_error_message').hide();
	        			if($('#rememberme').is(':checked')){
	        	            setCookie('moses_l', $('input[type=email]').val(), 31);
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
	        		case '5':
	        			$('#login_error_message').text('Password has to be at least six characters long!');
	        			$('#login_error_message').show();
	        			break;
	        		default:
	        			$('#login_error_message').text('An unknown error has occured. Giovani Giorgo will be informed about this!');
	        			$('#login_error_message').show();
	        		}        		
        	}
        });
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
    
});

function scrollToElement(selector, time, verticalOffset) {
    time = typeof(time) != 'undefined' ? time : 1000;
    verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
    element = $(selector);
    offset = element.offset();
    offsetTop = offset.top + verticalOffset;
    $('html, body').animate({
        scrollTop: offsetTop
    }, time);
}

//Adjust height of dim overlay to fill screen when browser gets resized
$(window).bind("resize", function(){
    $("#dim_back").css("height", $(window).innerHeight);
});
