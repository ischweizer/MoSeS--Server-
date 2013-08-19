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