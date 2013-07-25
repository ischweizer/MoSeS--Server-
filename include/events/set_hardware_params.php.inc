<?php

$sessionID = '';
$logger->logInfo("##################### SETTING HARDWARE PARAMS ARRIVED ######################");

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
        $logger->logInfo("##################### SETTING HARDWARE PARAMS, IS MD5 ######################");
        $logger->logInfo("##################### SETTING HARDWARE PARAMS, DEVICEID IS ".$data->DEVICEID);
        $logger->logInfo("##################### SETTING HARDWARE PARAMS, SESSIONID IS ".$data->SESSIONID);
        if($data->FORCE)
            $logger->logInfo("##################### SETTING HARDWARE PARAMS, FORCE IS TRUE");
        else
            $logger->logInfo("##################### SETTING HARDWARE PARAMS, FORCE IS FALSE");
       
        $sessionID = $data->SESSIONID;
        
        /**
        *  DEVICEID check
        */
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            // get all information from data
            $deviceID = $data->DEVICEID;
            $deviceName = $data->DEVICENAME;
            $SENSORS = $data->SENSORS;
            $androidVersion = $data->ANDVER;
            $modelName = $data->MODEL_NAME;
            $vendorName = $data->VENDOR_NAME;
      
            $device = LoginManager::getLoggedInDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
                                
            if(!empty($device)){
                
               $logger->logInfo("##################### SETTING HARDWARE PARAMS, SESSION IS VALID, DEVICE IS FOUND ######################");
                
               $USERID = $device["userid"];
               $LASTACTIVITY = $device["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                      
                   $logger->logInfo("##################### SETTING HARDWARE PARAMS, UPDATING SESSION ################");
                   
                   LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                   
                   $logger->logInfo("SESSION IS UPDATED");
                   
                   $row = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
                   
                   if(!empty($row)){
                   	 
                   	// A device with the given ID already exists, the hardware information will be updated
                   	 
                   	$logger->logInfo("A device with the given ID already exists, the hardware information will be updated");
                   	 
                   	$SENSORS = HardwareManager::sortSensors($SENSORS);
                   	
                   	HardwareManager::updateDeviceLogger($logger, $DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $androidVersion,
                   	$SENSORS, $modelName, $vendorName, $USERID, $deviceID, $deviceName);

                   	$return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                   			"STATUS" => "SUCCESS");
                   }else{
                       
                       $logger->logInfo("The user has a new deviceID");
                       
                       // The user has a new deviceID
                       $SENSORS = HardwareManager::sortSensors($SENSORS);
                       
                       HardwareManager::insertDeviceLogger($logger, $DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'],
                            $androidVersion, $SENSORS, $modelName, $vendorName, $USERID, $deviceID, $deviceName);
                            
                       $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                                       "STATUS" => "SUCCESS");
                   }
               }else{
                    // session is timed out
                    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                                    "STATUS" => "FAILURE_INVALID_SESSION");
               } 
            }else{
                // session not in the database at all
                $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                                "STATUS" => "FAILURE_INVALID_SESSION");
            }
       }else{
            $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE", 
                        "STATUS" => "FAILURE_INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE", 
                    "STATUS" => "FAILURE_INVALID_SESSION");
}

print(json_encode($return));

?>
