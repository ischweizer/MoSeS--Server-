<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN'])){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    exit;
}
    
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");

$CREATE = 0;

if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){

   if(isset($_GET['m']) && !empty($_GET['m']) && $_GET['m'] === 'new'){
     
      $CREATE = 1; 
   
   }else{ 
         
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
   }
   
   /**
   * Selecting standard survey's questions 
   */
   $SURVEYS = array();
   
   $i=1;
   $res = json_decode(getStandardSurveyById($i), true);
   
   while(!empty($res)){
       $SURVEYS[] = json_decode(getStandardSurveyById($i), true);
       $i++;
       $res = json_decode(getStandardSurveyById($i), true);
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
include_once("./include/_login.php");
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