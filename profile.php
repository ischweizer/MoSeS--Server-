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
 */

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) && $_SESSION["GROUP_ID"] == 0){
    header("Location: " . dirname($_SERVER['PHP_SELF']));   
    exit;
}

include_once("./include/functions/func.php");

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>The Mobile Sensing System - Edit Your Profile</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <form class="form-horizontal saveProfileForm" method="post" accept-charset="UTF-8">
            <fieldset>
                <legend>Edit Your Profile</legend>
                <div class="control-group">
                    <label class="control-label">Access level</label>
                    <div class="controls">
                        <?php echo getUserAccessLevelTitleById($_SESSION["GROUP_ID"]); ?>
                    </div>
                </div>
                <?php
                if(isset($_SESSION['GROUP_ID']) && $_SESSION['GROUP_ID'] == 1){
                     // request a scientist account link
                     ?>
                     <div class="control-group">
                        <div class="controls">
                            <a href="apply.php" class="btn btn-success btnApplyAsScientist">Apply as scientist</a>
                        </div>
                    </div>
                <?php
                
                 }
                ?>
                <div class="control-group">
                    <label class="control-label">First name</label>
                    <div class="controls">
                        <input type="text" name="firstname" value="<?php echo $_SESSION["FIRSTNAME"]; ?>" placeholder="Enter your name">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Last name</label>
                    <div class="controls">
                        <input type="text" name="lastname" value="<?php echo $_SESSION["LASTNAME"]; ?>" placeholder="Enter your family name">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <label style="cursor: default;">Change password</label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Password</label>
                    <div class="controls">
                        <input type="password" name="password1" placeholder="Password">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Password repeat</label>
                    <div class="controls">
                        <input type="password" name="password2" placeholder="Repeat password">
                    </div>
                </div>
                <div class="clear"></div>
                 <div class="control-group">
                     <label class="control-label"></label>
                     <div class="controls">
                        <input type="hidden" name="edit_profile_code" value="9950" />
                        <button class="btn btn-success btnSaveProfile">Save Profile</button>
                    </div>
                </div>
            </fieldset>
        </form>
    <hr>

 <?php
 
//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>
<script src="js/profile.js"></script>
