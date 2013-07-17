<?php
session_start();

include_once("./include/functions/func.php");

/**
* Add survey handler
*/ 

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_POST['get_questions_pwd']) && $_POST['get_questions_pwd'] == 6767 &&
    isset($_POST['get_questions']) && is_numeric($_POST['get_questions'])){

    
  /*
   * Select all surveys from DB to show them later on
   */
   
   include_once("./config.php");
   include_once("./include/functions/dbconnect.php");
   
   $sql = 'SELECT * 
           FROM `'. $CONFIG['DB_TABLE']['QUESTION'] .'` 
           WHERE questid = '. $_POST['get_questions'];
            
   $result=$db->query($sql);
   $SURVEYS_ALL = $result->fetchAll(PDO::FETCH_ASSOC);
?>  
   <div class="row-fluid" name="survey_container_<?php echo $SURVEY['questid']; ?>">
    <div class="span10" name="survey_body">
      <!--Body content-->
      <?php
        $i = 1;
        foreach($SURVEYS_ALL as $SURVEY){
            echo '#'. $i .' '. $SURVEY['content'] .'<br>'; 
            $i++;
        }   
       ?>
    </div>
    <div class="span2" name="survey_sidebar">
      <!--Sidebar content-->
      <?php
      include_once('./include/_survey_controls.php');    
      ?>
    </div>
  </div>
  
<?php
}

/*
* Handle allow access for scientists in admin panel
*/

if(isset($_SESSION['USER_LOGGED_IN']) && 
    isset($_REQUEST['hash']) && !empty($_REQUEST['hash']) && is_md5($_REQUEST['hash'])){
    
    if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
       
       include_once("./config.php");
       include_once("./include/functions/dbconnect.php"); 
           
       $request = $_REQUEST['hash'];
           
       // update his request, set to not pending one              
       $sql = "UPDATE request
                 SET
                 pending = 0, accepted = 1 
                 WHERE
                 uid = (SELECT userid 
                         FROM user
                         WHERE hash = '". $request ."')";
                            
        $db->exec($sql);
       
        // USER IS NOW IN A SCIENTIST GROUP
        $sql = "UPDATE user 
                SET usergroupid= 2 
                WHERE hash = '". $request ."'";
       
        $db->exec($sql);
        
        echo "0";
   }
}

/*
* Handling user CREATES or JOINS existing group 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && isset($_SESSION['USER_ID']) &&
    isset($_REQUEST['group_name']) && !empty($_REQUEST['group_name']) &&
    isset($_REQUEST['group_password']) && !empty($_REQUEST['group_password']) &&
   (isset($_REQUEST['createGroup']) && $_REQUEST['createGroup'] == $_SESSION['USER_ID'] || 
    isset($_REQUEST['joinGroup']) && $_REQUEST['joinGroup'] == $_SESSION['USER_ID']) &&
    !(isset($_REQUEST['createGroup']) && isset($_REQUEST['joinGroup']))){
        
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php");
        
    $groupname = trim($_REQUEST["group_name"]);
    $grouppwd = trim($_REQUEST["group_password"]);
        
    // if user wants to create a group    
    if(isset($_REQUEST['createGroup'])){
        
       // the user wants to create a group, check if the group name is already given
        $sql_check = "SELECT * 
                      FROM ".$CONFIG['DB_TABLE']['RGROUP']. " 
                      WHERE name='".$groupname."'";
                      
        $check_result = $db->query($sql_check);
        $check_row = $check_result->fetch();
        
        if(!empty($check_row)){
            // group-name is already given
            $jcstatsus = 2;
            echo $jcstatsus; 
        }else{
            // update the databases
            $members = json_encode(array(intval($_SESSION['USER_ID'])));
            $sql_newgroup = "INSERT INTO ".$CONFIG['DB_TABLE']['RGROUP']." 
                            (name, password, members) 
                            VALUES 
                            ('". $groupname ."', '". $grouppwd . "', '" . $members . "')";
            
            $sql_update2 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." 
                            SET rgroup='".$groupname."' 
                            WHERE userid=".$_SESSION['USER_ID'];
                            
            $db->exec($sql_newgroup);
            $db->exec($sql_update2);
            
            $jcstatsus = 3;
            echo $jcstatsus;
        }
    }
    
    // if user wants to join existing group    
    if(isset($_REQUEST['joinGroup'])){
        
        // the user wants to join a group
        // check if the user has provided a valid name of the group and password
        $sql_join = "SELECT * 
                     FROM ".$CONFIG['DB_TABLE']['RGROUP']. " 
                     WHERE name='".$groupname."' AND password='".$grouppwd."'";
                     
        $rgroup_result = $db->query($sql_join);
        $rgroup_row = $rgroup_result->fetch();
        
        if(!empty($rgroup_row)){
           
            $jcstatsus = 1; // the user has provided valid rgroup-name and password
            
            // update the tables
            $sql_update1 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." 
                            SET rgroup='".$groupname."' 
                            WHERE userid=".$_SESSION['USER_ID'];
           
            $db->exec($sql_update1);
            
            $sql_members = "SELECT members 
                            FROM ".$CONFIG['DB_TABLE']['RGROUP']." 
                            WHERE name='".$groupname."'";
                            
            $members_result = $db->query($sql_members); 
            $members_row = $members_result->fetch();
            
            $members = json_decode($members_row['members']);
            $members[] = intval($_SESSION['USER_ID']);
            $members = array_unique($members);
            
            sort($members);
            
            $members = json_encode($members);
            
            $sql_update3 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." 
                            SET members='".$members."' 
                            WHERE name='".$groupname."'";
            
            $db->exec($sql_update3);
            
            echo $jcstatsus;
        }else{
            $jcstatsus = 4; // that group name doesn't exist
            echo $jcstatsus;
        } 
    }
}
 
/*
* Handling user LEAVES his group 
*/

