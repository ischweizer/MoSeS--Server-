<?php

/*
* Handling of CHANGE_DEVICE_ID messages
*/

$logger->logInfo("############## ANSWER_QUESTIONNAIRE ARRIVED ###############");
$logger->logInfo(print_r($data, true));

if($data->SESSIONID != null)
{  
  $sessionID = $data->SESSIONID;
  $logger->logInfo("SESSIONID PROVIDED BY THE CLIENT: ".$sessionID );

  if($data->APKID != null)
  {
    $apkid = $data->APKID;
    $logger->logInfo("apkid =  ".$apkid);

    if($data->ANSWERS != null)
    {
      $answers_edcoded = $data->ANSWERS;
      $logger->logInfo("anwsers_encoded =  ".$answers_edcoded);

      include_once(MOSES_HOME . '/include/functions/dbconnect.php');
      $userid = LoginManager::getLoggedInUser($db, $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
      $logger->logInfo("userid =  ".$userid['userid']);


      $answers_elements = explode("#SG#", $answers_edcoded);

      foreach ($answers_elements as $answer_element)
      {
        $logger->logInfo("answer_element =  ".$answer_element);

        $answer = explode("#SE#", $answer_element);

        if(count($answer) == 2)
        {
          $logger->logInfo("set for qid = ".$answer[0]." the answer = ".$answer[1]);
          include_once(MOSES_HOME . '/include/functions/dbconnect.php');
          QuestionnaireManager::setAnswerForQuestion( 
              $db,
              $CONFIG['DB_TABLE']['ANSWER'],
              $answer[0],
              $apkid,
              $userid['userid'],
              $answer[1],
              $logger
            );
        } 
      }

      $return = array(
          "MESSAGE" => "ANSWER_QUESTIONNAIRE", 
          "STATUS" => "SUCCESS",
          "APKID" => $data->APKID
          );  
    }
    else
    {
      $return = array(
        "MESSAGE" => "ANSWER_QUESTIONNAIRE",
        "STATUS" => "FAILURE_INVALID_ANSWERS",
        "APKID" => $data->APKID
        );
    }
  }
  else
  {
    $return = array(
      "MESSAGE" => "ANSWER_QUESTIONNAIRE",
      "STATUS" => "FAILURE_INVALID_APKID",
      "APKID" => $data->APKID
      );
  }
}
else
{
  $return = array(
    "MESSAGE" => "ANSWER_QUESTIONNAIRE",
    "STATUS" => "FAILURE_INVALID_SESSION",
    "APKID" => $data->APKID
    );
}

print(json_encode($return));

?>
