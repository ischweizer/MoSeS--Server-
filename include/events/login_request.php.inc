<?php

$logger->logInfo("########### LOGIN ARRIVED ##############");

/**
* Check LOGIN for right data
*/
if($data->EMAIL != null && !empty($data->EMAIL) && preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $data->EMAIL)){
    
    $EMAIL = $data->EMAIL;
    
    /**
    * Check PASSWORD
    */
    if($data->PASSWORD != null && !empty($data->PASSWORD)){
    
        $PASSWORD = $data->PASSWORD;

        $result = LoginManager::loginUser($DBManager->getDB(), $CONFIG['DB_TABLE']['USER'], $CONFIG['DB_TABLE']['ANDROID_SESSION'], 
                                                          $EMAIL, $PASSWORD, session_id());
                        
        if($result !== false){
            
            $logger->logInfo("USER_FOUND");

            $logger->logInfo("ASSIGNED ANDROID-SESSION ".session_id());
            
            $return = array("MESSAGE" => "LOGIN_RESPONSE",
                            "EMAIL" => $EMAIL,
                            "SESSIONID" => session_id());
        }else{
            $return = array("MESSAGE" => "LOGIN_RESPONSE",
                            "SESSIONID" => "NULL");
            
            $logger->logInfo("USER_NOT_FOUND");
        }
    }else{
        $return = array("MESSAGE" => "LOGIN_RESPONSE",
                        "SESSIONID" => "NULL");
    }
}else{
    $return = array("MESSAGE" => "LOGIN_RESPONSE",
                    "SESSIONID" => "NULL");
}
    
print(json_encode($return));

?>