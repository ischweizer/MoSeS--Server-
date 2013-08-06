<?php
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
    
$APK_ID = trim($_POST['study_remove']);

if(is_numeric($APK_ID)){
   
  // getting userhash and apkhash 
  $sql = "SELECT userhash, apkhash 
          FROM ". $CONFIG['DB_TABLE']['APK'] ." 
          WHERE userid = ". $_SESSION['USER_ID'] . " 
          AND apkid = ". $APK_ID;
  
  $result = $db->query($sql);
  $row = $result->fetch();
  
  if(!empty($row)){
      $dir = './apk/' . $row['userhash'];
      if(is_dir($dir)){
         if(file_exists($dir . '/'. $row['apkhash'] . '.apk')){
             unlink($dir . '/' . $row['apkhash'] . '.apk');
             
             if(is_empty_dir($dir)){
                 rmdir($dir);
             }
         }
      }
  }else{
      die('0');
  }
   
  // remove apk entry from DB 
  $sql = "DELETE 
          FROM ". $CONFIG['DB_TABLE']['APK'] ." 
          WHERE userid = ". $_SESSION['USER_ID'] . " AND apkid = ". $APK_ID;
  
  $db->exec($sql);
  
  /* 
   * Remove corresponding surveys 
   * 
   */
  
  // select answers
  $survey_answers_sql = 'DELETE 
                         FROM '. $CONFIG['DB_TABLE']['STUDY_ANSWER'] .' 
                         WHERE questionid 
                         IN (SELECT questionid 
                             FROM '. $CONFIG['DB_TABLE']['STUDY_QUESTION'] .' 
                             WHERE formid 
                             IN (SELECT formid 
                                 FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                                 WHERE surveyid 
                                 IN (SELECT surveyid 
                                     FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                                     WHERE userid = '. $_SESSION['USER_ID'] .' AND apkid = '. $APK_ID .')))';
                                     
  // remove answers
  $db->exec($survey_answers_sql);
                                    
  $survey_questions_sql = 'DELETE 
                           FROM '. $CONFIG['DB_TABLE']['STUDY_QUESTION'] .' 
                           WHERE formid 
                           IN (SELECT formid 
                               FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                               WHERE surveyid 
                               IN (SELECT surveyid 
                                   FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                                   WHERE userid = '. $_SESSION['USER_ID'] .' AND apkid = '. $APK_ID .'))';
                                   
  // remove questions
  $db->exec($survey_questions_sql);
                                     
  $survey_forms_sql = 'DELETE 
                       FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                       WHERE surveyid 
                       IN (SELECT surveyid 
                           FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                           WHERE userid = '. $_SESSION['USER_ID'] .' AND apkid = '. $APK_ID .')';
                           
  // remove forms
  $db->exec($survey_forms_sql);
                                     
  $survey_surveys_sql = 'DELETE 
                         FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                         WHERE userid = '. $_SESSION['USER_ID'] .' AND apkid = '. $APK_ID;
                         
  // remove surveys
  $db->exec($survey_surveys_sql);
  
  // success
  die('1'); 
}else{
   die('0');
}        
?>