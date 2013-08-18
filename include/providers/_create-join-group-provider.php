<?php
include_once("./config.php");
include_once("./include/functions/dbconnect.php");
    
$groupname = trim($_REQUEST["group_name"]);
$grouppwd = trim($_REQUEST["group_password"]);
    
// if user wants to create a group    
if(isset($_REQUEST['createGroup'])){
    
   // the user wants to create a group, check if the group name is already given
    $sql_check = "SELECT * 
                  FROM ".$CONFIG['DB_TABLE']['RGROUP']. " 
                  WHERE name='".$groupname."'";
                  
    $check_result = $db->query($sql_check);
    $check_row = $check_result->fetch();
    
    if(!empty($check_row)){
        // group-name is already given
        $jcstatsus = 2;
        die($jcstatsus.""); 
    }else{
        // update the databases
        $members = json_encode(array(intval($_SESSION['USER_ID'])));
        $sql_newgroup = "INSERT INTO ".$CONFIG['DB_TABLE']['RGROUP']." 
                        (name, password, members) 
                        VALUES 
                        ('". $groupname ."', '". $grouppwd . "', '" . $members . "')";
        
        $sql_update2 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." 
                        SET rgroup='".$groupname."' 
                        WHERE userid=".$_SESSION['USER_ID'];
                        
        $db->exec($sql_newgroup);
        $db->exec($sql_update2);
        
        // set group name in session
        $_SESSION['RGROUP'] = $groupname;
        
        $jcstatsus = 3;
        die($jcstatsus."");
    }
}

// if user wants to join existing group    
if(isset($_REQUEST['joinGroup'])){
    
    // the user wants to join a group
    // check if the user has provided a valid name of the group and password
    $sql_join = "SELECT * 
                 FROM ".$CONFIG['DB_TABLE']['RGROUP']. " 
                 WHERE name='".$groupname."' AND password='".$grouppwd."'";
                 
    $rgroup_result = $db->query($sql_join);
    $rgroup_row = $rgroup_result->fetch();
    
    if(!empty($rgroup_row)){
       
        $jcstatsus = 1; // the user has provided valid rgroup-name and password
        
        // update the tables
        $sql_update1 = "UPDATE ".$CONFIG['DB_TABLE']['USER']." 
                        SET rgroup='".$groupname."' 
                        WHERE userid=".$_SESSION['USER_ID'];
       
        $db->exec($sql_update1);
        
        $sql_members = "SELECT members 
                        FROM ".$CONFIG['DB_TABLE']['RGROUP']." 
                        WHERE name='".$groupname."'";
                        
        $members_result = $db->query($sql_members); 
        $members_row = $members_result->fetch();
        
        $members = json_decode($members_row['members']);
        $members[] = intval($_SESSION['USER_ID']);
        $members = array_unique($members);
        
        sort($members);
        
        $members = json_encode($members);
        
        $sql_update3 = "UPDATE ".$CONFIG['DB_TABLE']['RGROUP']." 
                        SET members='".$members."' 
                        WHERE name='".$groupname."'";
        
        $db->exec($sql_update3);
        
        // set group name in session
        $_SESSION['RGROUP'] = $groupname;
        
        die($jcstatsus."");
    }else{
        $jcstatsus = 4; // that group name doesn't exist
        die($jcstatsus."");
    } 
}
?>