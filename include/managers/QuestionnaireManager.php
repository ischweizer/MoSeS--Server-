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
      public static function getQuestionnaireArray($db, $questionnaireTabel, $questID){
          
         $sql = "SELECT *  FROM ". $questionnaireTabel ." WHERE questid = ". $questID;
                      
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
  }
    
?>