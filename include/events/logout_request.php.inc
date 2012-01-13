<?php

$result = LoginManager::logoutUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $data->SESSIONID);
                  
if($result !== false){
    $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                    "STATUS" => "SUCCESS");
}else{
    $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                    "STATUS" => "FAILURE");
}

print(json_encode($return));

?>