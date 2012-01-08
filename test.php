<?php
session_start();

$session_interval = 200; // duration of a session in seconds

/**
* Accept only form data with "HTTP_JSON" veriable 
*/
if(isset($_POST['HTTP_JSON'])){
    
    include_once("./include/functions/logger.php");
    
    $json = stripslashes($_POST['HTTP_JSON']);
    $data = json_decode($json);
    $IS_VALID_JSON = (is_object($data)) ? true : false;
    
    $logger->logInfo("IS_VALID_JSON: ". $IS_VALID_JSON);
    
    if($IS_VALID_JSON){
        
        $SENSORS = json_encode($data->SENSORS);
        $data->SENSORS = $SENSORS;
        
        $logger->logInfo('Sensors: ' . print_r($data, true));
        
        /**
        *  Here will be selected which MESSAGE type was sent
        *  from Android client to this script  
        */
        switch($data->MESSAGE){
        
            case "LOGIN_REQUEST":
            
                include_once("./include/events/login_request.php.inc");
                
                $db = null;
                break;
            
            case "LOGOUT_REQUEST": 
                          
                include_once("./include/events/logout_request.php.inc");
                
                $db = null;
                break;
                
            case "SET_HARDWARE_PARAMS":
                    
                    include_once("./include/events/set_hardware_params.php.inc");
                    
                    $db = null;
                    break;
                    
            case "GET_HARDWARE_PARAMS":
            
                    include_once("./include/events/get_hardware_params.php.inc");
                    
                    $db = null;
                    break;
                    
            case "SET_FILTER":
                    
                    include_once("./include/events/set_filter.php.inc");
                    
                    $db = null;
                    break;
                
            case "GET_FILTER":

                include_once("./include/events/get_filter.php.inc");
                
                $db = null;
                break;
                    
            case "STILL_ALIVE":
            
                include_once("./include/events/still_alive.php.inc");
            
                $db = null; 
                break;
                
            case "GET_APK_LIST_REQUEST":
            
                include_once("./include/events/get_apk_list_request.php.inc");
                
                $db = null; 
                break;
                
            case "DOWNLOAD_REQUEST":
            
                include_once("./include/events/download_request.php.inc");

                $db = null;
                break;
                
            default:
                    echo "Only specific messages are accepted.";
                    break;
        }
    }else{
        echo "Sorry, but your data ain't valid json instance";
    }          
/**
*  if no POST var HTTP_JSON was sent       
*/
}else{  
    echo "You didn't sent us HTTP_JSON post var.";
}
     
?>