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
 * @author: Zijad Maksuti
 */
  
// SCRIPT USED FOR CLEANING OBSOLETE OUTDATED ANDROID SESSIONS
include_once('/home/dasense/moses/config.php');
include_once(MOSES_HOME."/include/functions/cronLogger.php");
include_once(MOSES_HOME. "/include/functions/dbconnect.php");

$sql = "DELETE FROM ". $CONFIG['DB_TABLE']['ANDROID_SESSION'] ." WHERE ".time()."- lastactivity > ". $CONFIG['SESSION']['TIMEOUT'];
$logger->logInfo(" ###################### CLEAN OUTDATED ANDROID SESSIONS CRONJOB ############################## ");
$logger->logInfo($sql);
$db->exec($sql);
$logger->logInfo(" ###################### CLEAN OUTDATED ANDROID SESSIONS CRONJOB DONE ############################## ");
?>
