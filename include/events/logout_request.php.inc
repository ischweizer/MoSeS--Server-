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
 * @author: Zijad Maksuti
 */

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

echo json_encode($return);

?>
