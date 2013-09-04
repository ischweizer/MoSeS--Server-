<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */

// start the session
session_start();

if(isset($_SESSION['USER_LOGGED_IN'])){
    header("Location: " . dirname($_SERVER['PHP_SELF'])); 
    exit;
}

// include header
include_once("./include/_header.php");

include_once("./config.php");
include_once(MOSES_HOME."/include/functions/logger.php");
$logger->logInfo(" ###################### REGISTRATION ############################## ");

//If the formular is sent
if(isset($_GET["confirm"]) && strlen(trim($_GET["confirm"])) == 32){
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
  
<title>The Mobile Sensing System - Registration</title>

<?php  //Import of the menu
  include_once("./include/_menu.php");  
?>  

<!-- Main Block -->
    <div class="hero-unit">
        <?php
                           
                if(isset($USER_CONFIRMED) && $USER_CONFIRMED){
                   ?>
                    <div class="registration_form">
                        <fieldset>
                            <legend>Confirmed!</legend>
                            <h4>You have successfully confirmed your registration. You can now log in.</h4>
                        </fieldset>
                    </div>
                   
                   <?php 
                }
                
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
	                    	<input type="text" type="email" name="email" id="email" maxlength="50"/>
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
	                    	<button type="submit" name="submit" id="submitButton" class="btn btn-success">Create account</button>
                    	</div>
                    </div>
                </fieldset>
                <input type="hidden" name="submitted" id="submitted" value="1" />
                <input type="hidden" name="url" value="<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>">
            </form>

            <?php
            }
        ?>
        <br/>
    </div>
    <!-- / Main Block -->
    
    <hr>  
<?php 
  include_once("./include/_login.php");
  include_once("./include/_footer.php");  
?>
<script src="js/registration.js"></script>
