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

$pages = intval($_REQUEST['pages']);
$pageMax = intval($_REQUEST['pageMax']);
$curPage = intval($_REQUEST['curPage']);

$USER_DEVICES = array();

$sql = 'SELECT * 
       FROM hardware 
       WHERE uid = '. $_SESSION['USER_ID'] .' 
       LIMIT '.((($curPage-1)*$pageMax)).', '.($curPage*$pageMax);
                               
$result = $db->query($sql);
$devices = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($devices)){
  $USER_DEVICES = $devices;
}

// return devices as json
die(json_encode($USER_DEVICES));
?>
