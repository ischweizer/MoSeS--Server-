<?php
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: /moses/");
    
include_once("./include/functions/func.php");
include_once("./include/_header.php");
include_once("./config.php");

$apk_listing = '';  // just init
$groupname = NULL; // name of the group the user is in OR name of the group the user wants to join
$grouppwd = NULL; // password of the group the user wants to join
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

/* show_add_quest will be true only if the user want to add questionnaires to an user study */
$show_add_quest = false;
/* show_us_quest will be true only if the user want see chosen questionnaires and their results for an user study */
$show_us_quest = false;

$apk_to_update = array();
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
                                  
// SWITCH USER CONTORL PANEL MODE
if(isset($_GET['m']))
{
    
    $RAW_MODE = strtoupper(trim($_GET['m']));
    $MODE = '';
    
    switch($RAW_MODE)
    {

        case 'ADDQUEST';
        	// user want to add  questionnaires to a user study
        	if(isset($_GET['id']))
        	{
            	$apkid = preg_replace("/\D/", "", $_GET['id']);
	            $show_add_quest = true;
	            include_once("./include/functions/dbconnect.php");
	            $sql = "SELECT apktitle FROM apk WHERE apkid = ".$apkid;
	            $req = $db->query($sql);
    			$row = $req->fetch();
	            $apkname = $row['apktitle'];
				include_once("./include/managers/QuestionnaireManager.php");
				$notchosen_quests = QuestionnaireManager::getNotChosenQuestionnireForApkid(
					$db,
					$CONFIG['DB_TABLE']['QUEST'],
					$CONFIG['DB_TABLE']['APK_QUEST'],
					$apkid);
				$chosen_quests = QuestionnaireManager::getChosenQuestionnireForApkid(
					$db,
					$CONFIG['DB_TABLE']['QUEST'],
					$CONFIG['DB_TABLE']['APK_QUEST'],
					$apkid);
            }
            break;

         case 'USQUEST';
        	// user want to see the result of the questionnaires to a user study
        	if(isset($_GET['id']))
        	{
            	$apkid = preg_replace("/\D/", "", $_GET['id']);
	            $show_us_quest = true;
	            include_once("./include/functions/dbconnect.php");
	            $sql = "SELECT apktitle FROM apk WHERE apkid = ".$apkid;
	            $req = $db->query($sql);
    			$row = $req->fetch();
	            $apkname = $row['apktitle'];
				include_once("./include/managers/QuestionnaireManager.php");
				$notchosen_quests = QuestionnaireManager::getNotChosenQuestionnireForApkid(
					$db,
					$CONFIG['DB_TABLE']['QUEST'],
					$CONFIG['DB_TABLE']['APK_QUEST'],
					$apkid);
				$chosen_quests = QuestionnaireManager::getChosenQuestionnireForApkid(
					$db,
					$CONFIG['DB_TABLE']['QUEST'],
					$CONFIG['DB_TABLE']['APK_QUEST'],
					$apkid);
            }
            break;

        case 'UPLOAD':
        	// user want to create a user study 
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
		            ."', startdate = '". ((isset($_POST['start_d'])) ? $_POST['start_d'] : NULL)
		            ."', enddate = '". ((isset($_POST['end_d'])) ? $_POST['end_d'] : NULL)
		            ."', startcriterion = '". ((isset($_POST['start_n'])) ? $_POST['start_n'] : "0")
		            ."', runningtime = '". ((isset($_POST['end_n']) && $_POST['end_n'] != 'yyyy-mm-dd') ? $_POST['end_n'] : NULL)
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
		                $sql = "UPDATE temp set androidversion = '".((isset($_POST['apk_android_version']))?$_POST['apk_android_version']:'')
		                ."' WHERE userid = ".$_SESSION['USER_ID'];
		                $db->exec($sql);
		
		                // there is a problem if nothing changes on sensors' selecting
		                $sql = "UPDATE temp set sensors = '".$SENSOR_LIST_STRING."' WHERE userid = ".$_SESSION['USER_ID'];
		                $db->exec($sql);
		            } // end of else : if(isset($_REQUEST['next1']))
	            } // end of elseif(isset($_REQUEST['back3']) || isset($_REQUEST['next1']))
	            elseif(isset($_REQUEST['next2']))
	            {
	            	$page3 = true;
	            	include_once("./include/functions/dbconnect.php");
	            	$sql = "UPDATE temp set radioButton = '". ((isset($_POST['radioButton'])) ? $_POST['radioButton'] : "1")
		            ."', startdate = '". ((isset($_POST['start_d'])) ? $_POST['start_d'] : NULL)
		            ."', enddate = '". ((isset($_POST['end_d'])) ? $_POST['end_d'] : NULL)
		            ."', startcriterion = '". ((isset($_POST['start_n'])) ? $_POST['start_n'] : "0")
		            ."', runningtime = '". ((isset($_POST['end_n']) && $_POST['end_n'] != 'yyyy-mm-dd') ? $_POST['end_n'] : NULL)
		            ."', maxdevice = '". ((isset($_POST['maxdevice'])) ? $_POST['maxdevice'] : NULL)
		            ."', locked = '". ( !(isset($_POST['invite'])) || ((isset($_POST['invite']) && $_POST['invite'] == "0")) ? "1" : "0")
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
		        }// end of elseif(isset($_REQUEST['create']))  
		    } // end of if(isset($_REQUEST['next1']) || isset($_REQUEST['back2']) || isset($_REQUEST['next2']) || isset($_REQUEST['back3']) || isset($_REQUEST['create']))
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
                    
                    if(isset($_POST['promo_sent']) && trim($_POST['promo_sent']) == "1")
                    {
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
                        if(!empty($row))
                        {
                            if($row['pending'] == 1){
                                $USER_PENDING = 1;  
                            }else{
                                if($row['accepted'] == 1)
                                    $USER_PENDING = 0;
                                    $USER_ALREADY_ACCEPTED = 1;  
                            }
                        }
                        else
                        {
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
            
            if(!empty($group_row) && $groupname!=NULL){
                
                $groupname = $group_row['rgroup'];
                
                $sql = "SELECT members 
                        FROM ". $CONFIG['DB_TABLE']['RGROUP'] ." 
                        WHERE name = '". $groupname ."'";
                        
                 $result = $db->query($sql);
                 $row = $result->fetch();
                 
                 $group_members_count = count(json_decode($row['members']));
                 
                 $group_members_array = json_decode($row['members']);
                 if($group_members_array != NULL){
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
            if(!empty($gr_row) && $gr_row['rgroup']!=NULL){
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
                // HERE GROUP
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
        
        if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES")
        {
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
         if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>0)
         {
             
	        ?>
	        <li<?php 
	            if(isset($_GET['m'])&& $_GET['m'] == 'group')
	            {
	                echo " id=\"current_page_menu\"";
	            } ?>><a href="ucp.php?m=group" title="My Group">My Group</a></li>
	        
	        <?php
         }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>1)
        {
            ?>
            <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'list'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=list" title="Show my App">My User Studies</a></li>
            <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'upload'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=upload" title="User Study create">Create a User Study</a></li>
            </ul>
        <?php
        }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]<2)
        {
            /*
            * Offer an instant upgrade to scientist account if the user is a member of a group and
            * #unique-devices-in-group - #scientist-in-group*5 >= 5
            */
            // determine if the user is a member of a group
            $gr_sql = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $_SESSION['USER_ID'];
            include_once("./include/functions/dbconnect.php");
            $gr_result = $db->query($gr_sql);
            $gr_row = $gr_result->fetch();
            if(!empty($gr_row) && $gr_row['rgroup']!=NULL)
            {
                $grname = $gr_row['rgroup'];
                // #### USER IS A MEMBER OF A GROUP###//
                // determine number of unique devices and scientists
                $nDevices = 0;
                $all_devices = array();
                $unique_array = array();
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
                    // get all devices the user has
                    $dev_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['HARDWARE']." WHERE uid=".$id;
                    $dev_result = $db->query($dev_sql);
                    $dev_rows = $dev_result->fetchAll(PDO::FETCH_ASSOC);
                    foreach($dev_rows as $row)
                    	$all_devices[] = $row;
                }
                // check for unique devices
                for($h = 0 ; $h < count($all_devices) ; $h++)
				{
					if(($all_devices[$h]['uniqueid'] != NULL) && !in_array($all_devices[$h]['uniqueid'],$unique_array))
					{
						$unique_array[] = $all_devices[$h]['uniqueid'];
						$nDevices++;
					}
				}
				// The rule to get scientist credentials
                $control = $nDevices - ($nScientists * $CONFIG['MISC']['SC_TRESHOLD']);
                if($control >= $CONFIG['MISC']['SC_TRESHOLD'])
                {
?>
                  <li><a href="ucp.php?m=instant">Get scientist credentials today!</a></li>
<?php
                }
                else
                {
?>
                    <li><a href="ucp.php?m=promo">Request scientist account</a></li>
<?php
                }  
            } // end of if(!empty($gr_row) && $gr_row['rgroup']!=NULL)
            else
            {
?>
                <li><a href="ucp.php?m=promo">Request scientist account</a></li>
<?php
            }
        } // end of if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]<2)
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
							/********************************************
                            **************** My Devices *****************
                            *********************************************/
                          if(isset($USER_DEVICES))
                          {
?>
                            <fieldset>
							<legend><h3><em><b>My Devices</b></em></h3></legend>
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
							</div><br>
							</fieldset>
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
                            
                            /********************************************
                            ************* Create a User Study ***************
                            *********************************************/
                            
                            if($SHOW_UPDATE_PAGE != 1 && $MODE == 'UPLOAD' && !isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1)
                            {
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
                                    $enddate_value = $row['enddate'];
                                    $startcriterion_value = $row['startcriterion'];
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
										<fieldset><legend> <h3><em><b>Create a User Study (1/3)</b></em></h3> </legend>
	                                    <p>Userstudy name:</p>
	                                    <input type="text" name="apk_title" value="<?php echo $apk_title_value; ?>">
	                                    <br><br>
	                                    <p>Program description:</p>
	                                    <textarea cols="30" rows="6" name="apk_description"><?php echo $description_value; ?></textarea>
	                                    <br>
	                                    <input type="submit" name="next1" value="next" style="height: 25px; width: 90px"/>
                                        </fieldset>
<?php
                                      
                                    }
                                    elseif($page2 == true)
                                    {
                                    	
?>    								<fieldset><legend><h3><em><b>Create a User Study (2/3)</b></em></h3></legend>
	                                    	
									 This user study:<br><br>
									 
                                      <input type="radio"
                                        <?php echo (($radioButton_value == "1" || $radioButton_value == null)?'checked="checked"':''); ?>
                                        name="radioButton" value = "1" onclick="javascript:enableText('1');">
                                     starts on:
                                     <input type="text" class="tcal" name="start_d"
                                     	value="<?php echo ($startdate_value == NULL)? '' : $startdate_value ; ?>"/>
                                     and ends on:
                                     <input type="text" class="tcal" name="end_d"
                                     	value="<?php echo ($enddate_value == NULL)? '' : $enddate_value ; ?>"/><br><br>
	                                 <input type="radio"
	                                 	<?php echo (($radioButton_value == "2")?'checked="checked"':''); ?>
	                                 	name="radioButton" value = "2" onclick="javascript:enabledate('1');"/>
	                                 starts after number of installing:
	                                 <input type="text" name="start_n"
                                     	value="<?php echo ($startcriterion_value == NULL)? '0' : $startcriterion_value ; ?>"/>
                                     and ends after running time:
                                     <input type="text" name="end_n"
                                     	value="<?php echo ($runningtime_value == NULL)? 'yyyy-mm-dd' : $runningtime_value ; ?>"/>
                                     <br>
                                     <br><br><hr><br>
                                     Max number of Devices:
                                     <input type="text" name="maxdevice" value="<?php echo $maxdevice_value; ?>"/>
                                     <br><br><hr>
                                     <br><br>
<?php
                                     if(!empty($groupname))
                                     {
?>  								Who can run my userstudy?
	                                     <br><br>  <input type=radio
	                                        <?php  
	                                        echo
	                                        	($inviteinstall_value == "0"|| $inviteinstall_value == null)?'checked="checked"':''; ?>
	                                        name="invite" value="0"/> only my group<br>
                                      <?php
                                        }
                                      ?>
                                      <INPUT TYPE=RADIO
                                        <?php echo (($inviteinstall_value == "1")?'checked="checked"':''); ?>
                                        NAME="invite" VALUE="1"> Invite only<br>
                                      <INPUT TYPE=RADIO
                                        <?php echo (($inviteinstall_value == "2")?'checked="checked"':''); ?>
                                        NAME="invite" VALUE="2"> Invite & Install<br>
                                      <INPUT TYPE=RADIO
                                        <?php echo (($inviteinstall_value == "3")?'checked="checked"':''); ?>
                                        NAME="invite" VALUE="3"> Install only<P>
                                      <br>
                                      <input type="submit" name="back2" value="back" style="height: 25px; width: 90px"/>
                                      <input type="submit" name="next2" value="next"style="height: 25px; width: 90px"/>
                                      </fieldset>
									  <!--<button>next</button>
                                    </form>-->
<?php
                                    }
                                    elseif($page3 == true)
                                    {
?>
									<fieldset><legend><h3><em><b>Create a User Study (3/3)</b></em></h3></legend>
	                                    	
                                      <!-- third page -->
                                      <label for="file">Select a file:</label>
                                      <input type="file" name="userfile" id="file" style="margin: 15px 0;">
                                      <br><hr><br>
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
                                      <br><br><hr>
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
                                      
                                      <input type="submit" name="back3" value="back" style="height: 25px; width: 90px"/>
                                      <input type="submit" name="create" value="create" style="height: 25px; width: 90px" onClick = "docuemnt.location = 'http://da-sense.de/moses/upload.php' "/>
                                      
									  <!--<button>create</button>-->
                                    </form>
                                    <br>
                                    </fieldset>
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
                            
                            /********************************************
                            ***************** My Group ******************
                            *********************************************/
                            
                            if($MODE == 'GROUP')
                            {
                            	// number of unique devices in this group
                            	$numOfUniq = 0;
                            	// list of devices
                            	$all_devices = array();
                            	// number of scientest useres in this group
                            	$numOfSient = 0;

                            	if(!empty($groupname))
                                { 
                                    echo '<fieldset><legend><h3><b><em>My Group: '.$groupname.'</em></b></h3></legend>';
                                   // echo '<div class="group_name">'. $groupname .'</div>';
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
														if($user['usergroupid'] == 1)
														{
															echo "normal";
														}
			                                    		elseif($user['usergroupid'] == 2)
			                                    		{
			                                    			echo "scientist";
			                                    			$numOfSient++;
			                                    		}
			                                    		elseif ($user['usergroupid'] == 3)
			                                    		{
			                                    			echo "admin";
			                                    			$numOfSient++;
			                                    		}
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
	                            						$numOfUniq++;
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
                            		<br>
                            		<br>
                            		<form action=ucp.php?m=leave method="post" >
                            		    <button>Leave this group</button>
                            		</form>
                            		<br>
                            		</fieldset>
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
                            
                            
                            /********************************************
                            ************* My user studies ***************
                            *********************************************/
                            
                            if($MODE == 'LIST' && isset($LIST_APK))
                            {
?>
                              <fieldset>
                              <legend><h3><b><em>My User Studies</em></b></h3></legend>
                              <br>
                              <div id="list_us"> 
<?php                         // we found some APKs
                              if($LIST_APK == 1)
                              {
?>
                              	<ul>
	                              	<script>
	                              	    function changeParentClass(element)
	                              	    {
	                              	    	if(element.parentNode.className=='clicked')
	                              	    	{
	                              	       	 	element.parentNode.className='notclicked';
	                              	       	}
	                              	       	else
	                              	       	{
	                              	       		element.parentNode.className='clicked';	
	                              	       	}
	                              	   	}
	                              	</script>
<?php
	                                foreach($apk_listing as $row)
	                                {
?>   
	                             		<li>
	                                   		<p onclick="changeParentClass(this);"><b>Name: </b><?php echo $row['apktitle']; ?></p>
<?php
                                            $android_version = $row['androidversion'];
		                            		$startdate = $row['startdate'];
	                                    	$startcriterion = $row['startcriterion']; 
	                                  	    $enddate = $row['enddate'];
	                                      	$runningtime = $row['runningtime'];
	                                      	$description = $row['description'];
	                                      	$ustudy_finished = $row['ustudy_finished'];
	                                      	$onlymygroup = $row['locked'];
	                                      	$invite = $row['inviteinstall'];
	                                      	$maxdevice = $row['maxdevice'];
	                                      	$apkname = $row['apkname'];
	                                      	
	                                      	echo '<a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk" class="bt_download"></a>';
                                            echo '<a href="ucp.php?m=update&id='. $row['apkid'] .'" title="Update APK" class="bt_upload"></a>';
                                            echo '<a href="ucp.php?m=list&remove='. $row['apkhash'] .'" title="Remove APK" class="sensor_box_api_remove"></a>';
                                            if($ustudy_finished == 1)
                                            {
                                            	echo '<a href="ucp.php?m=usquest&id='. $row['apkid'] .'" title="Result Of Questionnaire" class="bt_usquest"></a>';
                                            }
                                            else
                                            {
                                        		echo '<a href="ucp.php?m=addquest&id='. $row['apkid'] .'" title="Add Questionnaire" class="bt_addquest"></a>';
                                            }
                                            
                                            
		                                   	
?>
		                                   	<ul>
			                                   	The lowest supported android version: <?php echo $android_version; ?>
			                                    <br>
<?php
		                                        if($startdate != NULL)
		                                          echo "The start date: ".$startdate;
		                                        elseif($startcriterion != NULL)
		                                            echo "Commencement after ".$startcriterion." users join.";
		                                        else
		                                          echo "Commenced while creating ".$row['apktitle'].".";
?>
		                                      <br>
<?php
		                                        if($enddate != NULL)
		                                          echo "The end date: ".$enddate;
		                                        elseif($runningtime != NULL)
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
								</div><br></fieldset>
							
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
                            
                            /********************************************
                            ************* SEE QUESTs FOR US ***************
                            *********************************************/
                            if($show_us_quest == true)
                            {
                                
                                
  								// check if there is any questionnaire are chosen for this US
  								if(empty($chosen_quests))
  								{
  									// no questionnaire found
?>
  									<h4>There is no questionnaire been added to <?php echo $apkname; ?>.</h4><br>
<?php
  								}
  								else
  								{
?>
									<fieldset>
                                    	<legend><h3><em><b>The result of chosen questionnaires for this user study: <?php echo $apkname; ?></b></em></h3></legend>
										<div id="quests_list">
											<ul>
												<script>
												
						                    	   	/*
						                    	   	* to switch the class name of the parent of this element between clicked and notclicked
						                    	   	*/
						                    	    function changeParentClass(element)
						                    	    {
						                    	    	if(element.parentNode.className=='clicked')
						                    	    	{
						                    	       	 	element.parentNode.className='notclicked';
						                    	       	}
						                    	       	else
						                    	       	{
						                    	       		element.parentNode.className='clicked';	
						                    	       	}
						                    	   	}
						                    	   	
						                    	      /*
							                    	   * to switch the image
							                    	   */
							                    	   function changeimage(parent)
							                    	   {	
								                    	   for (var i = 0; i < parent.childNodes.length; i++)
							                    	    	{
	
															  var child = parent.childNodes[i];
	
														      	if(child.className=='collapsed')
								                    	    	{
								                    	       	 	child.className='expanded';
								                    	       	 	
								                    	       	}
								                    	       	else if(child.className=='expanded')
								                    	    	{
								                    	       	 	child.className='collapsed';
								                    	       	 	
								                    	       	}
							                    	    	}	
						                    	       		
															/* alert(imgID);		
															var id=imgID/10;
													
							                    	   		if(document.getElementById(id) != null)
								                    	   	{
								                    	   		if(document.getElementById(id).className == "collapsed")
									                    	   	{
								                    	   			document.getElementById(id).className = "expanded";
									                    	   	}
									                    	   	else
									                    	   	{
								                    	   			document.getElementById(id).className = "collapsed";
								                    	   		}
								                    	   	}
							                    	   		else
							                    	   		{
								                    	   		//alert(id);
							                    	   		}*/
							                    	   }
							                    	   
						                    	   /*
						                    	   	* to switch the class name of this element between clicked and notclicked
						                    	   	*/
						                    	    function changeChildrenClass(element, id, imgID)
						                    	    {
						                    	    	var changeImg = false;
						                    	  		if(imgID == 0)
						                    	  		{
						                    	    		changeimage(element);
						                    	  		}
						                    	  		else
													    {
						                    	  			changeImg = true;
													    }
				                    	       	 		
						                    	    	var parent = element.parentNode;
														 
						                    	    	for (var i = 0; i < parent.childNodes.length; i++)
						                    	    	{

														  var child = parent.childNodes[i];

														  if(changeImg && child.id == imgID)
														  {
															  changeimage(child);
														  }

													      if (child.id == id)
													      {
													      	if(child.className=='notclicked')
							                    	    	{
							                    	       	 	child.className='clicked';
							                    	       	 	
							                    	       	}
							                    	       	else
							                    	       	{
						                    	       			child.className='notclicked';
						                    	       		}	
													      }
													      else if(child.id > id && child.id < (id + 1))
													      {
													      	child.className='notclicked';
													      }
													    }
													    
						                    	   	}
						                    	  
						                    	</script>
<?php	  	                            





							                    // loop for each chosen questionnaire
							                    foreach($chosen_quests as $quest)
						                        {

					                              	// max number of answers
				                                  	$maxAnswer = 0;
?>
						                      		<li>
														<p onclick="changeParentClass(this);" id="<?php echo $quest['name']; ?>">
															<b>Name: </b>
		 													<a href="./CSVs/<?php echo $quest['name'].'_'.$apkname; ?>.csv" title="Download as CSV" class="bt_downloadCSV"></a>	
<?php														
															echo $quest['name'];
?>
														</p>

<?php
								                        include_once("./include/managers/QuestionnaireManager.php");
								                        // get all questions in this questionnaire
								                        $qust = QuestionnaireManager::getQuestionsForQuestid(
								                        	$db,
								                            $CONFIG['DB_TABLE']['QUESTION'],
								                            $quest['questid']);
								                        // the content of csv as string. Write the head of the table
								                        $csvContent = "#;Question;Question ID;Type of Question;User ID;Answer;Answer ID\n";
?>
							                          	<table class="questTable">
							                          		<!-- the header of the table -->
								                            <thead>
															
																<th>   </th>
									                            <!-- Column 1 -->
									                            <th>#</th>
									                            <!-- Column 2 -->
								                              	<th>Question</th>
								                              	<!-- Column 3 -->
								                              	<th>Qst.ID</th>
								                              	<!-- Column 4 -->
								                             	<th>Type of Question</th>
								                             	<!-- Column 5 -->
								                              	<th>User ID</th>
								                              	<!-- Column 6 -->
									                            <th>Answer</th>
									                            <!-- Column 7 -->
									                            <th>Ans.ID</th>
									                            <!-- end of the header of the table -->
								                            </thead>
								                            
								                            <!-- the body of the table -->
								                            <tbody>
<?php                         
								                              	// $i represents the index of a question
								                              	$i = 1;
								                              	// loop for each question in this questionnaire
								                              	foreach($qust as $q)
								                              	{
									                                include_once("./include/managers/QuestionnaireManager.php");
									                                // get all answers of this question
									                                $answers = QuestionnaireManager::getAnswersForQidAndApkid(
									                                  $db,
									                                  $CONFIG['DB_TABLE']['ANSWER'],
									                                  $q['qid'],
									                                  $apkid);
?>
								                                	<tr  title="one click to collapse/expand more information" onclick="changeChildrenClass(this,<?php echo $i; ?>,0);"> 
																		<!-- changing of the image collapse/expand -->
																		
																		<td id="<?php echo $i/10; ?>" class="collapsed" />
																		
																	
																		<!-- make a new row for this question -->
								                                 	 	<td><?php echo $i; ?></td>
<?php
								                                		if($q['type'] == 1) // multiple choices
								                                  		{
?>
										                                    <td><?php echo trim(substr($q['content'],0,strrpos($q['content'],"["))); ?></td>
										                                    <td><?php echo $q['qid']; ?></td>
										                                    <td>Multiple Choices</td>
<?php                               
																			// adding index, question, question id and its type
																			$csvContent.=
																				$i
																				.";".trim(substr($q['content'],0,strrpos($q['content'],"[")))
																				.";".$q['qid'].
																				";Multiple Choices;";
									                                  	}
									                                  	elseif($q['type'] == 2) // single choice
									                                  	{
?>
										                                    <td><?php echo trim(substr($q['content'],0,strrpos($q['content'],"["))); ?></td>
										                                    <td><?php echo $q['qid']; ?></td>
										                                    <td>Single Choice</td>
<?php                               
										                                  	// adding index, question, question id and its type
																			$csvContent.=
																				$i
																				.";".trim(substr($q['content'],0,strrpos($q['content'],"[")))
																				.";".$q['qid']
																				.";Single Choice;";
									                                  	}
									                                  	else // open question
									                                  	{
?>
										                                    <td><?php echo trim($q['content']); ?></td>
										                                    <td><?php echo $q['qid']; ?></td>
										                                    <td>Open Question</td>
<?php                               
										                                  	// adding index, question, question id and its type
																			$csvContent.=
																				$i
																				.";".trim($q['content'])
																				.";".$q['qid']
																				.";Open Question;";
								                                  		}
									                                  	$csvString = "";

									                                  	// number of answers for this question
									                                  	$numOfAnswer = 0;

									                                  	// to calculate the average answer
									                                  	$answers_array = array();
									                                  	
									                                  	// to pair each answer as a key and how many times as a value
									                                  	$answers_counter = array();
									                                  	
									                                  	// to pair each answer as a key with its html code as a value
									                                  	$answers_rows = array();

									                                  	// to pair each answer as a key with its table row id as a value
									                                  	$answers_itr = array();

									                                  	// to create id for each table row
									                                  	$iTr = 0; 
									                                  	// loop for each answer of this question
									                                  	foreach($answers as $ans)
									                                  	{
										                                  	// for each answer found for this question
									                                  		$numOfAnswer++;

									                                  		// check if this answer comes early
									                                  		if($answers_counter[$ans['content']] == 0)
									                                  		{
										                                  		// increase id for tr
										                                  		$iTr++;

										                                  		// the complate id for a table row
										                                  		$onclick_id = $i.".".$iTr;

										                                  		// put a new iTr key to these answers
										                                  		$answers_itr[$ans['content']] = $iTr;

									                                  			// and make a row for this answer
											                                    // make a new row and skip the first 4 fields (index, question, question id, type)
									                                  			$answers_rows[$ans['content']] =
										                                  			'</tr>
										                                      		<tr title="one click to collapse/expand more information" id="'.$onclick_id.'" class="notclicked"'
										                                      		//.' onclick="changeChildrenClass(this,'.$onclick_id.', '.$i.');">'
										                                      		.'<td/><td/><td/><td/><td/>'
										                                      		."<td>".$ans['userid']."</td>"
											                                    	."<td>".$ans['content']."</td>"
											                                    	."<td>".$ans['aid']."</td>";
									                                  		}
									                                  		else
									                                  		{
									                                  			// the complate id for a table row
									                                  			$onclick_id = $i.".".$answers_itr[$ans['content']];
									                                  			
									                                  			// make a new row and skip the first 4 fields (index, question, question id, type)
									                                  			$answers_rows[$ans['content']].=
										                                  			'</tr>
										                                      		<tr title="one click to collapse/expand more information" id="'.$onclick_id.'" class="notclicked"'
										                                      		//.' onclick="changeChildrenClass(this,'.$onclick_id.', '.$i.');">'
										                                      		.'<td/><td/><td/><td/><td/>'
										                                      		."<td>".$ans['userid']."</td>"
											                                    	."<td>".$ans['content']."</td>"
											                                    	."<td>".$ans['aid']."</td>";
									                                  		}

									                                  		// add this answer in the array of answers
										                                    $answers_array[] = $ans['content'];
										                                    
										                                    // incremtent the number of users who answered with this answer
										                                    $answers_counter[$ans['content']]++;
																			
										                                    // csv content
										                                    $csvString.="\n;;;;".$ans['userid'].";".$ans['content'].";".$ans['aid'];

								                                  		} // end of foreach($answers as $ans)
								                                  		
								                                  		$sortedAnswers = $q['sortedAnswers'];
								                                  		$average = NULL;
								                                  		$polpular = NULL;

								                                  		// get the average/popular answer of this question
								                                  		include_once("./include/managers/QuestionnaireManager.php");
								                                  		
								                                  		if($sortedAnswers != NULL && $sortedAnswers == 1)
								                                  		{ 	
								                                  			// Get the sorted answers
								                                  			$sortedAnswersArray = json_decode(trim(substr($q['content'],(strrpos($q['content'],"[")-1),(strrpos($q['content'],"]")+1))));
								                                  			$average = QuestionnaireManager::getAverageAnswerOfArray($answers_array,$sortedAnswersArray);
								                                  			// set the average answer of this question in html
									                                  		$trWithaverage = "<td>average</td><td>";
									                                  		$trWithaverage .=$average."</td><td></td>";
									                                  		// as well in csv
									                                  		$csvContent .= "average;".$average.";".$csvString."\n";	
								                                  		}
								                                  		else
								                                  		{
								                                  			$popular = QuestionnaireManager::getPopularAnswerOfArray($answers_array);
								                                  			// set the polpular answer of this question in html
									                                  		$trWithaverage = "<td>popular</td><td>";
									                                  		$trWithaverage .=$popular."</td><td></td>";
									                                  		// as well in csv
									                                  		$csvContent .= "popular;".$popular.";".$csvString."\n";
								                                  		}

								                                  		// get all strings of table rows of these answers
								                                  		foreach ($answers_rows as $key => $value)
								                                  		{
								                                  			$onclick_id = $i.".".$answers_itr[$key];
								                                  			$answers_rows[$key] = 
									                                  				'</tr>
										                                      		<tr title="one click to collapse/expand more information" id="'.$i.'" class="notclicked"'
										                                      		.' onclick="changeChildrenClass(this,'.$onclick_id.',0);">'
										                                      		.'<td id="0.'.$i.'" class = "collapsed" /><td/><td/><td/><td/>'
										                                      		."<td>"
										                                      		. $answers_counter[$key]
										                                      		." users</td>"
											                                    	."<td>".$key."</td>"
											                                    	."<td></td>"
											                                    	.$value;
								                                  		
								                                  			$trWithaverage.=$answers_rows[$key];
								                                  			$iTr++;
								                                  		}
								                                  		// finaly print the html code
								                                  		echo $trWithaverage;


																		// set max number of answers
																		if($maxAnswer < $numOfAnswer)
																		{
																			$maxAnswer = $numOfAnswer;
																		}

																		// increment the index of this table
									                                  	$i++;
?>
						                      	         		 	</tr>
<?php
						                     	         		} // end of foreach($qust as $q)
?>
							                            	<!-- end of the body of the table -->
							                            	</tbody>
							                            </table>
							                            There are <?php echo $maxAnswer; ?> users answer this questionnaire.
<?php
							                            // make a csv file for this questionnaire
														
								                        $csvFilePath = './CSVs/'.$quest['name'].'_'.$apkname.'.csv';
								                        $csvF = fopen($csvFilePath, 'w');
								                        fwrite($csvF, $csvContent);
								                        fclose($csvF);
?>
					                      		  	</li>
<?php
							  		   			} // end of foreach($chosen_quests as $quest)
?>
											</ul>
										</div>
									</fieldset>
<?php
  		                        } // end of else : if(empty($chosen_quests))
?>

<?php
							} // end of if($show_us_quest == true)

                            /********************************************
                            ************* ADD QUEST TO US ***************
                            *********************************************/
                            
                            if($show_add_quest == true)
                            {
?>
								
                                <fieldset><legend><h3><b><em>Adding questionnaires for: <?php echo $apkname; ?></h3></b></em></legend>
                                <h4>List of all available not chosen questionnaires:</h4>
                                    <form action="" method="POST">
									<div id="quests_list_top">
										<ul>
											<script>
												/* 
												* To change the class name of its parent between (clicked..) and (notclicked..)
												* It helps to determine which element as been clicked to be opened
												*/
											    function changeClicked(element)
											    {
											    	if(element.parentNode.className=='clickedMarked')
											    	{
											       	 	element.parentNode.className='notclickedMarked';
											       	}
											       	else if(element.parentNode.className=='notclickedMarked')
											    	{
											       	 	element.parentNode.className='clickedMarked';
											       	}
											       	else if(element.parentNode.className=='clickedNotMarked')
											    	{
											       	 	element.parentNode.className='notclickedNotMarked';
											       	}
											       	else if(element.parentNode.className=='notclickedNotMarked')
											    	{
											       	 	element.parentNode.className='clickedNotMarked';
											       	}
											       	else
											       		element.parentNode.className='clickedNotMarked';
											   	}
											   	/* 
												* To change the class name of its parent between (..Marked) and (..NotMarked)
												* It helps to determine which element as been marked
												*/
											   	function changeMark(element,id)
											   	{
											   		if(element.parentNode.className=='clickedMarked')
											    	{
											       	 	element.parentNode.className='clickedNotMarked';
											       	 	document.getElementById(id).value = "notchosen";
											       	}
											       	else if(element.parentNode.className=='notclickedMarked')
											    	{
											       	 	element.parentNode.className='notclickedNotMarked';
											       	 	document.getElementById(id).value = "notchosen";
											       	}
											       	else if(element.parentNode.className=='clickedNotMarked')
											    	{
											       	 	element.parentNode.className='clickedMarked';
											       	 	document.getElementById(id).value = "chosen";
											       	}
											       	else if(element.parentNode.className=='notclickedNotMarked')
											    	{
											       	 	element.parentNode.className='notclickedMarked';
											       	 	document.getElementById(id).value = "chosen";
											       	}
											       	else
											       	{
											       		element.parentNode.className='notclickedMarked';
											       		document.getElementById(id).value = "chosen";
											       	}
											   	}
											</script>
<?php										
											// check if there is any not already chosen questionnaire
											if(!empty($notchosen_quests))
											{
												echo "(one click to select & double click to show/hide more information)";
											}
											else
											{
												echo "There is no more available questionnaires";
											}

											// loop for each not already chosen questionnaires
											foreach($notchosen_quests as $quest)
											{
?>
												<li>
													<!-- title -->
													<p
														onclick="changeMark(this,<?php echo $quest['questid']; ?>);"
														ondblclick="changeClicked(this);">
														<b>Name: </b><?php echo $quest['name']; ?>
													</p>
													<!-- hidden input is used to know if the element been chosen -->
													<input type="hidden" name="<?php echo $quest['questid']; ?>" id="<?php echo $quest['questid']; ?>" value="notchosen"/>
<?php
							                        include_once("./include/managers/QuestionnaireManager.php");
							                        // get all questions of this questionnaire
							                        $qust = QuestionnaireManager::getQuestionsForQuestid(
						                                $db,
						                                $CONFIG['DB_TABLE']['QUESTION'],
						                                $quest['questid']);
?>
													<table class="questTable">
							                            <thead>
							                              <th>#</th>
							                              <th>Question</th>
							                              <th>Type of Question</th>
							                            </thead>
							                            <tbody>
<?php													
								                            // index of the question in the table
								                            $i = 1;
								                            // loop for each question in this questionnaire
							                                foreach($qust as $q)
								                            {
								                            	include_once("./include/managers/QuestionnaireManager.php");
								                                $answers = QuestionnaireManager::getAnswersForQidAndApkid(
								                                  $db,
								                                  $CONFIG['DB_TABLE']['ANSWER'],
								                                  $q['qid'],
								                                  $apkid);
?>
								                                <tr>
								                                	<!-- print the index of this question -->
							                                  		<td><?php echo $i; ?></td>
<?php
									                                if($q['type'] == 1) // multiple choices
								                                    {
?>
									                                    <td><?php echo substr($q['content'],0,strrpos($q['content'],"[")); ?></td>
									                                    <td>Multiple Choices</td>
<?php                               
							                                  		}
									                                elseif($q['type'] == 2) // single choices
									                                {
?>
									                                    <td><?php echo substr($q['content'],0,strrpos($q['content'],"[")); ?></td>
									                                    <td>Single Choices</td>
<?php                               
							                                  		}
							                                  		else // open question
							                                  		{
?>
									                                    <td><?php echo $q['content']; ?></td>
									                                    <td>Open Question</td>
<?php                               
							                                  		}
							                                  		$i++; 
?>
						                                		</tr>
<?php
						                              		} // end of foreach($qust as $q)
?>
												    	</tbody>
						                          	</table>
						                        </li>
<?php										
											} // end of foreach($notchosen_quests as $quest)

?>
										</ul>
									</div>
									<br>
									<div id="quests_list_btm">
										<ul>
											<script>
					                    	    /* 
												* To change the class name of its parent between (clicked..) and (notclicked..)
												* It helps to determine which element as been clicked to be opened
												*/
											    function changeClicked(element)
											    {
											    	if(element.parentNode.className=='clickedMarked')
											    	{
											       	 	element.parentNode.className='notclickedMarked';
											       	}
											       	else if(element.parentNode.className=='notclickedMarked')
											    	{
											       	 	element.parentNode.className='clickedMarked';
											       	}
											       	else if(element.parentNode.className=='clickedNotMarked')
											    	{
											       	 	element.parentNode.className='notclickedNotMarked';
											       	}
											       	else if(element.parentNode.className=='notclickedNotMarked')
											    	{
											       	 	element.parentNode.className='clickedNotMarked';
											       	}
											       	else
											       		element.parentNode.className='clickedNotMarked';
											   	}
											   	/* 
												* To change the class name of its parent between (..Marked) and (..NotMarked)
												* It helps to determine which element as been marked
												*/
											   	function changeMark(element,id)
											   	{
											   		if(element.parentNode.className=='clickedMarked')
											    	{
											       	 	element.parentNode.className='clickedNotMarked';
											       	 	document.getElementById(id).value = "notchosen";
											       	}
											       	else if(element.parentNode.className=='notclickedMarked')
											    	{
											       	 	element.parentNode.className='notclickedNotMarked';
											       	 	document.getElementById(id).value = "notchosen";
											       	}
											       	else if(element.parentNode.className=='clickedNotMarked')
											    	{
											       	 	element.parentNode.className='clickedMarked';
											       	 	document.getElementById(id).value = "chosen";
											       	}
											       	else if(element.parentNode.className=='notclickedNotMarked')
											    	{
											       	 	element.parentNode.className='notclickedMarked';
											       	 	document.getElementById(id).value = "chosen";
											       	}
											       	else
											       	{
											       		element.parentNode.className='notclickedMarked';
											       		document.getElementById(id).value = "chosen";
											       	}
											   	}
					                    	</script>
<?php
											if(empty($chosen_quests))
		  									{
?>
  												<br><h4>There is no questionnaire been added to this user study.</h4><br>
<?php
			  								}
			  								else
			  								{
?>									
												<br><h4>You added already these questionnaires:</h4>
									
<?php	  	                            
							                    foreach($chosen_quests as $quest)
						                        {
?>
	                      							<li>
														<p 
															onclick="changeMark(this,<?php echo $quest['questid']; ?>);"
															ondblclick="changeClicked(this);">
															<b>Name: </b><?php echo $quest['name']; ?>
														</p>
														<!-- hidden input is used to know if the element been chosen -->
														<input type="hidden" name="<?php echo $quest['questid']; ?>" id="<?php echo $quest['questid']; ?>" value="notchosen"/>
<?php
							                            include_once("./include/managers/QuestionnaireManager.php");
							                          	$qust = QuestionnaireManager::getQuestionsForQuestid(
							                            	$db,
							                            	$CONFIG['DB_TABLE']['QUESTION'],
							                            	$quest['questid']);
?>
						                          		<table class="questTable">
								                            <thead>
															  
								                              <th>#</th>
								                              <th>Question</th>
								                              <th>Type of Question</th>
								                            </thead>
						                            		<tbody>
<?php                         
	                              								$i = 1;
								                              	foreach($qust as $q)
								                              	{
									                                include_once("./include/managers/QuestionnaireManager.php");
									                                $answers = QuestionnaireManager::getAnswersForQidAndApkid(
									                                  $db,
									                                  $CONFIG['DB_TABLE']['ANSWER'],
									                                  $q['qid'],
									                                  $apkid);
?>
									                                <tr>
									                                  	<td><?php echo $i; ?></td>
<?php
									                                  	if($q['type'] == 1) // multiple choices
									                                  	{
?>
										                                    <td><?php echo substr($q['content'],0,strrpos($q['content'],"[")); ?></td>
										                                    <td>multiple Choices</td>
<?php                               
									                                  	}
									                                  	elseif($q['type'] == 2) // single choices
									                                  	{
?>
										                                    <td><?php echo substr($q['content'],0,strrpos($q['content'],"[")); ?></td>
										                                    <td>Single Choices</td>
<?php                               
									                                  	}
									                                  	else // open question
									                                  	{
?>
										                                    <td><?php echo $q['content']; ?></td>
										                                    <td>Open Question</td>
<?php                               
	                                  									}
	                                  									$i++;
?>
                                									</tr>
<?php
                              									} // end of foreach($qust as $q)
?>
                            								</tbody>
                          								</table>
                          							</li>
<?php
		  		                           		} // end of foreach($chosen_quests as $quest)
	  		                           		} // end of else : if(empty($chosen_quests))
?>
										</ul>
									</div>
									<br>
									<input type="submit" value="update" name="update" style="height: 25px; width: 100px"/>
								</form></fieldset>
<?php
								if(isset($_POST['update']))
								{
									
									$numOfSelected = 0;
									foreach($notchosen_quests as $quest)
									{
										if(isset($_POST[$quest['questid']]) && $_POST[$quest['questid']] == "chosen")
										{
											$numOfSelected++;
											QuestionnaireManager::pairQuestionnireWithApk($db, $CONFIG['DB_TABLE']['APK_QUEST'], $apkid, $quest['questid']);
										}
									}

									foreach($chosen_quests as $quest)
									{
										if(isset($_POST[$quest['questid']]) && $_POST[$quest['questid']] == "chosen")
										{
											$numOfSelected++;
											QuestionnaireManager::unpairQuestionnireWithApk($db, $CONFIG['DB_TABLE']['APK_QUEST'], $apkid, $quest['questid']);
										}
									}

									if($numOfSelected > 0)
									{
										header("Location: ucp.php?m=addquest&id=".$apkid);
									}
									else
									{
?>
										You did not select any questionnaire to update on this user study!
										<br>
										You may want to select a questionnaire by one click on it and then click on update button.
										<br>
<?php
									} 
								} // end of if(isset($_POST['update']))							
                            } // end of if($show_add_quest == true)
                            
                            /********************************************
                            *********** UPDATE MY APK PAGE **************
                            *********************************************/
                            
                            if(isset($SHOW_UPDATE_PAGE) && $SHOW_UPDATE_PAGE == 1)
                            {
?>                              <fieldset><legend><h3><em>Update</em></h3></legend>
                            	<form action="update.php" method="post" enctype="multipart/form-data" class="upload_form">
	                                <p>Program name (title):</p>
	                                <h4><?php echo $apk_to_update['apktitle']; ?></h4><br>
                                        <p>Program description:</p>
	                                <textarea cols="30" rows="6" name="apk_description">
	                                	<?php echo $apk_to_update['description']; ?>
	                                </textarea><br><br><hr><br>
	                                <p>Lowest android version needed for my program to run:</p>
	                                <select name="apk_android_version">
<?php
	                                    $_SESSION['APKID'] = $apk_to_update['apkid'];
	                                    for($i=0; $i<count($API_VERSION); $i++)
	                                    {
	                                        echo
	                                        	'<option value="'. $API_VERSION[$i][0] .'"'. 
	                                        	($apk_to_update['androidversion'] == $API_VERSION[$i][0] ? ' selected="selected" ' : '') 
	                                        	.'>'. $API_VERSION[$i][1] .'</option>';    
	                                    }
?>
	                                </select>                              
	                                
	                                <p style="margin: 20px 0;">My program uses following sensors:</p>
	                                <ul>
<?php
	                                  	$apk_to_update_sensors = json_decode($apk_to_update['sensors']);
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
                                        <hr>
                                        <label for="file">Select a file:</label> 
	                                <input type="file" name="userfile" id="file" style="margin: 15px 0;">
	                                <br /><br />
	                                <button>Update</button>
	                                <p style="margin-top: 50px;"></p>
                                </form></fieldset>
<?php
                            } // end of if(isset($SHOW_UPDATE_PAGE) && $SHOW_UPDATE_PAGE == 1)

                            
                            /********************************************
                            ******* SCIENTIST CREDINTIAL REQUEST ********
                            *********************************************/
                            if(
                            	$MODE == 'PROMO' && !isset($_POST['promo_sent'])
                            	&& (!isset($USER_ALREADY_ACCEPTED) || !isset($USER_PENDING)))
                            {    
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

                            if($MODE == 'PROMO' && isset($_POST['promo_sent']))
                            {
?>
                                <div class="promo_sent_text">
                                    <p>Your scientist application was sent. Thank you for interesting in that!</p>
                                </div>
<?php
                            }
                            
                            if(
                            	$MODE == 'PROMO' && isset($USER_PENDING) && $USER_PENDING == 1
                                && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED != 1)
                            {
?>
                                
                                <div class="promo_sent_text">
                                    <p>Your application to become a scientist was already sent to us.</p>
                                </div>
<?php
                            }
                            
                            if($MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 1)
                            {
?>
                                <div class="promo_sent_text">
                                    <p>You are already a scientist!</p>
                                </div>
<?php
                            }
                            
                            // nobody wants you as scientist
                            if(
                            	$MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 0 
                                && isset($USER_PENDING) && $USER_PENDING == 0)
                            {
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