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
   if(isset($_GET['remove'])){
    
       $RAW_REMOVE_HASH = trim($_GET['remove']);
       
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
              
                    echo 'Study version: '. $APK['apk_version'] .' <br>';
                    echo 'Lowest Android version: '. getAPILevel($APK['androidversion']) .' <br>';
                    echo 'Start: '. $startDate .' <br>';
                    echo 'End: '. $endDate .' <br>';
                    echo 'Description: '. $APK['description'] .' <br>';
                    echo 'This study marked as <strong>'. ($APK['locked'] == 1 ? 'private' : 'public') .'</strong> <br>';
                    
                    switch($APK['inviteinstall']){
                        case '1': echo 'Joining is allowed for invited users. <br>';
                                break;
                        case '2': echo 'Joining is allowd from all invited users that installed '. $APK['apktitle'] .'. <br>'; 
                                break;
                        case '3': echo 'Joining is allowed from all users that installed '. $APK['apktitle'] .'. <br>';
                                break;
                        default: echo 'Something went wrong with retrieving invite install! <br>';
                    }
                    
                    echo 'Max number of participating devices: '. $APK['maxdevice'] .' <br>';
                    echo $joinedDevices .' <br>';
                    echo 'Selected quests: <br>';
                    echo '<ul>';
                    foreach($APK_QUESTIONS[$APK['apkid']] as $quests){
                       echo '<li>'. $quests .'</li>'; 
                    }
                    echo '</ul>';
                ?>  
              </div>
            </div>
          </div>
          <?php
               }
           ?>
        </div>   
    </div>
    <!-- / Main Block -->
    
    <hr>

 <?php

//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");

?>