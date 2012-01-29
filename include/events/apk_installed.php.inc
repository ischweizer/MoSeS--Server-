<?php

$logger->logInfo("APK INSTALLED ARRIVED");
                    
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### APK INSTALLED ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
      
      $APKID = $data->APKID;
      
      $RESTRICTION_NUMBER = ApkManager::getRestrictionUserNumber($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $APKID);
     
     if($RESTRICTION_NUMBER != null){
         if($RESTRICTION_NUMBER == -1){
              // update the usage count
              ApkManager::incrementAPKUsage($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $APKID);   
         }
         else
            if(ApkManager::isSelectedUser($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $APKID, $USERID)){
                // selected user: count the succesfull installation
                ApkManager::incrementAPKUsage($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $APKID);    
            }
     }
     
      
   }
}

?>