<?php

$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);
                    
if(!empty($USER)){
    
   $logger->logInfo("##################### C2DM ARRIVED ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
       $logger->logInfo("##################### C2DM ################ Session update");
       
       LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
       
       $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID);
       
       if(!empty($DEVICE)){
           // Just update existing device
           
           HardwareManager::addc2dm($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $data->DEVICEID, $data->C2DM);
           
           
           $logger->logInfo("C2DM ARRIVED FROM CLIENT");
           $logger->logInfo($data->C2DM);
           
           $return = array("MESSAGE" => "C2DM",
                            "STATUS" => "SUCCESS");
           
       }else{
           
           $return = array("MESSAGE" => "C2DM",
                           "STATUS" => "FAILURE_NO_SUCH_DEVICE");
       }
       
       

   }else{
       //$logger->logInfo(" ##################### SETTING HARDWARE PARAMS ################ Session TIMEOUT");

        // session is timed out
        $return = array("MESSAGE" => "C2DM",
                        "STATUS" => "FAILURE_SESSION_TIME_OUT");
   }
    
}else{

    $return = array("MESSAGE" => "C2DM",
                    "STATUS" => "FAILURE_NO_SUCH_USER");
}
             
print(json_encode($return));

?>
