<?php
    
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] <= 1){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    exit;
}
                    
?><h2>Studies</h2>
<br>     
<div class="accordion" id="accordionFather">
<?php
      
   for($i=0; $i<count($USER_APKS); $i++){
       
       $APK = $USER_APKS[$i];
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
                            <textarea rows="3" cols="20" name="description" placeholder="Add here some description about the study" style="display: none;"><?php echo $APK['description']; ?></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Number of invitations: </label>
                        <div class="controls">
                            <div name="max_devices_number_text"><?php echo $APK['restriction_device_number']; ?></div>
                            <input type="number" name="max_devices_number" maxlength="10" placeholder="Number" value="<?php echo $APK['restriction_device_number']; ?>" style="display: none;" />
                        </div>
                    </div>
                    <div name="allowed_join_text">
                    <?php
                        switch($APK['inviteinstall']){
                            case '1': echo 'Only invited people can see this study!';
                                    break;
                            case '2': echo 'This study is avalaible for everyone.'; 
                                    break;
                            default: echo 'Something went wrong!';
                        }
                    ?>
                    </div>
                    <div class="control-group" name="allowed_join" style="display: none;">
                        <label class="control-label"></label>
                        <div class="controls">
                            <label><input type="checkbox" name="setup_types" checked="checked">Publish to MoSeS</label>
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
                            <label><input type="checkbox" name="private" <?php echo ($APK['private'] == 1 ? 'checked="checked"' : ''); ?> />Make visible only to my group</label>
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
                <hr>
                <button class="btn" name="btnAddSurvey" value="" style="float: right; display: none;"><i class="icon-plus-sign"></i> Add survey</button>
                <?php
                    include_once('./include/_survey.php');        
                ?>
                <input type="hidden" name="study_update" value="6825">
                <input type="hidden" name="apk_id" value="<?php echo $APK['apkid']; ?>">
                <input type="hidden" name="userhash" value="<?php echo $APK['userhash']; ?>">
                <input type="hidden" name="apkhash" value="<?php echo $APK['apkhash']; ?>">
            </form>
            <ul class="apk_control_buttons">
                <li><button class="btn" name="btnDownloadApp" title="Download APP">Download</button></li>
                <li><button class="btn" name="btnUpdateStudy" title="Update APP">Update</button></li>
            <?php
            if($APK['ustudy_finished'] == 1){
                ?>
                <li><button class="btn" title="Survey results">Results</button></li>
                <?php
            }
            ?>
                <li><button class="btn btn-danger confirm-delete" title="Remove study" value="<?php echo $APK['apkid']; ?>">Remove</button></li>
            </ul>
      </div>
    </div>
  </div>
  <?php
       }
   ?>
</div>