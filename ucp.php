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
$group_members_count = 0;
$group_device_count = 0;

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
$SHOW_UPDATE_PAGE = 0;
$apk_to_update = array();

$scientist_succses = 0; // 1 only if the user has gain instant scientist credentials, use to check if someone is trying something nasty

$sensors_ultrasmall_mapping = array(1 => array('accelerometer_sensor.png', 'Accelerometer sensor'),
                                    array('magnetic_field_sensor.png', 'Magnetic field sensor'),
                                    array('orientation_sensor.png', 'Orientation sensor'),
                                    array('gyroscope_sensor.png', 'Gyroscope sensor'),
                                    array('light_sensor.png', 'Light sensor'),
                                    array('pressure_sensor.png', 'Pressure sensor'),
                                    array('temp_sensor.png', 'Temperature sensor'),
                                    array('proximity_sensor.png', 'Proximity sensor'),
                                    array('gravity_sensor.png', 'Gravity sensor'),
                                    array('linear_acceleration_sensor.png', 'Linear acceleration sensor'),
                                    array('rotation_sensor.png', 'Rotation sensor'),
                                    array('humidity_sensor.png', 'Humidity sensor'),
                                    array('ambient_temp_sensor.png', 'Ambient temperature sensor'));
                                    
$sensors_info = array(array('accelerometer', 'accelerometer_pressed', 'Accelerometer sensor'),
                    array('magnetic_field', 'magnetic_field_pressed', 'Magnetic field sensor'),
                    array('orientation', 'orientation_pressed', 'Orientation sensor'),
                    array('gyroscope', 'gyroscope_pressed', 'Gyroscope sensor'),
                    array('light', 'light_pressed', 'Light sensor'),
                    array('pressure', 'pressure_pressed', 'Pressure sensor'),
                    array('temperature', 'temperature_pressed', 'Temperature sensor'),
                    array('proximity', 'proximity_pressed', 'Proximity sensor'),
                    array('gravity', 'gravity_pressed', 'Gravity sensor'),
                    array('linear_acceleration', 'linear_acceleration_pressed', 'Linear acceleration sensor'),
                    array('rotation', 'rotation_pressed', 'Rotation sensor'),
                    array('humidity', 'humidity_pressed', 'Humidity sensor'),
                    array('ambient_temperature', 'ambient_temperature_pressed', 'Ambient temperature sensor'));
                    
