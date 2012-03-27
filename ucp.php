<?php
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: /moses/");
    
include_once("./include/functions/func.php");
include_once("./include/_header.php");
include_once("./config.php");

$apk_listing = '';  // just init
$groupname = null; // name of the group the user is in OR name of the group the user wants to join
$grouppwd = null; // password of the group the user wants to join
$groupsize = 0; // size of the group

/*
* join/create status
* ON JOIN:
* 0: invalid group-name/password
* 1: valid group-name and password
* ON CREATE
* 2: group-name already given
* 3: group-name not already given
* 
*/
$jcstatsus = 0;

// SWITCH USER CONTORL PANEL MODE
if(isset($_GET['m'])){
    
    $RAW_MODE = strtoupper(trim($_GET['m']));
    $MODE = '';
    
    switch($RAW_MODE){
        case 'UPLOAD':
        
                     $MODE = 'UPLOAD';
                       
                       if(isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                        
                           $RAW_UPLOAD_RESULT = strtoupper(trim($_GET['res']));
                           
                           switch($RAW_UPLOAD_RESULT){
                               case "1":
                                        $UPLOAD_RESULT = 1;  // file successfully uploaded
                                        break;
                                        
                               case "0":
                                        $UPLOAD_RESULT = 0;  // file failed to upload
                                        break;
                                        
                               case "2":
                                        $UPLOAD_RESULT = 2;  // filetype not allowed
                                        break;
                                        
                               case "3":
                                        $UPLOAD_RESULT = 3;  // file too large
                                        break;
                                        
                               case "4":
                                        $UPLOAD_RESULT = 4;  // no permissions to write into dir
                                        break;
                                        
                               default:
                                        $UPLOAD_RESULT = 999;  // someone is trying to hax?
                           }
                       }
                       
                       break; 
        
        case 'LIST':
            if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                   $MODE = 'LIST'; 
                   
                   include_once("./include/functions/dbconnect.php");
                   
                   if(isset($_GET['remove'])){
                    
                       $RAW_REMOVE_HASH = trim($_GET['remove']);
                       
                       if(is_md5($RAW_REMOVE_HASH)){
                           
                          $APK_REMOVED = 1;
                          $REMOVE_HASH = strtolower($RAW_REMOVE_HASH);
                           
                          // getting userhah for dir later
                          $sql = "SELECT userhash FROM apk 
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
                          
                          // remove file itself from the system
                          
                             
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
                   
                   break;
                   
        case 'PROMO':
                    $MODE = 'PROMO';
                    
                    if(isset($_POST['promo_sent']) && trim($_POST['promo_sent']) == "1"){
                        
                        include_once("./include/functions/dbconnect.php");
                        
                        $RAW_TELEPHONE = $_POST['telephone'];
                        $RAW_REASON = $_POST['reason'];
                        
                        // TODO: Add some security later
                        
                        $TELEPHONE  = trim($RAW_TELEPHONE);
                        $REASON  = trim($RAW_REASON);
                        
                        $sql = "SELECT accepted, pending 
                                FROM request 
                                WHERE uid = ". $_SESSION['USER_ID'];
                                
                        $result = $db->query($sql);
                        $row = $result->fetch();    
      
                        // user has sent scientist request
                        if(!empty($row)){
                            if($row['pending'] == 1){
                                $USER_PENDING = 1;  
                            }else{
                                if($row['accepted'] == 1)
                                    $USER_PENDING = 0;
                                    $USER_ALREADY_ACCEPTED = 1;  
                            }
                        }else{
                            
                            // User hasn't sent us scientist request yet
                             $sql = "INSERT INTO request 
                                    (uid, telephone, reason) 
                                    VALUES 
                                    (". $_SESSION['USER_ID'] .", '". $TELEPHONE . "', '". $REASON ."')";
                    
                             $db->exec($sql);
                             
                             $USER_PENDING = 1;  
                        }
                    }else{
                        
                        include_once("./include/functions/dbconnect.php");
                        
                        $sql = "SELECT accepted, pending 
                                FROM request 
                                WHERE uid = ". $_SESSION['USER_ID'];
                                
                        $result = $db->query($sql);
                        $row = $result->fetch();
                        
                        if(!empty($row)){
                           if($row['pending'] == 1){
                                $USER_PENDING = 1;
                                
                                if($row['accepted'] == 0){
                                    $USER_ALREADY_ACCEPTED = 0;  
                                }else{
                                    $USER_ALREADY_ACCEPTED = 1;  
                                }
                            }else{
                                $USER_PENDING = 0;
                                
                                if($row['accepted'] == 1){
                                    $USER_ALREADY_ACCEPTED = 1;  
                                }else{
                                    $USER_ALREADY_ACCEPTED = 0;
                                }
                            }
                        }
                        
                    }
                    
                    break;
                       
        case 'ADMIN':  
                    if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
                       
                       $MODE = 'ADMIN';
                       
                       include_once("./include/functions/dbconnect.php");
                       
                       if(isset($_POST['pending_requests']) && is_array($_POST['pending_requests']) && count($_POST['pending_requests']) > 0){
                                        
                          foreach($_POST['pending_requests'] as $request){
                              
                              $sql = "UPDATE request
                                        SET
                                        pending = 0, accepted = 1 
                                        WHERE
                                        uid = (SELECT userid 
                                                FROM user
                                                WHERE hash = '". $request ."')";
                                                    
                               $db->exec($sql);
                               
                               // USER IS NOW IN SCIENTIST-GROUP
                               $sql = "UPDATE user SET usergroupid= 2 WHERE hash = '". $request ."'";
                               
                               $db->exec($sql);
                              
                          }     
                           
                       }
                       
                       $USERS_SCIENTIST_LIST = array();
                       
                       $sql = "SELECT r.telephone, r.reason, u.hash, u.usergroupid, u.firstname, u.lastname 
                               FROM request r, user u 
                               WHERE r.pending = 1 AND r.uid = u.userid";
                               
                       $result = $db->query($sql);
                       $array = $result->fetchAll(PDO::FETCH_ASSOC);
                          
                       if(!empty($array)){
                          $USERS_SCIENTIST_LIST = $array;
                       }
                    }
                       break;
        // ##### GROUP ############
        case 'GROUP':
            $MODE = 'GROUP';
            // obtain the name of group the user is currently in (if any)
            $group_sql = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $_SESSION['USER_ID'];
            include_once("./include/functions/dbconnect.php");
            $group_result = $db->query($group_sql);
            $group_row = $group_result->fetch();
            if(!empty($group_row)){
                $groupname = $group_row['rgroup'];
            }
            
            break;
        // ##############
        
        // ##### USER HAS CLICKED THE JOIN/CREATE BUTTON ############
        case 'JOIN':
            if(isset($_POST["group_name"]) && isset($_POST["group_pwd"])){
                include_once("./include/functions/dbconnect.php");
                $MODE = 'JOIN';
                $groupname = trim($_POST["group_name"]);
                $grouppwd = trim($_POST["group_pwd"]);
                if(isset($_POST["join_create"]) && $_POST["join_create"] == "create" ){
                    // the user wants to create a group, check if the group name is already given
                    $sql_check = "SELECT * FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='".$groupname."'";
                    $check_result = $db->query($sql_check);
                    $check_row = $check_result->fetch();
                    if(!empty($check_row)){
                        // group-name is already given
                        $jcstatsus = 2;
                    }
                    else{
                        // update the databases
                        $members = json_encode(array(intval($_SESSION['USER_ID'])));
                        $sql_newgroup = "INSERT INTO ".$CONFIG['DB_TABLE']['RGROUP']." (name, password, members) VALUES 
                        ('". $groupname ."', '". $grouppwd . "', '" . $members . "')";
                        $sql_update2 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." SET rgroup='".$groupname."' WHERE userid=".$_SESSION['USER_ID'];
                        $db->exec($sql_newgroup);
                        $db->exec($sql_update2);
                        $jcstatsus = 3;
                    }
                }
                else{
                    // the user wants to join a group
                    // check if the user has provided a valid name of the group and password
                    $sql_join = "SELECT * FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='".$groupname."' AND password='".$grouppwd."'";
                    $rgroup_result = $db->query($sql_join);
                    $rgroup_row = $rgroup_result->fetch();
                    if(!empty($rgroup_row)){
                        $jcstatsus = 1; // the user has provided valid rgroup-name and password
                        // update the tables
                        $sql_update1 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." SET rgroup='".$groupname."' WHERE userid=".$_SESSION['USER_ID'];
                        $db->exec($sql_update1);
                        $sql_members = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']." WHERE name='".$groupname."'";
                        $members_result = $db->query($sql_members); 
                        $members_row = $members_result->fetch();
                        $members = json_decode($members_row['members']);
                        $members[] = intval($_SESSION['USER_ID']);
                        $members = array_unique($members);
                        sort($members);
                        $members = json_encode($members);
                        $sql_update3 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." SET members='".$members."' WHERE name='".$groupname."'";
                        $db->exec($sql_update3);
                    }
                }
            }
            
            break;
        // ##############
        
        // ##### USER HAS CLICKED THE LEAVE BUTTON ############
        case 'LEAVE':
            $MODE = 'LEAVE';
            include_once("./include/functions/dbconnect.php");
            $sql_leave = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']." WHERE userid=".$_SESSION['USER_ID'];
            $old_group_result =  $db->query($sql_leave);
            $aRow = $old_group_result->fetch();
            $groupname = " ";
            if(!empty($aRow))
                $groupname = $aRow['rgroup'];
            // update the tables
            $sql_update1 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." SET rgroup='' WHERE userid=".$_SESSION['USER_ID'];
            $db->exec($sql_update1);
            // remove the user from the group
            $sql_members = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']." WHERE name='".$groupname."'";
            $members_result = $db->query($sql_members); 
            $members_row = $members_result->fetch();
            $members = json_decode($members_row['members']);
            $newMembers = array();
            foreach($members as $mid)
                if($mid != $_SESSION['USER_ID'])
                    $newMembers[] = $mid;
            //$members = array_diff($members, array($_SESSION['USER_ID'])); remove me
            $sql_update4;
            if(count($newMembers) == 0)
                $sql_update4 = "DELETE FROM ".$CONFIG['DB_TABLE']['RGROUP']." WHERE name='".$groupname."'";
            else{
                $newMembers = json_encode($newMembers);
                $sql_update4 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." SET members='".$newMembers."' WHERE name='".$groupname."'";
            }
            $db->exec($sql_update4);
            
            break;
        // ##############
        
        
        default: 
                $MODE = 'NONE';
    }
}else{

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
}

?>
  
<title>Hauptseite von MoSeS - User control panel</title>

<?php  
  include_once("./include/_menu.php");
?>  

<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>
<!-- <div id="splash">&nbsp;</div> -->
<!-- end #header -->

<div class="user_menu">  
    <ul><?php
        
        if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
          ?>  
          
          <li><a href="ucp.php?m=admin">ADMIN PANEL</a></li>
          <li>&nbsp;</li>  
            
          <?php
        }
    
        ?>
        <li><a href="ucp.php">My Devices</a></li>
        <?php
         if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>0){
             
        ?>
        <li><a href="ucp.php?m=group">My Group</a></li>
        <li>&nbsp;</li>
        <?php
         }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>1){
            ?>
            <li><a href="ucp.php?m=upload">Upload an App</a></li>
            <li><a href="ucp.php?m=list">Show my Apps</a></li>
        <?php }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]<2){
            /*
            * Offer an instant upgrade to scientist account if the user is a member of a group and
            * #devices-in-group - #scientist-in-group*5 >= 5
            */
            // determine if the user is a member of a group
            $gr_sql = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $_SESSION['USER_ID'];
            include_once("./include/functions/dbconnect.php");
            $gr_result = $db->query($gr_sql);
            $gr_row = $gr_result->fetch();
            //echo("<p>".$gr_sql."<p>" );
            if(!empty($gr_row) && $gr_row['rgroup']!=null){
                $grname = $gr_row['rgroup'];
               // echo("<p>hello<p>" );
                //echo("<p>".$grname."<p>" );
                // #### USER IS A MEMBER OF A GROUP###//
                // determine number of devices and scientists
                $nDevices = 0;
                $mem_sql = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='" .$grname."'";
                $mem_result = $db->query($mem_sql);
               // echo("<p>".$mem_sql."<p>" );
                $mem_row = $mem_result->fetch();
                $mem = json_decode($mem_row['members']);
                // determine number of scientists
                $nScientists = 0;
                foreach($mem as $id){
                    $mbr_sql = "SELECT usergroupid FROM ".$CONFIG['DB_TABLE']['USER']." WHERE userid=".$id;
                    $mbr_result = $db->query($mbr_sql);
                    $mbr_row = $mbr_result->fetch();
                    if(!empty($mbr_row))
                        if($mbr_row['usergroupid']>=2)
                            $nScientists++;
                    // determine how many devices the user has
                    $dev_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['HARDWARE']." WHERE uid=".$id;
                  //  echo("<p>".$dev_sql."<p>" );
                    $dev_result = $db->query($dev_sql);
                    $dev_rows = $dev_result->fetchAll(PDO::FETCH_ASSOC);
                    $nDevices+=count($dev_rows);
                }
                $control = $nDevices - $nScientists * $CONFIG['MISC']['SC_TRESHOLD'];
             //   echo("<p>".$nDevices."<p>" );
             //   echo("<p>".$nScientists."<p>" );
             //   echo("<p>".$control."<p>" );
                if($control >= $CONFIG['MISC']['SC_TRESHOLD']){
                  ?>
                  <li><h3>Get scientist credentials today!</h3></li>
                  <?php
                }
                  else{
                      ?>
                      <li><a href="ucp.php?m=promo">Request scientist account</a></li>
            <?php
                  }  
                }
                else{
                    ?>
                    <li><a href="ucp.php?m=promo">Request scientist account</a></li>
                    <?php
            }
        }
             ?>
    </ul>
