<?php

/*
 * @author: Wladimir Schmidt
 */

include_once("./config.php");
include_once("./include/functions/dbconnect.php"); 
   
$request = $_POST['hash'];
   
// update his request, set to not pending one              
$sql = "UPDATE ". $CONFIG['DB_TABLE']['REQUEST'] ."
         SET
         pending = 0, accepted = 0 
         WHERE
         uid = (SELECT userid 
                 FROM ". $CONFIG['DB_TABLE']['USER'] ."
                 WHERE hash = '". $request ."')";
                    
$db->exec($sql);

die("0");
?>