<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");   
    
include_once("./config.php");
include_once("./include/functions/func.php");
include_once("./include/functions/dbconnect.php");
                     
// obtain the name of group the user is currently in (if any)
$group_sql = "SELECT rgroup 
              FROM ".$CONFIG['DB_TABLE']['USER']. " 
              WHERE userid=" . $_SESSION['USER_ID'];
              
$group_result = $db->query($group_sql);
$group_row = $group_result->fetch();
$groupname = $group_row['rgroup'];

$GROUP_MEMBERS = array();

if(!empty($group_row) && $groupname!=NULL){
    
    $sql = "SELECT members 
            FROM ". $CONFIG['DB_TABLE']['RGROUP'] ." 
            WHERE name = '". $groupname ."'";
            
     $result = $db->query($sql);
     $row = $result->fetch();
     
     $GROUP_MEMBERS = $row;
     
     $group_members_count = count(json_decode($row['members']));
     
     $group_members_array = json_decode($row['members']);
     if($group_members_array != NULL){
         foreach($group_members_array as $user){
             
             $sql = 'SELECT hwid 
                     FROM '. $CONFIG['DB_TABLE']['HARDWARE'] .' 
                     WHERE uid = '. $user;
                     
             $result = $db->query($sql);
             $row = $result->fetchAll();
             
             if(!empty($row)){
                 $group_device_count += count($row);
             }
         }
     }
}

//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Devices</title>

<?php  //Import of the menu
include_once("./include/_menu.php");


print_r($group_members_array);
echo "<br>-----------------------";
print_r($GROUP_MEMBERS);
?>

    <!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>You're member of group: <?php echo $groupname; ?></h2>
            <h4>This group has <?php echo ($group_members_count > 1 ? $group_members_count.' members' : '1 member (you)') ?></h4>
            <div class="accordion" id="accordionFather">
              <div class="accordion-group">
                <div class="accordion-heading">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionFather" href="#collapseOne">
                    <?php
                       echo "name"; 
                    ?>
                  </a>
                </div>
                <div id="collapseOne" class="accordion-body collapse in">
                  <div class="accordion-inner">
                    
                  </div>
                </div>
              </div>
              
              <div class="accordion-group">
                <div class="accordion-heading">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionFather" href="#collapseTwo">
                    Collapsible Group Item #2
                  </a>
                </div>
                <div id="collapseTwo" class="accordion-body collapse">
                  <div class="accordion-inner">
                    Anim pariatur cliche...
                  </div>
                </div>
              </div>
            </div>
    </div>
    <!-- / Main Block -->
    
    <hr>

 <?php

//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");

?>