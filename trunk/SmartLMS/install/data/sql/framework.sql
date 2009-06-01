-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 04 Lug, 2008 at 11:51 AM
-- Versione MySQL: 5.0.51
-- Versione PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `docebo_3504`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `core_admin_course`
--

CREATE TABLE `core_admin_course` (
  `idst_user` int(11) NOT NULL default '0',
  `type_of_entry` varchar(50) NOT NULL default '',
  `id_entry` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idst_user`,`type_of_entry`,`id_entry`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_admin_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_admin_tree`
--

CREATE TABLE `core_admin_tree` (
  `idst` varchar(11) NOT NULL default '',
  `idstAdmin` varchar(11) NOT NULL default '',
  PRIMARY KEY  (`idst`,`idstAdmin`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_admin_tree`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_calendar`
--

CREATE TABLE `core_calendar` (
  `id` bigint(20) NOT NULL auto_increment,
  `class` varchar(30) default NULL,
  `create_date` datetime default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `title` varchar(255) default NULL,
  `description` text,
  `private` varchar(2) default NULL,
  `category` varchar(255) default NULL,
  `type` bigint(20) default NULL,
  `visibility_rules` tinytext,
  `_owner` int(11) default NULL,
  `_day` smallint(2) default NULL,
  `_month` smallint(2) default NULL,
  `_year` smallint(4) default NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company`
--

CREATE TABLE `core_company` (
  `company_id` int(11) NOT NULL auto_increment,
  `code` varchar(255) default NULL,
  `name` varchar(255) NOT NULL default '',
  `ctype_id` int(11) NOT NULL default '0',
  `cstatus_id` int(11) NOT NULL default '0',
  `address` text NOT NULL,
  `tel` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `vat_number` varchar(255) NOT NULL default '',
  `restricted_access` tinyint(1) NOT NULL default '0',
  `is_used` tinyint(1) NOT NULL default '0',
  `imported_from_connection` varchar(255) default NULL,
  PRIMARY KEY  (`company_id`),
  UNIQUE KEY `code` (`code`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_companystatus`
--

CREATE TABLE `core_companystatus` (
  `cstatus_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cstatus_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_companystatus`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_companytype`
--

CREATE TABLE `core_companytype` (
  `ctype_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ctype_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_companytype`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company_field`
--

CREATE TABLE `core_company_field` (
  `idst` varchar(14) NOT NULL default '0',
  `id_field` int(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company_fieldentry`
--

CREATE TABLE `core_company_fieldentry` (
  `id_common` int(11) NOT NULL default '0',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company_fieldentry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company_user`
--

CREATE TABLE `core_company_user` (
  `company_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`user_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_connection`
--

CREATE TABLE `core_connection` (
  `name` varchar(50) NOT NULL default '',
  `description` varchar(255) default NULL,
  `type` varchar(50) NOT NULL default '',
  `params` text,
  PRIMARY KEY  (`name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_connection`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_connector`
--

CREATE TABLE `core_connector` (
  `type` varchar(25) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `class` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`type`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_connector`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_country`
--

CREATE TABLE `core_country` (
  `id_country` int(11) NOT NULL auto_increment,
  `name_country` varchar(64) NOT NULL default '',
  `iso_code_country` varchar(3) NOT NULL default '',
  `id_zone` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_country`),
  KEY `IDX_COUNTRIES_NAME` (`name_country`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_country`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_deleted_user`
--

CREATE TABLE `core_deleted_user` (
  `id_deletion` int(11) NOT NULL auto_increment,
  `idst` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `signature` text NOT NULL,
  `level` int(11) NOT NULL default '0',
  `lastenter` datetime NOT NULL default '0000-00-00 00:00:00',
  `valid` tinyint(1) NOT NULL default '0',
  `pwd_expire_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `register_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deletion_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_by` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_deletion`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_deleted_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_event`
--

CREATE TABLE `core_event` (
  `idEvent` int(11) NOT NULL auto_increment,
  `idClass` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `priority` smallint(1) unsigned NOT NULL default '1289',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idEvent`),
  KEY `idClass` (`idClass`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event`
--

INSERT INTO `core_event` (`idEvent`, `idClass`, `module`, `section`, `priority`, `description`) VALUES
(1, 40, 'configuration', 'edit', 1, 'Config for user_manager updated'),
(2, 40, 'configuration', 'edit', 1, 'Config for 0 updated'),
(3, 40, 'configuration', 'edit', 1, 'Config for 8 updated'),
(4, 40, 'configuration', 'edit', 1, 'Config for suiteman updated'),
(5, 40, 'configuration', 'edit', 1, 'Config for 0 updated'),
(6, 40, 'configuration', 'edit', 1, 'Config for 1 updated'),
(7, 40, 'configuration', 'edit', 1, 'Config for 0 updated'),
(8, 40, 'configuration', 'edit', 1, 'Config for 3 updated'),
(9, 40, 'configuration', 'edit', 1, 'Config for 4 updated'),
(10, 40, 'configuration', 'edit', 1, 'Config for 5 updated'),
(11, 40, 'configuration', 'edit', 1, 'Config for 7 updated'),
(12, 40, 'configuration', 'edit', 1, 'Config for 0 updated');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_class`
--

CREATE TABLE `core_event_class` (
  `idClass` int(11) NOT NULL auto_increment,
  `class` varchar(50) NOT NULL default '',
  `platform` varchar(50) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`idClass`),
  UNIQUE KEY `class_2` (`class`),
  KEY `class` (`class`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_class`
--

INSERT INTO `core_event_class` (`idClass`, `class`, `platform`, `description`) VALUES
(1, 'UserNew', 'framework', '_EVENT_USERNEW'),
(2, 'UserMod', 'framework', '_EVENT_USERMOD'),
(3, 'UserDel', 'framework', '_EVENT_USERDEL'),
(4, 'UserNewModerated', 'framework', '_EVENT_USERNEWMODERATED'),
(5, 'UserGroupModerated', 'framework', '_EVENT_USERGROUPMODERATED'),
(6, 'UserGroupInsert', 'framework', '_EVENT_USERGROUPINSERTED'),
(7, 'UserGroupRemove', 'framework', '_EVENT_USERGROUPREMOVED'),
(8, 'UserCourseInsertModerate', 'lms-a', '_EVENT_USERCOURSEINSERTMODEATE'),
(9, 'UserCourseInserted', 'lms-a', '_EVENT_USERCOURSEINSERTED'),
(10, 'UserCourseRemoved', 'lms-a', '_EVENT_USERCOURSEREMOVED'),
(11, 'UserCourseLevelChanged', 'lms-a', '_EVENT_USERCOURSELEVELCHANGED'),
(12, 'UserCourseEnded', 'lms-a', '_EVENT_USERCOURSEENDED'),
(13, 'CoursePorpModified', 'lms-a', '_EVENT_COURSEPROPMODIFIED'),
(14, 'AdviceNew', 'lms', '_EVENT_ADVICENEW'),
(15, 'MsgNewReceived', 'lms', '_EVENT_MSGNEWRECEIVED'),
(16, 'ForumNewCategory', 'lms', '_EVENT_FORUMNEWCATEGORY'),
(17, 'ForumNewThread', 'lms', '_EVENT_FORUMNEWTHERAD'),
(18, 'ForumNewResponse', 'lms', '_EVENT_FORUMNEWRESPONSE'),
(19, 'NewsCreated', 'cms-a', '_EVENT_NEWSCREATED'),
(20, 'MediaCreated', 'cms-a', '_EVENT_MEDIACREATED'),
(21, 'DocumentCreated', 'cms-a', '_EVENT_DOCUMENTCREATED'),
(22, 'ContentCreated', 'cms-a', '_EVENT_CONTENTCREATED'),
(23, 'PageCreated', 'cms-a', '_EVENT_PAGECREATED'),
(24, 'NewsModified', 'cms-a', '_EVENT_NEWSMODIFIED'),
(25, 'MediaModified', 'cms-a', '_EVENT_MEDIAMODIFIED'),
(26, 'DocumentModified', 'cms-a', '_EVENT_DOCUMENTMODIFIED'),
(27, 'ContentModified', 'cms-a', '_EVENT_CONTENTMODIFIED'),
(28, 'PageModified', 'cms-a', '_EVENT_PAGEMODIFIED'),
(29, 'FlowApprovation', 'cms-a', '_EVENT_FLOWAPPROVATION'),
(30, 'NewletterReceived', 'cms-a', '_EVENT_NEWSLETTERRECEIVED'),
(31, 'CmsForumNewCategory', 'cms', '_EVENT_CMSFORUMNEWCATEGORY'),
(32, 'CmsForumNewThread', 'cms', '_EVENT_CMSFORUMNEWTHREAD'),
(33, 'CmsForumNewResponse', 'cms', '_EVENT_CMSFORUMNEWRESPONSE'),
(38, 'UserApproved', 'framework', '_EVENT_USERAPPROVED'),
(39, 'UserCourseBuy', 'lms', '_EVENT_USERCOURSEBUY'),
(40, 'SettingUpdate', 'framework', '_CONFIG_SETTINGS_HAS_BEEN_UPDATED');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_consumer`
--

CREATE TABLE `core_event_consumer` (
  `idConsumer` int(11) NOT NULL auto_increment,
  `consumer_class` varchar(50) NOT NULL default '',
  `consumer_file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idConsumer`),
  UNIQUE KEY `consumer_class` (`consumer_class`)
)  DEFAULT CHARSET=utf8 COMMENT='Table of consumer with PHP classes and files';

--
-- Dump dei dati per la tabella `core_event_consumer`
--

INSERT INTO `core_event_consumer` (`idConsumer`, `consumer_class`, `consumer_file`) VALUES
(1, 'DoceboUserNotifier', '/lib/lib.usernotifier.php'),
(2, 'DoceboCourseNotifier', '/lib/lib.coursenotifier.php'),
(3, 'DoceboOrgchartNotifier', '/lib/lib.orgchartnotifier.php'),
(4, 'DoceboCompanyNotifier', '/lib/lib.companynotifier.php'),
(5, 'DoceboSettingNotifier', '/lib/lib.settingnotifier.php');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_consumer_class`
--

CREATE TABLE `core_event_consumer_class` (
  `idConsumer` int(11) NOT NULL default '0',
  `idClass` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idConsumer`,`idClass`)
) DEFAULT CHARSET=utf8 COMMENT='n:m relation from consumers and event''s classes';

--
-- Dump dei dati per la tabella `core_event_consumer_class`
--

INSERT INTO `core_event_consumer_class` (`idConsumer`, `idClass`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(2, 3),
(3, 3),
(4, 3),
(5, 40);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_manager`
--

CREATE TABLE `core_event_manager` (
  `idEventMgr` int(11) NOT NULL auto_increment,
  `idClass` int(11) NOT NULL default '0',
  `permission` enum('not_used','mandatory','user_selectable') NOT NULL default 'not_used',
  `channel` set('email','sms') NOT NULL default 'email',
  `recipients` varchar(255) NOT NULL default '',
  `show_level` set('godadmin','admin','user') NOT NULL default '',
  PRIMARY KEY  (`idEventMgr`),
  UNIQUE KEY `idClass` (`idClass`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_manager`
--

INSERT INTO `core_event_manager` (`idEventMgr`, `idClass`, `permission`, `channel`, `recipients`, `show_level`) VALUES
(1, 1, 'user_selectable', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(2, 2, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(3, 3, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(4, 4, 'not_used', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin'),
(5, 5, 'not_used', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin'),
(6, 6, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(7, 7, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(8, 8, 'not_used', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin'),
(9, 9, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(10, 10, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(11, 11, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(12, 12, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(13, 13, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER_GOD', 'godadmin,admin,user'),
(14, 14, 'not_used', 'email', '_EVENT_RECIPIEMTS_COURSEUSERS', 'godadmin,admin,user'),
(15, 15, 'not_used', 'email', '_EVENT_RECIPIEMTS_COURSEUSERS', 'godadmin,admin,user'),
(16, 16, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(17, 17, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(18, 18, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(19, 19, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(20, 20, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(21, 21, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(22, 22, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(23, 23, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(24, 24, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(25, 25, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(26, 26, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(27, 27, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(28, 28, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(29, 29, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(30, 30, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(31, 31, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(32, 32, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(33, 33, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin,user'),
(34, 34, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user'),
(35, 35, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user'),
(36, 36, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user'),
(37, 37, 'user_selectable', 'email', '_EVENT_RECIPIENTS_FLOWMANAGER_OPERATOR', 'godadmin,admin,user'),
(38, 38, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(39, 39, 'not_used', 'email', 'user', 'godadmin,admin,user');


-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_property`
--

CREATE TABLE `core_event_property` (
  `idEvent` int(11) NOT NULL default '0',
  `property_name` varchar(50) NOT NULL default '',
  `property_value` text NOT NULL,
  `property_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`idEvent`,`property_name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_property`
--

INSERT INTO `core_event_property` (`idEvent`, `property_name`, `property_value`, `property_date`) VALUES
(1, 'field_saved', 'mail_sender - register_type - max_log_attempt - save_log_attempt - pass_min_char - pass_max_time_valid - hour_request_limit - privacy_policy - register_tree - field_tree', '2007-08-01'),
(1, '_event_log', 'processed - DoceboEventConsumer - 2007-08-01', '2007-08-01'),
(2, 'field_saved', 'url - default_language - defaultTemplate - ttlSession - sender_event - hteditor - htmledit_image_godadmin - htmledit_image_admin - htmledit_image_user - visuItem - visuUser - default_pubflow_method - user_quota', '2007-08-01'),
(2, '_event_log', 'processed - DoceboEventConsumer - 2007-08-01', '2007-08-01'),
(3, 'field_saved', 'google_stat_code', '2007-08-01'),
(4, 'field_saved', '', '2007-08-01'),
(5, 'field_saved', 'use_social_courselist - url - admin_mail - ttlSession - tracking - course_quota', '2007-08-01'),
(6, 'field_saved', 'visuItem - visu_course - defaultTemplate - activeNews - course_list_plan - course_block - max_pdp_answer - on_catalogue_empty - home_course_catalogue', '2007-08-01'),
(7, 'field_saved', 'url - default_language - defaultTemplate - ttlSession - sender_event - hteditor - htmledit_image_godadmin - htmledit_image_admin - htmledit_image_user - visuItem - visuUser - default_pubflow_method - user_quota', '2007-08-01'),
(8, 'field_saved', 'stop_concurrent_user', '2007-08-01'),
(9, 'field_saved', 'user_use_coursecatalogue - user_use_profile', '2007-08-01'),
(10, 'field_saved', 'first_coursecatalogue_tab', '2007-08-01'),
(11, 'field_saved', 'use_course_catalogue', '2007-08-01'),
(12, 'field_saved', 'url - cms_admin_mail - ttlSession - defaultCmsTemplate - defaultCmsLanguage - cms_previewimg_maxsize - anonymous_comment - use_mod_rewrite - grpsel_type - cms_use_dropdown_menu', '2007-08-01');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_user`
--

CREATE TABLE `core_event_user` (
  `idEventMgr` int(11) NOT NULL default '0',
  `idst` int(11) NOT NULL default '0',
  `channel` set('email','sms') NOT NULL default '',
  PRIMARY KEY  (`idEventMgr`,`idst`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_faq`
--

CREATE TABLE `core_faq` (
  `faq_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `answer` text NOT NULL,
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`faq_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_faq`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_faq_cat`
--

CREATE TABLE `core_faq_cat` (
  `category_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_faq_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_feed_cache`
--

CREATE TABLE `core_feed_cache` (
  `feed_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `refresh_time` int(5) NOT NULL default '0',
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `show_on_platform` text NOT NULL,
  `zone` varchar(255) NOT NULL default 'public',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`feed_id`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_feed_cache`
--

INSERT INTO `core_feed_cache` (`feed_id`, `title`, `url`, `image`, `content`, `active`, `refresh_time`, `last_update`, `show_on_platform`, `zone`, `ord`) VALUES
(1, 'Docebo.org Feed', 'http://www.docebo.org/doceboCms/feed.php?alias=news', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 0),
(2, 'Bugs Feed', 'http://www.docebo.org/doceboCms/feed.php?alias=fixed_bugs&lang=english', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 1),
(3, 'Docebo Corporate Blog', 'http://feeds.feedburner.com/DoceboCorpBlog', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_feed_out`
--

CREATE TABLE `core_feed_out` (
  `feed_id` int(11) NOT NULL auto_increment,
  `alias` varchar(100) default NULL,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) default NULL,
  `platform` varchar(255) NOT NULL default '',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `regenerate` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`feed_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_feed_out`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field`
--

CREATE TABLE `core_field` (
  `idField` int(11) NOT NULL auto_increment,
  `id_common` int(11) NOT NULL default '0',
  `type_field` varchar(255) NOT NULL default '',
  `lang_code` varchar(255) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `show_on_platform` varchar(255) NOT NULL default 'framework,',
  `use_multilang` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idField`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_son`
--

CREATE TABLE `core_field_son` (
  `idSon` int(11) NOT NULL auto_increment,
  `idField` int(11) NOT NULL default '0',
  `id_common_son` int(11) NOT NULL default '0',
  `lang_code` varchar(50) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`idSon`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_son`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_template`
--

CREATE TABLE `core_field_template` (
  `id_common` int(11) NOT NULL default '0',
  `ref_id` int(11) NOT NULL default '0',
  `template_code` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_common`,`ref_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_template`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_type`
--

CREATE TABLE `core_field_type` (
  `type_field` varchar(255) NOT NULL default '',
  `type_file` varchar(255) NOT NULL default '',
  `type_class` varchar(255) NOT NULL default '',
  `type_category` varchar(255) NOT NULL default 'standard',
  PRIMARY KEY  (`type_field`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_type`
--

INSERT INTO `core_field_type` (`type_field`, `type_file`, `type_class`, `type_category`) VALUES
('codicefiscale', 'class.cf.php', 'Field_Cf', 'standard'),
('date', 'class.date.php', 'Field_Date', 'standard'),
('dropdown', 'class.dropdown.php', 'Field_Dropdown', 'standard'),
('freetext', 'class.freetext.php', 'Field_Freetext', 'standard'),
('gmail', 'class.gmail.php', 'CField_Gmail', 'contact'),
('icq', 'class.icq.php', 'CField_Icq', 'contact'),
('msn', 'class.msn.php', 'CField_Msn', 'contact'),
('skype', 'class.skype.php', 'CField_Skype', 'contact'),
('textfield', 'class.textfield.php', 'Field_Textfield', 'standard'),
('upload', 'class.upload.php', 'Field_Upload', 'standard'),
('yahoo', 'class.yahoo.php', 'CField_Yahoo', 'contact'),
('yesno', 'class.yesno.php', 'Field_Yesno', 'standard');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_userentry`
--

CREATE TABLE `core_field_userentry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_userentry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_group`
--

CREATE TABLE `core_group` (
  `idst` int(11) NOT NULL default '0',
  `groupid` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `hidden` enum('true','false') NOT NULL default 'false',
  `type` enum('free','moderate','private','invisible','course','company') NOT NULL default 'free',
  `show_on_platform` text NOT NULL,
  PRIMARY KEY  (`idst`),
  UNIQUE KEY `groupid` (`groupid`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group`
--

INSERT INTO `core_group` (`idst`, `groupid`, `description`, `hidden`, `type`, `show_on_platform`) VALUES
(1, '/oc_0', 'Root of organization chart', 'true', 'free', 'framework,'),
(2, '/ocd_0', 'Root of organization chart and descendants', 'true', 'free', 'framework,'),
(3, '/framework/level/godadmin', 'Group of godadmins', 'true', 'free', 'framework, '),
(4, '/framework/level/admin', 'Group of administrators', 'true', 'free', 'framework, '),
(5, '/framework/level/user', 'Group of normal users', 'true', 'free', 'framework,'),
(6, '/framework/orgchart/fields', 'Used for orgchart field assignement', 'true', 'free', 'framework,'),
(7, '/pubflow_step_1', 'Pubblication flow step group', 'true', 'free', 'framework,'),
(8, '/pubflow_step_2', 'Pubblication flow step group', 'true', 'free', 'framework,'),
(9, '/pubflow_step_3', 'Pubblication flow step group', 'true', 'free', 'framework,'),
(10, '/framework/company/fields', 'Used to associate a set of fields', 'true', 'free', ''),
(271, '/lms/custom/1/7', 'for custom lms menu', 'true', 'free', ''),
(272, '/lms/custom/1/6', 'for custom lms menu', 'true', 'free', ''),
(273, '/lms/custom/1/5', 'for custom lms menu', 'true', 'free', ''),
(274, '/lms/custom/1/4', 'for custom lms menu', 'true', 'free', ''),
(275, '/lms/custom/1/3', 'for custom lms menu', 'true', 'free', ''),
(276, '/lms/custom/1/2', 'for custom lms menu', 'true', 'free', ''),
(277, '/lms/custom/1/1', 'for custom lms menu', 'true', 'free', ''),
(1011, '/framework/level/publicadmin', 'Group of Public Admin', 'true', 'free', 'framework, ');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_group_fields`
--

CREATE TABLE `core_group_fields` (
  `idst` int(11) NOT NULL default '0',
  `id_field` int(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group_fields`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_group_members`
--

CREATE TABLE `core_group_members` (
  `idst` int(11) NOT NULL default '0',
  `idstMember` int(11) NOT NULL default '0',
  `filter` varchar(50) NOT NULL default '',
  UNIQUE KEY `unique_relation` (`idst`,`idstMember`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group_members`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_group_user_waiting`
--

CREATE TABLE `core_group_user_waiting` (
  `idst_group` int(11) NOT NULL default '0',
  `idst_user` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idst_group`,`idst_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group_user_waiting`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_hteditor`
--

CREATE TABLE `core_hteditor` (
  `hteditor` varchar(255) NOT NULL default '',
  `hteditorname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`hteditor`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_hteditor`
--

INSERT INTO `core_hteditor` (`hteditor`, `hteditorname`) VALUES
('tinymce', '_TINYMCE'),
('textarea', '_TEXTAREA'),
('yui', '_YUI');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_lang_language`
--

CREATE TABLE `core_lang_language` (
  `lang_code` varchar(50) NOT NULL default '',
  `lang_description` varchar(255) NOT NULL default '',
  `lang_charset` varchar(20) NOT NULL default 'utf-8',
  `lang_browsercode` varchar(50) NOT NULL default '',
  `lang_direction` enum('ltr','rtl') NOT NULL default 'ltr',
  PRIMARY KEY  (`lang_code`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_lang_language`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_lang_text`
--

CREATE TABLE `core_lang_text` (
  `id_text` int(11) NOT NULL auto_increment,
  `text_key` varchar(50) NOT NULL default '',
  `text_module` varchar(50) NOT NULL default '',
  `text_platform` varchar(50) NOT NULL default '',
  `text_description` varchar(255) NOT NULL default '',
  `text_attributes` set('accessibility','sms','email') NOT NULL default '',
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `text_key` (`text_key`,`text_module`,`text_platform`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_lang_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_lang_translation`
--

CREATE TABLE `core_lang_translation` (
  `id_translation` int(11) NOT NULL auto_increment,
  `translation_text` text,
  `lang_code` varchar(50) NOT NULL default '',
  `save_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_text` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_translation`),
  KEY `lang_code` (`lang_code`,`id_text`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_lang_translation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_menu`
--

CREATE TABLE `core_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_menu`
--

INSERT INTO `core_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '', '', 1, 'true'),
(2, '_USER_MANAGMENT', '', 2, 'false'),
(3, '_TRASV_MANAGMENT', '', 3, 'false'),
(4, '_PERMISSION', '', 4, 'false'),
(5, '_LANGUAGE', '', 5, 'false'),
(6, '', '', 6, 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_menu_under`
--

CREATE TABLE `core_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_menu_under`
--

INSERT INTO `core_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'dashboard', '_DASHBOARD', 'dashboard', 'view', NULL, 1, 'class.dashboard.php', 'Module_Dashboard'),
(2, 2, 'directory', '_LISTUSER', 'org_chart', 'view_org_chart', NULL, 1, 'class.directory.php', 'Module_Directory'),
(3, 2, 'directory', '_LISTGROUP', 'listgroup', 'view_group', NULL, 2, 'class.directory.php', 'Module_Directory'),
(4, 2, 'field_manager', '_FIELD_MANAGER', 'field_list', 'view', NULL, 4, 'class.field_manager.php', 'Module_Field_Manager'),
(5, 3, 'configuration', '_CONFIGURATION', 'config', 'view', NULL, 1, 'class.configuration.php', 'Module_Configuration'),
(7, 3, 'event_manager', '_EVENTMANAGER', 'display', 'view_event_manager', NULL, 3, 'class.event_manager.php', 'Module_Event_Manager'),
(8, 3, 'iotask', '_IOTASK', 'iotask', 'view', NULL, 4, 'class.iotask.php', 'Module_IOTask'),
(9, 4, 'admin_manager', '_ADMIN_MANAGER', 'view', 'view', NULL, 1, 'class.admin_manager.php', 'Module_Admin_Manager'),
(10, 5, 'lang', '_LANG', 'lang', 'view', NULL, 1, 'class.lang.php', 'Module_Lang'),
(11, 5, 'lang', '_LANG_IMPORT_EXPORT', 'importexport', 'view', NULL, 2, 'class.lang.php', 'Module_Lang'),
(12, 5, 'regional_settings', '_REGSET', 'regset', 'view', NULL, 3, 'class.regional_settings.php', 'Module_Regional_settings'),
(13, 6, 'newsletter', '_NEWSLETTER', 'newsletter', 'view', NULL, 1, 'class.newsletter.php', 'Module_Newsletter'),
(14, 2, 'company', '_COMPANY', 'main', 'view', 'crm', 3, 'class.company.php', 'Module_Company'),
(15, 4, 'public_admin_manager', '_PUBLIC_ADMIN_MANAGER', 'view', 'view', NULL, 2, 'class.public_admin_manager.php', 'Module_Public_Admin_Manager');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_message`
--

CREATE TABLE `core_message` (
  `idMessage` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `sender` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `attach` varchar(255) NOT NULL default '',
  `priority` int(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_message`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_message_user`
--

CREATE TABLE `core_message_user` (
  `idMessage` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `read` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`,`idUser`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_message_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_newsletter`
--

CREATE TABLE `core_newsletter` (
  `id` int(11) NOT NULL auto_increment,
  `id_send` int(11) NOT NULL default '0',
  `sub` varchar(255) NOT NULL default '',
  `msg` text NOT NULL,
  `fromemail` varchar(255) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  `tot` int(11) NOT NULL default '0',
  `send_type` enum('email','sms') NOT NULL default 'email',
  `stime` datetime NOT NULL default '0000-00-00 00:00:00',
  `file` text NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_newsletter`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_newsletter_sendto`
--

CREATE TABLE `core_newsletter_sendto` (
  `id_send` int(11) NOT NULL default '0',
  `idst` int(11) NOT NULL default '0',
  `stime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_send`,`idst`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_newsletter_sendto`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart`
--

CREATE TABLE `core_org_chart` (
  `id_dir` int(11) NOT NULL default '0',
  `lang_code` varchar(50) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_dir`,`lang_code`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_field`
--

CREATE TABLE `core_org_chart_field` (
  `idst` int(11) NOT NULL default '0',
  `id_field` varchar(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('readonly','readwrite','noaccess') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_fieldentry`
--

CREATE TABLE `core_org_chart_fieldentry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_fieldentry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_tree`
--

CREATE TABLE `core_org_chart_tree` (
  `idOrg` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` text NOT NULL,
  `lev` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idOrg`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_tree`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_user`
--

CREATE TABLE `core_org_chart_user` (
  `id_org` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0'
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_pflow_lang`
--

CREATE TABLE `core_pflow_lang` (
  `id` int(11) NOT NULL default '0',
  `type` varchar(30) NOT NULL default '',
  `language` varchar(40) NOT NULL default '',
  `val_name` varchar(30) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`type`,`language`,`val_name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pflow_lang`
--

INSERT INTO `core_pflow_lang` (`id`, `type`, `language`, `val_name`, `value`) VALUES
(1, 'flow', 'english', 'description', ''),
(1, 'flow', 'english', 'label', 'Immediate publish'),
(1, 'flow', 'italian', 'description', ''),
(1, 'flow', 'italian', 'label', 'Pubblicazione immediata'),
(1, 'step', 'english', 'description', ''),
(1, 'step', 'english', 'label', 'Published'),
(1, 'step', 'italian', 'description', ''),
(1, 'step', 'italian', 'label', 'Pubblicato'),
(2, 'flow', 'english', 'description', ''),
(2, 'flow', 'english', 'label', 'Publish / Unpublish'),
(2, 'flow', 'italian', 'description', ''),
(2, 'flow', 'italian', 'label', 'Pubblica / spubblica'),
(2, 'step', 'english', 'description', ''),
(2, 'step', 'english', 'label', 'Unpublished'),
(2, 'step', 'italian', 'description', ''),
(2, 'step', 'italian', 'label', 'Non pubblicato'),
(7, 'step', 'italian', 'description', ''),
(7, 'step', 'italian', 'label', 'Step 2'),
(8, 'step', 'english', 'description', ''),
(8, 'step', 'english', 'label', 'Step 3'),
(8, 'step', 'italian', 'description', ''),
(8, 'step', 'italian', 'label', 'Step 3');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_pflow_list`
--

CREATE TABLE `core_pflow_list` (
  `flow_id` int(11) NOT NULL auto_increment,
  `flow_code` varchar(20) default NULL,
  `default` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`flow_id`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pflow_list`
--

INSERT INTO `core_pflow_list` (`flow_id`, `flow_code`, `default`, `ord`) VALUES
(1, 'pub_onestate', 1, 1),
(2, 'pub_twostate', 1, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_pflow_step`
--

CREATE TABLE `core_pflow_step` (
  `step_id` int(11) NOT NULL auto_increment,
  `flow_id` int(11) NOT NULL default '0',
  `st_id` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  `is_published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`step_id`)
)  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pflow_step`
--

INSERT INTO `core_pflow_step` (`step_id`, `flow_id`, `st_id`, `ord`, `is_published`) VALUES
(1, 1, 118, 3, 1),
(2, 2, 119, 4, 0),
(3, 2, 120, 5, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_platform`
--

CREATE TABLE `core_platform` (
  `platform` varchar(255) NOT NULL default '',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `class_file_menu` varchar(255) NOT NULL default '',
  `class_name_menu` varchar(255) NOT NULL default '',
  `class_name_menu_managment` varchar(255) NOT NULL default '',
  `file_class_config` varchar(255) NOT NULL default '',
  `class_name_config` varchar(255) NOT NULL default '',
  `var_default_template` varchar(255) NOT NULL default '',
  `class_default_admin` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `is_active` enum('true','false') NOT NULL default 'true',
  `mandatory` enum('true','false') NOT NULL default 'true',
  `dependencies` text NOT NULL,
  `main` enum('true','false') NOT NULL default 'true',
  `hidden_in_config` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`platform`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_platform`
--

INSERT INTO `core_platform` (`platform`, `class_file`, `class_name`, `class_file_menu`, `class_name_menu`, `class_name_menu_managment`, `file_class_config`, `class_name_config`, `var_default_template`, `class_default_admin`, `sequence`, `is_active`, `mandatory`, `dependencies`, `main`, `hidden_in_config`) VALUES
('cms', '', '', 'class.admin_menu_cms.php', 'Admin_Cms', 'Admin_Managment_Cms', 'class.conf_cms.php', 'Config_Cms', 'defaultCmsTemplate', 'CmsAdminModule', 5, 'true', 'false', '', 'false', 'false'),
('crm', '', '', 'class.admin_menu_crm.php', 'Admin_Crm', 'Admin_Managment_Crm', 'class.conf_crm.php', 'Config_Crm', 'defaultTemplate', 'CrmAdminModule', 6, 'false', 'false', '', 'false', 'true'),
('ecom', '', '', 'class.admin_menu_ecom.php', 'Admin_Ecom', 'Admin_Managment_Ecom', 'class.conf_ecom.php', 'Config_Ecom', 'defaultTemplate', 'EcomAdminModule', 7, 'false', 'false', '', 'false', 'false'),
('framework', '', '', 'class.admin_menu_fw.php', 'Admin_Framework', 'Admin_Managment_Framework', 'class.conf_fw.php', 'Config_Framework', 'defaultTemplate', 'Module', 1, 'true', 'true', '', 'false', 'false'),
('lms', '', '', 'class.admin_menu_lms.php', 'Admin_Lms', 'Admin_Managment_Lms', 'class.conf_lms.php', 'Config_Lms', 'defaultTemplate', 'LmsAdminModule', 2, 'true', 'false', '', 'true', 'false'),
('scs', '', '', 'class.admin_menu_scs.php', 'Admin_Scs', '', 'class.conf_scs.php', 'Config_Scs', 'defaultTemplate', 'ScsAdminModule', 3, 'true', 'false', '', 'false', 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_pwd_recover`
--

CREATE TABLE `core_pwd_recover` (
  `idst_user` int(11) NOT NULL default '0',
  `random_code` varchar(255) NOT NULL default '',
  `request_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idst_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pwd_recover`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_reg_list`
--

CREATE TABLE `core_reg_list` (
  `region_id` varchar(100) NOT NULL default '',
  `lang_code` varchar(50) NOT NULL default '',
  `region_desc` varchar(255) NOT NULL default '',
  `default_region` tinyint(1) NOT NULL default '0',
  `browsercode` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`region_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_reg_list`
--

INSERT INTO `core_reg_list` (`region_id`, `lang_code`, `region_desc`, `default_region`, `browsercode`) VALUES
('england', 'english', 'england, usa, ...', 0, 'en-EN, en-US'),
('italy', 'italian', 'Italia', 1, 'it');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_reg_setting`
--

CREATE TABLE `core_reg_setting` (
  `region_id` varchar(100) NOT NULL default '',
  `val_name` varchar(100) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`region_id`,`val_name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_reg_setting`
--

INSERT INTO `core_reg_setting` (`region_id`, `val_name`, `value`) VALUES
('england', 'custom_date_format', ''),
('england', 'custom_time_format', ''),
('england', 'date_format', 'd_m_Y'),
('england', 'date_sep', '/'),
('england', 'time_format', 'H_i'),
('italy', 'custom_date_format', ''),
('italy', 'custom_time_format', ''),
('italy', 'date_format', 'd_m_Y'),
('italy', 'date_sep', '-'),
('italy', 'time_format', 'H_i');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_resource`
--

CREATE TABLE `core_resource` (
  `resource_code` varchar(60) NOT NULL default '',
  `platform` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`resource_code`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_resource`
--

INSERT INTO `core_resource` (`resource_code`, `platform`) VALUES
('classroom', 'lms'),
('course', 'lms'),
('course_edition', 'lms'),
('user', 'framework');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_resource_timetable`
--

CREATE TABLE `core_resource_timetable` (
  `id` int(11) NOT NULL auto_increment,
  `resource` varchar(60) NOT NULL default '',
  `resource_id` int(11) NOT NULL default '0',
  `consumer` varchar(60) NOT NULL default '',
  `consumer_id` int(11) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_resource_timetable`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_rest_authentication`
--

CREATE TABLE `core_rest_authentication` (
  `id_user` int(11) NOT NULL,
  `user_level` int(11) NOT NULL,
  `token` varchar(255) collate utf8_unicode_ci NOT NULL,
  `generation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_enter_date` datetime default NULL,
  `expiry_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`token`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella `core_rest_authentication`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_revision`
--

CREATE TABLE `core_revision` (
  `type` enum('wiki','faq') NOT NULL default 'faq',
  `parent_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `sub_key` varchar(80) NOT NULL default '0',
  `author` int(11) NOT NULL default '0',
  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` longtext NOT NULL,
  PRIMARY KEY  (`type`,`parent_id`,`version`,`sub_key`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_revision`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_role`
--

CREATE TABLE `core_role` (
  `idst` int(11) NOT NULL default '0',
  `roleid` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`idst`),
  KEY `roleid` (`roleid`)
) DEFAULT CHARSET=utf8 PACK_KEYS=0;

--
-- Dump dei dati per la tabella `core_role`
--

INSERT INTO `core_role` (`idst`, `roleid`, `description`) VALUES
(12, '/framework/admin/configuration/view', 'Configuration module'),
(13, '/framework/admin/directory/approve_waiting_user', 'Approve waiting user'),
(14, '/framework/admin/directory/associate_group', 'Associate users to groups'),
(15, '/framework/admin/directory/creategroup', 'Group insert'),
(16, '/framework/admin/directory/createuser_org_chart', 'User insert'),
(17, '/framework/admin/directory/delgroup', 'Group remove'),
(18, '/framework/admin/directory/deluser_org_chart', 'User remove'),
(19, '/framework/admin/directory/editgroup', 'Edit group'),
(20, '/framework/admin/directory/edituser_org_chart', 'Edit user'),
(21, '/framework/admin/directory/view', 'View user managment'),
(22, '/framework/admin/directory/view_group', 'View groups list'),
(23, '/framework/admin/directory/view_org_chart', 'You can see the organization_chart module'),
(24, '/framework/admin/directory/view_user', 'View users list'),
(25, '/framework/admin/event_manager/view_event_manager', 'Show event managment'),
(26, '/framework/admin/field_manager/add', 'You can add custom field'),
(27, '/framework/admin/field_manager/del', 'You can add remove field'),
(28, '/framework/admin/field_manager/mod', 'You can add modify field'),
(29, '/framework/admin/field_manager/view', 'You can see the custom field'),
(30, '/framework/admin/lang/importexport', 'Import language'),
(31, '/framework/admin/lang/view', 'You can use the language module'),
(32, '/framework/admin/lang/view_org_chart', 'You can see the org chart'),
(33, '/framework/admin/newsletter/view', ''),
(34, '/framework/admin/publication_flow/view', 'You can see the pubblication flow'),
(35, '/framework/admin/regional_settings/view', 'You can see the regional setting'),
(36, '/framework/admin/feedreader/view', ''),
(37, '/framework/admin/dashboard/view', ''),
(38, '/framework/admin/dashboard/view_event_manager', ''),
(39, '/framework/admin/iotask/view', 'Can operate on inport export tasks'),
(40, '/framework/admin/admin_manager/view', 'Can operate with admin configuration'),
(45, '/lms/admin/eportfolio/mod', ''),
(46, '/lms/admin/catalogue/associate', ''),
(47, '/lms/admin/catalogue/mod', 'You can operate with the catalogues'),
(48, '/lms/admin/catalogue/view', 'You can operate see the catalogues'),
(49, '/lms/admin/course/add', 'You can add courses'),
(50, '/lms/admin/course/del', 'You can remove courses'),
(51, '/lms/admin/course/mod', 'You can modify courses'),
(52, '/lms/admin/course/moderate', 'Modertae user tocourse subscription'),
(53, '/lms/admin/course/subscribe', 'You can subscribe users to courses'),
(54, '/lms/admin/course/view', 'You can see the courses'),
(55, '/lms/admin/coursepath/mod', 'You can operate with the bounded courses'),
(56, '/lms/admin/coursepath/moderate', ''),
(57, '/lms/admin/coursepath/subscribe', ''),
(58, '/lms/admin/coursepath/view', 'You can see the bounded courses'),
(59, '/lms/admin/manmenu/mod', 'You can modify the custom menu for courses'),
(60, '/lms/admin/manmenu/view', 'You can see the custom menu for courses'),
(61, '/lms/admin/news/mod', 'You can modify the news'),
(62, '/lms/admin/news/view', 'You can see the news'),
(63, '/lms/admin/questcategory/mod', ''),
(64, '/lms/admin/questcategory/view', ''),
(65, '/lms/admin/report/view', ''),
(66, '/lms/admin/webpages/mod', 'You can modify the webpages'),
(67, '/lms/admin/webpages/view', 'You can see the news'),
(68, '/lms/admin/eportfolio/view', 'You can view the eportfolio manager'),
(69, '/lms/admin/preassessment/view', ''),
(70, '/lms/admin/preassessment/mod', ''),
(71, '/lms/admin/preassessment/subscribe', ''),
(72, '/lms/admin/classroom/view', ''),
(73, '/lms/admin/classroom/mod', ''),
(74, '/lms/admin/classlocation/view', ''),
(75, '/lms/admin/classevent/view', ''),
(76, '/lms/course/private/course/view_info', ''),
(77, '/lms/course/private/course/mod', ''),
(78, '/lms/course/private/advice/view', ''),
(79, '/lms/course/private/advice/mod', ''),
(80, '/lms/course/private/groups/view', ''),
(81, '/lms/course/private/groups/mod', ''),
(82, '/lms/course/private/groups/subscribe', ''),
(83, '/lms/course/private/manmenu/view', ''),
(84, '/lms/course/private/manmenu/mod', ''),
(85, '/lms/course/private/organization/view', ''),
(86, '/lms/course/private/chat/view', ''),
(87, '/lms/course/private/notes/view', ''),
(88, '/lms/course/private/forum/view', ''),
(89, '/lms/course/private/forum/write', ''),
(90, '/lms/course/private/forum/upload', ''),
(91, '/lms/course/private/forum/add', ''),
(92, '/lms/course/private/forum/mod', ''),
(93, '/lms/course/private/forum/del', ''),
(94, '/lms/course/private/forum/moderate', ''),
(95, '/lms/course/private/forum/sema', ''),
(96, '/lms/course/private/teleskill/view', ''),
(97, '/lms/course/private/teleskill/moderator', ''),
(98, '/lms/course/private/project/view', ''),
(99, '/lms/course/private/project/add', ''),
(100, '/lms/course/private/project/mod', ''),
(101, '/lms/course/private/project/del', ''),
(102, '/lms/course/private/gradebook/view', ''),
(103, '/lms/course/private/storage/view', ''),
(104, '/lms/course/private/storage/home', ''),
(105, '/lms/course/private/storage/lesson', ''),
(106, '/lms/course/private/storage/public', ''),
(107, '/lms/course/private/coursereport/view', ''),
(108, '/lms/course/private/coursereport/mod', ''),
(109, '/lms/course/private/statistic/view', ''),
(110, '/lms/course/private/stats/view_user', ''),
(111, '/lms/course/private/stats/view_course', ''),
(112, '/lms/course/private/htmlfront/view', ''),
(113, '/lms/course/private/htmlfront/mod', ''),
(114, '/lms/course/private/conference/view', ''),
(115, '/lms/course/private/conference/mod', ''),
(119, '/lms/course/private/wiki/view', ''),
(120, '/lms/course/private/wiki/mod', ''),
(121, '/lms/course/private/newsletter/view', ''),
(122, '/lms/course/public/message/view', ''),
(123, '/lms/course/public/message/send_all', ''),
(124, '/lms/course/public/myfriends/view', ''),
(125, '/lms/course/public/course/view', 'Logged people can see list of subscribed course'),
(126, '/lms/course/public/coursecatalogue/view', 'Logged people can see the course catalogue'),
(127, '/lms/course/public/mygroup/view', 'View my group management'),
(128, '/lms/course/public/profile/mod', 'User can modify the own profile'),
(129, '/lms/course/public/profile/view', 'User can see the own profile'),
(130, '/lms/course/public/userevent/view', 'Show the module for my alert management'),
(131, '/lms/course/public/mycertificate/view', ''),
(132, '/lms/course/public/eportfolio/view', ''),
(133, '/lms/course/public/myfiles/view', ''),
(134, '/lms/course/public/tprofile/view', NULL),
(135, '/scs/admin/admin_configuration/view', 'You can modify the confiuration'),
(136, '/scs/admin/room/mod', 'You can operate with room'),
(137, '/scs/admin/room/view', 'You can see the rooms'),
(138, '/crm/admin/company/view', ''),
(139, '/crm/admin/ticketstatus/view', ''),
(140, '/crm/admin/companytype/view', ''),
(141, '/crm/admin/companystatus/view', ''),
(142, '/crm/admin/contactreason/view', ''),
(143, '/crm/admin/company/add', ''),
(144, '/crm/admin/company/mod', ''),
(145, '/crm/admin/company/del', ''),
(146, '/crm/admin/storedform/view', ''),
(147, '/crm/module/company/view', ''),
(148, '/crm/module/abook/view', ''),
(149, '/crm/module/project/view', ''),
(150, '/crm/module/todo/view', ''),
(151, '/crm/module/ticket/view', ''),
(152, '/crm/module/contacthistory/view', ''),
(153, '/crm/module/task/view', ''),
(154, '/ecom/admin/bought/view', ''),
(155, '/ecom/admin/payaccount/view', ''),
(156, '/ecom/admin/taxzone/view', ''),
(157, '/ecom/admin/taxcountry/view', ''),
(158, '/ecom/admin/taxcatgod/view', ''),
(159, '/ecom/admin/taxrate/view', ''),
(160, '/ecom/admin/transaction/view', ''),
(161, '/ecom/admin/reservation/view', ''),
(162, '/ecom/admin/taxzone/mod', ''),
(163, '/ecom/admin/taxcatgod/mod', ''),
(164, '/ecom/admin/payaccount/mod', ''),
(165, '/ecom/admin/taxcountry/mod', ''),
(166, '/ecom/admin/taxrate/mod', ''),
(167, '/ecom/admin/transaction/mod', ''),
(168, '/ecom/admin/reservation/mod', ''),
(169, '/ecom/admin/bought/mod', ''),
(170, '/cms/admin/banners/cat_view', ''),
(171, '/cms/admin/banners/view', ''),
(172, '/cms/admin/docs/view', ''),
(173, '/cms/admin/form/view', ''),
(174, '/cms/admin/links/view', ''),
(175, '/cms/admin/manpage/view', 'Page management'),
(176, '/cms/admin/banners/add', ''),
(177, '/cms/admin/banners/del', ''),
(178, '/cms/admin/banners/mod', ''),
(179, '/cms/admin/content/add', ''),
(180, '/cms/admin/content/del', ''),
(181, '/cms/admin/content/mod', ''),
(182, '/cms/admin/content/view', ''),
(183, '/cms/admin/docs/add', ''),
(184, '/cms/admin/docs/del', ''),
(185, '/cms/admin/docs/mod', ''),
(186, '/cms/admin/form/add', ''),
(187, '/cms/admin/form/del', ''),
(188, '/cms/admin/form/mod', ''),
(189, '/cms/admin/forum/add', ''),
(190, '/cms/admin/forum/del', ''),
(191, '/cms/admin/forum/mod', ''),
(192, '/cms/admin/forum/view', ''),
(193, '/cms/admin/links/add', ''),
(194, '/cms/admin/links/del', ''),
(195, '/cms/admin/links/mod', ''),
(196, '/cms/admin/manpage/add', ''),
(197, '/cms/admin/manpage/del', ''),
(198, '/cms/admin/manpage/mod', ''),
(199, '/cms/admin/faq/view', ''),
(200, '/cms/admin/mantopic/add', ''),
(201, '/cms/admin/mantopic/del', ''),
(202, '/cms/admin/mantopic/mod', ''),
(203, '/cms/admin/mantopic/view', 'You can see the CMS topic management module'),
(204, '/cms/admin/media/add', ''),
(205, '/cms/admin/media/del', ''),
(206, '/cms/admin/media/mod', ''),
(207, '/cms/admin/media/view', 'Multimedia manager'),
(208, '/cms/admin/news/add', ''),
(209, '/cms/admin/news/del', ''),
(210, '/cms/admin/news/mod', ''),
(211, '/cms/admin/news/view', 'News management'),
(212, '/cms/admin/poll/add', ''),
(213, '/cms/admin/poll/del', ''),
(214, '/cms/admin/poll/mod', ''),
(215, '/cms/admin/poll/view', ''),
(216, '/cms/admin/stats/view', ''),
(217, '/cms/admin/wiki/view', ''),
(278, '/lms/course/private/light_repo/view', ''),
(279, '/lms/course/private/light_repo/mod', ''),
(430, '/lms/admin/certificate/view', 'Certificate admin view'),
(431, '/lms/admin/certificate/mod', 'Certificate admin mod'),
(453, '/lms/admin/report_certificate/view', 'Certificate report admin view'),
(456, '/lms/course/private/calendar/view', ''),
(457, '/lms/course/private/calendar/personal', ''),
(458, '/lms/course/private/calendar/mod', ''),
(459, '/lms/course/private/wiki/edit', ''),
(460, '/lms/course/private/wiki/admin', ''),
(578, '/cms/admin/faq/add', ''),
(579, '/cms/admin/faq/mod', ''),
(580, '/cms/admin/faq/del', ''),
(581, '/cms/admin/wiki/add', ''),
(582, '/cms/admin/wiki/mod', ''),
(583, '/cms/admin/wiki/del', ''),
(584, '/framework/admin/bugtracker/view', ''),
(585, '/cms/admin/calendar/view', ''),
(586, '/cms/admin/calendar/add', ''),
(587, '/cms/admin/calendar/mod', ''),
(588, '/cms/admin/calendar/del', ''),
(592, '/lms/admin/classlocation/mod', ''),
(593, '/lms/admin/classevent/mod', ''),
(594, '/lms/admin/reservation/view', ''),
(595, '/lms/admin/reservation/mod', ''),
(893, '/crm/admin/companytype/add', ''),
(894, '/crm/admin/companytype/mod', ''),
(895, '/crm/admin/companytype/del', ''),
(896, '/crm/admin/companystatus/mod', ''),
(897, '/crm/admin/ticketstatus/add', ''),
(898, '/crm/admin/ticketstatus/mod', ''),
(899, '/crm/admin/ticketstatus/del', ''),
(900, '/crm/admin/contactreason/add', ''),
(901, '/crm/admin/contactreason/mod', ''),
(902, '/crm/admin/contactreason/del', ''),
(994, '/cms/admin/simpleprj/view', ''),
(995, '/lms/admin/middlearea/view', ''),
(996, '/lms/admin/internal_news/view', ''),
(997, '/lms/admin/internal_news/mod', ''),
(998, '/lms/course/public/public_forum/view', ''),
(999, '/lms/course/public/public_forum/write', ''),
(1000, '/lms/course/public/public_forum/upload', ''),
(1001, '/lms/course/public/public_forum/add', ''),
(1002, '/lms/course/public/public_forum/mod', ''),
(1003, '/lms/course/public/public_forum/del', ''),
(1004, '/lms/course/public/public_forum/moderate', ''),
(1005, '/lms/course/public/course_autoregistration/view', ''),
(1012, '/lms/course/public/mycompetences/view', NULL),
(1013, '/framework/admin/public_admin_manager/view', NULL),
(1014, '/lms/course/public/public_user_admin/view_org_chart', NULL),
(1015, '/lms/course/public/public_user_admin/createuser_org_chart', NULL),
(1016, '/lms/course/public/public_user_admin/edituser_org_chart', NULL),
(1017, '/lms/course/public/public_user_admin/deluser_org_chart', NULL),
(1018, '/lms/course/public/public_user_admin/approve_waiting_user', NULL),
(1019, '/lms/course/public/public_user_admin/view_user', NULL),
(1020, '/lms/course/public/public_course_admin/view', NULL),
(1021, '/lms/course/public/public_course_admin/add', NULL),
(1022, '/lms/course/public/public_course_admin/mod', NULL),
(1023, '/lms/course/public/public_course_admin/del', NULL),
(1024, '/lms/course/public/public_course_admin/subscribe', NULL),
(1025, '/lms/course/public/public_course_admin/moderate', NULL),
(1026, '/lms/course/public/public_subscribe_admin/view_org_chart', NULL),
(1027, '/lms/course/public/public_subscribe_admin/createuser_org_chart', NULL),
(1028, '/lms/course/public/public_subscribe_admin/edituser_org_chart', NULL),
(1029, '/lms/course/public/public_subscribe_admin/deluser_org_chart', NULL),
(1030, '/lms/course/public/public_subscribe_admin/approve_waiting_user', NULL),
(1031, '/lms/course/public/public_report_admin/view', NULL),
(1032, '/lms/course/public/public_newsletter_admin/view', NULL),
(1033, '/lms/admin/meta_certificate/view', NULL),
(1034, '/lms/admin/meta_certificate/mod', NULL),
(1035, '/lms/admin/competences/view', NULL),
(1036, '/lms/admin/competences/mod', NULL),
(1037, '/lms/admin/report/view', NULL),
(1038, '/lms/admin/report/mod', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_role_members`
--

CREATE TABLE `core_role_members` (
  `idst` int(11) NOT NULL default '0',
  `idstMember` int(11) NOT NULL default '0',
  UNIQUE KEY `idst` (`idst`,`idstMember`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_role_members`
--

INSERT INTO `core_role_members` (`idst`, `idstMember`) VALUES
(12, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(31, 3),
(32, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(37, 3),
(37, 4),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 3),
(46, 3),
(47, 3),
(49, 3),
(50, 3),
(51, 3),
(52, 3),
(53, 3),
(54, 3),
(55, 3),
(56, 3),
(57, 3),
(59, 3),
(60, 3),
(61, 3),
(62, 3),
(63, 3),
(64, 3),
(65, 3),
(66, 3),
(67, 3),
(68, 3),
(69, 3),
(70, 3),
(71, 3),
(72, 3),
(73, 3),
(74, 3),
(75, 3),
(76, 3),
(76, 271),
(76, 272),
(76, 273),
(76, 274),
(76, 275),
(76, 276),
(76, 277),
(77, 3),
(77, 271),
(77, 272),
(77, 273),
(77, 274),
(78, 3),
(78, 271),
(78, 272),
(78, 273),
(78, 274),
(78, 275),
(78, 276),
(78, 277),
(79, 3),
(79, 271),
(79, 272),
(79, 273),
(79, 274),
(80, 3),
(80, 271),
(80, 272),
(80, 273),
(80, 274),
(81, 3),
(81, 271),
(81, 272),
(82, 3),
(82, 271),
(82, 272),
(82, 273),
(82, 274),
(83, 3),
(83, 271),
(83, 272),
(84, 3),
(84, 271),
(84, 272),
(85, 3),
(85, 271),
(85, 272),
(85, 273),
(85, 274),
(85, 275),
(85, 276),
(85, 277),
(86, 3),
(86, 271),
(86, 272),
(86, 273),
(86, 274),
(86, 275),
(87, 3),
(87, 271),
(87, 272),
(87, 273),
(87, 274),
(87, 275),
(87, 276),
(87, 277),
(88, 3),
(88, 271),
(88, 272),
(88, 273),
(88, 274),
(88, 275),
(88, 276),
(88, 277),
(89, 3),
(89, 271),
(89, 272),
(89, 273),
(89, 274),
(89, 275),
(90, 3),
(90, 271),
(90, 272),
(90, 273),
(90, 274),
(90, 275),
(91, 3),
(91, 271),
(91, 272),
(92, 3),
(92, 271),
(92, 272),
(93, 3),
(93, 271),
(93, 272),
(94, 3),
(94, 271),
(94, 272),
(94, 273),
(94, 274),
(95, 3),
(96, 3),
(96, 271),
(96, 272),
(96, 273),
(96, 274),
(96, 275),
(96, 276),
(96, 277),
(97, 3),
(97, 271),
(97, 272),
(98, 3),
(98, 271),
(98, 272),
(98, 273),
(98, 274),
(98, 275),
(98, 276),
(98, 277),
(99, 3),
(99, 271),
(99, 272),
(100, 3),
(100, 271),
(100, 272),
(101, 3),
(101, 271),
(101, 272),
(102, 3),
(102, 275),
(103, 3),
(103, 271),
(103, 272),
(104, 3),
(104, 271),
(104, 272),
(105, 3),
(105, 271),
(105, 272),
(106, 3),
(106, 271),
(106, 272),
(107, 3),
(107, 271),
(107, 272),
(108, 3),
(108, 271),
(108, 272),
(109, 3),
(109, 271),
(109, 272),
(110, 3),
(110, 271),
(110, 272),
(111, 3),
(111, 271),
(111, 272),
(112, 3),
(112, 271),
(112, 272),
(112, 273),
(112, 274),
(112, 275),
(112, 276),
(112, 277),
(113, 3),
(113, 271),
(113, 272),
(113, 273),
(113, 274),
(114, 271),
(114, 272),
(114, 273),
(114, 274),
(114, 275),
(114, 276),
(114, 277),
(115, 3),
(115, 271),
(115, 272),
(115, 273),
(115, 274),
(119, 3),
(119, 271),
(119, 272),
(119, 273),
(119, 274),
(119, 275),
(119, 276),
(119, 277),
(120, 3),
(121, 3),
(121, 271),
(121, 272),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 3),
(136, 3),
(137, 3),
(138, 3),
(139, 3),
(140, 3),
(141, 3),
(142, 3),
(143, 3),
(144, 3),
(145, 3),
(146, 3),
(147, 3),
(148, 3),
(149, 3),
(150, 3),
(151, 3),
(152, 3),
(153, 3),
(154, 3),
(155, 3),
(156, 3),
(157, 3),
(158, 3),
(159, 3),
(160, 3),
(161, 3),
(162, 3),
(163, 3),
(164, 3),
(165, 3),
(166, 3),
(167, 3),
(168, 3),
(169, 3),
(170, 3),
(171, 3),
(172, 3),
(173, 3),
(174, 3),
(175, 3),
(176, 3),
(177, 3),
(178, 3),
(179, 3),
(180, 3),
(181, 3),
(182, 3),
(183, 3),
(184, 3),
(185, 3),
(186, 3),
(187, 3),
(188, 3),
(189, 3),
(190, 3),
(191, 3),
(192, 3),
(193, 3),
(194, 3),
(195, 3),
(196, 3),
(197, 3),
(198, 3),
(199, 3),
(200, 3),
(201, 3),
(202, 3),
(203, 3),
(204, 3),
(205, 3),
(206, 3),
(207, 3),
(208, 3),
(209, 3),
(210, 3),
(211, 3),
(212, 3),
(213, 3),
(214, 3),
(215, 3),
(216, 3),
(217, 3),
(278, 271),
(278, 272),
(278, 273),
(278, 274),
(278, 275),
(278, 276),
(278, 277),
(279, 271),
(279, 272),
(279, 273),
(279, 274),
(430, 3),
(431, 3),
(453, 3),
(456, 271),
(456, 272),
(456, 273),
(456, 274),
(456, 275),
(456, 276),
(456, 277),
(457, 271),
(457, 272),
(457, 273),
(457, 274),
(457, 275),
(458, 271),
(458, 272),
(458, 273),
(458, 274),
(459, 271),
(459, 272),
(459, 273),
(459, 274),
(459, 275),
(460, 271),
(460, 272),
(585, 3),
(586, 3),
(587, 3),
(588, 3),
(594, 3),
(595, 3),
(994, 3),
(995, 3),
(996, 3),
(997, 3),
(998, 3),
(999, 3),
(1000, 3),
(1001, 3),
(1002, 3),
(1003, 3),
(1004, 3),
(1005, 3),
(1012, 1),
(1013, 3),
(1014, 3),
(1015, 3),
(1016, 3),
(1017, 3),
(1018, 3),
(1019, 3),
(1020, 3),
(1021, 3),
(1022, 3),
(1023, 3),
(1024, 3),
(1025, 3),
(1026, 3),
(1027, 3),
(1028, 3),
(1029, 3),
(1030, 3),
(1031, 3),
(1032, 3),
(1033, 3),
(1034, 3),
(1035, 3),
(1036, 3),
(1037, 3),
(1038, 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_setting`
--

CREATE TABLE `core_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` text NOT NULL,
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `pack` varchar(255) NOT NULL default 'main',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_setting`
--

INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('accessibility', 'off', 'enum', 255, 'main', 0, 16, 1, 0, ''),
('common_admin_session', 'on', 'enum', 3, 'main', 4, 1, 1, 0, ''),
('company_idref_code', 'code', 'string', 255, 'main', 0, 0, 1, 1, ''),
('core_version', '3.6.0.3', 'string', 255, 'main', 0, 3, 1, 1, ''),
('defaultTemplate', 'standard', 'template', 255, 'main', 0, 3, 1, 0, ''),
('default_language', 'italian', 'language', 255, 'main', 0, 2, 1, 0, ''),
('default_pubflow_method', 'advanced', 'pubflow_method_chooser', 8, 'main', 0, 13, 1, 0, ''),
('do_debug', 'off', 'enum', 3, 'main', 0, 17, 1, 0, ''),
('field_tree', '0', 'field_tree', 255, 'log_option', 0, 11, 1, 0, ''),
('google_stat_code', '', 'textarea', 65535, 'main', 8, 1, 1, 0, ''),
('google_stat_in_cms', '0', 'check', 1, 'main', 8, 2, 1, 0, ''),
('google_stat_in_lms', '0', 'check', 1, 'main', 8, 3, 1, 0, ''),
('hour_request_limit', '24', 'int', 2, 'log_option', 0, 8, 0, 0, ''),
('hteditor', 'tinymce', 'hteditor', 255, 'main', 0, 7, 1, 0, ''),
('htmledit_image_admin', '1', 'check', 255, 'main', 0, 9, 1, 0, ''),
('htmledit_image_godadmin', '1', 'check', 255, 'main', 0, 8, 1, 0, ''),
('htmledit_image_user', '1', 'check', 255, 'main', 0, 10, 1, 0, ''),
('lang_edit', 'off', 'enum', 3, 'main', 0, 14, 1, 0, ''),
('lastfirst_mandatory', 'off', 'enum', 3, 'log_option', 0, 2, 2, 0, ''),
('layout', 'over', 'layout_chooser', 255, 'main', 0, 6, 1, 1, ''),
('ldap_port', '389', 'string', 5, 'main', 5, 3, 1, 0, ''),
('ldap_server', '192.168.0.1', 'string', 255, 'main', 5, 2, 1, 0, ''),
('ldap_used', 'off', 'enum', 3, 'main', 5, 1, 1, 0, ''),
('ldap_user_string', '$user@domain2.domain1', 'string', 255, 'main', 5, 4, 1, 0, ''),
('mail_sender', 'sample@localhost.com', 'string', 255, 'log_option', 0, 0, 0, 0, ''),
('max_log_attempt', '0', 'int', 3, 'log_option', 0, 2, 0, 0, ''),
('nl_sendpause', '20', 'int', 3, 'main', 2, 1, 1, 0, ''),
('nl_sendpercycle', '200', 'int', 4, 'main', 2, 0, 1, 0, ''),
('pass_alfanumeric', 'off', 'enum', 3, 'log_option', 0, 6, 0, 0, ''),
('pass_change_first_login', 'off', 'enum', 3, 'log_option', 0, 13, 1, 0, ''),
('pass_max_time_valid', '0', 'int', 4, 'log_option', 0, 7, 1, 0, ''),
('pass_min_char', '5', 'int', 2, 'log_option', 0, 5, 0, 0, ''),
('pathfield', 'field/', 'string', 255, 'main', 1, 2, 1, 0, ''),
('pathphoto', 'photo/', 'string', 255, 'main', 1, 2, 1, 0, ''),
('phantom', '', 'security_check', 255, 'main', 4, 3, 0, 0, ''),
('privacy_policy', 'on', 'enum', 3, 'log_option', 0, 9, 0, 0, ''),
('register_in_company', 'off', 'enum', 3, 'log_option', 0, 12, 1, 0, ''),
('register_tree', 'off', 'register_tree', 255, 'log_option', 0, 10, 1, 0, ''),
('register_type', 'self', 'register_type', 10, 'log_option', 0, 1, 0, 0, ''),
('save_log_attempt', 'after_max', 'save_log_attempt', 255, 'log_option', 0, 4, 0, 0, ''),
('sender_event', 'sample@localhost.com', 'string', 255, 'main', 0, 4, 1, 0, ''),
('session_ip_control', 'on', 'enum', 3, 'main', 4, 1, 1, 0, ''),
('session_ip_filter', '', 'textarea', 65535, 'main', 4, 2, 1, 0, ''),
('sms_cell_num_field', '1', 'field_select', 5, 'main', 3, 6, 1, 0, ''),
('sms_credit', '0', 'string', 20, 'main', 3, 10, 1, 1, ''),
('sms_gateway', 'smsmarket', 'string', 50, 'main', 3, 3, 1, 1, ''),
('sms_gateway_host', '193.254.241.47', 'string', 15, 'main', 3, 8, 1, 0, ''),
('sms_gateway_id', '3', 'sel_sms_gateway', 1, 'main', 3, 7, 1, 0, ''),
('sms_gateway_pass', '', 'string', 255, 'main', 3, 5, 1, 0, ''),
('sms_gateway_port', '26', 'int', 5, 'main', 3, 9, 1, 0, ''),
('sms_gateway_user', '', 'string', 50, 'main', 3, 4, 1, 0, ''),
('sms_international_prefix', '+39', 'string', 3, 'main', 3, 1, 1, 0, ''),
('sms_sent_from', '0', 'string', 25, 'main', 3, 2, 1, 0, ''),
('sserver_host', '', 'string', 255, 'main', 7, 1, 1, 0, ''),
('sserver_user', '', 'string', 255, 'main', 7, 2, 1, 0, ''),
('templ_use_field', '0', 'id_field', 11, 'main', 0, 0, 1, 1, ''),
('title_organigram_chart', 'root', 'string', 255, 'main', 0, 5, 1, 1, ''),
('ttlSession', '4000', 'int', 5, 'main', 0, 4, 1, 0, ''),
('url', 'http://127.0.0.1/doceboCore/', 'string', 255, 'main', 0, 1, 1, 0, ''),
('user_quota', '50', 'string', 255, 'main', 0, 15, 1, 0, ''),
('use_accesskey', 'off', 'enum', 3, 'main', 0, 15, 1, 0, ''),
('use_admin', '1', 'menuvoice', 1, 'main', 6, 5, 1, 0, '/admin_manager/view'),
('use_advanced_form', 'off', 'enum', 3, 'log_option', 0, 12, 1, 0, ''),
('use_groups', '1', 'menuvoice', 1, 'main', 6, 2, 1, 0, '/directory/view_group'),
('use_org_chart', '1', 'check', 1, 'main', 6, 1, 1, 0, ''),
('use_org_chart_field', '0', 'check', 1, 'main', 6, 4, 1, 0, ''),
('use_org_chart_multiple_choice', '1', 'check', 1, 'main', 6, 6, 1, 0, ''),
('use_tag', 'off', 'enum', 3, 'main', 0, 18, 1, 0, ''),
('use_user_fields', '1', 'check', 1, 'main', 6, 3, 1, 0, ''),
('visuItem', '20', 'int', 3, 'main', 0, 11, 1, 0, ''),
('visuUser', '20', 'int', 5, 'main', 0, 12, 1, 0, ''),
('welcome_use_feed', 'off', 'enum', 3, 'main', 0, 16, 1, 1, ''),
('register_deleted_user', 'off', 'enum', 3, 'log_option', 0, 14, 1, 0, ''),
('profile_only_pwd', 'on', 'enum', 3, 'log_option', 0, 15, 1, 0, ''),
('register_with_code', 'off', 'enum', 3, 'log_option', 0, 16, 1, 0, ''),
('rest_auth_code', '', 'string', 255, 'main', 10, 2, 1, 0, ''),
('rest_auth_lifetime', '60', 'int', 3, 'main', 10, 3, 1, 0, ''),
('rest_auth_method', '1', 'rest_auth_sel_method', 3, 'main', 10, 1, 1, 0, ''),
('rest_auth_update', 'off', 'enum', 3, 'main', 10, 4, 1, 0, ''),
('use_rest_api', 'off', 'enum', 3, 'main', 10, 0, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_setting_list`
--

CREATE TABLE `core_setting_list` (
  `path_name` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `default_value` text NOT NULL,
  `type` varchar(255) NOT NULL default '',
  `visible` tinyint(1) NOT NULL default '0',
  `load_at_startup` tinyint(1) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`path_name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_setting_list`
--

INSERT INTO `core_setting_list` (`path_name`, `label`, `default_value`, `type`, `visible`, `load_at_startup`, `sequence`) VALUES
('admin_rules.direct_course_subscribe', '_DIRECT_COURSE_SUBSCRIBE', 'off', 'enum', 1, 1, 6),
('admin_rules.direct_user_insert', '_DIRECT_USER_INSERT', 'off', 'enum', 1, 1, 3),
('admin_rules.limit_course_subscribe', '_LIMIT_COURSE_SUBSCRIBE', 'off', 'enum', 1, 1, 4),
('admin_rules.limit_user_insert', '_LIMIT_USER_INSERT', 'off', 'enum', 1, 1, 1),
('admin_rules.max_course_subscribe', '_MAX_COURSE_SUBSCRIBE', '0', 'integer', 1, 1, 5),
('admin_rules.max_user_insert', '_MAX_USER_INSERT', '0', 'integer', 1, 1, 2),
('admin_rules.user_lang_assigned', '', '', 'string', 0, 1, 0),
('ui.directory.custom_columns', '_CUSTOM_COLUMS', '', 'hidden', 0, 1, 0),
('ui.language', '_LANGUAGE', '', 'language', 1, 1, 0),
('ui.template', '_TEMPLATE', '', 'template', 1, 1, 0),
('user_rules.field_policy', '', '', 'serialized', 0, 1, 0),
('user_rules.user_quota', '', '-1', 'int', 0, 1, 0),
('user_rules.user_quota_used', '', '0', 'int', 0, 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_setting_user`
--

CREATE TABLE `core_setting_user` (
  `path_name` varchar(255) NOT NULL default '',
  `id_user` int(11) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`path_name`,`id_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_setting_user`
--

INSERT INTO `core_setting_user` (`path_name`, `id_user`, `value`) VALUES
('ui.language', 12, 'italian');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_st`
--

CREATE TABLE `core_st` (
  `idst` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`idst`)
)  DEFAULT CHARSET=utf8 COMMENT='Security Tokens';

--
-- Dump dei dati per la tabella `core_st`
--

INSERT INTO `core_st` (`idst`) VALUES
(1010),
(1011),
(1012),
(1013),
(1014),
(1015),
(1016),
(1017),
(1018),
(1019),
(1020),
(1021),
(1022),
(1023),
(1024),
(1025),
(1026),
(1027),
(1028),
(1029),
(1030),
(1031),
(1032),
(1033),
(1034),
(1035),
(1036),
(1037),
(1038);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_sysforum`
--

CREATE TABLE `core_sysforum` (
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
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_tag`
--

CREATE TABLE `core_tag` (
  `id_tag` int(11) NOT NULL auto_increment,
  `tag_name` varchar(255) NOT NULL,
  `id_parent` int(11) NOT NULL,
  PRIMARY KEY  (`id_tag`),
  KEY `tag_name` (`tag_name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_tag`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_tag_relation`
--

CREATE TABLE `core_tag_relation` (
  `id_tag` int(11) NOT NULL,
  `id_resource` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `id_course` int(11) NOT NULL,
  PRIMARY KEY  (`id_tag`,`id_resource`,`resource_type`,`id_user`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_tag_relation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_tag_resource`
--

CREATE TABLE `core_tag_resource` (
  `id_resource` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sample_text` text NOT NULL,
  `permalink` text NOT NULL,
  PRIMARY KEY  (`id_resource`,`resource_type`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_tag_resource`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_task`
--

CREATE TABLE `core_task` (
  `name` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `conn_source` varchar(50) NOT NULL default '',
  `conn_destination` varchar(50) NOT NULL default '',
  `schedule_type` enum('at','any') NOT NULL default 'at',
  `schedule` varchar(50) NOT NULL default '',
  `import_type` varchar(50) NOT NULL default '',
  `map` text NOT NULL,
  `last_execution` datetime default NULL,
  `sequence` int(3) NOT NULL,
  PRIMARY KEY  (`name`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_task`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user`
--

CREATE TABLE `core_user` (
  `idst` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `pass` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `photo` varchar(255) NOT NULL default '',
  `avatar` varchar(255) NOT NULL default '',
  `signature` text NOT NULL,
  `level` int(11) NOT NULL default '0',
  `lastenter` datetime default NULL,
  `valid` tinyint(1) NOT NULL default '1',
  `pwd_expire_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `register_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idst`),
  UNIQUE KEY `userid` (`userid`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user`
--

INSERT INTO `core_user` (`idst`, `userid`, `firstname`, `lastname`, `pass`, `email`, `photo`, `avatar`, `signature`, `level`, `lastenter`, `valid`, `pwd_expire_at`, `register_date`) VALUES
(11, '/Anonymous', '', '', '', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_file`
--

CREATE TABLE `core_user_file` (
  `id` int(11) NOT NULL auto_increment,
  `user_idst` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `media_url` varchar(255) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `uldate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_friend`
--

CREATE TABLE `core_user_friend` (
  `id_user` int(11) NOT NULL default '0',
  `id_friend` int(11) NOT NULL default '0',
  `waiting` int(1) NOT NULL default '0',
  `request_msg` text NOT NULL,
  PRIMARY KEY  (`id_user`,`id_friend`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_friend`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_log_attempt`
--

CREATE TABLE `core_user_log_attempt` (
  `id` int(11) NOT NULL auto_increment,
  `userid` varchar(255) NOT NULL default '',
  `attempt_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `attempt_number` int(5) NOT NULL default '0',
  `user_ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_log_attempt`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_myfiles`
--

CREATE TABLE `core_user_myfiles` (
  `id_file` int(11) NOT NULL auto_increment,
  `area` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `file_name` varchar(255) NOT NULL default '',
  `owner` int(11) NOT NULL default '0',
  `file_policy` int(1) NOT NULL default '0',
  `size` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_file`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_myfiles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_profileview`
--

CREATE TABLE `core_user_profileview` (
  `id_owner` int(11) NOT NULL default '0',
  `id_viewer` int(11) NOT NULL default '0',
  `date_view` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_owner`,`id_viewer`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_profileview`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_temp`
--

CREATE TABLE `core_user_temp` (
  `idst` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `firstname` varchar(100) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `pass` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `language` varchar(50) NOT NULL default '',
  `request_on` datetime default '0000-00-00 00:00:00',
  `random_code` varchar(255) NOT NULL default '',
  `create_by_admin` int(11) NOT NULL default '0',
  `confirmed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idst`),
  UNIQUE KEY `userid` (`userid`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_temp`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki`
--

CREATE TABLE `core_wiki` (
  `wiki_id` int(11) NOT NULL auto_increment,
  `source_platform` varchar(255) NOT NULL default '',
  `public` tinyint(1) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `other_lang` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `page_count` int(11) NOT NULL default '0',
  `revision_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wiki_id`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_wiki`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki_page`
--

CREATE TABLE `core_wiki_page` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_code` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `page_path` varchar(255) NOT NULL default '',
  `lev` int(3) NOT NULL default '0',
  `wiki_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`page_id`)
) DEFAULT CHARSET=utf8 PACK_KEYS=1;

--
-- Dump dei dati per la tabella `core_wiki_page`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki_page_info`
--

CREATE TABLE `core_wiki_page_info` (
  `page_id` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `version` int(11) NOT NULL default '0',
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `wiki_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`page_id`,`language`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_wiki_page_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki_revision`
--

CREATE TABLE `core_wiki_revision` (
  `wiki_id` int(11) NOT NULL default '0',
  `page_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '0',
  `author` int(11) NOT NULL default '0',
  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` longtext NOT NULL,
  PRIMARY KEY  (`wiki_id`,`page_id`,`version`,`language`)
) DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_wiki_revision`
--


-- --------------------------------------------------------