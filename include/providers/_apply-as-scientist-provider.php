<?php
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php"); 
       
    $RAW_TELEPHONE = $_POST['telephone'];
    $RAW_REASON = $_POST['reason'];
    $TELEPHONE  = trim($RAW_TELEPHONE);
    $REASON  = trim($RAW_REASON);
    
    $sql = "SELECT accepted, pending 
            FROM ". $CONFIG['DB_TABLE']['REQUEST'] ." 
            WHERE uid = ". $_SESSION['USER_ID'];
    
    $result = $db->query($sql);
    $row = $result->fetch();    

    // user has sent scientist request
    if(!empty($row)){
        if($row['pending'] == 1){
            // user pending
            die('1');  
        }else 
            die('0');
        /*else{
            if($row['accepted'] == 1){
                // user not pending
                // user was accepted
                die('2');  
            }
        }*/
    }else{
        
        // User isn't applied as scientist yet, insert request
         $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['REQUEST'] ." 
                (uid, telephone, reason) 
                VALUES 
                (". $_SESSION['USER_ID'] .", '". $TELEPHONE . "', '". $REASON ."')";

         $db->exec($sql);
         
         // user pending
         die('1');
    }
?>