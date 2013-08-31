<?php

/*
 * @author: Zijad Maksuti
 */

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            $deviceID = $data->DEVICEID;
            
            if($data->C2DM != null && !empty($data->C2DM)){
                
                $c2dmID = $data->C2DM;

                $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
                                    
                if(!empty($USER)){
                    
                   $logger->logInfo("##################### C2DM ARRIVED ################ USER FOUND");
                    
                   $USERID = $USER["userid"];
                   $LASTACTIVITY = $USER["lastactivity"];
                   $TIME_NOW = time();
                   
                   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
                   
                   if($IS_VALID_SESSION){
                       
                       $logger->logInfo("##################### C2DM ################ Session update");
                       
                       LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                       
                       $DEVICE = HardwareManager::selectDeviceForUser($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
                       
                       if(!empty($DEVICE)){
                           // Just update existing device
                           
                           HardwareManager::addc2dm($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID, $c2dmID);
                           
                           
                           $logger->logInfo("############# C2DM ARRIVED FROM CLIENT ##############");
                           $logger->logInfo($c2dmID);
                           
                           $return = array("MESSAGE" => "C2DM",
                                           "STATUS" => "SUCCESS");   
                       }else{ 
                           $return = array("MESSAGE" => "C2DM",
                                           "STATUS" => "FAILURE_NO_SUCH_DEVICE");
                       }
                   }else{
                        // session is timed out
                        $return = array("MESSAGE" => "C2DM",
                                        "STATUS" => "INVALID_SESSION");
                   }
                }else{
                    $return = array("MESSAGE" => "C2DM",
                                    "STATUS" => "INVALID_SESSION");
                }
            }else{
               $return = array("MESSAGE" => "C2DM", 
                               "STATUS" => "INVALID_C2DM"); 
            }
        }else{
            $return = array("MESSAGE" => "C2DM", 
                            "STATUS" => "INVALID_DEVICEID");
        }
    }else{
        $return = array("MESSAGE" => "C2DM", 
                        "STATUS" => "INVALID_SESSION");
        }
    }else{
        $return = array("MESSAGE" => "C2DM", 
                        "STATUS" => "INVALID_SESSION");
    } 
             
print(json_encode($return));

?>
