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
           
           if(empty($deviceID) || $deviceID==null){
               array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "FAILURE_DEVICEID_NOT_SET");
           }else{
           
               //#########
               $newDeviceID = $data->DEVICEID;
               
               // check if the device with the device with the target id already exists
               $logger->logInfo("#### VALUE OF CONFIG['DB_TABLE']['HARDWARE'] IS");
               $logger->logInfo($CONFIG['DB_TABLE']['HARDWARE']);
               $existant = HardwareManager::getHardware($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $newDeviceID, $USERID, $logger);
               
               if(empty($existant)){
                   // just update the current device, device with equal deviceid does not exist
                   HardwareManager::changeDeviceID($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID, $newDeviceID, $logger);
                   $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "SUCCESS");
               }
               else{
                   // device with equal id exists
                   $force = $data->FORCE;
                   if($force){
                       // FORCE-FLAG IS SET, DELTE THE PREVIOUS ENTRY
                       HardwareManager::removeHardware($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $newDeviceID, $USERID, $logger);
                       HardwareManager::changeDeviceID($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID, $newDeviceID, $logger);
                       $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "SUCCESS");
                   }
                   else{
                       $return = array("MESSAGE" => "CHANGED_DEVICE_ID", "STATUS" => "FAILURE_DEVICEID_DUPLICATED",
                       "MODEL_NAME" => $existant['modelname'], "VENDOR_NAME"=> $existant['vendorname'], "ANDVER"=>$existant['androidversion']);
                   }
               }
           }
           #####
       }
    }
}

print(json_encode($return));

?>
