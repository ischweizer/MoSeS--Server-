// allow scientist
$('.btnAllowAccess').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-warning');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: { 
                'hash': clickedButton.val(),
                'allow': 4343
              }
    }).done(function(result) {
        if(result == '0'){
            clickedButton.addClass('btn-success');
            clickedButton.text('Approved');
            clickedButton.html(clickedButton.html()+' <i class="icon-white icon-ok"></i>');
            
        }
    });
});

// reject scientist
$('.btnRejectAccess').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-danger');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: { 
                'hash': clickedButton.val(),
                'reject': 3434
              }
    }).done(function(result) {
        if(result == '0'){
            clickedButton.text('Rejected');
            clickedButton.html(clickedButton.html()+' <i class="icon-ok"></i>');
        }
    });
});

// iterate through all menus and remove selection
$('.dropdown').each(function(){
    $(this).removeClass('active');   
});
// add selection for this page
$('.nav-menu7').addClass('active');