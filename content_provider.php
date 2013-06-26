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
	
	// If the email is unique, 0 is returned
	// if the email is already contained in the database (someone used it already) 1 is returned
	
	include_once("./config.php");
	include_once("./include/functions/dbconnect.php");
	
	// search the database for users who are registered with the email
	// if found, check if they have confirmed the registration via email
	// if yes, the email is not unique
	// this means, user can use the same email if (for some reason) he has 
	// not confirmed his previous registration with the same email
	$sql = "SELECT confirmed
           FROM ".$CONFIG["DB_TABLE"]["USER"]." WHERE email='".$_POST["isEmailUnique"]."' AND confirmed=1";
	 
	$result = $db->query($sql);
	$emails = $result->fetchAll(PDO::FETCH_ASSOC);
	
	if(empty($emails)){
		echo 0; // no users with such confirmed email found, the email is thus unique
	}
	else
		echo 1; // a user has already confirmed this email, the email is thus NOT unique
}

?>