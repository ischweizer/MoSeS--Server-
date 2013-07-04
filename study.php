<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");


if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){

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

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Studies</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <?php
                 if(empty($USER_APKS)){
                     
            ?><h2>No user study was created by you.</h2><?php
                     
                 }else{
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
                                <label class="control-label">Lowest Android version: </label>
                                <div class="controls">
                                    <div id="android_version"><?php echo getAPILevel($APK['androidversion']); ?></div>
                                    <select id="android_version_select" style="display: none;">
                                      <?php
                                         for($i=1; $i<=getAllAPIsCount(); $i++){ 
                                      ?>
                                      <option value="<?php echo $i; ?>"<?php if($APK['androidversion'] == $i) echo ' selected="selected"'; ?>><?php echo getAPILevel($i); ?></option>
                                      <?php
                                         }
                                      ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Start: </label>
                                <div class="controls">
                                    <div id="start_date"><?php echo $startDate; ?></div>
                                    <input type="text" name="start_date" id="dp1" maxlength="50" placeholder="Format: yyyy-mm-dd" value="<?php echo $startDate; ?>" style="display: none;" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">End: </label>
                                <div class="controls">
                                    <div id="end_date"><?php echo $endDate; ?></div>
                                    <input type="text" name="end_date" id="dp2" maxlength="50" placeholder="Format: yyyy-mm-dd" value="<?php echo $endDate; ?>" style="display: none;" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Description: </label>
                                <div class="controls">
                                    <div id="description"><?php echo $APK['description']; ?></div>
                                    <textarea rows="3" cols="20" style="display: none;"><?php echo $APK['description']; ?></textarea>
                                </div>
                            </div>
                            This study marked as <strong><?php echo $APK['locked'] == 1 ? 'private' : 'public'; ?>.</strong> <br>
                            <?php
                                switch($APK['inviteinstall']){
                                    case '1': echo 'Joining is allowed for invited users. <br>';
                                            break;
                                    case '2': echo 'Joining is allowd from all invited users that installed '. $APK['apktitle'] .'. <br>'; 
                                            break;
                                    case '3': echo 'Joining is allowed from all users that installed '. $APK['apktitle'] .'. <br>';
                                            break;
                                    default: echo 'Something went wrong with retrieving invite install! <br>';
                                }
                            ?>
                            <div class="control-group">
                                <label class="control-label">Max participating devices: </label>
                                <div class="controls">
                                    <div id="max_devices_number"><?php echo $APK['maxdevice']; ?></div>
                                    <input type="text" name="max_devices_number" maxlength="10" placeholder="Max devices" value="<?php echo $APK['maxdevice']; ?>" style="display: none;" />
                                </div>
                            </div>
                            <?php 
                                echo $joinedDevices; 
                            ?> <br>
                            <div class="control-group">
                                <label class="control-label">Selected quests: </label>
                                <div class="controls">
                                    <div id="quests"><?php 

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
                                    <select multiple="multiple" id="quests_select" style="display: none;">
                                      <?php
                                         foreach($ALL_QUESTS as $quest){
                                            foreach($APK_QUESTIONS[$APK['apkid']] as $need_to_seelect_quest){ 
                                      ?>
                                      <option value="<?php echo $quest['questid']; ?>"<?php 
                                            if($quest['name'] == $need_to_seelect_quest) 
                                                echo ' selected="selected"'; 
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
                            <div class="control-group" id="uploadFile" style="display: none;">
                                <label class="control-label">Select an APP: </label>
                                <div class="controls">
                                    <input type="file" name="file">
                                </div>
                            </div>
                            <progress style="display: none;"></progress><br>
                            <button class="btn btn-success" id="btnUpdateOK" style="display: none;">OK</button>
                        </fieldset>
                    </form>
                    <?php 
                    
                    echo '<ul class="apk_control_buttons">';
                    echo '<li><a href="./apk/'. $APK['userhash'] .'/'. $APK['apkhash'] .'.apk" title="Download APP" class="btn btn-success">Download</a></li>';
                    echo '<li><button class="btn btn-warning" id="btnUpdateStudy" title="Update APP">Update</button></li>';
                    
                    if($APK['ustudy_finished'] == 1){
                        echo '<li><a href="'. $_SERVER['PHP_SELF'] .'?m=usquest&id='. $APK['apkid'] .'" title="Result Of Questionnaire" class="btn btn-info">Results</a></li>';
                    }else{
                        echo '<li><a href="'. $_SERVER['PHP_SELF'] .'?m=addquest&id='. $APK['apkid'] .'" title="Add Questionnaire" class="btn btn-info">Add quest</a></li>';
                    }
                    
                    echo '<li><button class="btn btn-danger" title="Remove APP" id="btnRemoveStudy" value="'. $APK['apkhash'] .'">Remove</button></li>';
                    echo '</ul>';
                ?>  
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

$('#btnUpdateStudy').click(function(){
   $('#android_version').hide();
   $('#start_date').hide();
   $('#end_date').hide();
   $('#description').hide(); 
   $('#max_devices_number').hide();
   $('#quests').hide();  
                 
   $('#android_version_select').show();
   $('.controls :input').show();
   $('#quests_select').show();
   $('#uploadFile').show();
   $('progress').show();
   $('#btnUpdateOK').show();
});

$('#btnUpdateOK').click(function(){
   $('#android_version').show();
   $('#start_date').show();
   $('#end_date').show();
   $('#description').show(); 
   $('#max_devices_number').show();
   $('#quests').show();  
                 
   $('#android_version_select').hide();
   $('.controls :input').hide();
   $('#quests_select').hide();
   $('#uploadFile').hide();
   $('#btnUpdateOK').hide(); 
});

$('#dp1').datepicker({
  format: 'yyyy-mm-dd'
});

$('#dp2').datepicker({
  format: 'yyyy-mm-dd'
});

$(':file').change(function(){
    var file = this.files[0];
    name = file.name;
    size = file.size;
    type = file.type;
                               
    /*if(type.toLowerCase() != 'apk')
        alert("Only *.apk files are accepted!");
        */
});

$('#btnUpdateOK').click(function(e){
    
    var formData = new FormData($('form')[0]);
    $.ajax({
        url: 'upload.php',  //server script to process data
        type: 'POST',
        xhr: function() {  // custom xhr
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){ // check if upload property exists
                myXhr.upload.addEventListener('progress', function(e) {
                                                                console.log("progress Handling Function");
                                                                if(e.lengthComputable){
                                                                    $('progress').attr({value:e.loaded,max:e.total});
                                                                }
                                                            }, false); // for handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
        //beforeSend: beforeSendHandler,
        success: function(){
            $('progress').hide();
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

$('#btnRemoveStudy').click(function(e){
   
    if(confirm('Are you sure want to remove this APP?')){
        // removing APK
        $.post("study.php", { 'remove': $('#btnRemoveStudy').val() })
            .done(function() {
              location.reload();
              });
    }
    
   e.preventDefault(); 
});

</script>