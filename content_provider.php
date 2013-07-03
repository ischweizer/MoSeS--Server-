<?php
session_start();

include_once("./include/functions/func.php");

/*
* Handle allow access for scientists in admin panel
*/
if(isset($_SESSION['USER_LOGGED_IN']) && isset($_REQUEST['hash']) && !empty($_REQUEST['hash']) && is_md5($_REQUEST['hash'])){
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
* Handling user creates or joins existing group 
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
* Handling user leaves his group 
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
* Handle user login
*/
if(isset($_POST["submit"]) && $_POST["submit"] == "1"){
	
    include_once("./config.php");
	//If the login exists
	if(isset($_POST["login"]) && !empty($_POST["login"]) && preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $_POST["login"])){
		//And the password exists
		if(isset($_POST["password"]) && !empty($_POST["password"])){
			//Import of the connection�s file to database
			include_once("./include/functions/dbconnect.php");

			$USER_LOGIN =  $_POST["login"];
			$USER_PASSWORD =  $_POST["password"];

			//Select the user
			$sql = "SELECT *
                  FROM ". $CONFIG['DB_TABLE']['USER'] ."
                  WHERE login = '". $USER_LOGIN ."'
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
					$_SESSION["LOGIN"] =     $row["login"];
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

?>