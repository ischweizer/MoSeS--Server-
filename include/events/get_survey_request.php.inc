<?php

$logger->logInfo("############## QUESTIONNAIRES REQUEST ARRIVED ###############");
$logger->logInfo("SESSIONID PROVIDED BY THE CLIENT: ".$data->SESSIONID);

$sessionID = '';

/**
*  SESSIONID check
*/
$apkid = $data->APKID;
if($data->SESSIONID != null)
{
  if(is_md5($data->SESSIONID))
  {
    $sessionID = $data->SESSIONID;
    if($apkid != null && !empty($apkid))
    {
      $logger->logInfo("questionnaries_request.php.inc APKID PROVIDED BY THE CLIENT: ".$apkid);
      $apk = ApkManager::getApk($db, $CONFIG['DB_TABLE']['APK'], $apkid, $logger);
      $survey = SurveyManager::getSurvey($logger, $db, $CONFIG['DB_TABLE']['STUDY_SURVEY'], $apkid);
      
      if(!empty($survey) && $apk['ustudy_finished'] == 2){
          
        $return = array(
              "MESSAGE" => "GET_SURVEY_RESPONSE",
              "STATUS" => "SUCCESS",
              "APKID" => $apkid);
          
        $logger->logInfo("##################### SURVEY REQUEST ################ SURVEY FOUND");
        
        // RECURSIVELY BUILD THE ANSWER CONTAINING THE SURVEY
        
        
        // =========== SETTING SURVEY INFORMATION ================
        $surveyid = $survey['surveyid'];
        $return["SURVEY"] = array("SURVEY_ID" => $surveyid, "TITLE" => "DUMMY TITLE");
        // =========== END SETTING SURVEY END ================
        
        // =========== SETTING FORMS INFORMATION ================
        $forms = SurveyManager::getForms($db, $CONFIG['DB_TABLE']['STUDY_FORM'], $surveyid);
        foreach($forms as $form){
        	$formID = $form['formid'];
        	$formTitle = $form['title'];
        	$return["SURVEY"][$formID] = array("FORM_TITLE" => $formTitle);
        	
        	// =========== SETTING QUESTIONS INFORMATION ================
        	$questions = SurveyManager::getQuestions($db, $CONFIG['DB_TABLE']['STUDY_QUESTION'], $formID);
        	foreach($questions as $question){
        		$questionID = $question['questionid'];
        		$questionType = $question['type'];
        		$questionTitle = $question['text'];
                $questionMandatory = $question['mandatory'];
        		$return["SURVEY"][$formID][$questionID] = array("QUESTION_TYPE" => $questionType, 
                                                                "QUESTION_TITLE" => $questionTitle,
                                                                "QUESTION_MANDATORY" => $questionMandatory);
        		
        		// =========== SETTING POSSIBLE ANSWER INFORMATION ================
        		$possibleAnswers = SurveyManager::getPossibleAnswers($db, $CONFIG['DB_TABLE']['STUDY_ANSWER'], $questionID);
        		foreach($possibleAnswers as $possibleAnswer){
        			$possibleAnswerID = $possibleAnswer['aid'];
        			$possibleAnswerTitle = $possibleAnswer['text'];
        			$return["SURVEY"][$formID][$questionID][$possibleAnswerID] = array("POSSIBLE_ANSWER_TITLE" => $possibleAnswerTitle);
        		}
        		// =========== END SETTING POSSIBLE ANSWER INFORMATION END ================
        		
        	}
        	// =========== END SETTING FORMS INFORMATION END ================
        	
        }
        // =========== END SETTING FORMS INFORMATION END ================
      }
      else
      {
        $return = array(
            "MESSAGE" => "GET_SURVEY_RESPONSE", 
            "STATUS" => "FAILURE_NO_QUESTIONNAIRE_FOUND",
        	"APKID" => $apkid
            );
      }
    }
    else
    {
      $return = array(
          "MESSAGE" => "GET_SURVEY_RESPONSE", 
          "STATUS" => "INVALID_APKID"
          );
    }
  }
  else
  {
    $return = array(
        "MESSAGE" => "GET_SURVEY_RESPONSE", 
        "STATUS" => "INVALID_SESSION",
    		"APKID" => $apkid
        );
  }
}
else
{
  $return = array(
      "MESSAGE" => "GET_SURVEY_RESPONSE", 
      "STATUS" => "INVALID_SESSION",
  	  "APKID" => $apkid
      );
} 

echo json_encode($return);
?>