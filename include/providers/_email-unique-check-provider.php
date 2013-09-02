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

include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");

// If the email is unique, 0 is returned
// if the email is already contained in the database (someone used it already) 1 is returned
$logger->logInfo(" ###################### content_provider.php request for only checking the email ############################## ");
if(isEmailUnique($_POST["isEmailUnique"], $CONFIG, $db, $logger)){
    die("0"); // no users with such email found, the email is thus unique
}
else
    die("1"); // a user has already confirmed this email, the email is thus NOT unique
?>
