<?php
	
	// TODO : this is a cronjob used to send the questionnaires for each user studies that are finished
	
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
		
		// Print in the logger that we have an apk not finished
		$logger->logInfo("apkid = ".$APK_ID, true);

		// Get start criterion of this user study
		$startcriterion = $row['startcriterion'];

		// Print in the logger the start criterion
		$logger->logInfo("Start criterion = ".$startcriterion, true);
		
		// Get start date of this user study
		$startdate = $row['startdate'];

		// Print in the logger the start date
		$logger->logInfo("Start date = ".$startdate, true);

		// Check the start date stills not been set
		if($startdate == NULL)
		{
			// Get the number of devices who install this apk
			$participated_count = $row['participated_count'];

			// Print in the logger the participated count
			$logger->logInfo("Participated count = ".$participated_count, true);

			// If number of devices >= start criterion
			if($participated_count >= $startcriterion)
			{
				// Set the start date as today
				$startdate = date("Y-m-d");

				// Print in the logger the new start date
				$logger->logInfo("Start date has been set = ".$startdate, true);

				// Update the db with this start date
				$sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. " SET startdate = '".$startdate."' WHERE apkid=".$row['apkid'];
				$logger->logInfo("sd : ".$sql, true);
				$db->exec($sql);
			}
		}

	    // Get the end date for this user study
	    $enddate = $row['enddate'];
	    
	    // Print in the logger the end date
		$logger->logInfo("End date = ".$enddate, true);
	    
	    // Get the date of today
	    $actualTime = date("Y-m-d");
	    
	    // Print in the logger the date and time for now
		$logger->logInfo("actual day and time = ".$actualTime, true);

		// Check if only end date stills not been set
		if($enddate == NULL && $startdate != NULL)
		{
			// Get running time of this user study
			$runningtime = $row['runningtime'];

			// Print in the logger the running time
			$logger->logInfo("Running time = ".$runningtime, true);

			$adding_array = explode("-", $runningtime);
			$addingday = intval($adding_array[2]);
			$addingmonth = intval($adding_array[1]);
			$addingyear = intval($adding_array[0]);

			// Print in the logger the adding date
			$logger->logInfo("addingday = ".$addingday." addingmonth = ".$addingmonth." addingyear = ".$addingyear, true);

			$enddate = strtotime($startdate);

			$enddate = date('Y-m-d', mktime(0,0,0,
				date('m',$enddate)+$addingmonth,
				date('d',$enddate)+$addingday,
				date('Y',$enddate)+$addingyear));

			// Print in the logger the new end date
			$logger->logInfo("End date has been set = ".$enddate, true);

			// Update the db with this end date
			$sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. " SET enddate = '".$enddate."' WHERE apkid = ".$row['apkid'];
			$logger->logInfo("ed : ".$sql, true);
			$db->exec($sql);
		}
		
		// Compare between them.
	    if($enddate != NULL && (strtotime($actualTime) >= strtotime($enddate))) // true if today > enddate, else false
	    {
	    	// Set user study as finished in DB
	        $sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. " SET ustudy_finished = 1 WHERE apkid = ".$row['apkid'];
	        $db->exec($sql);

	        // Get the questionnaire of the user study
	        include_once(MOSES_HOME.'/include/managers/SurveyManager.php');
	        include_once(MOSES_HOME . '/include/functions/dbconnect.php');
	        $quests = QuestionnaireManager::getChosenQuestionnireForApkid(
					$db,
					$CONFIG['DB_TABLE']['QUEST'],
					$CONFIG['DB_TABLE']['APK_QUEST'],
					$APK_ID);

	       	$sql = "SELECT *  FROM ".$CONFIG['DB_TABLE']['QUEST']." WHERE questid in (SELECT questid  FROM ".$CONFIG['DB_TABLE']['APK_QUEST']." WHERE apkid = ".$APK_ID.")";
	        $logger->logInfo("sql get questids =  ".$sql, true);
	        $result = $db->query($sql);
	        $quests = $result->fetchAll();
	        
	        // print in the logger which quests has this apk
			$logger->logInfo("its ".count($quests)." quests: ", true);
			$logger->logInfo(print_r($quests),true);

			$questsIds = array();
			foreach($quests as $quest)
			{
				$questsIds[] = $quest['questid'];
			}

			$questsIdsEncoded = json_encode($questsIds);

			// print in the logger the questids that will be sent
			$logger->logInfo("Notification content = ".$questsIdsEncoded, true);

	        $recievers_hardware_ids = json_decode($row['installed_on']);
	        // print in the logger which hardwares installed this apk
			$logger->logInfo("send to these hardwares = ".print_r($recievers_hardware_ids), true);
            if(count($recievers_hardware_ids)>0)
            {
            	foreach($recievers_hardware_ids as $hardware_id)
            	{
            		// print in the logger that we found this device
					$logger->logInfo("for device = ".$hardware_id, true);
					// Get the c2dm for this device
		            $sql = "SELECT c2dm FROM ".$CONFIG['DB_TABLE']['HARDWARE']." WHERE hwid=".$hardware_id;
		            $result = $db->query($sql);
		            $hwrow = $result->fetch();
		            $GOOGLE_C2DM_ID = $hwrow['c2dm'];

		            // print in the logger what the c2dm id has this device
					$logger->logInfo("c2dm = ".$GOOGLE_C2DM_ID, true);

		            include_once(MOSES_HOME."/include/managers/GooglePushManager.php");
		            GooglePushManager::googlePushSendQuest($APK_ID, $GOOGLE_C2DM_ID, $hardware_id, $logger, $CONFIG, $questsIdsEncoded);
           		}
           	}
           	else
           	{
           		// print in the logger there is no device runs this apk
				$logger->logInfo("but no device runs this apk", true);
           	}
	    }
	    else
	    {
	    	// print in the logger that the apk still running
			$logger->logInfo("but it still running", true);

	    }
		

	}
	$logger->logInfo(" ###################### USER STUDY QUEST CRONJOB FINISHED ############################## ");
?>