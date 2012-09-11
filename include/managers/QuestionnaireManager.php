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
         return NULL;
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
    		$req=$db->query($sql);
    		$rows = $req->fetch();
    		if(empty($rows))
    		{
		      $sql = "INSERT INTO $apk_questTable (apkid, questid) VALUES ($apkID,$questID)"; 
	        $result = $db->exec($sql);
    	  }
      }

      /**
      * unpair a questionnaire $questid with a user study $apkID in $apk_questTable from $db
      * 
      * @param mixed $db
      * @param mixed $questionnaireTable
      * @param mixed $apk_questTable
      * @param mixed $apkID
      * @param mixed $questID
      */
      public static function unpairQuestionnireWithApk($db, $apk_questTable, $apkID, $questID){

        $sql ="DELETE FROM $apk_questTable WHERE apkid=$apkID AND questid = $questID";
        $db->exec($sql);
        
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

      /**
      * to find the popular answer of an array of strings
      */
      public static function getPopularAnswerOfArray($ansArr){
        if(is_array($ansArr) && !empty($ansArr))
        {
          // to pair each answer with its quantity
          $counter_array = array();
          $bestAns = "-";
          $maxAns = 0;
          foreach($ansArr as $ans)
          {
            $counter_array[$ans]++;
            if($maxAns < $counter_array[$ans])
            {
              $bestAns = $ans;
              $maxAns = $counter_array[$ans];
            }
            elseif($maxAns == $counter_array[$ans])
            {
              $bestAns .= ", ".$ans;
            }
          }
          return $bestAns;
        }
        else
        {
          return "no answer";
        }
      }

      /**
      * to find the average answer of an array of strings
      */
      public static function getAverageAnswerOfArray($ansArr, $sortedAns){
        if(is_array($ansArr) && !empty($ansArr))
        {
          // this value represents an answer
          $value = 1;

          // to pair each answer with a value
          $valueToAns_array = array(); // ans -> value
          $ansToValue_array = array(); // value -> ans

          // loop and make a value for each answer 
          foreach($sortedAns as $ans)
          {
            // set for each new answer a new value
            if($ansToValue_array[$ans] == 0)
            {
              // set value
              $ansToValue_array[$ans] = $value; // TODO or = ++$value;
              $valueToAns_array[$value] = $ans;

              // make another value for coming new answer
              $value++;
            }
          }

          // to get an average value
          $totalValue = 0;
          $numberOfValue = 0; // TODO : may be there is a funchtion to get the length of an array
          foreach($ansArr as $ans)
          {
            // counter the number of values
            $numberOfValue++;

            // add a new value to total
            $totalValue += $ansToValue_array[$ans];
          }

          // the average value could be float number like 2.7 => so the average should be then 2~3
          $averageValue = $totalValue/$numberOfValue;

          $lowestAverageValue = floor($averageValue);
          $highestAverageValue = ceil($averageValue);

          if($lowestAverageValue == $highestAverageValue)
          {
            return $valueToAns_array[$lowestAverageValue];
          }
          else
          {
            return $valueToAns_array[$lowestAverageValue]." ~ ".$valueToAns_array[$highestAverageValue];
          }

        }
        else
        {
          return "no answer";
        }
      }
      
  }
    
?>