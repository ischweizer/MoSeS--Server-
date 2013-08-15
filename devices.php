<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN'])){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    exit;
}
    
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
  
<title>The Mobile Sensing System - Devices</title>

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
<script type="text/javascript">
    /**
    * API Versions
    */

    var API_VERSION = {1 : 'Android 1.0 (API: 1)',
                       2 : 'Android 1.1 (API: 2)',
                       3 : '"Cupcake" 1.5 (API: 3)',
                       4 : '"Donut" 1.6 (API: 4)',
                       5 : '"Eclair" 2.0 (API: 5)',
                       6 : '"Eclair" 2.0.1 (API: 6)',
                       7 : '"Eclair" 2.1 (API: 7)',
                       8 : '"Froyo" 2.2.x (API: 8)',
                       9 : '"Gingerbread" 2.3.0 - 2.3.2 (API: 9)',
                       10 : '"Gingerbread" 2.3.3 - 2.3.7 (API: 10)',
                       11 : '"Honeycomb" 3.0 (API: 11)',
                       12 : '"Honeycomb" 3.1 (API: 12)',
                       13 : '"Honeycomb" 3.2.x (API: 13)',
                       14 : '"Ice Cream Sandwich" 4.0.0 - 4.0.2 (API: 14)',
                       15 : '"Ice Cream Sandwich" 4.0.3 - 4.0.4 (API: 15)',
                       16 : '"Jelly Bean" 4.1.x (API: 16)',
                       17 : '"Jelly Bean" 4.2.x (API: 17)',
                       18 : '"Jelly Bean" 4.3 (API: 18)'};

    /**
    * Pagination setup and query
    */
    var paging = {
        'pages': <?php echo ((int)(count($USER_DEVICES) / 5)) + 1; ?>,
        'pageMax': 5,
        'curPage': 1
    };
    $('#page-selection').bootpag({
        total: paging['pages'],
        page: 1,
        maxVisible: paging['pageMax']
        }).on('page', function(event, num){    
            
           // setting current selected page
           paging['curPage'] = num;
           // request json data
           $.ajax({
            dataType: "json",
            url: 'content_provider.php',
            data: paging,
            success: function(result){
                // processing returned data
                var deviceNumber = (((paging['curPage']-1)*paging['pageMax'])+1);
                var replaceRows = '';
                for(var i=0; i<result.length; ++i){
                    replaceRows += '<tr>';
                    replaceRows += '<td>' + (deviceNumber+i) + '</td>';
                    replaceRows += '<td>' + result[i].deviceid + '</td>';
                    replaceRows += '<td>' + result[i].modelname + '</td>';
                    replaceRows += '<td>' + API_VERSION[result[i].androidversion] + '</td>';
                    replaceRows += '<td><a href="devices.php?remove='+ result[i].hwid +'" title="Remove device" class="btn btn-danger">Remove</a></td>';
                    replaceRows += '</tr>';
                }
                
                $('#content').html(replaceRows);
            }
           }); 
    });

    // iterate through all menus and remove selection
    $('.dropdown').each(function(){
        $(this).removeClass('active');   
    });
    // add selection for this page
    $('.nav-menu2').addClass('active');
    
</script>