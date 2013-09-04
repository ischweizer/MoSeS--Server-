<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Zijad Maksuti
 */

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
       $deviceID = $data->OLD_DEVICEID;
       $TIME_NOW = time();
       
       $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
       
       if($IS_VALID_SESSION){
              
           $logger->logInfo("##################### CHANGE_DEVICE_ID, UPDATING SESSION ################");
           
           LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
           
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
       }
    }
}

print(json_encode($return));

?>
