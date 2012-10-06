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
        $quests = array();
        $i = 1;

        $isStandard1 = -1;
        $isStandard2 = -1;
        $isStandard3 = -1;
        $isSUS = -1;

        foreach ($questionnaires as $quest)
        {
                    
          $questid = $quest["questid"];
          

          if($questid == "1")
          {
            $isStandard1 = 1;
          }
          elseif($questid == "2")
          {
            $isStandard2 = 2;
          }
          elseif($questid == "3")
          {
            $isStandard2 = 3;
          }
          elseif($questid == "4")
          {
            $isSUS = 4;
          }
          else
          {
            $quests[]= $questid;
          }

        }
        $questIds = json_encode($quests);    
        
        $return = array(
            "MESSAGE" => "QUESTIONNAIRES_RESPONSE",
            "STATUS" => "SUCCESS",
            "APKID" => $apkid,

            "SUS" => $isSUS,
            "STANDARD1" => $isStandard1,
            "STANDARD2" => $isStandard2,
            "STANDARD3" => $isStandard3,
            
            "QUESTIDS" => $questIds
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