<?php

$logger->logInfo("GET APK INFO");
                    
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### GET APK INFO ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
      
      $APK_ID = $data->ID;
      
      $APK = ApkManager::getApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $USERID, $APK_ID);
      
      if(!empty($APK)){
          
          $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                          "STATUS" => "SUCCESS",
                          "ID" => $APK['apkid'],
                          "NAME" => $APK['apktitle'],
                          "DESCR" => $APK['description']);
                      
      }else{
          $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                          "STATUS" => "FAILURE");
      }
   }else{
       $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                       "STATUS" => "FAILURE");
   }
}else{
    $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                    "STATUS" => "FAILURE");
}

print(json_encode($return));

?>