<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php
   
/*
 * @author: Wladimir Schmidt
 * @author: Zijad Maksuti
 */   
    
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
    * Updates the hardware table with new information regarding a specific device assinged to a user.
    * 
    * @param mixed $db
    * @param mixed $hardwareTable
    * @param mixed $aVersion
    * @param mixed $sensors
    * @param mixed $modelname
    * @param mixed $vendorname
    * @param mixed $userID
    * @param mixed $deviceID
    * @param string $deviceName the name of the device
    */
    public static function updateDevice($db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID, $deviceName){
           
       $sql = "UPDATE ". $hardwareTable ." 
               SET androidversion = '". $aVersion ."', 
                   sensors = '". $sensors ."' , 
                   modelname = '".$modelname."' , 
                   vendorname = '".$vendorname."',
                   devicename = '".$deviceName."'  
               WHERE uid = ". $userID . " AND deviceid = '". $deviceID ."'";
                        
       $db->exec($sql);
    }
    
    /**
    * Updates the hardware table with new information regarding a specific device assinged to a user.
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
    * @param string $deviceName the name of the device
    */
    public static function updateDeviceLogger($logger, $db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID, $deviceName){
           
       $sql = "UPDATE ". $hardwareTable ." 
               SET androidversion = '". $aVersion ."', 
                   sensors = '". $sensors ."' , 
                   modelname = '".$modelname."' , 
                   vendorname = '".$vendorname."',
                   devicename = '".$deviceName."'  
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
    * Inserts a new device into hardware table.
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
    * @param string $deviceName the name of the device
    */
    public static function insertDevice($db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID, $deviceName){
      
      $sql = "INSERT INTO ". $hardwareTable ." 
                (uid, deviceid, modelname, vendorname, androidversion, sensors, devicename) 
                VALUES 
                (". $userID .", '". $deviceID . "' , '".$modelname."' , '".$vendorname."' , '". $aVersion ."', '". $sensors ."', '". $deviceName ."')";
      
      $logger->logInfo("insertDeviceLogger; sql=".$sql);

      $db->exec($sql); 
    }
    
    /**
    * Inserts a new device into hardware table.
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
    * @param string $deviceName the name of the device
    */
    public static function insertDeviceLogger($logger, $db, $hardwareTable, $aVersion, $sensors, $modelname, $vendorname, $userID, $deviceID, $deviceName){
      
      $sql = "INSERT INTO ". $hardwareTable ." 
                (uid, deviceid, modelname, vendorname, androidversion, sensors, devicename) 
                VALUES 
                (". $userID .", '". $deviceID . "' , '".$modelname."' , '".$vendorname."' , '". $aVersion ."', '". $sensors ."', '". $deviceName ."')";
      
      $logger->logInfo("insertDeviceLogger; sql=".$sql);

      $db->exec($sql); 
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
     * Returns the devicename assigned to the provided deviceid and userid
     *
     * @param mixed $db the database
     * @param string $hardwareTable the name of the hardware table 
     * @param string $userID the id of the user
     * @param string $deviceID the id of the device
     * @return the name of the device with deviceID and assigned to the user with the ID $userID
     * or null if the name does not exist
     */
    public static function getDeviceName($db, $hardwareTable, $userID, $deviceID){
    
    	$sql = "SELECT devicename
                FROM ". $hardwareTable ."
                WHERE uid = ". $userID ." AND deviceid = '". $deviceID ."'";
    
    	$result = $db->query($sql);
    	$row = $result->fetch(PDO::FETCH_ASSOC);
    	 
    	if(!empty($row)){
    		return $row['devicename'];
    	}
    	 
    	return null;
    }
    
    /**
     * Returns the GCM registration id assigned to the provided hardwareid
     *
     * @param mixed $db the database
     * @param string $hardwareTable the name of the hardware table
     * @param string $hardwareID the hwid of the device
     * @return the GCM registration id of the device with $hardwareID
     * or null if gcm id does not exist
     */
    public static function getGCMRegistrationId($db, $hardwareTable, $hardwareID){
    
    	$sql = "SELECT c2dm
                FROM ". $hardwareTable ."
                WHERE hwid = ". $hardwareID;
    
    	$result = $db->query($sql);
    	$row = $result->fetch(PDO::FETCH_ASSOC);
    
    	if(!empty($row)){
    		$toBeReturned = $row['c2dm']; 
    		if(empty($toBeReturned))
    			return null;
    		else
    			return $toBeReturned;
    	}
    	else
    		return null;
    }
                                                   
    /**
    * Returns an array of all hardware-ids that can install an apk
    * with required android version
    * 
    * @param mixed $db the database
    * @param String $hardwareTable the name of the hardware table to search in
    * @param String $androidVersion the lowest android version required by the apk
    */
    public static function getCandidatesForAndroid($db, $hardwareTable, $androidVersion, $logger){
        
        if(!empty($androidVersion)){
        
            // get for android version
            $sql = "SELECT hwid, sensors
                    FROM " .$hardwareTable. " 
                    WHERE androidversion >=".$androidVersion;
            
            //$logger->logInfo($sql);
            
            $result = $db->query($sql);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        
            return $rows;
        }
        return $rows;
    }
    
    
    /**
    * Returns an array of all hardware-ids that can install an apk
    * with required android version
    * Returned hardware-id belong to users from the
    * specified rGroup
    * 
    * @param mixed $db the database
    * @param String $hardwareTable the name of the hardware table to search in
    * @param String $androidVersion the lowest android version required by the apk
    * @param String $rGroup select only hardware from this group
    */
    public static function getCandidatesForAndroidFromGroup($db, $hardwareTable, $rgroupTable, $androidVersion, $rGroup, $logger){
        
        $return = array();
        $sql = "SELECT members FROM ".$rgroupTable. " WHERE name='".$rGroup."'";
        $result_members = $db->query($sql);
        $member_row = $result_members->fetch();
        $members = json_decode($member_row['members']);
        foreach($members as $member){
                // get for android version
            $sql = "SELECT hwid, sensors
                    FROM " .$hardwareTable. " 
                    WHERE androidversion >=".$androidVersion. " AND uid=".$member;    
            
            $result = $db->query($sql);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            $return = array_merge_recursive($return, $rows);
        }
        
        return $return;
    }
    
    /**
     * Returns an array of GCM registration ids of all devices that can install an apk
     * with required android version and whose owners are members of the specified group.
     * 
     * @param mixed $db the database
     * @param String $hardwareTable the name of the hardware table to search in
     * @param String $androidVersion the lowest android version required by the apk
     * @param String $rGroup select only hardware from this group
     * @param mixed $logger the logger
     */
    public static function getGCMRegistrationsFromGroup($db, $hardwareTable, $rgroupTable, $androidVersion, $rGroup, $logger){
    
    	$return = array();
    	$sql = "SELECT members FROM ".$rgroupTable. " WHERE name='".$rGroup."'";
    	$result_members = $db->query($sql);
    	$member_row = $result_members->fetch();
    	$members = json_decode($member_row['members']);
    	foreach($members as $member){
    		// get for android version
    		$sql = "SELECT c2dm
                    FROM " .$hardwareTable. "
                    WHERE androidversion >=".$androidVersion. " AND uid=".$member;
    
    		$result = $db->query($sql);
    		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
    		foreach($rows as $row)
    			$return[] = $row['c2dm'];
    	}
    	
    	return $return;
    }
    
    /**
    * Returns the row from hw-table containing specified deviceID and userID
    */
    public static function getHardware($db, $hwTable, $deviceID, $userID, $logger){
        $sql="SELECT * FROM ".$hwTable." WHERE uid=".$userID." AND deviceid='".$deviceID."'"; 
        $result = $db->query($sql);
        $row = $result->fetch();
        return $row;
    }
    
    
    /**
    * Removes the row from hw-table containing specified deviceID and userID
    */
    public static function removeHardware($db, $hwTable, $deviceID, $userID, $logger){
        $sql="DELETE FROM ".$hwTable." WHERE uid=".$userID." AND deviceid='".$deviceID."'";
        $db->exec($sql);
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
    
    /**
     * Validates the consumed name of the device. A device name is valid
     * if it is not empty and it does not consist characters other than
     * letters, numbers, underscores and whitespaces. A name consisting only
     * out of whitespaces is invalid.
     * @param string $deviceName the name to be validated
     * @return true if $deviceName is valid, fals otherwise
     */
    public static function isValidDeviceName($deviceName){
    	if($deviceName == null || empty($deviceName) || strlen(trim($deviceName)) == 0)
    		return false; // empty or null
    	else
    	if(!preg_match("/^[a-zA-Z0-9_]+$/", str_replace(" ", "", $deviceName)))
    		return false; // invalid character found
    	return true; // if we got here, everything was ok
    }
}
    
?>
