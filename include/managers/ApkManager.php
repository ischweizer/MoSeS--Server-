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
    * Returns all APKs in DB that are not published in a user-study
    * 
    * @param mixed $db
    * @param mixed $apkTable
    */
    public static function getNonStudyAllApk($db, $apkTable){
        
        $sql = "SELECT * 
                FROM ". $apkTable ." WHERE locked=0";
                
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
                            
       $result = $db->query($sql);
       $row = $result->fetch();
       
       return $row;
    }
    
    /**
    * Increments the number that tells how many times the apk has been downloaded
    * returns true if the apk with the provided id exists, false otherwise
    * 
    * @param mixed $db
    * @param mixed $apkTable
    * @param mixed $apkID
    */
      public static function incrementAPKUsage($db, $apkTable, $apkID, $hwid, $logger){
          $logger->logInfo("incrementAPKUsage");
          // checking if the apk exists
          $sql = "SELECT * FROM ". $apkTable ." WHERE apkid = ". intval($apkID);
          $result = $db->query($sql);
          $row = $result->fetch();
          if(empty($row)){
              return false;
          }
          else{
              $devs = $row['installed_on'];
              if(empty($devs)){
                $devs = array();
              }
              else{
                  $devs = json_decode($devs);
              }
              $devs[] = intval($hwid);
              $devs = array_unique($devs);
              sort($devs);
              $part = count($devs);
              $devs=json_encode($devs);
              $sql1 = "UPDATE " .$apkTable. " SET participated_count=".$part." WHERE apkid= ".$apkID;
              $sql2 = "UPDATE ".$apkTable . " SET installed_on='".$devs."' WHERE apkid=".$apkID;
              $db->exec($sql1);
              $db->exec($sql2);
              }
              return true;
      }
    
    
    /**
    * Decrements the number that tells how many times the apk has been downloaded
    * returns true if the apk with the provided id exists, false otherwise
    * 
    * @param mixed $db
    * @param mixed $apkTable
    * @param mixed $apkID
    */
      public static function decrementAPKUsage($db, $apkTable, $apkID, $hwid, $logger){
          
          $logger->logInfo("decrementAPKUsage");
          // checking if the apk exists
          
          $sql = "SELECT * FROM ". $apkTable ." WHERE apkid = ". intval($apkID);
          $result = $db->query($sql);
          $row = $result->fetch();
          $devs = $row['installed_on'];
          if(empty($devs)){
            return;
          }
          else{
              $devs = json_decode($devs);
          }
          $new_devs = array();
          foreach($devs as $hw_old){
              if($hw_old != $hwid)
                $new_devs[] = $hw_old;
          }
          $new_devs = array_unique($new_devs);
          sort($new_devs);
          $part = count($new_devs);
          $new_devs=json_encode($new_devs);
          $sql1 = "UPDATE " .$apkTable. " SET participated_count=".$part." WHERE apkid= ".$apkID;
          $sql2 = "UPDATE ".$apkTable . " SET installed_on='".$new_devs."' WHERE apkid=".$apkID;
          $db->exec($sql1);
          $db->exec($sql2);
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
