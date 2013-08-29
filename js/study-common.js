// iterate through all menus and remove selection
$('.dropdown').each(function(){
    $(this).removeClass('active');   
});
// add menu selection for this page
$('.nav-menu4').addClass('active');

/* Datepicker format */
$('[name="start_date"]').datepicker({ dateFormat: "yy-mm-dd" });
$('[name="end_date"]').datepicker({ dateFormat: "yy-mm-dd" });
/* ---------------- */

/* If not a number or less than 1 -> substitute with 1 in value */
$('[name="start_after_n_devices"]').change(function(){
    if(!isNumber($(this).val()) || $(this).val() < 1){
        $(this).val('1');
    }
});

$('[name="publishMethodInvite"]').change(function(){
    if($(this).is(':checked')){
        $('[name="max_devices_number"]').attr('disabled', false);
    }else{
        $('[name="max_devices_number"]').attr('disabled', true);
    }
});

$('.content_appears_here').on('click','.scrollTo',function(e){
    e.preventDefault();
    
    scrollToElement('.survey_select');
});

$('[name="btnAddSurvey"]').click(function(e){
    e.preventDefault();
    
    // move button create study to bottom of page
    $('.btnCreateOK').css('margin-left','10em');   // TODO: fix that to proper value
    $('.btnCreateOK').css('margin-top','4em');
    var createButton = $('.my_control-group_create').find('.controls').html();
    $('.my_control-group_create').remove();
    $('.survey_controls').append(createButton);
    
    // move cancel and send buttons from update user study
    $('.btnUpdateOK').css('margin-top','4em');
    $('.btnUpdateCancel').css('margin-left','12em');   // TODO: fix that to proper value
    $('.btnUpdateCancel').css('margin-top','4em');
    var updateCancelButtons = $('.control-group-update').find('.controls').html();
    $('.control-group-update').remove();
    $('.survey_controls').append(updateCancelButtons);
    
    // move progress bar aswell
    var progressBar = $('[name="progress"]').parent().html();
    $('[name="progress"]').parent().parent().remove();
    $('.survey_controls').append(progressBar);
    
    //$('[name="progress"]').css('margin-left','10em');   // TODO: fix that to proper value
    $('[name="progress"]').css('margin-top','2.5em');
    $('[name="progress"]').css('float','right');
    
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
    
    p.find('.survey_controls').show();
    //p.find('[name="survey_container"]').hide();
    
    $(this).hide();
    
});

