<?php

class ApkManager{

	public function __construct(){

	}

	/**
	 * Returns all APKs in DB
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 */
	public static function getAllApk($db, $apkTable){

		$sql = "SELECT *
				FROM ". $apkTable;

		$result = $db->query($sql);
		$array = $result->fetchAll(PDO::FETCH_ASSOC);

		return $array;
	}

	/**
	 * Returns all APKs in DB that are not published in a user-study
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 */
	public static function getNonStudyAllApk($db, $apkTable){

		$sql = "SELECT *
				FROM ". $apkTable ." WHERE private=0";

		$result = $db->query($sql);
		$array = $result->fetchAll(PDO::FETCH_ASSOC);

		return $array;
	}

	/**
	 * Returns all APKs in DB that are not published in a user-study and
	 * that can run on the specified android version.
	 *
	 * @param mixed $db the database instance
	 * @param mixed $apkTable the name of the apk table
	 * @param int $minAndroidVersion the minimal android version on which the apk should be runnable
	 * @return array an array containing all apks that meet the requirements. If no such apks exist, an empty array is returned.
	 */
	public static function getNonStudyAllApkRegardingMinAndroidVersion($db, $apkTable, $minAndroidVersion){

		$sql = "SELECT *
				FROM ". $apkTable ." WHERE private=0 AND ustudy_finished =0 AND androidversion<=".$minAndroidVersion;

		$result = $db->query($sql);
		$array = $result->fetchAll(PDO::FETCH_ASSOC);

		return $array;
	}

	/**
	 * Returns all APKs in DB that can run on the specified android version by the given user.
	 *
	 * @param mixed $db the database instance
	 * @param $userId the user id
	 * @param mixed $apkTable the name of the apk table
	 * @param int $minAndroidVersion the minimal android version on which the apk should be runnable
	 * @return array an array containing all apks that meet the requirements. If no such apks exist, an empty array is returned.
	 */
	public static function getAllApkRegardingMinAndroidVersion($logger, $db, $userId,$CONFIG, $minAndroidVersion){
		$sql = "SELECT *
				FROM ". $CONFIG['DB_TABLE']['APK'] ." WHERE private=0 AND ustudy_finished =0 AND androidversion<=".$minAndroidVersion;
		 
		$logger->logInfo("getAllApkRegardingMinAndroidVersion() sql=".$sql);

		$result = $db->query($sql);
		$array = $result->fetchAll(PDO::FETCH_ASSOC);
		 
		$groupName = LoginManager::getGroupName($logger, $db, $CONFIG['DB_TABLE']['USER'], $userId);
		 
		if(!empty($groupName)){
			$sqlGroupMembers = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']." WHERE name='".$groupName."'";
			$result2 = $db->query($sqlGroupMembers);
			$rowMembers = $result2->fetch(PDO::FETCH_ASSOC);
			if(!empty($rowMembers)){
				$members = json_decode($rowMembers['members']);
				foreach($members as $member){
					$sqlGetAPKsPublishedByUser = "SELECT *
							FROM ". $CONFIG['DB_TABLE']['APK'] ." WHERE private=1 AND ustudy_finished =0
									AND androidversion<=".$minAndroidVersion." AND userid=".$member;
					$result3 = $db->query($sqlGetAPKsPublishedByUser);
					$rowsAPKs = $result3->fetchAll(PDO::FETCH_ASSOC);
					$array = array_merge_recursive($array, $rowsAPKs);
				}
				 
			}
		}

		return $array;
	}


	/**
	 * Returns a particular APK.
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 * @param mixed $userID
	 * @param mixed $apkID
	 * @return mixed A row containing the apk or. If no apk with the provided apkID exists, an empty row is returned.
	 */
	public static function getApk($db, $apkTable, $apkID, $logger){

		$sql = "SELECT *
				FROM ". $apkTable ."
						WHERE apkid = ". intval($apkID);

		$result = $db->query($sql);
		$row = $result->fetch();
		 
		return $row;
	}

	/**
	 * Increments the number that tells how many times the apk has been downloaded
	 * returns true if the apk with the provided id exists, false otherwise
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 * @param mixed $apkID
	 */
	public static function incrementAPKUsage($db, $apkTable, $apkID, $hwid, $logger){
		$logger->logInfo("incrementAPKUsage");
		// checking if the apk exists
		$sql = "SELECT * FROM ". $apkTable ." WHERE apkid = ". intval($apkID);
		$result = $db->query($sql);
		$row = $result->fetch();
		if(empty($row)){
			return false;
		}
		else{
			$devs = $row['installed_on'];
			if(empty($devs)){
				$devs = array();
			}
			else{
				$devs = json_decode($devs);
			}
			$devs[] = intval($hwid);
			$devs = array_unique($devs);
			sort($devs);
			$part = count($devs);
			$devs=json_encode($devs);
			$sql1 = "UPDATE " .$apkTable. " SET participated_count=".$part." WHERE apkid= ".$apkID;
			$sql2 = "UPDATE ".$apkTable . " SET installed_on='".$devs."' WHERE apkid=".$apkID;
			$db->exec($sql1);
			$db->exec($sql2);
		}
		return true;
	}


