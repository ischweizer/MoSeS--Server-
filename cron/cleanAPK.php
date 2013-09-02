<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php
  
/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */
  
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
