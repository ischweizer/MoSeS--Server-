$(document).ready(function() {
    
    /*$(document).click(function() {
        $("#popup").fadeOut("fast");
    });*/
    
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
    $("#btn_login").click(function(){
        $("#popup").fadeIn("slow");
        
        // reads cookies and sets to fields
        var cookie = readCookie('moses_l');
        if(cookie != ''){
            $('#login').val(cookie);
            $('#rememberme').attr('checked', true);
        }else{
            $('#login').val('');   
            $('#password').val('');
            $('#login').focus();   
            $('#rememberme').attr('checked', false);
        }
    });
    $("#signin").click(function(){
        if($('#rememberme').is(':checked')){
            setCookie('moses_l', $('#login').val(), 31);
        }else{
            // if unchecked, just delete cookie
            var cookie = readCookie('moses_l');
            if(cookie != ''){
                delCookie('moses_l');
            } 
        }
        $("#popup").fadeOut("fast");
    });

    $('body').bind('keypress', function(e) {
        if(e.keyCode==27){
            $("#popup").fadeOut("fast");
        }
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
    
    /* ************************** */
});