if(isset($_SESSION['USER_LOGGED_IN']) && isset($_SESSION['USER_ID']) &&
   isset($_REQUEST['leaveGroup']) && !empty($_REQUEST['leaveGroup']) &&
   intval($_REQUEST['leaveGroup']) == $_SESSION['USER_ID']){
      
        include_once("./config.php");
        include_once("./include/functions/dbconnect.php");
        
        $sql_leave = "SELECT rgroup 
                      FROM ".$CONFIG['DB_TABLE']['USER']." 
                      WHERE userid=".$_SESSION['USER_ID'];
        
        $old_group_result =  $db->query($sql_leave);
        $aRow = $old_group_result->fetch();
        
        $groupname = " ";
        
        if(!empty($aRow))
            $groupname = $aRow['rgroup'];
        
        // update the tables
        $sql_update1 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." 
                        SET rgroup='' 
                        WHERE userid=".$_SESSION['USER_ID'];
                        
        $db->exec($sql_update1);
        
        // remove the user from the group
        $sql_members = "SELECT members 
                        FROM ".$CONFIG['DB_TABLE']['RGROUP']." 
                        WHERE name='".$groupname."'";
                        
        $members_result = $db->query($sql_members); 
        $members_row = $members_result->fetch();
        
        $members = json_decode($members_row['members']);
        $newMembers = array();
        
        foreach($members as $mid)
            if($mid != $_SESSION['USER_ID'])
                $newMembers[] = $mid;
                
        $sql_update4;
        
        if(count($newMembers) == 0)
            $sql_update4 = "DELETE 
                            FROM ".$CONFIG['DB_TABLE']['RGROUP']." 
                            WHERE name='".$groupname."'";
        else{
            $newMembers = json_encode($newMembers);
            $sql_update4 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." 
                            SET members='".$newMembers."' 
                            WHERE name='".$groupname."'";
        }
        $db->exec($sql_update4); 
        
        echo $groupname;
   }

/*
* Handling of json request for new devices table
*/
                                                                 
if(isset($_SESSION['USER_LOGGED_IN']) && 
   isset($_REQUEST['pages']) && !empty($_REQUEST['pages']) && 
   isset($_REQUEST['pageMax']) && !empty($_REQUEST['pageMax']) &&
   isset($_REQUEST['curPage']) && !empty($_REQUEST['curPage'])){
    //configuration of connection
    
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php");

    $pages = intval($_REQUEST['pages']);
    $pageMax = intval($_REQUEST['pageMax']);
    $curPage = intval($_REQUEST['curPage']);
    
    $USER_DEVICES = array();

    $sql = 'SELECT * 
           FROM hardware 
           WHERE uid = '. $_SESSION['USER_ID'] .' 
           LIMIT '.((($curPage-1)*$pageMax)).', '.($curPage*$pageMax);
                                   
    $result = $db->query($sql);
    $devices = $result->fetchAll(PDO::FETCH_ASSOC);
      
    if(!empty($devices)){
      $USER_DEVICES = $devices;
    }
    
    //$ar = array('modelname' => $_SESSION['USER_ID']);
    // return devices as json
    echo json_encode($USER_DEVICES);  
}

