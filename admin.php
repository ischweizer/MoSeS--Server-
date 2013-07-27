<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    if(!(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"))
        header("Location: " . dirname($_SERVER['PHP_SELF'])."/");

if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
   
   include_once("./include/functions/dbconnect.php");
   
   $USERS_SCIENTIST_LIST = array();
   
   $sql = "SELECT r.telephone, r.reason, u.hash, u.usergroupid, u.firstname, u.lastname, u.email 
           FROM request r, user u 
           WHERE r.pending = 1 AND r.uid = u.userid";
           
   $result = $db->query($sql);
   $array = $result->fetchAll(PDO::FETCH_ASSOC);
      
   if(!empty($array)){
      $USERS_SCIENTIST_LIST = $array;
   }
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Devices</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit" style="font-family: 'Myriad Pro', 'Gill Sans', 'Gill Sans MT', Calibri, sans-serif;">
        <h2>Admin control panel</h2>
        <form id="allowAccessForm" action="" method="post">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Surname</th>
                  <th>E-mail</th>
                  <th>Reason</th>
                  <th>Telephone</th>
                  <th>Allow</th>
                </tr>
              </thead>
              <tbody id="content">
              <?php
                
                if(!empty($USERS_SCIENTIST_LIST)){
              
                    $i=1;
                    foreach($USERS_SCIENTIST_LIST as $user){
                       echo '<tr>';
                       echo '<td>'. $i .'</td>';
                       echo '<td>'. $user['firstname'] .'</td>'; 
                       echo '<td>'. $user['lastname'] .'</td>'; 
                       echo '<td>'. $user['email'] .'</td>'; 
                       echo '<td>'. $user['reason'] .'</td>'; 
                       echo '<td>'. $user['telephone'] .'</td>'; 
                       echo '<td><button type="submit" class="btn btn-warning btnAllowAccess" value="'. $user['hash'] .'">Give access</button></td>'; 
                       echo '</tr>';
                       $i++;
                    }
                   
                }else{
                    echo "<tr><td>No pending requests.</td></tr>";
                }
              ?>
              </tbody>
            </table>
        </form>
    <hr>

 <?php
 
//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>
<script src="js/admin.js"></script>