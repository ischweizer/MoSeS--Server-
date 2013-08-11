<?php

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] <= 1){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    exit;
}

if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) &&
   isset($_GET['m']) && !empty($_GET['m'])){

    include_once("./include/functions/func.php");
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php");

    $id = $_GET['id'];
    $mode = $_GET['m'];
    
    if($mode === 'csv'){
        
        $sql = "SELECT * 
               FROM ". $CONFIG['DB_TABLE']['STUDY_RESULT'] ." 
               WHERE survey_id = ". $id;
                
        $result = $db->query($sql);
        $survey_results = $result->fetchAll(PDO::FETCH_ASSOC);

        // if there are some results
        if(!empty($survey_results)){
            $RESULTS_ARRAY = array();
            
            $sql = "SELECT userid, apkid 
                    FROM ". $CONFIG['DB_TABLE']['STUDY_SURVEY'] ." 
                    WHERE surveyid = ". $id;
                    
           $result = $db->query($sql);
           $survey = $result->fetch(PDO::FETCH_ASSOC);
            
            // selecting APK title
            $sql = "SELECT apktitle 
                    FROM ". $CONFIG['DB_TABLE']['APK'] ." 
                    WHERE apkid = ". $survey['apkid'] ." AND userid = ". $survey['userid'];
                    
           $result = $db->query($sql);
           $apk_title_r = $result->fetch(PDO::FETCH_ASSOC);
           $apk_title = $apk_title_r['apktitle'];
           
           $RESULTS_ARRAY['apk_title'] = $apk_title;
           
           $sql = "SELECT title, formid 
                   FROM ". $CONFIG['DB_TABLE']['STUDY_FORM'] ." 
                   WHERE surveyid = ". $id;
                    
           $result = $db->query($sql);
           $forms = $result->fetchAll(PDO::FETCH_ASSOC);
           
           $forms_array = array();
           foreach($forms as $f){
               
              $sql = "SELECT questionid, type, text  
                      FROM ". $CONFIG['DB_TABLE']['STUDY_QUESTION'] ." 
                      WHERE formid = ". $f['formid'];
                        
              $result = $db->query($sql);
              $questions = $result->fetchAll(PDO::FETCH_ASSOC); 
               
              $questions_array = array();
              foreach($questions as $q){
                  
                  $counter = array();
                  
                  $sql = "SELECT text  
                          FROM ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                          WHERE questionid = ". $q['questionid'];
                            
                  $result = $db->query($sql);
                  $answers = $result->fetchAll(PDO::FETCH_ASSOC);
                  
                  $answers_array = array();
                  foreach($answers as $a){
                      $answers_array[] = $a['text'];
                  }
                  
                  foreach($survey_results as $res){
                    //for($i=0; $i < count($answers_array); $i++){
                          if(intval($q['questionid']) == intval($res['question_id'])){
                              if(empty($res['result']) || $res['result'] === '0'){
                                $counter[0] = (empty($counter[0]) ? 1 : $counter[0]+1);
                              }else{
                                $counter[$res['result']] = (empty($counter[$res['result']]) ? 1 : $counter[$res['result']]+1);       
                              }
                          }
                      //}
                  }
                  
                  $questions_array[] = array('question_title' => $q['text'],
                                             'question_type' => $q['type'],
                                             'answers' => array('titles' => $answers_array, 
                                                                'counters' => $counter));
              }
               
               $forms_array[] = array('form_title' => $f['title'],
                                      'questions' => $questions_array);
           }
           
           $RESULTS_ARRAY['forms'] = $forms_array;
            
           download_send_headers("survey_results_" . date("d.m.Y") . ".csv");
           echo survey2csv($RESULTS_ARRAY);   
        
        }else{
            echo 'No results.';
        }
    }
    die();
   }
?>
