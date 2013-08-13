<?php
    
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php"); 
       
    $sql = "UPDATE ".$CONFIG['DB_TABLE']['USER']. " u, ".$CONFIG['DB_TABLE']['RGROUP']. " g   
            SET u.usergroupid = 2, g.instant_scientists_counter = g.instant_scientists_counter + 1 
            WHERE u.userid=" . $_SESSION['USER_ID'] ." AND u.rgroup = g.name";
    
    $db->exec($sql);
    
    die('1');
?>