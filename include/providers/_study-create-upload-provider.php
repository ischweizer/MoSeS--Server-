<?php
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
* Selecting THE NAME OF FILE FROM THE DATABASE
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
        if(!mkdir($uploadPath, 0777, true)){
            umask($oldumask);
            // failed to create folder
            die('0');
        }
        umask($oldumask); 
    }
   
}
else{
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

/**
* Moving file into its directory and storing that data in DB
*/
if(is_uploaded_file($_FILES['file']['tmp_name']) 
    && move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . $HASH_FILE . $fileExt)){
    
    /**
    * Checking for: can I change permission to file?
    */
    if(!chmod($uploadPath . $HASH_FILE . $fileExt, 0777)){
        die('4');
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
    $private = (intval($_POST['publishMethod']) == 3 ? 1 : 0);
    $startdate = $_POST['start_date'];
    $enddate = $_POST['end_date'];        
    $maxdevice = (intval($_POST['publishMethod']) == 2 ? $_POST['max_devices_number'] : -1);
    $inviteinstall = (intval($_POST['publishMethod']) == 2 ? 1 : 0);
    
    $SURVEY_FORMS_JSON = stripslashes(trim($_POST['survey_json']));
    // decode json to arrays instead of objects
    $SURVEY_FORMS_OBJ = json_decode($SURVEY_FORMS_JSON, true);
    
    if($SURVEY_FORMS_OBJ === NULL){
        // bad format
        die('5');
    }

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
    // check the sensors
    if(!empty($rows)){
        // TODO: what we do with sensors/filters?
       /* foreach($rows as $hardware){
            $hwFilter_array = json_decode($hardware['sensors']);
            $apkSensors_array = json_decode($SENSOR_LIST_STRING);
            if(isFilterMatch($hwFilter_array, $apkSensors_array))
            {
                $candidates[] = intval($hardware['hwid']);
            }
        }
        shuffle($candidates);
        */
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
    
    if($radioButton == "1"){
        
        $startcriterion = 0;
        $runningtime = NULL;

        // user study should be finished if the end date is today or in the past days
        if($enddate != NULL && strtotime($enddate) <= strtotime(date("Y-m-d", mktime(0, 0, 0, 0, 0, 0000)))){
            $USTUDY_FINISHED = 1;
        }
        
    }
    
    if($radioButton == "2"){
        
        $startdate = NULL;
        $enddate = NULL;
        
        $startcriterion = $_POST['start_after_n_devices'];
        
        // converting time to milliseconds
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
    * WARNING: hashed filename is WITHOUT .apk extention!
    */
    $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['APK'] ." 
                            (userid, userhash, apkname,
                             apkhash, description, private,
                             apktitle, restriction_device_number, pending_devices,
                             candidates, notified_devices, androidversion, ustudy_finished,
                             startdate, startcriterion, enddate, runningtime, inviteinstall
                             )
                              VALUES 
                              (". $_SESSION["USER_ID"]
                                .", '". $HASH_DIR ."'"
                                .", '". $filename ."'"
                                .", '" . $HASH_FILE ."'"
                                .", '". $description ."'"
                                .", ". $private
                                .", '". $apk_title ."'"
                                .", ". $RESTRICTION_USER_NUMBER
                                .", '". $pending_users ."'"
                                .", '". $candidates ."'"
                                .", '". $notified_users ."'"
                                .", '". $androidversion ."'"
                                .", ". $USTUDY_FINISHED
                                .", ". (!empty($startdate) ? "'". $startdate ."'" : "NULL")
                                .", ". $startcriterion
                                .", ". (!empty($enddate) ? "'". $enddate ."'" : 'NULL')
                                .", '". $runningtime."'"
                                .", '". $inviteinstall."' )";
                                
    //$logger->logInfo("Upload/insert APK sql: ". $sql);
    
    $db->exec($sql);

    /* ****************************************************************
    *
    *               SURVEYS
    * 
    * ****************************************************************/
    
    /* if there are some selected surveys */
    if(!empty($SURVEY_FORMS_OBJ)){
        
        $apk_id = $db->lastInsertId();
        
        /* insert survey for user study */
        $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_SURVEY'] ." 
                                (userid, apkid)
                                VALUES 
                                (". $_SESSION["USER_ID"] .", ". $apk_id .")";
        
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
            
            switch(intval($survey_form['survey_form_id'])){
                
                case 9001:  // loop through all questions in the form
                            foreach($survey_form['survey_form_questions'] as $question){

                                $question_type = intval($question['question_type']);
                                $question_text = $question['question'];
                                
                                // store question in db
                                $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                                                        (formid, type, text)
                                                        VALUES 
                                                        (". $form_id .", ". $question_type .", '". $question_text ."')";
                                
                                $db->exec($sql);
                                
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
                            }
                
                            break;
                case 1:  
                         $survey_array = getStandardSurveysArray();
                         // SUS
                         $survey_form = $survey_array[0];
                         $survey_form_questions = $survey_form['content'];
                         
                         foreach($survey_form_questions as $question){
                             
                            $question_type = $question['question_type'];
                            $question_text = $question['question'];
                             
                            // store question in db
                            $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                                                    (formid, type, text)
                                                    VALUES 
                                                    (". $form_id .", ". $question_type .", '". $question_text ."')";
                            
                            $db->exec($sql);
                            
                            $question_id = $db->lastInsertId();
                            
                            $answers = $question['answers'];
                            
                            // store answers in db
                            $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                                                    (questionid, text) 
                                                    VALUES ";
                            
                            foreach($answers as $answer){
                                // append answer values 
                                $sql .=" (". $question_id .", '". $answer ."'),";       
                            }
                            
                            // remove last ',' from sql string
                            $sql = substr($sql, 0, -1);
                            
                            $db->exec($sql);
                         } 
                
                         break;
                case 2:  
                         $survey_array = getStandardSurveysArray();
                         // Standard 1
                         $survey_form = $survey_array[1];
                         $survey_form_questions = $survey_form['content'];
                         
                         foreach($survey_form_questions as $question){
                             
                            $question_type = $question['question_type'];
                            $question_text = $question['question'];
                             
                            // store question in db
                            $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                                                    (formid, type, text)
                                                    VALUES 
                                                    (". $form_id .", ". $question_type .", '". $question_text ."')";
                            
                            $db->exec($sql);
                            
                            // no store of answers
                         }              
                
                         break;
                case 3:  
                         $survey_array = getStandardSurveysArray();
                         // Standard 2
                         $survey_form = $survey_array[2];
                         $survey_form_questions = $survey_form['content'];
                         
                         foreach($survey_form_questions as $question){
                             
                            $question_type = $question['question_type'];
                            $question_text = $question['question'];
                             
                            // store question in db
                            $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                                                    (formid, type, text)
                                                    VALUES 
                                                    (". $form_id .", ". $question_type .", '". $question_text ."')";
                            
                            $db->exec($sql);
                            
                            $question_id = $db->lastInsertId();
                            
                            $answers = $question['answers'];
                            
                            // store answers in db
                            $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                                                    (questionid, text) 
                                                    VALUES ";
                            
                            foreach($answers as $answer){
                                // append answer values 
                                $sql .=" (". $question_id .", '". $answer ."'),";       
                            }
                            
                            // remove last ',' from sql string
                            $sql = substr($sql, 0, -1);
                            
                            $db->exec($sql);
                         }
                
                         break;
                            
                default: die('6');  // wrong JSON                
            }            
        } 
    }
    
    /* **************************** */
    
    // success!
    die('1');
}else{
    // cannot move file to its destination
    die('0');
}
?>