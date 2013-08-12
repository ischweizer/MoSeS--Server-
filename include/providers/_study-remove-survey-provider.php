<?php
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
    
$SURVEY_ID = trim($_POST['study_survey_remove']);

/* 
* Remove corresponding surveys and results 
* 
*/

// remove answers
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
                                 WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'] .')))';
                                 
$db->exec($survey_answers_sql);


// remove questions                    
$survey_questions_sql = 'DELETE 
                       FROM '. $CONFIG['DB_TABLE']['STUDY_QUESTION'] .' 
                       WHERE formid 
                       IN (SELECT formid 
                           FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                           WHERE surveyid 
                           IN (SELECT surveyid 
                               FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                               WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'] .'))';
                               
$db->exec($survey_questions_sql);
               
                       
// remove forms
$survey_forms_sql = 'DELETE 
                   FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                   WHERE surveyid 
                   IN (SELECT surveyid 
                       FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                       WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'] .')';
                       
$db->exec($survey_forms_sql);

                     
// remove surveys            
$survey_surveys_sql = 'DELETE 
                       FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                       WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'];
                     
$db->exec($survey_surveys_sql);


// remove survey results
$survey_results_sql = 'DELETE 
                       FROM '. $CONFIG['DB_TABLE']['STUDY_RESULT'] .' 
                       WHERE survey_id = '. $SURVEY_ID;
                     
$db->exec($survey_results_sql);

die('1');        
?>