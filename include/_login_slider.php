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
                    <!--<label><input name="rememberme" id="rememberme" type="checkbox" value="forever" /> &nbsp;Remember me</label>-->
                    <div class="clear"></div>
                    <input type="submit" name="submit_button" value="Login" class="bt_login" />
                    <div class="clear"></div>
                    <a class="lost-pwd" href="./forgot.php">Lost your password?</a>
                    <div class="clear"></div>
                    <a class="lost-pwd" href="./registration.php">New user? Register here!</a>
                    <input type="hidden" name="submit" value="1">
                </form>
                
                <?php
                }
                ?>
            </div>
        </div> <!-- /login_content -->
</div> <!-- /panel -->    

    <?php
    if(isset($_SESSION['USER_LOGGED_IN'])){
        
       ?>
       
        <!-- The tab on top -->    
        <div class="tab">
            <ul class="login">
                <!--<li class="left">&nbsp;</li>-->
                <li class="welcome">Hello, <?php echo $_SESSION['FIRSTNAME']; ?>!</li>
                <li class="sep">|</li>
                <li id="toggle">
                    <a id="open" class="open" href="#">Logout</a>
                    <a id="close" style="display: none;" class="close" href="#">Hide</a>            
                </li>
                <!--<li class="right">&nbsp;</li>-->
            </ul> 
            </div> 
        </div> <!-- / top -->
       
       <?php
        
    }else{
    ?>

    <!-- The tab on top -->    
    <div class="tab">
        <ul class="login">
            <!--<li class="left">&nbsp;</li>-->
            <li class="welcome">Hello, Guest!</li>
            <li class="sep">|</li>
            <li id="toggle">
                <a id="open" class="open" href="#">Login</a>
                <a id="close" style="display: none;" class="close" href="#">HIDE</a>            
            </li>
            <!--<li class="right">&nbsp;</li>-->
        </ul>                   
        </div> 
    </div> <!-- / topPanel -->
    <?php
    }
    ?>
    
</div> <!-- SLIDER -->