/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`moses` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `moses`;

/*Table structure for table `android_session` */

DROP TABLE IF EXISTS `android_session`;

CREATE TABLE `android_session` (
  `session_id` char(32) NOT NULL,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `apk` */

DROP TABLE IF EXISTS `apk`;

CREATE TABLE `apk` (
  `apkid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'apk id',
  `apktitle` varchar(255) DEFAULT NULL COMMENT 'name of the programm',
  `apkname` varchar(255) NOT NULL COMMENT 'name of the apk (file)',
  `userhash` varchar(32) NOT NULL COMMENT 'user''s hash value',
  `apkhash` varchar(32) NOT NULL COMMENT 'hash of the apk',
  `apk_version` int(10) unsigned DEFAULT '1' COMMENT 'version of the apk',
  `userid` int(10) unsigned NOT NULL COMMENT 'user''s id',
  `description` text COMMENT 'short description of the apk',
  `androidversion` varchar(32) DEFAULT NULL COMMENT 'minimal android version needed to run the apk',
  `restriction_device_number` int(20) NOT NULL DEFAULT '-1' COMMENT 'number of users allowed to install the app',
  `participated_count` int(32) unsigned NOT NULL DEFAULT '0' COMMENT 'number of users that have installed the app',
  `survey_results_sent_count` int(32) unsigned NOT NULL DEFAULT '0' COMMENT 'The counter for users that have filled out and sent their survey',
  `candidates` mediumtext COMMENT 'all users that can install the app',
  `notified_devices` mediumtext COMMENT 'notification has been sent',
  `pending_devices` mediumtext COMMENT 'waiting for this users to install the app',
  `last_round_time` int(10) unsigned DEFAULT '0' COMMENT 'the last time the cron-job has started',
  `ustudy_finished` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TRUE only when user study for an uploaded apk is finished, otherwise FALSE',
  `installed_on` mediumtext COMMENT 'list of devices having the app currently installed',
  `private` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'private 1, public 0',
  `startdate` date DEFAULT NULL COMMENT 'the start date of this user study',
  `enddate` date DEFAULT NULL COMMENT 'the end date of this user study',
  `startcriterion` int(6) unsigned DEFAULT '0' COMMENT 'The number of users who installed this apk ,so that the user study should start to run',
  `runningtime` int(10) unsigned DEFAULT NULL COMMENT 'The running time of this user study after starting with startcriterion',
  `inviteinstall` tinyint(4) unsigned DEFAULT NULL COMMENT 'If 1, invites will be send to selected users by the system',
  `time_enough_participants` int(10) unsigned DEFAULT NULL COMMENT 'Timestamp at which enough devices have been reached in order to mark ustudy as finished',
  `apk_updated` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'if new apk file was uploaded, set here 1, else 0',
  PRIMARY KEY (`apkid`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

/*Table structure for table `hardware` */

DROP TABLE IF EXISTS `hardware`;

CREATE TABLE `hardware` (
  `hwid` int(32) unsigned NOT NULL AUTO_INCREMENT COMMENT 'hardware id assigned by MoSeS',
  `uid` int(32) unsigned NOT NULL COMMENT 'user id',
  `deviceid` varchar(16) CHARACTER SET latin1 NOT NULL COMMENT 'the unique id of this device',
  `devicename` varchar(255) DEFAULT NULL COMMENT 'device name selected by the user',
  `modelname` varchar(255) CHARACTER SET latin1 NOT NULL COMMENT 'the name of the device''s model',
  `vendorname` varchar(255) CHARACTER SET latin1 NOT NULL COMMENT 'the name of the device''s vendor',
  `androidversion` varchar(32) DEFAULT NULL COMMENT 'android version of the device',
  `sensors` varchar(255) DEFAULT NULL COMMENT 'all sensors on the device',
  `c2dm` varchar(4096) CHARACTER SET latin1 DEFAULT NULL COMMENT 'gcm id of the device',
  PRIMARY KEY (`uid`,`deviceid`),
  KEY `hwid` (`hwid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `request` */

DROP TABLE IF EXISTS `request`;

CREATE TABLE `request` (
  `uid` int(10) unsigned NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `reason` text,
  `accepted` smallint(2) unsigned NOT NULL DEFAULT '0',
  `pending` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `rgroup` */

DROP TABLE IF EXISTS `rgroup`;

CREATE TABLE `rgroup` (
  `name` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'name of a research group',
  `password` varchar(32) CHARACTER SET latin1 NOT NULL COMMENT 'password needed to join the group',
  `members` varchar(256) CHARACTER SET latin1 NOT NULL COMMENT 'ids of the users in the group',
  `instant_scientists_counter` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of scientists that got their scientist account via group instant scientist button',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `study_answer` */

DROP TABLE IF EXISTS `study_answer`;

CREATE TABLE `study_answer` (
  `aid` int(32) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The unique id of the possible answer.',
  `questionid` int(32) unsigned NOT NULL COMMENT 'The id of the question to which the possible answer belongs.',
  `text` varchar(255) DEFAULT NULL COMMENT 'The text of the possible answer.',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=1024 DEFAULT CHARSET=utf8;

/*Table structure for table `study_form` */

DROP TABLE IF EXISTS `study_form`;

CREATE TABLE `study_form` (
  `formid` bigint(64) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The unique id of the form.',
  `surveyid` bigint(64) unsigned NOT NULL COMMENT 'The unique id of the survey to which this form belongs to.',
  `title` varchar(255) DEFAULT NULL COMMENT 'The title of the form.',
  PRIMARY KEY (`formid`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

/*Table structure for table `study_question` */

DROP TABLE IF EXISTS `study_question`;

CREATE TABLE `study_question` (
  `questionid` bigint(64) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The unique id of the question.',
  `formid` bigint(64) unsigned NOT NULL COMMENT 'Form id',
  `type` tinyint(2) unsigned NOT NULL COMMENT 'The type of the question.',
  `text` varchar(255) NOT NULL COMMENT 'The text of the question.',
  `mandatory` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - this question is mandatory, 0 - not',
  PRIMARY KEY (`questionid`)
) ENGINE=InnoDB AUTO_INCREMENT=258 DEFAULT CHARSET=utf8;

/*Table structure for table `study_result` */

DROP TABLE IF EXISTS `study_result`;

CREATE TABLE `study_result` (
  `survey_id` bigint(64) unsigned NOT NULL COMMENT 'survey id',
  `form_id` bigint(64) unsigned NOT NULL COMMENT 'form id',
  `question_id` bigint(64) unsigned NOT NULL COMMENT 'question id',
  `result` varchar(255) DEFAULT NULL COMMENT 'result. question was answered...'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `study_survey` */

DROP TABLE IF EXISTS `study_survey`;

CREATE TABLE `study_survey` (
  `surveyid` bigint(64) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The unique id of the survey.',
  `userid` int(10) unsigned NOT NULL COMMENT 'The id of the user that created the survey.',
  `apkid` int(10) unsigned NOT NULL COMMENT 'The id of the apk for which the survey is created.',
  PRIMARY KEY (`surveyid`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usergroupid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL,
  `rgroup` varchar(50) DEFAULT NULL COMMENT 'Name of the research group the user is in',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `usertitle` varchar(250) NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `joindate` int(10) unsigned NOT NULL DEFAULT '0',
  `passworddate` int(10) unsigned NOT NULL,
  `confirmed` int(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
