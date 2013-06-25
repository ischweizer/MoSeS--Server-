<?php
//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    if(!(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"))
        header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    
include_once("./include/functions/func.php");
include_once("./config.php");

if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
                       
   $MODE = 'ADMIN';
   
   include_once("./include/functions/dbconnect.php");
   
   // TODO: make this ajax!!
   if(isset($_POST['pending_requests']) && is_array($_POST['pending_requests']) && count($_POST['pending_requests']) > 0){
                    
      foreach($_POST['pending_requests'] as $request){
          
          $sql = "UPDATE request
                    SET
                    pending = 0, accepted = 1 
                    WHERE
                    uid = (SELECT userid 
                            FROM user
                            WHERE hash = '". $request ."')";
                                
           $db->exec($sql);
           
           // USER IS NOW IN A SCIENTIST GROUP
           $sql = "UPDATE user SET usergroupid= 2 WHERE hash = '". $request ."'";
           
           $db->exec($sql);
          
      }                  
      
      echo "<meta http-equiv='refresh' content='0;URL=". $_SERVER['HTTP_REFERER'] ."'>";     
       
   }
   
   $USERS_SCIENTIST_LIST = array();
   
   $sql = "SELECT r.telephone, r.reason, u.hash, u.usergroupid, u.firstname, u.lastname 
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
        <h2>Admin</h2>
        <table>
          <tr><th>Users that wanting permission to be a scientiest:</th></tr>
          <?php
            
            if(!empty($USERS_SCIENTIST_LIST)){
          
                foreach($USERS_SCIENTIST_LIST as $user){
                    
                ?>
                    <tr><td><?php echo $user['firstname'] ." ". $user['lastname']; ?></td><td>Accept:<input type="checkbox" name="pending_requests[]" value="<?php echo $user['hash']; ?> " /></td></tr>        
                <?php
                }
             ?>
             
             <tr><td>&nbsp;</td><td><button class="btn btn-success">Give access</button></td></tr>
             
             <?php   
                
            }else{
                echo "<tr><td>No requests.</td></tr>";
            }
          ?>
          </table>
    <hr>

 <?php
 
//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>