<?php
//Starting session
session_start();
//test if user is registered
if(!isset($_SESSION['USER_LOGGED_IN']))
    die('Only registered users may access that file!');

include_once("./config.php");
include_once(MOSES_HOME."/include/functions/func.php");
include_once(MOSES_HOME."/include/functions/logger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");
    
/**
*  SETTING FILE FOR UPLOAD
*/
$allowedTypes = array('.apk');
$maxFileSize = $CONFIG['UPLOAD']['FILESIZE'];//stting the maximale size of file
$uploadPath = './apk/'; // folder to save to

$filename = $_FILES['userfile']['name']; // gets filename
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
            header("Location: ucp.php?m=upload&res=0");
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
 
if(filesize($_FILES['userfile']['tmp_name']) > $maxFileSize)
    die('3');
  //header("Location: ucp.php?m=upload&res=3");
       
if(!is_writable($uploadPath))
    die('4');
  //header("Location: ucp.php?m=upload&res=4");
 
chmod($_FILES['userfile']['tmp_name'], 0777);       

/**
* Moving file into its directory and storing that data in DB
*/
if(is_uploaded_file($_FILES['userfile']['tmp_name']) 
    && move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadPath . $HASH_FILE . $fileExt))
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
    
    // Initilization the contents of the pages for US creation form
    $sql = "SELECT * FROM temp WHERE userid = ". $_SESSION["USER_ID"];
    $req = $db->query($sql);
    $row = $req->fetch();
	//Affectation of the different
    if(!empty($row))
    {
        $apk_title = $row['apk_title'];
        $description = $row['description'];
        $radioButton = $row['radioButton'];
        $startdate = $row['startdate'];
        $startcriterion = $row['startcriterion'];
        $enddate = $row['enddate'];
        $runningtime = $row['runningtime'];
        $maxdevice = $row['maxdevice'];
        $locked = $row['locked'];
        $inviteinstall = $row['inviteinstall'];
        $androidversion = $row['androidversion'];
        $sensors = $row['sensors'];
    }


    $RESTRICTION_USER_NUMBER = $maxdevice;
    include_once(MOSES_HOME."/include/managers/HardwareManager.php");
    // get the list of candidates with the specified android version
    // Check if the user wants only members from his group to take part on the user study
    if($locked == "1")
    {
        $rows = HardwareManager::getCandidatesForAndroidFromGroup($db, $CONFIG['DB_TABLE']['HARDWARE'], $CONFIG['DB_TABLE']['RGROUP'],
                                                            $androidversion, $_SESSION['RGROUP'],$logger);
    }
    else
    {
        $rows =  HardwareManager::getCandidatesForAndroid($db, $CONFIG['DB_TABLE']['HARDWARE'], $androidversion);    
    }
    // check the filters
    if(!empty($rows))
    {
        foreach($rows as $hardware)
        {
            $hwFilter_array = json_decode($hardware['filter']);
            $apkSensors_array = json_decode($SENSOR_LIST_STRING);
            if(isFilterMatch($hwFilter_array, $apkSensors_array))
            {
                $candidates[] = intval($hardware['hwid']);
            }
        }
        shuffle($candidates);
    }

    // WRITE APK TO DATABASE AND START USER STUDY IF NEEDED
   
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
    }
        
	
    $logger->logInfo("title = ".$apk_title);
    $logger->logInfo("description = ".$description);
    $logger->logInfo("radioButton = ".$radioButton);
    $logger->logInfo("startdate = ".$startdate);
    $logger->logInfo("enddate = ".$enddate);
    $logger->logInfo("startcriterion = ".$startcriterion);
    $logger->logInfo("runningtime = ".$runningtime);
    $logger->logInfo("maxdevice = ".$maxdevice);
    $logger->logInfo("USTUDY_FINISHED = ".$USTUDY_FINISHED);
    $logger->logInfo("locked = ".$locked);
    $logger->logInfo("inviteinstall = ".$inviteinstall);
    $logger->logInfo("androidversion = ".$androidversion);
    $logger->logInfo("sensors = ".$sensors);

    /**
    * Store filename, hash in DB and other informations
	* inserting into APK table
    */
    $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['APK'] ." (userid, userhash, apkname, apk_version,
                             apkhash, sensors, description,
                             apktitle, restriction_device_number, pending_devices,
                             candidates, notified_devices, androidversion, ustudy_finished, locked,
                             startdate, startcriterion, enddate, runningtime, maxdevice, inviteinstall
                             )
                              VALUES 
                              (". $_SESSION["USER_ID"]
                                .", '". $HASH_DIR ."'"
                                .", '". $filename ."'"
                                .", 1"
                                .", '" . $HASH_FILE ."'"
                                .", '". $sensors ."'"
                                .", '". $description ."'"
                                .", '". $apk_title ."'"
                                .", ". $RESTRICTION_USER_NUMBER
                                .", '". $pending_users ."'"
                                .", '". $candidates ."'"
                                .", '". $notified_users ."'"
                                .", '". $androidversion ."'"
                                .", ". $USTUDY_FINISHED
                                .", ". $locked
                                .", '". $startdate."'"
                                .", ". $startcriterion
                                .", '". $enddate."'"
                                .", '". $runningtime."'"
                                .", ". $maxdevice
                                .", '". $inviteinstall."' )";
    $logger->logInfo("sql = ".$sql);

    // WARNING: hashed filename is WITHOUT .apk extention!
    $db->exec($sql) or die('Error SQL !<br/>'.$sql);

    $sql = "DELETE FROM temp WHERE userid = ". $_SESSION["USER_ID"];
    $db->exec($sql);
	$row = $req->fetch(); // TODO : remove this line
    
    header("Location: ucp.php?m=upload&res=1");
}
else
{
    header("Location: ucp.php?m=upload&res=0");
}

?>