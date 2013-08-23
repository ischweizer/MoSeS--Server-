<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) && $_SESSION["GROUP_ID"] == 0){
    header("Location: " . dirname($_SERVER['PHP_SELF']));   
    exit;
}

include_once("./include/functions/dbconnect.php");

$sql = "SELECT r.reason, u.hash, u.usergroupid, u.firstname, u.lastname, u.email 
       FROM ". $CONFIG['DB_TABLE']['REQUEST'] ." r, ". $CONFIG['DB_TABLE']['USER'] ." u 
       WHERE r.pending = 1 AND r.uid = u.userid";
       
$result = $db->query($sql);
$array = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($array)){
  $USERS_SCIENTIST_LIST = $array;
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>The Mobile Sensing System - Admin panel</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit">
        <form class="form-horizontal saveProfileForm" method="post" accept-charset="UTF-8">
            <fieldset>
                <legend>Edit Your Profile</legend>
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