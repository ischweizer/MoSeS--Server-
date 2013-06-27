<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    
include_once("./include/functions/func.php");
include_once("./config.php");

/**
* Select all user devices
*/
if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
       
       include_once("./include/functions/dbconnect.php");
       
       // remove my group
       if(isset($_GET['remove']))
       {
        
           $RAW_REMOVE_HASH = trim($_GET['remove']);
           
           if(is_md5($RAW_REMOVE_HASH)){
               
              $APK_REMOVED = 1;
              $REMOVE_HASH = strtolower($RAW_REMOVE_HASH);
               
              // getting userhah for dir later
              $sql = "SELECT userhash 
                      FROM apk 
                      WHERE userid = ". $_SESSION['USER_ID'] . " 
                      AND apkhash = '". $REMOVE_HASH ."'";
              
              $result = $db->query($sql);
              $row = $result->fetch();
              
              if(!empty($row)){
                  $dir = './apk/' . $row['userhash'];
                  if(is_dir($dir)){
                     if(file_exists($dir . '/'. $REMOVE_HASH . '.apk')){
                         unlink($dir . '/' . $REMOVE_HASH . '.apk');
                         
                         if(is_empty_dir($dir)){
                             rmdir($dir);
                         }
                     }
                  }
              }
               
              // remove entry from DB 
              $sql = "DELETE FROM apk 
                             WHERE userid = ". $_SESSION['USER_ID'] . " 
                             AND apkhash = '". $REMOVE_HASH ."'";
              
              $db->exec($sql);
               
           }else{
               $APK_REMOVED = 0;
           }  
           
       }

       // select all entries for particular user
       $sql = "SELECT * 
                FROM apk 
                WHERE userid = ". $_SESSION["USER_ID"];
                
       $result = $db->query($sql);
       $apk_listing = $result->fetchAll();    
                    
       if(!empty($apk_listing)){
           $LIST_APK = 1;
       }else{
           $LIST_APK = 0;
       }
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
              <tbody id="content"><?php
                
                $i=1;
                foreach($USER_DEVICES as $device){
                //for(int $i=1; $i<20; )
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
            <div id="page-selection" class="pagination pagination-centered"></div>
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
* API Versions
*/

var API_VERSION = {8: 'API 8: "Froyo" 2.2.x',
                   9: 'API 9: "Gingerbread" 2.3.0 - 2.3.2',
                   10: 'API 10: "Gingerbread" 2.3.3 - 2.3.7',
                   11: 'API 11: "Honeycomb" 3.0',
                   12: 'API 12: "Honeycomb" 3.1',
                   13: 'API 13: "Honeycomb" 3.2.x',
                   14: 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2',
                   15: 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4',
                   16: 'API 16: "Jelly Bean" 4.1.x',
                   17: 'API 17: "Jelly Bean" 4.2.x'};

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
                replaceRows += '<td><a href="devices.php?remove='+ result[i].hwid +'" title="Remove device" class="btn btn-warning">Remove</a></td>';
                replaceRows += '</tr>';
            }
            
            $('#content').html(replaceRows);
        }
       }); 
});
   
</script>

<?php
  
?>