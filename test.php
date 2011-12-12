<?php
session_start();

if(isset($_POST['HTTP_JSON'])){
    
    $json = stripslashes($_POST['HTTP_JSON']);
    $data = json_decode($json);
    
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
            
        case "SET_HARDWARE_PARAMS":
                
                // accept only set hardware params messages
                include_once("./include/functions/dbconnect.php");
                
                
                $result = $db->query("SELECT userid, lastactivity FROM android_session WHERE session_id = '". $data->SESSIONID ."'");
                $row = $result->fetch();
                
                if(!empty($row)){
                    
                   $USERID = $row["userid"];
                   $LASTACTIVITY = $row["lastactivity"];
                   
                   // session timeout 20 sec
                   $TIME_NOW = time();
                   $VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= 20) ? true : false;
                    
                   if($VALID_SESSION){
                        
                       $sql = "UPDATE android_session
                                SET
                                lastactivity = ". $TIME_NOW ." 
                                WHERE
                                session_id = '". $data->SESSIONID ."'";
                                            
                       $db->exec($sql);
                    
                       $sql = "SELECT deviceid FROM hardware WHERE uid = ". $USERID;
                       $result = $db->query($sql);
                       $row = $result->fetch();
                       
                       if(!empty($row)){
                           // just update, cause there is one in DB
                           
                           $sql = "UPDATE hardware
                                            SET
                                            deviceid = '". $data->DEVICEID ."', androidversion = '". $data->ANDVER ."', sensors = '". $data->SENSORS ."' 
                                            WHERE
                                            uid = ". $USERID;
                                            
                           $db->exec($sql);
                           
                       }else{
                           // there is no such device in the DB, so insert new one
                           
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
                   $VALID_SESSION = ($TIME_NOW - $LASTACTIVITY <= 20) ? true : false;
                    
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
                           $SENSORS = $row["sensors"];
                       
                           $return = array("MESSAGE" => "HARDWARE_PARAMS",
                                           "DEVICEID" => $DEVICEID,
                                           "ANDVER" => $ANDROID_VERSION,
                                           "SENSORS" => $SENSORS);
                                 
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
            
        default:
                echo "Only specific messages are accepted.";
                break;

    }
     
     
// if no POST var HTTP_JSON was sent       
}else{
    
    echo "You didn't sent us HTTP_JSON post var.";
    
}
     
?>