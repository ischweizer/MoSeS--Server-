<?php

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            $deviceID = $data->DEVICEID;

            $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);

            if(!empty($USER)){
                
               $logger->logInfo("##################### STILL ALIVE ################ USER FOUND");
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                  $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ Session update");
                   
                  LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $deviceID, $sessionID);
                  
                  $FILTER = HardwareManager::getFilter($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
                  
                  if(!empty($FILTER)){
                      
                     $ANDROID_VERSION = HardwareManager::getAndroidVersion($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $deviceID);
                     
                     $USER_FILTER = json_decode($FILTER['filter']);
                     $json_array_return = array();
                     
                     $APKs = ApkManager::getNonStudyAllApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK']);
                     
                     foreach($APKs as $apk){

                        // Some sensors from user filter were found in particular APK record
                        //if(count(array_intersect($USER_FILTER, json_decode($apk['sensors']))) > 0){
                        
                        $logger->logInfo("Android version from APK: ". $apk['androidversion']);
                        $logger->logInfo("Android version from DEVICE: ". $ANDROID_VERSION);
                        $logger->logInfo("intval shit ". intval($apk['androidversion']). " und so: ".intval($ANDROID_VERSION));
                        
                        // filter fits exact to sensors in APK
                        if(isFilterMatch($USER_FILTER, json_decode($apk['sensors'])) 
                            && !empty($apk['androidversion']) 
                            && is_numeric($apk['androidversion'])
                            && intval($apk['androidversion']) <= intval($ANDROID_VERSION)){
                            
                            // check for androic version in apk file to be <= android version in device  
                         
                            $APK_JSON = array("ID" => $apk['apkid'],
                                              "NAME" => $apk['apktitle'],
                                              "ANDVER" => json_decode($APK['androidversion']),
                                              "DESCR" => $apk['description'],
                                              "SENSORS" => json_decode($apk['sensors']));
                                              
                            $json_array_return[] = $APK_JSON; 
                        } 
                     }
                     
                     $return = array("MESSAGE" => "GET_APK_LIST_RESPONSE",
                                     "STATUS" => "SUCCESS",
                                     "APK_LIST" => $json_array_return);
                  }else{
                      $logger->logInfo("######### No hardware found");

                      $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                      "STATUS" => "FAILURE_UNKNOWN_HARDWARE");
                  }
               }else{
                   $logger->logInfo("######### No valid session");

                   $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                   "STATUS" => "FAILURE");
               }
            }else{
                $logger->logInfo("######### No session found");

                $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                "STATUS" => "FAILURE_NOT_LOGGED_IN");
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