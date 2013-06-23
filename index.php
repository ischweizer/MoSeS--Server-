<?php
//Starting the session
session_start();
//Import of the header  
include_once("./include/_header.php");                 

// //If the formular is sent
// if(isset($_POST["submit"]) && $_POST["submit"] == "1"){    
//   //If the login exists 
//   if(isset($_POST["login"]) && !empty($_POST["login"]) && preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $_POST["login"])){
//       //And the password exists
//       if(isset($_POST["password"]) && !empty($_POST["password"])){
// 		  //Import of the connection�s file to database
//           include_once("./config.php");
//           include_once("./include/functions/dbconnect.php");   
          
//           $USER_LOGIN =  $_POST["login"];
//           $USER_PASSWORD =  $_POST["password"];
          
// 		  //Select the user
//           $sql = "SELECT * 
//                   FROM ". $CONFIG['DB_TABLE']['USER'] ." 
//                   WHERE login = '". $USER_LOGIN ."' 
//                   AND password = '". $USER_PASSWORD ."'";
          
//           $result = $db->query($sql);
//           $row = $result->fetch();    
          
// 		  //users exist in database
//           if(!empty($row)){
		  
//                //test if the user is registered
//               if($row["confirmed"] == 1){
              
//                   $USER_CONFIRMED = 1;
//                   $_SESSION["USER_LOGGED_IN"] = 1;    
                  
//                   $_SESSION["USER_ID"] =   $row["userid"];
//                   $_SESSION["GROUP_ID"] =  $row["usergroupid"];
//                   $_SESSION["LOGIN"] =     $row["login"];    
//                   $_SESSION["PASSWORD"] =  $row["password"];
//                   $_SESSION["FIRSTNAME"] = $row["firstname"];
//                   $_SESSION["LASTNAME"] =  $row["lastname"];
                  
//                   // we have an admin here logged in
//                   if($row["usergroupid"] == 3){
//                       $_SESSION["ADMIN_ACCOUNT"] =  "YES";    
//                   }
               
//               echo "<meta http-equiv='refresh' content='0;URL=./'>";   
              
//               }else{
//                   $USER_CONFIRMED = 0;
//                   echo "<meta http-equiv='refresh' content='3;URL=./'>";
//               }
//           }else{
//               $LOGIN_FAIL = 1;
//               echo "<meta http-equiv='refresh' content='3;URL=./'>";
//           }
//       }else{
//           $LOGIN_FAIL = 1;
//           echo "<meta http-equiv='refresh' content='3;URL=./'>";
//       }
//   }else{
//       $LOGIN_FAIL = 1;
//       echo "<meta http-equiv='refresh' content='3;URL=./'>";
//   }
// }
  
?>
  
<title>Hauptseite von MoSeS - Home</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

<!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>MoSeS makes your life easier!</h2>
        <p>The Mobile Sensing System helps scientists from all around the world 
            to distribute their Android apps and make the world a better place.
        </p>
        <p>    
            Every person with an Android device can contribute.
            Feel free to register and download moses client, it's easy!
        </p>
        <p>&nbsp;
        </p>
        <p><a href="registration.php" class="btn btn-warning btn-large" style="font-weight: bold; width: 130px;"><i class="icon-white icon-tag"></i> Sign up</a></p>
    </div>
    <!-- / Main Block -->
    
    <hr>

    <!-- Example row of columns -->
   <!-- <div class="row-fluid">
    <div class="span4">
      <h2>Heading</h2>
      <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
      <p><a class="btn" href="#">View details &raquo;</a></p>
    </div>
    <div class="span4">
      <h2>Heading</h2>
      <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
      <p><a class="btn" href="#">View details &raquo;</a></p>
    </div>
    <div class="span4">
      <h2>Heading</h2>
      <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa.</p>
      <p><a class="btn" href="#">View details &raquo;</a></p>
    </div>
    </div>
