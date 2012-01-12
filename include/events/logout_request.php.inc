<?php

include_once("./include/functions/dbconnect.php");

$sql = "DELETE FROM android_session 
               WHERE session_id='". $data->SESSIONID ."'";

$db->exec($sql);

//send the response
// at the moment, success is always returned
$return = array("MESSAGE" => "LOGOUT_RESPONSE",
                "STATUS" => "SUCCESS");

// send the JSON response
print(json_encode($return));
?>