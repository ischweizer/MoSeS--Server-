<?php

class LoginManager{
    
    //private $db;
    //private $logger;
    
    public function __construct(){
        
        //include_once(MOSES_HOME."/include/functions/klogger.php");
        //$this->logger = new KLogger(MOSES_HOME . "/log", KLogger::INFO);
        
        //$this->logger->logInfo("MOSES HOME".MOSES_HOME);
        
        //$this->db = $db;
    }
    
    /**
    * Updates user session
    * 
    * @param timestamp $lastActivity
    * @param string $deviceID
    * @param md5 string $sessionID
    */
    public static function updateSession($db, $aSessionTable, $lastActivity, $deviceID, $sessionID){
        
       $sql = "UPDATE ". $aSessionTable ."
                SET lastactivity = ". $lastActivity .", deviceid = '". $deviceID ."' 
                WHERE session_id = '". $sessionID ."'";
       
       //$this->logger->logInfo($sql); // LOG THE QUERY
                            
       $res = $db->exec($sql);
       
       return $res; 
    }
    
    /**
    * This function consumes userName and password
    * and returns the session id assigned to the
    * user, if login successfull, or null if unsuccessful
    *     
    * @param string $uname
    * @param string $password
    */
    public static function loginUser($db, $userTable, $aSessionTable, $login, $password, $sessionID){
         
        $sql =   "SELECT * 
                      FROM ". $userTable ." 
                      WHERE login = '". $login ."' AND password = '". $password ."'";
                
        //$this->logger->logInfo("LoginManager: ".$sql);

        // check if the user is in the table
        $result = $db->query($sql);
        $row = $result->fetch();
        
        //$this->logger->logInfo(print_r($row, true));

        if(!empty($row)){
            
            $userID = intval($row["userid"]);
            //$this->logger->logInfo($uid);
            
            $sql = "INSERT INTO " . $aSessionTable . 
                        " (session_id, userid, lastactivity) 
                        VALUES 
                        ('". $sessionID ."', ". $userID . ", " . time() . ")";
            
            //$this->logger->logInfo("INSERT SESSION: ".$sql);
            
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
        
        //$this->logger->logInfo("SET HARDWARE PARAMS ARRIVED");
      
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
    * Checks if user session in between timeout window
    * returns true if so, false if not.
    * 
    * @param timestamp $lastActivity
    * @param timestamp $timenow
    */
    public static function isSessionTimedout($lastActivity, $timenow, $maxSessionTime){
        return ($timenow - $lastActivity <= $maxSessionTime) ? true : false;
    }
    
}
?>