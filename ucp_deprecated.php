<?php
session_start();
ob_start();

if(!isset($_SESSION['USER_LOGGED_IN']))
    header("Location: /moses/");
    
include_once("./include/functions/func.php");
include_once("./include/_header.php");
include_once("./config.php");

$apk_listing = '';  // just init
$groupname = NULL; // name of the group the user is in OR name of the group the user wants to join
$grouppwd = NULL; // password of the group the user wants to join
$groupsize = 0; // size of the group
$group_members_count = 0;
$group_device_count = 0;


/*
* join/create status
* ON JOIN:
* 0: invalid group-name/password
* 1: valid group-name and password
* ON CREATE
* 2: group-name already given
* 3: group-name not already given
* 
*/
$jcstatsus = 0;
$SHOW_UPDATE_PAGE = 0;

/* show_add_quest will be true only if the user want to add questionnaires to an user study */
$show_add_quest = false;
/* show_us_quest will be true only if the user want see chosen questionnaires and their results for an user study */
$show_us_quest = false;

$apk_to_update = array();
$all_devices = array(); 

$scientist_succses = 0; // 1 only if the user has gain instant scientist credentials, use to check if someone is trying something nasty

$sensors_ultrasmall_mapping = array(1 => array('accelerometer_sensor.png', 'Accelerometer sensor'),
                                    array('magnetic_field_sensor.png', 'Magnetic field sensor'),
                                    array('orientation_sensor.png', 'Orientation sensor'),
                                    array('gyroscope_sensor.png', 'Gyroscope sensor'),
                                    array('light_sensor.png', 'Light sensor'),
                                    array('pressure_sensor.png', 'Pressure sensor'),
                                    array('temp_sensor.png', 'Temperature sensor'),
                                    array('proximity_sensor.png', 'Proximity sensor'),
                                    array('gravity_sensor.png', 'Gravity sensor'),
                                    array('linear_acceleration_sensor.png', 'Linear acceleration sensor'),
                                    array('rotation_sensor.png', 'Rotation sensor'),
                                    array('humidity_sensor.png', 'Humidity sensor'),
                                    array('ambient_temp_sensor.png', 'Ambient temperature sensor'));
                                    
$sensors_info = array(array('accelerometer', 'accelerometer_pressed', 'Accelerometer sensor'),
                    array('magnetic_field', 'magnetic_field_pressed', 'Magnetic field sensor'),
                    array('orientation', 'orientation_pressed', 'Orientation sensor'),
                    array('gyroscope', 'gyroscope_pressed', 'Gyroscope sensor'),
                    array('light', 'light_pressed', 'Light sensor'),
                    array('pressure', 'pressure_pressed', 'Pressure sensor'),
                    array('temperature', 'temperature_pressed', 'Temperature sensor'),
                    array('proximity', 'proximity_pressed', 'Proximity sensor'),
                    array('gravity', 'gravity_pressed', 'Gravity sensor'),
                    array('linear_acceleration', 'linear_acceleration_pressed', 'Linear acceleration sensor'),
                    array('rotation', 'rotation_pressed', 'Rotation sensor'),
                    array('humidity', 'humidity_pressed', 'Humidity sensor'),
                    array('ambient_temperature', 'ambient_temperature_pressed', 'Ambient temperature sensor'));
                    
