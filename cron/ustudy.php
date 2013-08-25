
<?php

// this is a cronjob used for user studies

/* ustudy_finished encodings
* 0  user-study
* 1  finished
*/

include_once('/home/dasense/moses/config.php');
include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");
include_once (MOSES_HOME."/include/managers/ApkManager.php");
include_once (MOSES_HOME."/include/managers/LoginManager.php");
include_once (MOSES_HOME."/include/managers/HardwareManager.php");
include_once (MOSES_HOME."/include/managers/GooglePushManager.php");

$logger->logInfo(" ###################### STARTED USER STUDY CRONJOB ############################## ");

$sql = "SELECT * FROM " .$CONFIG['DB_TABLE']['APK']. " WHERE ustudy_finished < 1";

$result = $db->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

// iterate over all apkids
foreach($rows as $row){

	$apkID = $row['apkid'];
	$isInviteInstall = $row['inviteinstall']==1;
	$isPrivate = $row['private']==1;
	$startDate = $row['startdate'];
	$endDate = $row['enddate'];
	$restrictionDeviceNumber = $row['Big test 2'];
	$runningTime = $row['runningtime'];
	$timeEnoughParticipants = $row['time_enough_participants'];
	$startCriterion = $row['startcriterion'];
	$userdID = $row['userid'];
	$androidVersion = intval($row['androidversion']);
	$RESTRICTION_USER_NUMBER = $row['restriction_device_number'];
	$PARTICIPATED_COUNT = $row['participated_count'];
	$last_round_time = $row['last_round_time']; // the last time a round on this apk has been made

	if($isPrivate || (!$isPrivate && !$isInviteInstall)){
		// public ustudy, no need to send apk notifications, just look if the ustudy should be finished
			
		if(!empty($endDate)){
			// it should be finished if the endDate has passed
			if(time() >= strtotime($endDate)){
				ApkManager::markUserStudyAsFinished($db, $CONFIG['DB_TABLE']['APK'], $apkID, $logger);
			}
		}
		else{
			// enddate is empty, we have start criterion user study
			if($PARTICIPATED_COUNT >= $startCriterion){
				// we have enough devices, check the timestamp
				if(empty($timeEnoughParticipants)){
					// just insert a timestamp and do nothing more
					ApkManager::insertTimestampToTimeEnoughParticipants($db, $CONFIG['DB_TABLE']['APK'], $apkID);
				}
				else{
					//a timestamp already exists, look if enough time has passed in order to mark the study as finished
					$currentTime = time();
					if($currentTime - $timeEnoughParticipants >= $runningTime*60*60)
						// enough time has passed, mark the user study as finished
						ApkManager::markUserStudyAsFinished($db, $CONFIG['DB_TABLE']['APK'], $apkID, $logger);
						
				}
			}
			else{
				// not enough devices have installed it, do not do anything
			}


		}
	}
	else{
		//second case, ustudy is invite only
		if($isInviteInstall){
			// ========================= START INVITE ===================================================
				
			$logger->logInfo("ustudy.php the study is invite only, devices should be notified about it");
			if(!empty($endDate)){
				// it should be finished if the endDate has passed
				if(time() >= strtotime($endDate)){
					ApkManager::markUserStudyAsFinished($db, $CONFIG['DB_TABLE']['APK'], $apkID, $logger);
				}
				else{
					// end date has not been reached yet, do not mark the ustudy as finished
					if(time()>=strtotime($startDate)){
						// check if enough devices have installed the apk, if not, send some more notifications
						if($PARTICIPATED_COUNT < $RESTRICTION_USER_NUMBER){
							if(empty($last_round_time) || (time() - $last_round_time >= $CONFIG['CRON']['STUDY_TIMEOUT'])){
								$pending_devices = json_decode($row['pending_devices']); // users that have not installed the app
								$notified_devices = json_decode($row['notified_devices']); // notified users
								foreach($pending_devices as $pending_device){
									// move the device to notified devices
									// notified devices have waited too long to install the app
									$notified_devices[] = $pending_device;
								}
								// now look for some candidates
								$candidates = json_decode($row['candidates']);
								$num_new_devices = $RESTRICTION_USER_NUMBER - $PARTICIPATED_COUNT; // number of devices we need
								if(empty($candidates) || count($candidates)<$num_new_devices){
									// there are no candidates, it may be because the user study is being processed for the first time
									$potentialCandidatesRows = HardwareManager::getCandidatesForAndroid($db, $CONFIG['DB_TABLE']['HARDWARE'], $androidVersion, $logger);
									foreach($potentialCandidatesRows as $potentialCandidateRow){
										$potentialCandidateId = $potentialCandidateRow['hwid'];
										if(!in_array($potentialCandidateId, $pending_devices) && !in_array($potentialCandidateId, $notified_devices) &&
										!in_array($potentialCandidateId, $candidates))
											$candidates[]=$potentialCandidateId; // we have found a new candidate, yeah yeah yeah
									}
									/////////////////////////////////////////////////////////////////////////////
								}
								// now choose $num_new_devices out of the candidates list and send them notification
								$candidatesForSending = array();
								foreach($candidates as $candidate){
									if(count($candidatesForSending)==$num_new_devices)
										break;
									else
										$candidatesForSending[]=$candidate;
								}
								if(!empty($candidatesForSending)){
									// send the notifications
									GooglePushManager::googlePushSendUStudyToHardware($db, $apkID, $candidatesForSending, $logger, $CONFIG);
								}
								// UPDATE THE DATABASE
								$newCandidates = array();
								foreach($candidates as $oldCandidate){
									if(!in_array($oldCandidate, $candidatesForSending))
										$newCandidates[]=$oldCandidate;
								}
								$candidates = json_encode($newCandidates);
								$pending_devices = json_encode($candidatesForSending);
								$notified_devices = json_encode($notified_devices);
	
	
								$sql_update = "UPDATE " .$CONFIG['DB_TABLE']['APK']. "
										SET pending_devices='".$pending_devices."' , candidates='".$candidates."' , notified_devices='".$notified_devices."' , last_round_time=".time().
										" WHERE apkid=".$apkID;
	
								$logger->logInfo("ustudy.php updating database sql_update=".$sql_update);
	
								$db->exec($sql_update);
								// END UPDATE THE DATABASE END
							}
						}
						else{
							// enough devices have installed it, do not do anything, we are waiting for enddate to mark ustudy as finished
						}
					}
			}
			}
			else{
				// enddate is empty
				// the case where the ustudy waits for certain number of devices in order to get running
				$logger->logInfo("invitation study with start criterion for apkid=".$apkID);

				if($PARTICIPATED_COUNT >= $startCriterion){
					// we have enough devices, check the timestamp
					$logger->logInfo("invitation study with start criterion for we have enough devices, check the timestamp");
					if(empty($timeEnoughParticipants)){
						// just insert a timestamp and do nothing more
						$logger->logInfo("just insert a timestamp and do nothing more");
						ApkManager::insertTimestampToTimeEnoughParticipants($db, $CONFIG['DB_TABLE']['APK'], $apkID);
					}
					else{
						//a timestamp already exists, look if enough time has passed in order to mark the study as finished
						$logger->logInfo("a timestamp already exists, look if enough time has passed in order to mark the study as finished");
						$currentTime = time();
						if($currentTime - $timeEnoughParticipants >= $runningTime*60*60){
							// enough time has passed, mark the user study as finished
							$logger->logInfo("enough time has passed, mark the user study as finished");
							ApkManager::markUserStudyAsFinished($db, $CONFIG['DB_TABLE']['APK'], $apkID, $logger);
						}
							
					}
				}
				else{
					// not enough devices have installed it, invite them ////////////////////////////////////////////////////
					$logger->logInfo("not enough devices have installed it, invite them");
					if(empty($last_round_time) || (time() - $last_round_time >= $CONFIG['CRON']['STUDY_TIMEOUT'])){
						$logger->logInfo("empty(last_round_time) || (time() - last_round_time >= CONFIG['CRON']['STUDY_TIMEOUT'])==true");
						$pending_devices = json_decode($row['pending_devices']); // users that have not installed the app
						$notified_devices = json_decode($row['notified_devices']); // notified users
						foreach($pending_devices as $pending_device){
							// move the device to notified devices
							// notified devices have waited too long to install the app
							$notified_devices[] = $pending_device;
						}
						// now look for some candidates
						$candidates = json_decode($row['candidates']);
						$num_new_devices = $startCriterion - $PARTICIPATED_COUNT; // number of devices we need
						if(empty($candidates) || count($candidates)<$num_new_devices){
							// there are no candidates, it may be because the user study is being processed for the first time
							$logger->logInfo("there are no candidates, it may be because the user study is being processed for the first time");
							$potentialCandidatesRows = HardwareManager::getCandidatesForAndroid($db, $CONFIG['DB_TABLE']['HARDWARE'], $androidVersion, $logger);
							foreach($potentialCandidatesRows as $potentialCandidateRow){
								$potentialCandidateId = $potentialCandidateRow['hwid'];
								if(!in_array($potentialCandidateId, $pending_devices) && !in_array($potentialCandidateId, $notified_devices) &&
								!in_array($potentialCandidateId, $candidates))
									$candidates[]=$potentialCandidateId; // we have found a new candidate, yeah yeah yeah
							}
							/////////////////////////////////////////////////////////////////////////////
						}
						// now choose $num_new_devices out of the candidates list and send them notification
						$logger->logInfo("now choose num_new_devices out of the candidates list and send them notification");
						$candidatesForSending = array();
						foreach($candidates as $candidate){
							if(count($candidatesForSending)==$num_new_devices)
								break;
							else
								$candidatesForSending[]=$candidate;
						}
						if(!empty($candidatesForSending)){
							// send the notifications
							$logger->logInfo("send the notifications");
							GooglePushManager::googlePushSendUStudyToHardware($db, $apkID, $candidatesForSending, $logger, $CONFIG);
						}
						// UPDATE THE DATABASE
						$newCandidates = array();
						foreach($candidates as $oldCandidate){
							if(!in_array($oldCandidate, $candidatesForSending))
								$newCandidates[]=$oldCandidate;
						}
						$candidates = json_encode($newCandidates);
						$pending_devices = json_encode($candidatesForSending);
						$notified_devices = json_encode($notified_devices);


						$sql_update = "UPDATE " .$CONFIG['DB_TABLE']['APK']. "
								SET pending_devices='".$pending_devices."' , candidates='".$candidates."' , notified_devices='".$notified_devices."' , last_round_time=".time().
								" WHERE apkid=".$apkID;

						$logger->logInfo("ustudy.php updating database sql_update=".$sql_update);

						$db->exec($sql_update);
						// END UPDATE THE DATABASE END
					}




					///////////////////////////////////////////////////////////////////////
				}


					
					
			}
				
				
				
			// ========================= END INVITE ===================================================
				
		}
	}

}

$logger->logInfo(" ###################### USER STUDY CRONJOB FINISHED ############################## ");
// strat the script for sending of surveys
include_once(MOSES_HOME."/cron/survey.php");

?>