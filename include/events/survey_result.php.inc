<?php

$logger->logInfo("############## SURVEY_RESULT ARRIVED ###############");
$logger->logInfo(print_r($data, true));

if($data->SESSIONID != null){
	$sessionID = $data->SESSIONID;
	$logger->logInfo("SESSIONID PROVIDED BY THE CLIENT: ".$sessionID );

	include_once(MOSES_HOME . '/include/functions/dbconnect.php');
	$userid = LoginManager::getLoggedInUser($db, $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
	if($userid != null){
		foreach($data as $key => $value){
			if($key != "SESSIONID" && $key != "MESSAGE"){
				$questionID = $key;
				$answer = $value;
				$temp = SurveyManager::getQuestionInformation($logger, $db, $CONFIG, $questionID);
				$formID = $temp['formid'];
				$surveyID = $temp['surveyid'];
				$logger->logInfo("survey_result.php.inc temp=".print_r($temp, true));
				SurveyManager::insertAnswer($logger, $db, $CONFIG['DB_TABLE']['STUDY_RESULT'], $surveyID, $formID, $questionID, $answer);
			}
		}

		$return = array(
				"MESSAGE" => "SURVEY_RESULT_RESPONSE",
				"STATUS" => "SUCCESS");
	}
	else{
		$return = array(
				"MESSAGE" => "SURVEY_RESULT_RESPONSE",
				"STATUS" => "INVALID_USER");
	}

}
else{
	$return = array(
			"MESSAGE" => "SURVEY_RESULT_RESPONSE",
			"STATUS" => "INVALID_SESSION");
}


print(json_encode($return));

?>
