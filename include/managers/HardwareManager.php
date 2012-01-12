<?php
    
class HardwareManager{
    private $db;
    private $hardwareTable;
    private $logger;
    
    /**
    * Constructor
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @return HardwareManager
    */
    public function __construct(){
        
        //include_once(MOSES_HOME."/include/functions/klogger.php");
        //$this->logger = new KLogger(MOSES_HOME . "/log", KLogger::INFO);        
        
        //$this->db = $db;
        //$this->hardwareTable = $hardwareTable;
        
        //$this->logger->logInfo("MOSES HOME".MOSES_HOME);                              
    }
    
    /**
    * Checks if user has some device with device id given
    * and returns this entry, null if nothing found
    * 
    * @param int $userID
    * @param string $deviceID
    */
    public static function selectDeviceForUser($db, $userID, $deviceID){
      
       $sql = "SELECT deviceid 
               FROM ". $this->hardwareTable ." 
               WHERE uid = ". $userID. " AND deviceid = '". $deviceID ."'";
               
       $result = $db->query($sql);
       
       $this->logger->logInfo($sql); // LOG INFO
       
       $row = $result->fetch();
       
       $this->logger->logInfo("ROW IS: ". !empty($row));
       
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
    public static function updateDevice($aVersion, $sensors, $userID, $deviceID){
      
       $this->logger->logInfo("row update has to be commited");   
       $this->logger->logInfo("##################### SETTING HARDWARE PARAMS ################ deviceid selected and uid jetzt sofort");
       $this->logger->logInfo("UPDATE HARDWARE");
           
       $sql = "UPDATE ". $this->hardwareTable ."
                    SET androidversion = '". $aVersion ."', sensors = '". $sensors ."' 
                    WHERE uid = ". $userID . " AND deviceid = '". $deviceID ."'";      
                
       $this->logger->logInfo("##################### SETTING HARDWARE PARAMS ################". $sql); // LOG THE QUERY 
                        
       $this->db->exec($sql);
    }
    
    /**
    * Inserts new entry for device
    * 
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $userID
    * @param mixed $deviceID
    */
    public static function insertDevice($aVersion, $sensors, $userID, $deviceID){
       
      $this->logger->logInfo("INSERT HARDWARE");
      
      $sql = "INSERT INTO ". $this->hardwareTable ." 
                (uid, deviceid, androidversion, sensors) 
                VALUES 
                (". $userID .", '". $deviceID . "', '". $aVersion ."', '". $sensors ."')";

      $this->db->exec($sql); 
    }
}
    
?>