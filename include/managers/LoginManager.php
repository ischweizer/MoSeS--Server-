<?php
/**
* This class hanldes login and logout of a user
*/
class LoginManager{
    
    private $db; // the database
    private $aSessionTable; // name of the table for andriod session
    private $userTable;
    private $sessionID;
    private $logger;
    
    public function __construct($db, $aSessionTable, $userTable){
        
        include_once(MOSES_HOME."/include/functions/klogger.php");
        $this->logger = new KLogger(MOSES_HOME . "/log", KLogger::INFO);
        $this->logger->logInfo("MOSES HOME".MOSES_HOME);
        
        $this->db = $db;
        $this->aSessionTable = $aSessionTable;
        $this->userTable = $userTable;
        $this->sessionID = session_id();
    }
    
    /**
    * Inserts a session into DB
    * 
    * @param varchar $sessionID
    * @param int $userID
    * @param long $time
    */
    private function insertSession($sessionID, $userID, $time){
        // Store the session in the database
        $sql = "INSERT INTO " .$this->aSessionTable. 
                        " (session_id, userid, lastactivity) 
                        VALUES 
                        ('". $sessionID ."', ". $userID . ", " . $time . ")";
        
        $this->logger->logInfo("INSERT SESSION: ".$sql);
        
        $this->db->exec($sql);
    }
    
    /**
    * This deletes given session id from DB
    * returns number of rows affected or false as error
    * 
    * @param mixed $sessionID
    */
    private function deleteSession($sessionID){
        
        $sql = "DELETE FROM ". $this->aSessionTable ." 
                       WHERE session_id='". $sessionID ."'";

        $res = $this->db->exec($sql);
        
        return $res;
    }
    
    /**
    * This function returns the user id for the consumed
    * login and password
    * 
    * @param string $login login name of the user
    * @param string $password password of the user
    * @return integer the user's id, if any, or null user does not exist
    */
    private function getUserID($login, $password){
        
        
        $sql =   "SELECT * 
                      FROM ". $this->userTable .
                      " WHERE login = '". $login ."' AND password = '". $password ."'";
                
        $this->logger->logInfo("LoginManager: ".$sql);
        
        // check if the user is in the table
        $result = $this->db->query($sql);
                      
        $row = $result->fetch();
        
        $this->logger->logInfo(print_r($row, true));
        
        if(empty($row))
            return null;
        else{
            $uid = intval($row["userid"]);
            $this->logger->logInfo($uid);
            return $uid;
        }
    }
    
    /**
    * This function consumes userName and password
    * and returns the session id assigned to the
    * user, if login successfull, or null if unsuccessful
    *     
    * @param string $uname
    * @param string $password
    */
    public function loginUser($uname, $password){
         $userID = $this->getUserID($uname, $password);
         
         if($userID != null){
             $this->insertSession(session_id(), $userID, time());
             return $this->sessionID;
         }
         else
            return null;
         
    }
    
    /**
    * This method logouts given user by his SESSION ID
    * 
    * @param string $session_id
    */
    public function logoutUser($sessionID){
         
        $result = $this->deleteSession($sessionID);
        
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
    public static function getLoggedInUser($sessionID){
        
        $logger->logInfo("SET HARDWARE PARAMS ARRIVED");
                
        $result = $db->query("SELECT userid, lastactivity 
                                FROM ". $this->aSessionTable ." 
                                WHERE session_id = '". $sessionID ."'");
        $row = $result->fetch();
        
        if(!empty($row)){
            return $row;
        }
        
        return null;
    }
    
}
?>