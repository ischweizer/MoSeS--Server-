<?php

/*
 * @author: Zijad Maksuti
 * @author: Wladimir Schmidt
 * @author: Sandra Christina Amend
 */   
    
class SurveyManager
{

  public function __construct(){                  
  }
  
  /**
  * Returns the row containing the survey for the speified $apkID
  * @param $logger the logger
  * @param mixed $db
  * @param mixed $surveyTable the table containing the surveys
  * @param mixed $apkID
  * @return mixed $apk containing the the survey
  */
  public static function getSurvey($logger, $db, $surveyTable, $apkID){
      
     $sql = "SELECT * 
             FROM ".$surveyTable." 
             WHERE apkid=".$apkID;
             
     $logger->logInfo("SurveryManager::getSurvey(), sql=".$sql);
     $result = $db->query($sql);
     $apk = $result->fetch(PDO::FETCH_ASSOC);
     
     return $apk;
  }
  
  /**
   * Returns all rows containing forms for the speified $surveyID
   *
   * @param mixed $db
   * @param mixed $formsTable the table containing the forms
   * @param mixed $surveyID the id of the survey for which forms should be returned
   * @return mixed $rows containing forms for the specified $surveyID
   */
  public static function getForms($db, $formsTable, $surveyID){
       
      $sql = "SELECT *  FROM ".$formsTable." WHERE surveyid=".$surveyID;
      $result = $db->query($sql);
      $rows = $result->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
  }
  
  /**
   * Returns all rows containing questions for the speified $formID
   *
   * @param mixed $db
   * @param mixed $questionsTable the table containing the questions
   * @param mixed $formID the id of the form for which questions should be returned
   * @return mixed $rows containing questions for the specified $formID
   */
  public static function getQuestions($db, $questionsTable, $formID){
  
      $sql = "SELECT *  FROM ".$questionsTable." WHERE formid=".$formID;
      $result = $db->query($sql);
      $rows = $result->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
  }
  
  /**
   * Returns all rows containing possible answers for the speified $questionID
   *
   * @param mixed $db
   * @param mixed $possibleAnswersTable the table containing the possible answers
   * @param mixed $questionID the id of the question for which possible answers should be returned
   * @return mixed $rows containing possible answers for the specified $questionID
   */
  public static function getPossibleAnswers($db, $possibleAnswersTable, $questionID){
  
      $sql = "SELECT *  FROM ".$possibleAnswersTable." WHERE questionid=".$questionID;
      $result = $db->query($sql);
      $rows = $result->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
  }

  /**
   * Returns the formid containing a question with the specified id. If a mapping between the
   * specified question id and a form id could be found in the tables of the database. If this is not
   * the case, this method returns NULL;
   * @param unknown $db the database
   * @param unknown $CONFIG the config array
   * @param unknown $questionID the id of the question for which the formid is queried
   * @return NULL|string
   */
  public static function getFormIdForQuestion($logger, $db, $CONFIG, $questionID){
      $sql = "SELECT formid  FROM ".$CONFIG['DB_TABLE']['STUDY_QUESTION']." WHERE questionid=".$questionID;
      $logger->logInfo("SurveyManager:getFormIdForQuestion() sql=".$sql);
      $result = $db->query($sql);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      if($row == null || empty($row))
          return null;
      else{
          $formid = $row['formid'];
          if($formid == null || empty($formid))
              return null;
          else
              return $formid;
      }
  }
  
  /**
   * Returns the survey id containing a form with the specified id. If a mapping between the
   * specified form id and a survey id could be found in the tables of the database. If this is not
   * the case, this method returns NULL;
   * @param unknown $db the database
   * @param unknown $CONFIG the config array
   * @param unknown $formID the id of the form for which the questionid is queried
   * @return NULL|string
   */
  public static function getSurveyIdForForm($logger, $db, $CONFIG, $formID){
      $sql = "SELECT surveyid  FROM ".$CONFIG['DB_TABLE']['STUDY_FORM']." WHERE formid=".$formID;
      $logger->logInfo("SurveyManager:getSurveyIdForQuestion() sql=".$sql);
      $result = $db->query($sql);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      if($row == null || empty($row))
          return null;
      else{
          $surveyid = $row['surveyid'];
          if($surveyid == null || empty($surveyid))
              return null;
          else
              return $surveyid;
      }
  }
  
  /**
   * Returns an associative array containing the formid and surveyid mapped to the consumed questionid.
   * If such mapping could not be found, this function returns NULL.
   * The informations can be obtained from the array by calling values of following keys: "formid" and "surveyid".
   * @param unknown $db the database
   * @param unknown $CONFIG the config array
   * @param unknown $questionID the id of the question for which informations should be fetched
   * @return NULL|array
   */
  public static function getQuestionInformation($logger, $db, $CONFIG, $questionID){
      $formid = SurveyManager::getFormIdForQuestion($logger, $db, $CONFIG, $questionID);
      if($formid == null){
          $logger->logInfo("SurveyManager:getQuestionInformation formid not found, returning null");
          return null;
      }
      else{
          $logger->logInfo("SurveyManager:getQuestionInformation formid=".$formid);
          $surveyid = SurveyManager::getSurveyIdForForm($logger, $db, $CONFIG, $formid);
          if($surveyid == null){
              $logger->logInfo("SurveyManager:getQuestionInformation surveyid not found, returning null");
              return null;
          }
          else{
              $logger->logInfo("SurveyManager:getQuestionInformation surveyid=".$surveyid);
              $arr = array("formid" => $formid, "surveyid" => $surveyid);
              return $arr;
          }
      }
  }
  
  /**
   * Inserts an answer made by a user into the result table.
   * @param unknown $db the database
   * @param unknown $resultTableName the name of the result table
   * @param unknown $surveyID the id of the survey
   * @param unknown $formID the id of the form
   * @param unknown $questionID the id of the question
   * @param unknown $answer the answer to the question
   */
  public static function insertAnswer($logger, $db, $resultTableName, $surveyID, $formID, $questionID, $answer){
      
      $sql = "INSERT INTO " . $resultTableName . 
                    " (survey_id, form_id, question_id, result) 
                    VALUES 
                    ('". $surveyID ."', ". $formID . ", " . $questionID . ", '".$answer."')";
      $logger->logInfo("SurveyManager:insertAnswer() sql=".$sql);
      $db->exec($sql);
    
  }
  
  
  /**
   * Queries the survey database and returns true if a survey for the given apk exists
   * @param unknown $logger the logger
   * @param unknown $db the database
   * @param unknown $surveyTableName the name of the survey table
   * @param unknown $apkID the if of the apk which is queried
   * @return boolean true only if the user study with the given apkID has a survey attached
   */
  public static function hasSurvey($logger, $db, $surveyTableName, $apkID){
      $sql = "SELECT apkid FROM ".$surveyTableName." WHERE apkid=".$apkID;
      $logger->logInfo("SurveyManager:hasSurvey() sql=".$sql);
      $result = $db->query($sql);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      if(empty($row)){
          $logger->logInfo("SurveyManager:hasSurvey() no survey found for apkID=".$apkID);
          return false;
      }
      else{
          $logger->logInfo("SurveyManager:hasSurvey() found survey for apkID=".$apkID);
          return true;
      }
  }
  
  public static function hasSurveyWOLogger($db, $surveyTableName, $apkID){
      $sql = "SELECT apkid FROM ".$surveyTableName." WHERE apkid=".$apkID;
      $result = $db->query($sql);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      if(empty($row)){
          return false;
      }
      else{
          return true;
      }
  }
  
}   
?>