<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Wladimir Schmidt
 */

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] <= 1){
    header("Location: " . dirname($_SERVER['PHP_SELF']));
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
            
            // select suited survey
            $sql = "SELECT userid, apkid 
                    FROM ". $CONFIG['DB_TABLE']['STUDY_SURVEY'] ." 
                    WHERE surveyid = ". $id;
                    
           $result = $db->query($sql);
           $survey = $result->fetch(PDO::FETCH_ASSOC);
            
            // selecting APK title, 
            // participated count and 
            // survey result sent count
            $sql = "SELECT apktitle, participated_count, survey_results_sent_count 
                    FROM ". $CONFIG['DB_TABLE']['APK'] ." 
                    WHERE apkid = ". $survey['apkid'] ." AND userid = ". $survey['userid'];
                    
           $result = $db->query($sql);
           $apk_res = $result->fetch(PDO::FETCH_ASSOC);
           $apk_title = $apk_res['apktitle'];
           $apk_participated_count = $apk_res['participated_count'];
           $apk_survey_results_sent_count = $apk_res['survey_results_sent_count'];
           
           $RESULTS_ARRAY['apk_title'] = $apk_title;
           $RESULTS_ARRAY['participated_count'] = $apk_participated_count;
           $RESULTS_ARRAY['survey_results_sent_count'] = $apk_survey_results_sent_count;
           
           // select all forms
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
                  
                  $sql = "SELECT aid, text  
                          FROM ". $CONFIG['DB_TABLE']['STUDY_ANSWER'] ." 
                          WHERE questionid = ". $q['questionid'];
                            
                  $result = $db->query($sql);
                  $answers = $result->fetchAll(PDO::FETCH_ASSOC);
                  
                  $answers_array = array();
                  foreach($answers as $a){
                      $answers_array[$a['aid']] = $a['text'];
                  }
                  
                  // through all results
                  foreach($survey_results as $res){
                      
                      // if we found matched question id with the result question id
                      if(intval($q['questionid']) == intval($res['question_id'])){
                          
                          // unanswered
                          if(empty($res['result']) || $res['result'] === '0'){
                            $counter[0] = (empty($counter[0]) ? 1 : $counter[0]+1);
                          }else{
                            // normal answers, numeric
                            if(is_numeric($res['result'])){
                                // increment counter
                                $counter[$res['result']] = (empty($counter[$res['result']]) ? 1 : $counter[$res['result']]+1);       
                            }else{
                                // miltiple choice question
                                if(json_decode($res['result'])){
                                    // multiple choice question
                                    $ans = json_decode($res['result']);
                                    foreach($ans as $a){
                                        // increment counter
                                        $counter[$a] = (empty($counter[$a]) ? 1 : $counter[$a]+1);   
                                    }
                                }else{
                                    if($q['type'] == 2){
                                        // text question
                                        $answers_array[] = $res['result'];
                                    }
                                }
                            }
                          }
                      }
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
