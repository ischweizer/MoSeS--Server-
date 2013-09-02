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

class LoginManager{
    
    /**
    * Updates user session
    * 
    * @param timestamp $lastActivity
    * @param string $deviceID
    * @param md5 string $sessionID
    */
    public static function updateSession($db, $aSessionTable, $lastActivity, $sessionID){
        
       $sql = "UPDATE ". $aSessionTable ."
                SET lastactivity = ". $lastActivity ."  
                WHERE session_id = '". $sessionID ."'";
                            
       $res = $db->exec($sql);
       
       return $res; 
    }
    
    /**
    * This function consumes userName and password
    * and returns the session id assigned to the
    * user, if login successfull, or null if unsuccessful
    *     
    * @param string $email
    * @param string $password
    */
    public static function loginUser($db, $userTable, $aSessionTable, $email, $password, $sessionID){
         
        $sql =   "SELECT * 
                      FROM ". $userTable ." 
                      WHERE email = '". $email ."' AND password = '". $password ."' AND confirmed=1";

        // check if the user is in the table
        $result = $db->query($sql);
        $row = $result->fetch();

        if(!empty($row)){
            
            $userID = intval($row["userid"]);
            
            $sql = "INSERT INTO " . $aSessionTable . 
                        " (session_id, userid, lastactivity) 
                        VALUES 
                        ('". $sessionID ."', ". $userID . ", " . time() . ")";
            
            $db->exec($sql);
                        
            return true;
        }
         
        return false;
         
    }
    
    /**
    * This method logouts given user by his SESSION ID
    * 
    * @param string $session_id
    */
    public static function logoutUser($db, $aSessionTable, $sessionID){
         
        $sql = "DELETE FROM ". $aSessionTable ." 
                       WHERE session_id='". $sessionID ."'";

        $result = $db->exec($sql);
        
        if($result === false){
            return false;
        }
        
        return true;
    }
    
    /**
    * Returns userid and last activity back if user logged in with given session id 
    * 
    * @param string $sessionID
    */
    public static function getLoggedInUser($db, $aSessionTable, $sessionID){
      
        $sql = "SELECT userid, lastactivity 
                                FROM ". $aSessionTable ." 
                                WHERE session_id = '". $sessionID ."'"; 
                
        $result = $db->query($sql);
        $row = $result->fetch();
        
        if(!empty($row)){
            return $row;
        }
        
        return null;
    }
    
     /**
    * Returns deviceID and lastactivity of the logged in device
    * 
    * @param string $sessionID
    */
    public static function getLoggedInDevice($db, $aSessionTable, $sessionID){
      
        $sql = "SELECT lastactivity, userid 
                FROM ". $aSessionTable ." 
                WHERE session_id = '". $sessionID ."'"; 
                
        $result = $db->query($sql);
        $row = $result->fetch();
        
        return $row;
        
    }
    
    /**
    * Checks if user session in between timeout window
    * returns true if so, false if not.
    * 
    * @param timestamp $lastActivity
    * @param timestamp $timenow
    */
    public static function isSessionTimedout($lastActivity, $timenow, $maxSessionTime){
        return ($timenow - $lastActivity <= $maxSessionTime) ? true : false;
    }
    
    /**
     * Returns the name of the group of the user with the specified id.
     * @param unknown $logger the logger
     * @param unknown $db the database
     * @param unknown $userTable the name of the user table
     * @param unknown $userId the id of the user whose group is queried
     * @return name od the user's group or null if the user is not member of any group or no such user exists
     */
    public static function getGroupName($logger, $db, $userTable, $userId){
    	$sql = "SELECT rgroup FROM ". $userTable ."
                WHERE userid = ". $userId;
    	
    	$logger->logInfo("getGroupName() sql=".$sql);
    	
    	$result = $db->query($sql);
    	$row = $result->fetch(PDO::FETCH_ASSOC);
    	if(!empty($row))
    		return $row['rgroup'];
    	else
    		return $row;
    }
    
}
?>
