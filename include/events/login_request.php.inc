<?php

$logger->logInfo("########### LOGIN ARRIVED ##############");

/**
* Check LOGIN for right data
*/
if($data->LOGIN != null && !empty($data->LOGIN) && preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $data->LOGIN)){
    
    $LOGIN = $data->LOGIN;
    
    /**
    * Check PASSWORD
    */
    if($data->PASSWORD != null && !empty($data->PASSWORD)){
    
        $PASSWORD = $data->PASSWORD;

        $result = LoginManager::loginUser($DBManager->getDB(), $CONFIG['DB_TABLE']['USER'], $CONFIG['DB_TABLE']['ANDROID_SESSION'], 
                                                          $LOGIN, $PASSWORD, session_id());
                        
        if($result !== false){
            
            $logger->logInfo("USER_FOUND"); 
            
            $return = array("MESSAGE" => "LOGIN_RESPONSE",
                            "LOGIN" => $LOGIN,
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