<?php

/**
* This class manages the creation and dropping of tables
*/
class GooglePushManager
{
    /**
    * Sends apkid of the new apk to all targetDevices
    * 
    * @param String $apkid the id for the user study
    * @param String $targetDevices array of String ids of target devices c2dm-ids
    * @param logger the Logger
    */
    public static function googlePushSendUStudy($apkid, $targetDevices, $logger, $CONFIG){
        
        $logger->logInfo("###########################googlePushSendUStudy##########################");
        
        $message = json_encode(array(
        		'MESSAGE' => "USERSTUDY",
        		'APKID' => $apkid));
        
        $response = GooglePushManager::sendMessage($logger, $CONFIG, $message, $targetDevices);
        
        $logger->logInfo("googlePushSendUStudy() RESPONSE");
        $logger->logInfo($response);
    }
    
    /**
     * Sends apkid of the new apk to all targetDevices
     *
     * @param String $apkid the id for the user study
     * @param String $targetDevices array of HARDWARE ids of target devices
     * @param logger the Logger
     */
    public static function googlePushSendUStudyToHardware($db, $apkid, $targetDevicesHWIds, $logger, $CONFIG){
    	
    	$targetDevices = array();
    	foreach($targetDevicesHWIds as $hwid){
    		$gcmId = HardwareManager::getGCMRegistrationId($db, $CONFIG['DB_TABLE']['HARDWARE'], $hwid);
    		if(!empty($gcmId))
    			$targetDevices[]=$gcmId;
    	}
    
    	GooglePushManager::googlePushSendUStudy($apkid, $targetDevices, $logger, $CONFIG);
    }
    
    
    /**
    * Sends update messages about new apk to all $targetDevices
    * 
    * @param String $apkid  the id of the new update
    * @param String $targetDevices array of String ids of target devices c2dm-ids
    * @param logger the Logger
    */
    public static function googlePushSendUpdate($apkid, $targetDevices, $logger, $CONFIG){
        
    
        $logger->logInfo("###########################googlePushSendUpdate##########################");

        $message = json_encode(array(
        		'registration_id' => $GOOGLE_C2DM_ID,
        		'collapse_key' => 'ck_' . $device_id,
        		'data.MESSAGE' => "UPDATE",
        		'data.APKID' => $apkid));
        
        $response = GooglePushManager::sendMessage($logger, $CONFIG, $message, $targetDevices);
        $logger->logInfo("RESPONSE");
        $logger->logInfo($response);
    }
    
    /**
     * Sends notifications to specified devies about an available survey with the specified apkid
     *
     * @param String $apkid the id for the user study
     * @param String $targetDevices array of String ids of target devices c2dm-ids
     * @param logger the Logger
     */
    public static function sendSurveyAvailable($apkid, $targetDevices, $logger, $CONFIG){
    
    	$logger->logInfo("GooglePushManager::sendSurveyAvailable()");
    
    	$message = json_encode(array(
    			'MESSAGE' => "QUEST",
    			'APKID' => $apkid));
    
    	$response = GooglePushManager::sendMessage($logger, $CONFIG, $message, $targetDevices);
    
    	$logger->logInfo("GooglePushManager::sendSurveyAvailable() RESPONSE");
    	$logger->logInfo($response);
    }
    
    /**
     * Sends notification about available survey for the specified apk to all targetDevices
     *
     * @param String $apkid the id for the user study
     * @param String $targetDevices array of HARDWARE ids of target devices
     * @param logger the Logger
     */
    public static function sendSurveyAvailableToHardware($db, $apkid, $targetDevicesHWIds, $logger, $CONFIG){
    	 
    	$targetDevices = array();
    	foreach($targetDevicesHWIds as $hwid){
    		$gcmId = HardwareManager::getGCMRegistrationId($db, $CONFIG['DB_TABLE']['HARDWARE'], $hwid);
    		if(!empty($gcmId))
    			$targetDevices[]=$gcmId;
    	}
    
    	GooglePushManager::sendSurveyAvailable($apkid, $targetDevices, $logger, $CONFIG);
    }
    
    /**
     * Sends a message using Google push services to receivers.
     * @param mixed $logger for logging purposes
     * @param array $CONFIG associciative array of the config file 
     * @param string $message message to be sent
     * @param array $receivers containing registration ids of all receivers
     * @return array containing the results: fails or successes 
     */
    private static function sendMessage($logger, $CONFIG, $message, $receivers){
    	
    	
    	$apiKey = $CONFIG['GPUSH']['GOOGLE_SERVER_KEY'];
    	
    	// Set POST variables
    	$url = 'https://android.googleapis.com/gcm/send';
    	
    	$fields = array(
    			'registration_ids'  => $receivers,
    			'data'              => array( "message" => $message ),
    	);
    	
    	$headers = array(
    			'Authorization: key=' . $apiKey,
    			'Content-Type: application/json'
    	);
    	
    	// Open connection
    	$ch = curl_init();
    	
    	// Set the url, number of POST vars, POST data
    	curl_setopt( $ch, CURLOPT_URL, $url );
    	
    	curl_setopt( $ch, CURLOPT_POST, true );
    	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    	
    	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
    	
    	// Execute post
    	$result = curl_exec($ch);
    	
    	// Close connection
    	curl_close($ch);
    	
    	return $result;
    }
}
?>