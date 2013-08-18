<?php
    
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] <= 1){
    header("Location: " . dirname($_SERVER['PHP_SELF']));
    exit;
}
                    
?><h2>Studies</h2>
<br>     
<div class="accordion" id="accordionFather">
<?php
      
   for($i=0; $i<count($USER_APKS); $i++){
       
       $APK = $USER_APKS[$i];
       $survey = $SURVEY_BY_APK_ID[$APK['apkid']];
?>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" name="study_title_link" data-toggle="collapse" data-parent="#accordionFather" href="#collapseStudies<?php echo $i; ?>">
        <?php
           echo 'Study name: "'. $APK['apktitle'] .'"'; 
        ?>
      </a>
    </div>
    <div id="collapseStudies<?php 
        echo $i;
        
        // for selected collapse use "collapse in" for class 
        ?>" class="accordion-body collapse">
      <div class="accordion-inner">
      <?php
            $startDate = !empty($APK['startdate']) ? 
                            $APK['startdate'] : ''; 
           
            $startCriterion = $APK['startcriterion'];                 
            $startCriterion = !empty($startCriterion) ? 
                                'Commencement after '. $startCriterion .' user'. 
                                ($startCriterion > 1 ? 's' : '') .' join'. 
                                ($startCriterion > 1 ? '' : 's') .'.' 
                                : 'Commenced while creating '. $APK['apktitle'] .'.';
            
            $endDate = !empty($APK['enddate']) ? 
                        $APK['enddate'] : ''; 
                        
            $runningTime = $APK['runningtime'];
            $runningTime = !empty($runningTime) ? 
                            'The termination after '. $runningTime .' hours from the date of start.' :
                            'Terminated immediately after creating '. $APK['apktitle'] .'.'; 
                        
            $joinedDevices = 'There '. ($APK['participated_count'] < 2 ? 'is' : 'are') .' '.
                                       ($APK['participated_count'] == 0 ? 'no' : $APK['participated_count']) .' '.
                                       ($APK['participated_count'] < 2 ? 'device' : 'devices') .' '.                           
                                        'currently joined to "'. $APK['apktitle'] .'".';
                                        
            $study_running = !empty($startDate) && 
                            !empty($endDate) && 
                            $now >= strtotime($startDate) &&
                            $now <= strtotime($endDate);
      
            ?>
            <form class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="updateAPKForm">
                <fieldset>
                    Study ver. <?php echo $APK['apk_version']; ?> <br>
                    <?php
                         $now = time(); 
                         // RUNNING User Study!
                         if($study_running){
                            ?>
                            <h3 class="txtUSWarning text-center" style="color: green;">The User Study is running!</h3>
                            <?php
                         }else{   
                    
                             // FINISHED User Study!
                             if($APK['ustudy_finished'] == 1){
                                ?>
                                <h3 class="txtUSWarning text-center" style="color: red;">This user study is finished!</h3>
                                <?php
                             }
                         }
                     ?>
                    <div class="control-group">
                        <label class="control-label">APK title: </label>
                        <div class="controls">
                            <div name="study_title_text"><?php echo $APK['apktitle']; ?></div>
                            <input type="text" name="apk_title" value="<?php echo $APK['apktitle']; ?>" maxlength="50" placeholder="Study name" style="display: none;" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Lowest Android version: </label>
                        <div class="controls">
                            <div name="android_version_text"><?php echo getAPILevel($APK['androidversion']); ?></div>
                            <select name="android_version_select" style="display: none;">
                              <?php
                                 for($j=1; $j<=getAllAPIsCount(); $j++){ 
                              ?>
                              <option value="<?php echo $j; ?>"<?php if($APK['androidversion'] == $j) echo ' selected="selected"'; ?>><?php echo getAPILevel($j); ?></option>
                              <?php
                                 }
                              ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group" name="study_period_text1" style="display: none;">
                        <label class="control-label"></label>
                        <div class="controls">
                            <label><input type="radio" name="study_period" value="1"<?php 
                                echo !empty($APK['startdate']) || !empty($APK['enddate']) ? ' checked="checked"' : '' ; 
                                ?>> Study period from date to date</label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Start: </label>
                        <div class="controls">
                            <div name="start_date_text"><?php echo !empty($APK['startdate']) ? $startDate : $startCriterion; ?></div>
                            <input type="text" name="start_date" maxlength="50" placeholder="yyyy-mm-dd"<?php 
                                echo !empty($APK['startdate']) || !empty($APK['enddate']) ? '' : ' disabled="disabled"'; ?> value="<?php echo $startDate; ?>" style="display: none;" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">End: </label>
                        <div class="controls">
                            <div name="end_date_text"><?php echo !empty($APK['enddate']) ? $endDate : $runningTime; ?></div>
                            <input type="text" name="end_date" maxlength="50" placeholder="yyyy-mm-dd"<?php 
                                echo !empty($APK['startdate']) || !empty($APK['enddate']) ? '' : ' disabled="disabled"'; ?> value="<?php echo $endDate; ?>" style="display: none;" />
                        </div>
                    </div>
                    <div class="control-group" name="study_period_text2" style="display: none;">
                        <label class="control-label"></label>
                        <div class="controls">
                            <label><input type="radio" name="study_period" value="2"<?php 
                                echo !empty($APK['startcriterion']) || !empty($APK['runningtime']) ? ' checked="checked"' : '' ; 
                                ?>> Study for minimum devices and running period</label>
                        </div>
                    </div>
                    <div class="control-group" name="start_after_n_devices_text" style="display: none;">
                        <label class="control-label">Minimum number of devices to start after: </label>
                        <div class="controls">
                            <input type="number" name="start_after_n_devices" maxlength="10" placeholder="Number"<?php 
                                echo !empty($APK['startcriterion']) || !empty($APK['runningtime']) ? ' value="'. $APK['startcriterion'] .'"' : ' disabled="disabled"' ; 
                                ?> />
                        </div>
                    </div>
                    <div class="control-group" name="running_time_text" style="display: none;">
                        <label class="control-label">Running period:</label>
                        <div class="controls">
                            <input type="text" name="running_time" maxlength="50" placeholder="Number"<?php 
                                echo !empty($APK['startcriterion']) || !empty($APK['runningtime']) ? ' value="'. $APK['runningtime'] .'"' : ' disabled="disabled"' ; 
                                ?> />
                            <select name="running_time_value"<?php 
                                echo !empty($APK['startcriterion']) || !empty($APK['runningtime']) ? '' : ' disabled="disabled"' ; 
                                ?>>
                                <option value="h">Hours</option>
                                <option value="d">Days</option>
                                <option value="m">Months</option>
                                <option value="y">Years</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Description: </label>
                        <div class="controls">
                            <div name="description_text"><?php echo $APK['description']; ?></div>
                            <textarea rows="3" cols="20" name="description" placeholder="Add here some description about the study" style="display: none;"><?php echo $APK['description']; ?></textarea>
                        </div>
                    </div>
                    <div name="allowed_join_text">
                    <?php
                        if(!empty($APK['restriction_device_number']) && $APK['inviteinstall'] == 1){
                            echo 'Only invited people can see this study!';
                        }
                        
                        if($APK['private'] == 1 && (empty($APK['restriction_device_number']) || $APK['inviteinstall'] != 1)){
                            echo 'This study is private (only your group has access to it).';   
                        }
                        
                        if($APK['private'] == 0 && $APK['inviteinstall'] != 1){
                            echo 'This study is avalaible for everyone.';    
                        }
                    ?>
                    </div>
                    <div class="control-group" name="allowed_join" style="display: none;">
                        <label class="control-label"></label>
                        <div class="controls">
                            <label><input type="radio" name="publishMethod" value="1" checked="checked">Publish to MoSeS (Public)</label>
                        </div>
                    </div>
                    <div class="control-group" name="invites_only_install" style="display: none;">
                        <label class="control-label"></label>
                        <div class="controls">
                            <label><input type="radio" name="publishMethod" value="2" <?php echo (!empty($APK['restriction_device_number']) && $APK['inviteinstall'] == 1 ? 'checked="checked"' : ''); ?>>Ivites only study</label>
                            <input type="number" name="max_devices_number" <?php 
                                    echo (!empty($APK['restriction_device_number']) && $APK['inviteinstall'] == 1 
                                    ? 
                                    '' : 
                                    'disabled="disabled"'); 
                                    ?> maxlength="10" placeholder="Amount of invites to send" value="<?php echo (!empty($APK['restriction_device_number']) && $APK['restriction_device_number'] != -1) ? $APK['restriction_device_number'] : ''; ?>" style="display: none;" />
                        </div>
                    </div>
                    <div name="private_text">
                        This study marked as <strong><?php echo $APK['private'] == 1 ? 'private' : 'public'; ?>.</strong>
                    </div>
                    <?php
                        if(!empty($USER_RGROUP)){
                    ?>
                    <div class="control-group" name="private_type" style="display: none;">
                        <label class="control-label"></label>
                        <div class="controls">
                            <label><input type="radio" name="publishMethod" value="3" <?php echo ($APK['private'] == 1 ? 'checked="checked"' : ''); ?> />Make visible only to my group (Private)</label>
                        </div>
                    </div>
                    <?php 
                        }?>
                    <div class="control-group" name="uploadFile" style="display: none;">
                        <label class="control-label">Select an APP: </label>
                        <div class="controls">
                            <input type="file" name="file">
                        </div>
                    </div>
                    <div name="joined_devices_text">
                    <?php
                        echo $joinedDevices; 
                    ?>
                    </div>
                    <?php
                        // checks if a users tudy survey available for this apk
                        if(!empty($survey)){
                            ?>
                            <div class="survey_available_text">A survey is attached to this user study.</div>
                            <?php
                        }
                    ?>
                    <br>
                    <div class="control-group">
                        <label class="control-label"></label>
                        <div class="controls">
                            <progress name="progress" value="0" style="display: none;"></progress>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"></label>
                        <div class="controls">
                            <button class="btn btnUpdateCancel" title="Cancel update!" style="display: none;">Cancel</button>
                            <button class="btn btn-success btnUpdateOK" title="Upload changes" style="display: none; margin-left: 20pt;">OK</button>
                        </div>
                    </div>
                </fieldset>
                <?php
                
                    // if user study got survey, show it! 
                    if(!empty($survey)){
                        ?>
                        <ul style="list-style-type: none;">
                            <li style="display: inline;"><button type="button" class="btn btn-link surveyShowHide">Show/Hide survey <i class="icon icon-chevron-right"></i></button></li>
                            <?php
                                if($APK['ustudy_finished'] != 1){ 
                                ?><li style="display: inline;"><button type="button" class="btn btn-link surveyRemove" value="<?php 
                                        echo $survey['survey_id']; 
                                ?>">Remove survey <i class="icon icon-remove-sign"></i></button></li><?php 
                                    } ?>
                        </ul>
                        <?php
                        ?><hr class="survey_content" style="display: none;">
                        <div class="survey_content" style="display: none;"><?php
                        $forms = $survey['forms'];
                        
                        foreach($forms as $form){
                            ?>
                            <div class="row-fluid survey" style="border:2px solid #CCC;">
                              <div class="survey_name text-center"><?php echo $form['form_title']; ?></div>
                                  <div class="span10 survey_body">
                                    <!--Body content-->
                                    <div class="survey_question_container">
                                    <?php
                                         $questions = $form['questions'];
                                         
                                         $k=1;
                                         foreach($questions as $question){
                                             echo $k .'. '. $question['question'] .'<br>';
                                             $k++;
                                             
                                             $answers_yes_no =  '<ul style="list-style-type: none;">'.
                                                                    '<li><input type="radio" disabled="disabled"><span class="survey_answer">Yes</span></li>'.
                                                                    '<li><input type="radio" disabled="disabled"><span class="survey_answer">No</span></li>'.
                                                                    '<li><input type="radio" disabled="disabled"><span class="survey_answer">Not sure</span></li>'.
                                                                '</ul>';
                                                                      
                                             $answers_text = '<ul style="list-style-type: none;">'.
                                                                '<li><textarea cols="20" rows="3" disabled="disabled" placeholder="Answer will be here..."></textarea></li>'.
                                                             '</ul>';
                                               
                                             $answers_likert_scale = '<ul style="list-style-type: none;">'.
                                                                        '<li><input type="radio" disabled="disabled"><span class="survey_answer">"Strongly Disagree"</span></li>'.
                                                                        '<li><input type="radio" disabled="disabled"><span class="survey_answer">"Disagree"</span></li>'.
                                                                        '<li><input type="radio" disabled="disabled"><span class="survey_answer">"Neutral"</span></li>'.
                                                                        '<li><input type="radio" disabled="disabled"><span class="survey_answer">"Agree"</span></li>'.
                                                                        '<li><input type="radio" disabled="disabled"><span class="survey_answer">"Strongly Agree"</span></li>'.
                                                                    '</ul>';
                                                                    
                                             switch(intval($question['question_type'])){
                                                 case 1: echo $answers_yes_no;
                                                         break;
                                                 case 2: echo $answers_text;
                                                         break;
                                                 case 3: echo $answers_likert_scale;
                                                         break;
                                                 case 4: if(!empty($question['answers'])){
                                                             foreach($question['answers'] as $answer){
                                                                 echo '<ul style="list-style-type: none;">'.
                                                                        '<li>'.
                                                                            '<input type="checkbox" disabled="disabled">'.
                                                                            '<span><input type="text" class="survey_answer" value="'. $answer .'" placeholder="Answer here" disabled="disabled"></span>'.
                                                                         '</li>'.
                                                                      '</ul>';
                                                             }
                                                         }
                                                         break;
                                                 case 5: if(!empty($question['answers'])){
                                                             foreach($question['answers'] as $answer){
                                                                 echo '<ul style="list-style-type: none;">'.
                                                                        '<li>'.
                                                                            '<input type="radio" disabled="disabled">'.
                                                                            '<span><input type="text" class="survey_answer" value="'. $answer .'" placeholder="Answer here" disabled="disabled"></span>'.
                                                                         '</li>'.
                                                                      '</ul>';
                                                             }
                                                         }
                                                         break;
                                                 default: echo 'Something went wrong!'; 
                                             }
                                         }
                                     ?>
                                    </div>                              
                                  </div>
                              </div>
                            <?php                               
                        }
                        ?>
                        </div>
                        <?php
                    }
                ?>
                <hr>
                <button class="btn" name="btnAddSurvey" value="" style="float: right; display: none;"><i class="icon-plus-sign"></i> Add survey</button>
                <?php
                    
                    include("./include/_survey.php");        
                
                // check if user study already finished
                if($APK['ustudy_finished'] != 1){
                    ?>
                    <input type="hidden" name="study_update" value="6825">
                    <?php
                }   
                ?>
                <input type="hidden" name="apk_id" value="<?php echo $APK['apkid']; ?>">
                <input type="hidden" name="userhash" value="<?php echo $APK['userhash']; ?>">
                <input type="hidden" name="apkhash" value="<?php echo $APK['apkhash']; ?>">
            </form>
            <ul class="apk_control_buttons">
                <li><button class="btn" name="btnDownloadApp" title="Download APP">Download</button></li>
                <?php 
                    // only show if user study not running or finished 
                    if($study_running){ 
                        ?><li><button class="btn btnUpdateSurveyOnly" title="Update APP">Update</button></li>
            <?php
                    }
                    
                    if($APK['ustudy_finished'] != 1 && !$study_running){ 
                        ?>
                        <li><button class="btn" name="btnUpdateStudy" title="Update APP">Update</button></li>
                        <?php
                    }
                        
            if($APK['ustudy_finished'] == 1){
                ?>
                <li><button class="btn btnSurveyResultsExportCsv" title="Survey results" value="<?php echo $survey['survey_id']; ?>">Results to CSV</button></li>
                <?php
            }
            
            if($APK['ustudy_finished'] == 1 && !$study_running){
            ?>
                <li><button class="btn btn-danger confirm-delete" title="Remove study" value="<?php echo $APK['apkid']; ?>">Remove</button></li>
                <?php
            }
                ?>
            </ul>
      </div>
    </div>
  </div>
  <?php
       }
   ?>
</div>