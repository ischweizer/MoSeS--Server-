<?php
//Starting the session
session_start();
//Import of the header  
include_once("./include/_header.php");                 

//If the formular is sent
if(isset($_POST["submit"]) && $_POST["submit"] == "1"){    
  //If the login exists 
  if(isset($_POST["login"]) && !empty($_POST["login"]) && preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $_POST["login"])){
      //And the password exists
      if(isset($_POST["password"]) && !empty($_POST["password"])){
        
		  //Import of the connections file to database
          include_once("./config.php");
          include_once("./include/functions/dbconnect.php");   
          
          $USER_LOGIN =  $_POST["login"];
          $USER_PASSWORD =  $_POST["password"];
          
		  //Select the user
          $sql = "SELECT * 
                  FROM ". $CONFIG['DB_TABLE']['USER'] ." 
                  WHERE login = '". $USER_LOGIN ."' 
                  AND password = '". $USER_PASSWORD ."'";
          
          $result = $db->query($sql);
          $row = $result->fetch();    
          
		  //users exist in database
          if(!empty($row)){
		  
               //test if the user is registered
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
               
              echo "<meta http-equiv='refresh' content='0;URL=./'>";   
              
              }else{
                  $USER_CONFIRMED = 0;
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
  }else{
      $LOGIN_FAIL = 1;
      echo "<meta http-equiv='refresh' content='3;URL=./'>";
  }
}
  
?>
  
<title>Hauptseite von MoSeS - Home</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>
  
<!--<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>--> 

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
                           if(isset($USER_CONFIRMED) && $USER_CONFIRMED == 0){
                               ?>
                                <h3>You have not confirmed your registration.</h3>
                                <p>Please, check your mailbox and confirm the registration.</p>
                               <?php  
                           }else{
                         ?>
                            <h2 class="title">Welcome Friend!</h2>
                            <div class="entry">
                                
                                <p>MoSeS helps scientists from all around the world to distribute their Android apps and make the world a better place.</p>
                                <p>Every person with an Android device can contribute.<br />Feel free to register and download moses client, it's easy!</p>
                                <p>Be adwised: this site is under construction!</p>
                                
                            </div>
							<h2 class="title">About MoSeS</h2>
							<div class="entry">
                            <p>MoSeS offers researchers a platform for distribution of non-comercial Android apps, that are used for research purposes.</p>
                            <br />
                            <p>Project MoSeS is developed at <a href="http://www.tk.informatik.tu-darmstadt.de/" title="Telecooperation Group" target="_blank">Telecooperation Group</a> by <li><a href="https://github.com/maksuti" target="_blank">Zijad Maksuti</a>, <li><a href="https://github.com/wlsch" target="_blank">Wladimir Schmidt</a>, 
                            <li><a href="https://github.com/simlei" target="_blank">Simon Leisching</a>, <li><a href="https://github.com/jahofmann" target="_blank">Jaco Hofmann</a>, <li><a href="https://github.com/scalaina" target="_blank">Sandra Christina Amend</a>, <li><a href="https://github.com/alyahya" target="_blank">Ibrahim Alyahya</a>, 
                            <li><a href="https://github.com/fahouma" target="_blank">Fehmi Belhadj</a> and <li><a href="https://github.com/FSchnell" target="_blank">Florian Schnell</a> 
							<br><br>under supervision of <a href="http://www.tk.informatik.tu-darmstadt.de/?id=1699" title="Immanuel Schweizer" target="_blank">Immanuel Schweizer</a>.</p>
							</div>
							
							
							
							<h2 >How to contribute?</h2>
							
                            <p>1. <a href="./registration.php">Register</a>.</p>
                            <p>2. <a href="./download.php">Download</a> and install the client.</p>
                            <p>3. That's it!</p>
							
                        
                       
                    
                          
                            <p class="meta">Posted by Admin on September 2012</p>
                            <?php
                           }
                       }
                        ?>
                    </div>
                   
                </div>
                
            </div>
			
        </div>
		
    </div>
    <!-- end #page -->
</div>

<?php
//Import of the slider
include_once("./include/_login_slider.php");
//Import of the footer
include_once("./include/_footer.php");  
?>