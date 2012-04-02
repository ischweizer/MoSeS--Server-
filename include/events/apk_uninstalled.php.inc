<?php

$logger->logInfo("APK UNINSTALLED ARRIVED");
$logger->logInfo(print_r($data, true));


$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
       $sessionID = $data->SESSIONID;  
       $row = LoginManager::getLoggedInDevice($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);

        // session is found
        if(!empty($row)){
            
            /**
            *  if new DEVICEID found, set new DEVICEID
            */
            if($data->DEVICEID != null){
                $deviceID = ((strcmp($data->DEVICEID, $row['deviceid']) != 0) ? $data->DEVICEID : $row['deviceid']);
            }else{
                $deviceID = $row['deviceid'];
            } 
            
            $lastactivity = $row['lastactivity'];
            $apkid = '';
            
            /**
            *  APKID check for right data
            */
            if($data->APKID != null){
               $data->APKID = preg_replace("/\D/", "", $data->APKID);
               
               if(!empty($data->APKID))
                  $apkid = $data->APKID;      
            }
            
            if(!empty($apkid)){
            
                //###################
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($lastactivity, time(), $CONFIG['SESSION']['TIMEOUT']);
           
               if($IS_VALID_SESSION){
                   
                  LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);
                  
                  
                  // get hw-id
                  $userid = $row['userid'];
                  $sql_hw = "SELECT hwid FROM ".$CONFIG['DB_TABLE']['HARDWARE']. " WHERE uid=".$userid." AND deviceid='".$deviceID."'";
                  $db = $DBManager->getDB();
                  $result_hw =$db->query($sql_hw);
                  $logger->logInfo("sql on getting hwid");
                  $logger->logInfo($sql_hw);
                  $row_hw = $result_hw->fetch();
                  $hwid = $row_hw['hwid'];
                  
                  
                  
                  ApkManager::decrementAPKUsage($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $apkid, $hwid, $logger);
               }
            }
        }
    }
}

?>