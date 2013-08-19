
$('.btnInstantScientist').click(function(e){
    e.preventDefault();
    
    $.ajax({
        type: "POST",
        url: 'content_provider.php',
        data: {'instantScientist': 7779},
        success: function(result){
            if(result == "1"){
                location.reload();
            }
        }
       });
});

$('.btnLeaveGroup').click(function(e){
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-danger');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: 'content_provider.php',
        data: {'leaveGroup': $('.btnLeaveGroup').val()},
        success: function(result){
            if(result){
                $('.hero-unit').html('<h3 class="text-center">You left the group '+ result +'!</h3>');
            }
        },
        error: function(){
            clickedButton.addClass('btn-danger');
            clickedButton.attr('disabled', false);
            clickedButton.text('Leave group');
        }
       });
});

$('.btnCreateJoinGroup').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-success');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    // create group ajax
    if($('#optionsRadios1').is(':checked')){
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: { 'createGroup': clickedButton.val(),
                    'group_name': $('#group_name').val(),
                    'group_password': $('#group_password').val()}
        }).done(function(result) {
            handleGroupServerAnswer(result);
        });
    }
    
    // join group ajax
    if($('#optionsRadios2').is(':checked')){
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: { 'joinGroup': clickedButton.val(),
                    'group_name': $('#group_name').val(),
                    'group_password': $('#group_password').val()}
        }).done(function(result) {
           handleGroupServerAnswer(result);
        });
    }    
});

function handleGroupServerAnswer(result){
    switch(result){
        case '1':   location.href = location.protocol + '//' + location.host + location.pathname;
                    //$('.hero-unit').html('<h2 class="text-center">You successfully joined a group!</h2>'); 
                    break;
        case '2':   $('.hero-unit').html('<h2 class="text-center">Error: That name already exists!</h2>'); 
                    break;
        case '3':   location.href = location.protocol + '//' + location.host + location.pathname;
                    //$('.hero-unit').html('<h2 class="text-center">You successfully created a group!</h2>');
                    break;
        case '4':   $('.hero-unit').html('<h2 class="text-center">Error: Entered group doesn\'t exist!</h2>');
                    break;
        default:    alert('Something went wrong! Try again later.');
                    $('.btnCreateJoinGroup').addClass('btn-success');
                    $('.btnCreateJoinGroup').attr('disabled', false);
                    $('.btnCreateJoinGroup').text('GO');
                    break;
    }
}

// iterate through all menus and remove selection
$('.dropdown').each(function(){
    $(this).removeClass('active');   
});
// add selection for this page
$('.nav-menu3').addClass('active');
