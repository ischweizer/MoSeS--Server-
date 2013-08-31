<?php

/*
 * @author: Wladimir Schmidt
 */

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN'])){
    header("Location: " . dirname($_SERVER['PHP_SELF']));   
    exit;
}
    
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");

$CREATE = 0;

if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){

   /* taking group name from user */
   $sql = "SELECT rgroup 
           FROM ". $CONFIG['DB_TABLE']['USER'] ." 
           WHERE userid = ". $_SESSION["USER_ID"];
            
   $result = $db->query($sql);
   $row = $result->fetch(PDO::FETCH_ASSOC);
   
   $USER_RGROUP = (!empty($row['rgroup']) ? $row['rgroup'] : ''); 
    
   if(isset($_GET['m']) && !empty($_GET['m']) && $_GET['m'] === 'new'){
     
      $CREATE = 1; 
   
   }else{ 
         
       // select all information from apk table by user id
       $sql = "SELECT * 
               FROM ". $CONFIG['DB_TABLE']['APK'] ." 
               WHERE userid = ". $_SESSION["USER_ID"];
                
       $result = $db->query($sql);
       $USER_APKS = $result->fetchAll(PDO::FETCH_ASSOC);
       
       /**
       * Selecting user survey  if any created
       */
       
       $SURVEY_BY_APK_ID = array();
       
       foreach($USER_APKS as $apk){
       
           /*
           *  Build up a survey array
           */
           
           $sql = "SELECT surveyid 
                   FROM ". $CONFIG['DB_TABLE']['STUDY_SURVEY'] ." 
                   WHERE userid = ". $_SESSION["USER_ID"] ." AND apkid = ". $apk['apkid'];
                    
           $result = $db->query($sql);
           $survey = $result->fetch(PDO::FETCH_ASSOC);
           
           // user study got a survey
           if(!empty($survey)){
           
               $sql = "SELECT title, formid 
                       FROM ". $CONFIG['DB_TABLE']['STUDY_FORM'] ." 
                       WHERE surveyid = ". $survey['surveyid'];
                        
               $result = $db->query($sql);
               $forms = $result->fetchAll(PDO::FETCH_ASSOC);
               
               $forms_array = array();
               foreach($forms as $f){
                  
                  $sql = "SELECT questionid, type, text, mandatory  
                          FROM ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                          WHERE formid = ". $f['formid'];
                            
                  $result = $db->query($sql);
                  $questions = $result->fetchAll(PDO::FETCH_ASSOC);
                  
                  $questions_array = array();
                  foreach($questions as $q){
                      
                      $sql = "SELECT text  
                              FROM ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                              WHERE questionid = ". $q['questionid'];
                                
                      $result = $db->query($sql);
                      $answers = $result->fetchAll(PDO::FETCH_ASSOC);
                      
                      $answers_array = array();
                      foreach($answers as $a){
                          $answers_array[] = $a['text'];
                      }
                      
                      $questions_array[] = array('question_type' => $q['type'],
                                                 'question_mandatory' => $q['mandatory'],
                                                 'question' => $q['text'],
                                                 'answers' => $answers_array);
                  }
                   
                  $forms_array[] = array('form_id' => $f['formid'],
                                         'form_title' => $f['title'],
                                         'questions' => $questions_array);
               }
               
               $SURVEY_BY_APK_ID[$apk['apkid']] = array('survey_id' => $survey['surveyid'],
                                                           'forms' => $forms_array);
           }else{
               // user study got NO survey created by user
               $SURVEY_BY_APK_ID[$apk['apkid']] = array();
           }
       } 
       
       /**
       * *********************************************
       */
   }
   
   /**
   * Selecting standard form's questions 
   */
   $FORMS = array();
   
   $i=1;
   $res = json_decode(getStandardSurveyById($i), true);
   
   while(!empty($res)){
       // forming array with standard surveys
       $FORMS[] = json_decode(getStandardSurveyById($i), true);
       $i++;
       $res = json_decode(getStandardSurveyById($i), true);
   }
   
   /**
   * ************************************************
   */ 
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>The Mobile Sensing System - User study</title>

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
<script src="js/jquery-ui-1.10.3.custom.min.js"></script>
<?php

/* COMMONS */
?>
<script src="js/study-common.js"></script>
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
?>