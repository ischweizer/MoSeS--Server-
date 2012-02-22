<?php
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: /moses/");
    
include_once("./include/functions/func.php");
include_once("./include/_header.php");

$apk_listing = '';  // just init

// SWITCH USER CONTORL PANEL MODE
if(isset($_GET['m'])){
    
    $RAW_MODE = strtoupper(trim($_GET['m']));
    $MODE = '';
    
    switch($RAW_MODE){
        case 'UPLOAD':
        
                     $MODE = 'UPLOAD';
                       
                       if(isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                        
                           $RAW_UPLOAD_RESULT = strtoupper(trim($_GET['res']));
                           
                           switch($RAW_UPLOAD_RESULT){
                               case "1":
                                        $UPLOAD_RESULT = 1;  // file successfully uploaded
                                        break;
                                        
                               case "0":
                                        $UPLOAD_RESULT = 0;  // file failed to upload
                                        break;
                                        
                               case "2":
                                        $UPLOAD_RESULT = 2;  // filetype not allowed
                                        break;
                                        
                               case "3":
                                        $UPLOAD_RESULT = 3;  // file too large
                                        break;
                                        
                               case "4":
                                        $UPLOAD_RESULT = 4;  // no permissions to write into dir
                                        break;
                                        
                               default:
                                        $UPLOAD_RESULT = 999;  // someone is trying to hax?
                           }
                       }
                       
                       break; 
        
        case 'LIST':
            if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                   $MODE = 'LIST'; 
                   
                   include_once("./include/functions/dbconnect.php");
                   
                   if(isset($_GET['remove'])){
                    
                       $RAW_REMOVE_HASH = trim($_GET['remove']);
                       
                       if(is_md5($RAW_REMOVE_HASH)){
                           
                          $APK_REMOVED = 1;
                          $REMOVE_HASH = strtolower($RAW_REMOVE_HASH);
                           
                          // getting userhah for dir later
                          $sql = "SELECT userhash FROM apk 
                                                 WHERE userid = ". $_SESSION['USER_ID'] . " 
                                                   AND apkhash = '". $REMOVE_HASH ."'";
                          
                          $result = $db->query($sql);
                          $row = $result->fetch();
                          
                          if(!empty($row)){
                              $dir = './apk/' . $row['userhash'];
                              if(is_dir($dir)){
                                 if(file_exists($dir . '/'. $REMOVE_HASH . '.apk')){
                                     unlink($dir . '/' . $REMOVE_HASH . '.apk');
                                     
                                     if(is_empty_dir($dir)){
                                         rmdir($dir);
                                     }
                                 }
                              }
                          }
                           
                          // remove entry from DB 
                          $sql = "DELETE FROM apk 
                                         WHERE userid = ". $_SESSION['USER_ID'] . " 
                                           AND apkhash = '". $REMOVE_HASH ."'";
                          
                          $db->exec($sql);
                          
                          // remove file itself from the system
                          
                             
                       }else{
                           $APK_REMOVED = 0;
                       }  
                       
                   }

                   // select all entries for particular user
                   $sql = "SELECT * 
                            FROM apk 
                            WHERE userid = ". $_SESSION["USER_ID"];
                            
                   $result = $db->query($sql);
                   $apk_listing = $result->fetchAll();    
                                
                   if(!empty($apk_listing)){
                       $LIST_APK = 1;
                   }else{
                       $LIST_APK = 0;
                   }
            }   
                   
                   break;
                   
        case 'PROMO':
                    $MODE = 'PROMO';
                    
                    if(isset($_POST['promo_sent']) && trim($_POST['promo_sent']) == "1"){
                        
                        include_once("./include/functions/dbconnect.php");
                        
                        $RAW_TELEPHONE = $_POST['telephone'];
                        $RAW_REASON = $_POST['reason'];
                        
                        // TODO: Add some security later
                        
                        $TELEPHONE  = trim($RAW_TELEPHONE);
                        $REASON  = trim($RAW_REASON);
                        
                        $sql = "SELECT accepted, pending 
                                FROM request 
                                WHERE uid = ". $_SESSION['USER_ID'];
                                
                        $result = $db->query($sql);
                        $row = $result->fetch();    
      
                        // user has sent scientist request
                        if(!empty($row)){
                            if($row['pending'] == 1){
                                $USER_PENDING = 1;  
                            }else{
                                if($row['accepted'] == 1)
                                    $USER_PENDING = 0;
                                    $USER_ALREADY_ACCEPTED = 1;  
                            }
                        }else{
                            
                            // User hasn't sent us scientist request yet
                             $sql = "INSERT INTO request 
                                    (uid, telephone, reason) 
                                    VALUES 
                                    (". $_SESSION['USER_ID'] .", '". $TELEPHONE . "', '". $REASON ."')";
                    
                             $db->exec($sql);
                             
                             $USER_PENDING = 1;  
                        }
                    }else{
                        
                        include_once("./include/functions/dbconnect.php");
                        
                        $sql = "SELECT accepted, pending 
                                FROM request 
                                WHERE uid = ". $_SESSION['USER_ID'];
                                
                        $result = $db->query($sql);
                        $row = $result->fetch();
                        
                        if(!empty($row)){
                           if($row['pending'] == 1){
                                $USER_PENDING = 1;
                                
                                if($row['accepted'] == 0){
                                    $USER_ALREADY_ACCEPTED = 0;  
                                }else{
                                    $USER_ALREADY_ACCEPTED = 1;  
                                }
                            }else{
                                $USER_PENDING = 0;
                                
                                if($row['accepted'] == 1){
                                    $USER_ALREADY_ACCEPTED = 1;  
                                }else{
                                    $USER_ALREADY_ACCEPTED = 0;
                                }
                            }
                        }
                        
                    }
                    
                    break;
                       
        case 'ADMIN':  
                    if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
                       
                       $MODE = 'ADMIN';
                       
                       include_once("./include/functions/dbconnect.php");
                       
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
                               
                               // USER IS NOW IN SCIENTIST-GROUP
                               $sql = "UPDATE user SET usergroupid= 2 WHERE hash = '". $request ."'";
                               
                               $db->exec($sql);
                              
                          }     
                           
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
                       break;
        
        default: 
                $MODE = 'NONE';
    }
}else{

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
}

