<?php
class LoginManagerTest extends PHPUnit_Framework_TestCase
{
    private $db;
    private $loginManager;
    private $asTable;
    private $userTable;
    
    protected function setUp() {
        include_once('../config.php');
        try {
        $this->db = new PDO("mysql:host=212.72.183.108;dbname=moses", "moses", "mosespassworddasense");
        }
        catch (PDOException $e) {
            print "Error on Testing!: " . $e->getMessage() . "<br/>";
            die();
        }
        
        $this->asTable = "test_android_session";
        $this->userTable = "test_user";
        
        $sql = "CREATE TABLE `".$this->asTable."` (
            `session_id` char(32) character set utf8 NOT NULL,
            `userid` int(10) unsigned NOT NULL default '0',
            `lastactivity` int(10) unsigned NOT NULL default '0',
            `deviceid` varchar(255) NOT NULL,
            PRIMARY KEY  (`session_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

        // create the table for testis            
        $this->db->exec($sql);
        
        $sql = "CREATE TABLE `".$this->userTable."` (
              `userid` int(10) unsigned NOT NULL auto_increment,
              `usergroupid` smallint(5) unsigned NOT NULL default '0',
              `firstname` varchar(50) NOT NULL,
              `lastname` varchar(50) NOT NULL,
              `login` varchar(50) NOT NULL,
              `password` varchar(32) NOT NULL,
              `hash` varchar(32) NOT NULL,
              `usertitle` varchar(250) NOT NULL,
              `email` varchar(100) NOT NULL,
              `ipaddress` varchar(15) NOT NULL,
              `lastactivity` int(10) unsigned NOT NULL default '0',
              `joindate` int(10) unsigned NOT NULL default '0',
              `passworddate` int(10) unsigned NOT NULL,
              `confirmed` int(5) unsigned NOT NULL default '0',
              PRIMARY KEY  (`userid`)
            ) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;";

        // create the table for testis            
        $this->db->exec($sql);
        
        include_once(ROOT_DIR."/include/managers/LoginManager.php");
        $this->loginManager = new LoginManager($this->db, $this->asTable, $this->userTable);
    }
    
    /**
    * @test
    * testing insert function
    */
    public function testInsertSession(){
       $this->loginManager->insertSession("123", "321", 111);
       $sql = "SELECT * FROM ".$this->asTable. " WHERE session_id=123";
       $result = $this->db->query($sql);
       $row = $result->fetch();
       $this->assertTrue(!empty($row));
    }
    
}
?>