<?php

include_once(MOSES_HOME . '/include/functions/func.php');
                
$USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);

if(!empty($USER)){
    
   $logger->logInfo("##################### STILL ALIVE ################ USER FOUND");
    
   $USERID = $USER["userid"];
   $LASTACTIVITY = $USER["lastactivity"];
   $DEVICEID = $data->DEVICEID;
   $TIME_NOW = time();
   
   $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
   
   if($IS_VALID_SESSION){
       
      $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ Session update");
       
      LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $data->DEVICEID, $data->SESSIONID);
      
      $FILTER = HardwareManager::getFilter($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $USERID, $DEVICEID);
      
      if(!empty($FILTER)){

         $USER_FILTER = json_decode($FILTER['filter']);
         $json_array_return = array();
         
         $APKs = ApkManager::getAllApk($DBManager->getDB(), $CONFIG['DB_TABLE']['APK']);
         
         foreach($APKs as $apk){

            // Some sensors from user filter were found in particular APK record
            //if(count(array_intersect($USER_FILTER, json_decode($apk['sensors']))) > 0){
            
            // filter fits exact to sensors in APK
            if(isFilterMatch($USER_FILTER, json_decode($apk['sensors']))){
             
                $APK_JSON = array("ID" => $apk['apkid'],
                                  "NAME" => $apk['apktitle'],
                                  "DESCR" => $apk['description']);
                                  
                $json_array_return[] = $APK_JSON; 
            } 
         }
         
         $return = array("MESSAGE" => "GET_APK_LIST_RESPONSE",
                                         "STATUS" => "SUCCESS",
                                         "APK_LIST" => $json_array_return);
                      }else{
                          $logger->logInfo("######### No hardware found");
               
                          $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                          "STATUS" => "FAILURE");
                      }
                   }else{
                       $logger->logInfo("######### No valid session");
           
                       $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                       "STATUS" => "FAILURE");
                   }
                }else{
                    $logger->logInfo("######### No session found");
    
                    $return = array("MESSAGE" => "GET_APK_LIST_REQUEST",
                                    "STATUS" => "FAILURE");
                }
                
                print(json_encode($return));

?>