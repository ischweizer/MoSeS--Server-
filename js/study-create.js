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
    
    /* Gather a JSON-Object for surveys */
    var surveysJSON = {};
    
    // find all surveys
    $('.survey').each(function(survey_i, elem){
        
        var survey = $(this);
        var survey_id = parseInt(survey.find('.survey_id').val());
        var questions = [];

        // iterate through all questions of one survey
        survey.find('.survey_question').each(function(question_i, elem2){
            
            var question = $(this);
            var answers = [];
            
            // find question type
            var question_type = question.parent().find('.survey_question_type').val();
            
            // find all answers
            question.parent().find('.survey_answer').each(function(answer_i, elem3){
                var answer = $(this);
                answers.push(answer.val());
            });
            
            questions.push({'question_type':question_type,
                            'question':question.val(),
                            'answers':answers});
            
        }); 

        // populate JSON object
        surveysJSON[survey_i] = {'survey_id':survey_id,
                                 'survey_questions':questions}; 
    });
    
    // append created JSON object to form data
    formData.append('survey_json', JSON.stringify(surveysJSON));
    
    /* ******************************** */
    
    $.ajax({
        url: 'content_provider.php', 
        type: 'POST',
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            
            // check if upload property exists
            if(myXhr.upload){
                myXhr.upload.addEventListener('progress', function(e) {
                                                                if(e.lengthComputable){
                                                                    $('progress').attr({value:e.loaded,max:e.total});
                                                                }
                                                            }, false);
            }
            return myXhr;
        },
        //beforeSend: beforeSendHandler,
        success: function(result){
            
            $('progress').hide();
            
            // react to response code
            switch(result){
                case '0':   $('.hero-unit').html('<h3 class="text-center">No such user found!</h3>');
                            break;
                case '1':   $('.hero-unit').html('<h3 class="text-center">You created a study <strong>'+ $('#study_name').val() +'</strong></h3>');
                            break;
                case '2':   $('.hero-unit').html('<h3 class="text-center">That file extension was not accepted by server! Please, use <strong>*.apk</strong></h3>');
                            break;
                case '3':   $('.hero-unit').html('<h3 class="text-center">The filesize exceeds permitted size!</h3>');
                            break;
                case '4':   $('.hero-unit').html('<h3 class="text-center">Cannot set permission for file on server!</h3>');
                            break;
                default:    $('.hero-unit').html('<h3 class="text-center">Something went wrong with creating user study. Sorry.</h3>');  
            }
        },
        //error: errorHandler,
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
});