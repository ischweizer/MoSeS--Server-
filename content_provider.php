<?php
session_start();

// handling of jsong request for new table devices entries
                                                                 
if(isset($_REQUEST['pages']) && !empty($_REQUEST['pages']) && 
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

//If the formular is sent
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


?>