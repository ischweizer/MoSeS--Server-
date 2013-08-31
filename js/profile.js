
/*
 * @author: Wladimir Schmidt
 */

// Send request to server to save user's profile
$('.btnSaveProfile').click(function(e){        
    
    e.preventDefault();
    
    var password1 = $('[name="password1"]').val();
    var password2 = $('[name="password2"]').val();
    
    if(password1 != password2){
        alert("Please, enter same password twice to proceed.");
        return;
    }
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-success');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    // send
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: $('.saveProfileForm').serialize(),
    }).done(function(result) {
        if(result == '0'){
            clickedButton.addClass('btn-success');
            clickedButton.text('Saved!');
            setTimeout(function(){
                clickedButton.attr('disabled', false);
                clickedButton.text('Save Profile');
            },2500);
        }else{
            clickedButton.addClass('btn-success');
            clickedButton.attr('disabled', false);
            clickedButton.text('Save Profile');
            alert("Error while updating your profile: check your internet connection.");
        }
    });
});