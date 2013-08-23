<?php
   include_once("./config.php");
   include_once("./include/functions/dbconnect.php"); 
       
   $request = $_REQUEST['hash'];
       
   // update his request, set to not pending one              
   $sql = "UPDATE ". $CONFIG['DB_TABLE']['REQUEST'] ."
             SET
             pending = 0, accepted = 1 
             WHERE
             uid = (SELECT userid 
                     FROM ". $CONFIG['DB_TABLE']['USER'] ."
                     WHERE hash = '". $request ."')";
                        
    $db->exec($sql);
   
    // USER IS NOW IN A SCIENTIST GROUP
    $sql = "UPDATE ". $CONFIG['DB_TABLE']['USER'] ." 
            SET usergroupid = 2 
            WHERE hash = '". $request ."'";
   
    $db->exec($sql);
    
    die("0");
?>