<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */

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
            
            $logger->logInfo("A client has logged in");

            $logger->logInfo("ASSIGNED ANDROID-SESSION ".session_id());
            
            /*
             * Remember the DEVICEID and look for the assigned DEVICENAME
             */
            $DEVICEID = $data->DEVICEID;
            $logger->logInfo("DEVICEID provided by the client ".$DEVICEID);
            
            $userIDRow = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], session_id());
            $userID = $userIDRow['userid'];
            $deviceName = HardwareManager::getDeviceName($DBManager->getDB(), $CONFIG['DB_TABLE']['HARDWARE'], $userID, $DEVICEID);
            if($deviceName != null){
            	$logger->logInfo("DEVICENAME found: ".$deviceName);
            	// the server knows the name of the device, the client should recive it
            	$return = array("MESSAGE" => "LOGIN_RESPONSE",
            					"STATUS" => "SUCCESS",
            					"EMAIL" => $EMAIL,
            					"SESSIONID" => session_id(),
            					"DEVICENAME" => $deviceName);
            }
            else{
            	$logger->logInfo("DEVICENAME not found; the response will NOT contain DEVICENAME");
            	// the server does not know the name of the device
            	$return = array("MESSAGE" => "LOGIN_RESPONSE",
            			"STATUS" => "SUCCESS",
            			"EMAIL" => $EMAIL,
            			"SESSIONID" => session_id());
            }
        }else{
            $return = array("MESSAGE" => "LOGIN_RESPONSE",
            				"STATUS" => "FAILURE",
                            "SESSIONID" => "NULL");
            
            $logger->logInfo("USER_NOT_FOUND");
        }
    }else{
        $return = array("MESSAGE" => "LOGIN_RESPONSE",
        				"STATUS" => "FAILURE",
                        "SESSIONID" => "NULL");
    }
}else{
    $return = array("MESSAGE" => "LOGIN_RESPONSE",
    				"STATUS" => "FAILURE",
                    "SESSIONID" => "NULL");
}
    
print(json_encode($return));

?>
