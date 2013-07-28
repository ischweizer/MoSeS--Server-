var surveyQuestionNumber = 1;   

$('[name="study_period"]').click(function(){
    if($(this).is(':checked')){
        if($(this).val() == 1){
            $('[name="start_date"]').attr('disabled', false);    
            $('[name="end_date"]').attr('disabled', false);
            
            $('[name="start_after_n_devices"]').attr('disabled', true);    
            $('[name="running_time"]').attr('disabled', true);
            $('[name="running_time_value"]').attr('disabled', true);
        }
        
        if($(this).val() == 2){
            $('[name="start_date"]').attr('disabled', true);    
            $('[name="end_date"]').attr('disabled', true);
            
            $('[name="start_after_n_devices"]').attr('disabled', false);    
            $('[name="running_time"]').attr('disabled', false);
            $('[name="running_time_value"]').attr('disabled', false);
        }
    }
});

$('[name="btnCreateOK"]').click(function(e){
   
   e.preventDefault();
    
   $('progress').show(); 
   $(this).attr('disabled', true);
       
   /* Handling form data */ 
    var formData = new FormData($('form')[0]);
    
    $.ajax({
        url: 'content_provider.php', 
        type: 'POST',
        xhr: function() {  // custom xhr
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){ // check if upload property exists
                myXhr.upload.addEventListener('progress', function(e) {
                                                                if(e.lengthComputable){
                                                                    $('progress').attr({value:e.loaded,max:e.total});
                                                                }
                                                            }, false); // for handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
        //beforeSend: beforeSendHandler,
        success: function(result){
            
            $('progress').hide();
            
            // react to response code
            switch(result){
                case '0':   $('.hero-unit').html('<h3 class="text-center">No such user found!</h3>');
                            break;
                case '1':   $('.hero-unit').html('<h3 class="text-center">You created a study <strong>'+ $('#study_name').val() +'</strong></h3>');
                            break;
                case '2':   $('.hero-unit').html('<h3 class="text-center">That file extension was not accepted by server! Please, user <strong>*.apk</strong></h3>');
                            break;
                case '3':   $('.hero-unit').html('<h3 class="text-center">The filesize exceeds permitted size!</h3>');
                            break;
                case '4':   $('.hero-unit').html('<h3 class="text-center">Cannot set permission for file on server!</h3>');
                            break;
                default:    $('.hero-unit').html('<h3 class="text-center">Something went wrong with creating user study. Sorry.</h3>');  
            }
        },
        //error: errorHandler,
        // Form data
        data: formData,
        //Options to tell JQuery not to process data or worry about content-type
        cache: false,
        contentType: false,
        processData: false
    });
});