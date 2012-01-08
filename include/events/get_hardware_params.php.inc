<?php

include_once("./include/functions/dbconnect.php");

$sql = "SELECT userid, lastactivity 
        FROM android_session 
        WHERE session_id = '". $data->SESSIONID ."'";
        
$result = $db->query($sql);
$row = $result->fetch();


if(!empty($row)){
    
   $USERID = $row["userid"];
   $LASTACTIVITY = $row["lastactivity"];
   
   // session timeout
   $TIME_NOW = time();
   $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
    
   if($IS_VALID_SESSION){
        
       $sql = "UPDATE android_session
                SET
                lastactivity = ". $TIME_NOW ." 
                WHERE
                session_id = '". $data->SESSIONID ."'";
                            
       $db->exec($sql);
       
       // get device params from DB
       $sql = "SELECT * 
               FROM hardware 
               WHERE 
               uid = ". $USERID ." AND deviceid = '". $data->DEVICEID ."'";
                            
       $result = $db->query($sql);
       $row = $result->fetch();
       
       if(!empty($row)){
           
           $DEVICEID = $row["deviceid"];
           $ANDROID_VERSION = $row["androidversion"];
           $SENSORS = json_decode($row["sensors"]);
       
           $return = array("MESSAGE" => "HARDWARE_PARAMS",
                           "DEVICEID" => $DEVICEID,
                           "ANDVER" => $ANDROID_VERSION,
                           "SENSORS" => $SENSORS,
                           "STATUS" => "SUCCESS");
                 
            // send the JSON HARDWARE_PARAMS response
            print(json_encode($return));
           
       }else{
           
           $return = array("MESSAGE" => "HARDWARE_PARAMS",
                           "STATUS" => "FAILURE");
                 
            // send the JSON FAILURE response
            print(json_encode($return)); 
           
       }
       
   }else{
       
    $return = array("MESSAGE" => "HARDWARE_PARAMS",
                 "STATUS" => "FAILURE");
                 
    // send the JSON FAILURE response
    print(json_encode($return)); 
       
   }
    
}else{
    
  $return = array("MESSAGE" => "HARDWARE_PARAMS",
                  "STATUS" => "FAILURE");
                 
  // send the JSON FAILURE response
  print(json_encode($return)); 
    
}

?>