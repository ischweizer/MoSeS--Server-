<?php
    
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php"); 
       
    $sql = "UPDATE ".$CONFIG['DB_TABLE']['USER']. " 
            SET usergroupid = 2 
            WHERE userid=" . $_SESSION['USER_ID'];
    
    $db->exec($sql);
    
    die('1');
?>