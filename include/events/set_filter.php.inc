<?php

include_once("./include/functions/dbconnect.php");

$logger->logInfo("SET FILTER ARRIVED");
                
$result = $db->query("SELECT userid, lastactivity 
                      FROM android_session 
                      WHERE session_id = '". $data->SESSIONID ."'");
$row = $result->fetch();

$return = array(); // TO BE RETURNED TO THE USER

if(!empty($row)){
    
   $logger->logInfo("##################### SETTING FILTER ################ USER FOUND");    
    
   $USERID = $row["userid"];
   $LASTACTIVITY = $row["lastactivity"];
   
   // session timeout 200 sec
   $TIME_NOW = time();
   $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
    
   if($IS_VALID_SESSION){
       
       $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ Session update");
       
       $FILTER = json_encode($data->FILTER); // filter from the message
       
       $logger->logInfo("Filter set by the user: " . $FILTER);
        
       $sql = "UPDATE android_session
                SET
                lastactivity = ". $TIME_NOW ." 
                WHERE
                session_id = '". $data->SESSIONID ."'";
       
       $logger->logInfo($sql); // LOG THE QUERY
                            
       $db->exec($sql);
    
       $sql = "SELECT deviceid 
               FROM hardware 
               WHERE uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";
       $result = $db->query($sql);
       
       $logger->logInfo($sql); // LOG INFO
       
       $row = $result->fetch();
       
       $logger->logInfo("ROW IS: ". !empty($row));
       
       if(!empty($row)){
       
           $logger->logInfo("row update has to be commited");
               
           $logger->logInfo("##################### SETTING FILTER ################ deviceid selected and uid jetzt sofort");
           
           $logger->logInfo("UPDATE hardware");
               
               // just update, cause there is one in DB
                $sql = "UPDATE hardware
                        SET filter = '".$FILTER."' 
                        WHERE uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";      
                        
                   $logger->logInfo("##################### SETTING FILTER ################".$sql); // LOG THE QUERY 
                                    
                   $db->exec($sql);
                   
       }else{
           // there is no such device in the DB, return the faliure
                   
          $logger->logInfo("TRYING TO SET FILTER FOR AN UNKNOWN DEVICE");
          
          $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                     "STATUS" => "FAILURE - USER HAS TO REGISTER THE DEVICE BEVORE SETTING FILTER FOR IT");
       }
       
    $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                     "STATUS" => "SUCCESS");
    
}else{
    
    //$logger->logInfo(" ##################### SETTING FILTER ################ Session TIMEOUT");
    
    // session is timed out
    $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                 "STATUS" => "FAILURE_SESSION_TIME_OUT");
    
}
   
}else{
    
    $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                 "STATUS" => "FAILURE_NO_SUCH_USER");                    
}

// send the response
print(json_encode($return));

?>