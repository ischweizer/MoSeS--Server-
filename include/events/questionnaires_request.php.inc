<?php

$logger->logInfo("############## QUESTIONNAIRES REQUEST ARRIVED ###############");
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
    if($data->APKID != null && !empty($data->APKID))
    {
      $apkid = $data->APKID;
     
      $questionnaires = QuestionnaireManager::getChosenQuestionnireForApkid(
          $DBManager->getDB(), $CONFIG['DB_TABLE']['QUEST'], $CONFIG['DB_TABLE']['APK_QUEST'], $apkid);

      if(!empty($questionnaires) && count($questionnaires) > 0)
      {
        $logger->logInfo("##################### QUESTIONNAIRES REQUEST ################ QUESTIONNAIRE FOUND");
        $quests = '[';
        $i = 1;
        foreach ($questionnaires as $quest)
        {
          if($i > 1)
          {
            $quests .= ',';
          }          
          $questid = $quest["questid"];
          $questname = $quest["name"];
          $quests .= '['.$questid.','.$questname.']';
          $i++;
        }
        $quests = ']';    
        
        $return = array(
            "MESSAGE" => "QUESTIONNAIRES_RESPONSE",
            "STATUS" => "SUCCESS",
            "APKID" => $apkid,
            "QUESTS" => $quests
            );                                
      }
      else
      {
        $return = array(
            "MESSAGE" => "QUESTIONNAIRES_RESPONSE", 
            "STATUS" => "FAILURE_NO_QUESTIONNAIRE_FOUND"
            );
      }
    }
    else
    {
      $return = array(
          "MESSAGE" => "QUESTIONNAIRES_RESPONSE", 
          "STATUS" => "INVALID_APKID"
          );
    }
  }
  else
  {
    $return = array(
        "MESSAGE" => "QUESTIONNAIRES_RESPONSE", 
        "STATUS" => "INVALID_SESSION"
        );
  }
}
else
{
  $return = array(
      "MESSAGE" => "QUESTIONNAIRES_RESPONSE", 
      "STATUS" => "INVALID_SESSION"
      );
} 

print(json_encode($return));

?>