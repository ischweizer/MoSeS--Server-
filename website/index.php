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
//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>The Mobile Sensing System - Welcome to MoSeS!</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <h2>MoSeS makes your life easier!</h2>
        <p>The Mobile Sensing System helps scientists from all around the world 
            to distribute their Android apps and make the world a better place.
        </p>
        <p>    
            Every person with an Android device can contribute.
            Feel free to register and download moses client, it's easy!
        </p>
        <p class="playStoreLink">
        <a href="https://play.google.com/store/apps/details?id=de.da_sense.moses.client" target="_blank">
          <img alt="Android app on Google Play" title="Android app on Google Play" src="./img/google-play-badge.png" />
        </a>
        </p>
        <p>&nbsp;
        </p><?php
            if(!isset($_SESSION['USER_LOGGED_IN'])){    
            ?>
        <p><a href="registration.php" class="btn btn-warning btn-large" style="font-weight: bold; width: 130px;"><i class="icon-white icon-tag"></i> Sign up</a></p>
        <?php
            }                                                                                                                                                                
        ?>
    </div>
    <!-- / Main Block -->
    
    <hr>
<?php
    if(!isset($_SESSION['USER_LOGGED_IN'])){
?>
    <div class="row">
      
      <div class="span8 text-center" style="width: 400pt; height: 50pt; background-color: #EEE; float: none; margin: 0 auto;">
        <div style="margin: 50pt 0pt;">
            <a href="registration.php" class="btn btn-link" style="color: #ffa338; font-weight: bold; font-size: 20pt; margin-left: 15pt; margin-top: 12pt;">Sign up</a>
            <a href="" class="scrollToTop"><i class="icon-chevron-up" style="float: right; margin-right: 15pt; margin-top: 20pt;"></i></a>
        </div>
      </div>
      <div class="span1"></div>
    </div>
<?php
    }         
    
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>
<script type="text/javascript">
    // iterate through all menus and remove selection
    $('.dropdown').each(function(){
        $(this).removeClass('active');   
    });
    // add selection for this page
    $('.nav-menu1').addClass('active');
</script>