$API_VERSION = array(array(8, 'API 8: "Froyo" 2.2.x'),
                     array(9, 'API 9: "Gingerbread" 2.3.0 - 2.3.2'),
                     array(10, 'API 10: "Gingerbread" 2.3.3 - 2.3.7'),
                     array(11, 'API 11: "Honeycomb" 3.0'),
                     array(12, 'API 12: "Honeycomb" 3.1'),
                     array(13, 'API 13: "Honeycomb" 3.2.x'),
                     array(14, 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2'),
                     array(15, 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4'));
                                  
// SWITCH USER CONTORL PANEL MODE
if(isset($_GET['m']))
{
    
    $RAW_MODE = strtoupper(trim($_GET['m']));
    $MODE = '';
    
    switch($RAW_MODE)
    {

        case 'ADDQUEST';
            // user want to add  questionnaires to a user study
            if(isset($_GET['id']))
            {
                $apkid = preg_replace("/\D/", "", $_GET['id']);
                $show_add_quest = true;
                include_once("./include/functions/dbconnect.php");
                $sql = "SELECT apktitle FROM apk WHERE apkid = ".$apkid;
                $req = $db->query($sql);
                $row = $req->fetch();
                $apkname = $row['apktitle'];
                include_once("./include/managers/QuestionnaireManager.php");
                $notchosen_quests = QuestionnaireManager::getNotChosenQuestionnireForApkid(
                    $db,
                    $CONFIG['DB_TABLE']['QUEST'],
                    $CONFIG['DB_TABLE']['APK_QUEST'],
                    $apkid);
                $chosen_quests = QuestionnaireManager::getChosenQuestionnireForApkid(
                    $db,
                    $CONFIG['DB_TABLE']['QUEST'],
                    $CONFIG['DB_TABLE']['APK_QUEST'],
                    $apkid);
            }
            break;

         case 'USQUEST';
            // user want to see the result of the questionnaires to a user study
            if(isset($_GET['id']))
            {
                $apkid = preg_replace("/\D/", "", $_GET['id']);
                $show_us_quest = true;
                include_once("./include/functions/dbconnect.php");
                $sql = "SELECT apktitle FROM apk WHERE apkid = ".$apkid;
                $req = $db->query($sql);
                $row = $req->fetch();
                $apkname = $row['apktitle'];
                include_once("./include/managers/QuestionnaireManager.php");
                $notchosen_quests = QuestionnaireManager::getNotChosenQuestionnireForApkid(
                    $db,
                    $CONFIG['DB_TABLE']['QUEST'],
                    $CONFIG['DB_TABLE']['APK_QUEST'],
                    $apkid);
                $chosen_quests = QuestionnaireManager::getChosenQuestionnireForApkid(
                    $db,
                    $CONFIG['DB_TABLE']['QUEST'],
                    $CONFIG['DB_TABLE']['APK_QUEST'],
                    $apkid);
            }
            break;
                   
        case 'PROMO':
                    $MODE = 'PROMO';
                    
                    if(isset($_POST['promo_sent']) && trim($_POST['promo_sent']) == "1")
                    {
                        include_once("./include/functions/dbconnect.php");
                        $RAW_TELEPHONE = $_POST['telephone'];
                        $RAW_REASON = $_POST['reason'];
                        $TELEPHONE  = trim($RAW_TELEPHONE);
                        $REASON  = trim($RAW_REASON);
                        $sql = "SELECT accepted, pending 
                                FROM request 
                                WHERE uid = ". $_SESSION['USER_ID'];
                        $result = $db->query($sql);
                        $row = $result->fetch();    
      
                        // user has sent scientist request
                        if(!empty($row))
                        {
                            if($row['pending'] == 1){
                                $USER_PENDING = 1;  
                            }else{
                                if($row['accepted'] == 1)
                                    $USER_PENDING = 0;
                                    $USER_ALREADY_ACCEPTED = 1;  
                            }
                        }
                        else
                        {
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
                    

        case 'INSTANT':
            $MODE ='INSTANT';
            $gr_sql = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $_SESSION['USER_ID'];
            include_once("./include/functions/dbconnect.php");
            $gr_result = $db->query($gr_sql);
            $gr_row = $gr_result->fetch();
            if(!empty($gr_row) && $gr_row['rgroup']!=NULL){
                $grname = $gr_row['rgroup'];
                
                // #### USER IS A MEMBER OF A GROUP###//
                // determine number of devices and scientists
                $nDevices = 0;
                $mem_sql = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='" .$grname."'";
                $mem_result = $db->query($mem_sql);
                $mem_row = $mem_result->fetch();
                $mem = json_decode($mem_row['members']);
                // determine number of scientists
                $nScientists = 0;
                foreach($mem as $id){
                    $mbr_sql = "SELECT usergroupid FROM ".$CONFIG['DB_TABLE']['USER']." WHERE userid=".$id;
                    $mbr_result = $db->query($mbr_sql);
                    $mbr_row = $mbr_result->fetch();
                    if(!empty($mbr_row))
                        if($mbr_row['usergroupid']>=2)
                            $nScientists++;
                    // determine how many devices the user has
                    $dev_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['HARDWARE']." WHERE uid=".$id;
                    $dev_result = $db->query($dev_sql);
                    $dev_rows = $dev_result->fetchAll(PDO::FETCH_ASSOC);
                    $nDevices+=count($dev_rows);
                }
                // HERE GROUP
                $control = $nDevices - $nScientists * $CONFIG['MISC']['SC_TRESHOLD'];
                if($control >= $CONFIG['MISC']['SC_TRESHOLD']){
                    $sql_sci = "UPDATE ".$CONFIG['DB_TABLE']['USER']. " SET usergroupid=2 WHERE userid=".$_SESSION['USER_ID'];
                    $db->exec($sql_sci);                    
                    $scientist_succses = 1;
                    $_SESSION["GROUP_ID"]=2;
                }
            }
            break;
        
        default: 
                $MODE = 'NONE';
    }
}else{

   
}

?>
  <link rel="stylesheet" type="text/css" href="style/style.css" />
  <script src="js/jquery.js"></script>
<title>Hauptseite von MoSeS - User control panel</title>

<?php  
  include_once("./include/_menu.php");
?>  

<div  id="menu_vertical">  
    <ul><?php
        
        if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES")
        {
          ?>  
          
          <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'admin'){
                echo " id=\"current_page_menu\"";
            } ?>
          ><a href="ucp.php?m=admin" title="Admin">ADMIN PANEL</a></li>
           
            
          <?php
        }
    
        ?>
        <li<?php 
            if(!isset($_GET['m'])){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php" title="My Devices">My Devices</a></li>
        <?php
         if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>0)
         {
             
            ?>
            <li<?php 
                if(isset($_GET['m'])&& $_GET['m'] == 'group')
                {
                    echo " id=\"current_page_menu\"";
                } ?>><a href="ucp.php?m=group" title="My Group">My Group</a></li>
            
            <?php
         }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]>1)
        {
            ?>
            <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'list'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=list" title="Show my App">My User Studies</a></li>
            <li<?php 
            if(isset($_GET['m'])&& $_GET['m'] == 'upload'){
                echo " id=\"current_page_menu\"";
            } ?>><a href="ucp.php?m=upload" title="User Study create">Create a User Study</a></li>
            </ul>
        <?php
        }
        if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]<2)
        {
            /*
            * Offer an instant upgrade to scientist account if the user is a member of a group and
            * #unique-devices-in-group - #scientist-in-group*5 >= 5
            */
            // determine if the user is a member of a group
            $gr_sql = "SELECT rgroup FROM ".$CONFIG['DB_TABLE']['USER']. " WHERE userid=" . $_SESSION['USER_ID'];
            include_once("./include/functions/dbconnect.php");
            $gr_result = $db->query($gr_sql);
            $gr_row = $gr_result->fetch();
            if(!empty($gr_row) && $gr_row['rgroup']!=NULL)
            {
                $grname = $gr_row['rgroup'];
                // #### USER IS A MEMBER OF A GROUP###//
                // determine number of unique devices and scientists
                $nDevices = 0;
                $all_devices = array();
                $unique_array = array();
                $mem_sql = "SELECT members FROM ".$CONFIG['DB_TABLE']['RGROUP']. " WHERE name='" .$grname."'";
                $mem_result = $db->query($mem_sql);
                $mem_row = $mem_result->fetch();
                $mem = json_decode($mem_row['members']);
                // determine number of scientists
                $nScientists = 0;
                foreach($mem as $id){
                    $mbr_sql = "SELECT usergroupid FROM ".$CONFIG['DB_TABLE']['USER']." WHERE userid=".$id;
                    $mbr_result = $db->query($mbr_sql);
                    $mbr_row = $mbr_result->fetch();
                    if(!empty($mbr_row))
                        if($mbr_row['usergroupid']>=2)
                            $nScientists++;
                    // get all devices the user has
                    $dev_sql = "SELECT * FROM ".$CONFIG['DB_TABLE']['HARDWARE']." WHERE uid=".$id;
                    $dev_result = $db->query($dev_sql);
                    $dev_rows = $dev_result->fetchAll(PDO::FETCH_ASSOC);
                    foreach($dev_rows as $row)
                        $all_devices[] = $row;
                }
                // check for unique devices
                for($h = 0 ; $h < count($all_devices) ; $h++)
                {
                    if(($all_devices[$h]['uniqueid'] != NULL) && !in_array($all_devices[$h]['uniqueid'],$unique_array))
                    {
                        $unique_array[] = $all_devices[$h]['uniqueid'];
                        $nDevices++;
                    }
                }
                // The rule to get scientist credentials
                $control = $nDevices - ($nScientists * $CONFIG['MISC']['SC_TRESHOLD']);
                if($control >= $CONFIG['MISC']['SC_TRESHOLD'])
                {
?>
                  <li><a href="ucp.php?m=instant">Get scientist credentials today!</a></li>
<?php
                }
                else
                {
?>
                    <li><a href="ucp.php?m=promo">Request scientist account</a></li>
<?php
                }  
            } // end of if(!empty($gr_row) && $gr_row['rgroup']!=NULL)
            else
            {
?>
                <li><a href="ucp.php?m=promo">Request scientist account</a></li>
<?php
            }
        } // end of if(isset($_SESSION["GROUP_ID"]) && $_SESSION["GROUP_ID"]<2)
