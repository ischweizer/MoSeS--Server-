<?php

$logger->logInfo("DOWNLOAD REQUEST ARRIVED");
                
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### DOWNLOAD REQUEST ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      $logger->logInfo("##################### DOWNLOAD REQUEST ################ Session update");
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
      
      $APKID = $data->APKID;
      
      $APK = ApkManager::getApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $USERID, $APKID);
      
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
                      "STATUS" => "FAILURE");
  }
}else{
    $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                    "STATUS" => "FAILURE");
}

print(json_encode($return));

?>