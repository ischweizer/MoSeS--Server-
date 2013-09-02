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
include_once("./include/functions/dbconnect.php"); 
   
$request = $_POST['hash'];
   
// update his request, set to not pending one              
$sql = "UPDATE ". $CONFIG['DB_TABLE']['REQUEST'] ."
         SET
         pending = 0, accepted = 0 
         WHERE
         uid = (SELECT userid 
                 FROM ". $CONFIG['DB_TABLE']['USER'] ."
                 WHERE hash = '". $request ."')";
                    
$db->exec($sql);

die("0");
?>
