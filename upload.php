<?php
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    die('Only registered users may access that file!');

//print_r($_POST);

include_once("./config.php");
    
/**
*  SETTINGS FOR UPLOAD
*/
$allowedTypes = array('.apk');
$maxFileSize = 3145728; // 3MB
$uploadPath = './apk/'; // folder to save to

$filename = $_FILES['userfile']['name']; // gets filename
$fileExt = substr($filename, strripos($filename, '.'), strlen($filename)-1);
    
/**
* Connect to DB and get hashes for folder and file
*/
include_once(MOSES_HOME. "/include/functions/dbconnect.php");

$sql = "SELECT hash FROM user WHERE userid = ". $_SESSION["USER_ID"];
       
$result = $db->query($sql);

$row = $result->fetch();

if(!empty($row)){
  
$HASH_DIR = $row['hash'];   
$HASH_FILE = md5(time() . $filename);

$uploadPath .= $HASH_DIR . "/";

// check if directory exists
clearstatcache();

if(!is_dir($uploadPath)){
    if(!mkdir($uploadPath, 0775, true)){
        // folder failed to create
        header("Location: ucp.php?m=upload&res=0");     
    }    
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
 
chmod($_FILES['userfile']['tmp_name'], 0775);       

/**
* Moving file into its directory and storing that data in DB
*/
if(is_uploaded_file($_FILES['userfile']['tmp_name']) 
    && move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadPath . $HASH_FILE . $fileExt)){
    
    // fix file permission
    if(!chmod($uploadPath . $HASH_FILE . $fileExt, 0775)){
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
    
    $RESTRICTION_USER_NUMBER = -1;
    $SELECTED_USERS_LIST = '';
    
    if(isset($_POST['restrict_users_number']) && isset($_POST['number_restricted_users'])){
        
        $RESTRICTION_CHECHED = (preg_replace('/[^0-9]/', '', $_POST['restrict_users_number']) == '1') ? true : false;
        
        if($RESTRICTION_CHECHED){
            $RESTRICTION_USER_NUMBER = preg_replace('/[^0-9]/', '', $_POST['number_restricted_users']);
            
            $sql = "SELECT userid
                    FROM user
                    WHERE userid != 1";
                    
            $result = $db->query($sql);
            $row = $result->fetchAll(PDO::FETCH_COLUMN);
            
            if(!empty($row)){
                
                $user_count_to_generate = 1; // default value
                $users_array = array();
                
                if(count($row) < $RESTRICTION_USER_NUMBER){
                    $user_count_to_generate = count($row);    
                }else{
                    $user_count_to_generate = $RESTRICTION_USER_NUMBER;
                }
                
                foreach($row as $id){
                    $users_array[] = $id;
                }
                
                shuffle($users_array);
                $random_indexes = array_rand($users_array, $user_count_to_generate);
                
                // deep shit tho
                for($i=0; $i < count($random_indexes); $i++){
                   $random_users_array[] = $users_array[$random_indexes[$i]];
                }
                
                $SELECTED_USERS_LIST = implode(',', $random_users_array);
            }
        }
    }
    
    
    /**
    * Store filename and hash in DB
    */
    $sql = "INSERT INTO apk (userid, userhash, apkname, 
                             apkhash, sensors, description,
                             apktitle, restriction_user_number, selected_users_list,
                             androidversion)
                              VALUES 
                              (". $_SESSION["USER_ID"] .", '". $HASH_DIR ."', '". $filename ."', 
                              '". $HASH_FILE ."', '". $SENSOR_LIST_STRING ."', '". $APK_DESCRIPTION ."',
                              '". $APK_TITLE ."', ". $RESTRICTION_USER_NUMBER .", '". $SELECTED_USERS_LIST ."',
                              '". $APK_ANDROID_VERSION ."')"; 
    // WARNING: hashed filename is WITHOUT .apk extention!
                             
    $db->exec($sql);
    
    $LAST_INSERTED_ID = $db->lastInsertId();
    
    include_once(MOSES_HOME ."/include/managers/GooglePushManager.php");
                      
    
    // ##### TEMP ##############################
    
    $sql = "SELECT c2dm FROM hardware";
    
    $result = $db->query($sql);
    $targetDevices = $result->fetchAll(PDO::FETCH_ASSOC);
    
    
    //#########################################
                      
    
    
    GooglePushManager::googlePushSend($LAST_INSERTED_ID, $targetDevices);

    header("Location: ucp.php?m=upload&res=1");
    //echo 'Your file "'. $filename .'" was successfully uploaded.';
}else{
    header("Location: ucp.php?m=upload&res=0");
    //echo 'Some error occured while uploading a file. Please try again later.';
}
  

?>