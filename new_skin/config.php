<?php

//if(!defined('PAGE'))
  //  die();

/**
* Database configuration
*/
$CONFIG['DB']['HOST'] = '212.72.183.108';
$CONFIG['DB']['DBNAME'] = 'moses';
$CONFIG['DB']['USER'] = 'moses';
$CONFIG['DB']['PASSWORD'] = 'mosespassworddasense';

/**
* Table names for database
*/
$CONFIG['DB_TABLE']['ANDROID_SESSION'] = 'android_session';
$CONFIG['DB_TABLE']['APK'] = 'apk';
$CONFIG['DB_TABLE']['HARDWARE'] = 'hardware';
$CONFIG['DB_TABLE']['REQUEST'] = 'request';
$CONFIG['DB_TABLE']['USER'] = 'user';

/**
* Define project home dir
*/
$CONFIG['PROJECT']['MOSES_HOME'] = __DIR__;
define('MOSES_HOME', __DIR__);

/**
* Session configuration
*/
$CONFIG['SESSION']['TIMEOUT'] = 120; // in seconds 

$CONFIG['CRON']['STUDY_TIMEOUT'] = 55; // in seconds  


?>