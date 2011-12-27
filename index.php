<?php
session_start();
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
  if(isset($_POST["submit"]) && $_POST["submit"] == "1"){
      echo "<meta http-equiv='refresh' content='0;URL=./'>"; 
  }
  
?>
  
<title>Hauptseite von MoSeS - Home</title>

<?php  
  include_once("./include/_menu.php");
  
  if(isset($_POST["submit"]) && $_POST["submit"] == "1"){
            
      include_once("./include/functions/dbconnect.php");   
      
      $USER_LOGIN =  $_POST["login"];
      $USER_PASSWORD =  $_POST["password"];
      
      $result = $db->query("SELECT * FROM user WHERE login = '". $USER_LOGIN ."' AND password = '". $USER_PASSWORD ."'");
      
      $row = $result->fetch();    
      
      if(!empty($row)){
                
          if($row["confirmed"] == 1){
          
              $USER_CONFIRMED = 1;
              $_SESSION["USER_LOGGED_IN"] = 1;    
              
              $_SESSION["USER_ID"] =   $row["userid"];
              $_SESSION["GROUP_ID"] =  $row["usergroupid"];
              $_SESSION["LOGIN"] =     $row["login"];    
              $_SESSION["PASSWORD"] =  $row["password"];
              $_SESSION["FIRSTNAME"] = $row["firstname"];
              $_SESSION["LASTNAME"] =  $row["lastname"];
              
              // we have an admin here logged in
              if($row["usergroupid"] == 3){
                  $_SESSION["ADMIN_ACCOUNT"] =  "YES";    
              }
              
          }else{
              $USER_CONFIRMED = 0;
          }
      }
  
      
  }
?>  
  
<div class="heading_text">Welcome to our page</div>
<div class="clear"></div>

<?php

if(!isset($_SESSION["USER_LOGGED_IN"])){

?>
    
<form action="./" method="post" name="login_form" class="login_form_box">
  <table width="100" border="0">
  <?php
  
    if(isset($row) && empty($row)){
        ?>
        
        <div class="user_not_found">User not found!</div>
                
        <?php
        
    }else{
        if(isset($USER_CONFIRMED) && $USER_CONFIRMED == 0){
            ?>
            
        <div class="user_not_confirmed">User was not confirmed!</div>
                    
            <?php
        }
    }
    ?>
    <tr>
    <td>Login:</td>
    <td><input name="login" type="text" size="15" maxlength="25" /></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input name="password" type="password" size="15" maxlength="25" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit_button" id="submit_button" value="LOGIN" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="./forgot.php">Forgot password ?</a></td>
  </tr>
  <tr>
    <td><a href="./registration.php">Registration</a></td>
  </tr>
</table>
<input type="hidden" name="submit" value="1">
</form>

<?php

}

?>

<?php  
  include_once("./include/_bottom.php");  
?>