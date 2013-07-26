<?php
   include_once("./config.php");
   include_once("./include/functions/dbconnect.php"); 
       
   $request = $_REQUEST['hash'];
       
   // update his request, set to not pending one              
   $sql = "UPDATE request
             SET
             pending = 0, accepted = 1 
             WHERE
             uid = (SELECT userid 
                     FROM user
                     WHERE hash = '". $request ."')";
                        
    $db->exec($sql);
   
    // USER IS NOW IN A SCIENTIST GROUP
    $sql = "UPDATE user 
            SET usergroupid= 2 
            WHERE hash = '". $request ."'";
   
    $db->exec($sql);
    
    die("0");
?>