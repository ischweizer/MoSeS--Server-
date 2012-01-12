<?php
session_start();

/**
* Accept only form data with "HTTP_JSON" veriable 
*/
if(isset($_POST['HTTP_JSON'])){
        
    $session_interval = 200; // duration of a session in seconds
    
    $json = stripslashes($_POST['HTTP_JSON']);
    $data = json_decode($json);
    $IS_VALID_JSON = (is_object($data)) ? true : false;    
    
    if($IS_VALID_JSON){
        
        include_once('./config.php');
        include_once(MOSES_HOME . '/include/managers/LoginManager.php'); 
        include_once(MOSES_HOME . '/include/managers/DBManager.php');
        include_once(MOSES_HOME . '/include/functions/logger.php');
        
        $SENSORS = json_encode($data->SENSORS);
        $data->SENSORS = $SENSORS;
        
        //$logger->logInfo('Sensors: ' . print_r($data, true));
        
        /**
        *  Here will be selected which MESSAGE type was sent
        *  from Android client to this script  
        */
        $return = array();
        $DBManager = null;
        switch($data->MESSAGE){
        
            case "LOGIN_REQUEST":
            
                $DBManager = new DBManager();
                $DBManager->connect($CONFIG['DB']['HOST'], $CONFIG['DB']['DBNAME'], $CONFIG['DB']['USER'], $CONFIG['DB']['PASSWORD']);
                
                $LoginManager = new LoginManager($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $CONFIG['DB_TABLE']['USER']);
                $sid = $LoginManager->loginUser($data->LOGIN, $data->PASSWORD);
                
                if($sid != null){
                    $return = array("MESSAGE" => "LOGIN_RESPONSE",
                                    "LOGIN" => $data->LOGIN,
                                    "SESSIONID" => $sid);
                }else{
                    $return = array("MESSAGE" => "LOGIN_RESPONSE",
                                     "SESSIONID" => "NULL");
                }
                    
                print(json_encode($return));
                
                break;
            
            case "LOGOUT_REQUEST": 
                          
                $DBManager = new DBManager();
                $DBManager->connect($CONFIG['DB']['HOST'], $CONFIG['DB']['DBNAME'], $CONFIG['DB']['USER'], $CONFIG['DB']['PASSWORD']);
                
                $LoginManager = new LoginManager($DBManager->getDB(), $CONFIG['DB_TABLE']['ANDROID_SESSION'], $CONFIG['DB_TABLE']['USER']);
                $result = $LoginManager->logoutUser($data->SESSIONID);
                
                if($result === false){
                    $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                                    "STATUS" => "FAILURE");
                }else{
                    $return = array("MESSAGE" => "LOGOUT_RESPONSE",
                                    "STATUS" => "SUCCESS");
                }
                
                print(json_encode($return));
                
                break;
                
            case "SET_HARDWARE_PARAMS":
                    
                    include_once("./include/events/set_hardware_params.php.inc");
                    
                    
                    

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
        
        $DBManager = null;
        $LoginManager = null;
        
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