/*
* Handle user LOGIN
*/
if(isset($_POST["submit"]) && $_POST["submit"] == "1"){
	
    include_once("./config.php");
	//If the login exists
	if(isset($_POST["email_login"]) && !empty($_POST["email_login"]) && preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST["email_login"])){
		//And the password exists
		if(isset($_POST["password_login"]) && !empty($_POST["password_login"])){
			if(strlen($_POST["password_login"]) < 6){
				// password too short
				echo $CONFIG['LOGIN_RESPONSE']['SHORT_PASSWORD'];
			}
			else{
				//Import of the connection�s file to database
				include_once("./include/functions/dbconnect.php");
				
				$USER_EMAIL =  $_POST["email_login"];
				$USER_PASSWORD =  $_POST["password_login"];
				
				//Select the user
				$sql = "SELECT *
                  FROM ". $CONFIG['DB_TABLE']['USER'] ."
                  WHERE email = '". $USER_EMAIL ."'
                  AND password = '". $USER_PASSWORD ."'";
				
				$result = $db->query($sql);
				$row = $result->fetch();
				
				//users exist in database
				if(!empty($row)){
				
					//test if the user is registered
					if($row["confirmed"] == 1){
				
						$USER_CONFIRMED = 1;
						$_SESSION["USER_LOGGED_IN"] = 1;
				
						$_SESSION["USER_ID"] =   $row["userid"];
						$_SESSION["GROUP_ID"] =  $row["usergroupid"];
						$_SESSION["EMAIL"] =	 $row["email"];
						// 					$_SESSION["LOGIN"] =     $row["login"];
						$_SESSION["PASSWORD"] =  $row["password"];
						$_SESSION["FIRSTNAME"] = $row["firstname"];
						$_SESSION["LASTNAME"] =  $row["lastname"];
				
						// we have an admin here logged in
						if($row["usergroupid"] == 3){
							$_SESSION["ADMIN_ACCOUNT"] =  "YES";
						}
						echo $CONFIG['LOGIN_RESPONSE']['OK'];
				
					}else{
						echo $CONFIG['LOGIN_RESPONSE']['NOT_CONFIRMED'];
					}
				}else{
					echo $CONFIG['LOGIN_RESPONSE']['WRONG_LOGIN_OR_PASSWORD'];
				}	
			}
		}else{
			echo $CONFIG['LOGIN_RESPONSE']['MISSING_PASSWORD'];
		}
	}else{
		echo $CONFIG['LOGIN_RESPONSE']['MISSING_LOGIN'];
	}
}

/**
 * Handling of requests for checking the uniqueness of an email
 * a new user is trying to register with.
 * The new user has to provide a new email.
 */
if(isset($_POST['isEmailUnique']) && !empty($_POST['isEmailUnique'])){
	include_once("./config.php");
	include_once("./include/functions/dbconnect.php");
	include_once("./include/functions/logger.php");
	
	// If the email is unique, 0 is returned
	// if the email is already contained in the database (someone used it already) 1 is returned
	$logger->logInfo(" ###################### content_provider.php request for only checking the email ############################## ");
	if(isEmailUnique($_POST["isEmailUnique"], $CONFIG, $db, $logger)){
		echo 0; // no users with such email found, the email is thus unique
	}
	else
		echo 1; // a user has already confirmed this email, the email is thus NOT unique
}

/**
 * Checking the uniqueniness of the provided form on registration and 
 * sending the email to the user if it is.
 * This functions echoes back:
 * 		0 if the email was unique and confirmation email has been sent
 * 		1 if the email was not unique
 * 		2 if there has been a problem sending the email (i.e. the mail server did not respond)
 */
