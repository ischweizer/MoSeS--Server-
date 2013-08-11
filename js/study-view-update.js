/* Download handler */
$('[name="btnDownloadApp"]').click(function(e){
    e.preventDefault(); 
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
    location.href = './apk/'+ p.find('[name="userhash"]').val() +'/'+ p.find('[name="apkhash"]').val() +'.apk';
});

/* Confirm dialog */
$('.confirm-delete').click(function(e) {
    e.preventDefault();
    $('.btnConfirm').val($(this).val());
    $('#modal-from-dom').modal('show'); 
});

/* Button confirm study deletion */
$('.btnConfirm').click(function(e){
    e.preventDefault(); 
    
    // removing APK
    $.post("content_provider.php", { 'study_remove': $('.btnConfirm').val() })
        .done(function() {
          location.reload();
    });
});

/* Button cancel study deletion */
$('.btnConfirmCancel, .close').click(function(){
   $('#modal-from-dom').modal('hide'); 
});
/* ------------------- */

/* Showing form data */
$('[name="btnUpdateStudy"]').click(function(){ 
    
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
    /* Hide and show form stuff */
    
    p.find('[name="study_title_text"]').hide();
    p.find('[name="android_version_text"]').hide();
    p.find('[name="start_date_text"]').hide();
    p.find('[name="end_date_text"]').hide();
    p.find('[name="description_text"]').hide(); 
    p.find('[name="max_devices_number_text"]').hide();
    p.find('[name="allowed_join_text"]').hide();
    p.find('[name="joined_devices_text"]').hide();
    p.find('.survey_available_text').hide();
    p.find('[name="private_text"]').hide();
    p.find('.surveyShowHide').hide();
    p.find('.surveyRemove').hide();
    //p.find('[name="quests"]').hide();  
                 
    p.find('[name="android_version_select"]').show();
    p.find('.controls :input').show();
    p.find('[name="study_period_text1"]').show();
    p.find('[name="study_period_text2"]').show();
    p.find('[name="start_after_n_devices_text"]').show();
    p.find('[name="running_time_text"]').show();
    p.find('[name="description"]').show();
    p.find('[name="allowed_join"]').show();
    p.find('[name="private_type"]').show();
    p.find('[name="btnAddSurvey"]').show();
    //p.find('[name="quests_select"]').show();
    p.find('[name="uploadFile"]').show();
    p.find('.btnUpdateOK').show();
    p.find('.btnUpdateCancel').show();
   
    $(this).attr('disabled',true);
});

