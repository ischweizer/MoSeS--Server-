<?php

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] <= 1){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/"); 
    exit;
}
                              
?><h2>Create user study</h2>
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
            <select name="android_version_select">
              <?php
                 for($i=1; $i<=getAllAPIsCount(); $i++){ 
              ?>
              <option<?php echo($i == 14 ? ' selected="selected"' : ''); ?> value="<?php echo $i; ?>"><?php echo getAPILevel($i); ?></option>
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
            <textarea rows="3" cols="20" name="description" placeholder="Add here some description about the study"></textarea>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Number of invitations:</label>
        <div class="controls">
            <input type="number" name="max_devices_number" maxlength="10" placeholder="Number" value="10" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label"></label>
        <div class="controls">
            <label><input type="checkbox" name="setup_types" checked="checked">Publish to MoSeS</label>
        </div>
    </div>
    <?php
        if(!empty($USER_RGROUP)){
    ?>
    <div class="control-group">
        <label class="control-label"></label>
        <div class="controls">
            <label><input type="checkbox" name="private">Make visible only to my group</label>
        </div>
    </div>
    <?php
        }
     ?>
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
            <button class="btn btn-success btnCreateOK" title="Upload changes">Create study</button>
        </div>
    </div>
</fieldset>
<hr><button class="btn" name="btnAddSurvey" value="" style="float: right;"><i class="icon-plus-sign"></i> Add survey</button>
   <?php
   include_once('./include/_survey.php');        
   ?>
<input name="study_create" type="hidden" value="2975">
</form>