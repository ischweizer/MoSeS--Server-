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
    if($data->QUESTID != null && !empty($data->QUESTID))
    {
      $questid = $data->QUESTID;
      if($data->PART != null && !empty($data->PART))
      {
        $part = $data->PART;

        $questions = QuestionnaireManager::getQuestionsForQuestid(
            $DBManager->getDB(), $$CONFIG['DB_TABLE']['QUESTION'], $questid);
        
        if(!empty($questions) && count($questions) > 0)
        {
          $logger->logInfo("##################### QUESTION REQUEST ################ QUESTION FOUND");
          
          if(is_integer($part) && $part <= (count($questions)))
          {
            $logger->logInfo("##################### QUESTION REQUEST ################ QUESTION FOR THIS PART FOUND");
            $index = $part - 1;
            $q = $questions[$index];

            $qid = $q['qid'];
            $qcontent = $q['content'];
            $qtype = $q['type'];

            $max = count($questions);

            $return = array(
                "MESSAGE" => "QUESTION_RESPONSE",
                "STATUS" => "SUCCESS",
                "QUESTID" => $questid,
                "QID" => $qid,
                "CONTENT" => $qcontent,
                "TYPE" => $qtype,
                "PART" => $part,
                "MAX" => $max
                );

          }
          else
          {
            $return = array(
                "MESSAGE" => "QUESTION_RESPONSE",
                "STATUS" => "INVALID_PART",
                );
          }
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
            "STATUS" => "INVALID_PART");
      }
    }
    else
    {
      $return = array(
          "MESSAGE" => "QUESTION_RESPONSE", 
          "STATUS" => "INVALID_QUESTID");
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