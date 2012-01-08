<?php

include_once("./include/functions/dbconnect.php");

$result = $db->query("SELECT * 
                      FROM user 
                      WHERE login = '". $data->LOGIN ."' AND password = '". $data->PASSWORD ."'");
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

?>