<?php
// start the session
session_start();
// include header
include_once("./include/_header.php");

//If the formular is sent
if(!isset($_POST["submitted"]) && isset($_GET["confirm"]) && strlen(trim($_GET["confirm"])) == 32){
    //Import of configuration?s file
   include_once("./config.php");
   //Import of connections file to database
   include_once("./include/functions/dbconnect.php"); 
   
   $sql = "SELECT userid, confirmed  
           FROM ". $CONFIG['DB_TABLE']['USER'] ." 
           WHERE hash = '". $_GET["confirm"] ."'";
   
   $result = $db->query($sql);
   $row = $result->fetch();
   
   // only update if user is not confirmed yet
   if(!empty($row) && $row['confirmed'] == 0){
       
      $sql = "UPDATE ". $CONFIG['DB_TABLE']['USER'] ." 
              SET confirmed=1, usergroupid=1 
              WHERE userid=". $row["userid"];
      
      $db->exec($sql);
      
      $USER_CONFIRMED = true;
       
   }else{
      $USER_CONFIRMED = false; 
   }
}

if(isset($_POST["submitted"])){
  
  include_once("./config.php");
  include_once("./include/functions/dbconnect.php");
  
  $USER_CREATED = 0;
  $LOGIN_EXISTS = 1;    // default 1 because of fault tolerance
  $ERROR_REGFORM = array();
  
  // init
  $FIRSTNAME = '';
  $LASTNAME = '';
  $EMAIL = '';
  $LOGIN = '';
  $PASSWORD = '';
  
  if(!isset($_POST["firstname"])){
      $ERROR_REGFORM[] = "Please, enter your firstname!";
  }else{
      
     $_POST["firstname"] = trim($_POST["firstname"]);
     if(empty($_POST["firstname"])){
        $ERROR_REGFORM[] = "Please, enter your firstname!";    
     }
  }
  
  if(!isset($_POST["lastname"])){
      $ERROR_REGFORM[] = "Please, enter your lastname!";
  }else{
    
     $_POST["lastname"] = trim($_POST["lastname"]);
     if(empty($_POST["lastname"])){
        $ERROR_REGFORM[] = "Please, enter your lastname!";    
     }
  }
  
  if(!isset($_POST["email"])){
      $ERROR_REGFORM[] = "Please, enter your E-mail!";
  }else{
    
     $_POST["email"] = trim($_POST["email"]);
                                  
     // really better than !preg_match( "/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/", $_POST["email"] ) ????
     if(empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
        $ERROR_REGFORM[] = "Please, enter valid E-mail!";    
     }
  }
  
  if(!isset($_POST["login"])){
      $ERROR_REGFORM[] = "Please, enter your login!";
  }else{
    
     $_POST["login"] = trim($_POST["login"]); 
     if(empty($_POST["login"]) || !preg_match("/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/", $_POST["login"])){
        $ERROR_REGFORM[] = "Please, enter valid login!";    
     }
  }
  
  if(!isset($_POST["password"])){
      $ERROR_REGFORM[] = "Please, enter your password!";   
  }else{
    
     $_POST["password"] = trim($_POST["password"]); 
     if(empty($_POST["password"])){
        $ERROR_REGFORM[] = "Please, enter your password!";   
     } 
  }
  
  if(count($ERROR_REGFORM) == 0){
      
      $FIRSTNAME = $_POST["firstname"];
      $LASTNAME = $_POST["lastname"];
      $EMAIL = $_POST["email"];
      $LOGIN = $_POST["login"];
      $PASSWORD = $_POST["password"];
      $CUR_TIME = time();
      $CONFIRM_CODE = md5($EMAIL);
      
      $sql = "SELECT userid 
              FROM ". $CONFIG['DB_TABLE']['USER'] ." 
              WHERE login = '". $LOGIN ."'";
      
      $result = $db->query($sql);
      $row = $result->fetch();

      if(!empty($row)){
          $USER_CREATED = 0;
          $LOGIN_EXISTS = 1;
          
          $ERROR_REGFORM[] = "That login already exists! Please choose another one.";
          
      }else{
          
          $sql = "SELECT userid 
                  FROM ". $CONFIG['DB_TABLE']['USER'] ." 
                  WHERE email = '". $EMAIL ."'";
          
          $result = $db->query($sql);
          $row = $result->fetch();

          if(!empty($row)){
              
              $USER_CREATED = 0;
              $LOGIN_EXISTS = 1;
              
              $ERROR_REGFORM[] = "That E-mail already exists! Please choose another one.";
              
          }else{
              
            $LOGIN_EXISTS = 0;
          
              // we have no duplicate logins
              // so we can insert new entry

             $sql = "INSERT INTO ". $CONFIG['DB_TABLE']['USER'] ." (usergroupid, firstname, lastname, 
                                                      login, password, hash, usertitle,
                                                      email, ipaddress, lastactivity, 
                                                      joindate, passworddate)
                          VALUES 
                          (0, '". $FIRSTNAME ."', '". $LASTNAME ."',
                          '". $LOGIN ."', '". $PASSWORD ."', '". $CONFIRM_CODE ."', '". $USER_TITLE ."',
                          '". $EMAIL ."', '". $_SERVER["REMOTE_ADDR"] ."', ". $CUR_TIME .",
                          ". $CUR_TIME .", ". $CUR_TIME .")";
              
              $db->exec($sql);
              
              $USER_CREATED = 1;  
                 
              // compose email to user                                        
              $to = $EMAIL; 
              $subject = "MoSeS: Please confirm your registration"; 
              $from = "admin@moses.tk.informatik.tu-darmstadt.de"; 
                  
              $message = "Hi, ". $FIRSTNAME ." ". $LASTNAME ."!\n";
              $message .= "Please follow this link to confirm your registration: ";
              $message .= "http://". $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"] ."?confirm=". $CONFIRM_CODE;
               
              $headers = "From: $from"; 
              $sent = mail($to, $subject, $message, $headers); 
              
              // sending was successful?
              if(!$sent) {
                  die("We encountered an error sending your mail"); 
              }      
          }
      } 
  } 
}
  
