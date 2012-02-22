<?php
session_start();

include_once("./include/_header.php");

if(isset($_GET["confirm"]) && strlen($_GET["confirm"]) == 32){
  
   include_once("./include/functions/dbconnect.php"); 
   
   $sql = "SELECT userid FROM user WHERE hash = '". $_GET["confirm"] ."'";
   
   $result = $db->query($sql);
   
   $row = $result->fetch();
   
   if(!empty($row)){
      $sql = "UPDATE user SET confirmed=". 1 ." WHERE userid=". $row["userid"];
      
      $db->exec($sql);
       
   }else{
       die("You provided wrong hash.");
   }
}

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
  $CONFIRM_CODE = md5($EMAIL);
  
  $sql = "INSERT INTO user (usergroupid, firstname, lastname, 
                                          login, password, hash, usertitle,
                                          email, ipaddress, lastactivity, 
                                          joindate, passworddate)
                                          VALUES 
                                          (1, '". $FIRSTNAME ."', '". $LASTNAME ."',
                                          '". $LOGIN ."', '". $PASSWORD ."', '". $CONFIRM_CODE ."', '". $USER_TITLE ."',
                                          '". $EMAIL ."', '". $_SERVER["REMOTE_ADDR"] ."', ". $CUR_TIME .",
                                          ". $CUR_TIME .", ". $CUR_TIME .")";
  
  $db->exec($sql);
    
                                          
  $to = $EMAIL; 
  $subject = "Our site - Please confirm the registration"; 
  $from = "admin@localhost"; 
      
  $message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
  $message .= "Please follow this link: ";
  $message .= "http://". $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"] ."?confirm=". $CONFIRM_CODE;
   
  $headers = "From: $from"; 
  $sent = mail($to, $subject, $message, $headers); 
  
  if($sent) {
      echo("Your mail was sent successfully"); 
  } else {
      die("We encountered an error sending your mail"); 
  }
  
}
  
?>
  
<title>Hauptseite von MoSeS - Registration</title>

<?php  
  include_once("./include/_menu.php");  
?>  
  

<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>
<!-- <div id="splash">&nbsp;</div> -->
<!-- end #header -->

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="content">
                    <div class="post">
                        <h2 class="title">Registration</h2>
                        <div class="entry">
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
                                    <label for="usertitle" >Your salutation (*): </label>
                                    <div class="clear"></div>
                                    <input type="text" name="usertitle" id="usertitle" maxlength="10" />
                                    <div class="clear"></div>
                                    <label for="firstname" >Your first name (*): </label>
                                    <div class="clear"></div>
                                    <input type="text" name="firstname" id="firstname" maxlength="50" />
                                    <div class="clear"></div>
                                    <label for="lastname" >Your last name (*): </label>
                                    <div class="clear"></div>
                                    <input type="text" name="lastname" id="lastname" maxlength="50" />
                                    <div class="clear"></div>
                                    <label for="email" >E-mail address (*):</label>
                                    <div class="clear"></div>
                                    <input type="text" name="email" id="email" maxlength="50" />
                                    <div class="clear"></div>
                                    <label for="login" >Login (*):</label>
                                    <div class="clear"></div>
                                    <input type="text" name="login" id="login" maxlength="50" />
                                    <div class="clear"></div>
                                    <label for="password" >Password (*):</label>
                                    <div class="clear"></div>
                                    <input type="password" name="password" id="password" maxlength="50" />
                                    <div class="clear"></div>
                                    <input type="hidden" name="submitted" id="submitted" value="1" />
                                    <input type="submit" name="submit" value="Register" />
                                </fieldset>
                            </form>

                            <?php
                            }
                        ?>
                        </div>
                        <div style="clear: both;">&nbsp;</div>
                    </div>
                    <div style="clear: both;">&nbsp;</div>
                </div>
                <!-- end #content -->
                <div style="clear: both;">&nbsp;</div>
            </div>
        </div>
    </div>
    <!-- end #page -->
</div>

<?php
  include_once("./include/_login_slider.php");
 
  include_once("./include/_footer.php");  
?>