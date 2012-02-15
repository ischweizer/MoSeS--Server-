<?php
  
// SCRIPT USED FOR CLEANING OUTDATED SESSIONS
// get all apks
include_once('/home/dasense/moses/config.php');
include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");

$sql = "DELETE FROM ". $CONFIG['DB_TABLE']['ANDROID_SESSION'] ." WHERE ".time()."- lastactivity > ". $CONFIG['SESSION']['TIMEOUT'];
$logger->logInfo(" ###################### CLEAN OUTDATED ANDROID SESSIONS CRONJOB ############################## ");
$logger->logInfo($sql);
$db->exec($sql);
$logger->logInfo(" ###################### CLEAN OUTDATED ANDROID SESSIONS CRONJOB DONE ############################## ");
?>
