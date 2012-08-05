<?php
session_start();
ob_start();

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

$show_add_quest = false;
$apk_to_update = array();
$quest_db = array();
$quest_selected = array();
$num_sel_quest = 0;
$all_devices = array(); 

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

if(isset($_GET['selqst']))
{
  echo " JA !! ";
}                                  
// SWITCH USER CONTORL PANEL MODE
if(isset($_GET['m']))
{
    
    $RAW_MODE = strtoupper(trim($_GET['m']));
    $MODE = '';
    
    switch($RAW_MODE)
    {

        case 'ADDQUEST';
          if(isset($_GET['id']))
          {
            $apkid = preg_replace("/\D/", "", $_GET['id']);
            $show_add_quest = true;
            include_once("./include/functions/dbconnect.php");
            $sql = "SELECT * FROM apk_quest WHERE apkid=$apkid";
            $req = $db->query($sql);
            $quest_selected = $req->fetchAll();
            include_once("./include/functions/dbconnect.php");
            $sql = "SELECT * FROM questionnaire";
            $req = $db->query($sql);
            $quest_all = $req->fetchAll();
            $quest_db = array();
            foreach($quest_all as $quall)
            {
            	$add = 1;	
            	foreach($quest_selected as $selqu)
            		if($quall['questid'] == $selqu['questid'])
            			$add = 0;
            	if($add == 1)
            	{
            		$quest_db[] = $quall;
            	}
            }
            $num_sel_quest = 0;
          }
          break;

        case 'UPLOAD':
          if(isset($_REQUEST['next1']) || isset($_REQUEST['back2']) || isset($_REQUEST['next2'])
            || isset($_REQUEST['back3']) || isset($_REQUEST['create']))
          {
            $page1 = false;
            $page2 = false;
            $page3 = false;
            if(isset($_REQUEST['back2']))
            {
              $page1 = true;
              include_once("./include/functions/dbconnect.php");
              $sql = "UPDATE temp set radioButton = '". ((isset($_POST['radioButton'])) ? $_POST['radioButton'] : "1")
              ."', startdate = '". ((isset($_POST['start_1_a']) && $_POST['start_1_a'] != 'yyyy-mm-dd') ? $_POST['start_1_a'] : null)
              ."', startcriterion = '". ((isset($_POST['start_2_a'])) ? $_POST['start_2_a'] : "0")
              ."', radioButton1 = '". ((isset($_POST['radioButton1'])) ? $_POST['radioButton1'] : "1")
              ."', enddate = '". ((isset($_POST['end_1_b']) && $_POST['end_1_b'] != 'yyyy-mm-dd') ? $_POST['end_1_b'] : null)
              ."', runningtime = '". ((isset($_POST['end_2_b']) && $_POST['end_2_b'] != 'yyyy-mm-dd') ? $_POST['end_2_b'] : null)
              ."', maxdevice = '". ((isset($_POST['maxdevice'])) ? $_POST['maxdevice'] : NULL)
              ."', locked = '". ( !(isset($_POST['invite'])) || ((isset($_POST['invite']) && $_POST['invite'] == "0")) ? "1" : "0")
              ."', inviteinstall = '". ((isset($_POST['invite'])) ? $_POST['invite'] : "0")
              ."' WHERE userid = ".$_SESSION['USER_ID'];
              $db->exec($sql);
            }
            elseif(isset($_REQUEST['back3']) || isset($_REQUEST['next1']))
            {
              $page2 = true;
              if(isset($_REQUEST['next1']))
              {
                include_once("./include/functions/dbconnect.php");
                $sql = "UPDATE temp set apk_title = '".((isset($_POST['apk_title'])) ? $_POST['apk_title'] : "''")
                ."', description = '". ((isset($_POST['apk_description'])) ? $_POST['apk_description'] : "''")
                ."' WHERE userid = ".$_SESSION['USER_ID'];
                $db->exec($sql);
              }
              else
              {
                if(isset($_POST['sensors']) && is_array($_POST['sensors']) && count($_POST['sensors']) > 0)
                {
                    $RAW_SENSOR_LIST = $_POST['sensors'];
                    $SENSOR_LIST_STRING = '[';
                    foreach($RAW_SENSOR_LIST as $sensor)
                    {
                      $SENSOR_LIST_STRING .= $sensor .','; 
                    }
                    $SENSOR_LIST_STRING = substr($SENSOR_LIST_STRING, 0, -1) . ']';
                    
                }
                else
                {
                    $SENSOR_LIST_STRING = '[]';
                }

                include_once("./include/functions/dbconnect.php");
                $sql = "UPDATE temp set androidversion = '". ((isset($_POST['apk_android_version'])) ? $_POST['apk_android_version'] : '')
                ."' WHERE userid = ".$_SESSION['USER_ID'];
                $db->exec($sql);

                // there is a problem if nothing changes on sensors' selecting
                $sql = "UPDATE temp set sensors = '".$SENSOR_LIST_STRING."' WHERE userid = ".$_SESSION['USER_ID'];
                $db->exec($sql);
              }
            }
            elseif(isset($_REQUEST['next2']))
            {
              $page3 = true;
              include_once("./include/functions/dbconnect.php");
              $sql = "UPDATE temp set radioButton = '". ((isset($_POST['radioButton'])) ? $_POST['radioButton'] : "1")
              ."', startdate = '". ((isset($_POST['start_1_a']) && $_POST['start_1_a'] != '') ? $_POST['start_1_a'] : null)
              ."', startcriterion = '". ((isset($_POST['start_2_a'])) ? $_POST['start_2_a'] : "0")
              ."', radioButton1 = '". ((isset($_POST['radioButton1'])) ? $_POST['radioButton1'] : "1")
              ."', enddate = '". ((isset($_POST['end_1_b']) && $_POST['end_1_b'] != '') ? $_POST['end_1_b'] : null)
              ."', runningtime = '". ((isset($_POST['end_2_b']) && $_POST['end_2_b'] != 'yyyy-mm-dd') ? $_POST['end_2_b'] : null)
              ."', maxdevice = '". ((isset($_POST['maxdevice'])) ? $_POST['maxdevice'] : NULL)
              ."', locked = '". (!(isset($_POST['invite'])) || ((isset($_POST['invite']) && $_POST['invite'] == "0")) ? "1" : "0")
              ."', inviteinstall = '". ((isset($_POST['invite'])) ? $_POST['invite'] : "0")
              ."' WHERE userid = ".$_SESSION['USER_ID'];
              $db->exec($sql);
            }
            elseif(isset($_REQUEST['create']))
            {
              if(isset($_POST['sensors']) && is_array($_POST['sensors']) && count($_POST['sensors']) > 0)
              {
                $RAW_SENSOR_LIST = $_POST['sensors'];
                $SENSOR_LIST_STRING = '[';
                foreach($RAW_SENSOR_LIST as $sensor)
                {
                  $SENSOR_LIST_STRING .= $sensor .','; 
                }
                $SENSOR_LIST_STRING = substr($SENSOR_LIST_STRING, 0, -1) . ']';
                    
              }
              else
              {
                $SENSOR_LIST_STRING = '[]';
              }

              include_once("./include/functions/dbconnect.php");
              $sql = "UPDATE temp set androidversion = '". ((isset($_POST['apk_android_version'])) ? $_POST['apk_android_version'] : '')
              ."' WHERE userid = ".$_SESSION['USER_ID'];
              $db->exec($sql);

              // there is a problem if nothing changes on sensors' selecting
              $sql = "UPDATE temp set sensors = '".$SENSOR_LIST_STRING."' WHERE userid = ".$_SESSION['USER_ID'];
              $db->exec($sql);

              include_once("./upload.php");
            }   
          }
          else
          {
            $page1 = true;
            $page2 = false;
            $page3 = false;
            include_once("./include/functions/dbconnect.php");
            $sql = "SELECT * FROM temp WHERE userid = ". $_SESSION["USER_ID"];
            $req = $db->query($sql);
            $row = $req->fetch();
            if(empty($row))
            {
              $sql = "INSERT INTO temp (userid) VALUE(".$_SESSION['USER_ID'].")";
              $db->exec($sql);
            }
          }

          $MODE = 'UPLOAD';
          
           if(isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1)
           {
            
               $RAW_UPLOAD_RESULT = strtoupper(trim($_GET['res']));
               
               switch($RAW_UPLOAD_RESULT){
                   case "0":
                            $UPLOAD_RESULT = 0;  // file failed to upload
                            break;
                            
                   case "1":
                            $UPLOAD_RESULT = 1;  // file successfully uploaded
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
                               
                               // USER IS NOW IN A SCIENTIST GROUP
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
                 
                 $group_members_array = json_decode($row['members']);
                 if($group_members_array != null){
                     foreach($group_members_array as $user){
                         
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
            }
            break;
    
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
            $sql_update4;
            if(count($newMembers) == 0)
                $sql_update4 = "DELETE FROM ".$CONFIG['DB_TABLE']['RGROUP']." WHERE name='".$groupname."'";
            else{
                $newMembers = json_encode($newMembers);
                $sql_update4 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." SET members='".$newMembers."' WHERE name='".$groupname."'";
            }
            $db->exec($sql_update4);
            
            break;

        case 'INSTANT':
            $MODE ='INSTANT';
            $gr_sql = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $_SESSION['USER_ID'];
            include_once("./include/functions/dbconnect.php");
            $gr_result = $db->query($gr_sql);
            $gr_row = $gr_result->fetch();
            if(!empty($gr_row) && $gr_row['rgroup']!=null){
                $grname = $gr_row['rgroup'];
                
                // #### USER IS A MEMBER OF A GROUP###//
                // determine number of devices and scientists
                $nDevices = 0;
                $mem_sql = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='" .$grname."'";
                $mem_result = $db->query($mem_sql);
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
  <link rel="stylesheet" type="text/css" href="style/style.css" />
  <script src="js/jquery.js"></script>
<title>Hauptseite von MoSeS - User control panel</title>

<?php  
  include_once("./include/_menu.php");
?>  

<!--<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>-->
<!-- <div id="splash">&nbsp;</div> -->
<!-- end #header -->

<div  id="menu_vertical">  
    <ul><?php
        
        if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
          ?>  
          
          <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'admin'){
                echo " id=\"current_page_menu\"";
            } ?>
          ><a href="ucp.php?m=admin" title="Admin">ADMIN PANEL</a></li>
           
            
          <?php
        }
    
        ?>
        <li<?php 
            if(!isset($_GET['m'])){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php" title="My Devices">My Devices</a></li>
        <?php
         if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>0){
             
        ?>
        <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'group'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=group" title="My Group">My Group</a></li>
        
        <?php
         }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>1){
            ?>
            <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'list'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=list" title="Show my App">My user studies</a></li>
            <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'upload'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=upload" title="User Study create">Create a user study</a></li>
            </ul>
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
            if(!empty($gr_row) && $gr_row['rgroup']!=null){
                $grname = $gr_row['rgroup'];
                // #### USER IS A MEMBER OF A GROUP###//
                // determine number of devices and scientists
                $nDevices = 0;
                $mem_sql = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='" .$grname."'";
                $mem_result = $db->query($mem_sql);
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
                    $dev_result = $db->query($dev_sql);
                    $dev_rows = $dev_result->fetchAll(PDO::FETCH_ASSOC);
                    $nDevices+=count($dev_rows);
                }
                $control = $nDevices - $nScientists * $CONFIG['MISC']['SC_TRESHOLD'];
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
                          if(isset($USER_DEVICES))
                          {
?>
                            <h3>Your devices</h3>
                            <br>
                            <div id="list_devices">
                            	<ul>
	                            	<script>
	                            	    function changeClass(element)
	                            	    {
	                            	    	if(element.className=='clicked')
	                            	    	{
	                            	       	 	element.className='notclicked';
	                            	       	}
	                            	       	else
	                            	       	{
	                            	       		element.className='clicked';	
	                            	       	}
	                            	   	}
	                            	</script>
<?php  
		                            if(!empty($USER_DEVICES))
		                            {
		                                // user has got some devices
		                                foreach($USER_DEVICES as $device)
		                                {
?>
		                                    <li onclick="changeClass(this);">
		                                        <p><b>Device name: </b><?php echo $device['deviceid'];?></p>
		                                        <ul>      
<?php
                                            	echo
                                            	'<a href="ucp.php?m=device&remove='. $device['hwid'] .'" title="Remove device" class="sensor_box_api_remove"></a>';                                       
?>
		                                        
		                                        <b>Selected sensors (filter):</b>
		                                        
<?php
		                                        	$sensor_array = json_decode($device['filter']);
			                                        if(count($sensor_array) != 0)
			                                        {
			                                        	echo "<br>";
			                                        	foreach($sensor_array as $sensor_number)
			                                        	{
		                                                      echo '<img src="images/sensors/ultrasmall/'. 
		                                                            $sensors_ultrasmall_mapping[$sensor_number][0] .'" alt="'. 
		                                                            $sensors_ultrasmall_mapping[$sensor_number][1] .'" title="'. 
		                                                            $sensors_ultrasmall_mapping[$sensor_number][1] .'" />'; 
		                                                }
		                                             }
		                                             else
		                                             {
		                                             	echo ' Nothing';
		                                           	 }
?>
		                                        <br>
		                                        <b>Android API version:</b><?php echo $device['androidversion']; ?>
		                                    </ul></li><!-- device_element -->
<?php
		                                } // end of foreach($USER_DEVICES as $device)s 
		                           	} // end of if(!empty($USER_DEVICES))
		                            else
		                            {
?>
		                                <b>Your device list is empty.</b> 
<?php
		                            }
?>
								</ul>
							</div>
<?php
		                   }// if(isset($USER_DEVICES))
                            if($MODE == 'ADMIN' && !isset($_POST['pending_requests'])){
?>
                            
                            <h3>Admin control panel</h3>
                            
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
                            
                            if($SHOW_UPDATE_PAGE != 1 && $MODE == 'UPLOAD' && !isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1)
                            {
                        ?>
                            
                            <h3>User study create form</h3>
                                <!-- by Ibrahim -->
                                <?php
                                  include_once("./include/functions/dbconnect.php");
                                  // Initlization the contents of the pages for US creation form
                                  $sql = "SELECT * FROM temp WHERE userid = ". $_SESSION["USER_ID"];
                                  $req = $db->query($sql);
                                  $row = $req->fetch();
                                  if(!empty($row))
                                  {
                                    $apk_title_value = $row['apk_title'];
                                    $description_value = $row['description'];
                                    $radioButton_value = $row['radioButton'];
                                    $startdate_value = $row['startdate'];
                                    $startcriterion_value = $row['startcriterion'];
                                    $radioButton1_value = $row['radioButton1'];
                                    $enddate_value = $row['enddate'];
                                    $runningtime_value = $row['runningtime'];
                                    $maxdevice_value = $row['maxdevice'];
                                    $locked_value = $row['locked'];
                                    $inviteinstall_value = $row['inviteinstall'];
                                    $androidversion_value = $row['androidversion'];
                                    $sensors_value = $row['sensors'];
                                  }
                                ?>
                                

                                                              
                                <form action="ucp.php?m=upload" method="post" enctype="multipart/form-data" class="upload_form"> 
                                
<?php
                                    if($page1 == true)
                                    {
?>
                                   		<!-- first page -->
                                      <p>Userstudy name:</p1>
                                      <input type="text" name="apk_title" value = "<?php echo $apk_title_value; ?>">
                                      <br>
                                      <p>Program description:</p>
                                      <textarea cols="30" rows="6" name="apk_description"><?php echo $description_value; ?></textarea>
                                      <br>
                                      <input type="submit" name="next1" value="next"/>
                                  
<?php
                                      
                                    }
                                    elseif($page2 == true)
                                    {
?>    
										<!-- second page -->
                                      Start of the user study is
                                      <br>
                                      <!--<script language="JavaScript">
                                        var currentFields = "";
                                        var current="";
                                        function enableText(elementId)
                                        {
                                          if (currentFields != "")
                                          {
                                            eval("document.forms[0].start_" + currentFields + "_a.disabled=true;");
                                          }
                                          eval("status = document.forms[0].start_" + elementId + "_a.disabled");
                                          if (String(status) == String("true"))
                                          {
                                            eval("document.forms[0].start_" + elementId + "_a.disabled=false;");
                                          }
                                          currentFields = elementId;
                                        }
                                        function enabledate(element)
                                        {
                                          if (current != "")
                                          {
                                           eval("document.forms[0].end_" + current + "_b.disabled=true;");
                                          }
                                          eval("status = document.forms[0].end_" + element + "_b.disabled");
                                          if (String(status) == String("true"))
                                          {
                                           eval("document.forms[0].end_" + element + "_b.disabled=false;");
                                          }
                                          current = element;
                                        }
                                      </script>-->
                                      <input type="radio"
                                        <?php echo (($radioButton_value == "1" || $radioButton_value == null)?'checked="checked"':''); ?>
                                        name="radioButton" value = "1" onclick="javascript:enableText('1');">
                                     on date:<input type="text" class="tcal" name="start_1_a"
	                                    	<?php //echo (($radioButton_value == "2")? 'disabled':''); ?>
	                                     value="<?php echo ($startdate_value == NULL)? '' : $startdate_value ; ?>"/>
                                      <br>
                                      <input type="radio"
                                      	<?php echo (($radioButton_value == "2")?'checked="checked"':''); ?>
                                      	name="radioButton" value = "2" onclick="javascript:enableText('2');">
                                         after this number of installing:<input type="text" name="start_2_a"
	                                         <?php //echo (($radioButton_value == "1" || $radioButton_value == null)?'disabled':''); ?>
	                                         value="<?php echo ($startcriterion_value == NULL)? '0' : $startcriterion_value ; ?>"/>
                                      <br>  
                                      <br>
                                      End of the user study is
                                      <br>
                                      <input type="radio"
                                        <?php echo (($radioButton1_value == "1" || $radioButton1_value == null)?'checked="checked"':''); ?>
                                        name="radioButton1" value = "1" onclick="javascript:enabledate('1');">
                                         on date:<input type="text" class="tcal" name="end_1_b"
                                         <?php //echo (($radioButton1_value == "2")? 'disabled':''); ?>
                                         value="<?php echo ($enddate_value == NULL)? '' : $enddate_value ; ?>"/>
                                      <br><input type="radio" <?php echo (($radioButton1_value == "2")?'checked="checked"':''); ?> name="radioButton1" value = "2" onclick="javascript:enabledate('2');">
                                         after running time:<input type="text" name="end_2_b"
                                          <?php //echo (($radioButton1_value == "1" || $radioButton1_value == null)?'disabled':''); ?>
                                         value="<?php echo ($runningtime_value == NULL)? 'yyyy-mm-dd' : $runningtime_value ; ?>"/>
                                      <br>
                                      <br>
                                      Max number of Devices:
                                      <input type="text" name="maxdevice" value="<?php echo $maxdevice_value; ?>"/>
                                      <br>
                                      <br>
                                      <?php
                                        if(!empty($groupname))
                                        {
                                      ?>  
	                                      <input type=radio
	                                        <?php  
	                                        echo
	                                        	($inviteinstall_value == "0"|| $inviteinstall_value == null)?'checked="checked"':''; ?>
	                                        name="invite" value="0"/>Send only to my group<br>
                                      <?php
                                        }
                                      ?>
                                      <INPUT TYPE=RADIO
                                        <?php echo (($inviteinstall_value == "1")?'checked="checked"':''); ?>
                                        NAME="invite" VALUE="1">Invite only<br>
                                      <INPUT TYPE=RADIO
                                        <?php echo (($inviteinstall_value == "2")?'checked="checked"':''); ?>
                                        NAME="invite" VALUE="2">Invite & Install<br>
                                      <INPUT TYPE=RADIO
                                        <?php echo (($inviteinstall_value == "3")?'checked="checked"':''); ?>
                                        NAME="invite" VALUE="3">Install only<P>
                                      <br>
                                      <input type="submit" name="back2" value="back"/>
                                      <input type="submit" name="next2" value="next"/>
                                      <!--<button>next</button>
                                    </form>-->
<?php
                                    }
                                    elseif($page3 == true)
                                    {
?>

                                      <!-- third page -->
                                      <label for="file">Select a file:</label> 
                                      <input type="file" name="userfile" id="file" style="margin: 15px 0;">
                                      <br>
                                      <p>The lowest supported android version for your user study:</p>
                                      <select name="apk_android_version">
                                        <?php   
                                        for($i=0; $i<count($API_VERSION); $i++){
                                            echo
                                            '<option value="'.$API_VERSION[$i][0].'" '
                                            .(($androidversion_value==$API_VERSION[$i][0]) ? 'selected="selected"' : '')
                                            .' >'. $API_VERSION[$i][1].'</option>';    
                                        }
                                        ?>
                                      </select>
                                      <br>
                                      <p style="margin: 20px 0;">Your user study requires the following sensors:</p>
                                      <ul>
                                        <?php
                                          $sensors_value = ($sensors_value != null) ? $sensors_value : '[]';      
                                          $apk_to_update_sensors = json_decode($sensors_value);
                                      
                                          for($i=0; $i < count($sensors_info); $i++)
                                          {
                                               
                                            echo '<li>';
                                            if(in_array(($i+1), $apk_to_update_sensors))
                                            {
                                                
                                                // if sensor was selected, check and select it here
                                                
                                                echo '<div class="'. $sensors_info[$i][0] .'" title="'. $sensors_info[$i][2] .'" style="display: none;"></div>';
                                                echo '<div class="'. $sensors_info[$i][1] .'" title="'. $sensors_info[$i][2] .'"></div>';
                                                echo '<input type="checkbox" name="sensors[]" value="'. ($i+1) .'" checked="checked" />';
                                            }
                                            else
                                            {
                                               
                                                // here is no selection of sensor
                                                
                                                echo '<div class="'. $sensors_info[$i][0] .'" title="'. $sensors_info[$i][2] .'"></div>';
                                                echo '<div class="'. $sensors_info[$i][1] .'" title="'. $sensors_info[$i][2] .'" style="display: none;"></div>';
                                                echo '<input type="checkbox" name="sensors[]" value="'. ($i+1) .'" />';
                                            }
                                            echo '</li>';
                                          }  
                                       ?>
                                      </ul>
                                      
                                      <input type="submit" name="back3" value="back"/>
                                      <input type="submit" name="create" value="create" onClick = "docuemnt.location = 'http://da-sense.de/moses/upload.php' "/>
                                      <!--<button>create</button>-->
                                    </form>
                                  <?php
                                    }
                                  ?>
                                

                        <?php
                            }
                            
                            // there WAS some upload
                            
                            if($MODE == 'UPLOAD' && isset($_GET['res']) && $_GET['res'] >= 0){
                                
                                echo '<h3>Finished uploading the file</h3>';
                                
                                switch($UPLOAD_RESULT){
                                    // failed upload.
                                    case 0:
                                    ?>
                                        <div class="upload_failed">
                                            <p>That filetype not allowed. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                    // successful upload
                                    case 1:
                                        ?>
                                        <div class="upload_successful">
                                            <?php
                                            // by Ibrahim
                                            include("./upload_successful.php");
                                            ?>
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
                            
                            if($MODE == 'GROUP')
                            {
                            	if(!empty($groupname))
                                { 
                                    echo '<h3>You are currently member of research group</h3>';
                                    echo '<div class="group_name">'. $groupname .'</div>';
                                    $apk_lists = "";
                                    $num_apks = 0;         
                                    
                                    if(count($group_members_array) > 0)
                                    {
                                    	// By Ibrahim
?>
	                                    <br>Your group has <b><?php
                                    	echo
                                    	((count($group_members_array) > 1) ? count($group_members_array)
                                    	.'</b> members:': 'a</b>'
                                    	.'member:');
?>
                                    	<div id="group_users">
                                    		<ul>
	                                    		<script>
	                                    		    function changeClass(element)
	                                    		    {
	                                    		    	if(element.className=='clicked')
	                                    		    	{
	                                    		       	 	element.className='notclicked';
	                                    		       	}
	                                    		       	else
	                                    		       	{
	                                    		       		element.className='clicked';	
	                                    		       	}
	                                    		   	}
	                                    		</script>
<?php
		                                    	foreach ($group_members_array as $member)
		                                    	{
		                                    		include_once("./include/functions/dbconnect.php");
		                                    		
		                                    		// 
		                                    		$user_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $member;    
		                                    		$req = $db->query($user_sql);
		                                    		$user = $req->fetch();
		                                    		
		                                    		$hw_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['HARDWARE']. " WHERE uid=" . $member;
		                                    		$req_hw = $db->query($hw_sql);
		                                    		$hw_rows = $req_hw->fetchAll();
		                                    		
		                                    		$apk_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['APK']. " WHERE locked=1 AND userid=" . $member;
		                                    		$req_apk = $db->query($apk_sql);
		                                    		$apk_rows = $req_apk->fetchAll();
?>
		                                    		<li onclick="changeClass(this);">
			                                    		<p><b> Name:</b> <?php echo $user['firstname']." ".$user['lastname']; ?></p>
			                                    		<ul>
			                                    		<b> Account level: </b>
			                                    		<?php  
			                                    			echo 
			                                    			(($user['usergroupid'] == 1) ?
			                                    			"normal"
			                                    			: (($user['usergroupid'] == 2) ?
			                                    			"scientist"
			                                    			: "admin"));
			                                    		?>
			                                    		<br><b> Email: </b><?php echo $user['email']; ?>
			                                    		<br><b> Number of devices: </b><?php echo count($hw_rows); ?>
			                                    		<br>
<?php
		                                    			if(count($hw_rows) > 0)
		                                    			{
		                                    				foreach($hw_rows as $row)
		                                    					$all_devices[] = $row;
				                                    	} // end of if(count($hw_rows) > 0)
				                                    	
				                                    	for($i = 0 ; $i < count($apk_rows) ;$i++)
		                                				{
		                                					$apk_lists.=($num_apks > 0)?", " : "";
		                                					$apk_lists.=$apk_rows[$i]['apktitle'];
		                                					$num_apks++;
		                                				}
?>
		                                   		</ul></li><!-- group_user -->
		                                   		
<?php
		                                    	} // end of foreach ($group_members_array as $member)
?>											</ul> <!-- list of users -->
		                       			</div> <!-- group_users -->
		                       			<br>              		
<?php
                                    } // end of if(count($group_members_array) > 0)
?>
									List of unique devices of this group:
									<div id="group_devices">
									
<?php
	                                    // list of all unique devices
	                                    if(count($all_devices) > 0)
	                                    {
	                                    	$unique_array = array();
?>
	                                    	<ul>
	                                    		<script>
	                                    		    function changeClass(element)
	                                    		    {
	                                    		    	if(element.className=='clicked')
	                                    		    	{
	                                    		       	 	element.className='notclicked';
	                                    		       	}
	                                    		       	else
	                                    		       	{
	                                    		       		element.className='clicked';	
	                                    		       	}
	                                    		   	}
	                                    		</script>
	                                    		
<?php
	                            				for($h = 0 ; $h < count($all_devices) ; $h++)
	                            				{
	                            					if(($all_devices[$h]['uniqueid'] != NULL) && !in_array($all_devices[$h]['uniqueid'],$unique_array))
	                            					{
	                            						$unique_array[] = $all_devices[$h]['uniqueid'];
?>
		                                				<li onclick="changeClass(this);">
		                                					<p><b>Device's model name: </b><?php echo $all_devices[$h]['modelname']; ?></p>
		                                					<ul><b>  Android version number: </b>
		                                    				<?php echo $all_devices[$h]['androidversion']; ?>
		                                    				<br><b>   Availabe sensors: </b>
		                                    				<br>
<?php
		                                					$sensor_array = json_decode($all_devices[$h]['filter']);
		                                					
		                                					if(count($sensor_array) != 0)
		                                					{
		                                					   foreach($sensor_array as $sensor_number)
		                                					   {
		                            					           echo '<img src="images/sensors/ultrasmall/'. 
		                            					                 $sensors_ultrasmall_mapping[$sensor_number][0] .'" alt="'. 
		                            					                 $sensors_ultrasmall_mapping[$sensor_number][1] .'" title="'. 
		                            					                 $sensors_ultrasmall_mapping[$sensor_number][1] .'" />'; 
		                                					        
		                                					    }
		                                					}
		                                					else
		                                					{
		                                						echo "no sensor selected";
		                                					}
		                                					echo "<br>";
		                                					echo "<b>C2DM</b> is ".(($all_devices[$h]['c2dm'] != null)?"" : "not ")."availabe";
?>	                                    						
														</ul></li><!-- group_device -->
<?php
	                                    			}// end of if()
	                                    		}
?>
	                                 		</ul><!-- list of devices -->
<?php
	                                    } // end of if(count($all_devices) > 0)
?>
									</div><!-- group_devices -->
									<br>
<?php
                                	echo (($num_apks == 0)?
                        				"This group has <b>no</b> private apk"
                        				: (($num_apks == 1 && $apk_lists != "")?
                        				"This group has <b>a</b> private apk: ".$apk_lists
                        				: "This group has <b>$num_apks</b> private apks:<br>".$apk_lists));
?>
                            		<form action=ucp.php?m=leave method="post" class="leave_group">
                            		    <button>Leave this group</button>
                            		</form>
<?php
                               	} // end of if(!empty($groupname)) 
	                            else
	                            {
?>
                                
                                    <h3>Join a research group or found one</h3>
                                    <form action=ucp.php?m=join enctype="multipart/form-data" method="post" class="join_group">
                                        <input type="radio" name="join_create" value="join" class="radio_join" />Join<br>
                                        <input type="radio" name="join_create" value="create" class="radio_create" />Create                                        
                                        <p>Enter the name of the research group<p>
                                        <input type="text" name="group_name" />
                                        <p>Enter the password of the group<p>
                                        <input type="password" name="group_pwd" />
                                        <button>OK</button>
                                    </form>
<?php
                              }
                         	} // end of if($MODE == 'GROUP')
                            
                            // THE USER HAS CLICKED THE JOIN BUTTON
                            if($MODE == 'JOIN'){
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
                                echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=". $_SERVER['HTTP_REFERER'] ."\">");
                            }
                            
                            // THE USER HAS CLICKED THE LEAVE BUTTON
                            if($MODE == 'LEAVE'){
                                echo '<h3>You left research group</h3>';
                                echo '<div class="group_name">'. $groupname .'</div>';
                                echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=". $_SERVER['HTTP_REFERER'] ."\">");
                                }
                            
                            // THE USER WANTS TO BE A SCIENTIST, INSTANTLY
                            if($MODE == 'INSTANT'){
                                if($scientist_succses == 1){
                                    echo("<h3>Congrats! You have gained scientist credentials!<h3>");
                                    echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=". $_SERVER['HTTP_REFERER'] ."\">");
                                }
                                else{
                                    echo("<h3>Y U DO THIS? br0<h3>");
                                    echo("<META HTTP-EQUIV=\"refresh\" CONTENT=\"3;URL=". $_SERVER['HTTP_REFERER'] ."\">");
                                }
                            }
                            
                            // user wants a listing of APK files
                            if($MODE == 'LIST' && isset($LIST_APK))
                            {
?>
                              <h3>Your User Studies</h3>
                              <br>
                              <div id="list_us"> 
<?php                         // we found some APKs
                              if($LIST_APK == 1)
                              {
?>
                              	<ul>
	                              	<script>
	                              	    function changeClass(element)
	                              	    {
	                              	    	if(element.className=='clicked')
	                              	    	{
	                              	       	 	element.className='notclicked';
	                              	       	}
	                              	       	else
	                              	       	{
	                              	       		element.className='clicked';	
	                              	       	}
	                              	   	}
	                              	</script>
<?php
	                                foreach($apk_listing as $row)
	                                {
?>   
	                             		<li onclick="changeClass(this);">
	                                   		<p><b>Name: </b><?php echo $row['apktitle']; ?></p>
<?php
                                            echo '<a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk" class="bt_download"></a>';
                                            echo '<a href="ucp.php?m=addquest&id='. $row['apkid'] .'" title="Add Questionnaire" class="bt_quest"></a>';
                                            echo '<a href="ucp.php?m=update&id='. $row['apkid'] .'" title="Update APK" class="bt_upload"></a>';
                                            echo '<a href="ucp.php?m=list&remove='. $row['apkhash'] .'" title="Remove APK" class="sensor_box_api_remove"></a>';
		                                   	$android_version = $row['androidversion'];
		                            		$startdate = $row['startdate'];
	                                    	$startcriterion = $row['startcriterion']; 
	                                  	    $enddate = $row['enddate'];
	                                      	$runningtime = $row['runningtime'];
	                                      	$description = $row['description'];
	                                      	$onlymygroup = $row['locked'];
	                                      	$invite = $row['inviteinstall'];
	                                      	$maxdevice = $row['maxdevice'];
	                                      	$apkname = $row['apkname'];
?>
		                                   	<ul>
		                                   	The lowest supported android version: <?php echo $android_version; ?>
		                                    <br>
<?php
		                                        if($startdate != null)
		                                          echo "The start date: ".$startdate;
		                                        elseif($startcriterion != null)
		                                            echo "Commencement after ".$startcriterion." users join.";
		                                        else
		                                          echo "Commenced while creating ".$row['apktitle'].".";
?>
		                                      <br>
<?php
		                                        if($enddate != null)
		                                          echo "The end date: ".$enddate;
		                                        elseif($runningtime != null)
		                                          echo "The termination after ".$runningtime." from start date.";
		                                        else
		                                          echo "Terminated immediately after creating ".$row['apktitle'].".";
?>
		                                      <br>
		                                      Description:
		                                      <?php
		                                        echo $description;
		                                      ?>
		                                      <br>
		                                      <?php
		                                        if($onlymygroup)
		                                          echo $row['apktitle']." is private for your group.";
		                                        else
		                                          echo $row['apktitle']." is public.";
		                                      ?>
		                                      <br> 
		                                      <?php
		                                        if($invite == 1)
		                                          echo "Joining is allowed for invited users.";
		                                        elseif($invite == 2)
		                                          echo "Joining is allowd from all invited users that installed ".$row['apktitle'].".";
		                                        elseif($invite == 3)
		                                          echo "Joining is allowed from all users that installed ".$row['apktitle'].".";
		                                      ?>
		                                      <br>
		                                      Max number of Devices: <?php echo $maxdevice; ?>
		                                      <br>
		<?php
		                                      $sensor_array = json_decode($row['sensors']);
		                                      if(count($sensor_array) != 0)
		                                      {
		                                      	
		                                        echo "Required sensors:<br>";
		                                        // we will make some lines of sensors
		                                        foreach($sensor_array as $sensor_number)
		                                         {
		                                            echo 
		                                              '<img src="images/sensors/ultrasmall/'. 
		                                              $sensors_ultrasmall_mapping[$sensor_number][0] .'" alt="'. 
		                                              $sensors_ultrasmall_mapping[$sensor_number][1] .'" title="'. 
		                                              $sensors_ultrasmall_mapping[$sensor_number][1] .'" />'; 
		                                          }
		                                      }
		                                      else
		                                      {
		                                        echo 'No sensors set.';
		                                      }
?>                                    
		                                      <br>
		                                      There 
		                                        <?php
		                                          echo ($row['participated_count'] < 2 ? 'is ' : 'are ');
		                                          echo ($row['participated_count'] == 0) ? "no" : $row['participated_count'];
		                                          echo ($row['participated_count'] < 2 ? ' device' : ' devices');                           
		                                          echo " currently joined to ".$row['apktitle'].".";
		                                        ?>
		                                       <br>
		                                       

<?php
												$sql ="SELECT questid FROM `apk_quest` WHERE apkid=".$row['apkid'];
												$req=$db->query($sql);
												$rows = $req->fetchAll();
												if(count($rows) > 0)
												{
?>
													The selected questionnaires:<br>
<?php
													for($qi = 0; $qi < count($rows); $qi++)
													{
														$sql ="SELECT name FROM `questionnaire` WHERE questid=".$rows[$qi]['questid'];
														$req=$db->query($sql);
														$us_quest = $req->fetch();
														echo $us_quest['name'].(($qi+1 < count($rows))? " - ":"");
													}
												}
												else
												{
?>
													There is no questionnaire selected
<?php	
												}
													
												
?>
		                                  </ul></li>
<?php 
                                	}
?>
								</ul>
								</div>
							
<?php   
                              }
                              else
                              {
                          ?>
                                <div class="sensor_box">
                                  <ul>
                                      <li>
                                          <div style="padding-top: 10px; padding-bottom: 10px;">You have no user studies.</div>
                                      </li>
                                  </ul>
                                </div>
                                
<?php
                              }

                            }

                           
                            if($show_add_quest == true)
                            {
?>
								List of all availabes questionnaires: <br>
<?php	
								include_once("./include/functions/dbconnect.php");
								$sql ="SELECT * FROM `questionnaire`";
								$req=$db->query($sql);
								$quests = $req->fetchAll();
?>
								<div id="quests_list">
									<ul>
										<script>
										    function changeClass(element)
										    {
										    	if(element.className=='clicked')
										    	{
										       	 	element.className='notclicked';
										       	}
										       	else
										       	{
										       		element.className='clicked';	
										       	}
										   	}
										</script>
<?php										
											foreach($quests as $quest)
											{
?>
												<li onclick="changeClass(this);">
													<p><b>Name: </b><?php echo $quest['name']; ?></p>
													It containes the following questions:<br><ul>
													
<?php
													$sql ="SELECT * FROM `question` WHERE questid=".$quest['questid'];
													$req=$db->query($sql);
													$qust = $req->fetchAll();
													$i = 1;
													foreach($qust as $q)
													{
														echo "q".$i.": ";
														if($q['type']==1) // multiple choices
														{
															echo substr($q['content'],0,strrpos($q['content'],"{"))."<br>";
														}
														else
														{
															echo $q['content']."<br>";
														}
														$i++;
													}
?>
												</ul></li>
<?php										
											}
?>
									</ul>
								</div>
								<br>
<?php
								
								if (isset($_POST['submit']))
	                              {
	                              	include_once("./include/functions/dbconnect.php");
	                              /*	
                              for ($i=0; $i<count($_POST['questbox']);$i++) {
		
										$idtodo=$_POST['questbox'][$i];
										$sql="insert into apk_quest values('$apkid','$idtodo')";
									    $req=$db->query($sql) ;
									    echo "Ok";
									    
						    	}
						    	*/
                              	
                              	/*
                              	foreach($_POST as $key => $value){
                              		if(substr($key,0,9)=="standard_"){
                              			$idtodo=substr($key, 9);
                              			
										$sql="INSERT INTO `apk_quest`  VALUES ($apkid,$idtodo);";
										echo "<br>".$sql;
									    $req=$db->query($sql) ;
                              		}
                              	}*/
                              	
                              	
                              
                              	
                              	if(isset($_POST['questionnaire']) && is_array($_POST['questionnaire']))
	                              	foreach($_POST['questionnaire'] as $questBox)
	                              	{
	                              		//echo "($questBox) ";
	                              		
	                              			$sql ="SELECT * FROM `apk_quest` WHERE apkid=$apkid AND questid = $questBox";
	                              			$req=$db->query($sql);
	                              			$rows = $req->fetch();
	                              			if(empty($rows))
	                              			{
												$sql="INSERT INTO `apk_quest`  VALUES ($apkid,$questBox);";
												//echo "<br>".$sql;
											    $db->exec($sql);
											    
											    
	                              			}
	                              			else
	                              				echo "$apkid and $questBox exist already!<br>";
	                              	}
	                             echo "Your selecting was done successfully!";
						   		
                              }	
                              else 
                              {   
                              ?>
                              <fieldset id="questionnaireFieldset">
                              <label>Select your Questionnaire:</label>
                              <form action="" method="POST">
                                <input type="hidden" id = "apkid" name="apkid" value="<?php echo $apkid; ?>" />
                               
                               <div id="quests_selected" style="padding:10px;"></div>
                               
                                <select id="quests" name="quests" >
                                  <option value="null">Select a questionnaire:</option>
                                  <?php
                                  //<br><input type = checkbox name= questbox[]/>
                                    if(!empty($quest_db))
                                    {
                                      for ($i = 0; $i < count($quest_db); $i++)
                                      {
                                        echo '<option value="'.$quest_db[$i]['questid'].'">'.$quest_db[$i]['name'].'</option>';
                                      }

                                    }
                              //XXX By Fehmi JQUERY      
                                  ?>
                                </select>
							
                             
                              
                              <script type="text/javascript">
                              $("#quests").change(function(){

                                  if($("#quests option:selected").val() != "null"){
                                  $.ajax({
                                	  type: "POST",
                                	  url: "getquest.php",
                                	  data: { id: $("#quests option:selected").val()}
                                	}).done(function( msg ) {
                                	  $("div#quests_selected").append(msg);
                                	  $("#quests option:selected").remove();
                                	  
                                	});
                                  }
                                  });
                              </script>
                              <script type="text/javascript">
                              function showUser()
                              { 
	                              if (window.XMLHttpRequest)
	                                {// code for IE7+, Firefox, Chrome, Opera, Safari
	                                xmlhttp=new XMLHttpRequest();
	                                }
	                              else
	                                {// code for IE6, IE5
	                                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	                                }
	                              xmlhttp.onreadystatechange=function()
	                                {
	                                if (xmlhttp.readyState==4 && xmlhttp.status==200)
	                                  {
	                                  document.getElementById("setText").innerHTML="Your selecting was done successfully!";
	                                  }
	                                }
	                              xmlhttp.open("GET","ucp.php",true);
	                              xmlhttp.send();
                              }
                              </script>
                              <input type="submit" name = "submit" onclick="showUser();" >
                             
	  							
                              
                              </form>
                              <span id="setText">
                              <?php
	  								if(empty($quest_selected))
	  									echo "<br>There is no questionnaire been added to this user study.<br>";
	  								else
	  								{
	  									echo "<br>You added already these questionnaires:";
?>
										<ul>
<?php	  	                            
		  		                            foreach($quest_selected as $qessel)
		  		                            {
		  		                            	include_once("./include/functions/dbconnect.php");
		  		                            	$sql = "SELECT * FROM questionnaire WHERE questid=".$qessel['questid'];
		  		                            	$req = $db->query($sql);
		  		                            	$qs = $req->fetch();
		  		                            	echo "<li>".$qs['name']."</li>";
		  		                            }
?>
										</ul>
<?php
	  		                        }
?>
	  							</span>
                              </fieldset>
                              
							
<?php							}
                                                            
                              
                              
                              
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
                            
                            <h3>Application for scientist credentials</h3>

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
                                                && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED != 1)
                            {
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
  ob_end_flush();  
?>