<?php

/*
* Handling of CHANGE_DEVICE_ID messages
*/

$return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "FAILURE_INVALID_SESSION");

if($data->SESSIONID != null){
    
    $sessionID = $data->SESSIONID;
    $device = LoginManager::getLoggedInDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
    
    if(!empty($device)){
        
       $USERID = $device["userid"];
       $LASTACTIVITY = $device["lastactivity"];
       $TIME_NOW = time();
       
       $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
       
       if($IS_VALID_SESSION){
              
           $logger->logInfo("##################### CHANGE_DEVICE_ID, UPDATING SESSION ################");
           
           LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);
           
           $logger->logInfo("SESSION IS UPDATED");
           
           HardwareManager::changeDeviceID($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID, $newDeviceID);
           
           $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "SUCESS");
       }
    }
}

print(json_encode($return));

?>
