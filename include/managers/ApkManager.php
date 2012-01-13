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
    public static function getApk($db, $apkTable, $userID, $apkID){
        
        $sql = "SELECT userhash, apkhash, apkname  
               FROM ". $apkTable ." 
               WHERE 
               userid = ". $userID ." AND apkid = '". $apkID ."'";
                            
       $result = $db->query($sql);
       $row = $result->fetch();
       
       return $row;
    }
}
?>