if(isset($_POST["submitted"]) && $_POST["submitted"] == "1"){
	include_once("./config.php");
	include_once("./include/functions/dbconnect.php");
	include_once("./include/functions/logger.php");
	$logger->logInfo(" ###################### content_provider.php request for only checking the email AND registering ############################## ");
	
	$USER_CREATED = 0;
	
	// init
	$FIRSTNAME = $_POST["firstname"];
	$LASTNAME = $_POST["lastname"];
	$EMAIL = $_POST["email"];
	$PASSWORD = $_POST["password"];
	$CUR_TIME = time();
	$CONFIRM_CODE = md5($EMAIL);
	if(isEmailUnique($EMAIL, $CONFIG, $db, $logger)){
		
		// we have no duplicate emails
		// so we can insert new entry
		$sql = "INSERT INTO ". $CONFIG['DB_TABLE']['USER'] ." (usergroupid, firstname, lastname, password,
			hash, email, ipaddress, lastactivity, joindate, passworddate)
			VALUES
			(0, '". $FIRSTNAME ."', '". $LASTNAME ."', '". $PASSWORD ."', '". $CONFIRM_CODE ."','". $EMAIL ."',
					'". $_SERVER["REMOTE_ADDR"] ."', ". $CUR_TIME .", ". $CUR_TIME .", ". $CUR_TIME .")";
		$db->exec($sql);
		$USER_CREATED = 1;
		
		// compose email to user
		$to = $EMAIL;
		$subject = "MoSeS: Please confirm your registration";
		$from = "admin@moses.tk.informatik.tu-darmstadt.de";
		$message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
		$message .= "Please follow this link to confirm your registration: ";
		$message .= "http://". $_SERVER["SERVER_NAME"] . "/moses/registration.php" ."?confirm=". $CONFIRM_CODE;
		
		$headers = "From: $from";
		$sent = mail($to, $subject, $message, $headers);
		
		// sending was successful?
		if(!$sent) { // there was a problem sending email
			echo 2;
		}
		else
			echo 0; 
	}
	else
		echo 1; // the email was not unique
}

/**
 * Checking the correctness of the email provided form on forgot password form and
 * sending the email to the user if it is.
 * This functions echoes back:
 * 		0 if the provided email is known by the server and the email has been sent
 * 		1 if the email provided by the user is unknown to the server
 * 		2 the server knows the email, but there has been a problem sending the email (i.e. the mail server did not respond)
 */
if(isset($_POST["submitted_forgot"]) && $_POST["submitted_forgot"] == "1"){
	include_once("./config.php");
	include_once("./include/functions/dbconnect.php");
	include_once("./include/functions/logger.php");
	$logger->logInfo(" ###################### content_provider.php request for checking the email AND sending password forgot email ############################## ");

	// init
	$FIRSTNAME;
	$LASTNAME;
	$EMAIL = $_POST["email_for"];
	$CONFIRM_CODE = md5($EMAIL);
	if(!isEmailUnique($EMAIL, $CONFIG, $db, $logger)){

		// the server knows the email, get the first and last name
		$sql = "SELECT firstname, lastname FROM ". $CONFIG['DB_TABLE']['USER'] ." WHERE email='".$EMAIL."'";
		$result = $db->query($sql);
		$fsname = $result->fetch();
		$FIRSTNAME = $fsname['firstname'];
		$LASTNAME = $fsname['lastname'];

		// compose email to user
		$to = $EMAIL;
		$subject = "MoSeS: Password reset";
		$from = "admin@moses.tk.informatik.tu-darmstadt.de";
		$message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
		$message .= "Please follow this link to enter a new password: ";
		$message .= "http://". $_SERVER["SERVER_NAME"] . "/moses/forgot.php" ."?newpassword=". $CONFIRM_CODE;

		$headers = "From: $from";
		$sent = mail($to, $subject, $message, $headers);

		// sending was successful?
		if(!$sent) { // there was a problem sending email
			echo 2;
		}
		else
			echo 0;
	}
	else
		echo 1; // the email was not found in the database
}

/**
 * 
 * Returns true if and only if there is a user that has registered the consumed email
 * @param String $email the email that has to be checked for uniquiness
 * @param mappings $CONFIG 
 * @param database-Object $db
 * @param logger-Object $logger
 * @return boolean
 */
function isEmailUnique($email, $CONFIG, $db, $logger){
	$logger->logInfo(" ###################### content_provider.php isEmailUnique ############################## ");
	
	// search the database for users who are registered with the email
	$sql = "SELECT confirmed
           FROM ".$CONFIG["DB_TABLE"]["USER"]." WHERE email='".$email."'";
	$logger->logInfo($sql);
	$result = $db->query($sql);
	$emails = $result->fetchAll(PDO::FETCH_ASSOC);
	
	if(empty($emails)){
		return true; // no users with such email found, the email is thus unique
	}
	else
		return false; // a user has already used this email, the email is thus NOT unique
}

/**
 * Changes the password. The user is identified by the provided email-hash
 * This functions echoes back:
 * 		0 if the password is successfully changed
 */
