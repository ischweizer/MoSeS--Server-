<?php
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von Boinc4Android - Registration</title>

<?php  
  include_once("./include/_menu.php");  
?>  
  

<div class="heading_text">Registration</div>

<div class="clear"></div>

<form class="registration_form" action="./registration.php" method="post" accept-charset="UTF-8">
    <fieldset>
        <legend>Registration of new user</legend>
        <label for="name" >Your name (*): </label>
        <input type="text" name="name" id="name" maxlength="50" />
        <label for="name" >Your surename (*): </label>
        <input type="text" name="surname" id="surname" maxlength="50" />
        <label for="email" >E-mail address (*):</label>
        <input type="text" name="email" id="email" maxlength="50" />
        <label for="username" >Login (*):</label>
        <input type="text" name="login" id="login" maxlength="50" />
        <label for="password" >Password (*):</label>
        <input type="password" name="password" id="password" maxlength="50" />
        <input type="hidden" name="submitted" id="submitted" value="1" />
        <input type="submit" name="submit" value="Register" />
    </fieldset>
</form>

<?php  
  include_once("./include/_bottom.php");  
?>