?>
  
<title>Hauptseite von MoSeS - Registration</title>

<?php  //Import of the menu
  include_once("./include/_menu.php");  
?>  

<!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>Registration</h2>
        <?php
                           
                if(isset($USER_CONFIRMED) && $USER_CONFIRMED){
                   ?>
                    <div class="registration_form">
                        <fieldset>
                            <legend>Confirmed!</legend>
                            <label>Your registration was successful confirmed.</label>
                            <label>You may now log in into MoSeS.</label>
                        </fieldset>
                    </div>
                   
                   <?php 
                }
           
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
                }
                
                if(!(isset($USER_CREATED) && $USER_CREATED == 1) && !(isset($USER_CONFIRMED) && $USER_CONFIRMED)){
            ?>

            <form class="registration_form" action="./registration.php" method="post" accept-charset="UTF-8">
                <fieldset>
                    <legend>Registration of new user</legend>
                    <label for="firstname" >Your first name (*): </label>
                    <div class="clear"></div>
                    <input type="text" name="firstname" id="firstname" maxlength="50" <?php
                        if(isset($_POST["firstname"])){
                            echo 'value="'. trim($_POST["firstname"]) .'" ';
                        }                                                                         
                    ?>/>
                    <div class="clear"></div>
                    <label for="lastname" >Your last name (*): </label>
                    <div class="clear"></div>
                    <input type="text" name="lastname" id="lastname" maxlength="50" <?php
                        if(isset($_POST["lastname"])){
                            echo 'value="'. trim($_POST["lastname"]) .'" ';
                        }                                                                         
                    ?>/>
                    <div class="clear"></div>
                    <label for="email" >E-mail address (*):</label>
                    <div class="clear"></div>
                    <input type="text" name="email" id="email" maxlength="50" <?php
                        if(isset($_POST["email"])){
                            echo 'value="'. trim($_POST["email"]) .'" ';
                        }                                                                         
                    ?>/>
                    <div class="clear"></div>
                    <label for="login" >Username (*):</label>
                    <div class="clear"></div>
                    <input type="text" name="login" id="login" maxlength="50" <?php
                        if(isset($_POST["login"])){
                            echo 'value="'. trim($_POST["login"]) .'" ';
                        }                                                                         
                    ?>/>
                    <div class="clear"></div>
                    <label for="password" >Password (*):</label>
                    <div class="clear"></div>
                    <input type="password" name="password" id="password" maxlength="50" <?php
                        if(isset($_POST["password"])){
                            echo 'value="'. trim($_POST["password"]) .'" ';
                        }                                                                         
                    ?>/>
                    <div class="clear"></div>
                    <?php
                         if(count($ERROR_REGFORM) > 0){
                     ?>
                        <ul class="error_regform"><?php
                        
                        foreach($ERROR_REGFORM as $err){
                           echo "<li>". $err ."</li>"; 
                        }
                        
                        ?></ul>
                    <?php
                         }
                     ?>
                    <input type="hidden" name="submitted" id="submitted" value="1" />
                    <input type="submit" name="submit" value="Register" />
                </fieldset>
            </form>

            <?php
            }
        ?>
        <br />
    </div>
    <!-- / Main Block -->
    
    <hr>  
<?php 
//Import of the slider to login
  include_once("./include/_login.php");
//IMport of the footer 
  include_once("./include/_footer.php");  
?>