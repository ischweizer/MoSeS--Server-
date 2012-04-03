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
                        
                        // filter fits to sensors in APK
                        if(isFilterMatch($USER_FILTER, json_decode($apk['sensors'])) 
                            && !empty($apk['androidversion']) 
                            && is_numeric($apk['androidversion'])
                            && intval($apk['androidversion']) <= intval($ANDROID_VERSION)){
                             
                         
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

                      $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                      "STATUS" => "FAILURE_UNKNOWN_HARDWARE");
                  }
               }else{

                   $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                   "STATUS" => "FAILURE");
               }
            }else{

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