<?php

/*
 * @author: Wladimir Schmidt
 */

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] < 1){
    header("Location: " . dirname($_SERVER['PHP_SELF']));
    exit;
}

// user is still just user
if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] == 1){

    include_once("./include/functions/dbconnect.php");

    $USER_PENDING = -1;
    $USER_ACCEPTED = -1;
                            
    $sql = "SELECT accepted, pending 
            FROM ". $CONFIG['DB_TABLE']['REQUEST'] ." 
            WHERE uid = ". $_SESSION['USER_ID'];
            
    $result = $db->query($sql);
    $row = $result->fetch();

    if(!empty($row)){
       if($row['pending'] == 1){
            $USER_PENDING = 1;
            
            if($row['accepted'] == 0){
                $USER_ACCEPTED = 0;  
            }else{
                $USER_ACCEPTED = 1;  
            }
        }else{
            $USER_PENDING = 0;
            
            if($row['accepted'] == 1){
                $USER_ACCEPTED = 1;  
            }else{
                $USER_ACCEPTED = 0;
            }
        }
    }
}
    
//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>The Mobile Sensing System - Request a scientist account</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <?php
        if($USER_ACCEPTED == 0 && $USER_PENDING == 0){
        ?>
            <h2 class="text-center">Sorry, but your application was rejected.</h2>
        <?php
        }else
            if($USER_ACCEPTED == 0 && $USER_PENDING == 1){
            ?>
            <h2 class="text-center">Your application for a scientist is pending...</h2>
            <?php
            }else
                if($USER_ACCEPTED == 1 && $USER_PENDING == 0){
                    ?>
                    <h2 class="text-center">You were accepted for a scientist account!</h2>
                    <?php       
                }else
                if($USER_ACCEPTED == -1 && $USER_PENDING == -1){    
                ?>
                <form class="form-horizontal apply_scientist_form" method="post" accept-charset="UTF-8">
                    <fieldset>
                        <legend>Apply as scientist form</legend>
                        <div class="control-group">
                            <label class="control-label">Reason (*)</label>
                            <div class="controls">
                                <textarea cols="30" rows="10" name="reason" placeholder="Please give us some details about yourself and your research" title="Please give us some details about yourself and your research"></textarea>
                            </div>
                        </div>
                        <div class="clear"></div>
                         <div class="control-group">
                             <label class="control-label"></label>
                             <div class="controls">
                                <input type="hidden" name="promo_sent" value="9325" />
                                <button class="btn btn-success btnApplyScientistSend">Send application</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <?php
                }
        ?>
    </div>
    <!-- / Main Block -->
    
    <hr>
<?php
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>
<script src="js/apply_scientist.js"></script>