-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generato il: 10 Dic, 2005 at 12:02 PM
-- Versione MySQL: 4.1.13
-- Versione PHP: 4.4.0
-- 
-- Database: `docebo30`
-- 

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_file`
-- 

CREATE TABLE IF NOT EXISTS `kms_file` (
  `file_id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) NOT NULL default '0',
  `cv_id` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`file_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_file_perm`
-- 

CREATE TABLE IF NOT EXISTS `kms_file_perm` (
  `file_id` int(11) NOT NULL default '0',
  `perm_name` varchar(255) NOT NULL default '',
  `idst` int(11) NOT NULL default '0',
  PRIMARY KEY  (`file_id`,`perm_name`,`idst`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_file_version`
-- 

CREATE TABLE IF NOT EXISTS `kms_file_version` (
  `version_id` int(11) NOT NULL auto_increment,
  `file_id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `file_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ctype` enum('none','upgrade','downgrade') NOT NULL default 'none',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`version_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_filesystem`
-- 

CREATE TABLE IF NOT EXISTS `kms_filesystem` (
  `id_dir` int(11) NOT NULL default '0',
  `lang_code` varchar(50) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_dir`,`lang_code`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_filesystem_field`
-- 

CREATE TABLE IF NOT EXISTS `kms_filesystem_field` (
  `idst` varchar(14) NOT NULL default '0',
  `id_field` int(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_filesystem_fieldentry`
-- 

CREATE TABLE IF NOT EXISTS `kms_filesystem_fieldentry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_filesystem_tree`
-- 

CREATE TABLE IF NOT EXISTS `kms_filesystem_tree` (
  `folder_id` int(11) NOT NULL auto_increment,
  `d_folder_id` int(11) NOT NULL default '0',
  `idParent` int(11) NOT NULL default '0',
  `path` text NOT NULL,
  `lev` int(3) NOT NULL default '0',
  `flow_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`folder_id`)
) TYPE=MyISAM PACK_KEYS=0;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_monitorized`
-- 

CREATE TABLE IF NOT EXISTS `kms_monitorized` (
  `item_type` varchar(20) NOT NULL default '',
  `item_id` int(11) NOT NULL default '0',
  `user_idst` int(11) NOT NULL default '0',
  PRIMARY KEY  (`item_type`,`item_id`,`user_idst`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_news`
-- 

CREATE TABLE IF NOT EXISTS `kms_news` (
  `idNews` int(11) NOT NULL auto_increment,
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL default '',
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idNews`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_pflow_log`
-- 

CREATE TABLE IF NOT EXISTS `kms_pflow_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `modname` varchar(100) NOT NULL default '',
  `item` varchar(100) NOT NULL default '',
  `key1` int(11) NOT NULL default '0',
  `key2` int(11) default NULL,
  `step_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `ctime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ctype` enum('none','upgrade','downgrade') NOT NULL default 'none',
  `note` text NOT NULL,
  PRIMARY KEY  (`log_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_pflow_status`
-- 

CREATE TABLE IF NOT EXISTS `kms_pflow_status` (
  `modname` varchar(100) NOT NULL default '',
  `item` varchar(100) NOT NULL default '',
  `key1` int(11) NOT NULL default '0',
  `key2` int(11) NOT NULL default '0',
  `step_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`modname`,`item`,`key1`,`key2`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_sysforum`
-- 

CREATE TABLE IF NOT EXISTS `kms_sysforum` (
  `idMessage` int(11) NOT NULL auto_increment,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) NOT NULL default '0',
  `key3` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_webpages`
-- 

CREATE TABLE IF NOT EXISTS `kms_webpages` (
  `idPages` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `language` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `in_home` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idPages`)
) TYPE=MyISAM;



-- 
-- Struttura della tabella `kms_menu`
-- 

CREATE TABLE IF NOT EXISTS `kms_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idMenu`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=2 ;

-- 
-- Dump dei dati per la tabella `kms_menu`
-- 

INSERT INTO `kms_menu` VALUES (1, '_GENERAL_KMS', 'general.gif', 1);

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_menu_under`
-- 

CREATE TABLE IF NOT EXISTS `kms_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idUnder`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dump dei dati per la tabella `kms_menu_under`
-- 

INSERT INTO `kms_menu_under` VALUES (1, 1, 'news', '_NEWS', 'news', 'view', 1);
INSERT INTO `kms_menu_under` VALUES (2, 1, 'webpages', '_WEBPAGES', 'webpages', 'view', 2);

-- --------------------------------------------------------

-- 
-- Struttura della tabella `kms_setting`
-- 

CREATE TABLE IF NOT EXISTS `kms_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`param_name`)
) TYPE=MyISAM;

-- 
-- Dump dei dati per la tabella `kms_setting`
-- 

INSERT INTO `kms_setting` VALUES ('kms_version', '3.0', 'string', 255, 0, 0, 1, 1);
INSERT INTO `kms_setting` VALUES ('allow_subdir_creation', 'off', 'enum', 3, 0, 1, 1, 0);
INSERT INTO `kms_setting` VALUES ('mantype', 'both', 'mantype_chooser', 10, 0, 2, 1, 0);
INSERT INTO `kms_setting` VALUES ('ttlSession', '2000', 'int', 5, 0, 3, 1, 0);
INSERT INTO `kms_setting` VALUES ('use_accesskey', 'on', 'enum', 3, 0, 4, 1, 0);
INSERT INTO `kms_setting` VALUES ('kms_show_to_anonymous', 'on', 'enum', 3, 0, 5, 1, 0);
INSERT INTO `kms_setting` VALUES ('visuWebPages', '10', 'int', 5, 1, 1, 1, 0);
INSERT INTO `kms_setting` VALUES ('visuNews', '10', 'int', 5, 1, 2, 1, 0);
INSERT INTO `kms_setting` VALUES ('visuNewsHomePage', '4', 'int', 5, 1, 3, 1, 1);
INSERT INTO `kms_setting` VALUES ('activeNews', 'on', 'enum', 3, 0, 6, 1, 0);
INSERT INTO `kms_setting` VALUES ('feature_comments', 'on', 'enum', 3, 2, 1, 1, 0);
INSERT INTO `kms_setting` VALUES ('url', '', 'string', 255, 0, 7, 1, 0);
INSERT INTO `kms_setting` VALUES ('defaultTemplate', 'standard', 'template', 255, 0, 8, 1, 0);
