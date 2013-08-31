<?php

/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */

$logger->logInfo("################# APK INSTALLED ARRIVED #############");
$logger->logInfo(print_r($data, true));

$return = ''; // answer to be returned

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
            
            if($data->DEVICEID != null){
            
                $deviceID = $data->DEVICEID;
                
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
                
                   $IS_VALID_SESSION = LoginManager::isSessionTimedout($lastactivity, time(), $CONFIG['SESSION']['TIMEOUT']);
               
                   if($IS_VALID_SESSION){
                       
                      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                      
                      // get hw-id
                      $userid = $row['userid'];
                      $sql_hw = "SELECT hwid FROM ".$CONFIG['DB_TABLE']['HARDWARE']. " WHERE uid=".$userid." AND deviceid='".$deviceID."'";
                      $db = $DBManager->getDB();
                      $result_hw =$db->query($sql_hw);
                      $row_hw = $result_hw->fetch();
                      $hwid = $row_hw['hwid'];
                      
                      
                      $result = ApkManager::incrementAPKUsage($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $apkid, $hwid, $logger);
                      
                      if($result){
                          $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                                    "STATUS" => "SUCCESS");
                      }
                      else{
                          $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                                    "STATUS" => "FAILURE_NO_SUCH_APK");
                      }
                   }else{
                       $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                                        "STATUS" => "INVALID_SESSION");
                   }
                }else{
                    $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                                    "STATUS" => "INVALID_APKID");
                   } 
        }else{
            $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                            "STATUS" => "FAILURE_INVALID_DEVICEID");
        }             
        }else{ 
            $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                            "STATUS" => "INVALID_SESSION");
        }
               
            }else{
                $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                                "STATUS" => "INVALID_SESSION");
            }
        }else{
            $return = array("MESSAGE" => "APK_INSTALLED_RESPONSE", 
                            "STATUS" => "INVALID_SESSION");
        } 

print(json_encode($return));

?>