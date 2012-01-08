<?php

include_once("./include/functions/dbconnect.php");

$logger->logInfo("DOWNLOAD REQUEST ARRIVED");   
                
$sql = "SELECT userid, lastactivity 
        FROM android_session 
        WHERE session_id = '". $data->SESSIONID ."'";
$result = $db->query($sql);
$row = $result->fetch();

// Message to be delivered to the client
$return = array();

// session found on server
if(!empty($row)){
    
   $USERID = $row["userid"];
   $LASTACTIVITY = $row["lastactivity"];
   
   $TIME_NOW = time();
   $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
    
   if($IS_VALID_SESSION){
        
       $sql = "UPDATE android_session
                SET
                lastactivity = ". $TIME_NOW ." 
                WHERE
                session_id = '". $data->SESSIONID ."'";
                            
       $db->exec($sql);
       
       $APKID = $data->APKID; 
       
       $sql = "SELECT userhash, apkhash, apkname  
               FROM apk 
               WHERE 
               userid = ". $USERID ." AND apkid = '". $APKID ."'";
                            
       $result = $db->query($sql);
       $row = $result->fetch();
       
       if(!empty($row)){
           
           $DOWNLOAD_URL = 'http://'. 
                            $_SERVER['SERVER_NAME'] . 
                            dirname($_SERVER['PHP_SELF']) . 
                            '/apk/'. 
                            $row['userhash'] .'/'. $row['apkhash'] .'.apk';
                            
           $APK_NAME = $row['apkname'];
       
           $return = array("MESSAGE" => "DOWNLOAD_RESPONSE",
                           "NAME" => $APK_NAME,
                           "URL" => $DOWNLOAD_URL);
           
       }else{
           
           $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                           "STATUS" => "FAILURE");
       }
   }else{
       
    $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                 "STATUS" => "FAILURE");
   }
    
}else{
    
  $return = array("MESSAGE" => "DOWNLOAD_REQUEST",
                  "STATUS" => "FAILURE");
    
}

print(json_encode($return));

?>