<?php
  
/**
* Checks if string is a valid md5 hash
* 
* @param string $md5
* @return boolean
*/
function is_md5($md5){
return (bool)preg_match("/[0-9a-f]{32}/i", $md5);
}

/**
* Checks for empty dir
* 
* @param string $dir
*/
function is_empty_dir($dir){
    $files = array();
    if($handle = opendir($dir)){
        while(false !== ($file = readdir($handle))){
            if($file != "." && $file != ".."){
                $files[] = $file;
            }
        }
        closedir($handle);
    }
    return (count($files) > 0) ? FALSE : TRUE;
}

/**
* Consumes an ordinal of the sensor and returns its name
* 
* @param mixed $sensor_ordinal
*/
function get_sensor_name($sensor_ordinal){
    $result = '';
    
    switch($sensor_ordinal){
        case 1 : $result = "Accelerometer sensor"; break;
        case 2 : $result = "Magnetic field sensor"; break;
        case 3 : $result = "Orientation sensor"; break;
        case 4 : $result = "Gyroscope"; break;
        case 5 : $result = "Light sensor"; break;
        case 6 : $result = "Preassure sensor"; break;
        case 7 : $result = "Temperature sensor"; break;
        case 8 : $result = "Proximity sensor"; break;
        case 9 : $result = "Gravity sensor"; break;
        case 10 : $result = "Linear acceleration sensor"; break;
        case 11 : $result = "Rotation sensor"; break;
        case 12 : $result = "Humidity sensor"; break;
        case 13 : $result = "Ambient temperature sensor"; break;
        default : $result = "Unknown sensor"; break;
    }
    
    return $result;
}

/**
* Compares two arrays to match content
* 
* @param mixed $filter_array
* @param mixed $apk_sensors_array
*/
function isFilterMatch($filter_array, $apk_sensors_array){
      $all_in = true;
  
      foreach($apk_sensors_array as $req){
         $all_in = $all_in && in_array($req, $filter_array);
      }
      
      return $all_in;
}

/**
*  Returns array of APIs
*/
function getAPIArray(){
    
    $API_LEVELS = array(1 => 'Android 1.0 (API: 1)',
                        2 => 'Android 1.1 (API: 2)',
                        3 => '"Cupcake" 1.5 (API: 3)',
                        4 => '"Donut" 1.6 (API: 4)',
                        5 => '"Eclair" 2.0 (API: 5)',
                        6 => '"Eclair" 2.0.1 (API: 6)',
                        7 => '"Eclair" 2.1 (API: 7)',
                        8 => '"Froyo" 2.2.x (API: 8)',
                        9 => '"Gingerbread" 2.3.0 - 2.3.2 (API: 9)',
                        10 => '"Gingerbread" 2.3.3 - 2.3.7 (API: 10)',
                        11 => '"Honeycomb" 3.0 (API: 11)',
                        12 => '"Honeycomb" 3.1 (API: 12)',
                        13 => '"Honeycomb" 3.2.x (API: 13)',
                        14 => '"Ice Cream Sandwich" 4.0.0 - 4.0.2 (API: 14)',
                        15 => '"Ice Cream Sandwich" 4.0.3 - 4.0.4 (API: 15)',
                        16 => '"Jelly Bean" 4.1.x (API: 16)',
                        17 => '"Jelly Bean" 4.2.x (API: 17)',
                        18 => '"Jelly Bean" 4.3 (API: 18)');
                        
    return $API_LEVELS;
}

/**
* Returns name for suplied API level integer
*/
function getAPILevel($level){
    $levels = getAPIArray();
    return $levels[$level];
}

