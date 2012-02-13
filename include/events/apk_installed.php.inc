<?php

$logger->logInfo("APK INSTALLED ARRIVED");
$logger->logInfo(print_r($data, true));

$return; // answer to be returned
                    
$row = LoginManager::getLoggedInDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(empty($row)){
    $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                    "STATUS" => "INVALID_SESSION");
}
else{
    $deviceID = $row['deviceid'];
    $lastactivity = $row['lastactivity'];
    $apkid = $data->APKID;
    
    //###################
    $IS_VALID_SESSION = LoginManager::isSessionTimedout($lastactivity, time(), $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
      
      $result = ApkManager::incrementAPKUsage($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $apkid);
      if($result){
          $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                    "STATUS" => "SUCCESS");
      }
      else{
          $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                    "STATUS" => "FALIURE_NO_SUCH_APK");
      }
   }
   else{
       $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                        "STATUS" => "INVALID_SESSION");
   }
}

print(json_encode($return));

?>