<?php
session_start();
  
include_once("./include/_header.php");                 

if(isset($_POST["submit"]) && $_POST["submit"] == "1"){    
  
  if(isset($_POST["login"]) && !empty($_POST["login"]) && preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $_POST["login"])){
      
      if(isset($_POST["password"]) && !empty($_POST["password"])){
        
          include_once("./config.php");
          include_once("./include/functions/dbconnect.php");   
          
          $USER_LOGIN =  $_POST["login"];
          $USER_PASSWORD =  $_POST["password"];
          
          $sql = "SELECT * 
                  FROM ". $CONFIG['DB_TABLE']['USER'] ." 
                  WHERE login = '". $USER_LOGIN ."' 
                  AND password = '". $USER_PASSWORD ."'";
          
          $result = $db->query($sql);
          $row = $result->fetch();    
          
          if(!empty($row)){
                    
              if($row["confirmed"] == 1){
              
                  $USER_CONFIRMED = 1;
                  $_SESSION["USER_LOGGED_IN"] = 1;    
                  
                  $_SESSION["USER_ID"] =   $row["userid"];
                  $_SESSION["GROUP_ID"] =  $row["usergroupid"];
                  $_SESSION["LOGIN"] =     $row["login"];    
                  $_SESSION["PASSWORD"] =  $row["password"];
                  $_SESSION["FIRSTNAME"] = $row["firstname"];
                  $_SESSION["LASTNAME"] =  $row["lastname"];
                  
                  // we have an admin here logged in
                  if($row["usergroupid"] == 3){
                      $_SESSION["ADMIN_ACCOUNT"] =  "YES";    
                  }
                  
              }else{
                  $USER_CONFIRMED = 0;
              }
              
          echo "<meta http-equiv='refresh' content='0;URL=./'>";
              
          }else{
              $LOGIN_FAIL = 1;
              echo "<meta http-equiv='refresh' content='3;URL=./'>";
          }
      }else{
          $LOGIN_FAIL = 1;
          echo "<meta http-equiv='refresh' content='3;URL=./'>";
      }
  }else{
      $LOGIN_FAIL = 1;
      echo "<meta http-equiv='refresh' content='3;URL=./'>";
  }
}
  
?>
  
<title>Hauptseite von MoSeS - Home</title>

<?php  
include_once("./include/_menu.php");

?>
  
<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>
<!-- end #header --> 

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="page_content">
                    <div class="post"><?php
                       if(isset($LOGIN_FAIL) && $LOGIN_FAIL == 1){
                     ?>
                       <h3>You provided wrong data for login or password!</h3>
                     <?php
                       }else{
                     ?>
                        <h2 class="title">Welcome Friend!</h2>
                        <div class="entry">
                            
                            <p>MoSeS helps scientists from all around the world to distribute their Android apps and make the world a better place.</p>
                            <p>Every person with an Android device can contribute.<br />Feel free to register and download moses client, it's easy!</p>
                            <p>Be adwised: this site is under construction!</p>
                            
                        </div>
                        <div style="clear: both;">&nbsp;</div>
                        <p class="meta">Posted by Admin on February 12, 2012</p>
                        <?php
                       }
                        ?>
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

<?php
include_once("./include/_login_slider.php");

include_once("./include/_footer.php");  
?>