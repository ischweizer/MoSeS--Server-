<?php
  
// SCRIPT USED FOR CLEANING OBSOLETE APKs from APK-Directory
include_once('/home/dasense/moses/config.php');
include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME."/include/functions/dbconnect.php");

$dir = MOSES_HOME."/apk/";

$logger->logInfo(" ###################### CLEAN OBSOLETE APKs CRONJOB ############################## ");

if ($handle = opendir($dir)) {

    // iterate over user-directories
    while (false !== ($file = readdir($handle))) {
        $content = $dir.$file;
        if(is_dir($content)  && $file != ".." && $file != "."){
            $files = array();
            // iterate over apks
            $apkdirPath = $content."/";
            $apkdir = opendir($apkdirPath);
            while (false !== ($apkhash = readdir($apkdir))){
                if($apkhash != ".." && $apkhash != "." ){
                    $sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['APK']. " WHERE userhash = '".$file."' AND apkhash = '". substr($apkhash, 0, -4)."'"; // remove ".apk"
                    $result = $db->query($sql);
                    $row = $result->fetch();
                    if(empty($row)){
                        // the apk-name does not exist in the database, remove it from hard drive
                        $toRemove = $apkdirPath.$apkhash;
                        if(unlink($toRemove))
                            $logger->logInfo("removed apk ".$toRemove);
                        else
                            $logger->logInfo("unable to remove apk ".$toRemove);
                    }
                    else
                        $files[]=$apkhash;
                }
            }
            
            // delete directory if empty
            if(count($files) == 0){
                if(rmdir($apkdirPath))
                    $logger->logInfo("removed directory ".$apkdirPath);
                else
                    $logger->logInfo("unable to remove directory ".$apkdirPath);
            }  
        }
    }
}

closedir($handle);
$logger->logInfo(" ###################### CLEAN OBSOLETE APKs CRONJOB DONE ############################## ");
?>
