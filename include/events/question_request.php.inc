<?php

$logger->logInfo("############## QUESTION REQUEST ARRIVED ###############");
$logger->logInfo("SESSIONID PROVIDED BY THE CLIENT: ".$data->SESSIONID);

$sessionID = '';

/**
*  SESSIONID check
*/
if($data->SESSIONID != null)
{
  if(is_md5($data->SESSIONID))
  {
    $sessionID = $data->SESSIONID;
    if($data->QID != null && !empty($data->QID))
    {
      $qid = $data->QID;

      $question = QuestionnaireManager::getQuestionWithQid(
          $DBManager->getDB(), $$CONFIG['DB_TABLE']['QUESTION'], $qid);
        
      if(!empty($question))
      {
        $logger->logInfo("##################### QUESTION REQUEST ################ QUESTION FOUND");
        
        $qid = $question['qid'];
        $qcontent = $question['content'];
        $qtype = $question['type'];
        $qSortedAnswer = $question['sortedAnswers'];

        $return = array(
            "MESSAGE" => "QUESTION_RESPONSE",
            "STATUS" => "SUCCESS",
            "QID" => $qid,
            "CONTENT" => $qcontent,
            "TYPE" => $qtype,
            "SORTEDANSWERS" => $qSortedAnswer
            );
      }
      else
      {
        $return = array(
            "MESSAGE" => "QUESTION_RESPONSE",
            "STATUS" => "FAILURE_NO_QUESTION_FOUND",
            ); 
      }
    }
    else
    {
      $return = array(
          "MESSAGE" => "QUESTION_RESPONSE", 
          "STATUS" => "INVALID_QID");
    }
  }
  else
  {
    $return = array(
        "MESSAGE" => "QUESTION_RESPONSE", 
        "STATUS" => "INVALID_SESSION");
  }
}
else
{
  $return = array(
      "MESSAGE" => "QUESTION_RESPONSE", 
      "STATUS" => "INVALID_SESSION");
} 

print(json_encode($return));

?>