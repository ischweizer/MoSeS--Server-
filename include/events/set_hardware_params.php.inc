<?php

$device = LoginManager::getLoggedInDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

$return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                    "STATUS" => "FAILURE_UNKNOWN_REASON");
                    
if(!empty($device)){
    
   $logger->logInfo("##################### SETTING HARDWARE PARAMS, SESSION IS VALID ######################");
    
   $USERID = $device["userid"];
   $LASTACTIVITY = $device["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
          
       $logger->logInfo("##################### SETTING HARDWARE PARAMS, UPDATING SESSION ################");
       
       LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
       
       $logger->logInfo("SESSION IS UPDATED");
       
       $row = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID);
       
       $logger->logInfo("RETRIVED ROW FROM THE DATABASE");
       
       if(!empty($row)){
           
           // A device with the given ID already exists, the hardware information will be updated if FORCE-flag is set to TRUE
           
           $logger->logInfo("A device with the given ID already exists, the hardware information will be updated if FORCE-flag is set to TRUE");
           
           if($data->FORCE){
               
               $logger->logInfo("FORCE IS TRUE");
               
               $data->SENSORS = HardwareManager::sortSensors($data->SENSORS);
               
               HardwareManager::updateDeviceLogger($logger, $DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'],
                        $data->ANDVER, $data->SENSORS, $data->MODEL_NAME, $data->VENDOR_NAME,  $USERID, $data->DEVICEID);
               $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                       "STATUS" => "SUCCESS");
           }
           else{
               $logger->logInfo("the FORCE-flag is not set, inform the user about the duplication");
               // the FORCE-flas is not set, inform the user about the duplication
               $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                       "STATUS" => "FAILURE_DEVICEID_DUPLICATED",
                       "MODEL_NAME" => $row['modelname'],
                       "VENDOR_NAME" => $row['vendorname'],
                       "ANDVER" => $row['androidversion']);
           }
       }else{
           $logger->logInfo("The user has a new deviceID");
           $logger->logInfo(var_export($data->SENSORS, true));
           // The user has a new deviceID
           $data->SENSORS = HardwareManager::sortSensors($data->SENSORS);
           
           HardwareManager::insertDeviceLogger($logger, $DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'],
                $data->ANDVER, $data->SENSORS, $data->MODEL_NAME, $data->VENDOR_NAME, $USERID, $data->DEVICEID);
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
$logger->logInfo("SET HARDWARE PARAMS ANSWERS WITH");
$logger->logInfo(var_export($return, true));
print(json_encode($return));

?>
