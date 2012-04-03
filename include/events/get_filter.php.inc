<?php

$logger->logInfo("############# GET FILTER ARRIVED ####################");
  
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
                
               $logger->logInfo("##################### GETTING FILTER ################ USER FOUND");
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                  LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);

                  $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
              
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
        }else{
            $return = array("MESSAGE" => "GET_FILTER_RESPONSE", 
                            "STATUS" => "INVALID_DEVICEID");
        }
    }else{
        $return = array("MESSAGE" => "GET_FILTER_RESPONSE", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "GET_FILTER_RESPONSE", 
                    "STATUS" => "INVALID_SESSION");
} 
    
print(json_encode($return));

?>