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
    
    // fadeout login lightbox on click outside
    /*jQuery("#menu").click(function(){ return false; });
    jQuery(document).one("click", function() { 
        jQuery("#menu").fadeOut(); 
    });
    
    $('#dim_back').click(function(){
        $("#dim_back").fadeOut();    
        return false;
    });*/
    
    //Adjust height of overlay to fill screen when page loads
    $("#dim_back").css("height", $(document).height());

    //When the link that triggers the message is clicked fade in overlay/msgbox
    $("#btn_login").click(function(){
        $("#dim_back").fadeIn();
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
        
        //return false;
    });

    //When the message box is closed, fade out
    $("#lightbox :submit").click(function(){
        if($('#rememberme').is(':checked')){
            setCookie('moses_l', $('input[type=text]').val(), 31);
            // ;"+$('input[type=password]').val()
        }else{
            // if unchecked, just delete cookie
            var cookie = readCookie('moses_l');
            if(cookie != ''){
                delCookie('moses_l');
            } 
        } 
        
        $("#dim_back").fadeOut();
        //return false;
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
     //alert(ind);
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
    
     
    
    /* ************************** */
});

//Adjust height of dim overlay to fill screen when browser gets resized
$(window).bind("resize", function(){
    $("#dim_back").css("height", $(window).innerHeight);
});