/**
*   Returns count of all APIs
*/
function getAllAPIsCount(){                      
    return count(getAPIArray());
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
* Returns array of standard surves
* * !!! DEFINE STANDARD SURVEY HERE !!! 
* 
*/
function getStandardSurveysArray(){
    
    $SURVEYS_STD_RESULT = array();
    
    /* System Usability Scale (SUS) */
               
    $SYSTEM_USABILITY_SCALE_Q = array();                            
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I think that I would like to use this system frequently', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );                             
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I found the system unnecessarily complex', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I thought the system was easy to use', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );                                
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I think that I would need the support of a technical person to be able to use this system', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );                             
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I found the various functions in this system were well integrated', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I thought there was too much inconsistency in this system', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I would imagine that most people would learn to use this system very quickly', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );                             
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I found the system very cumbersome to use', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I felt very confident using the system', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );                             
    $SYSTEM_USABILITY_SCALE_Q[] = array('question_type' => 3, 
                                'question'=> 'I needed to learn a lot of things before I could get going with this system', 
                                'question_number_of_answers' => 0,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );
                                
    $SYSTEM_USABILITY_SCALE = array('survey_form_id' => 1,
                                   'survey_form_name' => 'System Usability Scale', 
                                   'content' => $SYSTEM_USABILITY_SCALE_Q);
                                   
    // first element
    $SURVEYS_STD_RESULT[] = $SYSTEM_USABILITY_SCALE;
    
    /* Standard 1 */

    $STANDARD_SURVEY_1_Q = array();
    $STANDARD_SURVEY_1_Q[] = array('question_type' => 1, 
                                'question'=> 'Standard1- question1', 
                                'question_number_of_answers' => 3,
                                'answers' => array('Yes', 'No', 'Not sure')
                                );
                                
    $STANDARD_SURVEY_1_Q[] = array('question_type' => 2, 
                                'question'=> 'Standard1- question2', 
                                'question_number_of_answers' => 0,
                                'answers' => array()
                                );
    $STANDARD_SURVEY_1_Q[] = array('question_type' => 3, 
                                'question'=> 'Standard1- question3', 
                                'question_number_of_answers' => 5,
                                'answers' => array('Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree')
                                );

    $STANDARD_SURVEY_1 = array('survey_form_id' => 2,
                              'survey_form_name' => 'Standard form 1', 
                              'content' => $STANDARD_SURVEY_1_Q);
                              
    // second element
    $SURVEYS_STD_RESULT[] = $STANDARD_SURVEY_1;
    
    /* Standard 2 */
               
    $STANDARD_SURVEY_2_Q = array();                           
    $STANDARD_SURVEY_2_Q[] = array('question_type' => 1, 
                                'question'=> 'Standard2- question1', 
                                'question_number_of_answers' => 3,
                                'answers' => array('Yes', 'No', 'Not sure')
                                );
                                
    $STANDARD_SURVEY_2_Q[] = array('question_type' => 2, 
                                'question'=> 'Standard2- question2', 
                                'question_number_of_answers' => 0,
                                'answers' => array()
                                );
    $STANDARD_SURVEY_2_Q[] = array('question_type' => 1, 
                                'question'=> 'Standard2- question3', 
                                'question_number_of_answers' => 3,
                                'answers' => array('Yes', 'No', 'Not sure')
                                );
                                
    $STANDARD_SURVEY_2 = array('survey_form_id' => 3,
                              'survey_form_name' => 'Standard form 2',
                              'content' => $STANDARD_SURVEY_2_Q);
                              
    // third element
    $SURVEYS_STD_RESULT[] = $STANDARD_SURVEY_2;
    
    
    return $SURVEYS_STD_RESULT;
}

/*
*   Returns standard form by name
*/
function getStandardFormByName($name){
    $forms = getStandardSurveysArray();
    
    foreach($forms as $f){
        if($f['survey_form_name'] == $name){
            return $f;
        }       
    }
    return NULL;
}

/** 
* Returns name of standard survey by id
* 
* @param mixed $survey_id
*/
function getStandardSurveyNameById($survey_form_id){
    
    $RESULT = '';
    
    switch($survey_form_id){
       
       case 1: $survey_array = getStandardSurveysArray();
               $RESULT = $survey_array[0]['survey_form_name'];
               break;
   
       case 2: $survey_array = getStandardSurveysArray();
               $RESULT = $survey_array[1]['survey_form_name'];
               break;
   
       case 3: $survey_array = getStandardSurveysArray();
               $RESULT = $survey_array[2]['survey_form_name'];
               break;
               
        default: $RESULT = array();
   }
   
   return $RESULT;
}

/* 
* Returns survey as JSON string back
*/
function getStandardSurveyById($survey_form_id){
    
   if(empty($survey_form_id))
    return '[]'; 
   
   $RESULT = array();
   
   switch($survey_form_id){
       
       case 1: $survey_array = getStandardSurveysArray();
               $RESULT = $survey_array[0];
               break;
   
       case 2: $survey_array = getStandardSurveysArray();
               $RESULT = $survey_array[1];
               break;
   
       case 3: $survey_array = getStandardSurveysArray();
               $RESULT = $survey_array[2];
               break;
               
        default: $RESULT = array();
   
   }
   return json_encode($RESULT);
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}
  
function survey2csv(array $array){
   
   print_r($array); 
    
   if(count($array) == 0)
    return null;
     
   ob_start();
   $out = fopen("php://output", 'w');
   
   fputcsv($out, array('There are '. count($array['forms']) .' forms'));
   
   foreach ($array as $f) {
        fputcsv($out, array($f['form_title']));
   }
   
   fclose($out);
   return ob_get_clean();
}  

?>