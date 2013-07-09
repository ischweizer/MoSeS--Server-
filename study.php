<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");

$CREATE = 0;

if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){

   if(isset($_GET['m']) && !empty($_GET['m']) && $_GET['m'] === 'new'){
     
      $CREATE = 1; 
   
   }else{ 
    
       // remove APK 
       if(isset($_POST['remove']) && !empty($_POST['remove'])){
           
           $RAW_REMOVE_HASH = trim($_POST['remove']);
           
           if(is_md5($RAW_REMOVE_HASH)){
               
              $APK_REMOVED = 1;
              $REMOVE_HASH = strtolower($RAW_REMOVE_HASH);
               
              // getting userhah for dir later
              $sql = "SELECT userhash 
                      FROM apk 
                      WHERE userid = ". $_SESSION['USER_ID'] . " 
                      AND apkhash = '". $REMOVE_HASH ."'";
              
              $result = $db->query($sql);
              $row = $result->fetch();
              
              if(!empty($row)){
                  $dir = './apk/' . $row['userhash'];
                  if(is_dir($dir)){
                     if(file_exists($dir . '/'. $REMOVE_HASH . '.apk')){
                         unlink($dir . '/' . $REMOVE_HASH . '.apk');
                         
                         if(is_empty_dir($dir)){
                             rmdir($dir);
                         }
                     }
                  }
              }
               
              // remove entry from DB 
              $sql = "DELETE FROM apk 
                             WHERE userid = ". $_SESSION['USER_ID'] . " 
                             AND apkhash = '". $REMOVE_HASH ."'";
              
              $db->exec($sql);
               
           }else{
               $APK_REMOVED = 0;
           }  
           
           die('success');
       }
         
       // getting quest results  
       if(isset($_POST['USQUEST']) && !empty($_POST['USQUEST']))
        {
            $apkid = preg_replace("/\D/", "", $_POST['USQUEST']);
            $show_us_quest = true;

            $sql = "SELECT apktitle FROM apk WHERE apkid = ".$apkid;
            $req = $db->query($sql);
            $row = $req->fetch();
            
            $apkname = $row['apktitle'];
            
            include_once("./include/managers/QuestionnaireManager.php");
            
            $notchosen_quests = QuestionnaireManager::getNotChosenQuestionnireForApkid(
                $db,
                $CONFIG['DB_TABLE']['QUEST'],
                $CONFIG['DB_TABLE']['APK_QUEST'],
                $apkid);
            
            $chosen_quests = QuestionnaireManager::getChosenQuestionnireForApkid(
                $db,
                $CONFIG['DB_TABLE']['QUEST'],
                $CONFIG['DB_TABLE']['APK_QUEST'],
                $apkid);
        }  
         
       // select all entries for particular user
       $sql = "SELECT * 
               FROM apk 
               WHERE userid = ". $_SESSION["USER_ID"];
                
       $result = $db->query($sql);
       $USER_APKS = $result->fetchAll(PDO::FETCH_ASSOC);
       
       /**
       * Selecting questions related to apk
       */
       $APK_QUESTIONS = array();
       
       foreach($USER_APKS as $APK){
          
           $sql ="SELECT questid 
                  FROM ". $CONFIG['DB_TABLE']['APK_QUEST'] ." 
                  WHERE apkid=" .$APK['apkid'];
            
            $req=$db->query($sql);
            $rows = $req->fetchAll(PDO::FETCH_ASSOC);
            
            if(!empty($rows)){
                
                $QUESTIONS = array();
                
                for($qi = 0; $qi < count($rows); $qi++){
                    
                    $sql ="SELECT name 
                            FROM ". $CONFIG['DB_TABLE']['QUEST'] ." 
                            WHERE questid=".$rows[$qi]['questid'];
                            
                    $req=$db->query($sql);
                    $us_quest = $req->fetch(PDO::FETCH_ASSOC);

                    $QUESTIONS[] = $us_quest['name'];
                }

                $APK_QUESTIONS[$APK['apkid']] = $QUESTIONS;
            }
       }    
       
       /*
       * Select all questions for the selection below
       */
       
       $sql = "SELECT * 
              FROM ". $CONFIG['DB_TABLE']['QUEST'];
            
        $result=$db->query($sql);
        $ALL_QUESTS = $result->fetchAll(PDO::FETCH_ASSOC);
   }
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Studies</title>

<?php  //Import of the menu
include_once("./include/_menu.php");
include_once("./include/_confirm.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <?php
        
        if($CREATE == 1){
        /**
        * CREATE STUDY/UPLOAD APK FORM
        */
        ?>
        <h2>Create user study</h2>
        <br>
        <form class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="createAPKForm">
            <fieldset>
                <div class="control-group">
                    <label class="control-label">Study name: </label>
                    <div class="controls">
                        <input type="text" name="apk_title" id="study_name" maxlength="50" placeholder="Study name" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Lowest Android version: </label>
                    <div class="controls">
                        <select id="android_version_select">
                          <?php
                             for($i=1; $i<=getAllAPIsCount(); $i++){ 
                          ?>
                          <option value="<?php echo $i; ?>"><?php echo getAPILevel($i); ?></option>
                          <?php
                             }
                          ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <label><input type="radio" name="study_period" value="1" checked="checked"> Study period from date to date</label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Start: </label>
                    <div class="controls">
                        <input type="text" name="start_date" id="dp1" maxlength="50" placeholder="yyyy-mm-dd" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">End: </label>
                    <div class="controls">
                        <input type="text" name="end_date" id="dp2" maxlength="50" placeholder="yyyy-mm-dd" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <label><input type="radio" name="study_period" value="2"> Study for minimum devices and running period</label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Minimum number of devices to start after: </label>
                    <div class="controls">
                        <input type="number" name="start_after_n_devices" maxlength="10" placeholder="Number" disabled="disabled" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Running period:</label>
                    <div class="controls">
                        <input type="text" name="running_time" maxlength="50" placeholder="Number" disabled="disabled" />
                        <select name="running_time_value" disabled="disabled">
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
                        <textarea rows="3" cols="20" name="description"></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Setup types</label>
                    <div class="controls">
                        <label><input type="radio" name="setup_types" value="1" /> Invite only</label>
                        <label><input type="radio" name="setup_types" value="2" checked="checked" /> Invite & Install (Default)</label>
                        <label><input type="radio" name="setup_types" value="3" /> Install only</label>
                    </div>
                </div>                
                <div class="control-group">
                    <label class="control-label">Max participating devices: </label>
                    <div class="controls">
                        <input type="number" name="max_devices_number" maxlength="10" placeholder="Max devices" />
                    </div>
                </div>
                <div class="control-group" name="uploadFile">
                    <label class="control-label">Select an APP: </label>
                    <div class="controls">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <progress name="progress" value="0" style="display: none;"></progress>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <button class="btn btn-success" name="btnCreateOK" title="Upload changes">Create study</button>
                    </div>
                </div>
            </fieldset>
            <input name="study_create" type="hidden" value="2975">
        </form>
        <?php 
         
        }elseif(empty($USER_APKS)){
        /**
        * EMPTY USER APK/STUDY LIST
        */
                     
        ?><h2 class="text-center">No user study was created by you.</h2><?php
                     
        }else{
        /**
        * POPULATE USER APK LIST, EDIT FUNCTIONS, REMOVE ETC
        */
        ?>
        <h2>Studies</h2>
        <br>     
        <div class="accordion" id="accordionFather">
        <?php
              
           for($i=0; $i<count($USER_APKS); $i++){
               
               $APK = $USER_APKS[$i];
        ?>
          <div class="accordion-group">
            <div class="accordion-heading">
              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionFather" href="#collapseStudies<?php echo $i; ?>">
                <?php
                   echo $APK['apktitle'].' (Filename: '. $APK['apkname'] .')'; 
                ?>
              </a>
            </div>
            <div id="collapseStudies<?php 
                echo $i;
                
                // for selected collapse use "collapse in" for class 
                ?>" class="accordion-body collapse">
              <div class="accordion-inner">
              <?php
                    $startCriterion = $APK['startcriterion'];
                    $startDate = (!empty($APK['startdate']) ? 
                                    $APK['startdate'] : 
                                    (!empty($startCriterion) ? 
                                    'Commencement after '. $startCriterion .' user'. 
                                    ($startCriterion > 1 ? 's' : '') .' join'. 
                                    ($startCriterion > 1 ? '' : 's') .'.' 
                                    : 'Commenced while creating '. $APK['apktitle'] .'.'));
                                    
                    $runningTime = $APK['runningtime'];
                    $endDate = (!empty($APK['enddate']) ? 
                                $APK['enddate'] : 
                                (!empty($runningTime) ? 
                                'The termination after '. $runningTime .' from the date of start.' :
                                'Terminated immediately after creating '. $APK['apktitle'] .'.')); 
                                
                    $joinedDevices = 'There '. ($APK['participated_count'] < 2 ? 'is' : 'are') .' '.
                                               ($APK['participated_count'] == 0 ? 'no' : $APK['participated_count']) .' '.
                                               ($APK['participated_count'] < 2 ? 'device' : 'devices') .' '.                           
                                                'currently joined to "'. $APK['apktitle'] .'".';
              
                    ?>
                    <form class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="updateAPKForm">
                        <fieldset>
                            Study ver. <?php echo $APK['apk_version']; ?> <br>
                            <div class="control-group">
                                <label class="control-label">Study name: </label>
                                <div class="controls">
                                    <div name="study_title_text"><?php echo $APK['apktitle']; ?></div>
                                    <input type="text" name="apk_title" value="<?php echo $APK['apktitle']; ?>" maxlength="50" placeholder="Study name" style="display: none;" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Lowest Android version: </label>
                                <div class="controls">
                                    <div name="android_version"><?php echo getAPILevel($APK['androidversion']); ?></div>
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
                                    <label><input type="radio" name="study_period" value="1" checked="checked"> Study period from date to date</label>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Start: </label>
                                <div class="controls">
                                    <div name="start_date_text"><?php echo $startDate; ?></div>
                                    <input type="text" name="start_date" maxlength="50" placeholder="yyyy-mm-dd" value="<?php echo $startDate; ?>" style="display: none;" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">End: </label>
                                <div class="controls">
                                    <div name="end_date_text"><?php echo $endDate; ?></div>
                                    <input type="text" name="end_date" maxlength="50" placeholder="yyyy-mm-dd" value="<?php echo $endDate; ?>" style="display: none;" />
                                </div>
                            </div>
                            <div class="control-group" name="study_period_text2" style="display: none;">
                                <label class="control-label"></label>
                                <div class="controls">
                                    <label><input type="radio" name="study_period" value="2"> Study for minimum devices and running period</label>
                                </div>
                            </div>
                            <div class="control-group" name="start_after_n_devices_text" style="display: none;">
                                <label class="control-label">Minimum number of devices to start after: </label>
                                <div class="controls">
                                    <input type="number" name="start_after_n_devices" maxlength="10" placeholder="Number" disabled="disabled" />
                                </div>
                            </div>
                            <div class="control-group" name="running_time_text" style="display: none;">
                                <label class="control-label">Running period:</label>
                                <div class="controls">
                                    <input type="text" name="running_time" maxlength="50" placeholder="Number" disabled="disabled" />
                                    <select name="running_time_value" disabled="disabled">
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
                                    <textarea rows="3" cols="20" name="description" style="display: none;"><?php echo $APK['description']; ?></textarea>
                                </div>
                            </div>
                            This study marked as <strong><?php echo $APK['locked'] == 1 ? 'private' : 'public'; ?>.</strong> <br>
                            <div name="allowed_join_text">
                            <?php
                                switch($APK['inviteinstall']){
                                    case '1': echo 'Joining is allowed for invited users.';
                                            break;
                                    case '2': echo 'Joining is allowd from all invited users that installed '. $APK['apktitle'] .'.'; 
                                            break;
                                    case '3': echo 'Joining is allowed from all users that installed '. $APK['apktitle'] .'.';
                                            break;
                                    default: echo 'Something went wrong with retrieving invite install!';
                                }
                            ?>
                            </div>
                            <div class="control-group" name="allowed_join" style="display: none;">
                                <label class="control-label">Setup types</label>
                                <div class="controls">
                                    <label><input type="radio" name="setup_types" value="1" <?php echo $APK['inviteinstall'] == 1 ? 'checked="checked"' : ''; ?> /> Invite only</label>
                                    <label><input type="radio" name="setup_types" value="2" <?php echo $APK['inviteinstall'] == 2 ? 'checked="checked"' : ''; ?> /> Invite & Install (Default)</label>
                                    <label><input type="radio" name="setup_types" value="3" <?php echo $APK['inviteinstall'] == 3 ? 'checked="checked"' : ''; ?> /> Install only</label>
                                </div>
                            </div> 
                            <div class="control-group">
                                <label class="control-label">Max participating devices: </label>
                                <div class="controls">
                                    <div name="max_devices_number_text"><?php echo $APK['maxdevice']; ?></div>
                                    <input type="number" name="max_devices_number" maxlength="10" placeholder="Max devices" value="<?php echo $APK['maxdevice']; ?>" style="display: none;" />
                                </div>
                            </div>
                            <?php 
                                echo $joinedDevices; 
                            ?> <br>
                            <div class="control-group">
                                <label class="control-label">Selected quests: </label>
                                <div class="controls">
                                    <div name="quests"><?php 

                                           if(!empty($APK_QUESTIONS[$APK['apkid']])){
                                                echo '<ul>';
                                                foreach($APK_QUESTIONS[$APK['apkid']] as $quests){
                                                   echo '<li>'. $quests .'</li>'; 
                                                }
                                                echo '</ul>';
                                            }else{
                                                echo 'No quests were selected for this study! <br>';
                                            }
                                            
                                     ?></div>
                                    <select multiple="multiple" name="quests_select" style="display: none;">
                                      <?php
                                                
                                         foreach($ALL_QUESTS as $quest){?>
                                             <option value="<?php echo $quest['questid']; ?>"<?php
                                             
                                             if(!empty($APK_QUESTIONS[$APK['apkid']])){
                                                foreach($APK_QUESTIONS[$APK['apkid']] as $need_to_seelect_quest){ 
                                          ?>
                                          <?php 
                                                if($quest['name'] == $need_to_seelect_quest) 
                                                    echo ' selected="selected"'; 
                                                }
                                             }
                                        ?>><?php 
                                            echo $quest['name']; 
                                            ?></option>
                                      <?php
                                         }                              
                                      ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group" name="uploadFile" style="display: none;">
                                <label class="control-label">Select an APP: </label>
                                <div class="controls">
                                    <input type="file" name="file">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls">
                                    <progress name="progress" value="0" style="display: none;"></progress>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls">
                                    <button class="btn" name="btnUpdateCancel" title="Cancel update!" style="display: none;">Cancel</button>
                                    <button class="btn btn-success" name="btnUpdateOK" title="Upload changes" style="display: none; margin-left: 20pt;">OK</button>
                                </div>
                            </div>
                        </fieldset>
                        <input type="hidden" name="study_update" value="6825">
                        <input type="hidden" name="apk_id" value="<?php echo $APK['apkid']; ?>">
                    </form>
                    <ul class="apk_control_buttons">
                        <li><a href="./apk/<?php echo $APK['userhash'] .'/'. $APK['apkhash']; ?>.apk" title="Download APP" class="btn">Download</a></li>
                        <li><button class="btn" name="btnUpdateStudy" title="Update APP">Update</button></li>
                    <?php
                    if($APK['ustudy_finished'] == 1){
                        ?>
                        <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?m=usquest&id=<?php echo $APK['apkid']; ?>" title="Result Of Questionnaire" class="btn">Results</a></li>
                        <?php
                    }else{
                        ?>
                        <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?m=addquest&id=<?php echo $APK['apkid']; ?>" title="Add Questionnaire" class="btn">Add quest</a></li>
                    <?php
                    }
                    ?>
                        <li><button class="btn btn-danger confirm-delete" title="Remove study" value="<?php echo $APK['apkhash']; ?>">Remove</button></li>
                    </ul>
              </div>
            </div>
          </div>
          <?php
               }
           ?>
        </div><?php
        }             
        ?>   
    </div>
    <!-- / Main Block -->
    
    <hr>

 <?php

//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");

?>
<script src="js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
<?php

/* UPDATE AND VIEW PAGE */
if($CREATE == 0){
?>
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
    p.find('[name="quests"]').hide();  
                 
    p.find('[name="android_version_select"]').show();
    p.find('.controls :input').show();
    p.find('[name="study_period_text1"]').show();
    p.find('[name="study_period_text2"]').show();
    p.find('[name="start_after_n_devices_text"]').show();
    p.find('[name="running_time_text"]').show();
    p.find('[name="description"]').show();
    p.find('[name="allowed_join"]').show();
    p.find('[name="quests_select"]').show();
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
            if(result){
                p.find('progress').hide();
                p.find('[name="btnUpdateStudy"]').attr('disabled',false);
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
   p.find('[name="quests"]').show();
   p.find('[name="progress"]').show();  
                 
   p.find('[name="android_version_select"]').hide();
   p.find('.controls :input').hide();
   p.find('[name="study_period_text1"]').hide();
   p.find('[name="study_period_text2"]').hide();
   p.find('[name="start_after_n_devices_text"]').hide();
   p.find('[name="running_time_text"]').hide();
   p.find('[name="description"]').hide();
   p.find('[name="allowed_join"]').hide();
   p.find('[name="quests_select"]').hide();
   p.find('[name="uploadFile"]').hide();
   p.find('[name="btnUpdateCancel"]').hide();
   p.find('[name="progress"]').hide();
   
   $(this).parent().parent().parent().parent().parent().find('[name="btnUpdateStudy"]').attr('disabled',false);
});

$('[name="study_period"]').click(function(){
    // get the parent of selected stuff
    var p = $(this).parent().parent().parent();
    var this1 = p.find('[name="study_period"]');
    if(this1.is(':checked')){
        if(this1.val() == 1){
            p.find('[name="start_date"]').attr('disabled', false);    
            p.find('[name="end_date"]').attr('disabled', false);
            
            p.find('[name="start_after_n_devices"]').attr('disabled', true);    
            p.find('[name="running_time"]').attr('disabled', true);
            p.find('[name="running_time_value"]').attr('disabled', true);
        }
        
        if(this1.val() == 2){
            p.find('[name="start_date"]').attr('disabled', true);    
            p.find('[name="end_date"]').attr('disabled', true);
            
            p.find('[name="start_after_n_devices"]').attr('disabled', false);    
            p.find('[name="running_time"]').attr('disabled', false);
            p.find('[name="running_time_value"]').attr('disabled', false);
        }
    }
});

<?php
}else{    
/* CREATE STUDY PAGE */
?>

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

$('#btnCreateOK').click(function(e){
   
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
            if(result){
                $('progress').hide();
                $('.hero-unit').html('<h3 class="text-center">You created a study <strong>'+ $('#study_name').val() +'</strong></h3>');
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

<?php
}
?>
/* Datepicker format */
$('[name="start_date"]').datepicker({
  format: 'yyyy-mm-dd'
});

$('[name="end_date"]').datepicker({
  format: 'yyyy-mm-dd'
});
/* ---------------- */

/*$(':file').change(function(){
    var file = this.files[0];
    name = file.name;
    size = file.size;
    type = file.type;
                               
}); */

</script>