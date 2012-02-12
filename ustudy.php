<?php

// this is a cronjob used for user studies

// get all apks
include_once('/home/dasense/moses/config.php');
include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");

$logger->logInfo(" ###################### STARTED USER STUDY CRONJOB ############################## ");
 
$sql = "SELECT * FROM " .$CONFIG['DB_TABLE']['APK']. " WHERE restriction_device_number > 0 AND ustudy_finished = 0";
        
$result = $db->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

// iterate over all apkids
foreach($rows as $row){
    // new round for every apk that is not installed on the required number of devices
    $RESTRICTION_USER_NUMBER = $row['restriction_device_number'];
    $PARTICIPATED_COUNT = $row['participated_count'];
    $last_round_time = $row['last_round_time']; // the last time a round on this apk has been made
    if($PARTICIPATED_COUNT < $RESTRICTION_USER_NUMBER && (empty($last_round_time) || ($last_round_time >= $CONFIG['CRON']['STUDY_TIMEOUT']))){
        $TO_CHOOSE = $RESTRICTION_USER_NUMBER - $PARTICIPATED_COUNT; // number of new devices to be selected
        $pending_devices = json_decode($row['pending_devices']); // users that have not installed the app
        $notified_devices = json_decode($row['notified_devices']); // notified users
        foreach($pending_devices as $pending_device){
            $notified_devices[] = $pending_device; // move the device to notified devices
        }
        
        // select new users
        $num_new_devices = $RESTRICTION_USER_NUMBER - $PARTICIPATED_COUNT;
        $pending_devices = array(); // new users to send the notification to
        $candidates = json_decode($row['candidates']);
        for($i=0; $i<$num_new_devices; $i++){
            if($i >= count($candidates)){
                break;
            }
           $pending_devices[] = $candidates[$i];
        }
        
        // remove the new devices from candidates list
        if(count($candidates) > 0){
            $candidates = array_slice($candidates, count($pending_devices));
        }
        
        $ustudyFinished = 0; // this value will be written back to apk database
        if(count($pending_devices) == 0 && count($candidates) == 0){
            $ustudyFinished = 1; // user study is finished when there are no devices pending for installation and no potentiall candidates to send the notification to
        }
        
        // extracting c2dms for google
        
        
         $targetDevices = array();
        
   
        foreach($pending_devices as $pending_device){
             $sql = "SELECT c2dm FROM hardware WHERE hwid=".$pending_device;
             $result = $db->query($sql);
             $row_hw = $result->fetch();
             if(!empty($row_hw)){
                $targetDevices[] = $row_hw['c2dm'];
             }
        }
        
        //// ##########
        
        
        // UPDATE THE DATABASE
        $pending_devices = json_encode($pending_devices);
        $candidates = json_encode($candidates);
        $notified_devices = json_encode($notified_devices);
        
        
        $sql = "UPDATE " .$CONFIG['DB_TABLE']['APK']. "
        SET pending_devices='".$pending_devices."' , candidates='".$candidates."' , notified_devices='".$notified_devices."' , last_round_time=".time().
            ", ustudy_finished = ".$ustudyFinished. " WHERE apkid=".$row['apkid'];
        
        
        $logger->logInfo(print_r("QUERY IN ustudy", true));
        $logger->logInfo(print_r($sql, true)); 
                
        $db->exec($sql);
        
        
        
        
        // PUSH
        include_once(MOSES_HOME."/include/managers/GooglePushManager.php");
        if(count($targetDevices) > 0)
            GooglePushManager::googlePushSend($row['apkid'], $targetDevices, $logger);
         
    }
}

$logger->logInfo(" ###################### USER STUDY CRONJOB FINISHED ############################## ");


?>