<?php
  
/**
* Checks if string is a valid md5 hash
* 
* @param string $md5
* @return boolean
*/
function is_md5($md5){
return (bool)preg_match("/[0-9a-f]{32}/i", $md5);
}

/**
* Checks for empty dir
* 
* @param string $dir
*/
function is_empty_dir($dir){
    $files = array();
    if($handle = opendir($dir)){
        while(false !== ($file = readdir($handle))){
            if($file != "." && $file != ".."){
                $files[] = $file;
            }
        }
        closedir($handle);
    }
    return (count($files) > 0) ? FALSE : TRUE;
}

/**
* Consumes an ordinal of the sensor and returns its name
* 
* @param mixed $sensor_ordinal
*/
function get_sensor_name($sensor_ordinal){
    $result;
    
    switch($sensor_ordinael){
        case 1 : $result = "Accelerometer sensor"; break;
        case 2 : $result = "Magnetic field sensor"; break;
        case 3 : $result = "Orientation sensor"; break;
        case 4 : $result = "Gyroscope"; break;
        case 5 : $result = "Light sensor"; break;
        case 6 : $result = "Preassure sensor"; break;
        case 7 : $result = "Temperature sensor"; break;
        case 8 : $result = "Proximity sensor"; break;
        case 9 : $result = "Gravity sensor"; break;
        case 10 : $result = "Linear acceleration sensor"; break;
        case 11 : $result = "Rotation sensor"; break;
        case 12 : $result = "Humidity sensor"; break;
        case 13 : $result = "Ambient temperature sensor"; break;
        default : $result = "Unknown sensor"; break;
    }
    
    return $result;
}

/**
* Compares two arrays to match content
* 
* @param mixed $filter_array
* @param mixed $apk_sensors_array
*/
function isFilterMatch($filter_array, $apk_sensors_array){
      $all_in = true;
  
      foreach($apk_sensors_array as $req){
         $all_in = $all_in && in_array($req, $filter_array);
      }
      
      return $all_in;
}
  
?>