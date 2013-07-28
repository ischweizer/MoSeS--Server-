<?php
include_once("./include/functions/func.php");
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
    
$APK_ID = trim($_POST['study_remove']);

if(is_numeric($APK_ID)){
   
  // getting userhash and apkhash 
  $sql = "SELECT userhash, apkhash 
          FROM ". $CONFIG['DB_TABLE']['APK'] ." 
          WHERE userid = ". $_SESSION['USER_ID'] . " 
          AND apkid = ". $APK_ID;
  
  $result = $db->query($sql);
  $row = $result->fetch();
  
  if(!empty($row)){
      $dir = './apk/' . $row['userhash'];
      if(is_dir($dir)){
         if(file_exists($dir . '/'. $row['apkhash'] . '.apk')){
             unlink($dir . '/' . $row['apkhash'] . '.apk');
             
             if(is_empty_dir($dir)){
                 rmdir($dir);
             }
         }
      }
  }else{
      die('0');
  }
   
  // finally: remove apk entry from DB 
  $sql = "DELETE 
          FROM ". $CONFIG['DB_TABLE']['APK'] ." 
          WHERE userid = ". $_SESSION['USER_ID'] . " AND apkid = ". $APK_ID;
  
  $db->exec($sql);
  
  die('1'); 
}else{
   die('0');
}        
?>