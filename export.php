<?php

//Starting the session
session_start();

if(!isset($_SESSION['USER_LOGGED_IN']) || !isset($_SESSION['GROUP_ID']) || $_SESSION['GROUP_ID'] <= 1){
    header("Location: " . dirname($_SERVER['PHP_SELF'])."/");
    exit;
}

if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) &&
   isset($_GET['m']) && !empty($_GET['m'])){

    include_once("./include/functions/func.php");
    include_once("./config.php");
    include_once("./include/functions/dbconnect.php");

    $id = $_GET['id'];
    $mode = $_GET['m'];
    
    if($mode === 'csv'){
        
        $sql = "SELECT * 
               FROM ". $CONFIG['DB_TABLE']['STUDY_RESULT'] ." 
               WHERE survey_id = ". $id;
                
        $result = $db->query($sql);
        $survey_results = $result->fetchAll(PDO::FETCH_ASSOC);

        // if there are some results
        if(!empty($survey_results)){
            $results_array = array();
            
            
            
            download_send_headers("survey_results_" . date("d.m.Y") . ".csv");
            echo survey2csv($results_array);   
        }else{
            echo 'No results.';
        }
    }
    
    die();
   }
?>
