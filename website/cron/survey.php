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

/**
 * @author: Zijad Maksuti
 */

 // Cronjob for sending notifications about available surveys to client when 
// user study is finished

/* ustudy_finished encodings
* 0  user-study
* 1  finished
* 2 definitely finished and devices have been notified about a survey (if any)
*/

// get all apks
include_once('/home/dasense/moses/config.php');
include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");
include_once (MOSES_HOME."/include/managers/ApkManager.php");
include_once (MOSES_HOME."/include/managers/LoginManager.php");
include_once (MOSES_HOME."/include/managers/HardwareManager.php");
include_once (MOSES_HOME."/include/managers/GooglePushManager.php");
include_once (MOSES_HOME."/include/managers/SurveyManager.php");

$logger->logInfo(" ###################### SURVEY CRONJOB START ############################## ");

// Select all user studies that are finished
$sql = "SELECT * FROM " .$CONFIG['DB_TABLE']['APK']. " WHERE ustudy_finished = 1";
$result = $db->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

// iterate over all user studies
foreach($rows as $row)
{
	// Get Apkid
	$APK_ID = $row['apkid'];
	$logger->logInfo("survey.php APK_ID=".$APK_ID);

	// check if the user study has a survey attached, if so send notifications to all
	// devices that have installed
	if(SurveyManager::hasSurvey($logger, $db, $CONFIG['DB_TABLE']['STUDY_SURVEY'], $APK_ID)){
		// the user study has a survey, send notification to devices that have participated on the survey
		$participatedDevices = ApkManager::getParticipatedDevices($db, $CONFIG['DB_TABLE']['APK'], $APK_ID, $logger);
		if(!empty($participatedDevices)){
			// we have some devices, send dem notifications
			GooglePushManager::sendSurveyAvailableToHardware($db, $APK_ID, $participatedDevices, $logger, $CONFIG);
		}
	}
	// regardless if the apk had a survey or not, mark it as definitely finished
	ApkManager::markUserStudyAsDefinitelyFinished($db, $CONFIG['DB_TABLE']['APK'], $APK_ID, $logger);
	
}
$logger->logInfo(" ###################### SURVEY CRONJOB FINISHED ############################## ");
// strat the script for sending of updates
include_once(MOSES_HOME."/cron/update.php");
?>
