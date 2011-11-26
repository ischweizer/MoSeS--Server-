<?php
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von Boinc4Android - Home</title>

<?php  
  include_once("./include/_menu.php");
  
  if(isset($_POST["submit"]) && $_POST["submit"] == "1" ){
      
      include_once("./include/functions/dbconnect.php");   
      
      $USER_LOGIN =  $_POST["login"];
      $USER_PASSWORD =  $_POST["password"];
      
      $row = $db->query("SELECT * FROM user WHERE login = '". $USER_LOGIN ."' AND password = '". $USER_PASSWORD ."'");
     
      print_r($row);
      
  }  
?>  
  
<div class="heading_text">Welcome to our page</div>

<form action="./index.php" method="post" name="login_form" class="login_form_box">
  <table width="100" border="0">
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
  include_once("./include/_bottom.php");  
?>