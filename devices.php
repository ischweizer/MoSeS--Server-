<?php

/*
 * @author: Wladimir Schmidt
 */

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN'])){
    header("Location: " . dirname($_SERVER['PHP_SELF']));   
    exit;
}
    
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
                     
/**
* Select all user devices
*/
$USER_DEVICES = array();
$USER_DEVICES_COUNT = 0;

$sql = 'SELECT * 
       FROM hardware 
       WHERE uid = '. $_SESSION['USER_ID'];
                               
$result = $db->query($sql);
$devices = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($devices)){
  $USER_DEVICES_COUNT = count($devices); 
  // we need only 5 on the first page
  $USER_DEVICES = array_slice($devices, 0, 5);
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
            <h2>Your devices</h2>
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
                    echo "<td>". $device['devicename'] ."</td>";
                    echo "<td>". $device['modelname'] ."</td>";
                    echo "<td>". getAPILevel($device['androidversion']) ."</td>";
                    echo '<td><button class="btn btn-danger btnDeviceRemove" value="'. $device['hwid'] .'">Remove</button></td>';
                    echo "</tr>";
                    $i++;
                }
                         
                ?>
              </tbody>
            </table>
            <?php
            if($USER_DEVICES_COUNT > 5){
                ?>
                <div id="page-selection" class="pagination pagination-centered"></div>
                <?php
            }
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
        'pages': <?php echo ((int)(count($USER_DEVICES) / 5)+1); ?>,
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
                    replaceRows += '<td>' + result[i].devicename + '</td>';
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
    
    $('.btnDeviceRemove').click(function(e){
        
        e.preventDefault();
        
        var clickedButton = $(this);
    
        clickedButton.removeClass('btn-danger');
        clickedButton.attr('disabled', true);
        clickedButton.text('Working...');
        
        $.post("content_provider.php", { 'remove': $('.btnDeviceRemove').val(), 'device_remove_code' : 1112 })
            .done(function(result) {
                if(result && result == "1"){
                    location.reload();
                }else{
                    clickedButton.addClass('btn-danger');
                    clickedButton.attr('disabled', false);
                    clickedButton.text('Remove');
                }
        });
    });
    
</script>