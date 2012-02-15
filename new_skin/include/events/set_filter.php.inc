<?php

$logger->logInfo("SET FILTER ARRIVED");
                    
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### SETTING FILTER ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
       $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ Session update");

       $FILTER = json_encode($data->FILTER); // filter from the message
       
       $FILTER = HardwareManager::sortSensors($FILTER);
       
       $logger->logInfo("Filter set by the user: " . $FILTER);
       
       LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);

       $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID);
  
       if(!empty($DEVICE)){
           
           $logger->logInfo("row update has to be commited");
           $logger->logInfo("##################### SETTING FILTER ################ deviceid selected and uid jetzt sofort");
           $logger->logInfo("UPDATE hardware");
           
           HardwareManager::setFilter($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $FILTER, $USERID, $data->DEVICEID);
           
           $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                           "STATUS" => "SUCCESS");
       }else{
           
          $logger->logInfo("TRYING TO SET FILTER FOR AN UNKNOWN DEVICE");

          $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                          "STATUS" => "FAILURE - USER HAS TO REGISTER THE DEVICE BEVORE SETTING FILTER FOR IT");
       }
   }else{
       $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                       "STATUS" => "FAILURE_SESSION_TIMEOUT");
   }
}else{
   $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                   "STATUS" => "FAILURE_NO_SUCH_USER"); 
}
   
print(json_encode($return));

?>