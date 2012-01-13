<?php

$logger->logInfo("GET FILTER ARRIVED");
                    
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### GETTING FILTER ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);

      $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID);
  
      if(!empty($DEVICE)){
           
         $DEVICEID = $DEVICE["deviceid"];
         $FILTER = json_decode($DEVICE["filter"]);
       
         $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                         "DEVICEID" => $DEVICEID,
                         "FILTER" => $FILTER,
                         "STATUS" => "SUCCESS");   
      }else{
          $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                          "STATUS" => "FAILURE");
      }       
   }else{
       $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                       "STATUS" => "FAILURE");
   }
}else{
    $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                    "STATUS" => "FAILURE");
}
    
print(json_encode($return));

?>