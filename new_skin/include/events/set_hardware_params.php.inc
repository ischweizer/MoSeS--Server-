<?php

$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);
                    
if(!empty($USER)){
    
   $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
          
       $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ Session update");
       
       LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
       
       $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID);
       
       $data->SENSORS = HardwareManager::sortSensors($data->SENSORS);
       
       if(!empty($DEVICE)){
           // Just update existing device
           
           HardwareManager::updateDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $data->ANDVER, $data->SENSORS, $USERID, $data->DEVICEID);
           
       }else{
           // Insert new device
           
           HardwareManager::insertDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $data->ANDVER, $data->SENSORS, $USERID, $data->DEVICEID);
       }
       
       $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                       "STATUS" => "SUCCESS");

   }else{
       //$logger->logInfo(" ##################### SETTING HARDWARE PARAMS ################ Session TIMEOUT");

        // session is timed out
        $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                        "STATUS" => "FAILURE_SESSION_TIME_OUT");
   }
    
}else{

    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                    "STATUS" => "FAILURE_NO_SUCH_USER");
}
             
print(json_encode($return));

?>