if(isset($_POST["hash"]) && isset($_POST["newPassword"])){
	include_once("./config.php");
	include_once("./include/functions/dbconnect.php");
	include_once("./include/functions/logger.php");
	$logger->logInfo(" ###################### content_provider.php changing password ############################## ");

	// init
	$CUR_TIME = time();
	// update the password
	$sql = "UPDATE ".$CONFIG["DB_TABLE"]["USER"]." 
			SET password='".$_POST["newPassword"]."', passworddate=".$CUR_TIME." WHERE hash='".$_POST["hash"]."'";
	$db->exec($sql);
	echo "0";
}

/*
*  UPLOAD Handling. User study CREATE
*/
if(isset($_SESSION['USER_LOGGED_IN']) &&
   isset($_REQUEST['study_create']) && !empty($_REQUEST['study_create']) && $_REQUEST['study_create'] == 2975){
    
    include_once("./config.php");
    include_once("./include/functions/logger.php");
    include_once("./include/functions/dbconnect.php");
    
    /**
    *  SETTING FILE FOR UPLOAD
    */
    $allowedTypes = array('.apk');
    $maxFileSize = $CONFIG['UPLOAD']['FILESIZE'];//stting the maximale size of file
    $uploadPath = './apk/'; // folder to save to

    $filename = $_FILES['file']['name']; // gets filename
    $fileExt = substr($filename, strripos($filename, '.'), strlen($filename)-1);//gets the extension of file

        
    /**
    * DECRYPTING THE NAME OF FILE FROM DATABSE
    */

    $sql = "SELECT hash 
            FROM ". $CONFIG['DB_TABLE']['USER'] ." 
            WHERE userid = ". $_SESSION["USER_ID"];
           
    $result = $db->query($sql);
    $row = $result->fetch();

    //if the hash of file exists in database
    if(!empty($row))
    {
          
        $HASH_DIR = $row['hash'];   
        $HASH_FILE = md5(time() . $filename);

        $uploadPath .= $HASH_DIR . "/";

        // check if directory exists
        clearstatcache();

        if(!is_dir($uploadPath))
        {
            $oldumask = umask(0);
            //Test if the access permition allowed the upload
            if(!mkdir($uploadPath, 0777, true))
            {
                // failed to create folder
                umask($oldumask);
                die('0');
                //header("Location: ucp.php?m=upload&res=0");
            }
            umask($oldumask); 
        }
       
    }
    else{
       // no hash for user found
       die('0');
       //header("Location: ucp.php?m=upload&res=0");
    }

    /**
    * Checking for necessary conditions
    */
    if(!in_array($fileExt, $allowedTypes))
        die('2');
      //header("Location: ucp.php?m=upload&res=2");
     
    if(filesize($_FILES['file']['tmp_name']) > $maxFileSize)
        die('3');
      //header("Location: ucp.php?m=upload&res=3");
           
    if(!is_writable($uploadPath))
        die('4');
      //header("Location: ucp.php?m=upload&res=4");
     
    chmod($_FILES['file']['tmp_name'], 0777);       

    /**
    * Moving file into its directory and storing that data in DB
    */
    if(is_uploaded_file($_FILES['file']['tmp_name']) 
        && move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . $HASH_FILE . $fileExt))
    {
        
        // permission access rwx to File
        if(!chmod($uploadPath . $HASH_FILE . $fileExt, 0777)){
            die('4');
           //header("Location: ucp.php?m=upload&res=4"); 
        }
         
        
        $logger->logInfo("------------------ REQUESTED UPLOAD------------------");
        /**
        * Parsing description of APKs
        */
        
        
        $RESTRICTION_USER_NUMBER = -1;
        $SELECTED_USERS_LIST = '';
        
        // PREPARING VARIABLES FOR INSERTION TO DB
        $candidates = array();
        $pending_users = array();
        $notified_users = array();
        
        // Initilization the contents of the pages
        
        $apk_title = $_POST['apk_title'];
        $androidversion = $_POST['android_version_select'];
        $description = $_POST['description'];
        $radioButton = $_POST['study_period'];
        $startcriterion = NULL;
        $runningtime = NULL;
        $private = (isset($_POST['private']) ? 1 : 0);
        $startdate = $_POST['start_date'];
        $enddate = $_POST['end_date'];        
        $maxdevice = $_POST['max_devices_number'];
        $inviteinstall = (isset($_POST['setup_types']) ? 1 : 0);

        $RESTRICTION_USER_NUMBER = $maxdevice;
        
        include_once("./include/managers/HardwareManager.php");
        // get the list of candidates with the specified android version
        // Check if the user wants only members from his group to take part on the user study
        
        if($private == 1 && isset($_SESSION['RGROUP'])){
            $rows = HardwareManager::getCandidatesForAndroidFromGroup($db, $CONFIG['DB_TABLE']['HARDWARE'], $CONFIG['DB_TABLE']['RGROUP'],
                                                                $androidversion, $_SESSION['RGROUP'],$logger);
        }else{
            $rows =  HardwareManager::getCandidatesForAndroid($db, $CONFIG['DB_TABLE']['HARDWARE'], $androidversion, $logger);    
        }
        // check the filters
        if(!empty($rows))
        {
            foreach($rows as $hardware){
                $hwFilter_array = json_decode($hardware['filter']);
                $apkSensors_array = json_decode($SENSOR_LIST_STRING);
                if(isFilterMatch($hwFilter_array, $apkSensors_array))
                {
                    $candidates[] = intval($hardware['hwid']);
                }
            }
            shuffle($candidates);
        }

        /*
        *  WRITE APK TO DATABASE AND START USER STUDY IF NEEDED
        */  
       
        // convert to json 
        $candidates = json_encode($candidates);
        $pending_users = json_encode($pending_users);
        $notified_users = json_encode($notified_users);

        /* USTUDY_FINISHED encodings
        * -1  update
        * 0  user-study
        * 1  finished
        */
        $USTUDY_FINISHED = 0;
        if($radioButton == "1")
        {
            $startcriterion = 0;
            $runningtime = NULL;

            // user study should be finished if the end date is today or in the past days
            if($enddate != NULL && strtotime($enddate) <= strtotime(date("Y-m-d", mktime(0, 0, 0, 0, 0, 0000))))
            {
                $USTUDY_FINISHED = 1;
            }
        }
        elseif($radioButton == "2")
        {
            $startdate = NULL;
            $enddate = NULL;
            
            $startcriterion = $_POST['start_after_n_devices'];
            
            // converting to milliseconds
            switch($_POST['running_time_value']){        
                case 'h': $runningtime = intval($_POST['running_time'])*60*60*1000;   
                        break;
                case 'd': $runningtime = intval($_POST['running_time'])*24*60*60*1000;
                        break;
                case 'm': $runningtime = intval($_POST['running_time'])*30*24*60*60*1000;
                        break;
                case 'y': $runningtime = intval($_POST['running_time'])*12*30*24*60*60*1000;
                        break;
            }
        }
            
       
        /**
        * Store filename, hash in DB and other informations
        * inserting into APK table
        */
        $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['APK'] ." (userid, userhash, apkname,
                                 apkhash, sensors, description, private,
                                 apktitle, restriction_device_number, pending_devices,
                                 candidates, notified_devices, androidversion, ustudy_finished,
                                 startdate, startcriterion, enddate, runningtime, inviteinstall
                                 )
                                  VALUES 
                                  (". $_SESSION["USER_ID"]
                                    .", '". $HASH_DIR ."'"
                                    .", '". $filename ."'"
                                    .", '" . $HASH_FILE ."'"
                                    .", '". $sensors ."'"
                                    .", '". $description ."'"
                                    .", ". $private
                                    .", '". $apk_title ."'"
                                    .", ". $RESTRICTION_USER_NUMBER
                                    .", '". $pending_users ."'"
                                    .", '". $candidates ."'"
                                    .", '". $notified_users ."'"
                                    .", '". $androidversion ."'"
                                    .", ". $USTUDY_FINISHED
                                    .", '". $startdate."'"
                                    .", ". $startcriterion
                                    .", '". $enddate."'"
                                    .", '". $runningtime."'"
                                    .", '". $inviteinstall."' )";
                                    
        $logger->logInfo("Upload APK sql: ". $sql);

        // WARNING: hashed filename is WITHOUT .apk extention!
        $db->exec($sql);

        die('1');
        //header("Location: ucp.php?m=upload&res=1");
    }else{
        die('0');
        //header("Location: ucp.php?m=upload&res=0");
    }
}

