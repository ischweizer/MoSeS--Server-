<?php
// start the session
session_start();

if(isset($_SESSION['USER_LOGGED_IN']))
        header("Location: " . dirname($_SERVER['PHP_SELF'])."/"); 

// include header
include_once("./include/_header.php");

include_once("./config.php");
include_once(MOSES_HOME."/include/functions/logger.php");
$logger->logInfo(" ###################### REGISTRATION ############################## ");

//If the formular is sent
if(!isset($_POST["submitted"]) && isset($_GET["confirm"]) && strlen(trim($_GET["confirm"])) == 32){
    //Import of configurations file
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

?>
  
<title>Hauptseite von MoSeS - Registration</title>

<?php  //Import of the menu
  include_once("./include/_menu.php");  
?>  

<!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
<!--         <h2>Registration</h2> -->
        <?php
                           
                if(isset($USER_CONFIRMED) && $USER_CONFIRMED){
                   ?>
                    <div class="registration_form">
                        <fieldset>
                            <legend>Confirmed!</legend>
                            <label>Your registration was successfully confirmed.</label>
                            <label>You can now log in.</label>
                        </fieldset>
                    </div>
                   
                   <?php 
                }
           
//                 if(isset($USER_CREATED) && $USER_CREATED == 1){
//                    ?>
                   
<!--             <div class="registration_form"> -->
<!--                 <fieldset> -->
<!--                     <legend>Registered</legend> -->
<!--                     <label for="name" >Your registration was successful!</label> -->
<!--                     <label for="name" >You will receive an e-mail for confirmation of your registration.</label> -->
<!--                 </fieldset> -->
<!--             </div> -->
                   
                   <?php       
//                 }
                
                if(!(isset($USER_CONFIRMED) && $USER_CONFIRMED)){
            ?>
<!-- This is where the user enters his data when registering -->
            <form class="form-horizontal" action="./registration.php" method="post" accept-charset="UTF-8" id="registerHere">
                <fieldset id="registration_fieldset">
                    <legend>Registration</legend>
                    <div class="control-group">
                    	<label for="firstname" class="control-label">First name</label>
	                    <div class="controls">
	                    	<input type="text" name="firstname" id="firstname" maxlength="50"/>
	                    </div>
                    </div>
                    <div class="control-group">
	                    <label for="lastname" class="control-label">Last name</label>
	                    <div class="controls">
	                    	<input type="text" name="lastname" id="lastname" maxlength="50"/>
	                    </div>
                    </div>
                    <div class="control-group">
	                    <label for="email" class="control-label">Email</label>
	                    <div class="controls">
	                    	<input type="text" name="email" id="email" maxlength="50"/>
	                    </div>
                    </div>
                    <div class="control-group">
                    <label for="password" class="control-label">Password</label>
<!--                     <div class="clear"></div> -->
                    <div class="controls">
                    <input type="password" name="password" id="password" maxlength="50"/>
                    </div>
                    </div>
                    <div class="control-group">
                    	<label for="password_repeat" class="control-label">Confirm Password</label>
                   			<div class="controls">
                    			<input type="password" name="password_repeat" id="password_repeat" maxlength="50"/>
                    		</div>
                    </div>
                    <div class="clear"></div>
                     <div class="control-group">
                     	<label class="control-label"></label>
                     	<div class="controls">
	                    	<input type="hidden" name="submitted" id="submitted" value="1" />
	                    	<button type="submit" name="submit" id="submitButton" class="btn btn-success">Create account</button>
                    	</div>
                    </div>
                </fieldset>
            </form>

            <?php
            }
        ?>
        <br/>
    </div>
    <!-- / Main Block -->
    
    <hr>  
<?php 
//Import of the slider to login
  include_once("./include/_login.php");
//IMport of the footer 
  include_once("./include/_footer.php");  
?>