$('.survey_controls').on('click','.btnAddForm',function(e){
    e.preventDefault();
    
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
    var pForSurveySelect = $(this).parent().parent();
    
    if(parseInt(pForSurveySelect.find('.survey_select :selected').val()) != 9001){
    
        /*
         * Requesting server for standard questions for a survey ID
         */
        $.post("content_provider.php", { 'get_questions': pForSurveySelect.find('.survey_select :selected').val(), 'get_questions_pwd' : 6767 })
            .done(function(result) {
                
                if(result){
                    
                    var data = $.parseJSON(result);

                    var content = '<div class="row-fluid survey_form" style="border:2px solid #CCC;">'+
                                  '<div class="survey_name text-center">'+ pForSurveySelect.find('.survey_select :selected').text() +'</div>'+
                                  '<div class="span10 survey_body">'+
                                  '<!--Body content-->'+
                                  '<div class="survey_question_container">';
                   
                   var answers_yes_no =   '<ul>'+
                                          '<li><input type="radio" disabled="disabled"><span class="survey_answer">Yes</span></li>'+
                                          '<li><input type="radio" disabled="disabled"><span class="survey_answer">No</span></li>'+
                                          '<li><input type="radio" disabled="disabled"><span class="survey_answer">Not sure</span></li>'+
                                          '</ul>';
                                          
                   var answers_text = '<ul>'+
                                      '<li><textarea cols="20" rows="3" disabled="disabled" placeholder="Answer will be here..."></textarea></li>'+
                                      '</ul>';
                   
                   var answers_likert_scale = '<ul>'+
                                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Strongly Disagree</span></li>'+
                                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Disagree</span></li>'+
                                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Neutral</span></li>'+
                                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Agree</span></li>'+
                                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Strongly Agree</span></li>'+
                                              '</ul>';
                  
                   $(data.content).each(function(i, question){
                       
                        content += '<div>';
                        content += (i+1)+'. <span class="survey_question">'+ question.question +'</span>';
                        if(question.question_mandatory == 1){
                            content += '<label style="float: right;"><input type="checkbox" class="survey_question_mandatory" checked="checked" disabled="disabled" style="margin-right: 0.5em;">Mandatory question</label>';
                        }
                        content += '<br>';
                        
                        // handle type of question's answers
                        switch(question.question_type){
                            
                            // YES/NO 
                            case 1: content += answers_yes_no;
                                    content += '<input class="survey_question_type" type="hidden" value="1">';
                                    break;
                            
                            // Text        
                            case 2: content += answers_text; 
                                    content += '<input class="survey_question_type" type="hidden" value="2">';
                                    break;
                            
                            // Scale        
                            case 3: content += answers_likert_scale;
                                    content += '<input class="survey_question_type" type="hidden" value="3">';
                                    break;
                             
                            // Multiple choice        
                            case 4: var answers_multiple_choice = '<ul>';
                                       for(var j=1; j <= question.question_number_of_answers; j++){
                                          answers_multiple_choice += '<li>'+
                                                                     '<input type="checkbox" value="'+ j +'" disabled="disabled">'+
                                                                     '<span><input type="text" class="survey_answer" placeholder="Answer here" disabled="disabled"></span>'+
                                                                     '</li>'; 
                                       }
                                       answers_multiple_choice += '</ul>';
                                       content += answers_multiple_choice; 
                                       content += '<input class="survey_question_type" type="hidden" value="4">'; 
                                    break;
                                    
                            // Single choice
                            case 5: var answers_single_choice = '<ul>';
                                      for(var j=1; j <= question.question_number_of_answers; j++){
                                        answers_single_choice += '<li>'+
                                                                 '<input type="radio" value="'+ j +'" disabled="disabled">'+
                                                                 '<span><input type="text" class="survey_answer" placeholder="Answer here" disabled="disabled"></span>'+
                                                                 '</li>'; 
                                      }
                                      answers_single_choice += '</ul>';
                                      content += answers_single_choice;
                                      content += '<input class="survey_question_type" type="hidden" value="5">'; 
                                    break;
                                    
                            default: content += 'Something went wrong with displaying question type answers!<br>'; 
                        }
                        
                        content += '</div>';
                   });
                   
                   content += '</div>'+
                              '<i class="icon icon-chevron-up scrollTo"></i>'+
                              '<input type="hidden" class="survey_form_id" value="'+ pForSurveySelect.find('.survey_select :selected').val() +'">'+
                              '</div>'+
                              '<div class="span1"><button class="btn btn-danger btnRemoveSurvey">X</button></div>'+
                              '</div>';
                         
                   p.find('.content_appears_here').append(content);
                   
                   // scroll to inserted element
                   scrollToElement($('.survey_form').last());
                }   
        });
        
    }else{
                      
       var content =  '<div class="row-fluid survey_form" style="border:2px solid #CCC;">'+
                      '<div class="survey_name text-center">'+ pForSurveySelect.find('.survey_select :selected').text() +'</div>'+  
                      '<div class="span10 survey_body">'+
                      '<!--Body content-->'+
                      '<div class="survey_question_container">'+
                      'Compose your questions below!<br>'+
                      '  <div>'+
                      '    <div class="survey_elements_container">'+
                      '      <select class="survey_elements">'+
                      '         <option value="1">Yes/No question</option>'+
                      '         <option value="2">Text question</option>'+
                      '         <option value="3">Likert scale question</option>'+
                      '         <option value="4">Multiple choice question</option>'+
                      '         <option value="5">Single choice question</option>'+
                      '      </select>'+
                      '      <label class="survey_elements" style="display: none;">Number of answers:</label>'+
                      '      <input type="text" title="Number of answers" value="5" maxlength="2" style="width: 1.2em; display: none;">'+
                      '      <button class="btn survey_elements btnAddQuestionOK">Add question</button>'+
                      '    </div>'+
                      '  </div>'+
                      '</div>'+
                      '<input type="hidden" class="survey_form_questions_counter" value="0">'+
                      '<input type="hidden" class="survey_form_id" value="9001">'+
                      '</div>'+
                      '<div class="span1"><button class="btn btn-danger btnRemoveSurvey">X</button></div>'+
                      '</div>';
        
        p.find('.content_appears_here').append(content);
        
        // scroll to inserted element
        scrollToElement($('.survey_form').last());
    }
});

// on change question type in survey 
$('.content_appears_here').on('change','.survey_elements',function(e) {
    e.preventDefault();
    switch(parseInt($(this).val())){
        // YES/No question
        case 1: // hide number of questions
                $(this).parent().find(':text').hide();
                $(this).parent().find('label').hide();
                break;
                
        // Text question
        case 2: $(this).parent().find(':text').hide();
                $(this).parent().find('label').hide();
                break;
                
        // Scale question
        case 3: $(this).parent().find(':text').hide();
                $(this).parent().find('label').hide();
                break;
                
        // Multiple choice question
        case 4: $(this).parent().find(':text').show();
                $(this).parent().find('label').show();
                break;
                
        // Single choice question
        case 5: $(this).parent().find(':text').show();
                $(this).parent().find('label').show();
                break;
    }
    return false;
});

