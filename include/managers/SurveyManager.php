<?php
    
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
      * @return mixed $row containing the the survey
      */
      public static function getSurvey($logger, $db, $surveyTable, $apkID){
      	
	     $sql = "SELECT * FROM ".$surveyTable." WHERE apkid=".$apkID;
	     $logger->logInfo("SurveryManager::getSurvey(), sql=".$sql);
         $result = $db->query($sql);
         $row = $result->fetch();
         return $row;
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
      
      
  }
    
?>