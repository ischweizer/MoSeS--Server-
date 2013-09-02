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
 */ 
 
$logger->logInfo("######## STILL ALIVE ARRIVED #############");

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null){
    if(is_md5($data->SESSIONID)){
       
        $sessionID = $data->SESSIONID;
        
        if($data->DEVICEID != null && !empty($data->DEVICEID)){
            
            $deviceID = $data->DEVICEID;
                    
            $USER = LoginManager::getLoggedInUser($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);

            if(!empty($USER)){
                
               $logger->logInfo("##################### STILL ALIVE ################ USER FOUND");
                
               $USERID = $USER["userid"];
               $LASTACTIVITY = $USER["lastactivity"];
               $TIME_NOW = time();
               
               $IS_VALID_SESSION = LoginManager::isSessionTimedout($LASTACTIVITY, $TIME_NOW, $CONFIG['SESSION']['TIMEOUT']);
               
               if($IS_VALID_SESSION){
                   
                  LoginManager::updateSession($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $TIME_NOW, $sessionID);
                  
                  $return = array("MESSAGE" => "HELLO_THERE", 
                                  "STATUS" => "SUCCESS");
               }else{
                   $return = array("MESSAGE" => "HELLO_THERE", 
                                   "STATUS" => "FAILURE");
               }
            }else{
                $return = array("MESSAGE" => "HELLO_THERE", 
                                "STATUS" => "FAILURE");
            }
       }else{
            $return = array("MESSAGE" => "HELLO_THERE", 
                            "STATUS" => "FAILURE");
            }
    }else{
        $return = array("MESSAGE" => "HELLO_THERE", 
                        "STATUS" => "FAILURE");
    }
}else{
    $return = array("MESSAGE" => "HELLO_THERE", 
                    "STATUS" => "FAILURE");
} 

print(json_encode($return));

?>
