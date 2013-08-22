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
$sql = "SELECT apkname, apkhash, ustudy_finished 
        FROM ". $CONFIG['DB_TABLE']['APK'] ." 
        WHERE userid = ". $_SESSION["USER_ID"] ." AND apkid = ". $apkId;
       
$result = $db->query($sql);
$row = $result->fetch();    

if(empty($row)){
    // that user can't access and modify the apk!
    die('-1');
}

// user study was already finished -> send fail back
if($row['ustudy_finished'] != 0){
    die('0');
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
    $maxDevices = (intval($_POST['publishMethod']) == 2 ? $_POST['max_devices_number'] : -1);
    $inviteInstall = (intval($_POST['publishMethod']) == 2 ? 1 : 0);
    $private = (intval($_POST['publishMethod']) == 3 ? 1 : 0);
    $startcriterion = NULL;
    $runningtime = NULL;
    $radioButton = intval($_POST['study_period']);
    
    $SURVEY_FORMS_JSON = stripslashes(trim($_POST['survey_json']));
    // decode json to arrays instead of objects
    $SURVEY_FORMS_OBJ = json_decode($SURVEY_FORMS_JSON, true);
    
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
              startdate=". (!empty($startdate) ? "'". $startdate ."'" : "NULL") .",
              enddate=". (!empty($enddate) ? "'". $enddate ."'" : "NULL") .",
              restriction_device_number=". $maxDevices .",
              androidversion=". $APK_ANDROID_VERSION .",".
              (!empty($runningtime) ? 'runningtime='. $runningtime .',' : '')."
              inviteinstall=". $inviteInstall .",
              ustudy_finished=". $USTUDY_FINISHED ." 
          WHERE apkid=". $apkId;
     
    //$logger->logInfo($sql);

    $db->exec($sql);

    /* ****************************************************************
    *
    *               SURVEYS
    * 
    * ****************************************************************/
    
    /* if there are some selected survey forms */
    if(!empty($SURVEY_FORMS_OBJ)){
        
        /*
        *   DELETE ALL survey information and its results corresponding to selected APK and user id
        */
        
        // SELECT surveyid by apkid and userid
        $sql = "SELECT surveyid
                 FROM ".$CONFIG['DB_TABLE']['STUDY_SURVEY']." 
                 WHERE apkid=". $apkId ." AND userid = ". $_SESSION['USER_ID'];
                             
        $result = $db->query($sql);
        $row = $result->fetch();
        
        $SURVEY_ID = $row['surveyid'];
        
        // remove answers
        $survey_answers_sql = 'DELETE 
                             FROM '. $CONFIG['DB_TABLE']['STUDY_ANSWER'] .' 
                             WHERE questionid 
                             IN (SELECT questionid 
                                 FROM '. $CONFIG['DB_TABLE']['STUDY_QUESTION'] .' 
                                 WHERE formid 
                                 IN (SELECT formid 
                                     FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                                     WHERE surveyid 
                                     IN (SELECT surveyid 
                                         FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                                         WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'] .')))';
                                         
        $db->exec($survey_answers_sql);


        // remove questions                    
        $survey_questions_sql = 'DELETE 
                               FROM '. $CONFIG['DB_TABLE']['STUDY_QUESTION'] .' 
                               WHERE formid 
                               IN (SELECT formid 
                                   FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                                   WHERE surveyid 
                                   IN (SELECT surveyid 
                                       FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                                       WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'] .'))';
                                       
        $db->exec($survey_questions_sql);
                       
                               
        // remove forms
        $survey_forms_sql = 'DELETE 
                           FROM '. $CONFIG['DB_TABLE']['STUDY_FORM'] .' 
                           WHERE surveyid 
                           IN (SELECT surveyid 
                               FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                               WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'] .')';
                               
        $db->exec($survey_forms_sql);

                             
        // remove surveys            
        $survey_surveys_sql = 'DELETE 
                               FROM '. $CONFIG['DB_TABLE']['STUDY_SURVEY'] .' 
                               WHERE surveyid = '. $SURVEY_ID .' AND userid = '. $_SESSION['USER_ID'];
                             
        $db->exec($survey_surveys_sql);


        // remove survey results
        $survey_results_sql = 'DELETE 
                               FROM '. $CONFIG['DB_TABLE']['STUDY_RESULT'] .' 
                               WHERE survey_id = '. $SURVEY_ID;
                             
        $db->exec($survey_results_sql);
        
        /*
        * ************************************************************************
        */
        
        /* insert survey for user study */
        $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_SURVEY'] ." 
                                (userid, apkid)
                                VALUES 
                                (". $_SESSION["USER_ID"] .", ". $apkId .")";
        
        $db->exec($sql);       
        
        $survey_id = $db->lastInsertId();
        
        // for each supplied survey's form
        foreach($SURVEY_FORMS_OBJ as $survey_form){
        
            // determine form's title
            $survey_form_title = '';
            
            switch(intval($survey_form['survey_form_id'])){
                case 9001:  $survey_form_title = 'Custom form';
                            break;
                case 1:  $survey_form_title = getStandardSurveyNameById(1);
                         break;
                case 2:  $survey_form_title = getStandardSurveyNameById(2);
                         break;
                case 3:  $survey_form_title = getStandardSurveyNameById(3);
                         break;
                            
                default: die('6');  // wrong JSON                
            }
            
            // store form title in db
            $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_FORM'] ." 
                                    (surveyid, title)
                                    VALUES 
                                    (". $survey_id .", '". $survey_form_title ."')";
            
            $db->exec($sql);
            
            $form_id = $db->lastInsertId();
            
            // loop through all questions in the form
            foreach($survey_form['survey_form_questions'] as $question){

                $question_type = intval($question['question_type']);
                $question_text = $question['question'];
                
                // store question in db
                $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                                        (formid, type, text)
                                        VALUES 
                                        (". $form_id .", ". $question_type .", '". $question_text ."')";
                
                $db->exec($sql);
                
                // save answers only for types 1,3,4,5, because 2 is text type
                if($question_type != 2){
                
                    $question_id = $db->lastInsertId();
                    
                    // store answers in db
                    $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                                            (questionid, text) 
                                            VALUES ";
                                            
                    // loop through all answers in the question and append values                                                        
                    foreach($question['answers'] as $answer){ 
                      $sql .=" (". $question_id .", '". $answer ."') ,";
                    }
                    // remove last ',' from sql string
                    $sql = substr($sql, 0, -1);
                    
                    
                    
                    $db->exec($sql);
                }else{
                    // special case for text answers
                    $question_id = $db->lastInsertId();
                    
                    // store answers in db
                    $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                                            (questionid, text) 
                                            VALUES (". $question_id .", '')";
                    
                    $db->exec($sql);
                }
            }
        } 
    }
    
    /* **************************** */
    
    
    /*
    * PREPAIRING FOR GOOGLE PUSH 
    */
    if(!empty($row_installed_on)){
        
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