<?php

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;

        $result = LoginManager::logoutUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
                          
        if($result !== false){
            $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                            "STATUS" => "SUCCESS");
        }else{
            $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                            "STATUS" => "FAILURE");
        }
    }else{
        $return = array("MESSAGE" => "LOGOUT_RESPONSE", 
                        "STATUS" => "FAILURE");
        }
}else{
    $return = array("MESSAGE" => "LOGOUT_RESPONSE", 
                    "STATUS" => "FAILURE");
} 

print(json_encode($return));

?>