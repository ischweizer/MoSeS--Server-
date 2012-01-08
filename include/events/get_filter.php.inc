<?php

include_once("./include/functions/dbconnect.php");

$logger->logInfo("SET FILTER ARRIVED");   
            
$sql = "SELECT userid, lastactivity 
        FROM android_session 
        WHERE session_id = '". $data->SESSIONID ."'";
$result = $db->query($sql);
$row = $result->fetch();

$return = array(); // Message to be delivered to the client

if(!empty($row)){
    
   $USERID = $row["userid"];
   $LASTACTIVITY = $row["lastactivity"];
   
   // session timeout 20 sec
   $TIME_NOW = time();
   $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
    
   if($IS_VALID_SESSION){
        
       $sql = "UPDATE android_session
                SET
                lastactivity = ". $TIME_NOW ." 
                WHERE
                session_id = '". $data->SESSIONID ."'";
                            
       $db->exec($sql);
       
       // get filter params from DB
       $sql = "SELECT * 
               FROM hardware 
               WHERE 
               uid = ". $USERID ." AND deviceid = '". $data->DEVICEID ."'";
                            
       $result = $db->query($sql);
       $row = $result->fetch();
       
       if(!empty($row)){
           
           $DEVICEID = $row["deviceid"];
           $FILTER = json_decode($row["filter"]);
       
           $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                           "DEVICEID" => $DEVICEID,
                           "FILTER" => $FILTER,
                           "STATUS" => "SUCCESS");
           
       }else{
           
           $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                           "STATUS" => "FAILURE");
       }
   }else{
       
    $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                 "STATUS" => "FAILURE");
   }
    
}else{
    
  $return = array("MESSAGE" => "GET_FILTER_RESPONSE",
                  "STATUS" => "FAILURE");
    
}

// send the JSON FAILURE response
print(json_encode($return));

?>