<?php
   include_once("./config.php");
   include_once("./include/functions/dbconnect.php"); 
       
   $firstname = trim($_POST['firstname']);
   $lastname = trim($_POST['lastname']);
   $password1 = trim($_POST['password1']);
   $password2 = trim($_POST['password2']);
   
   if($password1 !== $password2){
       // passwords not match
       die("1");
   }
   
   $pass_edit = !empty($password1) && !empty($password2);
       
   // Update user's info
   $sql = "UPDATE ". $CONFIG['DB_TABLE']['USER'] ." 
           SET firstname = '". $firstname ."',
               lastname = '". $lastname ."'". 
               ($pass_edit ? ", password = '". $password2 ."'" : "") ."
           WHERE userid = ". $_SESSION['USER_ID'];

   $result = $db->exec($sql);

   // no such user or something bad happened
   if($result === false){
       die("1");
   }
   
   $_SESSION["FIRSTNAME"] = $firstname;
   $_SESSION["LASTNAME"] =  $lastname;
    
   die("0");
?>