<?php
    
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php"); 
       
    /*
     *   Remove device by its device id
    */
    $sql = "DELETE FROM ". $CONFIG['DB_TABLE']['HARDWARE'] ."  
                  WHERE uid = ". $_SESSION['USER_ID'] . " 
                  AND hwid = '". $_REQUEST['remove'] ."'";
      
    $db->exec($sql);
    
    die('1');
?>