<?php

/*
 * @author: Zijad Maksuti
 */

include_once("./config.php");
include_once("./include/functions/dbconnect.php");
include_once("./include/functions/logger.php");
$logger->logInfo(" ###################### content_provider.php changing password ############################## ");

// init
$CUR_TIME = time();
// update the password
$sql = "UPDATE ".$CONFIG["DB_TABLE"]["USER"]." 
        SET password='".$_POST["newPassword"]."', passworddate=".$CUR_TIME." WHERE hash='".$_POST["hash"]."'";
$db->exec($sql);
die("0");
?>