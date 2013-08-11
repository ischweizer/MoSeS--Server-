<?php
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
    // that user can't access and modify the apk!
    die('-1');
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
            umask($oldumask);
            // folder failed to create
            die('0');
        }
        umask($oldumask); 
    }
       
    }else{
       // no hash for user found
       die('0');
    }

    /**
    * Checking for necessary conditions: file extension match
    */
    if(!in_array($fileExt, $allowedTypes))
      die('2');

    /**
    * Checking for necessary conditions: file size match
    */
    if(filesize($_FILES['file']['tmp_name']) > $maxFileSize)
      die('3');
    
    /**
    * Checking for necessary conditions: is that directory writable?
    */       
    if(!is_writable($uploadPath))
      die('4');
     
    chmod($_FILES['file']['tmp_name'], 0777);       

    $FILE_WAS_UPLOADED = TRUE;
    
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
        
        if(!chmod($uploadPath . $HASH_FILE . $fileExt, 0777)){
            /**
            * Checking for: can I change permission to file?
            */
            die('4');
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
    $USTUDY_FINISHED = 0;
    $startdate = $_POST['start_date'];
    $enddate = $_POST['end_date'];
    $maxDevices = $_POST['max_devices_number'];
    $setupType = (isset($_POST['setup_types']) ? 1 : 0);
    $private = (isset($_POST['private']) ? 1 : 0);
    $startcriterion = NULL;
    $runningtime = NULL;
    $radioButton = intval($_POST['study_period']);
    
    if($radioButton == 1){
        
        $startcriterion = 0;
        $runningtime = NULL;

        // user study should be finished if the end date is today or in the past days
        if($enddate != NULL && strtotime($enddate) <= strtotime(date("Y-m-d", mktime(0, 0, 0, 0, 0, 0000)))){
            $USTUDY_FINISHED = 1;
        }
    }
    
    if($radioButton == 2){
        
        $startdate = NULL;
        $enddate = NULL;
        
        $startcriterion = intval($_POST['start_after_n_devices']);
    
        // converting to milliseconds
        switch($_POST['running_time_value']){        
            case 'h': $runningtime = intval($_POST['running_time']); // hours (in hours)   
                    break;
            case 'd': $runningtime = intval($_POST['running_time'])*24;  // days (in hours)
                    break;
            case 'm': $runningtime = intval($_POST['running_time'])*30*24;   // months (in hours)
                    break;
            case 'y': $runningtime = intval($_POST['running_time'])*12*30*24;    // years (in hours)
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
                         
    //$logger->logInfo($sql_installed_on);                             
                         
    $result_installed_on = $db->query($sql_installed_on);
    $row_installed_on = $result_installed_on->fetch();
    
    //$logger->logInfo("row_installed_on = ".$row_installed_on);

    /* incrementing study version*/
    $APK_VERSION = $row_installed_on['apk_version'] + 1;
    
    /**
    * Update the given APK and study
    * WARNING: hashed filename is WITHOUT .apk extention!
    */
    $sql = "UPDATE ". $CONFIG['DB_TABLE']['APK'] ." 
          SET apktitle='". $APK_TITLE ."',
              apkname='". (!$FILE_WAS_UPLOADED ? $oldAPKName : $filename)."', 
              apk_version='".$APK_VERSION."',
              apkhash='".(!$FILE_WAS_UPLOADED ? $oldAPKHash : $HASH_FILE) ."',
              private=". $private .", 
              description='". $APK_DESCRIPTION ."',".
              (!empty($startcriterion) ? 'startcriterion='.$startcriterion .',' : '')."
              startdate=". ($startdate != NULL ? "'". $startdate ."'" : "NULL") .",
              enddate=". ($enddate != NULL ? "'". $enddate ."'" : "NULL") .",
              restriction_device_number=". $maxDevices .",
              androidversion=". $APK_ANDROID_VERSION .",".
              (!empty($runningtime) ? 'runningtime='. $runningtime .',' : '')."
              inviteinstall=". $setupType .",
              ustudy_finished=". $USTUDY_FINISHED ." 
          WHERE apkid=". $apkId;
     
    //$logger->logInfo($sql);

    $db->exec($sql);

    if(!empty($row_installed_on))
    {
      $row_installed_on =  $row_installed_on[0];
      $logger->logInfo("row_installed_on[0] = ".$row_installed_on);

      if(!empty($row_installed_on)){

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

    // success!
    die('1');
}else{
    // cannot move file to its destination
    die('0');
}       
?>