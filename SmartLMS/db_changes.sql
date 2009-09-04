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