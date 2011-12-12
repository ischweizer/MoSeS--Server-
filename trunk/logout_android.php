<?php
session_start();

if(isset($_POST['HTTP_JSON'])){
    
    
    
    $json = stripslashes($_POST['HTTP_JSON']);
    $data = json_decode($json);
    
    // accept only logout messages
    if($data->MESSAGE == "LOGOUT_REQUEST"){
    
        include_once("./include/functions/dbconnect.php");
    
        $sessiondelete = "DELETE FROM android_session WHERE session_id='". $data->SESSIONID ."'";
        
        // destroy the session
        $db->exec($sessiondelete);
        
        //send the response
        // at the moment, success is always returned
        $return_logout = array("MESSAGE" => "LOGOUT_RESPONSE",
                                "STATUS" => "SUCCESS");
        
        // send the response
        print(json_encode($return_logout));
    }
    
    else {
        echo "Only logout messages are accepted.";
    }
    
    
}else{
    
    echo "You didn't sent us HTTP_JSON post var.";
    
}
     
?>