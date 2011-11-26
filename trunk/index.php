<?php
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von Boinc4Android - Home</title>

<?php  
  include_once("./include/_menu.php");  
?>  
  
<div class="heading_text">Welcome to our page</div>

<form action="login.php" method="post" name="login_form" class="login_form_box">
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

</form>


<?php  
  include_once("./include/_bottom.php");  
?>