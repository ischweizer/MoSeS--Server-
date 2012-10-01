<?php

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            $deviceID = $data->DEVICEID;

            $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
                                
            if(!empty($USER)){
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                   $logger->logInfo("##################### GETTING HARDWARE PARAMS ################ Session update");
                     
                   LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);

                   $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
              
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
       }else{
            $return = array("MESSAGE" => "HARDWARE_PARAMS", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "HARDWARE_PARAMS", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "HARDWARE_PARAMS", 
                    "STATUS" => "INVALID_SESSION");
} 

print(json_encode($return));

?>