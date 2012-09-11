<?php
    
  class QuestionnaireManager
  {
   
      public function __construct(){
                                       
      }
      
     /**
      * Returns the questionnaire array for a specific $questID from DB
      * 
      * @param mixed $db
      * @param mixed $questionnaireTabel
      * @param mixed $questID
      */
      public static function getQuestionnaireArray($db, $questionnaireTable, $questID){
          
         $sql = "SELECT *  FROM ". $questionnaireTable ." WHERE questid = ". $questID;
                      
         $result = $db->query($sql);
         $row = $result->fetch();
         
         if(!empty($row))
         {
              $quest = "[";
              for($i = 1; $i < 21 ; $i++){
                  $temp = $quest.$row['standard'.$i].",";
                  $quest = $temp;
              }
              $quest = $quest.$row['dynamic']."]";
              return $quest;
         }
         return null;
      }
      
      /**
      * Returns all questionnaires in $questionnaireTable from $db
      * 
      * @param mixed $db
      * @param mixed $questionnaireTable
      */
      public static function getAllQuestionnaires($db, $questionnaireTable){
      	
	     $sql = "SELECT *  FROM ". $questionnaireTable;           
         $result = $db->query($sql);
         $rows = $result->fetchAll();
         return $rows;
      }
      
      /**
      * Returns all questions of a specific $questID from $db
      * 
      * @param mixed $db
      * @param mixed $questionTabel
      * @param mixed $questID
      */
      public static function getQuestionsForQuestid($db, $questionTable, $questID){
      	
	     $sql = "SELECT *  FROM ". $questionTable ." WHERE questid = ". $questID;          
         $result = $db->query($sql);
         $rows = $result->fetchAll();
         return $rows;
      }
      
      /**
      * Returns all chosen questionnaires (as array of questid) of a specific $apkID (aka user study) from $db
      * 
      * @param mixed $db
      * @param mixed $questionnaireTable
      * @param mixed $apk_questTable
      * @param mixed $apkID
      */
      public static function getChosenQuestionnireForApkid($db, $questionnaireTable, $apk_questTable, $apkID){
      	
	     $sql = "SELECT *  FROM ".$questionnaireTable." WHERE questid in (SELECT questid  FROM ".$apk_questTable." WHERE apkid = ".$apkID.")";
         $result = $db->query($sql);
         $rows = $result->fetchAll();
         return $rows;
      }
      
      /**
      * Returns all not chosen questionnaires (as array of questid) of a specific $apkID (aka user study) from $db
      * 
      * @param mixed $db
      * @param mixed $questionnaireTable
      * @param mixed $apk_questTable
      * @param mixed $apkID
      */
      public static function getNotChosenQuestionnireForApkid($db, $questionnaireTable, $apk_questTable, $apkID){
      	
	     $sql = "SELECT *  FROM ".$questionnaireTable." WHERE questid NOT in (SELECT questid  FROM ".$apk_questTable." WHERE apkid = ".$apkID.")";  
         $result = $db->query($sql);
         $rows = $result->fetchAll();
        return $rows;
          
      }
      
      /**
      * pair a questionnaire $questid with a user study $apkID in $apk_questTable from $db
      * 
      * @param mixed $db
      * @param mixed $questionnaireTable
      * @param mixed $apk_questTable
      * @param mixed $apkID
      * @param mixed $questID
      */
      public static function pairQuestionnireWithApk($db, $apk_questTable, $apkID, $questID){

      	$sql ="SELECT * FROM $apk_questTable WHERE apkid=$apkID AND questid = $questID";
        echo "<br> pair: ".$sql."<br>";
    		$req=$db->query($sql);
    		$rows = $req->fetch();
    		if(empty($rows))
    		{
		      $sql = "INSERT INTO $apk_questTable (apkid, questid) VALUES ($apkID,$questID)"; 
	        $result = $db->exec($sql);
    	  }
      }
      
      /**
      * Returns all answers of a specific $apkID (aka user study) and a question $qid from $db
      * 
      * @param mixed $db
      * @param mixed $answerTable
      * @param mixed $qid
      * @param mixed $apkID
      */
      public static function getAnswersForQidAndApkid($db, $answerTable, $qid, $apkID){
        
       $sql = "SELECT *  FROM ".$answerTable." WHERE qid = ".$qid." AND apkid = ".$apkID;
       $result = $db->query($sql);
       $rows = $result->fetchAll();
       return $rows;
      }
      
  }
    
?>