?>
    </ul>
</div>

<div id="page">
        <div id="page-bgtop">
            <div id="page-bgbtm">
                <div id="page_content">
                    <div class="post">
                        <div class="entry">
                           
                
                        <?php

                            
                            /********************************************
                            ************* SEE QUESTs FOR US ***************
                            *********************************************/
                            if($show_us_quest == true)
                            {
                                
                                
                                  // check if there is any questionnaire are chosen for this US
                                  if(empty($chosen_quests))
                                  {
                                      // no questionnaire found
?>
                                      <h4>There is no questionnaire been added to <?php echo $apkname; ?>.</h4><br>
<?php
                                  }
                                  else
                                  {
?>
                                    <fieldset>
                                        <legend><h3><em><b>The result of chosen questionnaires for this user study: <?php echo $apkname; ?></b></em></h3></legend>
                                        <div id="quests_list">
                                            <ul>
                                                <script>
                                                
                                                       /*
                                                       * to switch the class name of the parent of this element between clicked and notclicked
                                                       */
                                                    function changeParentClass(element)
                                                    {
                                                        if(element.parentNode.className=='clicked')
                                                        {
                                                                element.parentNode.className='notclicked';
                                                           }
                                                           else
                                                           {
                                                               element.parentNode.className='clicked';    
                                                           }
                                                       }
                                                       
                                                      /*
                                                       * to switch the image
                                                       */
                                                       function changeimage(parent)
                                                       {    
                                                           for (var i = 0; i < parent.childNodes.length; i++)
                                                            {
    
                                                              var child = parent.childNodes[i];
    
                                                                  if(child.className=='collapsed')
                                                                {
                                                                        child.className='expanded';
                                                                        
                                                                   }
                                                                   else if(child.className=='expanded')
                                                                {
                                                                        child.className='collapsed';
                                                                        
                                                                   }
                                                            }    
                                                               
                                                            /* alert(imgID);        
                                                            var id=imgID/10;
                                                    
                                                               if(document.getElementById(id) != null)
                                                               {
                                                                   if(document.getElementById(id).className == "collapsed")
                                                                   {
                                                                       document.getElementById(id).className = "expanded";
                                                                   }
                                                                   else
                                                                   {
                                                                       document.getElementById(id).className = "collapsed";
                                                                   }
                                                               }
                                                               else
                                                               {
                                                                   //alert(id);
                                                               }*/
                                                       }
                                                       
                                                   /*
                                                       * to switch the class name of this element between clicked and notclicked
                                                       */
                                                    function changeChildrenClass(element, id, imgID)
                                                    {
                                                        var changeImg = false;
                                                          if(imgID == 0)
                                                          {
                                                            changeimage(element);
                                                          }
                                                          else
                                                        {
                                                              changeImg = true;
                                                        }
                                                            
                                                        var parent = element.parentNode;
                                                         
                                                        for (var i = 0; i < parent.childNodes.length; i++)
                                                        {

                                                          var child = parent.childNodes[i];

                                                          if(changeImg && child.id == imgID)
                                                          {
                                                              changeimage(child);
                                                          }

                                                          if (child.id == id)
                                                          {
                                                              if(child.className=='notclicked')
                                                            {
                                                                    child.className='clicked';
                                                                    
                                                               }
                                                               else
                                                               {
                                                                   child.className='notclicked';
                                                               }    
                                                          }
                                                          else if(child.id > id && child.id < (id + 1))
                                                          {
                                                              child.className='notclicked';
                                                          }
                                                        }
                                                        
                                                       }
                                                  
                                                </script>
<?php                                      





                                                // loop for each chosen questionnaire
                                                foreach($chosen_quests as $quest)
                                                {

                                                      // max number of answers
                                                      $maxAnswer = 0;
?>
                                                      <li>
                                                        <p onclick="changeParentClass(this);" id="<?php echo $quest['name']; ?>">
                                                            <b>Name: </b>
                                                             <a href="./CSVs/<?php echo $quest['name'].'_'.$apkname; ?>.csv" title="Download as CSV" class="bt_downloadCSV"></a>    
<?php                                                        
                                                            echo $quest['name'];
?>
                                                        </p>

<?php
                                                        include_once("./include/managers/QuestionnaireManager.php");
                                                        // get all questions in this questionnaire
                                                        $qust = QuestionnaireManager::getQuestionsForQuestid(
                                                            $db,
                                                            $CONFIG['DB_TABLE']['QUESTION'],
                                                            $quest['questid']);
                                                        // the content of csv as string. Write the head of the table
                                                        $csvContent = "#;Question;Question ID;Type of Question;User ID;Answer;Answer ID\n";
?>
                                                          <table class="questTable">
                                                              <!-- the header of the table -->
                                                            <thead>
                                                            
                                                                <th>   </th>
                                                                <!-- Column 1 -->
                                                                <th>#</th>
                                                                <!-- Column 2 -->
                                                                  <th>Question</th>
                                                                  <!-- Column 3 -->
                                                                  <th>Qst.ID</th>
                                                                  <!-- Column 4 -->
                                                                 <th>Type of Question</th>
                                                                 <!-- Column 5 -->
                                                                  <th>User ID</th>
                                                                  <!-- Column 6 -->
                                                                <th>Answer</th>
                                                                <!-- Column 7 -->
                                                                <th>Ans.ID</th>
                                                                <!-- end of the header of the table -->
                                                            </thead>
                                                            
                                                            <!-- the body of the table -->
                                                            <tbody>
<?php                         
                                                                  // $i represents the index of a question
                                                                  $i = 1;
                                                                  // loop for each question in this questionnaire
                                                                  foreach($qust as $q)
                                                                  {
                                                                    include_once("./include/managers/QuestionnaireManager.php");
                                                                    // get all answers of this question
                                                                    $answers = QuestionnaireManager::getAnswersForQidAndApkid(
                                                                      $db,
                                                                      $CONFIG['DB_TABLE']['ANSWER'],
                                                                      $q['qid'],
                                                                      $apkid);
?>
                                                                    <tr  title="one click to collapse/expand more information"> 
                                                                        <!-- changing of the image collapse/expand -->
                                                                        
                                                                        <td id="<?php echo $i/10; ?>" class="collapsed" />
                                                                        
                                                                    
                                                                        <!-- make a new row for this question -->
                                                                          <td><?php echo $i; ?></td>
<?php
                                                                        if($q['type'] == 1) // multiple choices
                                                                          {
?>
                                                                            <td><?php echo trim(substr($q['content'],0,strrpos($q['content'],"["))); ?></td>
                                                                            <td><?php echo $q['qid']; ?></td>
                                                                            <td>Multiple Choices</td>
<?php                               
                                                                            // adding index, question, question id and its type
                                                                            $csvContent.=
                                                                                $i
                                                                                .";".trim(substr($q['content'],0,strrpos($q['content'],"[")))
                                                                                .";".$q['qid'].
                                                                                ";Multiple Choices;";
                                                                          }
                                                                          elseif($q['type'] == 2) // single choice
                                                                          {
?>
                                                                            <td><?php echo trim(substr($q['content'],0,strrpos($q['content'],"["))); ?></td>
                                                                            <td><?php echo $q['qid']; ?></td>
                                                                            <td>Single Choice</td>
<?php                               
                                                                              // adding index, question, question id and its type
                                                                            $csvContent.=
                                                                                $i
                                                                                .";".trim(substr($q['content'],0,strrpos($q['content'],"[")))
                                                                                .";".$q['qid']
                                                                                .";Single Choice;";
                                                                          }
                                                                          else // open question
                                                                          {
?>
                                                                            <td><?php echo trim($q['content']); ?></td>
                                                                            <td><?php echo $q['qid']; ?></td>
                                                                            <td>Open Question</td>
<?php                               
                                                                              // adding index, question, question id and its type
                                                                            $csvContent.=
                                                                                $i
                                                                                .";".trim($q['content'])
                                                                                .";".$q['qid']
                                                                                .";Open Question;";
                                                                          }
                                                                          $csvString = "";

                                                                          // number of answers for this question
                                                                          $numOfAnswer = 0;

                                                                          // to calculate the average answer
                                                                          $answers_array = array();
                                                                          
                                                                          // to pair each answer as a key and how many times as a value
                                                                          $answers_counter = array();
                                                                          
                                                                          // to pair each answer as a key with its html code as a value
                                                                          $answers_rows = array();

                                                                          // to pair each answer as a key with its table row id as a value
                                                                          $answers_itr = array();

                                                                          // to create id for each table row
                                                                          $iTr = 0; 
                                                                          // loop for each answer of this question
                                                                          foreach($answers as $ans)
                                                                          {
                                                                              // for each answer found for this question
                                                                              $numOfAnswer++;

                                                                              // check if this answer comes early
                                                                              if($answers_counter[$ans['content']] == 0)
                                                                              {
                                                                                  // increase id for tr
                                                                                  $iTr++;

                                                                                  // the complate id for a table row
                                                                                  $onclick_id = $i.".".$iTr;

                                                                                  // put a new iTr key to these answers
                                                                                  $answers_itr[$ans['content']] = $iTr;

                                                                                  // and make a row for this answer
                                                                                // make a new row and skip the first 4 fields (index, question, question id, type)
                                                                                  $answers_rows[$ans['content']] =
                                                                                      '</tr>
                                                                                      <tr title="one click to collapse/expand more information" id="'.$onclick_id.'" class="notclicked"'
                                                                                      //.' onclick="changeChildrenClass(this,'.$onclick_id.', '.$i.');">'
                                                                                      .'<td/><td/><td/><td/><td/>'
                                                                                      ."<td>".$ans['userid']."</td>"
                                                                                    ."<td>".$ans['content']."</td>"
                                                                                    ."<td>".$ans['aid']."</td>";
                                                                              }
                                                                              else
                                                                              {
                                                                                  // the complate id for a table row
                                                                                  $onclick_id = $i.".".$answers_itr[$ans['content']];
                                                                                  
                                                                                  // make a new row and skip the first 4 fields (index, question, question id, type)
                                                                                  $answers_rows[$ans['content']].=
                                                                                      '</tr>
                                                                                      <tr title="one click to collapse/expand more information" id="'.$onclick_id.'" class="notclicked"'
                                                                                      //.' onclick="changeChildrenClass(this,'.$onclick_id.', '.$i.');">'
                                                                                      .'<td/><td/><td/><td/><td/>'
                                                                                      ."<td>".$ans['userid']."</td>"
                                                                                    ."<td>".$ans['content']."</td>"
                                                                                    ."<td>".$ans['aid']."</td>";
                                                                              }

                                                                              // add this answer in the array of answers
                                                                            $answers_array[] = $ans['content'];
                                                                            
                                                                            // incremtent the number of users who answered with this answer
                                                                            $answers_counter[$ans['content']]++;
                                                                            
                                                                            // csv content
                                                                            $csvString.="\n;;;;".$ans['userid'].";".$ans['content'].";".$ans['aid'];

                                                                          } // end of foreach($answers as $ans)
                                                                          
                                                                          $sortedAnswers = $q['sortedAnswers'];
                                                                          $average = NULL;
                                                                          $polpular = NULL;

                                                                          // get the average/popular answer of this question
                                                                          include_once("./include/managers/QuestionnaireManager.php");
                                                                          
                                                                          if($sortedAnswers != NULL && $sortedAnswers == 1)
                                                                          {     
                                                                              // Get the sorted answers
                                                                              $sortedAnswersArray = json_decode(trim(substr($q['content'],(strrpos($q['content'],"[")-1),(strrpos($q['content'],"]")+1))));
                                                                              $average = QuestionnaireManager::getAverageAnswerOfArray($answers_array,$sortedAnswersArray);
                                                                              // set the average answer of this question in html
                                                                              $trWithaverage = "<td>average</td><td>";
                                                                              $trWithaverage .=$average."</td><td></td>";
                                                                              // as well in csv
                                                                              $csvContent .= "average;".$average.";".$csvString."\n";    
                                                                          }
                                                                          else
                                                                          {
                                                                              $popular = QuestionnaireManager::getPopularAnswerOfArray($answers_array);
                                                                              // set the polpular answer of this question in html
                                                                              $trWithaverage = "<td>popular</td><td>";
                                                                              $trWithaverage .=$popular."</td><td></td>";
                                                                              // as well in csv
                                                                              $csvContent .= "popular;".$popular.";".$csvString."\n";
                                                                          }

                                                                          // get all strings of table rows of these answers
                                                                          foreach ($answers_rows as $key => $value)
                                                                          {
                                                                              $onclick_id = $i.".".$answers_itr[$key];
                                                                              $answers_rows[$key] = 
                                                                                      '</tr>
                                                                                      <tr title="one click to collapse/expand more information" id="'.$i.'" class="notclicked"'
                                                                                      .' onclick="changeChildrenClass(this,'.$onclick_id.',0);">'
                                                                                      .'<td id="0.'.$i.'" class = "collapsed" /><td/><td/><td/><td/>'
                                                                                      ."<td>"
                                                                                      . $answers_counter[$key]
                                                                                      ." users</td>"
                                                                                    ."<td>".$key."</td>"
                                                                                    ."<td></td>"
                                                                                    .$value;
                                                                          
                                                                              $trWithaverage.=$answers_rows[$key];
                                                                              $iTr++;
                                                                          }
                                                                          // finaly print the html code
                                                                          echo $trWithaverage;


                                                                        // set max number of answers
                                                                        if($maxAnswer < $numOfAnswer)
                                                                        {
                                                                            $maxAnswer = $numOfAnswer;
                                                                        }

                                                                        // increment the index of this table
                                                                          $i++;
?>
                                                                        </tr>
<?php
                                                                  } // end of foreach($qust as $q)
?>
                                                            <!-- end of the body of the table -->
                                                            </tbody>
                                                        </table>
                                                        There are <?php echo $maxAnswer; ?> users answer this questionnaire.
<?php
                                                        // make a csv file for this questionnaire
                                                        
                                                        $csvFilePath = './CSVs/'.$quest['name'].'_'.$apkname.'.csv';
                                                        $csvF = fopen($csvFilePath, 'w');
                                                        fwrite($csvF, $csvContent);
                                                        fclose($csvF);
?>
                                                        </li>
<?php
                                                     } // end of foreach($chosen_quests as $quest)
?>
                                            </ul>
                                        </div>
                                    </fieldset>
<?php
                                  } // end of else : if(empty($chosen_quests))
?>

<?php
                            } // end of if($show_us_quest == true)

                            /********************************************
                            ******* SCIENTIST CREDINTIAL REQUEST ********
                            *********************************************/
                            if(
                                $MODE == 'PROMO' && !isset($_POST['promo_sent'])
                                && (!isset($USER_ALREADY_ACCEPTED) || !isset($USER_PENDING)))
                            {    
?> 
                                <h3>Application for scientist credentials</h3>
                                <form action="ucp.php?m=promo" method="post" class="promo_form">
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
                                </form>   
<?php   
                            }

                            if($MODE == 'PROMO' && isset($_POST['promo_sent']))
                            {
?>
                                <div class="promo_sent_text">
                                    <p>Your scientist application was sent. Thank you for interesting in that!</p>
                                </div>
<?php
                            }
                            
                            if(
                                $MODE == 'PROMO' && isset($USER_PENDING) && $USER_PENDING == 1
                                && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED != 1)
                            {
?>
                                
                                <div class="promo_sent_text">
                                    <p>Your application to become a scientist was already sent to us.</p>
                                </div>
<?php
                            }
                            
                            if($MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 1)
                            {
?>
                                <div class="promo_sent_text">
                                    <p>You are already a scientist!</p>
                                </div>
<?php
                            }
                            
                            // nobody wants you as scientist
                            if(
                                $MODE == 'PROMO' && isset($USER_ALREADY_ACCEPTED) && $USER_ALREADY_ACCEPTED == 0 
                                && isset($USER_PENDING) && $USER_PENDING == 0)
                            {
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

  ob_end_flush();  
?>