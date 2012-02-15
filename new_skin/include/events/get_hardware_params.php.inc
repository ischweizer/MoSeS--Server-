<?php

$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);
                    
if(!empty($USER)){
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
       $logger->logInfo(print_r($data->SENSORS, true));
       $logger->logInfo("##################### GETTING HARDWARE PARAMS ################ Session update");
       $logger->logInfo($data->DEVICEID);
         
       LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);

       $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID);
  
       if(!empty($DEVICE)){
           
           $DEVICEID = $DEVICE["deviceid"];
           $ANDROID_VERSION = $DEVICE["androidversion"];
           $SENSORS = json_decode($DEVICE["sensors"]);
       
           $return = array("MESSAGE" => "HARDWARE_PARAMS",
                           "DEVICEID" => $DEVICEID,
                           "ANDVER" => $ANDROID_VERSION,
                           "SENSORS" => $SENSORS,
                           "STATUS" => "SUCCESS");
       }else{
           $return = array("MESSAGE" => "HARDWARE_PARAMS",
                           "STATUS" => "FAILURE");
       }
   }else{
       $return = array("MESSAGE" => "HARDWARE_PARAMS",
                       "STATUS" => "FAILURE");
   }
    
}else{
    $return = array("MESSAGE" => "HARDWARE_PARAMS",
                    "STATUS" => "FAILURE");
}

print(json_encode($return));

?>