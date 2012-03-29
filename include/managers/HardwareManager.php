<?php
    
class HardwareManager{
 
    public function __construct(){
                                     
    }
    
    /**
    * Checks if user has some device with device id given
    * and returns this entry, null if nothing found
    * 
    * @param int $userID
    * @param string $deviceID
    */
    public static function selectDeviceForUser($db, $hardwareTable, $userID, $deviceID){
      
       $sql = "SELECT * 
               FROM ". $hardwareTable ." 
               WHERE uid = ". $userID. " AND deviceid = '". $deviceID ."'";

       $result = $db->query($sql);
       
       $row = $result->fetch();
       
       return $row;
    }
    
    /**
    * Updates device info
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $modelname
    * @param mixed $vendorname
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function updateDevice($db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID){
      
       $sql = "UPDATE ". $hardwareTable ." 
               SET androidversion = '". $aVersion ."', 
                   sensors = '". $sensors ."' , 
                   modelname = '".$modelname."' , 
                   vendorname = '".$vendorname."' 
               WHERE uid = ". $userID . " AND deviceid = '". $deviceID ."'";
                        
       $db->exec($sql);
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $logger
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $modelname
    * @param mixed $vendorname
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function updateDeviceLogger($logger, $db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID){
           
       $sql = "UPDATE ". $hardwareTable ." 
               SET androidversion = '". $aVersion ."', 
                   sensors = '". $sensors ."' , 
                   modelname = '".$modelname."' , 
                   vendorname = '".$vendorname."' 
               WHERE uid = ". $userID . " AND deviceid = '". $deviceID ."'";
                        
       $db->exec($sql);
    }
    
    /**
    * Adds c2dm for the specified device
    * @param mixed $sensors
    * @param mixed $userID
    * @param mixed $deviceID
    * @param string $csmd google id of the device
    */
    public static function addc2dm($db, $hardwareTable, $userID, $deviceID, $c2dm){
           
       $sql = "UPDATE ". $hardwareTable ."
               SET c2dm = '". $c2dm ."'
               WHERE uid = ". $userID . " AND deviceid = '". $deviceID ."'";      
                        
       $db->exec($sql);
    }
    
    /**
    * Inserts a new entry for a device
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $modelname
    * @param mixed $vendorname
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function insertDevice($db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID){
      
      $sql = "INSERT INTO ". $hardwareTable ." 
                (uid, deviceid, modelname, vendorname, androidversion, sensors) 
                VALUES 
                (". $userID .", '". $deviceID . "' , '".$modelname."' , '".$vendorname."' , '". $aVersion ."', '". $sensors ."')";

      $db->exec($sql); 
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $logger
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $modelname
    * @param mixed $vendorname
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function insertDeviceLogger($logger, $db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID){
      
      $sql = "INSERT INTO ". $hardwareTable ." 
                (uid, deviceid, modelname, vendorname, androidversion, sensors) 
                VALUES 
                (". $userID .", '". $deviceID . "' , '".$modelname."' , '".$vendorname."' , '". $aVersion ."', '". $sensors ."')";
      
      $logger->logInfo($sql);

      $db->exec($sql); 
    }
    
    /**
    * Sets a filter for APK filter later on
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $filter
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function setFilter($db, $hardwareTable, $filter, $userID, $deviceID){
        
        $sql = "UPDATE ". $hardwareTable ."
                SET filter = '". $filter ."' 
                WHERE uid = ". $userID. " AND deviceid = '". $deviceID ."'";      
                
       //$logger->logInfo("##################### SETTING FILTER ################".$sql); // LOG THE QUERY 
                        
       $res = $db->exec($sql);
        
       return $res;
    }
    
    /**
    * Returns a filter from user
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function getFilter($db, $hardwareTable, $userID, $deviceID){
        
        $sql = "SELECT filter 
                FROM ". $hardwareTable ." 
                WHERE uid = ". $userID ." AND deviceid = '". $deviceID ."'";
                    
       $result = $db->query($sql);
       $row = $result->fetch();
       
       if(!empty($row)){
           return $row;
       }
       
       return null;
    }
    
    /**
    * Returns an android version for given user and device id
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function getAndroidVersion($db, $hardwareTable, $userID, $deviceID){
        
        $sql = "SELECT androidversion 
                FROM ". $hardwareTable ." 
                WHERE uid = ". $userID ." AND deviceid = '". $deviceID ."'";
                    
       $result = $db->query($sql);
       $row = $result->fetch();
       
       if(!empty($row)){
           return $row['androidversion'];
       }
       
       return null;
    }
    
    /**
    * Returns the hardwareID
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function getHardwareID($db, $hardwareTable, $userID, $deviceID){
        
        $sql = "SELECT hwid 
                FROM ". $hardwareTable ." 
                WHERE uid = ". $userID ." AND deviceid = '". $deviceID ."'";
                    
       $result = $db->query($sql);
       $row = $result->fetch();
       
       if(!empty($row)){
           return $row['hwid'];
       }
       
       return -1;
    }
    
    /**
    * Changes the deviceID of a device
    *     
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $userID
    * @param mixed $deviceID
    * @param mixed $newDeviceID
    */
    public static function changeDeviceID($db, $hardwareTable, $userID, $deviceID, $newDeviceID){
        $sql = "UPDATE ". $hardwareTable ." SET deviceid='". $newDeviceID ."' 
                WHERE uid = ". $userID. " AND deviceid = '". $deviceID ."'";
        $db->exec($sql);
    }
    
                                                
    /**
    * Returns an array of all hardware-ids that can install an apk
    * with required android version and required sensors
    * 
    * @param mixed $db the database
    * @param String $hardwareTable the name of the hardware table to search in
    * @param String $androidVersion the lowest android version required by the apk
    */
    public static function getCandidatesForAndroid($db, $hardwareTable, $androidVersion){
        
        // get for android version
        $sql = "SELECT hwid, filter
                FROM " .$hardwareTable. " 
                WHERE androidversion >=".$androidVersion;
        
        $result = $db->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        
        return $rows;
    }
    
    /**
    * Just sorts sensors in asc order
    * and removes duplicates
    * 
    * @param string $sensors
    * @return string
    */
    public static function sortSensors($sensors){
        
        $array = array_unique(json_decode($sensors));
        sort($array);
        $result = json_encode($array);
        
        return $result;
    }
}
    
?>