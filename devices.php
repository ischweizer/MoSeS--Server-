<?php
//Starting the session
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    
include_once("./include/functions/func.php");
include_once("./config.php");

$API_VERSION = array(array(8, 'API 8: "Froyo" 2.2.x'),
                     array(9, 'API 9: "Gingerbread" 2.3.0 - 2.3.2'),
                     array(10, 'API 10: "Gingerbread" 2.3.3 - 2.3.7'),
                     array(11, 'API 11: "Honeycomb" 3.0'),
                     array(12, 'API 12: "Honeycomb" 3.1'),
                     array(13, 'API 13: "Honeycomb" 3.2.x'),
                     array(14, 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2'),
                     array(15, 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4'));

/**
* Select all user devices
*/

include_once("./include/functions/dbconnect.php");

$USER_DEVICES = array();

$sql = 'SELECT * 
       FROM hardware 
       WHERE uid = '. $_SESSION['USER_ID'];
                               
$result = $db->query($sql);
$devices = $result->fetchAll(PDO::FETCH_ASSOC);
  
if(!empty($devices)){
  $USER_DEVICES = $devices;
}


//Import of the header  
include_once("./include/_header.php");                   
?>
  
<title>Hauptseite von MoSeS - Devices</title>

<?php  //Import of the menu
include_once("./include/_menu.php");

?>

    <!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>Devices</h2>
        <div class="pagination pagination-centered">
        <ul>
            <li class=""><a href="#">&laquo;</a></li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">&raquo;</a></li>
        </ul>
        </div>
        <div id="content"> 
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Model</th>
                  <th>API</th>
                  <th>Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Mark</td>
                  <td>Otto</td>
                  <td>@mdo</td>
                  <td>X</td>
                </tr>
              </tbody>
            </table>
        </div>
        <div id="page-selection">Pagination goes here</div>
    </div>
    <!-- / Main Block -->
    
    <hr>

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