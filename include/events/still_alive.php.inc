<?php

$logger->logInfo("STILL ALIVE ARRIVED");
                    
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### STILL ALIVE ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
      
      $return = array("MESSAGE" => "HELLO_THERE", 
                      "STATUS" => "SUCCESS");
   }else{
       $return = array("MESSAGE" => "HELLO_THERE", 
                       "STATUS" => "FAILURE");
   }
}else{
    $return = array("MESSAGE" => "HELLO_THERE", 
                    "STATUS" => "FAILURE");
}

print(json_encode($return));

?>