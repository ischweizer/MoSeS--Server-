<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] < 1){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    exit;
}
    
//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Request a scientist account</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <form class="form-horizontal apply_scientist_form" method="post" accept-charset="UTF-8">
            <fieldset>
                <legend>Apply as scientist form</legend>
                <div class="control-group">
                    <label class="control-label">Telephone</label>
                    <div class="controls">
                        <input type="tel" name="telephone" maxlength="10"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Reason (*)</label>
                    <div class="controls">
                        <textarea cols="30" rows="10" name="reason" placeholder="Tell us, please, why you want a scientist account."></textarea>
                    </div>
                </div>
                <div class="clear"></div>
                 <div class="control-group">
                     <label class="control-label"></label>
                     <div class="controls">
                        <input type="hidden" name="promo_sent" value="9325" />
                        <button class="btn btn-success btnApplyScientistSend">Send</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <!-- / Main Block -->
    
    <hr>
<?php
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>
<script src="js/apply_scientist.js"></script>