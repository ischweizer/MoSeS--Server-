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

/**
* Database configuration
*/
$CONFIG['DB']['HOST'] = 'xxx.xxx.xxx.xxx';
$CONFIG['DB']['DBNAME'] = 'xxxxxxxxxx';
$CONFIG['DB']['USER'] = 'xxxxxxxxxx';
$CONFIG['DB']['PASSWORD'] = 'xxxxxxxxxxx';

/**
* Table names for database
*/
$CONFIG['DB_TABLE']['ANDROID_SESSION'] = 'android_session';
$CONFIG['DB_TABLE']['APK'] = 'apk';
$CONFIG['DB_TABLE']['HARDWARE'] = 'hardware';
$CONFIG['DB_TABLE']['REQUEST'] = 'request';
$CONFIG['DB_TABLE']['USER'] = 'user';
$CONFIG['DB_TABLE']['RGROUP'] = "rgroup";
$CONFIG['DB_TABLE']['STUDY_SURVEY'] = "study_survey";
$CONFIG['DB_TABLE']['STUDY_FORM'] = "study_form";
$CONFIG['DB_TABLE']['STUDY_QUESTION'] = "study_question";
$CONFIG['DB_TABLE']['STUDY_ANSWER'] = "study_answer";
$CONFIG['DB_TABLE']['STUDY_RESULT'] = "study_result";

/*
* UPLOAD Config
*/
$CONFIG['UPLOAD']['FILESIZE'] = 209715200;   // 200MB

/**
* Google-Push Service
*/
$CONFIG['GPUSH']['GOOGLE_SERVER_KEY'] = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

/**
* Define project home dir
*/
$CONFIG['PROJECT']['MOSES_HOME'] = __DIR__;
define('MOSES_HOME', __DIR__);

/**
* Session configuration
*/
$CONFIG['SESSION']['TIMEOUT'] = 120; // in seconds, when logged in no the website

/**
* Cronjob config
*/
$CONFIG['CRON']['STUDY_TIMEOUT'] = 55; // in seconds

/**
* Treshold config
*/
$CONFIG['MISC']['SC_TRESHOLD'] = 5; // be a scientist treshold

/**
 * Login response codes, for website (not MoSeS-App)
 */
$CONFIG['LOGIN_RESPONSE']['OK'] = "0"; // user is registered and confirmed, thus logged in 
$CONFIG['LOGIN_RESPONSE']['NOT_CONFIRMED'] = "1"; // user is registered but not confirmed
$CONFIG['LOGIN_RESPONSE']['WRONG_LOGIN_OR_PASSWORD'] = "2"; // no such user or password was wrong
$CONFIG['LOGIN_RESPONSE']['MISSING_PASSWORD'] = "3"; // password is not entered
$CONFIG['LOGIN_RESPONSE']['MISSING_LOGIN'] = "4"; // missing login (email)
$CONFIG['LOGIN_RESPONSE']['SHORT_PASSWORD'] = "5"; // password is too short

?>
