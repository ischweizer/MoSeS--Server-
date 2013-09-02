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
 * @author: Zijad Maksuti
 * @author: Wladimir Schmidt
 */

$sessionID = '';

$logger->logInfo("##################### GET_APK_LIST_REQUEST ARRIVED ##########");
$logger->logInfo("##################### GET_APK_LIST_REQUEST ################ RECIVED JSON OBJECT");
$logger->logInfo(print_r($data, true));

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    $logger->logInfo("##################### GET_APK_LIST_REQUEST SESSIONID IS".$data->SESSIONID);
    if(is_md5($data->SESSIONID)){
        $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ ISMD5 TRUE");
       
        $sessionID = $data->SESSIONID;
        $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ DEVICEID IS ".$data->DEVICEID);
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ DEVICEID IS SET");
            
            $deviceID = $data->DEVICEID;

            $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
            
            $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ LOGGED IN USER".print_r($USER,true));

            if(!empty($USER)){
                $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ USER IS NOT EMPTY");
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ USERID FOUND ".$USERID);
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                  $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ Session update");
                   
                  LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                      
                     $ANDROID_VERSION = HardwareManager::getAndroidVersion($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);

                     $json_array_return = array();
                     
//                      $APKs = ApkManager::getNonStudyAllApkRegardingMinAndroidVersion($DBManager->getDB(), $CONFIG['DB_TABLE']['APK'], $ANDROID_VERSION);
//                      $APKs = ApkManager::getAllApkRegardingMinAndroidVersion($logger, $DBManager->getDB(), $USERID, $deviceID, $CONFIG, $ANDROID_VERSION);
//                      $APKs = ApkManager::getAllApkRegardingMinAndroidVersion($logger, $DBManager->getDB(), $USERID, $CONFIG, $deviceID, $ANDROID_VERSION);

                     $inviteOnlyAPKs = ApkManager::getAllInviteOnlyApkRegardingDeviceid($logger, $DBManager->getDB(), $USERID, $deviceID, $CONFIG);
                     $groupAPKs = ApkManager::getAllGroupAPKs($logger, $DBManager->getDB(), $USERID, $CONFIG, $deviceID, $ANDROID_VERSION);
                     $groupAPKsWithInvite = ApkManager::getAllGroupAPKsWithInvite($logger, $DBManager->getDB(), $USERID, $CONFIG, $deviceID, $ANDROID_VERSION);
                     $publicAPKs = ApkManager::getPublicAPKs($logger, $DBManager->getDB(), $USERID, $CONFIG, $deviceID, $ANDROID_VERSION);
                     
                     foreach(array_reverse($groupAPKsWithInvite) as $apk){
                     	 
                     	 
                     	$APK_JSON = array("ID" => $apk['apkid'],
                     			"NAME" => $apk['apktitle'],
                     			"ANDVER" => $apk['androidversion'],
                     			"DESCR" => $apk['description'],
                     			"STARTDATE" => $apk['startdate'],
                     			"ENDDATE" => $apk['enddate'],
                     			"APKVERSION" => $apk['apk_version'],
                     			"BADGE" => "INVITE GROUP");
                     
                     	$json_array_return[] = $APK_JSON;
                     }
                                          
                     foreach(array_reverse($inviteOnlyAPKs) as $apk){
                             
                         
                            $APK_JSON = array("ID" => $apk['apkid'],
                                              "NAME" => $apk['apktitle'],
                                              "ANDVER" => $apk['androidversion'],
                                              "DESCR" => $apk['description'],
                                              "STARTDATE" => $apk['startdate'],
                                              "ENDDATE" => $apk['enddate'],
                                              "APKVERSION" => $apk['apk_version'],
                            		          "BADGE" => "INVITE");
                                              
                            $json_array_return[] = $APK_JSON; 
                     }
                     
                     foreach(array_reverse($groupAPKs) as $apk){
                     	 
                     	 
                     	$APK_JSON = array("ID" => $apk['apkid'],
                     			"NAME" => $apk['apktitle'],
                     			"ANDVER" => $apk['androidversion'],
                     			"DESCR" => $apk['description'],
                     			"STARTDATE" => $apk['startdate'],
                     			"ENDDATE" => $apk['enddate'],
                     			"APKVERSION" => $apk['apk_version'],
                     			"BADGE" => "GROUP");
                     
                     	$json_array_return[] = $APK_JSON;
                     }
                     
                     foreach(array_reverse($publicAPKs) as $apk){
                     	 
                     	 
                     	$APK_JSON = array("ID" => $apk['apkid'],
                     			"NAME" => $apk['apktitle'],
                     			"ANDVER" => $apk['androidversion'],
                     			"DESCR" => $apk['description'],
                     			"STARTDATE" => $apk['startdate'],
                     			"ENDDATE" => $apk['enddate'],
                     			"APKVERSION" => $apk['apk_version'],
                     			"BADGE" => "PUBLIC");
                     
                     	$json_array_return[] = $APK_JSON;
                     }
                     
                     $return = array("MESSAGE" => "GET_APK_LIST_RESPONSE",
                                     "STATUS" => "SUCCESS",
                                     "APK_LIST" => $json_array_return);
               }else{

                   $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                   "STATUS" => "INVALID_SESSION");
               }
            }else{

                $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                "STATUS" => "INVALID_SESSION");
            }
       }else{
            $return = array("MESSAGE" => "GET_APK_LIST_REQUEST", 
                            "STATUS" => "INVALID_DEVICEID");
            }
    }else{
        $return = array("MESSAGE" => "GET_APK_LIST_REQUEST", 
                        "STATUS" => "INVALID_SESSION");
    }
}else{
    $return = array("MESSAGE" => "GET_APK_LIST_REQUEST", 
                    "STATUS" => "INVALID_SESSION");
} 

print(json_encode($return));

?>
