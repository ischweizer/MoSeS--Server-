<?php
session_start();

if(isset($_POST['HTTP_JSON'])){
    
    
    
    $json = stripslashes($_POST['HTTP_JSON']);
    $data = json_decode($json);
    
    if($data->MESSAGE == "LOGIN_REQUEST"){
    
        include_once("./include/functions/dbconnect.php");
    
        $result = $db->query("SELECT * FROM user WHERE login = '". $data->LOGIN ."' AND password = '". $data->PASSWORD ."'");
        $row = $result->fetch(); 
    
        if(!empty($row)){
        
               // Store the session in the database
            $sessioninsert = "INSERT INTO android_session (session_id, userid, lastactivity) VALUES ('". session_id() ."', ". intval($row["userid"]) . ", " . time() . ") ";
            $db->exec($sessioninsert);
        
            $return = array("MESSAGE" => "LOGIN_RESPONSE",
                            "LOGIN" => $data->LOGIN,
                            "SESSIONID" => session_id());
                            
             print(json_encode($return));
            
        }else{
            $return = array("MESSAGE" => "LOGIN_RESPONSE",
                            "SESSIONID" => "NULL");
                            
             print(json_encode($return));
        }
    
    }
    
    else {
        echo "Only login messages are accepted.";
    }
    
    
}else{
    
    echo "You didn't sent us HTTP_JSON post var.";
    
}
     
?>