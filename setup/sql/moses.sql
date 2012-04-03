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
  `session_id` char(32) CHARACTER SET utf8 NOT NULL,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `apk` */

DROP TABLE IF EXISTS `apk`;

CREATE TABLE `apk` (
  `apkid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'apk id',
  `apktitle` varchar(255) DEFAULT NULL COMMENT 'name of the programm',
  `apkname` varchar(255) NOT NULL COMMENT 'name of the apk (file)',
  `userhash` varchar(32) NOT NULL COMMENT 'user''s hash value',
  `apkhash` varchar(32) NOT NULL COMMENT 'hash of the apk',
  `apk_version` varchar(32) DEFAULT NULL COMMENT 'version of the apk',
  `userid` int(10) unsigned NOT NULL COMMENT 'user''s id',
  `sensors` varchar(255) NOT NULL COMMENT 'list of all sensors used by apk',
  `description` text COMMENT 'short description of the apk',
  `androidversion` varchar(32) DEFAULT NULL COMMENT 'minimal android version needed to run the apk',
  `restriction_device_number` int(20) NOT NULL DEFAULT '-1' COMMENT 'number of users allowed to install the app',
  `participated_count` int(32) unsigned NOT NULL DEFAULT '0' COMMENT 'number of users that have installed the app',
  `candidates` mediumtext COMMENT 'all users that can install the app',
  `notified_devices` mediumtext COMMENT 'notification has been sent',
  `pending_devices` mediumtext COMMENT 'waiting for this users to install the app',
  `last_round_time` int(10) DEFAULT '0' COMMENT 'the last time the cron-job has started',
  `ustudy_finished` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'TRUE only when user study for an uploaded apk is finished, otherwise FALSE',
  `installed_on` mediumtext COMMENT 'list of devices having the app currently installed',
  `locked` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 only when the app should be shown to clients in a user-study, else 0',
  PRIMARY KEY (`apkid`)
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=utf8;

/*Table structure for table `hardware` */

DROP TABLE IF EXISTS `hardware`;

CREATE TABLE `hardware` (
  `hwid` int(32) unsigned NOT NULL AUTO_INCREMENT COMMENT 'hardware id assigned by MoSeS',
  `uid` int(32) unsigned NOT NULL COMMENT 'user id',
  `deviceid` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'device id selected by the user',
  `modelname` varchar(255) NOT NULL COMMENT 'the name of the device''s model',
  `vendorname` varchar(255) NOT NULL COMMENT 'the name of the device''s vendor',
  `androidversion` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT 'android version of the device',
  `sensors` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'all sensors on the device',
  `filter` varchar(255) DEFAULT '[]' COMMENT 'filter set by the user for this device',
  `c2dm` varchar(1024) DEFAULT NULL COMMENT 'c2dm id of the device',
  PRIMARY KEY (`uid`,`deviceid`),
  KEY `hwid` (`hwid`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

/*Table structure for table `request` */

DROP TABLE IF EXISTS `request`;

CREATE TABLE `request` (
  `uid` int(10) unsigned NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `reason` text,
  `accepted` smallint(2) unsigned NOT NULL DEFAULT '0',
  `pending` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `rgroup` */

DROP TABLE IF EXISTS `rgroup`;

CREATE TABLE `rgroup` (
  `name` varchar(50) NOT NULL COMMENT 'name of a research group',
  `password` varchar(32) NOT NULL COMMENT 'password needed to join the group',
  `members` varchar(256) NOT NULL COMMENT 'ids of the users in the group',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usergroupid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rgroup` varchar(50) DEFAULT NULL COMMENT 'Name of the research group the user is in',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `usertitle` varchar(250) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `joindate` int(10) unsigned NOT NULL DEFAULT '0',
  `passworddate` int(10) unsigned NOT NULL,
  `confirmed` int(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
