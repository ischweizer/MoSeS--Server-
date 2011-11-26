<?php
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von Boinc4Android - Registration</title>

<?php  
  include_once("./include/_menu.php");  
?>  
  

<div class="heading_text">Forgot my password</div>

<div class="clear"></div>

<form class="forgot_pass_form" action="./forgot.php" method="post" accept-charset="UTF-8">
    <fieldset>
        <legend>Forgot your password?</legend>
        <label for="email" >Fill your E-mail here: </label>
        <input type="text" name="email" id="email" maxlength="50" />
        <input type="hidden" name="submitted" id="submitted" value="1" />
        <input type="submit" name="submit" value="Finish" />
    </fieldset>
</form>

<?php  
  include_once("./include/_bottom.php");  
?>