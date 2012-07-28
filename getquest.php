<?php
include_once("./include/functions/dbconnect.php");


$q=$_POST["id"];

$choice='';
$sql="SELECT * FROM questionnaire WHERE questid = '".$q."'";


$req=$db->query($sql) ;


while($row=$req->fetch()){
  $choice.= "<input type='checkbox'  name='questionnaire[]' value = ".$row['questid'] ." id = ".$row['name']." checked>";
  $choice.=  $row['name'];
  $choice.="<br>";
  }
  echo $choice;


?>