<?php
include_once("./config.php");
include_once("./include/functions/dbconnect.php");

$pages = intval($_REQUEST['pages']);
$pageMax = intval($_REQUEST['pageMax']);
$curPage = intval($_REQUEST['curPage']);

$USER_DEVICES = array();

$sql = 'SELECT * 
       FROM hardware 
       WHERE uid = '. $_SESSION['USER_ID'] .' 
       LIMIT '.((($curPage-1)*$pageMax)).', '.($curPage*$pageMax);
                               
$result = $db->query($sql);
$devices = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($devices)){
  $USER_DEVICES = $devices;
}

//$ar = array('modelname' => $_SESSION['USER_ID']);
// return devices as json
die(json_encode($USER_DEVICES));
?>