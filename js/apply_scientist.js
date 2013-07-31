$('.btnApplyScientistSend').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-success');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: $('.apply_scientist_form').serialize()
    }).done(function(result) {
        
        // success
        if(result == '1'){
            $('.hero-unit').html('<h2 class="text-center">Your scientist application was sent!</h2>');
        }else{
            // if no success, try again later
            // enable button again
            clickedButton.addClass('btn-success');
            clickedButton.text('Send');
            clickedButton.attr('disabled', false);
            alert('Something went wrong! Please, try again later.');   
        }
    });
});