<?php
session_start();
  
  include_once("./include/_top.php");
  include_once("./include/_header.php");
  
  if(isset($_POST["submitted"])){
      
      include_once("./include/functions/dbconnect.php");
      
      $USER_CREATED = 1;
      
      $FIRSTNAME = $_POST["firstname"];
      $LASTNAME = $_POST["lastname"];
      $EMAIL = $_POST["email"];
      $LOGIN = $_POST["login"];
      $PASSWORD = $_POST["password"];
      $USER_TITLE = $_POST["usertitle"];
      $CUR_TIME = time();
      
      $db->exec("INSERT INTO user (usergroupid, firstname, lastname, 
                                              login, password, usertitle,
                                              email, ipaddress, lastactivity, 
                                              joindate, passworddate)
                                              VALUES 
                                              (1, '". $FIRSTNAME ."', '". $LASTNAME ."',
                                              '". $LOGIN ."', '". $PASSWORD ."', '". $USER_TITLE ."',
                                              '". $EMAIL ."', '". $_SERVER["REMOTE_ADDR"] ."', ". $CUR_TIME .",
                                              ". $CUR_TIME .", ". $CUR_TIME .")");
                                              
      
      
  }
  
?>
  
<title>Hauptseite von Boinc4Android - Registration</title>

<?php  
  include_once("./include/_menu.php");  
?>  
  

<div class="heading_text">Registration</div>

<div class="clear"></div>

<?php
    if(isset($USER_CREATED) && $USER_CREATED == 1){
       ?>
       
<div class="registration_form">
    <fieldset>
        <legend>Registration of new user</legend>
        <label for="name" >Your registration was successful.</label>
        <label for="name" >You will receive an e-mail with confirmation of registration.</label>
    </fieldset>
</div>
       
       <?php       
    }else{
?>

<form class="registration_form" action="./registration.php" method="post" accept-charset="UTF-8">
    <fieldset>
        <legend>Registration of new user</legend>
        <label for="name" >Your salutation (*): </label>
        <input type="text" name="usertitle" id="usertitle" maxlength="10" />
        <label for="name" >Your first name (*): </label>
        <input type="text" name="firstname" id="firstname" maxlength="50" />
        <label for="name" >Your last name (*): </label>
        <input type="text" name="lastname" id="lastname" maxlength="50" />
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
    }
?>

<?php  
  include_once("./include/_bottom.php");  
?>