?>
  
<title>Hauptseite von MoSeS - User control panel</title>

<?php  
  include_once("./include/_menu.php");
?>  

<div id="header">
    <div id="logo">
        <h1><a href="./index.php">Mobile Sensing System</a></h1>
    </div>
</div>
<!-- <div id="splash">&nbsp;</div> -->
<!-- end #header -->

<div class="user_menu">  
    <ul><?php
        
        if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
          ?>  
          
          <li><a href="ucp.php?m=admin">ADMIN PANEL</a></li>
          <li>&nbsp;</li>  
            
          <?php
        }
    
        ?>
        <li><a href="ucp.php">Profile</a></li>
        <?php
         if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>1){
             
        ?>
        <li><a href="ucp.php?m=upload">APK Upload</a></li>
        <li>&nbsp;</li>
        <li><a href="ucp.php?m=list">Show all APKs</a></li>
        <li>&nbsp;</li>
        <?php
         }
         ?>
        <li><a href="ucp.php?m=promo">Request scientist account</a></li>
    </ul>
</div>

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="page_content">
                    <div class="post">
                        <h2 class="title">User control panel</h2>
                        <div class="entry">
                           
                        <?php 
                          if(isset($USER_DEVICES)){
                            if(!empty($USER_DEVICES)){
                              ?>
                                <div class="list_devices">
                                 <table>
                                 <tr><th>Your devices:</th><th>Android sdk:</th><th>Your filter:</th></tr>
                                 <?php

                                  // user has got some devices
                                  foreach($USER_DEVICES as $device){
                                     echo '<tr><td>'. $device['deviceid'] .'</td><td>'. $device['androidversion'] .'</td><td>'. $device['filter'] .'</td></tr>'; 
                                  }
                                  
                                 ?>
                                 </table>
                                </div>
                            <?php    
                            }else{
                                ?>
                                <div class="list_devices">
                                 <table>
                                 <tr><th>Devices:</th></tr>
                                 <tr><td>Your device list is empty.</td></tr> 
                                 </table>
                                </div> 
                                <?php
                            }
                          }
                            if($MODE == 'ADMIN' && !isset($_POST['pending_requests'])){
                            ?>
                            <div class="users_wanting_scientist">
                                <form action="ucp.php?m=admin" method="post">
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
                                         
                                         <tr><td>&nbsp;</td><td><button>Give access</button></td></tr>
                                         
                                         <?php   
                                            
                                        }else{
                                            echo "<tr><td>No requests.</td></tr>";
                                        }
                                      ?>
                                      </table>
                                 </form>
                            </div>
                            
                            <?php
                            }
                            
                            if($MODE == 'UPLOAD' && !isset($_GET['res']) && isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"] > 1){
                        ?>

                            <form action="upload.php" method="post" enctype="multipart/form-data" class="upload_form">
                              <p>Program name (title):</p>
                              <input type="text" name="apk_title" />
                              <p>Program android version:</p>
                              <input type="text" name="apk_android_version" />
                              <p>Program description:</p>
                              <textarea cols="30" rows="6" name="apk_description"></textarea>
                              <p>My program uses following sensors:</p>
                              <ul>
                                  <li><input type="checkbox" name="sensors[]" value="1" />Accelerometer</li>
                                  <li><input type="checkbox" name="sensors[]" value="2" />Magnetic field sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="3" />Orientation sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="4" />Gyroscope</li>
                                  <li><input type="checkbox" name="sensors[]" value="5" />Light sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="6" />Preassure sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="7" />Temperature sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="8" />Proximity sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="9" />Gravity sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="10" />Linear acceleration sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="11" />Rotation sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="12" />Humidity sensor</li>
                                  <li><input type="checkbox" name="sensors[]" value="13" />Ambient temperature sensor</li>
                              </ul>
                             
                             <script type="text/javascript">
                                $(document).ready(function(){
                                    
                                    $('.user_apk_restriction').find('input[name=number_restricted_users]').attr('maxlength', 6);
                                    
                                    $('.user_apk_restriction').find('input[name=restrict_users_number]').change(
                                        function() {
                                            if ($(this).is(':checked')) {
                                                $('.user_apk_restriction').find('input[name=number_restricted_users]').removeAttr('disabled');
                                            } else {
                                                $('.user_apk_restriction').find('input[name=number_restricted_users]').attr('disabled', true);
                                                $('.user_apk_restriction').find('input[name=number_restricted_users]').val('');
                                            }
                                    });   
                            
                                });
                                </script>
                              
                              <div class="user_apk_restriction">
                                  <input type="checkbox" name="restrict_users_number" value="1" />Restrict number of users
                                  <input type="text" name="number_restricted_users" disabled="disabled" />
                              </div>
                              
                              <label for="file">Select a file:</label> 
                              <input type="file" name="userfile" id="file">
                              <p>Click Upload button to upload your apk</p><button>Upload</button>
                            </form>
                        <?php
                            }
                            
                            // there WAS some upload
                            if($MODE == 'UPLOAD' && isset($_GET['res']) && !empty($_GET['res'])){
                                
                                switch($UPLOAD_RESULT){
                                    
                                    // successful upload
                                    case 1:
                                        ?>
                                        
                                        <div class="upload_successful">
                                            <p>Your file was successfully uploaded!</p>
                                        </div>

                                        <?php
                                        break;
                                        
                                    // failed upload.
                                    case 0:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>That filetype not allowed. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                    
                                    // failed upload. Filetype fail
                                    case 2:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>That filetype not allowed. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                        
                                    // failed upload. File is too large
                                    case 3:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>This file is too large. Sorry.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                       
                                    // failed upload. File is too large 
                                    case 4:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>We can't store that file, because of directory permissions. Please contact administrator.</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                        
                                    // failed upload. someone trying to do some dirty stuff
                                    default:
                                        ?>
                                        
                                        <div class="upload_failed">
                                            <p>Trying to hax, br0?</p>
                                        </div>
                                            
                                        <?php
                                        break;
                                }
                            }
                            
                            // user wants a listing of APK files
                            if($MODE == 'LIST' && isset($LIST_APK)){
                                
                            ?>
                                <script type="text/javascript">
                                $(document).ready(function(){

                                    $('div.slidepanel').hide();
                                    
                                    $('.slidebtn').click(function(e){

                                        $('div.slidepanel').stop(true, false).slideUp();
                                        $(e.target).closest('span').next('.slidepanel').stop(true, false).slideDown();
                                       /* $(this).find('#expose').siblings().each(function(){
                                            $(this).html('[+]');
                                        }); */
                                        //$(this).find('#expose').html('[-]');
                                        //$('.slidepanel').slideToggle('slow');
                                        //$(this).toggleClass('active'); return false;
                                    });
                                    
                                    /*     
                                    $('.slidebtn').toggle(function() {
                                        $(this).find('#expose').html('[-]');    
                                    }, function() {
                                        $(this).find('#expose').html('[+]');        
                                    });*/
                                    
                                  /*  $('#expose').each(function(){
                                        var value = $(this).html();
                                        $(this).html((value == '[+]') ? '[+]' : '[-]');
                                    });*/

                            
                                });
                                </script>
                                
                                <div class="apk_list">
                                 <p>Your uploaded files (click to expose):</p>
                                 <?php

                                  // we found some APKs
                                  if($LIST_APK == 1){
                                      foreach($apk_listing as $row){
                                         //echo '<tr><td><a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk">'. $row['apkname'] .'</a></td><td>'. $remove_url .'</td></tr>'; 
                                         echo '<span class="slide"><a href="#" title="More info" class="slidebtn">'. $row['apkname'] .' <span id="expose">[+]</span></a></span>';
                                         echo '<div class="slidepanel">';
                                         echo '<p>Information:</p>';
                                         echo '<p>'. $row['description'] .'</p>';
                                         echo '<p>Sensors:</p>';
                                         echo '<p>'. $row['sensors'] .'</p>';
                                         echo 'Download: <a href="./apk/'. $row['userhash'] .'/'. $row['apkhash'] .'.apk" title="Download apk">'. $row['apkname'] .'</a>&nbsp;&nbsp;&nbsp;<a href="ucp.php?m=list&remove='. $row['apkhash'] .'" title="Remove APK">Remove</a>';
                                         echo '</div>';
                                         echo '<br />';
                                      }   
                                  }else{
                                      echo "<span>Nothing found :(</span>"; 
                                  }

                                 ?>
                                </div>
                            <?php     
                            }
                            
                            if($MODE == 'PROMO' && !isset($_POST['promo_sent']) 
                                                && (!isset($USER_ALREADY_ACCEPTED) || !isset($USER_PENDING))){    
                            ?> 

                            <form action="ucp.php?m=promo" method="post" class="promo_form">
                               <p>
                                 <fieldset>
                                    <legend>Become a scientist!</legend>
                                    <label for="telephone" >Telephone:</label>
                                    <div class="clear"></div>
                                    <input type="text" name="telephone" id="telephone" maxlength="10" />
                                    <div class="clear"></div>
                                    <label for="reason" >Reason? Tell us why, pls (*):</label>
                                    <div class="clear"></div>
                                    <textarea cols="30" rows="10" name="reason" id="reason"></textarea>
                                    <div class="clear"></div>
                                    <input type="hidden" name="promo_sent" id="promo_sent" value="1" />
                                    <input type="submit" name="submit" value="Send" />
                                 </fieldset> 
                               <p>
                            </form>   
                                
                             <?php   
                            }
                            
                            if($MODE == 'PROMO' && isset($_POST['promo_sent'])){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>Your scientist application was sent. Thank you for interesting in that!</p>
                                </div>
                                
                                <?php
                            }
                            
                            if($MODE == 'PROMO' && isset($USER_PENDING) && $USER_PENDING == 1
                                                && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED != 1){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>Your application to become a scientist was already sent to us.</p>
                                </div>
                                
                                <?php
                            }
                            
                            if($MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 1){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>You are already a scientist!</p>
                                </div>
                                
                                <?php
                            }
                            
                            // nobody wants you as scientist
                            if($MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 0 
                                                && isset($USER_PENDING) && $USER_PENDING == 0){
                                ?>
                                
                                <div class="promo_sent_text">
                                    <p>Sorry, but admin won't you as scientist and rejected your application. :(</p>
                                </div>
                                
                                <?php
                            }
                            
                        ?>
                           
                        </div>
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