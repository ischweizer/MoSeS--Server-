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

<!-- SLIDER -->
<div id="toppanel">
    <div id="panel">
        <div class="login_content clearfix">
            <div class="left">
            
                <?php
                if(isset($_SESSION['USER_LOGGED_IN'])){
                ?>
                
                    <div class="slider_welcome_message">MoSeS welcomes you!</div>
                    <div class="clear"></div>
                    <a class="bt_logout" href="./logout.php">LOGOUT</a>
                <?php
                }else{
                ?>
            
                <!-- Login Form -->
                <form class="clearfix" action="./" method="post" name="login_form">
                    <h1>Member Login</h1>
                    <label class="grey" for="log">Username:</label>
                    <input class="field" type="text" name="login" id="log" value="" size="23" />
                    <label class="grey" for="pwd">Password:</label>
                    <input class="field" type="password" name="password" id="pwd" size="23" />
                    <label><input name="rememberme" id="rememberme" type="checkbox" value="forever" /> &nbsp;Remember me</label>
                    <div class="clear"></div>
                    <input type="submit" name="submit_button" value="Login" class="bt_login" />
                    <div class="clear"></div>
                    <a class="lost-pwd" href="./forgot.php">Lost your password?</a>
                    <div class="clear"></div>
                    <a class="lost-pwd" href="./registration.php">New user? Register here.</a>
                    <input type="hidden" name="submit" value="1">
                </form>
                
                <?php
                }
                ?>
            </div>
        </div>
</div> <!-- /login -->    

    <?php
    if(isset($_SESSION['USER_LOGGED_IN'])){
        
       ?>
       
        <!-- The tab on top -->    
        <div class="tab">
            <ul class="login">
                <li class="left">&nbsp;</li>
                <li>Hello, <?php echo $_SESSION['FIRSTNAME']; ?>!</li>
                <li class="sep">|</li>
                <li id="toggle">
                    <a id="open" class="open" href="#">Menu</a>
                    <a id="close" style="display: none;" class="close" href="#">Hide</a>            
                </li>
                <li class="right">&nbsp;</li>
            </ul> 
        </div> <!-- / top -->
       
       <?php
        
    }else{
    ?>

    <!-- The tab on top -->    
    <div class="tab">
        <ul class="login">
            <li class="left">&nbsp;</li>
            <li>Hallo, Gast!</li>
            <li class="sep">|</li>
            <li id="toggle">
                <a id="open" class="open" href="#">Log In | Register</a>
                <a id="close" style="display: none;" class="close" href="#">Hide</a>            
            </li>
            <li class="right">&nbsp;</li>
        </ul> 
    </div> <!-- / top -->
    <?php
    }
    ?>
    
</div> <!-- SLIDER -->

<?php
 
  include_once("./include/_footer.php");  
?>