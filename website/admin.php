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

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    if(!(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES")){
        header("Location: " . dirname($_SERVER['PHP_SELF']));
        exit;
    }

if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
   
   include_once("./include/functions/dbconnect.php");
   
   $USERS_SCIENTIST_LIST = array();
   
   $sql = "SELECT r.reason, u.hash, u.usergroupid, u.firstname, u.lastname, u.email 
           FROM ". $CONFIG['DB_TABLE']['REQUEST'] ." r, ". $CONFIG['DB_TABLE']['USER'] ." u 
           WHERE r.pending = 1 AND r.uid = u.userid";
           
   $result = $db->query($sql);
   $array = $result->fetchAll(PDO::FETCH_ASSOC);
      
   if(!empty($array)){
      $USERS_SCIENTIST_LIST = $array;
   }
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>The Mobile Sensing System - Admin panel</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <h2>Admin control panel</h2>
        <?php
        if(!empty($USERS_SCIENTIST_LIST)){
        ?>
        <form action="" method="post">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Surname</th>
                  <th>E-mail</th>
                  <th>Reason</th>
                  <th>Allow</th>
                  <th>Reject</th>
                </tr>
              </thead>
              <tbody id="content">
              <?php
                
                $i=1;
                foreach($USERS_SCIENTIST_LIST as $user){
                   echo '<tr>';
                   echo '<td>'. $i .'</td>';
                   echo '<td>'. $user['firstname'] .'</td>'; 
                   echo '<td>'. $user['lastname'] .'</td>'; 
                   echo '<td>'. $user['email'] .'</td>'; 
                   echo '<td>'. $user['reason'] .'</td>'; 
                   echo '<td><button type="submit" class="btn btn-warning btnAllowAccess" value="'. $user['hash'] .'">Approve</button></td>'; 
                   echo '<td><button type="submit" class="btn btn-danger btnRejectAccess" value="'. $user['hash'] .'">Reject</button></td>'; 
                   echo '</tr>';
                   $i++;
                }
              ?>
              </tbody>
            </table>
        </form>
        <?php
        }else{
                echo "<p>No pending requests for a scientist.</p>";
            }
        ?>
    <hr>

 <?php
 
//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>
<script src="js/admin.js"></script>