</div>

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="page_content">
                    <div class="post">
                        <div class="entry">
                           
                        <?php 
                          if(isset($USER_DEVICES)){
                            if(!empty($USER_DEVICES)){
                              ?>
                                <div class="list_devices">
                                 <table border="4" >
                                 <tr><th>Device</th><th>Android version</th><th>Sensors made available (filter)</th></tr>
                                 <?php

                                  // user has got some devices
                                  foreach($USER_DEVICES as $device){
                                     echo '<tr><td>'. $device['deviceid'] .'</td><td>'. $device['androidversion'] .'</td><td>'. substr($device['filter'], 1,-1) .'</td></tr>'; 
                                  }
                                  
                                 ?>
                                 </table>
                                </div>
                            <?php    
                            }else{
                                ?>
                                <div class="list_devices">
                                 <table>
                                 <tr><th>Devices:</th></tr>
                                 <tr><td>Your device list is empty.</td></tr> 
                                 </table>
                                </div> 
                                <?php
                            }
                          }
                            if($MODE == 'ADMIN' && !isset($_POST['pending_requests'])){
                            ?>
                            <div class="users_wanting_scientist">
                                <form action="ucp.php?m=admin" method="post">
                                    <table>
                                      <tr><th>Users that wanting permission to be a scientiest:</th></tr>
                                      <?php
                                        
                                        if(!empty($USERS_SCIENTIST_LIST)){
                                      
                                            foreach($USERS_SCIENTIST_LIST as $user){
                                                
                                            ?>
                                                <tr><td><?php echo $user['firstname'] ." ". $user['lastname']; ?></td><td>Accept:<input type="checkbox" name="pending_requests[]" value="<?php echo $user['hash']; ?> " /></td></tr>        
                                            <?php
                                            }
                                         ?>
                                         
                                         <tr><td>&nbsp;</td><td><button>Give access</button></td></tr>
                                         
                                         <?php   
                                            
                                        }else{
                                            echo "<tr><td>No requests.</td></tr>";
                                        }
                                      ?>
                                      </table>
                                 </form>
                            </div>
                            
                            <?php
                            }
                            
                            if($MODE == 'UPLOAD' && !isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                        ?>

                            <form action="upload.php" method="post" enctype="multipart/form-data" class="upload_form">
                              <p>Program name (title):</p>
                              <input type="text" name="apk_title" />
                              <p>Program android version:</p>
                              <input type="text" name="apk_android_version" />
                              <p>Program description:</p>
                              <textarea cols="30" rows="6" name="apk_description"></textarea>
                              <p>My program uses following sensors:</p>
                              <ul>
                                  <li><img src="./images/sensors/medium/accelerometer_sensor_best_reduction.png" alt="Accelerometer" title="Accelerometer"><input type="checkbox" name="sensors[]" value="1" /></li>
                                  <li><img src="./images/sensors/medium/magnetic_field_sensor_plus_minus_best_reduction.png" alt="Magnetic field" title="Magnetic field sensor"><input type="checkbox" name="sensors[]" value="2" /></li>
                                  <li><img src="./images/sensors/medium/orientation_sensor_best_reduction.png" alt="Orientation" title="Orientation sensor"><input type="checkbox" name="sensors[]" value="3" /></li>
                                  <li><img src="./images/sensors/medium/gyroscope_sensor_best_reduction.png" alt="Gyroscope" title="Gyroscope sensor"><input type="checkbox" name="sensors[]" value="4" /></li>
                                  <li><img src="./images/sensors/medium/light_sensor_best_reduction.png" alt="Light" title="Light sensor"><input type="checkbox" name="sensors[]" value="5" /></li>
                                  <li><img src="./images/sensors/medium/pressure_sensor_best_reduction.png" alt="Pressure" title="Pressure sensor"><input type="checkbox" name="sensors[]" value="6" /></li>
                                  <li><img src="./images/sensors/medium/temperature_sensor_best_reduction.png" alt="Temperature" title="Temperature sensor"><input type="checkbox" name="sensors[]" value="7" /></li>
                                  <li><img src="./images/sensors/medium/proximity_sensor_best_reduction.png" alt="Proximity" title="Proximity sensor"><input type="checkbox" name="sensors[]" value="8" /></li>
                                  <li><img src="./images/sensors/medium/gravity_sensor_best_reduction.png" alt="Gravity" title="Gravity sensor"><input type="checkbox" name="sensors[]" value="9" /></li>
                                  <li><img src="./images/sensors/medium/linear_acceleration_sensor_best_reduction.png" alt="Linear acceleration" title="Linear acceleration sensor"><input type="checkbox" name="sensors[]" value="10" /></li>
                                  <li><img src="./images/sensors/medium/rotation_sensor_best_reduction.png" alt="Rotation" title="Rotation sensor"><input type="checkbox" name="sensors[]" value="11" /></li>
                                  <li><img src="./images/sensors/medium/humidity_w_percent_sensor_best_reduction.png" alt="Humidity" title="Humidity sensor"><input type="checkbox" name="sensors[]" value="12" /></li>
                                  <li><img src="./images/sensors/medium/ambient_temperature_sensor_best_reduction.png" alt="Ambient temperature" title="Ambient temperature sensor"><input type="checkbox" name="sensors[]" value="13" /></li>
                              </ul>
                             
                             <script type="text/javascript">
                                $(document).ready(function(){
                                    
                                    $('.accel').click(function(){
                                        $('.accel').toggle();
                                        $('.accel_pressed').toggle();
                                        $(this).parent().find(':checkbox').attr("checked", true);
                                    });
                                    
                                    $('.accel_hide').click(function(){
                                        $('.accel').toggle();
                                        $('.accel_pressed').toggle();
                                        $(this).parent().find(':checkbox').attr("checked", false);
                                    });
                                    
                                    $('.user_apk_restriction').find('input[name=number_restricted_users]').attr('maxlength', 6);
                                    
                                    $('.user_apk_restriction').find('input[name=restrict_users_number]').change(
                                        function() {
                                            if ($(this).is(':checked')) {
                                                $('.user_apk_restriction').find('input[name=number_restricted_users]').removeAttr('disabled');
                                            } else {
                                                $('.user_apk_restriction').find('input[name=number_restricted_users]').attr('disabled', true);
                                                $('.user_apk_restriction').find('input[name=number_restricted_users]').val('');
                                            }
                                    });   
                            
                                });
                                </script>
                              
                              <div class="user_apk_restriction">
                                  <input type="checkbox" name="restrict_users_number" value="1" />Restrict number of users
                                  <input type="text" name="number_restricted_users" disabled="disabled" />
                              </div>
                              
                              <label for="file">Select a file:</label> 
                              <input type="file" name="userfile" id="file">
                              <p>Click Upload button to upload your apk</p><button>Upload</button>
                            </form>
                        <?php
                            }
                            
                            // there WAS some upload
                            if($MODE == 'UPLOAD' && isset($_GET['res']) && !empty($_GET['res'])){
                                
                                switch($UPLOAD_RESULT){
                                    
                                    // successful upload
                                    case 1:
                                        ?>
                                        
                                        <div class="upload_successful">
                                            <p>Your file was successfully uploaded!</p>
                                        </div>

                                        <?php
                                        break;
                                        
                                    // failed upload.
                                    case 0:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>That filetype not allowed. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                    
                                    // failed upload. Filetype fail
                                    case 2:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>That filetype not allowed. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                        
                                    // failed upload. File is too large
                                    case 3:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>This file is too large. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                       
                                    // failed upload. File is too large 
                                    case 4:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>We can't store that file, because of directory permissions. Please contact administrator.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                        
                                    // failed upload. someone trying to do some dirty stuff
                                    default:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>Trying to hax, br0?</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                }
                            }
                            
                            if($MODE == 'GROUP'){
                                if($groupname != null){
                                    echo("<h3> You are currently member of ".$groupname."</h3>");
                                    ?>
                                    <form action=ucp.php?m=leave method="post" class="leave_group">
                                        <button>Leave</button>
                                        <?php
                                }
                                else{ ?>
                                
                                    <h3>Join a research group or found one</h3>
                                    <form action=ucp.php?m=join enctype="multipart/form-data" method="post" class="join_group">
                                        <input type="radio" name="join_create" value="join" class="radio_join" />Join<br>
                                        <input type="radio" name="join_create" value="create" class="radio_create" />Create                                        
                                        <p>Enter the name of the research group<p>
                                        <input type="text" name="group_name" />
                                        <p>Enter the password of the group<p>
                                        <input type="text" name="group_pwd" />
                                        <button>OK</button>
                                    </form>
                                    
                                    <script type="text/javascript">
                                $(document).ready(function(){
                                    
                                    $('.radio_join').attr('checked', true);
                                    $('.join_group').find(':button').text('Join!');
                                    
                                    $('.radio_join').click(function(){
                                        $('.join_group').find(':button').text('Join!');
                                    });
                                    
                                    $('.radio_create').click(function(){
                                        $('.join_group').find(':button').text('Create!');
                                    });
                                       
                            
                                });
                                </script>
                                    <?php
                                }
                            }
                            // THE USER HAS CLICKED THE JOIN BUTTON
                            if($MODE == 'JOIN'){
                                // ###########
                                switch($jcstatsus){
                                    case 1 :
                                        echo("<h3>You joined ".$groupname."<h3>");
                                        break;
                                    case 2 : 
                                        echo("<h4>".$groupname." already exists! Specify another name for your research group<h4>");
                                        break;
                                    case 3 : 
                                        echo("<h3>You created ".$groupname."<h3>");
                                        break;
                                    default:
                                        echo("<h3>Invalid group-name and/or password ".$groupname."<h3>");
                                }
                                echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=".$CONFIG['PROJECT']['MOSES_URL']."ucp.php?m=group\">");
                            }
                            
                            // THE USER HAS CLICKED THE LEAVE BUTTON
                            if($MODE == 'LEAVE'){
                                echo("<h3>You left ".$groupname."<h3>");
                                echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=".$CONFIG['PROJECT']['MOSES_URL']."ucp.php?m=group\">");
                                }
                            
                            // user wants a listing of APK files
                            if($MODE == 'LIST' && isset($LIST_APK)){
                                
                            ?>
                                <script type="text/javascript">
                                $(document).ready(function(){

                                    $('div.slidepanel').hide();
                                    
                                    $('.slidebtn').click(function(e){

                                        $('div.slidepanel').stop(true, false).slideUp();
                                        $(e.target).closest('span').next('.slidepanel').stop(true, false).slideDown();
                                       /* $(this).find('#expose').siblings().each(function(){
                                            $(this).html('[+]');
                                        }); */
                                        //$(this).find('#expose').html('[-]');
                                        //$('.slidepanel').slideToggle('slow');
                                        //$(this).toggleClass('active'); return false;
                                    });
                                    
                                    /*     
                                    $('.slidebtn').toggle(function() {
                                        $(this).find('#expose').html('[-]');    
                                    }, function() {
                                        $(this).find('#expose').html('[+]');        
                                    });*/
                                    
                                  /*  $('#expose').each(function(){
                                        var value = $(this).html();
                                        $(this).html((value == '[+]') ? '[+]' : '[-]');
                                    });*/

                            
                                });
                                </script>
                                
                                <div class="apk_list">
                                 <p>Your uploaded files (click to expose):</p>
                                 <?php

                                  // we found some APKs
                                  if($LIST_APK == 1){
                                      foreach($apk_listing as $row){
                                         //echo '<tr><td><a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk">'. $row['apkname'] .'</a></td><td>'. $remove_url .'</td></tr>'; 
                                         echo '<span class="slide"><a href="#" title="More info" class="slidebtn">'. $row['apkname'] .' <span id="expose">[+]</span></a></span>';
                                         echo '<div class="slidepanel">';
                                         echo '<p>Information:</p>';
                                         echo '<p>'. $row['description'] .'</p>';
                                         echo '<p>Sensors:</p>';
                                         echo '<p>'. $row['sensors'] .'</p>';
                                         echo 'Download: <a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk">'. $row['apkname'] .'</a>&nbsp;&nbsp;&nbsp;<a href="ucp.php?m=list&remove='. $row['apkhash'] .'" title="Remove APK">Remove</a>';
                                         echo '</div>';
                                         echo '<br />';
                                      }   
                                  }else{
                                      echo "<span>Nothing found :(</span>"; 
                                  }

                                 ?>
                                </div>
                            <?php     
                            }
                            
                            if($MODE == 'PROMO' && !isset($_POST['promo_sent']) 
                                                && (!isset($USER_ALREADY_ACCEPTED) || !isset($USER_PENDING))){    
                            ?> 

                            <form action="ucp.php?m=promo" method="post" class="promo_form">
                               <p>
                                 <fieldset>
                                    <legend>Become a scientist!</legend>
                                    <label for="telephone" >Telephone:</label>
                                    <div class="clear"></div>
                                    <input type="text" name="telephone" id="telephone" maxlength="10" />
                                    <div class="clear"></div>
                                    <label for="reason" >Reason? Tell us why, pls (*):</label>
                                    <div class="clear"></div>
                                    <textarea cols="30" rows="10" name="reason" id="reason"></textarea>
                                    <div class="clear"></div>
                                    <input type="hidden" name="promo_sent" id="promo_sent" value="1" />
                                    <input type="submit" name="submit" value="Send" />
                                 </fieldset> 
                               <p>
                            </form>   
                                
                             <?php   
                            }
                            
                            if($MODE == 'PROMO' && isset($_POST['promo_sent'])){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>Your scientist application was sent. Thank you for interesting in that!</p>
                                </div>
                                
                                <?php
                            }
                            
                            if($MODE == 'PROMO' && isset($USER_PENDING) && $USER_PENDING == 1
                                                && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED != 1){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>Your application to become a scientist was already sent to us.</p>
                                </div>
                                
                                <?php
                            }
                            
                            if($MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 1){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>You are already a scientist!</p>
                                </div>
                                
                                <?php
                            }
                            
                            // nobody wants you as scientist
                            if($MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 0 
                                                && isset($USER_PENDING) && $USER_PENDING == 0){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>Sorry, but admin won't you as scientist and rejected your application. :(</p>
                                </div>
                                
                                <?php
                            }
                            
                        ?>
                           
                        </div>
                    </div>
                    <div style="clear: both;">&nbsp;</div>
                </div>
                <!-- end #content -->
                <div style="clear: both;">&nbsp;</div>
            </div>
        </div>
    </div>
    <!-- end #page -->
</div>

<?php  
  include_once("./include/_login_slider.php");
    
  include_once("./include/_footer.php");  
?>