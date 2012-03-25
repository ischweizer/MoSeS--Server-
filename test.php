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
        include_once(MOSES_HOME . '/include/managers/DBManager.php');
        include_once(MOSES_HOME . '/include/managers/ApkManager.php');
        include_once(MOSES_HOME . '/include/managers/LoginManager.php'); 
        include_once(MOSES_HOME . '/include/managers/HardwareManager.php'); 
        include_once(MOSES_HOME . '/include/functions/func.php');
        include_once(MOSES_HOME . '/include/functions/klogger.php');
    
        $logger = new KLogger(MOSES_HOME . "/log", KLogger::INFO);
        
        $logger->logInfo("###################### JSON OBJECT ARRIVED #########################");
        
        if($data->SENSORS != null){
            $SENSORS = json_encode($data->SENSORS);
            $data->SENSORS = $SENSORS;  
        }
        
        
        /**
        *  Here will be selected which MESSAGE type was sent
        *  from Android client to this script  
        */
        $return = array();
        $DBManager = null;
        
        $DBManager = new DBManager();
        $DBManager->connect($CONFIG['DB']['HOST'], $CONFIG['DB']['DBNAME'], $CONFIG['DB']['USER'], $CONFIG['DB']['PASSWORD']);
        
        if($data->MESSAGE != null){
        
            switch($data->MESSAGE){
            
                case "LOGIN_REQUEST":
                
                    include_once(MOSES_HOME . "/include/events/login_request.php.inc");
                    break;
                
                case "LOGOUT_REQUEST": 
                    
                    include_once(MOSES_HOME . "/include/events/logout_request.php.inc");
                    break;
                    
                case "SET_HARDWARE_PARAMS":
                        
                    include_once(MOSES_HOME . "/include/events/set_hardware_params.php.inc");
                    break;
                        
                case "GET_HARDWARE_PARAMS":
                
                    include_once(MOSES_HOME . "/include/events/get_hardware_params.php.inc");
                    break;
                        
                case "SET_FILTER":
                        
                    include_once(MOSES_HOME . "/include/events/set_filter.php.inc");                    
                    break;
                    
                case "GET_FILTER":

                    include_once(MOSES_HOME . "/include/events/get_filter.php.inc");
                    break;
                        
                case "STILL_ALIVE":
                
                    include_once(MOSES_HOME . "/include/events/still_alive.php.inc");
                    break;
                    
                case "GET_APK_LIST_REQUEST":
                
                    include_once(MOSES_HOME . "/include/events/get_apk_list_request.php.inc");
                    break;     
                    
                case "GET_APK_INFO":
                
                    include_once(MOSES_HOME . "/include/events/get_apk_info.php.inc");
                    break;
                    
                case "DOWNLOAD_REQUEST":
                
                    include_once(MOSES_HOME . "/include/events/download_request.php.inc");
                    break;
                
                case "APK_INSTALLED":
                
                    include_once(MOSES_HOME . "/include/events/apk_installed.php.inc");
                    break;
                
                case "C2DM":
                
                    include_once(MOSES_HOME . "/include/events/c2dm.php.inc");
                    break;
                    
                default:
                    echo "Only specific messages are accepted.";
                    break;
            }
        }else{
            echo "MESSAGE string was null";
        }
        
    }else{
        echo "Sorry, but your data ain't valid json instance";
    }          
/**
*  if no POST var HTTP_JSON was sent       
*/
}else{  
    echo "You didn't sent us JSON.";
}    
?>