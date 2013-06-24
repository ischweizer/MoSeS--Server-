<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    
include_once("./include/functions/func.php");
include_once("./config.php");

$API_VERSION = array(array(8, 'API 8: "Froyo" 2.2.x'),
                     array(9, 'API 9: "Gingerbread" 2.3.0 - 2.3.2'),
                     array(10, 'API 10: "Gingerbread" 2.3.3 - 2.3.7'),
                     array(11, 'API 11: "Honeycomb" 3.0'),
                     array(12, 'API 12: "Honeycomb" 3.1'),
                     array(13, 'API 13: "Honeycomb" 3.2.x'),
                     array(14, 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2'),
                     array(15, 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4'));

/**
* Select all user devices
*/

include_once("./include/functions/dbconnect.php");

$USER_DEVICES = array();

$sql = 'SELECT * 
       FROM hardware 
       WHERE uid = '. $_SESSION['USER_ID'];
                               
$result = $db->query($sql);
$devices = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($devices)){
  $USER_DEVICES = $devices;
}


print_r($USER_DEVICES);

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Devices</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>Devices</h2>
        <div class="pagination pagination-centered">
        <ul>
            <li class=""><a href="#">&laquo;</a></li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">&raquo;</a></li>
        </ul>
        </div>
        <div id="page-selection">Pagination goes here</div>
        <div id="content"> 
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Model</th>
                  <th>API</th>
                  <th>Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Mark</td>
                  <td>Otto</td>
                  <td>@mdo</td>
                  <td>X</td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
    <!-- / Main Block -->
    
    <hr>

 <?php

//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>