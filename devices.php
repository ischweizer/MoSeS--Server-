<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
                     
/*
    Remove device by its device id
*/
if(isset($_REQUEST['remove']) && !empty($_REQUEST['remove']) && is_numeric($_REQUEST['remove'])){
    
   // remove device entry from DB 
   $sql = "DELETE FROM ". $CONFIG['DB_TABLE']['HARDWARE'] ."  
                  WHERE uid = ". $_SESSION['USER_ID'] . " 
                  AND hwid = '". $_REQUEST['remove'] ."'";
      
   $db->exec($sql);
}

/**
* Select all user devices
*/
$USER_DEVICES = array();

$sql = 'SELECT * 
       FROM hardware 
       WHERE uid = '. $_SESSION['USER_ID'] .' 
       LIMIT 5';
                               
$result = $db->query($sql);
$devices = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($devices)){
  $USER_DEVICES = $devices;
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Devices</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
            <?php
                 if(empty($USER_DEVICES)){
                     
            ?><h2 class="text-center">You have no devices.</h2><?php
                     
                 }else{
             ?>
            <h2>Devices</h2>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Model</th>
                  <th>API</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="content"><?php
                
                $i=1;
                foreach($USER_DEVICES as $device){
                    echo "<tr>";
                    echo "<td>". $i ."</td>";
                    echo "<td>". $device['deviceid'] ."</td>";
                    echo "<td>". $device['modelname'] ."</td>";
                    echo "<td>". getAPILevel($device['androidversion']) ."</td>";
                    echo '<td><a href="'. $_SERVER['PHP_SELF'] .'?remove='. $device['hwid'] .'" title="Remove device" class="btn btn-danger">Remove</a></td>';
                    echo "</tr>";
                    $i++;
                }
                         
                ?>
              </tbody>
            </table>
            <div id="page-selection" class="pagination pagination-centered"></div>
            <?php
                 }
            ?>
    </div>
    <!-- / Main Block -->
    
    <hr>

 <?php

include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");
?>
<script src="js/devices.js"></script>