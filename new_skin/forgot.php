<?php
  
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - Registration</title>

<?php  
  include_once("./include/_menu.php");  
?>  
  

<div class="heading_text">Forgot my password</div>

<div class="clear"></div>

<form class="forgot_pass_form" action="./forgot.php" method="post" accept-charset="UTF-8">
    <fieldset>
        <legend>Forgot your password? You will receive new one via E-mail.</legend>
        <label for="email" >Fill your E-mail here: </label>
        <input type="text" name="email" id="email" maxlength="50" />
        <input type="hidden" name="submitted" id="submitted" value="1" />
        <input type="submit" name="submit" value="Finish" />
    </fieldset>
</form>

<?php  
  include_once("./include/_footer.php");  
?>