-->

    <!--<button class="btn btn-link btn-large span4" style="background-color: #EEE; width: 500pt; height: 50pt;">Sign up <a href="#top" class="icon-chevron-up"></a></button>-->
    <div class="row">
      
      <div class="span8 text-center" style="width: 400pt; height: 50pt; background-color: #EEE; float: none; margin: 0 auto;">
        <div style="margin: 50pt 0pt;">
            <a href="registration.php" class="btn btn-link" style="color: #ffa338; font-weight: bold; font-size: 20pt; margin-left: 15pt; margin-top: 12pt;">Sign up</a>
            <a href="" id="scrollToTop"><i class="icon-chevron-up" style="float: right; margin-right: 15pt; margin-top: 20pt;"></i></a>
        </div>
      </div>
      <div class="span1"></div>
    </div>
    <!--</div>--> <!-- /container -->

 <?php
      /*
  ?>
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
                           }
						   else
						   if (isset($_GET['m']) && ($_GET['m']=='print'))
						   { 
						   ?><fieldset><legend><b>About </b></legend>  
						   		
						   		<p> 
						   		    Technische Universität Darmstadt<br>
									Telekooperation<br>
									Immanuel Schweizer <br>
									Hochschulstraße 10 <br>
									64289 Darmstadt <br>
									schweizer@tk.informatik.tu-darmstadt.de<br> 
									www.da-sense.de <br>
								</p>
								<br><p> Disclaimer</p> <br>

								<p>	<b>1.</b> Content 
									The author reserves the right not to be responsible for the topicality, correctness, completeness or quality of the information provided. Liability claims regarding damage caused by the use of any information provided, including any kind of information which is incomplete or incorrect,will therefore be rejected. 
									All offers are not-binding and without obligation. Parts of the pages or the complete publication including all offers and information might be extended, changed or partly or completely deleted by the author without separate announcement.</p> 
									<br>	
								<p>	<b>2.</b> Referrals and links 
									The author is not responsible for any contents linked or referred to from his pages - unless he has full knowledge of illegal contents and would be able to prevent the visitors of his site fromviewing those pages. If any damage occurs by the use of information presented there, only the author of the respective pages might be liable, not the one who has linked to these pages. Furthermore the author is not liable for any postings or messages published by users of discussion boards, guestbooks or mailinglists provided on his page.</p> 
									<br>
								<p>	<b>3.</b> Copyright 
									The author intended not to use any copyrighted material for the publication or, if not possible, to indicatethe copyright of the respective object. 
									The copyright for any material created by the author is reserved. Any duplication or use of objects such as diagrams, sounds or texts in other electronic or printed publications is not permitted without the author s agreement. </p>
									<br>	
								<p>	<b>4.</b> Privacy policy 
									If the opportunity for the input of personal or business data (email addresses, name, addresses) is given, the input of these data takes place voluntarily. The use and payment of all offered services are permitted - if and so far technically possible and reasonable - without specification of any personal data or under specification of anonymized data or an alias. The use of published postal addresses, telephone or fax numbers and email addresses for marketing purposes is prohibited, offenders sending unwanted spam messages will be punished.</p> 
									<br>
								<p>	<b>5.</b> Legal validity of this disclaimer 
									This disclaimer is to be regarded as part of the internet publication which you were referred from. If sections or individual terms of this statement are not legal or correct, the content or validity of the other parts remain uninfluenced by this fact.</p>
						   
						   
						   </fieldset>
						   <?
						
						   }
						   else
						   {
                         ?>
						 
                            <h2 class="title"><em>Welcome Friend!</em></h2>
                            <div class="entry">
                                
                                <p>MoSeS helps scientists from all around the world to distribute their Android apps and make the world a better place.</p>
                                <p>Every person with an Android device can contribute.<br />Feel free to register and download moses client, it's easy!</p>
                                <p>Be adwised: this site is under construction!</p>
                                
                            </div>
							
							<h2 ><em>How to contribute?</em></h2>
							
                            <p>1. <a href="./registration.php">Register</a>.</p>
                            <p>2. <a href="./download.php">Download</a> and install the client.</p>
                            <p>3. That's it!</p><br>
							
							
							<h2 class="title"><em>About MoSeS</em></h2>
							<div class="entry">
                            <p>MoSeS offers researchers a platform for distribution of non-comercial Android apps, that are used for research purposes.</p>
                            <br />
                            <p>Project MoSeS is developed at <a href="http://www.tk.informatik.tu-darmstadt.de/" title="Telecooperation Group" target="_blank">Telecooperation Group</a> by <li><a href="https://github.com/maksuti" target="_blank">Zijad Maksuti</a>, <li><a href="https://github.com/wlsch" target="_blank">Wladimir Schmidt</a>, 
                            <li><a href="https://github.com/simlei" target="_blank">Simon Leisching</a>, <li><a href="https://github.com/jahofmann" target="_blank">Jaco Hofmann</a>, <li><a href="https://github.com/scalaina" target="_blank">Sandra Christina Amend</a>, <li><a href="https://github.com/alyahya" target="_blank">Ibrahim Alyahya</a>, 
                            <li><a href="https://github.com/fahouma" target="_blank">Fehmi Belhadj</a> and <li><a href="https://github.com/FSchnell" target="_blank">Florian Schnell</a> 
							<br><br>under supervision of <a href="http://www.tk.informatik.tu-darmstadt.de/?id=1699" title="Immanuel Schweizer" target="_blank">Immanuel Schweizer</a>.</p>
							</div>
							
							
							
							
							
                        
                       
                    
                          
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
*/
//Import of the slider
include_once("./include/_login.php");
//Import of the footer
include_once("./include/_footer.php");  
?>