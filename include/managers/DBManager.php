<?php

/*
 * @author: Zijad Maksuti
 */

/**
* This class manages the creation and dropping of tables
*/
class DBManager
{
    private $db=null; // the database
    
    public function __construct(){
                                     
    }
    
    /**
    * This function connects the manager to the database
    *     
    * @param string $host the address of the 
    * @param string $dbName the name of the database to connect to
    * @param string $user the name of the user
    * @param string $password the password needed to access the database
    */
    public function connect($host, $dbName, $user, $password){
        
        try{
        $this->db = new PDO("mysql:host=".$host.";dbname=".$dbName,
        $user, $password);
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
            }
    
    }
    
    /**
    * This function returns the database assigned to this object
    * @return the database assigned to this object, or null
    * if no database is assigned
    */
    public function getDB(){
        return $this->db;
    }
    
    public function __destruct() {
       $this->db = null;
   }
    
}
?>