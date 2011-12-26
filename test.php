<?php
session_start();

//$logger->logInfo("FILE REQUESTED");
/**
* Accept only form data with "HTTP_JSON" veriable 
*/
if(isset($_POST['HTTP_JSON'])){
    
    include_once("./include/functions/logger.php");
    
    $json = stripslashes($_POST['HTTP_JSON']);
    $data = json_decode($json);
    
    $SENSORS = json_encode($data->SENSORS);
    $data->SENSORS = $SENSORS;
    
    $logger->logInfo(print_r($data, true));
    
    ////$logger->logInfo("NACH: ". $data->SENSORS);
    
    /**
    *  Here will be selected which MESSAGE type was sent
    *  from Android client to this script  
    */
    switch($data->MESSAGE){
    
        case "LOGIN_REQUEST":
        
            include_once("./include/functions/dbconnect.php");
        
            $result = $db->query("SELECT * FROM user WHERE login = '". $data->LOGIN ."' AND password = '". $data->PASSWORD ."'");
            $row = $result->fetch(); 
        
            if(!empty($row)){
            
                // Store the session in the database
                $sql = "INSERT INTO android_session 
                                (session_id, userid, lastactivity) 
                                VALUES 
                                ('". session_id() ."', ". intval($row["userid"]) . ", " . time() . ")";
                
                $db->exec($sql);
            
                $return = array("MESSAGE" => "LOGIN_RESPONSE",
                                "LOGIN" => $data->LOGIN,
                                "SESSIONID" => session_id());
                  
                 // message to client that login was successful  
                 print(json_encode($return));
                 
                 //error_log(print_r($return, true), 3, './log/android.log');
                
            }else{
                $return = array("MESSAGE" => "LOGIN_RESPONSE",
                                "SESSIONID" => "NULL");
                          
                 // NO SUCH USER      
                 print(json_encode($return));
            }
            
            // close connection to DB
            $db = null;
            
            break;
        
        case "LOGOUT_REQUEST": 
           
            // accept only logout messages
            include_once("./include/functions/dbconnect.php");
        
            $sql = "DELETE FROM android_session WHERE session_id='". $data->SESSIONID ."'";
        
            // destroy the session
            $db->exec($sql);
            
            //send the response
            // at the moment, success is always returned
            $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                            "STATUS" => "SUCCESS");
            
            // send the JSON response
            print(json_encode($return));
            
            // close connection to DB
            $db = null;
            
            break;
            
        /**
        * ##################### SETTING HARDWARE PARAMS ################
        */
        case "SET_HARDWARE_PARAMS":
                
                // accept only set hardware params messages
                include_once("./include/functions/dbconnect.php");
                
                $logger->logInfo("SET HARDWARE PARAMS ARRIVED");
                
                $result = $db->query("SELECT userid, lastactivity FROM android_session WHERE session_id = '". $data->SESSIONID ."'");
                $row = $result->fetch();
                
                if(!empty($row)){
                    
                   $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ USER FOUND");    
                    
                   $USERID = $row["userid"];
                   $LASTACTIVITY = $row["lastactivity"];
                   
                   // session timeout 200 sec
                   $TIME_NOW = time();
                   $VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= 200) ? true : false;
                    
                   if($VALID_SESSION){
                       
                       $logger->logInfo($data->SENSORS);
                       $logger->logInfo(print_r($data->SENSORS, true));
                       
                       $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ Session update");
                        
                       $sql = "UPDATE android_session
                                SET
                                lastactivity = ". $TIME_NOW ." 
                                WHERE
                                session_id = '". $data->SESSIONID ."'";
                       
                       $logger->logInfo($sql); // LOG THE QUERY
                                            
                       $db->exec($sql);
                    
                       $sql = "SELECT deviceid FROM hardware WHERE uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";
                       $result = $db->query($sql);
                       
                       $logger->logInfo($sql); // LOG INFO
                       
                       $row = $result->fetch();
                       
                       $logger->logInfo("ROW IS: ". !empty($row));
                       
                       if(!empty($row)){
                       
                           $logger->logInfo("row update has to be commited");
                           
                           //while(!empty($row)){
                               
                               $logger->logInfo("##################### SETTING HARDWARE PARAMS ################ deviceid selected and uid jetzt sofort");
                           
                            //  if($row["deviceid"] == $data->DEVICEID){
                            $logger->logInfo("UPDATE HARDWARE");
                               
                                $sql = "UPDATE hardware
                                            SET
                                                androidversion = '". $data->ANDVER ."', sensors = '". $data->SENSORS ."' 
                                                 WHERE
                                                 uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";      // just update, cause there is one in DB
                                        
                                   $logger->logInfo("##################### SETTING HARDWARE PARAMS ################".$sql); // LOG THE QUERY 
                                                    
                                   $db->exec($sql);
                                   
                          //         break;
                                   
                         //     }

                           //     $row = $result->fetch();
                                
                               
                           //}
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
                
                // close connection to DB
                $db = null;
                
                break;
                
        case "GET_HARDWARE_PARAMS":
        
                // accept only set hardware params messages
                include_once("./include/functions/dbconnect.php");
                
                $sql = "SELECT userid, lastactivity FROM android_session WHERE session_id = '". $data->SESSIONID ."'";
                $result = $db->query($sql);
                $row = $result->fetch();
                
                
                if(!empty($row)){
                    
                   $USERID = $row["userid"];
                   $LASTACTIVITY = $row["lastactivity"];
                   
                   // session timeout 20 sec
                   $TIME_NOW = time();
                   $VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= 200) ? true : false;
                    
                   if($VALID_SESSION){
                        
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
                           
                           $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                                           "STATUS" => "FAILURE");
                                 
                            // send the JSON FAILURE response
                            print(json_encode($return)); 
                           
                       }
                       
                   }else{
                       
                    $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                                 "STATUS" => "FAILURE");
                                 
                    // send the JSON FAILURE response
                    print(json_encode($return)); 
                       
                   }
                    
                }else{
                    
                  $return = array("MESSAGE" => "HARDWARE_CHANGE_RESPONSE",
                                  "STATUS" => "FAILURE");
                                 
                  // send the JSON FAILURE response
                  print(json_encode($return)); 
                    
                }
        
                // close connection to DB
                $db = null;
                
                break;
                
                
                /**
        * ##################### SETTING FILTER ################
        */
        case "SET_FILTER":
                
                // accept only set hardware params messages
                include_once("./include/functions/dbconnect.php");
                
                $logger->logInfo("SET FILTER ARRIVED");
                
                $result = $db->query("SELECT userid, lastactivity FROM android_session WHERE session_id = '". $data->SESSIONID ."'");
                $row = $result->fetch();
                
                $return; // TO BE RETURNED TO THE USER
                
                if(!empty($row)){
                    
                   $logger->logInfo("##################### SETTING FILTER ################ USER FOUND");    
                    
                   $USERID = $row["userid"];
                   $LASTACTIVITY = $row["lastactivity"];
                   
                   // session timeout 200 sec
                   $TIME_NOW = time();
                   $VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= 200) ? true : false;
                    
                   if($VALID_SESSION){
                       
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
                    
                       $sql = "SELECT deviceid FROM hardware WHERE uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";
                       $result = $db->query($sql);
                       
                       $logger->logInfo($sql); // LOG INFO
                       
                       $row = $result->fetch();
                       
                       $logger->logInfo("ROW IS: ". !empty($row));
                       
                       if(!empty($row)){
                       
                           $logger->logInfo("row update has to be commited");
                               
                           $logger->logInfo("##################### SETTING FILTER ################ deviceid selected and uid jetzt sofort");
                           
                           $logger->logInfo("UPDATE hardware");
                               
                                $sql = "UPDATE hardware
                                            SET
                                                filter = '".$FILTER."' 
                                                 WHERE
                                                 uid = ". $USERID. " AND deviceid = '".$data->DEVICEID."'";      // just update, cause there is one in DB
                                        
                                   $logger->logInfo("##################### SETTING FILTER ################".$sql); // LOG THE QUERY 
                                                    
                                   $db->exec($sql);
                                   
                       }else{
                           // there is no such device in the DB, return the faliure
                                   
                          $logger->logInfo("TRYING TO SET FILTER FOR AN UNKNOWN DEVICE");
                          
                          $return = array("MESSAGE" => "SET_FILTER_RESPONSE",
                                     "STATUS" => "FALIURE_USER HAS TO REGISTER THE DEVICE BEVORE SETTING FILTER FOR IT");
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
                
                // close connection to DB
                $db = null;
                
                break;
            
        default:
                echo "Only specific messages are accepted.";
                break;

    }
     
     
// if no POST var HTTP_JSON was sent       
}else{
    
    echo "You didn't sent us HTTP_JSON post var.";
    
}
     
?>