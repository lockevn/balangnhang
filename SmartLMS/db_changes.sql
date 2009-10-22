
/**
 * phục vụ chức năng Learning Object management
 */
DROP TABLE IF EXISTS `smartlms`.`mdl_lo`;
CREATE TABLE  `smartlms`.`mdl_lo` (
  `id` bigint(10) NOT NULL auto_increment,
  `category` bigint(10) NOT NULL default '0',
  `instance` bigint(10) NOT NULL,
  `lotype` varchar(255) NOT NULL,
  `cm` bigint(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

ALTER TABLE `smartlms`.`mdl_quiz` 
	ADD COLUMN `lotype` VARCHAR(50) BINARY NOT NULL DEFAULT 'exercise' AFTER `delay2`;

ALTER TABLE `smartlms`.`mdl_resource`
	ADD COLUMN `lotype` VARCHAR(50) BINARY NOT NULL DEFAULT 'lecture';

/**
 * phục vụ chức năng hiển thị label cho course section
 */
ALTER TABLE `smartlms`.`mdl_course_sections` 
	ADD COLUMN `label` VARCHAR(50) NOT NULL DEFAULT '' AFTER `visible`;

/*
 * một số trường để lưu dữ liệu smartweb cũ
 * */
ALTER TABLE `smartlms`.`mdl_user` ADD COLUMN `birthday` BIGINT(10) UNSIGNED AFTER `officecity`;

ALTER TABLE `smartlms`.`mdl_user` ADD COLUMN `district` VARCHAR(45) AFTER `yearofbirth`,
 ADD COLUMN `officeaddress` VARCHAR(100) AFTER `district`,
 ADD COLUMN `officedistrict` VARCHAR(45) AFTER `officeaddress`,
 ADD COLUMN `officecity` VARCHAR(45) AFTER `officedistrict`;




	
	
	
/**
* Quản lý thẻ, nạp tiền
*/


# SQL Manager 2005 for MySQL 3.7.5.1
# ---------------------------------------
# Host     : 192.168.2.198
# Port     : 3306
# Database : smartlms


SET FOREIGN_KEY_CHECKS=0;

USE `smartlms`;

#
# Structure for the `mdl_smartcom` table : 
#

CREATE TABLE `mdl_smartcom` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `course` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) DEFAULT 'SmartComModule',
  PRIMARY KEY (`id`),
  UNIQUE KEY `course` (`course`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Defines for module';

#
# Structure for the `mdl_smartcom_account` table : 
#

CREATE TABLE `mdl_smartcom_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `coinvalue` int(11) NOT NULL,
  `expiredate` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

#
# Structure for the `mdl_smartcom_card` table : 
#

CREATE TABLE `mdl_smartcom_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serialno` varchar(50) NOT NULL COMMENT 'card unique GUID',
  `code` varchar(100) NOT NULL COMMENT 'secret code, scratch code',
  `facevalue` int(11) NOT NULL COMMENT 'VND, meta number printed on card',
  `coinvalue` int(11) NOT NULL COMMENT 'number of coin, to add to account coin',
  `periodvalue` int(11) NOT NULL DEFAULT '15' COMMENT 'number of valid date,to add/extend to account valid date',
  `batchcode` varchar(200) DEFAULT NULL COMMENT 'write batch (generate processing) code here',
  `publishdatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'publish date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='this table contain about prepaid card database of SmartCom';

#
# Structure for the `mdl_smartcom_card_used` table : 
#

CREATE TABLE `mdl_smartcom_card_used` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serialno` varchar(50) NOT NULL COMMENT 'card unique GUID',
  `code` varchar(100) NOT NULL COMMENT 'secret code, scratch code',
  `facevalue` int(11) NOT NULL COMMENT 'VND, meta number printed on card',
  `coinvalue` int(11) NOT NULL COMMENT 'number of coin, to add to account coin',
  `periodvalue` int(11) NOT NULL DEFAULT '15' COMMENT 'number of valid date,to add/extend to account valid date',
  `batchcode` varchar(200) DEFAULT NULL COMMENT 'write batch (generate processing) code here',
  `publishdatetime` datetime NOT NULL COMMENT 'publish date',
  `useddatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `depositforusername` varchar(100) NOT NULL COMMENT 'username in which deposited',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#
# Structure for the `mdl_smartcom_learning_ticket` table : 
#

CREATE TABLE `mdl_smartcom_learning_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `allowday` date NOT NULL COMMENT 'format = 20090920  YYYYMMDD',
  `courseid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `username_courseid_unique` (`username`,`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

	
/**
 * Test room
 */
CREATE TABLE `smartlms`.`mdl_smartcom_testroom` (
  `id` BIGINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `testid` BIGINT(10) UNSIGNED NOT NULL,  
  `mingrade` DOUBLE NOT NULL,
  `maxgrade` DOUBLE NOT NULL,
  `maincourseid` BIGINT(10) UNSIGNED NOT NULL,
	`minorcourseid1` BIGINT(10) UNSIGNED NOT NULL,
`minorcourseid2` BIGINT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;


/**
 * phục vụ chức năng Course Final Exam --> auto suggestion
 */


 
DROP TABLE IF EXISTS `mdl_smartcom_course_completion_suggestion`;

CREATE TABLE `mdl_smartcom_course_completion_suggestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courseid` int(11) DEFAULT NULL,
  `overallquizzespercent` int(11) DEFAULT '60',
  `finalquizid` int(11) DEFAULT NULL,
  `finalquizpercent` int(11) DEFAULT '60',
  `nextcourseidset` varchar(50) DEFAULT NULL,
  `isenable` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `courseid` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




/* Chạy đoạn này để thêm capa vào */
/* vào admin, chỉnh tay, allow capa này cho role Student */
insert into  `mdl_capabilities` 
(`name`, captype,contextlevel, component, riskbitmask)
values
('mod/smartcom:prepaidcardadjust', 'write', 10, 'mod/smartcom', 0),
('mod/smartcom:prepaidcardgenerator', 'write', 10, 'mod/smartcom', 0),
('mod/smartcom:prepaidcardmanager', 'read', 10, 'mod/smartcom', 0),
('mod/smartcom:prepaidcardusagereport', 'read', 10, 'mod/smartcom', 0),
('mod/smartcom:sendnotification', 'read', 10, 'mod/smartcom', 0),
('mod/smartcom:poolingnotification', 'read', 10, 'mod/smartcom', 0),
('mod/smartcom:realtimesupported', 'read', 10, 'mod/smartcom', 0),
('mod/smartcom:buyticket', 'read', 50, 'mod/smartcom', 0),
('mod/smartcom:coursecompletionsuggestconfigure', 'read', 50, 'mod/smartcom', 0),
('mod/smartcom:realtimeperformancecheck', 'write', 50, 'mod/smartcom', 0)


