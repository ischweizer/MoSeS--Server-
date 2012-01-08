<?php

include_once("./include/functions/dbconnect.php");

$logger->logInfo("SET HARDWARE PARAMS ARRIVED");
                
$result = $db->query("SELECT userid, lastactivity 
                        FROM android_session 
                        WHERE session_id = '". $data->SESSIONID ."'");
$row = $result->fetch();

if(!empty($row)){
    
   $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ USER FOUND");    
    
   $USERID = $row["userid"];
   $LASTACTIVITY = $row["lastactivity"];
   
   // session timeout 200 sec
   $TIME_NOW = time();
   $IS_VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= $session_interval) ? true : false;
    
   if($IS_VALID_SESSION){
                              
       $logger->logInfo(print_r($data->SENSORS, true));
       
       $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ Session update");
        
       $sql = "UPDATE android_session
                SET
                lastactivity = ". $TIME_NOW .",
                deviceid = '". $data->DEVICEID ."' 
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
           
               
           $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ deviceid selected and uid jetzt sofort");
           

            $logger->logInfo("UPDATE HARDWARE");
               
                $sql = "UPDATE hardware
                            SET
                                androidversion = '". $data->ANDVER ."', sensors = '". $data->SENSORS ."' 
                                 WHERE
                                 uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";      // just update, cause there is one in DB
                        
                   $logger->logInfo("##################### SETTING HARDWARE PARAMS ################".$sql); // LOG THE QUERY 
                                    
                   $db->exec($sql);
                   

       }else{
           // there is no such device in the DB, so insert new one
                   
          $logger->logInfo("INSERT HARDWARE");
          
          $sql = "INSERT INTO hardware 
                    (uid, deviceid, androidversion, sensors) 
                    VALUES 
                    (". $USERID .", '". $data->DEVICEID . "', '". $data->ANDVER ."', '". $data->SENSORS ."')";
    
          $db->exec($sql);
       }
       
    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                     "STATUS" => "SUCCESS");
                     
    // send the JSON SUCCESS response
    print(json_encode($return));
    
}else{
    
    //$logger->logInfo(" ##################### SETTING HARDWARE PARAMS ################ Session TIMEOUT");
    
    // session is timed out
    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                 "STATUS" => "FAILURE_SESSION_TIME_OUT");
                 
    // send the JSON FAILURE response
    print(json_encode($return));
    
}
   
}else{
    
    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                 "STATUS" => "FAILURE_NO_SUCH_USER");
                 
    // send the JSON FAILURE response
    print(json_encode($return));
    
}

?>