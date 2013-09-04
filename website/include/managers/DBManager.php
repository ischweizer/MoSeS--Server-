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
