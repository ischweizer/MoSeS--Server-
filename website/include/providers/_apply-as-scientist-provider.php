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
   
$REASON  = trim($_POST['reason']);

$sql = "SELECT accepted, pending 
        FROM ". $CONFIG['DB_TABLE']['REQUEST'] ." 
        WHERE uid = ". $_SESSION['USER_ID'];

$result = $db->query($sql);
$row = $result->fetch();    

// user has sent scientist request
if(!empty($row)){
    if($row['pending'] == 1){
        // user pending
        die('1');  
    }else 
        die('0');
}else{
    
    // User isn't applied as scientist yet, insert request
     $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['REQUEST'] ." 
            (uid, telephone, reason) 
            VALUES 
            (". $_SESSION['USER_ID'] .", '". $REASON ."')";

     $db->exec($sql);
     
     // user pending
     die('1');
}
?>
