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
      $answers = $data->ANSWERS;
      $logger->logInfo("print_r(anwsers) =  ".print_r($answers,true));

      include_once(MOSES_HOME . '/include/functions/dbconnect.php');
      $userid = LoginManager::getLoggedInUser($db, $CONFIG['DB_TABLE']['ANDROID_SESSION'], $sessionID);
      $logger->logInfo("userid =  ".$userid['userid']);

      foreach ($answers as $qid => $content)
      {
        $logger->logInfo("qid =  ".$qid." content = ".$content);
        if($content != null)
        {
          include_once(MOSES_HOME . '/include/functions/dbconnect.php');
          QuestionnaireManager::setAnswerForQuestion( 
              $db,
              $CONFIG['DB_TABLE']['ANSWER'],
              $qid,
              $apkid,
              $userid['userid'],
              $content,
              $logger
            );
        }
      }
      $return = array(
          "MESSAGE" => "ANSWER_QUESTIONNAIRE", 
          "STATUS" => "SUCCESS");  
    }
    else
    {
      $return = array(
        "MESSAGE" => "ANSWER_QUESTIONNAIRE",
        "STATUS" => "FAILURE_INVALID_ANSWERS");
    }
  }
  else
  {
    $return = array(
      "MESSAGE" => "ANSWER_QUESTIONNAIRE",
      "STATUS" => "FAILURE_INVALID_APKID");
  }
}
else
{
  $return = array(
    "MESSAGE" => "ANSWER_QUESTIONNAIRE",
    "STATUS" => "FAILURE_INVALID_SESSION");
}

print(json_encode($return));

?>