	/**
	 * Decrements the number that tells how many times the apk has been downloaded
	 * returns true if the apk with the provided id exists, false otherwise
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 * @param mixed $apkID
	 */
	public static function decrementAPKUsage($db, $apkTable, $apkID, $hwid, $logger){

		$logger->logInfo("decrementAPKUsage");
		// checking if the apk exists

		$sql = "SELECT * FROM ". $apkTable ." WHERE apkid = ". intval($apkID);
		$result = $db->query($sql);
		$row = $result->fetch();
		$devs = $row['installed_on'];
		if(empty($devs)){
			return;
		}
		else{
			$devs = json_decode($devs);
		}
		$new_devs = array();
		foreach($devs as $hw_old){
			if($hw_old != $hwid)
				$new_devs[] = $hw_old;
		}
		$new_devs = array_unique($new_devs);
		sort($new_devs);
		$part = count($new_devs);
		$new_devs=json_encode($new_devs);
		$sql1 = "UPDATE " .$apkTable. " SET participated_count=".$part." WHERE apkid= ".$apkID;
		$sql2 = "UPDATE ".$apkTable . " SET installed_on='".$new_devs."' WHERE apkid=".$apkID;
		$db->exec($sql1);
		$db->exec($sql2);
	}



	/**
	 * retrives restriction number of the given apkID
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 * @param mixed $apkID
	 * @return mixed
	 */
	public static function getRestrictionUserNumber($db, $apkTable, $apkID){

		$sql = "SELECT restriction_user_number FROM " .$apkTable. " WHERE apkid= ".$apkID;

		$result = $db->query($sql);
		$row = $result->fetch();

		if(!empty($row))
			return $row['restriction_user_number'];

		return null;

	}

	/**
	 * returns true if the deviceID is in the list of pending devices (for user study)
	 *
	 * @param mixed $db
	 * @param mixed $apkTable
	 * @param mixed $apkID
	 * @param mixed $userID
	 */
	public static function isSelectedDevice($db, $apkTable, $apkID, $hardwareID){

		$sql = "SELECT pending_devices FROM " .$apkTable. " WHERE apkid= ".$apkID;

		$result = $db->query($sql);
		$row = $result->fetch();

		if(!empty($row)){
			$selectedDevices = explode(',', $row['pending_devices']);
			if(in_array($hardwareID, $selectedDevices))
				return true;
		}

		return false;

	}

	/**
	 * Markes the userstudy as finished
	 *
	 * @param mixed $db the database
	 * @param mixed $apkTable the table name
	 * @param mixed $apkID the id of the apk whose user study should be marked as finished
	 * @param mixed $logger the logger
	 */
	public static function markUserStudyAsFinished($db, $apkTable, $apkID, $logger){
		//     	$logger->logInfo("markUserStudyAsFinished() called");
		// checking if the apk exists
		$sql = "UPDATE ". $apkTable ." SET ustudy_finished=1 WHERE apkid = ". intval($apkID);
		$db->exec($sql);
	}
	
	/**
	 * Markes the userstudy as definitely finished (all devices have been notified about an
	 * available survey if any)
	 *
	 * @param mixed $db the database
	 * @param mixed $apkTable the table name
	 * @param mixed $apkID the id of the apk whose user study should be marked as finished
	 * @param mixed $logger the logger
	 */
	public static function markUserStudyAsDefinitelyFinished($db, $apkTable, $apkID, $logger){
		//     	$logger->logInfo("markUserStudyAsFinished() called");
		// checking if the apk exists
		$sql = "UPDATE ". $apkTable ." SET ustudy_finished=2 WHERE apkid = ". intval($apkID);
		$db->exec($sql);
	}

	/**
	 * Inserts current timestamp to time_enough_participants
	 *
	 * @param mixed $db the database
	 * @param mixed $apkTable the table name
	 * @param mixed $apkID the id of the apk in whose row the timetamp should be updated
	 */
	public static function insertTimestampToTimeEnoughParticipants($db, $apkTable, $apkID){
		$sql = "UPDATE ". $apkTable ." SET time_enough_participants=". time() ." WHERE apkid = ". intval($apkID);
		$db->exec($sql);
	}
	
	/**
	 * Returns an array containing hwids of all devices that have installed the specified APK
	 * @param unknown $db the database
	 * @param unknown $apkTableName the name of the apk table
	 * @param unknown $apkID the id of the apk
	 * @param unknown $logger the logger
	 * @return multitype:|mixed an array containing the hwids of the devices; if no devices are found an empty array is returned
	 */
	public static function getParticipatedDevices($db, $apkTableName, $apkID, $logger){
		$sql="SELECT installed_on FROM ".$apkTableName." WHERE apkid=".$apkID;
		$result = $db->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		if(empty($row)){
			$logger->logInfo("ApkManager::getParticipatedDevices() no participated devices found for apkID=".$apkID);
			return array();
		}
		else{
			$logger->logInfo("ApkManager::getParticipatedDevices() found participated devices for apkID=".$apkID);
			return json_decode($row['installed_on']);
		}
	}
    
    /**
    *  Updates user study, set the counter+1 that a user has sent his survey to server
    */
    public static function incrementSurveySendCounter($db, $apkTable, $apkID, $logger){
        $sql = "UPDATE ". $apkTable ." 
                SET survey_results_sent_count = survey_results_sent_count + 1 
                WHERE apkid = ". intval($apkID);
        $db->exec($sql);
    }

}
?>
