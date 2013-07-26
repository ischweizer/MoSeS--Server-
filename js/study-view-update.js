/* Download handler */
$('[name="btnDownloadApp"]').click(function(e){
    e.preventDefault(); 
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
    window.location.href = './apk/'+ p.find('[name="userhash"]').val() +'/'+ p.find('[name="apkhash"]').val() +'.apk';
});

/* Confirm dialog */
$('.confirm-delete').click(function(e) {
    e.preventDefault();
    $('#modal-from-dom').modal('show'); 
});

/* Button confirm study deletion */
$('.btnConfirm').click(function(e){

    // removing APK
    $.post("study.php", { 'remove': $('.confirm-delete').val() })
        .done(function() {
          location.reload();
    });
    
   e.preventDefault(); 
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
    p.find('[name="android_version"]').hide();
    p.find('[name="start_date_text"]').hide();
    p.find('[name="end_date_text"]').hide();
    p.find('[name="description_text"]').hide(); 
    p.find('[name="max_devices_number_text"]').hide();
    p.find('[name="allowed_join_text"]').hide();
    p.find('[name="private_text"]').hide();
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
    //p.find('[name="quests_select"]').show();
    p.find('[name="uploadFile"]').show();
    p.find('[name="btnUpdateOK"]').show();
    p.find('[name="btnUpdateCancel"]').show();
   
    $(this).attr('disabled',true);
});

/* Handling of button send updated study to server and show changes */
$('[name="btnUpdateOK"]').click(function(e){
   
   $(this).attr('disabled', true);
   /* ------------------------ */
   
   // get the parent of selected stuff
   var p = $(this).parent().parent().parent();
   
   /* Handling form data */ 
    var formData = new FormData($(this).parent().parent().parent().parent().parent().find('form')[0]);

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
                p.find('progress').hide();
                p.find('[name="btnUpdateStudy"]').attr('disabled',false);
                p.find('[name="btnUpdateOK"]').attr('disabled', false);
                
                // get all info from inputs and inline substitute with old one
                p.parent().parent().parent().parent().find('[name="study_title_link"]').text(p.find('[name="apk_title"]').val());
                p.find('[name="study_title_text"]').text(p.find('[name="apk_title"]').val());
                p.find('[name="android_version"]').text(p.find('[name="android_version_select"] :selected').text());
                p.find('[name="start_date_text"]').text(p.find('[name="start_date"]').val());
                p.find('[name="end_date_text"]').text(p.find('[name="end_date"]').val());
                p.find('[name="description_text"]').text(p.find('[name="description"]').val()); 
                p.find('[name="max_devices_number_text"]').text(p.find('[name="max_devices_number"]').val());
                    
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
$('[name="btnUpdateOK"], [name="btnUpdateCancel"]').click(function(e){
 
    e.preventDefault();
    
   // get the parent of selected stuff
   var p = $(this).parent().parent().parent();
   /* Hide and show form stuff */
   
   p.find('[name="study_title_text"]').show();
   p.find('[name="android_version"]').show();
   p.find('[name="start_date_text"]').show();
   p.find('[name="end_date_text"]').show();
   p.find('[name="description_text"]').show(); 
   p.find('[name="max_devices_number_text"]').show();
   p.find('[name="allowed_join_text"]').show();
   p.find('[name="private_text"]').show();
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
   p.find('[name="btnUpdateCancel"]').hide();
   p.find('[name="progress"]').hide();
   
   $(this).parent().parent().parent().parent().parent().find('[name="btnUpdateStudy"]').attr('disabled',false);
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