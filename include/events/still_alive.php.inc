<?php

include_once("./include/functions/dbconnect.php");

$sql = "SELECT userid, lastactivity 
        FROM android_session 
        WHERE session_id = '". $data->SESSIONID ."'";
$result = $db->query($sql);
$row = $result->fetch();

$return = array(); // JSON OBJECT TO BE RETURNED 

if(!empty($row)){
       // the session-id is known, check if the session time is known
       $USERID = $row["userid"];
       $LASTACTIVITY = $row["lastactivity"];
       
       $TIME_NOW = time();
       $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
        
       if($IS_VALID_SESSION){
           
           $logger->logInfo("##################### STILL_ALIVE ################ Session update");
            
           // update the existant session
           $sql = "UPDATE android_session
                    SET
                    lastactivity = ". $TIME_NOW ." 
                    WHERE
                    session_id = '". $data->SESSIONID ."'";
           
           $logger->logInfo($sql); // LOG THE QUERY
                                
           $db->exec($sql);
           
           // create the response
           $return = array("MESSAGE" => "HELLO_THERE", 
                           "STATUS" => "SUCCESS");
       }else{
           $return = array("MESSAGE" => "HELLO_THERE", 
                           "STATUS" => "FAILURE");
       }
}else{
    $return = array("MESSAGE" => "HELLO_THERE", 
                    "STATUS" => "FAILURE");
}

print(json_encode($return));

?>