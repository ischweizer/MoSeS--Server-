<?php


$logger->logInfo("LOGIN ARRIVED");

$result = LoginManager::loginUser($DBManager->getDB(), $CONFIG['DB_TABLE']['USER'], $CONFIG['DB_TABLE']['ANDROID_SESSION'], 
                                                  $data->LOGIN, $data->PASSWORD, session_id());
                
if($result !== false){
    
    $logger->logInfo("USER_FOUND"); 
    
    $return = array("MESSAGE" => "LOGIN_RESPONSE",
                    "LOGIN" => $data->LOGIN,
                    "SESSIONID" => session_id());
}else{
    $return = array("MESSAGE" => "LOGIN_RESPONSE",
                     "SESSIONID" => "NULL");
    
    $logger->logInfo("USER_NOT_FOUND");
}
    
print(json_encode($return));

?>