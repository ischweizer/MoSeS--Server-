<?php
    
class ApkManager{
    
    public function __construct(){
        
    }
    
    /**
    * Returns all APKs in DB
    * 
    * @param mixed $db
    * @param mixed $apkTable
    */
    public static function getAllApk($db, $apkTable){
        
        $sql = "SELECT * 
                FROM ". $apkTable;
                
        $result = $db->query($sql);
        $array = $result->fetchAll(PDO::FETCH_ASSOC);
        
        return $array;
    }
    
    /**
    * Returns particular APK that fits requirements
    * 
    * @param mixed $db
    * @param mixed $apkTable
    * @param mixed $userID
    * @param mixed $apkID
    */
    public static function getApk($db, $apkTable, $userID, $apkID, $logger){
        
        $sql = "SELECT *  
               FROM ". $apkTable ." 
               WHERE apkid = ". intval($apkID);
               //WHERE userid = ". $userID ." AND apkid = ". intval($apkID);
               
        $logger->logInfo("#########################getApk Function#######################"); 
        $logger->logInfo($sql); 
                            
       $result = $db->query($sql);
       $row = $result->fetch();
       
       return $row;
    }
    
    /**
    * Increments the number that tells how many times the apk has been downloaded
    * 
    * @param mixed $db
    * @param mixed $apkTable
    * @param mixed $apkID
    */
      public static function incrementAPKUsage($db, $apkTable, $apkID){
        
        $sql = "UPDATE " .$apkTable. " SET participated_count = participated_count + 1 WHERE apkid= ".$apkID;
        
        
        $db->exec($sql);
        
    }
    
    /**
    * retrives restriction number of the given apkID
    *     
    * @param mixed $db
    * @param mixed $apkTable
    * @param mixed $apkID
    * @return mixed
    */
    public static function getRestrictionUserNumber($db, $apkTable, $apkID){
        
        $sql = "SELECT restriction_user_number FROM " .$apkTable. " WHERE apkid= ".$apkID;
        
        $result = $db->query($sql);
        $row = $result->fetch();
        
        if(!empty($row))
            return $row['restriction_user_number'];
        
        return null;
        
    }
    
    /**
    * returns true if the deviceID is in the list of pending devices (for user study)
    * 
    * @param mixed $db
    * @param mixed $apkTable
    * @param mixed $apkID
    * @param mixed $userID
    */
    public static function isSelectedDevice($db, $apkTable, $apkID, $hardwareID){
        
        $sql = "SELECT pending_devices FROM " .$apkTable. " WHERE apkid= ".$apkID;
        
        $result = $db->query($sql);
        $row = $result->fetch();
        
        if(!empty($row)){
            $selectedDevices = explode(',', $row['pending_devices']);
            if(in_array($hardwareID, $selectedDevices))
                return true;
        }
        
        return false;
        
    }
}
?>
