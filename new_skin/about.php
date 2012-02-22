<?php
session_start();
  
include_once("./include/_header.php");
  
?>
  
<title>Hauptseite von MoSeS - About</title>

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
                        <h2 class="title">About us/app</h2>
                        <div class="entry">
                            <p>We are so cool man!</p>
                        </div>
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