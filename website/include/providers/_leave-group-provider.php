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

$sql_leave = "SELECT rgroup 
              FROM ".$CONFIG['DB_TABLE']['USER']." 
              WHERE userid=".$_SESSION['USER_ID'];

$old_group_result =  $db->query($sql_leave);
$aRow = $old_group_result->fetch();

$groupname = " ";

if(!empty($aRow))
    $groupname = $aRow['rgroup'];

// update the tables
$sql_update1 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." 
                SET rgroup='' 
                WHERE userid=". $_SESSION['USER_ID'];
                
$db->exec($sql_update1);

// remove the user from the group
$sql_members = "SELECT members 
                FROM ".$CONFIG['DB_TABLE']['RGROUP']." 
                WHERE name='".$groupname."'";
                
$members_result = $db->query($sql_members); 
$members_row = $members_result->fetch();

$members = json_decode($members_row['members']);
$newMembers = array();

foreach($members as $mid)
    if($mid != $_SESSION['USER_ID'])
        $newMembers[] = $mid;
        
$sql_update4 = '';

if(count($newMembers) == 0)
    $sql_update4 = "DELETE 
                    FROM ".$CONFIG['DB_TABLE']['RGROUP']." 
                    WHERE name='".$groupname."'";
else{
    
    $newMembers = json_encode($newMembers);
    $sql_update4 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." 
                    SET members='". $newMembers ."' ". ($_SESSION['GROUP_ID'] == 2 ? ", instant_scientists_counter = instant_scientists_counter - 1 " : "") ." 
                    WHERE name='". $groupname ."'";
}
$db->exec($sql_update4); 

// set group name in session
$_SESSION['RGROUP'] = '';

die($groupname);
?>
