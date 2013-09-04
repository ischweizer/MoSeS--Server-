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

include_once("./config.php");
//If the login exists
if(isset($_POST["email_login"]) && 
   !empty($_POST["email_login"]) && 
   preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST["email_login"])){
   
    //And the password exists
    if(isset($_POST["password_login"]) && !empty($_POST["password_login"])){
        if(strlen($_POST["password_login"]) < 6){
            // password too short
            die($CONFIG['LOGIN_RESPONSE']['SHORT_PASSWORD']);
        }
        else{
            //Import of the connection�s file to database
            include_once("./include/functions/dbconnect.php");
            
            $USER_EMAIL =  $_POST["email_login"];
            $USER_PASSWORD =  $_POST["password_login"];
            
            //Select the user
            $sql = "SELECT *
              FROM ". $CONFIG['DB_TABLE']['USER'] ."
              WHERE email = '". $USER_EMAIL ."'
              AND password = '". $USER_PASSWORD ."'";
            
            $result = $db->query($sql);
            $row = $result->fetch();
            
            //users exist in database
            if(!empty($row)){
            
                //test if the user is registered
                if($row["confirmed"] == 1){
            
                    $USER_CONFIRMED = 1;
                    $_SESSION["USER_LOGGED_IN"] = 1;
            
                    $_SESSION["USER_ID"] =   $row["userid"];
                    $_SESSION["GROUP_ID"] =  $row["usergroupid"];
                    $_SESSION["EMAIL"] =     $row["email"];
                    $_SESSION["PASSWORD"] =  $row["password"];
                    $_SESSION["RGROUP"] =    $row["rgroup"];
                    $_SESSION["FIRSTNAME"] = $row["firstname"];
                    $_SESSION["LASTNAME"] =  $row["lastname"];
            
                    // we have an admin here logged in
                    if($row["usergroupid"] == 3){
                        $_SESSION["ADMIN_ACCOUNT"] =  "YES";
                    }
                    die($CONFIG['LOGIN_RESPONSE']['OK']);
            
                }else{
                    die($CONFIG['LOGIN_RESPONSE']['NOT_CONFIRMED']);
                }
            }else{
                die($CONFIG['LOGIN_RESPONSE']['WRONG_LOGIN_OR_PASSWORD']);
            }    
        }
    }else{
        die($CONFIG['LOGIN_RESPONSE']['MISSING_PASSWORD']);
    }
}else{
    die($CONFIG['LOGIN_RESPONSE']['MISSING_LOGIN']);
}
?>
