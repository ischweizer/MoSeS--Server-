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
       $deviceID = $device['deviceid'];
       $TIME_NOW = time();
       
       $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
       
       if($IS_VALID_SESSION){
              
           $logger->logInfo("##################### CHANGE_DEVICE_ID, UPDATING SESSION ################");
           
           LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);
           
           $logger->logInfo("SESSION IS UPDATED");
           
           //#########
           $newDeviceID = $data->DEVICEID;
           
           // check if the device with the device with the target id already exists
           $existant = HardwareManager::getHardware($DBManager->getDB(), $newDeviceID, $userID);
           
           if(!$existant){
               // just update the current device, device with equal deviceid does not exist
               HardwareManager::changeDeviceID($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID, $newDeviceID);
               $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "SUCESS");
           }
           else{
               // device with equal id exists
               $force = $data->FORCE;
               if($force){
                   // FORCE-FLAG IS SET, DELTE THE PREVIOUS ENTRY
                   HardwareManager::removeHardware($DBManager->getDB(), $newDeviceID, $userID);
                   HardwareManager::changeDeviceID($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID, $newDeviceID);
                   $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "SUCESS");
               }
               else{
                   $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "FAILURE_DEVICEID_IN_USE");
               }
           }
       }
    }
}

print(json_encode($return));

?>
