<?php
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    die('Only registered users may access that file!');

//print_r($_POST);

include_once("./config.php");
include_once(MOSES_HOME."/include/functions/func.php");
include_once(MOSES_HOME."/include/functions/logger.php");
    
/**
*  SETTINGS FOR UPLOAD
*/
$allowedTypes = array('.apk');
$maxFileSize = $CONFIG['UPLOAD']['FILESIZE']; // 20MB
$uploadPath = './apk/'; // folder to save to

$filename = $_FILES['userfile']['name']; // gets filename
$fileExt = substr($filename, strripos($filename, '.'), strlen($filename)-1);
    
/**
* Connect to DB and get hashes for folder and file
*/
include_once(MOSES_HOME. "/include/functions/dbconnect.php");

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
        header("Location: ucp.php?m=upload&res=0");
    }
    umask($oldumask); 
}
   
}else{
   // no hash for user found
   header("Location: ucp.php?m=upload&res=0");
}

/**
* Checking for necessary conditions
*/
if(!in_array($fileExt, $allowedTypes))
  header("Location: ucp.php?m=upload&res=2");
  //die('That filetype not allowed. Sorry.');
  //print_r($_FILES);
 //print_r(fileperms($_FILES['userfile']['tmp_name'])); 
if(filesize($_FILES['userfile']['tmp_name']) > $maxFileSize)
  header("Location: ucp.php?m=upload&res=3");
  //die('This file is too large. Sorry.');
       
if(!is_writable($uploadPath))
  header("Location: ucp.php?m=upload&res=4");
  //die("You don't have permission to upload.");
 
chmod($_FILES['userfile']['tmp_name'], 0777);       

/**
* Moving file into its directory and storing that data in DB
*/
if(is_uploaded_file($_FILES['userfile']['tmp_name']) 
    && move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadPath . $HASH_FILE . $fileExt)){
    
    // fix file permission
    if(!chmod($uploadPath . $HASH_FILE . $fileExt, 0777)){
       header("Location: ucp.php?m=upload&res=4"); 
    }
     
    /**
    * Building sensors string in JSON-Array-Format
    */
    if(isset($_POST['sensors']) && is_array($_POST['sensors']) && count($_POST['sensors']) > 0){
        
        $RAW_SENSOR_LIST = $_POST['sensors'];
        $SENSOR_LIST_STRING = '[';
        
        foreach($RAW_SENSOR_LIST as $sensor){
          $SENSOR_LIST_STRING .= $sensor .','; 
        }
        
        $SENSOR_LIST_STRING = substr($SENSOR_LIST_STRING, 0, -1) . ']';
        
    }else{
        $SENSOR_LIST_STRING = '[]';
    }
    
    /**
    * Parsing description of APKs
    */
    $APK_DESCRIPTION = '';
    
    if(isset($_POST['apk_description'])){
        
       $RAW_APK_DESCRIPTION = trim($_POST['apk_description']);
       
       // TODO: make some SQL-Injection security here
       
       $APK_DESCRIPTION = $RAW_APK_DESCRIPTION;
        
    }
    
    $APK_TITLE = trim($_POST['apk_title']);
    
    // TODO: add some security here
    $APK_ANDROID_VERSION = '';
    if(isset($_POST['apk_android_version'])){
        $APK_ANDROID_VERSION = trim($_POST['apk_android_version']);    
    }
    
    $APK_VERSION = '';
    if(isset($_POST['apk_version'])){
        $APK_VERSION = trim($_POST['apk_version']);    
    }
    
    $SELECTED_USERS_LIST = '';
    
    // PREPARING VARIABLES FOR INSERTION TO DB
    $pending_users = array();
    $notified_users = array();
    
    $sql_installed_on = "SELECT installed_on FROM ".$CONFIG['DB_TABLE']['APK']." WHERE apkid=".$_SESSION['APKID'];
    $result_installed_on = $db->query($sql_installed_on);
    $row_installed_on = $result_installed_on->fetch();
    
    $candidates = $row_installed_on['installed_on'];
    $RESTRICTION_USER_NUMBER = count(json_decode($candidates))*10;
    $pending_users = json_encode($pending_users);
    $notified_users = json_encode($notified_users);
    
    /**
    * Store filename, hash in DB and other informations
    */
    $sql = "UPDATE ". $CONFIG['DB_TABLE']['APK'] ." SET apkname='". $filename."', apk_version='".$APK_VERSION."',
                             apkhash='".$HASH_FILE ."', sensors='". $SENSOR_LIST_STRING ."', description='". $APK_DESCRIPTION ."',
                             restriction_device_number=".$RESTRICTION_USER_NUMBER.",
                             pending_devices='". $pending_users ."', candidates='". $candidates ."', 
                             notified_devices='". $notified_users ."', androidversion=". $APK_ANDROID_VERSION .", 
                             ustudy_finished=-1, locked=0 WHERE apkid=".$_SESSION['APKID'];
    
    $logger->logInfo("SQL ON UPDATE");
    $logger->logInfo($sql);
                              
    // WARNING: hashed filename is WITHOUT .apk extention!
                             
    $db->exec($sql);
   

    header("Location: ucp.php?m=upload&res=1");
    //echo 'Your file "'. $filename .'" was successfully uploaded.';
}else{
    header("Location: ucp.php?m=upload&res=0");
    //echo 'Some error occured while uploading a file. Please try again later.';
}

?>