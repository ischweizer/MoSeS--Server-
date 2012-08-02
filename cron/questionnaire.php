<?php
	
	// this is a cronjob used to send the questionnaires for each user studies that are finished
	
	/* ustudy_finished encodings
	* -1  update
	* 0  user-study
	* 1  finished
	*/
	
	// get all apks
	include_once(MOSES_HOME.'/config.php');
	include_once(MOSES_HOME."/include/functions/cronLogger.php");
	include_once(MOSES_HOME. "/include/functions/dbconnect.php");
	
	$logger->logInfo(" ###################### STARTED USER STUDY QUEST CRONJOB ############################## ");
	
	// Select all user studies that still running and check if they shoud be finished
	$sql = "SELECT * FROM " .$CONFIG['DB_TABLE']['APK']. " WHERE ustudy_finished < 1";
	$result = $db->query($sql);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);
	
	// iterate over all user studies
	foreach($rows as $row)
	{
		// Get Apkid
		$APK_ID = $row['apkid'];
		// print in the logger that we have an apk not finished
		$logger->logInfo(print_r("apkid = ".$APK_ID, true));
	    // Get the end date for this user study
	    $Enddate = $row['enddate'];
	    // print in the logger the end date
		$logger->logInfo(print_r("End date and time = ".$Enddate, true));
	    // Get the date of today
	    $actualTime = date("Y-m-d H:i:s");
	    // print in the logger the date and time for now
		$logger->logInfo(print_r("actual day and time = ".$actualTime, true));
	    // Compare between them.
	    $compareDates = (strtotime($actualTime) >= strtotime($Enddate)); // true if today > enddate, else false
	    if($compareDates)
	    {
	    	// Set user study as finished in DB
	        $sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. " SET ustudy_finished = 1 WHERE apkid=".$row['apkid'];
	        $db->exec($sql);

	        // Get the questionnaire of the user study
	        $quest = $row['idquest'];
	        // print in the logger which quest has this apk
			$logger->logInfo(print_r("its questid = ".$quest, true));
	        
	        // Get the questionnaire array of $quest
	        include_once(MOSES_HOME."/include/managers/QuestionnaireManager.php");
	        $quest_contents = QuestionnaireManager::getQuestionnaireArray($db, $CONFIG['DB_TABLE']['QUEST'], $quest);

	        // print in the logger which content has this quest
			$logger->logInfo(print_r("quest content = ".$quest_contents, true));

	        $recievers_hardware_ids = json_decode($row['installed_on']);
            if(count($recievers_hardware_ids)>0)
            {
            	foreach($recievers_hardware_ids as $hardware_id)
            	{
            		// print in the logger that we found this device
					$logger->logInfo(print_r("for device = ".$hardware_id, true));
					// Get the c2dm for this device
		            $sql = "SELECT c2dm FROM ".$CONFIG['DB_TABLE']['HARDWARE']." WHERE hwid=".$hardware_id;
		            $result = $db->query($sql);
		            $hwrow = $result->fetch();
		            $GOOGLE_C2DM_ID[] = $hwrow['c2dm'];

		            // print in the logger what the c2dm id has this device
					$logger->logInfo(print_r("c2dm = ".$GOOGLE_C2DM_ID[0], true));

		            include_once(MOSES_HOME."/include/managers/GooglePushManager.php");
		            GooglePushManager::googlePushSendQuest($APK_ID, $GOOGLE_C2DM_ID[0], $hardware_id, $logger, $CONFIG, $quest_contents);
           		}
           	}
           	else
           	{
           		// print in the logger there is no device runs this apk
				$logger->logInfo(print_r("but no device runs this apk", true));
           	}
	    }
	    else
	    {
	    	// print in the logger that the apk still running
			$logger->logInfo(print_r("but it still running", true));

	    }

	}
	$logger->logInfo(" ###################### USER STUDY QUEST CRONJOB FINISHED ############################## ");
?>