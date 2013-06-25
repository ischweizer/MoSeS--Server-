<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    
include_once("./include/functions/func.php");
include_once("./config.php");

$API_VERSION = array(8 => 'API 8: "Froyo" 2.2.x',
                     9 => 'API 9: "Gingerbread" 2.3.0 - 2.3.2',
                     10 => 'API 10: "Gingerbread" 2.3.3 - 2.3.7',
                     11 => 'API 11: "Honeycomb" 3.0',
                     12 => 'API 12: "Honeycomb" 3.1',
                     13 => 'API 13: "Honeycomb" 3.2.x',
                     14 => 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2',
                     15 => 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4',
                     16 => 'API 16: "Jelly Bean" 4.1.x',
                     17 => 'API 17: "Jelly Bean" 4.2.x');

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
        <div id="page-selection" class="pagination pagination-centered"></div>
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
              <tbody><?php
                
                $i=1;
                foreach($USER_DEVICES as $device){
                    echo "<tr>";
                    echo "<td>". $i ."</td>";
                    echo "<td>". $device['deviceid'] ."</td>";
                    echo "<td>". $device['modelname'] ."</td>";
                    echo "<td>". $API_VERSION[$device['androidversion']] ."</td>";
                    echo '<td><a href="'. $_SERVER['PHP_SELF'] .'?remove='. $device['hwid'] .'" title="Remove device" class="btn btn-warning">Remove</a></td>';
                    echo "</tr>";
                    $i++;
                }
                         
                ?>
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

<script type="text/javascript">

/**
* Pagination
*/
$('#page-selection').bootpag({
    total: <?php echo count($USER_DEVICES); ?>,
    page: 1,
    maxVisible: 10
    }).on('page', function(event, num){
        
    $("#content").text("TEST"+num); // or some ajax content loading...
});
   
</script>

<?php
  
?>