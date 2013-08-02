<?php

$logger->logInfo("########### DOWNLOAD REQUEST ARRIVED ##############");

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
        
        $sessionID = $data->SESSIONID;
        
        $row = LoginManager::getLoggedInDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
        $deviceID= $data->DEVICEID;
        
        if(!empty($deviceID)){
            
            $APKID = '';
            
            /**
            *  APKID check for right data
            */
            if($data->APKID != null){
               $data->APKID = preg_replace("/\D/", "", $data->APKID);
               
               if(!empty($data->APKID))
                  $APKID = $data->APKID;      
            }
            
            if(!empty($APKID)){
                
                $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);

                if(!empty($USER)){
                    
                   $logger->logInfo("##################### DOWNLOAD REQUEST ################ USER FOUND");
                    
                   $LASTACTIVITY = $USER["lastactivity"];
                   $TIME_NOW = time();
                   
                   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
                   
                   if($IS_VALID_SESSION){
                       
                      $logger->logInfo("##################### DOWNLOAD REQUEST ################ Session update");
                       
                      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);
                      
                      $APK = ApkManager::getApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $APKID, $logger);
                      
                      if(!empty($APK)){

                           $DOWNLOAD_URL = 'http://'. 
                                            $_SERVER['SERVER_NAME'] . 
                                            dirname($_SERVER['PHP_SELF']) . 
                                            '/apk/'. 
                                            $APK['userhash'] .'/'. $APK['apkhash'] .'.apk';
                                            
                           $APK_NAME = $APK['apkname'];
                       
                           $return = array("MESSAGE" => "DOWNLOAD_RESPONSE",
                                           "NAME" => $APK_NAME,
                                           "URL" => $DOWNLOAD_URL);
                                           
                      }else{
                          $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                                          "STATUS" => "FAILURE");
                      }
                  }else{
                      $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                                      "STATUS" => "INVALID_SESSION");
                  }
                }else{
                    $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                                    "STATUS" => "INVALID_SESSION");
                }
            }else{
                $return = array("MESSAGE" => "DOWNLOAD_REQUEST", 
                                "STATUS" => "INVALID_APKID");
               }              
        }else{
            $return = array("MESSAGE" => "DOWNLOAD_REQUEST", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "DOWNLOAD_REQUEST", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "DOWNLOAD_REQUEST", 
                    "STATUS" => "INVALID_SESSION");
} 

print(json_encode($return));

?>