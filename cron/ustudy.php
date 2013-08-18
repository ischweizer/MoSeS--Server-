
<?php

// this is a cronjob used for user studies

/* ustudy_finished encodings
 * -1  update
* 0  user-study
* 1  finished
*/

// get all apks
include_once('/home/dasense/moses/config.php');
// 	include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");
include_once (MOSES_HOME."/include/managers/ApkManager.php");
// 	include(MOSES_HOME."/cron/survey.php");

// 	$logger->logInfo(" ###################### STARTED USER STUDY CRONJOB ############################## ");

$sql = "SELECT * FROM " .$CONFIG['DB_TABLE']['APK']. " WHERE ustudy_finished < 1";

$result = $db->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

// iterate over all apkids
foreach($rows as $row)
{

	$apkID = $row['apkid'];

	$isInviteInstall = $row['inviteinstall']==1;
	$isPrivate = $row['private']==1;
	$startDate = $row['startdate'];
	$endDate = $row['enddate'];
	$restrictionDeviceNumber = $row['Big test 2'];
	$runningTime = $row['runningtime'];
	$participatedCount = $row['participated_count'];
	$timeEnoughParticipants = $row['time_enough_participants'];
	$startCriterion = $row['startcriterion'];

	if(!$isPrivate && !$isInviteInstall){
		// no need to send apk notifications, just look if the ustudy should be finished
			
		if(!empty($endDate)){
			// it should be finished if the endDate has passed
			if(time() >= strtotime($endDate)){
				ApkManager::markUserStudyAsFinished($db, $CONFIG['DB_TABLE']['APK'], $apkID, $logger);
			}
		}
		else{
			if($participatedCount >= $startCriterion){
				// we have enough devices, check the timestamp
				if(empty($timeEnoughParticipants)){
					// just insert a timestamp and do nothing more
					ApkManager::insertTimestampToTimeEnoughParticipants($db, $CONFIG['DB_TABLE']['APK'], $apkID);
				}
				else{
					//a timestamp already exists, look if enough time has passed in order to mark the study as finished
					$currentTime = time();
					if($currentTime - $timeEnoughParticipants >= $runningTime*60)
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
		// second case, ustudy is private or it is inviteinstall
	}

}


// 		$logger->logInfo("This apk ( ".$row['apktitle']." ) isn't finished yet and let more users to install it");
// 	    // new round for every apk that is not installed on the required number of devices
// 	    $RESTRICTION_USER_NUMBER = $row['restriction_device_number'];
// 	    $PARTICIPATED_COUNT = $row['participated_count'];
// 	    $last_round_time = $row['last_round_time']; // the last time a round on this apk has been made
// 	    if($PARTICIPATED_COUNT < $RESTRICTION_USER_NUMBER)
// 	    {
// 	        if(empty($last_round_time) || ($last_round_time >= $CONFIG['CRON']['STUDY_TIMEOUT']))
// 	        {
// 		        $TO_CHOOSE = $RESTRICTION_USER_NUMBER - $PARTICIPATED_COUNT; // number of new devices to be selected
// 		        $pending_devices = json_decode($row['pending_devices']); // users that have not installed the app
// 		        $notified_devices = json_decode($row['notified_devices']); // notified users
// 		        foreach($pending_devices as $pending_device){
// 		            $notified_devices[] = $pending_device; // move the device to notified devices
// 	        }

// 	        // select new users
// 	        $num_new_devices = $RESTRICTION_USER_NUMBER - $PARTICIPATED_COUNT;
// 	        $pending_devices = array(); // new users to send the notification to
// 	        $candidates = json_decode($row['candidates']);
// 	        for($i=0; $i<$num_new_devices; $i++)
// 	        {
// 	            if($i >= count($candidates))
// 	            {
// 	                break;
// 	            }
// 	           $pending_devices[] = $candidates[$i];
// 	        }

// 	        // remove the new devices from candidates list
// 	        if(count($candidates) > 0)
// 	        {
// 	            $candidates = array_slice($candidates, count($pending_devices));
// 	        }

// 	        $ustudyFinished = 0; // this value will be written back to apk database


// 	        // extracting c2dms for google
// 	        $targetDevices = array();

// 	        foreach($pending_devices as $pending_device)
// 	        {
// 	             $sql = "SELECT c2dm FROM hardware WHERE hwid=".$pending_device; // XXX Ibraim : I need it !
// 	             $result = $db->query($sql);
// 	             $row_hw = $result->fetch();
// 	             if(!empty($row_hw))
// 	             {
// 	                $targetDevices[] = $row_hw['c2dm'];
// 	             }
// 	        }


// 	        // UPDATE THE DATABASE
// 	        $pending_devices = json_encode($pending_devices);
// 	        $candidates = json_encode($candidates);
// 	        $notified_devices = json_encode($notified_devices);


// 	        $sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. "
// 	        SET pending_devices='".$pending_devices."' , candidates='".$candidates."' , notified_devices='".$notified_devices."' , last_round_time=".time().
// 	            ", ustudy_finished = ".$ustudyFinished. " WHERE apkid=".$row['apkid'];


// 	        $logger->logInfo(print_r("QUERY IN ustudy", true));
// 	        $logger->logInfo(print_r($sql, true));

// 	        $db->exec($sql);


// 	        $logger->logInfo(print_r($targetDevices, true));

// 	        // PUSH
// 	        include_once(MOSES_HOME."/include/managers/GooglePushManager.php");
// 	        if(count($targetDevices) > 0){
// 	            // user-study or just an update
// 	            switch($row['ustudy_finished']){
// 	                case -1 : GooglePushManager::googlePushSendUpdate($row['apkid'], $targetDevices, $logger, $CONFIG); break;
// 	                case 0 : GooglePushManager::googlePushSendUStudy($row['apkid'], $targetDevices, $logger, $CONFIG); break;
// 	                default : {
// 	                    // Enough devices have installed the APK. Just, mark the user study as finished
// 	                    $sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. " SET ustudy_finished=1 WHERE apkid=".$row['apkid'];
// 	                    $logger->logInfo(print_r($sql, true));
// 	                    $db->exec($sql);
// 	                }
// 	            }
// 	    }
// 	        }
// 	    }
// 	}

// 	$logger->logInfo(" ###################### USER STUDY CRONJOB FINISHED ############################## ");


?>