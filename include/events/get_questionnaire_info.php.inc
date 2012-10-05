<?php

$logger->logInfo("############## GET QUESTIONNAIRE INFO ARRIVED ###############");
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

      if($data->APKID != null)
      {
        $apkid = $data->APKID;
        $logger->logInfo("apkid =  ".$apkid);

        $questionnaire = QuestionnaireManager::getQuestionnaireWithQuestid(
          $DBManager->getDB(), $CONFIG['DB_TABLE']['QUEST'], $questid);

        if(!empty($questionnaire))
        {
          $logger->logInfo("##################### GET QUESTIONNAIRE INFO ################ QUESTIONNAIRE FOUND");

          $questions = QuestionnaireManager::getQuestionsForQuestid(
              $DBManager->getDB(), $CONFIG['DB_TABLE']['QUESTION'], $questid);

          if(!empty($questions) && count($questions) > 0)
          {
            $logger->logInfo("##################### GET QUESTIONNAIRE INFO ################ QUESTION FOUND");
          
            $i = 1;

            foreach ($questions as $question)
            {
                        
              $qId = $question['qid'];
              $qType = $question['type'];
              $qContent = $question['content'];
              $qSortedAnswers = $question['sortedAnswers'];

              $qQuestionText = trim(substr($qContent,0,strrpos($qContent,"[")));

              $sortedAnswersArray =
                json_decode(trim(substr($qContent,(strrpos($qContent,"[")-1),(strrpos($qContent,"]")+1))));
              $possibleAnswers = "";
              foreach ($sortedAnswersArray as $answer)
              {
                if($possibleAnswers != "")
                {
                  $possibleAnswers .= "#SA#";
                }
                $possibleAnswers .= $answer;
              }

              if($i > 1)
              {
                $qs .= '#SG#';
              }

              $qs .= $qType."#sep#".$qId."#sep#".$qQuestionText."#sep#".$possibleAnswers."#sep#";
              $i++;

            }   
            $logger->logInfo("QUESTIONS = ".$qs);

            $return = array(
                "MESSAGE" => "GET_QUESTIONNAIRE",
                "STATUS" => "SUCCESS",
                "QUESTID" => $questid,
                "APKID" => $apkid,
                "NAME" => $questionnaire['name'],
                "QUESTIONS" => $qs
                );
          }
          else
          {
            $return = array(
                "MESSAGE" => "GET_QUESTIONNAIRE", 
                "STATUS" => "FAILURE_NO_QUESTION_FOUND",
                "APKID" => $apkid,
                "QUESTID" => $data->QUESTID
                );
          }                              
        }
        else
        {
          $return = array(
              "MESSAGE" => "GET_QUESTIONNAIRE", 
              "STATUS" => "FAILURE_NO_QUESTIONNAIRE_FOUND",
              "APKID" => $apkid,
              "QUESTID" => $data->QUESTID
              );
        }
      }
      else
      {
        $return = array(
          "MESSAGE" => "ANSWER_QUESTIONNAIRE",
          "STATUS" => "FAILURE_INVALID_APKID",
          "APKID" => $data->APKID,
          "QUESTID" => $data->QUESTID
          );
      }
    }
    else
    {
      $return = array(
          "MESSAGE" => "GET_QUESTIONNAIRE", 
          "STATUS" => "INVALID_QUESTID",
          "APKID" => $data->APKID
          );
    }
  }
  else
  {
    $return = array(
        "MESSAGE" => "GET_QUESTIONNAIRE_", 
        "STATUS" => "INVALID_SESSION",
        "APKID" => $data->APKID,
        "QUESTID" => $data->QUESTID
        );
  }
}
else
{
  $return = array(
      "MESSAGE" => "GET_QUESTIONNAIRE_", 
      "STATUS" => "INVALID_SESSION",
      "APKID" => $data->APKID,
      "QUESTID" => $data->QUESTID
      );
} 

print(json_encode($return));

?>