/* Handling of button send updated study to server and show changes */
$('.btnUpdateOK').click(function(e){
   
   $(this).attr('disabled', true);
   /* ------------------------ */
   
   // get the parent of selected stuff
   var p = $(this).parent().parent().parent();
   
   /* Handling form data */ 
    var formData = new FormData($(this).parent().parent().parent().parent().parent().find('form')[0]);

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
        surveysJSON[survey_i] = {'survey_form_id':survey_form_id,
                                 'survey_form_questions':questions}; 
    });
    
    // append created JSON object to form data
    formData.append('survey_json', JSON.stringify(surveysJSON));
    
    /* ******************************** */
    
    $.ajax({
        url: 'content_provider.php',  
        type: 'POST',
        xhr: function() {  // custom xhr
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){ // check if upload property exists
                myXhr.upload.addEventListener('progress', function(e) {
                                                                if(e.lengthComputable){
                                                                    p.find('progress').attr({value:e.loaded,max:e.total});
                                                                }
                                                            }, false); // for handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
        //beforeSend: beforeSendHandler,
        success: function(result){
            if(result == '1'){
                /*
                p.find('progress').hide();
                p.find('[name="btnUpdateStudy"]').attr('disabled',false);
                p.find('.btnUpdateOK').attr('disabled', false);
                
                // get all info from inputs and inline substitute with old one
                p.parent().parent().parent().parent().find('[name="study_title_link"]').text(p.find('[name="apk_title"]').val());
                p.find('[name="study_title_text"]').text(p.find('[name="apk_title"]').val());
                p.find('[name="android_version_text"]').text(p.find('[name="android_version_select"] :selected').text());
                p.find('[name="start_date_text"]').text(p.find('[name="start_date"]').val());
                p.find('[name="end_date_text"]').text(p.find('[name="end_date"]').val());
                p.find('[name="description_text"]').text(p.find('[name="description"]').val()); 
                p.find('[name="max_devices_number_text"]').text(p.find('[name="max_devices_number"]').val());
                
                // forming criterion
                var startCriterion = $('[name="start_after_n_devices"]').val();                 
                if(startCriterion.length != 0){
                    var content = 'Commencement after '+ startCriterion +' user';
                    if(startCriterion > 1){
                        content += 's';
                    }
                    content += ' join';
                    if(startCriterion > 1){
                        content += '';
                    }else{
                        content += 's';
                    }
                    content += '.';
                    startCriterion = content;
                }
                startCriterion += 'Commenced while creating '+ $('[name="apk_title"]').val() +'.';
                
                // setting starting criterion
                p.find('[name="start_date_text"]').text(startCriterion);
                       
                // forming running time             
                var runningTime = $('[name="running_time"]').val();
                if(runningTime.length != 0){
                    runningTime = 'The termination after '+ runningTime +' hours from the date of start.';
                }else{
                    runningTime = 'Terminated immediately after creating '+ $('[name="apk_title"]').val() +'.';
                }
                
                // setting running time
                p.find('[name="end_date_text"]').text(runningTime);
                
                // show joined devices string
                p.find('[name="joined_devices_text"]').show();
                
                if(p.find('[name="setup_types"]').is(':checked')){
                   p.find('[name="allowed_join_text"]').text("This study is avalaible for everyone."); 
                }else{
                   p.find('[name="allowed_join_text"]').text("Only invited people can see this study!"); 
                }
                
                if(p.find('[name="private"]').is(':checked')){
                   p.find('[name="private_text"]').html("This study marked as <strong>private</strong>."); 
                }else{
                   p.find('[name="private_text"]').html("This study marked as <strong>public</strong>."); 
                }
                
                // removing survey controls
                p.parent().find('[name="survey_controls"]').remove();*/
                
                location.reload();
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
    
    e.preventDefault();
});

/* Hide edit form data */
$('.btnUpdateOK, .btnUpdateCancel').click(function(e){
 
    e.preventDefault();
    
   // get the parent of selected stuff
   var p = $(this).parent().parent().parent();
   /* Hide and show form stuff */
   
   p.find('[name="study_title_text"]').show();
   p.find('[name="android_version_text"]').show();
   p.find('[name="start_date_text"]').show();
   p.find('[name="end_date_text"]').show();
   p.find('[name="description_text"]').show(); 
   p.find('[name="max_devices_number_text"]').show();
   p.find('[name="allowed_join_text"]').show();
   p.find('[name="private_text"]').show();
   // show joined devices string
   p.find('[name="joined_devices_text"]').show();
   p.find('.survey_available_text').show();
   p.find('.surveyShowHide').show();
   p.find('.surveyRemove').show();
   //p.find('[name="quests"]').show();
   p.find('[name="progress"]').show();  
                 
   p.find('[name="android_version_select"]').hide();
   p.find('.controls :input').hide();
   p.find('[name="study_period_text1"]').hide();
   p.find('[name="study_period_text2"]').hide();
   p.find('[name="start_after_n_devices_text"]').hide();
   p.find('[name="running_time_text"]').hide();
   p.find('[name="description"]').hide();
   p.find('[name="allowed_join"]').hide();
   p.find('[name="private_type"]').hide();
   //p.find('[name="quests_select"]').hide();
   p.find('[name="uploadFile"]').hide();
   p.find('.btnUpdateCancel').hide();
   p.find('[name="progress"]').hide();
   p.parent().parent().find('[name="btnAddSurvey"]').hide();
   
   $(this).parent().parent().parent().parent().parent().find('[name="btnUpdateStudy"]').attr('disabled',false);
});

// special activities for cancel user study button
$('.btnUpdateCancel').click(function(e){
    e.preventDefault();
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
     
    // removing survey controls if it was selected
    p.parent().find('[name="survey_controls"]').remove();
});

$('[name="study_period"]').click(function(){
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent().parent().parent();
    
    if($(this).is(':checked')){
        if($(this).val() == 1){
            p.find('[name="start_date"]').attr('disabled', false);    
            p.find('[name="end_date"]').attr('disabled', false);
            
            p.find('[name="start_after_n_devices"]').attr('disabled', true);    
            p.find('[name="running_time"]').attr('disabled', true);
            p.find('[name="running_time_value"]').attr('disabled', true);
        }
        
        if($(this).val() == 2){
            p.find('[name="start_date"]').attr('disabled', true);    
            p.find('[name="end_date"]').attr('disabled', true);
            
            p.find('[name="start_after_n_devices"]').attr('disabled', false);    
            p.find('[name="running_time"]').attr('disabled', false);
            p.find('[name="running_time_value"]').attr('disabled', false);
        }
    }
});

$('.surveyShowHide').click(function(e){
    e.preventDefault();
    
    var p = $(this).parent().parent().parent();
    
    if(p.find('.survey_content').is(':visible')){
        p.find('.survey_content').hide();
        p.find('.surveyShowHide').find('i').addClass('icon-chevron-right');
        p.find('.surveyShowHide').find('i').removeClass('icon-chevron-down');
    }else{
        p.find('.survey_content').show();
        p.find('.surveyShowHide').find('i').addClass('icon-chevron-down');
        p.find('.surveyShowHide').find('i').removeClass('icon-chevron-right');
    }
});

$('.surveyRemove').click(function(e){
    e.preventDefault();
    
    var p = $(this).parent().parent().parent();
    
    // Remove survey from user study
    $.post("content_provider.php", { 'study_survey_remove': p.find('.surveyRemove').val(),
                                     'study_survey_remove_code': 4931})
        .done(function(result) {
          if(result){
              p.find('.survey_content').remove();
              p.find('.surveyShowHide').remove();
              p.find('.surveyRemove').remove();
              p.find('.survey_available_text').remove();
          }else{
              alert("Cannot remove user study survey! Try again later.");
          }
    });
});

$('.btnSurveyResultsExportCsv').click(function(e){
    e.preventDefault();
    
    location.href = './export.php?id='+ $(this).val() +'&m=csv';
});