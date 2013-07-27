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