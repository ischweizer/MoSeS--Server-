<?php

$logger->logInfo("GET APK INFO ARRIVED");
$logger->logInfo("SESSIONID PROVIDED BY THE CLIENT: ".$data->SESSIONID);

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            $deviceID = $data->DEVICEID;
            
            $APKID = '';
            
            /**
            *  APKID check for right data
            */
            if($data->ID != null){
               $data->ID = preg_replace("/\D/", "", $data->ID);
               
               if(!empty($data->ID))
                  $APKID = $data->ID;      
            }
            
            if(!empty($APKID)){
                    
                $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);

                if(!empty($USER)){
                    
                   $logger->logInfo("##################### GET APK INFO ################ USER FOUND");
                    
                   $USERID = $USER["userid"];
                   $LASTACTIVITY = $USER["lastactivity"];
                   $TIME_NOW = time();
                   
                   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
                   
                   if($IS_VALID_SESSION){
                       
                      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);
                      
                      $APK = ApkManager::getApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $USERID, $APKID, $logger);
                      
                      if(!empty($APK)){
                          
                          $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                          "STATUS" => "SUCCESS",
                                          "ID" => $APK['apkid'],
                                          "SENSORS" => json_decode($APK['sensors']),
                                          "NAME" => $APK['apktitle'],
                                          "DESCR" => $APK['description']);
                                      
                      }else{
                          $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                          "STATUS" => "FAILURE_NO_SUCH_APK");
                      }
                   }else{
                       $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                       "STATUS" => "FAILURE_INVALID_SESSION");
                   }
                }else{
                    $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                    "STATUS" => "FAILURE_INVALID_SESSION");
                }
          }else{
                $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                "STATUS" => "INVALID_APKID");
               }              
        }else{
            $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                    "STATUS" => "INVALID_SESSION");
} 

print(json_encode($return));

?>