$API_VERSION = array(array(8, 'API 8: "Froyo" 2.2.x'),
                     array(9, 'API 9: "Gingerbread" 2.3.0 - 2.3.2'),
                     array(10, 'API 10: "Gingerbread" 2.3.3 - 2.3.7'),
                     array(11, 'API 11: "Honeycomb" 3.0'),
                     array(12, 'API 12: "Honeycomb" 3.1'),
                     array(13, 'API 13: "Honeycomb" 3.2.x'),
                     array(14, 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2'),
                     array(15, 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4'));
                                    
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
                       
                       include_once("./include/functions/dbconnect.php");
                       
                       $sql_upload = "SELECT rgroup 
                                      FROM ". $CONFIG['DB_TABLE']['USER'] ." 
                                      WHERE userid=". $_SESSION['USER_ID'];
                                      
                       $result = $db->query($sql_upload);
                       $row = $result->fetch();
                       
                       $groupname = $row['rgroup'];
                       $_SESSION['RGROUP'] = $groupname;
                       
                       break;
                       
        case 'DEVICE':
        
                    if(isset($_GET['remove'])){
                        
                        $DEVICE_ID = preg_replace("/\D/", "", $_GET['remove']);
                        
                        if(preg_match('/^[0-9]+$/', $DEVICE_ID)){
                                  
                           include_once("./include/functions/dbconnect.php");
                           
                           // remove entry from DB 
                           $sql = "DELETE FROM ". $CONFIG['DB_TABLE']['HARDWARE'] ."  
                                          WHERE uid = ". $_SESSION['USER_ID'] . " 
                                          AND hwid = '". $DEVICE_ID ."'";
                              
                           $db->exec($sql);
                           
                           echo "<meta http-equiv='refresh' content='0;URL=". $_SERVER['HTTP_REFERER'] ."'>";
                           
                        }else{
                            echo "<meta http-equiv='refresh' content='0;URL=". $_SERVER['HTTP_REFERER'] ."'>"; 
                        }
                        
                    }else{
                       echo "<meta http-equiv='refresh' content='0;URL=". $_SERVER['HTTP_REFERER'] ."'>"; 
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
                   
        case 'UPDATE':
        
                    $MODE = 'UPLOAD';
                    
                    if(isset($_GET['id'])){
                                 
                        $APK_ID = preg_replace("/\D/", "", $_GET['id']);
                        
                        if(preg_match('/^[0-9]+$/', $APK_ID)){
                                  
                           include_once("./include/functions/dbconnect.php");
                            
                           $sql = "SELECT * 
                                   FROM apk 
                                   WHERE apkid = ". $APK_ID ." AND userid = ". $_SESSION["USER_ID"];
                            
                           $result = $db->query($sql);
                           $apk_to_update = $result->fetch();
                           
                           if(!empty($apk_to_update)){
                               $SHOW_UPDATE_PAGE = 1; 
                           }else{
                               $SHOW_UPDATE_PAGE = 0;
                           } 
                        }else{
                            $SHOW_UPDATE_PAGE = 0;
                        }
                    }else{
                        $SHOW_UPDATE_PAGE = 0;    
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
                          
                          echo "<meta http-equiv='refresh' content='0;URL=". $_SERVER['HTTP_REFERER'] ."'>";     
                           
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
            
            include_once("./include/functions/dbconnect.php");
            
            // obtain the name of group the user is currently in (if any)
            $group_sql = "SELECT rgroup 
                          FROM ".$CONFIG['DB_TABLE']['USER']. " 
                          WHERE userid=" . $_SESSION['USER_ID'];
                          
            $group_result = $db->query($group_sql);
            $group_row = $group_result->fetch();
            $groupname = $group_row['rgroup'];
            
            if(!empty($group_row) && $groupname!=null){
                
                $groupname = $group_row['rgroup'];
                
                $sql = "SELECT members 
                        FROM ". $CONFIG['DB_TABLE']['RGROUP'] ." 
                        WHERE name = '". $groupname ."'";
                        
                 $result = $db->query($sql);
                 $row = $result->fetch();
                 
                 $group_members_count = count(json_decode($row['members']));
                 
                 $user_array = json_decode($row['members']);
                 
                 foreach($user_array as $user){
                     
                     $sql = 'SELECT hwid 
                             FROM '. $CONFIG['DB_TABLE']['HARDWARE'] .' 
                             WHERE uid = '. $user;
                             
                     $result = $db->query($sql);
                     $row = $result->fetchAll();
                     
                     if(!empty($row)){
                         $group_device_count += count($row);
                     }
                 }
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
        // USER WANTS TO BE A SCIENTIST (INSTANTLY)
        case 'INSTANT':
            $MODE ='INSTANT';
            //#####
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
                if($control >= $CONFIG['MISC']['SC_TRESHOLD']){
                    $sql_sci = "UPDATE ".$CONFIG['DB_TABLE']['USER']. " SET usergroupid=2 WHERE userid=".$_SESSION['USER_ID'];
                    $db->exec($sql_sci);                    
                    $scientist_succses = 1;
                    $_SESSION["GROUP_ID"]=2;
                }
            }
            break;
            
            //#####
        
        
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
                  <li><a href="ucp.php?m=instant">Get scientist credentials today!</a></li>
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
                            
                            echo '<h3>Your devices</h3>';
                            echo '<br /><br />';  
                              
                            if(!empty($USER_DEVICES)){
                                
                                // user has got some devices
                                foreach($USER_DEVICES as $device){
                                    ?>
                                    <div class="sensor_box">
                                        <ul>
                                            <li><div style="font-weight: bold;">Device name:</div><div style="font-weight: bold;" class="sensor_box_name"><?php
                                                echo $device['deviceid'];                                       
                                            ?></div>
                                            </li>
                                            <li><?php
                                            echo '<a href="ucp.php?m=device&remove='. $device['hwid'] .'" title="Remove device" class="sensor_box_api_remove"></a>';                                       
                                        ?>
                                            </li>
                                        </ul>
                                        <div class="sensor_info">
                                            <p style="font-weight: bold">Selected sensors (filter):</p>
                                            <ul class="sensor_container_f"><?php
                                               $sensor_array = json_decode($device['sensors']);
                                           
                                           if(count($sensor_array) != 0){
                                               
                                               // we will make some lines of sensors
                                               if(count($sensor_array) <= 7){
                                               
                                                   foreach($sensor_array as $sensor_number){
                                                      echo '<li><img src="images/sensors/ultrasmall/'. 
                                                            $sensors_ultrasmall_mapping[$sensor_number][0] .'" alt="'. 
                                                            $sensors_ultrasmall_mapping[$sensor_number][1] .'" title="'. 
                                                            $sensors_ultrasmall_mapping[$sensor_number][1] .'" /></li>'; 
                                                   }
                                               }else{
                                                   
                                                   $once = true;
                                                   for($i=0; $i < count($sensor_array); $i++){
                                                      if($i < 7){ 
                                                          echo '<li><img src="images/sensors/ultrasmall/'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][0] .'" alt="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" title="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" /></li>'; 
                                                      }else{
                                                          // execute only once ;)
                                                          if($once){
                                                              echo '</ul><ul class="sensor_container_s">';
                                                              $once = false;
                                                          }
                                                          
                                                          echo '<li><img src="images/sensors/ultrasmall/'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][0] .'" alt="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" title="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" /></li>';
                                                      }
                                                   }
                                               }
                                               
                                           }else{
                                               echo '<li><p>No filter set.</p></li>';
                                           }
                                                                                 
                                          ?></ul>
                                          <div class="sensor_box_api"><div style="font-weight: bold">Android API version:</div>
                                                <div style="font-weight: bold;"><?php
                                                echo $device['androidversion'];                                       
                                            ?></div></div>
                                        </div>
                                    </div>
                                    <?php
                                }
                               
                            }else{
                                ?>
                                <div class="sensor_box">
                                    <ul>
                                        <li>
                                        <div style="padding-top: 35px;">Your device list is empty.</div>
                                        </li>
                                    </ul>
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
                            
                            if($SHOW_UPDATE_PAGE != 1 && $MODE == 'UPLOAD' && !isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                        ?>

                            <form action="upload.php" method="post" enctype="multipart/form-data" class="upload_form">
                              <p>Program name (title):</p>
                              <input type="text" name="apk_title" />
                              <p>Version of your program (can be any alphanumeric string):</p>
                              <input type="text" name="apk_version" />
                              <p>Lowest android version needed for my program to run:</p>
                              <select name="apk_android_version">
                                <?php
                                     
                                for($i=0; $i<count($API_VERSION); $i++){
                                    echo '<option value="'. $API_VERSION[$i][0] .'">'. $API_VERSION[$i][1] .'</option>';    
                                } 
                                
                                ?>
                              </select>                              
                              <p>Program description:</p>
                              <textarea cols="30" rows="6" name="apk_description"></textarea>
                              <p style="margin: 20px 0;">My program uses following sensors:</p>
                              <ul><?php
                                                        
                               for($i=0; $i < count($sensors_info); $i++){   
                                  ?><li>
                                    <div class="<?php echo $sensors_info[$i][0]; ?>" title="<?php echo $sensors_info[$i][2]; ?>"></div>
                                    <div class="<?php echo $sensors_info[$i][1]; ?>" title="<?php echo $sensors_info[$i][2]; ?>" style="display: none;"></div>
                                    <input type="checkbox" name="sensors[]" value="<?php echo $i+1; ?>" />
                                   </li>
                                   <?php
                               }    
                               
                               ?>
                              </ul>

                              <div class="user_apk_restriction">
                                  <input type="checkbox" name="restrict_users_number" value="1" /><span style="padding-left: 5px;">Make user study</span><br /><br />
                                  <span style="margin-right: 10px;">Restrict number of devices:</span><input type="text" name="number_restricted_users" disabled="disabled" />
                                  <br /><br /><?php
                                   if(!empty($groupname)){
                                     ?>  
                                   <div style="margin: 15px 0;"><span style="margin-right: 10px;">Send only to my group</span><input type="checkbox" name="send_only_to_my_group" value="1" disabled="disabled" class="send_only_to_my_group" /></div>
                                   <?php
                             
                                   }
                                   ?>
                              </div>
                              
                              <label for="file">Select a file:</label> 
                              <input type="file" name="userfile" id="file" style="margin: 15px 0;">
                              <p style="margin-bottom: 10px;">Click Upload button to upload your apk</p><button>Upload</button>
                              <p style="margin-top: 50px;"></p>
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
                                
                                if(!empty($groupname)){ 
                                    echo '<h3>You are currently member of research group</h3>';
                                    echo '<div class="group_name">'. $groupname .'</div>';           
                                    ?>
                                    <div class="group_stats">
                                     <p style="font-weight: bold; font-size: large; margin-bottom: 8px; margin-left: 20px;">Statistics</p>
                                        <ul>
                                            <li>
                                                <div>Your group has<div style="font-weight: bold;"><?php
                                                  echo $group_members_count;                 
                                                ?></div><?php
                                                  echo ($group_members_count == 1 ? 'member' : 'members');                           
                                                ?>!</div>
                                            </li>
                                            <li>
                                                <div>Your group has<div style="font-weight: bold;"><?php
                                                  echo $group_device_count;                 
                                                ?></div><?php
                                                  echo ($group_device_count == 1 ? 'device' : 'devices');                           
                                                ?>!</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php
                                    ?>
                                    <form action=ucp.php?m=leave method="post" class="leave_group">
                                        <button>Leave</button>
                                    </form>
                                    
                                        <?php
                                }else{ ?>
                                
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
                                    <?php
                                }
                            }
                            
                            // THE USER HAS CLICKED THE JOIN BUTTON
                            if($MODE == 'JOIN'){
                                // ###########
                                switch($jcstatsus){
                                    case 1 :
                                        echo("<h3>You joined research group<h3>");
                                        echo '<div class="group_name">'. $groupname .'</div>';
                                        break;
                                    case 2 : 
                                        echo("<h3>Group already exists! Specify another name for your research group<h3>");
                                        break;
                                    case 3 : 
                                        echo '<h3>You successfully created research group<h3>';
                                        echo '<div class="group_name">'. $groupname .'</div>';
                                        break;
                                    default:
                                        echo("<h3>Invalid group name and/or password!<h3>");
                                }
                                echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=".$CONFIG['PROJECT']['MOSES_URL']."ucp.php?m=group\">");
                            }
                            
                            // THE USER HAS CLICKED THE LEAVE BUTTON
                            if($MODE == 'LEAVE'){
                                echo '<h3>You successfully left from research group</h3>';
                                echo '<div class="group_name">'. $groupname .'</div>';
                                echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=".$CONFIG['PROJECT']['MOSES_URL']."ucp.php?m=group\">");
                                }
                            
                            // THE USER WANTS TO BE A SCIENTIST, INSTANTLY
                            if($MODE == 'INSTANT'){
                                if($scientist_succses == 1){
                                    echo("<h3>Congrats! You have gained scientist credentials!<h3>");
                                    echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=".$CONFIG['PROJECT']['MOSES_URL']."ucp.php\">");
                                }
                                else{
                                    echo("<h3>Y U DO THIS? br0<h3>");
                                    echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=".$CONFIG['PROJECT']['MOSES_URL']."ucp.php\">");
                                }
                            }
                            
                            // user wants a listing of APK files
                            if($MODE == 'LIST' && isset($LIST_APK)){
                                
                              echo '<h3>Your apps</h3>';  
                              echo '<br /><br />';
                                
                              // we found some APKs
                              if($LIST_APK == 1){
                                  
                                  foreach($apk_listing as $row){
                                   ?>   
                                  <div class="sensor_box">
                                    <ul>
                                        <li><div style="font-weight: bold;">Name:</div><div style="font-weight: bold;" class="sensor_box_name"><?php
                                            echo $row['apktitle'];                                       
                                        ?></div>
                                        </li>
                                        <li><div class="down_remove_links"><?php
                                            echo '<a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk" class="bt_download"></a>';
                                            echo '<a href="ucp.php?m=update&id='. $row['apkid'] .'" title="Update APK" class="bt_upload"></a>';
                                            echo '<a href="ucp.php?m=list&remove='. $row['apkhash'] .'" title="Remove APK" class="bt_remove"></a>';                                       
                                        ?></div>
                                        </li>
                                    </ul>
                                    <div class="sensor_info">
                                        <p style="font-weight: bold">Required sensors:</p>
                                        <ul class="sensor_container_f"><?php
                                           $sensor_array = json_decode($row['sensors']);
                                           
                                           if(count($sensor_array) != 0){
                                               
                                               // we will make some lines of sensors
                                               if(count($sensor_array) <= 7){
                                               
                                                   foreach($sensor_array as $sensor_number){
                                                      echo '<li><img src="images/sensors/ultrasmall/'. 
                                                            $sensors_ultrasmall_mapping[$sensor_number][0] .'" alt="'. 
                                                            $sensors_ultrasmall_mapping[$sensor_number][1] .'" title="'. 
                                                            $sensors_ultrasmall_mapping[$sensor_number][1] .'" /></li>'; 
                                                   }
                                               }else{
                                                   
                                                   $once = true;
                                                   for($i=0; $i < count($sensor_array); $i++){
                                                      if($i < 7){ 
                                                          echo '<li><img src="images/sensors/ultrasmall/'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][0] .'" alt="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" title="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" /></li>'; 
                                                      }else{
                                                          // execute only once ;)
                                                          if($once){
                                                              echo '</ul><ul class="sensor_container_s">';
                                                              $once = false;
                                                          }
                                                          
                                                          echo '<li><img src="images/sensors/ultrasmall/'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][0] .'" alt="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" title="'. 
                                                                $sensors_ultrasmall_mapping[$sensor_array[$i]][1] .'" /></li>';
                                                      }
                                                   }
                                               }
                                               
                                           }else{
                                               echo '<li><p>No sensors set.</p></li>';
                                           }
                                     
                                      ?></ul>
                                      <div class="apk_installed_on">App installed on<div style="font-weight: bold; padding: 0 5px;"><?php
                                                  echo $row['participated_count'];                 
                                                ?></div><?php
                                                  echo ($row['participated_count'] == 1 ? 'device' : 'devices');                           
                                                  if($row['participated_count'] != 0){
                                                     echo '!'; 
                                                  }
                                                ?></div>
                                      <div class="apk_description_trigger">description <div class="descr_arrow">-></div></div>
                                      <div class="apk_description">
                                    <p style="font-weight: bold">App description</p>
                                    <ul><li><p class="apk_descr_text"><?php
                                       
                                       if(!empty($row['description'])){
                                           echo $row['description'];
                                       }else{
                                           echo 'No description.';
                                       }
                                                                         
                                  ?></p></li></ul>
                                </div>
                                    </div>
                                  
                                </div>
                                     <?php 
                                      
                                  }   
                              }else{
                                  ?>
                                  
                                  <div class="sensor_box">
                                    <ul>
                                        <li>
                                            <div>You have no apps.</div>
                                        </li>
                                    </ul>
                                </div>
                                
                                <?php
                              }

                            }
                            
                            /********************************************
                            *********** UPDATE MY APK PAGE **************
                            *********************************************/
                            
                            if(isset($SHOW_UPDATE_PAGE) && $SHOW_UPDATE_PAGE == 1){
                                 ?>
                                 
                                 <form action="update.php" method="post" enctype="multipart/form-data" class="upload_form">
                                  <p>Program name (title):</p>
                                  <h4><?php
                                      
                                      echo $apk_to_update['apktitle'];         
                                      
                                      ?></h4>
                                  <p>Version of your program (can be any alphanumeric string):</p>
                                  <input type="text" name="apk_version" value="<?php
                                    echo $apk_to_update['apk_version'];                                                    
                                   ?>" />
                                  <p>Lowest android version needed for my program to run:</p>
                                  <select name="apk_android_version">
                                    <?php
                                     
                                    $_SESSION['APKID'] = $apk_to_update['apkid'];
                                     
                                    for($i=0; $i<count($API_VERSION); $i++){
                                        echo '<option value="'. $API_VERSION[$i][0] .'"'. 
                                        ($apk_to_update['androidversion'] == $API_VERSION[$i][0] ? ' selected="selected" ' : '') 
                                        .'>'. $API_VERSION[$i][1] .'</option>';    
                                    } 
                                    
                                    ?>
                                  </select>                              
                                  <p>Program description:</p>
                                  <textarea cols="30" rows="6" name="apk_description"><?php
                                    echo $apk_to_update['description'];
                                                                       
                                  ?></textarea>
                                  <p style="margin: 20px 0;">My program uses following sensors:</p>
                                  <ul><?php
                                  
                                      $apk_to_update_sensors = json_decode($apk_to_update['sensors']);
                                  
                                      for($i=0; $i < count($sensors_info); $i++){
                                           
                                        echo '<li>';
                                        if(in_array(($i+1), $apk_to_update_sensors)){
                                            
                                            // if sensor was selected, check and select it here
                                            
                                            echo '<div class="'. $sensors_info[$i][0] .'" title="'. $sensors_info[$i][2] .'" style="display: none;"></div>';
                                            echo '<div class="'. $sensors_info[$i][1] .'" title="'. $sensors_info[$i][2] .'"></div>';
                                            echo '<input type="checkbox" name="sensors[]" value="'. ($i+1) .'" checked="checked" />';
                                        }else{
                                           
                                            // here is no selection of sensor
                                            
                                            echo '<div class="'. $sensors_info[$i][0] .'" title="'. $sensors_info[$i][2] .'"></div>';
                                            echo '<div class="'. $sensors_info[$i][1] .'" title="'. $sensors_info[$i][2] .'" style="display: none;"></div>';
                                            echo '<input type="checkbox" name="sensors[]" value="'. ($i+1) .'" />';
                                            }
                                        echo '</li>';
                                       } 
                                   ?>
                                  </ul>
      
                                  <label for="file">Select a file:</label> 
                                  <input type="file" name="userfile" id="file" style="margin: 15px 0;">
                                  <br /><br />
                                  <button>Update</button>
                                  <p style="margin-top: 50px;"></p>
                                </form>
                                 
                                 <?php
                            }
                            
                            
                            if($MODE == 'PROMO' && !isset($_POST['promo_sent']) 
                                                && (!isset($USER_ALREADY_ACCEPTED) || !isset($USER_PENDING))){    
                            ?> 

                            <form action="ucp.php?m=promo" method="post" class="promo_form">
                               
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