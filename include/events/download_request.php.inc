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
                           					"STATUS" => "SUCCESS",
                                           "NAME" => $APK_NAME,
                                           "URL" => $DOWNLOAD_URL);
                                           
                      }else{
                          $return = array("MESSAGE" => "DOWNLOAD_RESPONSE",
                                          "STATUS" => "FAILURE");
                      }
                  }else{
                      $return = array("MESSAGE" => "DOWNLOAD_RESPONSE",
                                      "STATUS" => "INVALID_SESSION");
                  }
                }else{
                    $return = array("MESSAGE" => "DOWNLOAD_RESPONSE",
                                    "STATUS" => "INVALID_SESSION");
                }
            }else{
                $return = array("MESSAGE" => "DOWNLOAD_RESPONSE", 
                                "STATUS" => "INVALID_APKID");
               }              
        }else{
            $return = array("MESSAGE" => "DOWNLOAD_RESPONSE", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "DOWNLOAD_RESPONSE", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "DOWNLOAD_RESPONSE", 
                    "STATUS" => "INVALID_SESSION");
} 

print(json_encode($return));

?>