if(isset($_SESSION['USER_LOGGED_IN']) && 
   isset($_REQUEST['study_update']) && !empty($_REQUEST['study_update']) && $_REQUEST['study_update'] == 6825){
       
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php");
    include_once("./include/functions/logger.php");
    include_once('./include/functions/klogger.php');
        
    $logger = new KLogger(MOSES_HOME . "/log", KLogger::INFO);

    $logger->logInfo("###################### UPDATE USER STUDY #########################");
    
    $apkId = $_POST['apk_id'];
    
    /* check if that user can actually modify that APK */
        
    // restoring old data in case of new file
    $sql = "SELECT apkname, apkhash 
            FROM ". $CONFIG['DB_TABLE']['APK'] ." 
            WHERE userid = ". $_SESSION["USER_ID"] ." AND apkid = ". $apkId;
           
    $result = $db->query($sql);
    $row = $result->fetch();    
    
    if(empty($row)){
        die('-1');  // that user can't access and modify the apk!
    }
    
    $oldAPKName = $row['apkname'];
    $oldAPKHash = $row['apkhash'];
        
    /**
    *  SETTINGS FOR UPLOAD
    */
    $allowedTypes = array('.apk');
    $maxFileSize = $CONFIG['UPLOAD']['FILESIZE'];
    $uploadPath = './apk/'; // folder to save to

    $filename = $_FILES['file']['name']; // gets filename
    $fileExt = substr($filename, strripos($filename, '.'), strlen($filename)-1);
    $FILE_WAS_UPLOADED = FALSE;
    
    
    if($_FILES['file']['error'] !== 4){
        
        $FILE_WAS_UPLOADED = TRUE;
        
        /**
        * Connect to DB and get hashes for folder and file
        */    

        $sql = "SELECT hash 
                FROM ". $CONFIG['DB_TABLE']['USER'] ." 
                WHERE userid = ". $_SESSION["USER_ID"];
               
        $result = $db->query($sql);
        $row = $result->fetch();

        if(!empty($row)){
          
        $HASH_DIR = $row['hash'];   
        $HASH_FILE = md5(time() . $filename);

        $uploadPath .= $HASH_DIR . "/";

        // check if directory exists
        clearstatcache();

        if(!is_dir($uploadPath)){
            $oldumask = umask(0);
            if(!mkdir($uploadPath, 0777, true)){
                // folder failed to create
                umask($oldumask);
                die('0');
                //header("Location: ucp.php?m=upload&res=0");
            }
            umask($oldumask); 
        }
           
        }else{
           // no hash for user found
           die('0');
           //header("Location: ucp.php?m=upload&res=0");
        }

        /**
        * Checking for necessary conditions
        */
        if(!in_array($fileExt, $allowedTypes))
          die('2');
          //header("Location: ucp.php?m=upload&res=2");

        if(filesize($_FILES['file']['tmp_name']) > $maxFileSize)
          die('3');
          //header("Location: ucp.php?m=upload&res=3");
               
        if(!is_writable($uploadPath))
          die('4');
          //header("Location: ucp.php?m=upload&res=4");
         
        chmod($_FILES['file']['tmp_name'], 0777);       

    }else{
        $logger->logInfo("NO FILE WAS UPLOADED!");
    }
    
    /**
    * Moving file into its directory and storing that data in DB
    * or if no file was uploaded -> proceed
    */
    if(!$FILE_WAS_UPLOADED || is_uploaded_file($_FILES['file']['tmp_name']) 
        && move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . $HASH_FILE . $fileExt)){
        
        if($FILE_WAS_UPLOADED){
            // fix file permission
            if(!chmod($uploadPath . $HASH_FILE . $fileExt, 0777)){
                die('4');
                //header("Location: ucp.php?m=upload&res=4"); 
            }
        }
         
        /**
        * Building sensors string in JSON-Array-Format
        */
        if(isset($_POST['sensors']) && is_array($_POST['sensors']) && count($_POST['sensors']) > 0){
            
            /*
            $RAW_SENSOR_LIST = $_POST['sensors'];
            $SENSOR_LIST_STRING = '[';
            
            foreach($RAW_SENSOR_LIST as $sensor){
              $SENSOR_LIST_STRING .= $sensor .','; 
            }
            
            $SENSOR_LIST_STRING = substr($SENSOR_LIST_STRING, 0, -1) . ']'; */
            
        }else{
            $SENSOR_LIST_STRING = '[]';
        }
        
        // TODO: security checks!
        $startdate = $_POST['start_date'];
        $enddate = $_POST['end_date'];
        $maxDevices = $_POST['max_devices_number'];
        $setupType = (isset($_POST['setup_types']) ? 1 : 0);
        $private = (isset($_POST['private']) ? 1 : 0);
        $startcriterion = NULL;
        $runningtime = NULL;
        $radioButton = $_POST['study_period'];
        
        if($radioButton == "1")
        {
            $startcriterion = 0;
            $runningtime = NULL;

            // user study should be finished if the end date is today or in the past days
            if($enddate != NULL && strtotime($enddate) <= strtotime(date("Y-m-d", mktime(0, 0, 0, 0, 0, 0000))))
            {
                $USTUDY_FINISHED = 1;
            }
        }
        elseif($radioButton == "2")
        {
            $startdate = NULL;
            $enddate = NULL;
            
            $startcriterion = $_POST['start_after_n_devices'];
            
            // converting to milliseconds
            switch($_POST['running_time_value']){        
                case 'h': $runningtime = intval($_POST['running_time'])*60*60*1000; // hours   
                        break;
                case 'd': $runningtime = intval($_POST['running_time'])*24*60*60*1000;  // days
                        break;
                case 'm': $runningtime = intval($_POST['running_time'])*30*24*60*60*1000;   // months
                        break;
                case 'y': $runningtime = intval($_POST['running_time'])*12*30*24*60*60*1000;    // years
                        break;
            }
        }
        
        /**
        * Parsing description of APKs
        */
        $APK_DESCRIPTION = '';
        
        if(isset($_POST['description'])){
            
            //Affecting the APK with examinating the space 
           $RAW_APK_DESCRIPTION = trim($_POST['description']);
           
           $APK_DESCRIPTION = $RAW_APK_DESCRIPTION;
            
        }
        
        /* APK/Study Title */
        $APK_TITLE = trim($_POST['apk_title']);
        
        /* Android version */
        $APK_ANDROID_VERSION = '';
        if(isset($_POST['android_version_select'])){
            $APK_ANDROID_VERSION = trim($_POST['android_version_select']);    
        }
        
        $sql_installed_on = "SELECT installed_on, apk_version
                             FROM ".$CONFIG['DB_TABLE']['APK']." 
                             WHERE apkid=". $apkId;
                             
        $logger->logInfo($sql_installed_on);                             
                             
        $result_installed_on = $db->query($sql_installed_on);
        $row_installed_on = $result_installed_on->fetch();
        
        $logger->logInfo("row_installed_on = ".$row_installed_on);

        /* incrementing study version*/
        $APK_VERSION = $row_installed_on['apk_version'] + 1;

          /**
          * Store filename, hash in DB and other informations
          */
          $sql = "UPDATE ". $CONFIG['DB_TABLE']['APK'] ." 
                  SET apktitle='". $APK_TITLE ."',
                      apkname='". (!$FILE_WAS_UPLOADED ? $oldAPKName : $filename)."', 
                      apk_version='".$APK_VERSION."',
                      apkhash='".(!$FILE_WAS_UPLOADED ? $oldAPKHash : $HASH_FILE) ."', 
                      sensors='". $SENSOR_LIST_STRING ."',
                      private=". $private .", 
                      description='". $APK_DESCRIPTION ."',".
                      (!empty($startcriterion) ? 'startcriterion='.$startcriterion : '')."
                      startdate='". $startdate ."',
                      enddate='". $enddate ."',
                      restriction_device_number=". $maxDevices .",
                      androidversion=". $APK_ANDROID_VERSION .",
                      inviteinstall=". $setupType .",
                      ustudy_finished=-1 
                  WHERE apkid=". $apkId;
                  
          $logger->logInfo($sql);
          
          // WARNING: hashed filename is WITHOUT .apk extention!                        
          $db->exec($sql);

        if(!empty($row_installed_on))
        {
          $row_installed_on =  $row_installed_on[0];
          $logger->logInfo("row_installed_on[0] = ".$row_installed_on);

          if(!empty($row_installed_on))
          {

            include_once(MOSES_HOME."/include/managers/GooglePushManager.php");
            $targetDevices = array();
            $row_installed_on = substr($row_installed_on, 1);
            $row_installed_on = substr($row_installed_on, 0 , strlen($row_installed_on)-1);
            $row_installed_on = explode(",", $row_installed_on);
            
              //Selecting all different apk in a hardware
            foreach($row_installed_on as $hardware_id){
                 $sql="SELECT * 
                       FROM ". $CONFIG['DB_TABLE']['HARDWARE'] ." 
                       WHERE hwid=".$hardware_id;
                       
                 $req=$db->query($sql);
                 $row=$req->fetch();
                 $targetDevices[] = $row['c2dm'];
            }
            GooglePushManager::googlePushSendUpdate($apkId, $targetDevices, $logger, $CONFIG);
          }
        }

        die('1');
        //header("Location: ucp.php?m=upload&res=1");
    }else{
        die('0');
        //header("Location: ucp.php?m=upload&res=0");
    }       
}
?>