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
    
       // getting survey results  
       if(isset($_POST['USQUEST']) && !empty($_POST['USQUEST']))
        {
            $apkid = preg_replace("/\D/", "", $_POST['USQUEST']);
            $show_us_quest = true;

            $sql = "SELECT apktitle 
                    FROM ". $CONFIG['DB_TABLE']['APK'] ." 
                    WHERE apkid = ".$apkid;
                    
            $req = $db->query($sql);
            $row = $req->fetch();
            
            $apkname = $row['apktitle'];
            
            include_once("./include/managers/SurveyManager.php");
            
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
         
       /* taking group name from user */
       $sql = "SELECT rgroup 
               FROM ". $CONFIG['DB_TABLE']['USER'] ." 
               WHERE userid = ". $_SESSION["USER_ID"];
                
       $result = $db->query($sql);
       $row = $result->fetch(PDO::FETCH_ASSOC);
       
       $USER_RGROUP = (!empty($row['rgroup']) ? $row['rgroup'] : ''); 
       
       // select all entries from apk table for user
       $sql = "SELECT * 
               FROM ". $CONFIG['DB_TABLE']['APK'] ." 
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
   
   /*
   *    Select all survey names, ids for dropdown list  
   */
   $SURVEYS_ALL = array();
   
   $sql = 'SELECT * 
           FROM `'. $CONFIG['DB_TABLE']['QUEST'] .'`';
            
   $result=$db->query($sql);
   $SURVEYS_ALL = $result->fetchAll(PDO::FETCH_ASSOC);
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
        
        // user isn't scientiest or admin
        if(!isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] < 2 ){
           ?>
           <h2 class="text-center">You must be a scientist to have access here.</h2>
           <?php 
        }else
        
        /**
        * CREATE STUDY/UPLOAD APK FORM
        */
        if(isset($CREATE) && $CREATE == 1){
        
            include_once("./include/_study-create.php"); 
        
        }else
            if(empty($USER_APKS)){
        /*
        * EMPTY USER APK/STUDY LIST
        */
                     
        ?><h2 class="text-center">No user study was created by you.</h2><?php
                     
        }else{
            /**
            * POPULATE USER APK LIST, EDIT FUNCTIONS, REMOVE ETC
            */
        
            include_once("./include/_study-view-update.php");
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
<?php

/* UPDATE AND VIEW PAGE */
if($CREATE == 0){
?>
<script src="js/study-view-update.js"></script>
<?php
}else{    
/* CREATE STUDY PAGE JS */
?>
<script src="js/study-create.js"></script>
<?php
}
/* COMMONS */
?>
<script src="js/study-common.js"></script>