<?php

$logger->logInfo("######## STILL ALIVE ARRIVED #############");

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
                
               $logger->logInfo("##################### STILL ALIVE ################ USER FOUND");
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                  LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                  
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
       }else{
            $return = array("MESSAGE" => "HELLO_THERE", 
                            "STATUS" => "FAILURE");
            }
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