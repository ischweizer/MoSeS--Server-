<?php

include_once(MOSES_HOME . "/include/functions/dbconnect.php");
include_once(MOSES_HOME . "/include/functions/func.php");
        
// TODO: write the select valid session and update it in ONE query

$sql = "SELECT userid, lastactivity, deviceid 
        FROM android_session 
        WHERE session_id = '". $data->SESSIONID ."'";
        
$result = $db->query($sql);
$row = $result->fetch();

$return = array(); // JSON OBJECT TO BE RETURNED 

if(!empty($row)){
    
       // the session-id is known, check if the session time is known
       $USERID = $row["userid"];
       $DEVICEID = $data->DEVICEID;
       $LASTACTIVITY = $row["lastactivity"];
       
       $TIME_NOW = time();
       $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
        
       if($IS_VALID_SESSION){
           
           $logger->logInfo("##################### GET_APK_LIST_REQUEST ################ Session update");
            
           // update the existant session
           $sql = "UPDATE android_session
                    SET
                    lastactivity = ". $TIME_NOW ." 
                    WHERE
                    session_id = '". $data->SESSIONID ."'";
                                                       
           $db->exec($sql);
           
           $logger->logInfo($sql); // LOG that QUERY
           
           $sql = "SELECT filter 
                   FROM hardware 
                   WHERE uid = ". $USERID ." AND deviceid = '". $DEVICEID ."'";
                    
           $result = $db->query($sql);
           $row = $result->fetch();
           
           if(!empty($row)){
               
             $USER_FILTER = json_decode($row['filter']);
             $json_array_return = array();  
               
             $sql = "SELECT * 
                     FROM apk";
                    
             $result = $db->query($sql);
             $array = $result->fetchAll(PDO::FETCH_ASSOC);
             
             foreach($array as $apk){

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