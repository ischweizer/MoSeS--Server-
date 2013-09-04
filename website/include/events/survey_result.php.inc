<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Zijad Maksuti
 */

$logger->logInfo("############## SURVEY_RESULT ARRIVED ###############");
$logger->logInfo(print_r($data, true));

if($data->SESSIONID != null){
	$sessionID = $data->SESSIONID;
	$logger->logInfo("SESSIONID PROVIDED BY THE CLIENT: ".$sessionID );

	include_once(MOSES_HOME . '/include/functions/dbconnect.php');
	$userid = LoginManager::getLoggedInUser($db, $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
    $apkid = $data->APKID;
	if($userid != null){
		foreach($data as $key => $value){
			if($key != "SESSIONID" && $key != "MESSAGE" && $key != "APKID"){
				$questionID = $key;
				$answer = $value;
				$temp = SurveyManager::getQuestionInformation($logger, $db, $CONFIG, $questionID);
				$formID = $temp['formid'];
				$surveyID = $temp['surveyid'];
				$logger->logInfo("survey_result.php.inc temp=".print_r($temp, true));
				SurveyManager::insertAnswer($logger, $db, $CONFIG['DB_TABLE']['STUDY_RESULT'], $surveyID, $formID, $questionID, $answer);
			}
		}
        
        ApkManager::incrementSurveySendCounter($db, $CONFIG['DB_TABLE']['APK'], $apkid, $logger);

		$return = array(
				"MESSAGE" => "SURVEY_RESULT_RESPONSE",
				"STATUS" => "SUCCESS");
	}
	else{
		$return = array(
				"MESSAGE" => "SURVEY_RESULT_RESPONSE",
				"STATUS" => "INVALID_SESSION");
	}

}
else{
	$return = array(
			"MESSAGE" => "SURVEY_RESULT_RESPONSE",
			"STATUS" => "INVALID_SESSION");
}

echo json_encode($return);
?>
