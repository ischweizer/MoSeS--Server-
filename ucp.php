<?php
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: /moses/");
    //die('Only registered users may access that file!');
    
include_once("./include/functions/func.php");
include_once("./include/_top.php");
include_once("./include/_header.php");

$apk_listing = '';  // just init

// SWITCH USER CONTORL PANEL MODE
if(isset($_GET['m'])){
    
    $RAW_MODE = strtoupper(trim($_GET['m']));
    $MODE = '';
    
    switch($RAW_MODE){
        case 'UPLOAD':
                       $MODE = 'UPLOAD';
                       
                       if(isset($_GET['res'])){
                        
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
                       
        case 'PROFILE':
                       $MODE = 'PROFILE'; 
                       break;
        
        default: 
                $MODE = 'NONE';
    }
}

?>
  
<title>Hauptseite von MoSeS - User control panel</title>

<?php  
  include_once("./include/_menu.php");
  
 
?>  

<div class="user_menu">  
    <ul>
        <li><a href="ucp.php">User page</a></li>
        <li><a href="ucp.php?m=upload">APK Upload</a></li>
        <li></li>
        <li><a href="ucp.php?m=list">Show all APKs</a></li>
        <li></li>
        <li><a href="ucp.php?m=promo">Scientist account request</a></li>
    </ul>
</div>

<div class="heading_text">User control panel</div>

<div class="clear"></div>

<div class="main_container_text">

<?php 
    
    if($MODE == 'UPLOAD' && !isset($_GET['res'])){
?>

    <form action="upload.php" method="post" enctype="multipart/form-data" class="upload_form">
       <p>
          <label for="file">Select a file:</label> 
          <input type="file" name="userfile" id="file"><button>Upload</button>
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
       <p>
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
        <div class="list_apk">
         <table>
         <tr><th>Your uploaded files:</th></tr>
         <?php

          // we found some APKs
          if($LIST_APK == 1){
              foreach($apk_listing as $row){
                 $remove_url = '<a href="ucp.php?m=list&remove='. $row['apkhash'] .'">Remove</a>';
                 echo "<tr><td>". $row['apkname'] ."</td><td>". $remove_url ."</td></tr>"; 
              }
          }else{
              echo "<tr><td>Nothing found :(</td></tr>"; 
          }

         ?>
         </table>
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

<?php


?>

<?php  
  include_once("./include/_bottom.php");  
?>