<?php

$logger->logInfo("############# SET FILTER ARRIVED ###############");

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            $deviceID = $data->DEVICEID;
            
            $FILTER = json_encode(array());
            
            if($data->FILTER != null){
                $FILTER = json_encode($data->FILTER);
            }
                   
            $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);

            if(!empty($USER)){
                
               $logger->logInfo("##################### SETTING FILTER ################ USER FOUND");
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                   $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ Session update");
                   
                   $FILTER = HardwareManager::sortSensors($FILTER);
                   
                   $logger->logInfo("Filter set by the user: " . $FILTER);
                   
                   LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);

                   $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
              
                   if(!empty($DEVICE)){
                       
                       HardwareManager::setFilter($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $FILTER, $USERID, $deviceID);
                       
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
       }else{
            $return = array("MESSAGE" => "SET_FILTER_RESPONSE", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "SET_FILTER_RESPONSE", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "SET_FILTER_RESPONSE", 
                    "STATUS" => "INVALID_SESSION");
} 
   
print(json_encode($return));

?>