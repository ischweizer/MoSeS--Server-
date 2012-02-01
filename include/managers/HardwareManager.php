<?php
    
class HardwareManager{

    private $logger;
    
    public function __construct(){
                                     
    }
    
    /**
    * Checks if user has some device with device id given
    * and returns this entry, null if nothing found
    * 
    * @param int $userID
    * @param string $deviceID
    */
    public static function selectDeviceForUser(&$db, $hardwareTable, $userID, $deviceID){
      
       $sql = "SELECT * 
               FROM ". $hardwareTable ." 
               WHERE uid = ". $userID. " AND deviceid = '". $deviceID ."'";
               
       $result = $db->query($sql);
       
       //$this->logger->logInfo($sql); // LOG INFO
       
       $row = $result->fetch();
       
       //$this->logger->logInfo("ROW IS: ". !empty($row));
       
       if(!empty($row)){
           return $row;
       }
       
       return null;
    }
    
    /**
    * Updates existing device entry
    * 
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function updateDevice($db, $hardwareTable, $aVersion, $sensors, $userID, $deviceID){
      
       //$this->logger->logInfo("row update has to be commited");   
       //$this->logger->logInfo("##################### SETTING HARDWARE PARAMS ################ deviceid selected and uid jetzt sofort");
       //$this->logger->logInfo("UPDATE HARDWARE");
           
       $sql = "UPDATE ". $hardwareTable ."
                    SET androidversion = '". $aVersion ."', sensors = '". $sensors ." 
                    WHERE uid = ". $userID . " AND deviceid = '". $deviceID ."'";      
                
       //$this->logger->logInfo("##################### SETTING HARDWARE PARAMS ################". $sql); // LOG THE QUERY 
                        
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
    * Inserts new entry for device
    * 
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function insertDevice($db, $hardwareTable, $aVersion, $sensors, $userID, $deviceID){
       
      //$this->logger->logInfo("INSERT HARDWARE");
      
      $sql = "INSERT INTO ". $hardwareTable ." 
                (uid, deviceid, androidversion, sensors) 
                VALUES 
                (". $userID .", '". $deviceID . "', '". $aVersion ."', '". $sensors ."')";

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
                    FROM " .$hardwareTable.
                    " WHERE androidversion >=".$androidVersion;
        
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
        $sensors = json_encode($array);
        
        return $sensors;
    }
}
    
?>