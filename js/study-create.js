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

/* Checks input to be a number */
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

// Check the data, gather and send to server 
$('.form-horizontal').on('click','.btnCreateOK',function(e){
   
   e.preventDefault();
   
   /*************** CHECKS **************/
   
   // error if no title given
   if($.trim($('[name="apk_title"]').val()).length == 0){
       alert("Please enter the apk title.");
       return;
   }
   
   // error if study period from date to date selected and those are empty
   if($('[name="study_period"]:selected').val() == "1"){
       if($.trim($('[name="start_date"]').val()).length == 0){
           alert("Please enter start date.");
           return;
       }
       
       if($.trim($('[name="end_date"]').val()).length == 0){
           alert("Please enter end date.");
           return;
       }
       
       // TODO: fix it. not working
       var startDate = $.datepicker.parseDate("yy-mm-dd", $('[name="start_date"]').val());
       var endDate = $.datepicker.parseDate("yy-mm-dd", $('[name="end_date"]').val());
       
       if(startDate.getTime() - endDate.getTime() < 0){
           alert("Start date is before end date!");
           return;
       }
   }
   
   // for minimum devices and running period
   if($('[name="study_period"]:selected').val() == "2"){
       
       var startAfter = $.trim($('[name="start_after_n_devices"]').val()); 
       if(startAfter.length == 0){
           alert("Please minimum devices to start after.");
           return;
       }
       
       var runningTime = $.trim($('[name="running_time"]').val());
       if(runningTime.length == 0){
           alert("Please enter running time.");
           return;
       }
       
       if(!isNumber(startAfter) || !isNumber(runningTime)){
           alert("Please enter a number value.");
           return;
       }
       
       if(startAfter < 1){
           alert("Please enter minimum start number greater or equal 1.");
           return;
       }
       
       if(runningTime < 1){
           alert("Please enter running period greater or equal 1.");
           return;
       }
   }
   
   // error if it not apk file
   var filename = $.trim($('[name="file"]').val()).toLowerCase();
   
   if(filename.lastIndexOf('apk') != filename.length-3){
       alert("Please select an APK file.");
       return;
   }
   
   /****************************************/
    
   $('progress').show(); 
   $(this).attr('disabled', true);
       
   /* Handling form data */ 
    var formData = new FormData($('form')[0]);
    
    /* Gather a JSON-Object for surveys */
    var surveysJSON = {};
    
    // find all forms in a survey
    $('.survey_form').each(function(survey_i, elem){
        
        var survey = $(this);
        var survey_form_id = parseInt(survey.find('.survey_form_id').val());
        var questions = [];
        
        // iterate through all questions of one survey
        survey.find('.survey_question').each(function(question_i, elem2){
            
            var question = $(this);
            var answers = [];
            
            // find question type
            var question_type = question.parent().find('.survey_question_type').val();
            
            // find mandatory question flag
            var question_mandatory = 0; //default
            if(question.parent().find('.survey_question_mandatory').is(':checked')){
                question_mandatory = 1; 
            }
            
            // find all answers
            question.parent().find('.survey_answer').each(function(answer_i, elem3){
                var answer = $(this);
                
                if(answer.text().length != 0 && answer.val().length == 0){
                    answers.push(answer.text());
                }else{
                    answers.push(answer.val());    
                }
            });

            if(question.text().length != 0 && question.val().length == 0){
                questions.push({'question_type':question_type,
                                'question_mandatory':question_mandatory,
                                'question':question.text(),
                                'answers':answers});
            }else{
                questions.push({'question_type':question_type,
                                'question_mandatory':question_mandatory,
                                'question':question.val(),
                                'answers':answers});
            }
        }); 
        
        // populate JSON object
        surveysJSON[survey_i] = {'survey_form_id':survey_form_id,
                                 'survey_form_questions':questions}; 
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
                case '1':   $('.hero-unit').html('<h3 class="text-center">You created a study "<strong>'+ $('[name="apk_title"]').val() +'</strong>"</h3>');
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