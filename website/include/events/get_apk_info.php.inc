<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */

$logger->logInfo("############## GET APK INFO ARRIVED ###############");
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
                    
                   $LASTACTIVITY = $USER["lastactivity"];
                   $TIME_NOW = time();
                   
                   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
                   
                   if($IS_VALID_SESSION){
                       
                      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                      
                      $APK = ApkManager::getApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $APKID, $logger);
                      
                      if(!empty($APK)){
                          
                          $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                          "STATUS" => "SUCCESS",
                                          "ID" => $APK['apkid'],
                                          "ANDVER" => json_decode($APK['androidversion']),
                                          "NAME" => $APK['apktitle'],
                                          "DESCR" => $APK['description'],
                                          "STARTDATE" => $apk['startdate'],
                                          "ENDDATE" => $apk['enddate'],
                                          "APKVERSION" => $apk['apk_version']);
                                      
                      }else{
                          $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                          "STATUS" => "FAILURE_NO_SUCH_APK");
                      }
                   }else{
                       $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                       "STATUS" => "INVALID_SESSION");
                   }
                }else{
                    $return = array("MESSAGE" => "GET_APK_INFO_RESPONSE", 
                                    "STATUS" => "INVALID_SESSION");
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
