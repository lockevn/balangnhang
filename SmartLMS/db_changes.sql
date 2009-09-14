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
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

ALTER TABLE `smartlms`.`mdl_quiz` 
	ADD COLUMN `smarttype` VARCHAR(10) BINARY NOT NULL DEFAULT 'exercise' AFTER `delay2`;

ALTER TABLE `smartlms`.`mdl_resource`
	ADD COLUMN `smarttype` VARCHAR(10) BINARY NOT NULL DEFAULT 'lecture';

/**
 * phục vụ chức năng hiển thị label cho course section
 */
ALTER TABLE `smartlms`.`mdl_course_sections` 
	ADD COLUMN `label` VARCHAR(50) NOT NULL DEFAULT '' AFTER `visible`;

/**
* Quản lý thẻ, nạp tiền
*/
DROP TABLE IF EXISTS `smartlms`.`mdl_smartcom`;
CREATE TABLE  `smartlms`.`mdl_smartcom` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(20) default 'SmartComModule',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course` (`course`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Defines mail';

DROP TABLE IF EXISTS `smartlms`.`mdl_smartcom_account`;
CREATE TABLE  `smartlms`.`mdl_smartcom_account` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `coinvalue` int(11) NOT NULL,
  `expiredate` date NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `smartlms`.`mdl_smartcom_card`;
CREATE TABLE  `smartlms`.`mdl_smartcom_card` (
  `id` int(11) NOT NULL auto_increment,
  `serialno` varchar(50) NOT NULL COMMENT 'card unique GUID',
  `code` varchar(100) NOT NULL COMMENT 'secret code, scratch code',
  `facevalue` int(11) NOT NULL COMMENT 'VND, meta number printed on card',
  `coinvalue` int(11) NOT NULL COMMENT 'number of coin, to add to account coin',
  `periodvalue` int(11) NOT NULL default '15' COMMENT 'number of valid date,to add/extend to account valid date',
  `batchcode` varchar(50) default NULL COMMENT 'write batch (generate processing) code here',
  `publishdatetime` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'publish date',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 COMMENT='this table contain about prepaid card database of SmartCom';

DROP TABLE IF EXISTS `smartlms`.`mdl_smartcom_card_used`;
CREATE TABLE  `smartlms`.`mdl_smartcom_card_used` (
  `id` int(11) NOT NULL auto_increment,
  `serialno` varchar(50) NOT NULL COMMENT 'card unique GUID',
  `code` varchar(100) NOT NULL COMMENT 'secret code, scratch code',
  `facevalue` int(11) NOT NULL COMMENT 'VND, meta number printed on card',
  `coinvalue` int(11) NOT NULL COMMENT 'number of coin, to add to account coin',
  `periodvalue` int(11) NOT NULL default '15' COMMENT 'number of valid date,to add/extend to account valid date',
  `batchcode` varchar(50) default NULL COMMENT 'write batch (generate processing) code here',
  `publishdatetime` datetime NOT NULL COMMENT 'publish date',
  `useddatetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `depositforusername` varchar(100) NOT NULL COMMENT 'username in which deposited',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `smartlms`.`mdl_smartcom_learning_ticket`;
CREATE TABLE  `smartlms`.`mdl_smartcom_learning_ticket` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `allowday` date NOT NULL COMMENT 'format = 20090920  YYYYMMDD',
  `courseid` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;