// remove whole survey
$('.content_appears_here').on('click','.btnRemoveSurvey',function(e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    return false;
});

// remove question from survey
$('.content_appears_here').on('click','.survey_remove_question',function(e){
    e.preventDefault();
    $(this).parent().remove();
    return false; 
});

/* SURVEY CONTROLS */

$('.content_appears_here').on('click', '.btnAddQuestionOK', function(e){
    e.preventDefault();
    
    var parentForQCounter = $(this).parent().parent().parent().parent();
    var quantity = parseInt($(this).parent().find(':text').val());
    var p = $(this).parent().parent().parent();
                                                  
    p.append('<div></div>');
    // copy survey control
    $(this).parent().find(':text').hide();
    $(this).parent().find('label').hide();
    p.find(':last').html($(this).parent().parent().html());
    
    // Question field (common content for all questions)
    var content = '<span class="survey_question_number">'+(parseInt(parentForQCounter.find('.survey_form_questions_counter').val())+1)+'. </span> '+
                  '<input type="text" class="survey_question" placeholder="Your question here">'+
                  '<label style="float: right;"><input type="checkbox" class="survey_question_mandatory" style="margin-right: 0.5em;">Mark as mandatory</label>'+
                  '<button class="btn btn-link btnRemoveQuestion">Remove question</button>'+
                  '<br>';
      
    switch(parseInt($(this).parent().find('.survey_elements').val())){
        // YES/NO Question
        case 1: // compose all answers
                var answers = '<ul>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Yes</span></li>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">No</span></li>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Not sure</span></li>'+
                              '</ul>';
                 
                content += answers; 
                
                // append question type
                content += '<input type="hidden" class="survey_question_type" value="1">';
                
                // finally append to dom
                $(this).parent().parent().append(content);
                break;
                
        // Text question
        case 2: // compose all answers
                var answers = '<ul>'+
                              '<li><textarea cols="20" rows="3" disabled="disabled" placeholder="Answer will be here..."></textarea></li>'+
                              '</ul>';
                 
                content += answers;
                
                // append question type
                content += '<input type="hidden" class="survey_question_type" value="2">';
                
                // finally append to dom
                $(this).parent().parent().append(content);
                break; 
                
        // Scale question        
        case 3: // compose all answers
                var answers = '<ul>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Strongly Disagree</span></li>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Disagree</span></li>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Neutral</span></li>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Agree</span></li>'+
                              '<li><input type="radio" disabled="disabled"><span class="survey_answer">Strongly Agree</span></li>'+
                              '</ul>';
                 
                content += answers;
                
                // append question type
                content += '<input type="hidden" class="survey_question_type" value="3">';
                
                // finally append to dom
                $(this).parent().parent().append(content);
                break;
                
        // Multiple choice
        case 4: // compose all answers
                var answers = '<ul>';
                    for(var i=1; i <= quantity; i++){
                       answers += '<li>'+
                                  '<input type="checkbox" value="'+ i +'" disabled="disabled">'+
                                  '<span><input type="text" class="survey_answer" placeholder="Answer here"></span>'+
                                  '</li>'; 
                    }
                answers += '</ul>';
                 
                content += answers;
                
                // append question type
                content += '<input type="hidden" class="survey_question_type" value="4">';
                
                // finally append to dom
                $(this).parent().parent().append(content);
                break;
            
        // Single choice
        case 5: // compose all answers
                var answers = '<ul>';
                    for(var i=1; i <= quantity; i++){
                       answers += '<li>'+
                                  '<input type="radio" value="'+ i +'" disabled="disabled">'+
                                  '<span><input type="text" class="survey_answer" placeholder="Answer here"></span>'+
                                  '</li>'; 
                    }
                answers += '</ul>';
                 
                content += answers;
                
                // append question type
                content += '<input type="hidden" class="survey_question_type" value="5">'; 
                
                // finally append to dom
                $(this).parent().parent().append(content);
                break;               
        default:
                $(this).parent().append('Something went wrong! =(');
    }
    
    // increment question counter
    parentForQCounter.find('.survey_form_questions_counter').val(parseInt(parentForQCounter.find('.survey_form_questions_counter').val())+1);
    
    // remove survey control from dom
    $(this).parent().remove();
    
    return false;
});

$('.content_appears_here').on('click', '.btnRemoveQuestion', function(e){
    e.preventDefault();
    
    var parentForQCounter = $(this).parent().parent().parent().parent();
    // remove question
    $(this).parent().remove();
    
    // iterate throug all numbers and correct them
    var newCounter = 1;
    parentForQCounter.find('.survey_question_number').each(function(){
        $(this).text(newCounter+'. ');
        newCounter++;
    });
    
    // update question counter 
    parentForQCounter.find('.survey_form_questions_counter').val(parseInt(parentForQCounter.find('.survey_form_questions_counter').val())-1);

});

/* ---------------------------------- */