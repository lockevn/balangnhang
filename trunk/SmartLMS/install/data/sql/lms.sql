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
-- Struttura della tabella `learning_advice`
--

CREATE TABLE `learning_advice` (
  `idAdvice` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `important` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idAdvice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_advice`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_adviceuser`
--

CREATE TABLE `learning_adviceuser` (
  `idAdvice` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `archivied` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idAdvice`,`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_adviceuser`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_assessment_rules`
--

CREATE TABLE `learning_assessment_rules` (
  `id_rule` int(11) NOT NULL auto_increment,
  `id_assessment` int(11) NOT NULL default '0',
  `rule_type` varchar(255) NOT NULL default '',
  `rule_setting` varchar(255) NOT NULL default '',
  `rule_effect` text NOT NULL,
  `rule_casualities` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_rule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_assessment_rules`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_assessment_user`
--

CREATE TABLE `learning_assessment_user` (
  `id_assessment` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `type_of` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_assessment`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_assessment_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_calendar`
--

CREATE TABLE `learning_calendar` (
  `id` bigint(20) NOT NULL default '0',
  `idCourse` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_catalogue`
--

CREATE TABLE `learning_catalogue` (
  `idCatalogue` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idCatalogue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_catalogue`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_catalogue_entry`
--

CREATE TABLE `learning_catalogue_entry` (
  `idCatalogue` int(11) NOT NULL default '0',
  `idEntry` int(11) NOT NULL default '0',
  `type_of_entry` enum('course','coursepath') NOT NULL default 'course',
  PRIMARY KEY  (`idCatalogue`,`idEntry`,`type_of_entry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_catalogue_entry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_catalogue_member`
--

CREATE TABLE `learning_catalogue_member` (
  `idCatalogue` int(11) NOT NULL default '0',
  `idst_member` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCatalogue`,`idst_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_catalogue_member`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_category`
--

CREATE TABLE `learning_category` (
  `idCategory` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `lev` int(11) NOT NULL default '0',
  `path` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate`
--

CREATE TABLE `learning_certificate` (
  `id_certificate` int(11) NOT NULL auto_increment,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `base_language` varchar(255) NOT NULL default '',
  `cert_structure` text NOT NULL,
  `orientation` enum('P','L') NOT NULL default 'P',
  `bgimage` varchar(255) NOT NULL,
  `meta` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_certificate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_assign`
--

CREATE TABLE `learning_certificate_assign` (
  `id_certificate` int(11) NOT NULL default '0',
  `id_course` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `on_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `cert_file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_certificate`,`id_course`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_assign`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_course`
--

CREATE TABLE `learning_certificate_course` (
  `id_certificate` int(11) NOT NULL default '0',
  `id_course` int(11) NOT NULL default '0',
  `available_for_status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_certificate`,`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_meta`
--

CREATE TABLE `learning_certificate_meta` (
  `idMetaCertificate` int(11) NOT NULL auto_increment,
  `idCertificate` int(11) NOT NULL default '0',
  `title` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `description` longtext character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`idMetaCertificate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_meta`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_meta_assign`
--

CREATE TABLE `learning_certificate_meta_assign` (
  `idUser` int(11) NOT NULL default '0',
  `idMetaCertificate` int(11) NOT NULL default '0',
  `idCertificate` int(11) NOT NULL default '0',
  `on_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `cert_file` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idUser`,`idMetaCertificate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_meta_assign`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_meta_course`
--

CREATE TABLE `learning_certificate_meta_course` (
  `id` int(11) NOT NULL auto_increment,
  `idMetaCertificate` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `idCourseEdition` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_meta_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_tags`
--

CREATE TABLE `learning_certificate_tags` (
  `file_name` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`file_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_tags`
--

INSERT INTO `learning_certificate_tags` (`file_name`, `class_name`) VALUES
('certificate.course.php', 'CertificateSubs_Course'),
('certificate.user.php', 'CertificateSubs_User'),
('certificate.userstat.php', 'CertificateSubs_UserStat'),
('certificate.misc.php', 'CertificateSubs_Misc');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_classroom`
--

CREATE TABLE `learning_classroom` (
  `idClassroom` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `location_id` int(11) NOT NULL default '0',
  `room` varchar(255) NOT NULL default '',
  `street` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `zip_code` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `fax` varchar(255) NOT NULL default '',
  `capacity` varchar(255) NOT NULL default '',
  `disposition` text NOT NULL,
  `instrument` text NOT NULL,
  `available_instrument` text NOT NULL,
  `note` text NOT NULL,
  `responsable` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idClassroom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_classroom`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_classroom_calendar`
--

CREATE TABLE `learning_classroom_calendar` (
  `id` int(11) NOT NULL auto_increment,
  `classroom_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `owner` int(11) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_classroom_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_class_location`
--

CREATE TABLE `learning_class_location` (
  `location_id` int(11) NOT NULL auto_increment,
  `location` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_class_location`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_comment_ajax`
--

CREATE TABLE `learning_comment_ajax` (
  `id_comment` int(11) NOT NULL auto_increment,
  `resource_type` varchar(50) NOT NULL default '',
  `external_key` varchar(200) NOT NULL default '',
  `id_author` int(11) NOT NULL default '0',
  `posted_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `textof` text NOT NULL,
  `history_tree` varchar(255) NOT NULL default '',
  `id_parent` int(11) NOT NULL default '0',
  `moderated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_comment_ajax`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_commontrack`
--

CREATE TABLE `learning_commontrack` (
  `idReference` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idTrack` int(11) NOT NULL default '0',
  `objectType` varchar(20) NOT NULL default '',
  `firstAttempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateAttempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`idTrack`,`objectType`),
  KEY `idReference` (`idReference`),
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_commontrack`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence`
--

CREATE TABLE `learning_competence` (
  `id_competence` int(10) unsigned NOT NULL auto_increment,
  `id_category` int(10) unsigned NOT NULL default '0',
  `type` enum('score','flag') NOT NULL default 'score',
  `score` float NOT NULL default '0',
  `competence_type` enum('skill','attitude','_unknown') NOT NULL default 'skill',
  `score_min` float NOT NULL default '0',
  PRIMARY KEY  (`id_competence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_category`
--

CREATE TABLE `learning_competence_category` (
  `id_competence_category` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id_competence_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_category_text`
--

CREATE TABLE `learning_competence_category_text` (
  `id_category` int(10) unsigned NOT NULL default '0',
  `id_text` int(10) unsigned NOT NULL auto_increment,
  `lang_code` varchar(255) NOT NULL,
  `text_name` varchar(255) NOT NULL,
  `text_desc` text,
  PRIMARY KEY  (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_category_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_course`
--

CREATE TABLE `learning_competence_course` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_course` int(10) unsigned NOT NULL default '0',
  `score` float NOT NULL default '0',
  PRIMARY KEY  (`id_competence`,`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_required`
--

CREATE TABLE `learning_competence_required` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `idst` int(10) unsigned NOT NULL default '0',
  `type_of` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_competence`,`idst`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_required`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_text`
--

CREATE TABLE `learning_competence_text` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_text` int(10) unsigned NOT NULL auto_increment,
  `lang_code` varchar(255) NOT NULL,
  `text_name` varchar(255) NOT NULL,
  `text_desc` text,
  PRIMARY KEY  (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_track`
--

CREATE TABLE `learning_competence_track` (
  `id_track` int(10) unsigned NOT NULL auto_increment,
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_user` int(10) unsigned NOT NULL default '0',
  `source` varchar(255) NOT NULL default '',
  `date_assignment` datetime NOT NULL default '0000-00-00 00:00:00',
  `score` float NOT NULL default '0',
  PRIMARY KEY  (`id_track`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_track`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_user`
--

CREATE TABLE `learning_competence_user` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_user` int(10) unsigned NOT NULL default '0',
  `score_init` float NOT NULL default '0',
  `score_got` float NOT NULL default '0',
  PRIMARY KEY  (`id_competence`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course`
--

CREATE TABLE `learning_course` (
  `idCourse` int(11) NOT NULL auto_increment,
  `idCategory` int(11) NOT NULL default '0',
  `code` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `lang_code` varchar(100) NOT NULL default '',
  `status` int(1) NOT NULL default '0',
  `level_show_user` int(11) NOT NULL default '0',
  `subscribe_method` tinyint(1) NOT NULL default '0',
  `linkSponsor` varchar(255) NOT NULL default '',
  `imgSponsor` varchar(255) NOT NULL default '',
  `img_course` varchar(255) NOT NULL default '',
  `img_material` varchar(255) NOT NULL default '',
  `img_othermaterial` varchar(255) NOT NULL default '',
  `course_demo` varchar(255) NOT NULL default '',
  `mediumTime` int(10) unsigned NOT NULL default '0',
  `permCloseLO` tinyint(1) NOT NULL default '0',
  `userStatusOp` int(11) NOT NULL default '0',
  `difficult` enum('veryeasy','easy','medium','difficult','verydifficult') NOT NULL default 'medium',
  `show_progress` tinyint(1) NOT NULL default '1',
  `show_time` tinyint(1) NOT NULL default '0',
  `show_extra_info` tinyint(1) NOT NULL default '0',
  `show_rules` tinyint(1) NOT NULL default '0',
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `hour_begin` varchar(5) NOT NULL default '',
  `hour_end` varchar(5) NOT NULL default '',
  `valid_time` int(10) NOT NULL default '0',
  `max_num_subscribe` int(11) NOT NULL default '0',
  `min_num_subscribe` int(11) NOT NULL default '0',
  `max_sms_budget` double NOT NULL default '0',
  `selling` tinyint(1) NOT NULL default '0',
  `prize` varchar(255) NOT NULL default '',
  `course_type` varchar(255) NOT NULL default 'elearning',
  `policy_point` varchar(255) NOT NULL default '',
  `point_to_all` int(10) NOT NULL default '0',
  `course_edition` tinyint(1) NOT NULL default '0',
  `classrooms` varchar(255) NOT NULL default '',
  `certificates` varchar(255) NOT NULL default '',
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `security_code` varchar(255) NOT NULL default '',
  `imported_from_connection` varchar(255) default NULL,
  `course_quota` varchar(255) NOT NULL default '-1',
  `used_space` varchar(255) NOT NULL default '0',
  `course_vote` double NOT NULL default '0',
  `allow_overbooking` tinyint(1) NOT NULL default '0',
  `can_subscribe` tinyint(1) NOT NULL default '0',
  `sub_start_date` datetime default NULL,
  `sub_end_date` datetime default NULL,
  `advance` varchar(255) NOT NULL default '',
  `show_who_online` tinyint(1) NOT NULL,
  `direct_play` tinyint(1) NOT NULL,
  `autoregistration_code` varchar(255) NOT NULL,
  `use_logo_in_courselist` tinyint(1) NOT NULL,
  `show_result` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`idCourse`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath`
--

CREATE TABLE `learning_coursepath` (
  `id_path` int(11) NOT NULL auto_increment,
  `path_code` varchar(255) NOT NULL default '',
  `path_name` varchar(255) NOT NULL default '',
  `path_descr` text NOT NULL,
  `subscribe_method` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath_courses`
--

CREATE TABLE `learning_coursepath_courses` (
  `id_path` int(11) NOT NULL default '0',
  `id_item` int(11) NOT NULL default '0',
  `in_slot` int(11) NOT NULL default '0',
  `prerequisites` text NOT NULL,
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id_path`,`id_item`,`in_slot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath_courses`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath_slot`
--

CREATE TABLE `learning_coursepath_slot` (
  `id_slot` int(11) NOT NULL auto_increment,
  `id_path` int(11) NOT NULL default '0',
  `min_selection` int(3) NOT NULL default '0',
  `max_selection` int(3) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id_slot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath_slot`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath_user`
--

CREATE TABLE `learning_coursepath_user` (
  `id_path` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `waiting` tinyint(1) NOT NULL default '0',
  `subscribed_by` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_path`,`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursereport`
--

CREATE TABLE `learning_coursereport` (
  `id_report` int(11) NOT NULL auto_increment,
  `id_course` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `max_score` float NOT NULL default '0',
  `required_score` float NOT NULL default '0',
  `weight` int(3) NOT NULL default '0',
  `show_to_user` enum('true','false') NOT NULL default 'true',
  `use_for_final` enum('true','false') NOT NULL default 'true',
  `sequence` int(3) NOT NULL default '0',
  `source_of` enum('test','activity','scorm','final_vote') NOT NULL default 'test',
  `id_source` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`id_report`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursereport`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursereport_score`
--

CREATE TABLE `learning_coursereport_score` (
  `id_report` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `score` double(5,2) NOT NULL default '0.00',
  `score_status` enum('valid','not_checked','not_passed','passed') NOT NULL default 'valid',
  `comment` text NOT NULL,
  PRIMARY KEY  (`id_report`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursereport_score`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_courseuser`
--

CREATE TABLE `learning_courseuser` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `edition_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `date_inscr` datetime default NULL,
  `date_first_access` datetime default NULL,
  `date_complete` datetime default NULL,
  `status` int(1) NOT NULL default '0',
  `waiting` tinyint(1) NOT NULL default '0',
  `subscribed_by` int(11) NOT NULL default '0',
  `score_given` int(4) default NULL,
  `imported_from_connection` varchar(255) default NULL,
  `absent` tinyint(1) NOT NULL default '0',
  `cancelled_by` int(11) NOT NULL default '0',
  `new_forum_post` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idUser`,`idCourse`,`edition_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_courseuser`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course_edition`
--

CREATE TABLE `learning_course_edition` (
  `idCourseEdition` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `code` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `status` int(1) NOT NULL default '0',
  `img_material` varchar(255) NOT NULL default '',
  `img_othermaterial` varchar(255) NOT NULL default '',
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `hour_begin` varchar(5) NOT NULL default '',
  `hour_end` varchar(5) NOT NULL default '',
  `classrooms` varchar(255) NOT NULL default '',
  `max_num_subscribe` int(11) NOT NULL default '0',
  `min_num_subscribe` int(11) NOT NULL default '0',
  `price` varchar(255) NOT NULL default '',
  `advance` varchar(255) NOT NULL default '',
  `edition_type` varchar(255) NOT NULL default 'elearning',
  `allow_overbooking` tinyint(1) NOT NULL default '0',
  `can_subscribe` tinyint(1) NOT NULL default '0',
  `sub_start_date` datetime default NULL,
  `sub_end_date` datetime default NULL,
  PRIMARY KEY  (`idCourseEdition`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course_edition`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course_file`
--

CREATE TABLE `learning_course_file` (
  `id_file` int(11) NOT NULL auto_increment,
  `id_course` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course_point`
--

CREATE TABLE `learning_course_point` (
  `idCourse` int(11) NOT NULL default '0',
  `idField` int(11) NOT NULL default '0',
  `point` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course_point`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio`
--

CREATE TABLE `learning_eportfolio` (
  `id_portfolio` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `custom_pdp_descr` text NOT NULL,
  `custom_competence_descr` text NOT NULL,
  PRIMARY KEY  (`id_portfolio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_competence`
--

CREATE TABLE `learning_eportfolio_competence` (
  `id_competence` int(11) NOT NULL auto_increment,
  `id_portfolio` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  `min_score` int(5) NOT NULL default '0',
  `max_score` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `block_competence` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_competence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_competence`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_competence_invite`
--

CREATE TABLE `learning_eportfolio_competence_invite` (
  `invited_user` int(11) NOT NULL default '0',
  `sender` int(11) NOT NULL default '0',
  `id_portfolio` int(11) NOT NULL default '0',
  `message_text` text NOT NULL,
  `refused` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`invited_user`,`sender`,`id_portfolio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_competence_invite`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_competence_score`
--

CREATE TABLE `learning_eportfolio_competence_score` (
  `id_portfolio` int(11) NOT NULL default '0',
  `id_competence` int(11) NOT NULL default '0',
  `estimated_user` int(11) NOT NULL default '0',
  `from_user` int(11) NOT NULL default '0',
  `score` int(11) NOT NULL default '0',
  `comment` text NOT NULL,
  `status` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id_portfolio`,`id_competence`,`estimated_user`,`from_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_competence_score`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_curriculum`
--

CREATE TABLE `learning_eportfolio_curriculum` (
  `id_portfolio` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `curriculum_file` varchar(255) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_portfolio`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_curriculum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_member`
--

CREATE TABLE `learning_eportfolio_member` (
  `id_portfolio` int(11) NOT NULL default '0',
  `idst_member` int(11) NOT NULL default '0',
  `user_is_admin` enum('false','true') NOT NULL default 'false',
  PRIMARY KEY  (`id_portfolio`,`idst_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_member`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_pdp`
--

CREATE TABLE `learning_eportfolio_pdp` (
  `id_pdp` int(11) NOT NULL auto_increment,
  `id_portfolio` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  `allow_answer` enum('true','false') NOT NULL default 'true',
  `max_answer` int(11) NOT NULL default '0',
  `answer_mod_for_day` int(11) NOT NULL default '0',
  `sequence` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_pdp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_pdp`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_pdp_answer`
--

CREATE TABLE `learning_eportfolio_pdp_answer` (
  `id_answer` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `id_pdp` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_pdp_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_presentation`
--

CREATE TABLE `learning_eportfolio_presentation` (
  `id_presentation` int(11) NOT NULL auto_increment,
  `id_portfolio` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `owner` int(11) NOT NULL default '0',
  `show_pdp` tinyint(1) NOT NULL default '0',
  `show_competence` tinyint(1) NOT NULL default '0',
  `show_curriculum` tinyint(1) NOT NULL default '0',
  `pubblication_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_presentation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_presentation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_presentation_attach`
--

CREATE TABLE `learning_eportfolio_presentation_attach` (
  `id_presentation` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `id_file` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_presentation`,`id_user`,`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_presentation_attach`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_presentation_invite`
--

CREATE TABLE `learning_eportfolio_presentation_invite` (
  `id_presentation` int(11) NOT NULL default '0',
  `recipient_mail` varchar(255) NOT NULL default '',
  `security_code` varchar(255) NOT NULL default '',
  `send_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_presentation`,`recipient_mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_presentation_invite`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_faq`
--

CREATE TABLE `learning_faq` (
  `idFaq` int(11) NOT NULL auto_increment,
  `idCategory` int(11) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `answer` text NOT NULL,
  `sequence` int(5) NOT NULL default '0',
  PRIMARY KEY  (`idFaq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_faq`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_faq_cat`
--

CREATE TABLE `learning_faq_cat` (
  `idCategory` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_faq_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum`
--

CREATE TABLE `learning_forum` (
  `idForum` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `num_thread` int(11) NOT NULL default '0',
  `num_post` int(11) NOT NULL default '0',
  `last_post` int(11) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `emoticons` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idForum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forummessage`
--

CREATE TABLE `learning_forummessage` (
  `idMessage` int(11) NOT NULL auto_increment,
  `idThread` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `answer_tree` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `generator` tinyint(1) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  `modified_by` int(11) NOT NULL default '0',
  `modified_by_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forummessage`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forumthread`
--

CREATE TABLE `learning_forumthread` (
  `idThread` int(11) NOT NULL auto_increment,
  `id_edition` int(11) NOT NULL default '0',
  `idForum` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `author` int(11) NOT NULL default '0',
  `num_post` int(11) NOT NULL default '0',
  `num_view` int(5) NOT NULL default '0',
  `last_post` int(11) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `erased` tinyint(1) NOT NULL default '0',
  `emoticons` varchar(255) NOT NULL default '',
  `rilevantForum` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idThread`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forumthread`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_access`
--

CREATE TABLE `learning_forum_access` (
  `idForum` int(11) NOT NULL default '0',
  `idMember` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idForum`,`idMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_access`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_notifier`
--

CREATE TABLE `learning_forum_notifier` (
  `id_notify` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `notify_is_a` enum('forum','thread') NOT NULL default 'forum',
  PRIMARY KEY  (`id_notify`,`id_user`,`notify_is_a`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_notifier`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_sema`
--

CREATE TABLE `learning_forum_sema` (
  `idc` int(11) NOT NULL default '0',
  `idprof` int(11) NOT NULL default '0',
  `iduser` int(11) NOT NULL default '0',
  `idmsg` int(11) NOT NULL default '0',
  `idsema` int(11) NOT NULL default '0',
  `idsemaitem` int(11) NOT NULL default '0',
  PRIMARY KEY  (`iduser`,`idmsg`,`idsema`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_sema`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_timing`
--

CREATE TABLE `learning_forum_timing` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idUser`,`idCourse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_timing`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_glossary`
--

CREATE TABLE `learning_glossary` (
  `idGlossary` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idGlossary`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_glossary`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_glossaryterm`
--

CREATE TABLE `learning_glossaryterm` (
  `idTerm` int(11) NOT NULL auto_increment,
  `idGlossary` int(11) NOT NULL default '0',
  `term` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idTerm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_glossaryterm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_homerepo`
--

CREATE TABLE `learning_homerepo` (
  `idRepo` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `idResource` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idAuthor` int(11) NOT NULL default '0',
  `version` varchar(8) NOT NULL default '',
  `difficult` enum('_VERYEASY','_EASY','_MEDIUM','_DIFFICULT','_VERYDIFFICULT') NOT NULL default '_VERYEASY',
  `description` text NOT NULL,
  `language` varchar(50) NOT NULL default '',
  `resource` varchar(255) NOT NULL default '',
  `objective` text NOT NULL,
  `dateInsert` datetime NOT NULL default '0000-00-00 00:00:00',
  `idOwner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idRepo`),
  KEY `idParent` (`idParent`),
  KEY `path` (`path`),
  KEY `idOwner` (`idOwner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_homerepo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_htmlfront`
--

CREATE TABLE `learning_htmlfront` (
  `id_course` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  PRIMARY KEY  (`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_htmlfront`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_htmlpage`
--

CREATE TABLE `learning_htmlpage` (
  `idPage` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `textof` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idPage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_htmlpage`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_instmsg`
--

CREATE TABLE `learning_instmsg` (
  `id_msg` bigint(20) NOT NULL auto_increment,
  `id_sender` int(11) NOT NULL default '0',
  `id_receiver` int(11) NOT NULL default '0',
  `msg` text,
  `status` smallint(2) NOT NULL default '0',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_msg`),
  KEY `id_sender` (`id_sender`,`id_receiver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_instmsg`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_light_repo`
--

CREATE TABLE `learning_light_repo` (
  `id_repository` int(11) NOT NULL auto_increment,
  `id_course` int(11) NOT NULL default '0',
  `repo_title` varchar(255) NOT NULL default '',
  `repo_descr` text NOT NULL,
  PRIMARY KEY  (`id_repository`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_light_repo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_light_repo_files`
--

CREATE TABLE `learning_light_repo_files` (
  `id_file` int(11) NOT NULL auto_increment,
  `id_repository` int(11) NOT NULL default '0',
  `file_name` varchar(255) NOT NULL default '',
  `file_descr` text NOT NULL,
  `id_author` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_light_repo_files`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_light_repo_user`
--

CREATE TABLE `learning_light_repo_user` (
  `id_repo` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `last_enter` datetime NOT NULL default '0000-00-00 00:00:00',
  `repo_lock` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_repo`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_light_repo_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_link`
--

CREATE TABLE `learning_link` (
  `idLink` int(11) NOT NULL auto_increment,
  `idCategory` int(11) NOT NULL default '0',
  `title` varchar(150) NOT NULL default '',
  `link_address` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `description` text NOT NULL,
  `sequence` int(5) NOT NULL default '0',
  PRIMARY KEY  (`idLink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_link`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_link_cat`
--

CREATE TABLE `learning_link_cat` (
  `idCategory` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_link_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_lo_param`
--

CREATE TABLE `learning_lo_param` (
  `id` int(11) NOT NULL auto_increment,
  `idParam` int(11) NOT NULL default '0',
  `param_name` varchar(20) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idParam_name` (`idParam`,`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_lo_param`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_lo_types`
--

CREATE TABLE `learning_lo_types` (
  `objectType` varchar(20) NOT NULL default '',
  `className` varchar(20) NOT NULL default '',
  `fileName` varchar(50) NOT NULL default '',
  `classNameTrack` varchar(255) NOT NULL default '',
  `fileNameTrack` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`objectType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_lo_types`
--

INSERT INTO `learning_lo_types` (`objectType`, `className`, `fileName`, `classNameTrack`, `fileNameTrack`) VALUES
('faq', 'Learning_Faq', 'learning.faq.php', 'Track_Faq', 'track.faq.php'),
('glossary', 'Learning_Glossary', 'learning.glossary.php', 'Track_Glossary', 'track.glossary.php'),
('htmlpage', 'Learning_Htmlpage', 'learning.htmlpage.php', 'Track_Htmlpage', 'track.htmlpage.php'),
('item', 'Learning_Item', 'learning.item.php', 'Track_Item', 'track.item.php'),
('link', 'Learning_Link', 'learning.link.php', 'Track_Link', 'track.link.php'),
('poll', 'Learning_Poll', 'learning.poll.php', 'Track_Poll', 'track.poll.php'),
('scormorg', 'Learning_ScormOrg', 'learning.scorm.php', 'Track_Scormorg', 'track.scorm.php'),
('test', 'Learning_Test', 'learning.test.php', 'Track_Test', 'track.test.php');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_materials_lesson`
--

CREATE TABLE `learning_materials_lesson` (
  `idLesson` int(11) NOT NULL auto_increment,
  `author` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idLesson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_materials_lesson`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_materials_track`
--

CREATE TABLE `learning_materials_track` (
  `idTrack` int(11) NOT NULL auto_increment,
  `idResource` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_materials_track`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menu`
--

CREATE TABLE `learning_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menu`
--

INSERT INTO `learning_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_MANAGEMENT_COURSE', '', 1, 'false'),
(2, '_EXTERNAL_CONTENT', '', 2, 'false'),
(10, '_MIDDLE_AREA', '', 3, 'false'),
(3, '', '', 6, 'true'),
(4, '', '', 7, 'true'),
(5, '', '', 8, 'true'),
(6, '_CLASSROOMS', '', 5, 'false'),
(7, '_MAN_CERTIFICATE', '', 4, 'false'),
(8, '_MANAGEMENT_RESERVATION', '', 9, 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucourse_main`
--

CREATE TABLE `learning_menucourse_main` (
  `idMain` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idMain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucourse_main`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucourse_under`
--

CREATE TABLE `learning_menucourse_under` (
  `idCourse` int(11) NOT NULL default '0',
  `idModule` int(11) NOT NULL default '0',
  `idMain` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `my_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idCourse`,`idModule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucourse_under`
--

INSERT INTO `learning_menucourse_under` (`idCourse`, `idModule`, `idMain`, `sequence`, `my_name`) VALUES
(0, 1, 0, 1, ''),
(0, 2, 0, 2, ''),
(0, 3, 0, 3, ''),
(0, 4, 0, 4, ''),
(0, 5, 0, 5, ''),
(0, 6, 0, 6, ''),
(0, 7, 0, 7, ''),
(0, 8, 0, 8, ''),
(0, 9, 0, 9, ''),
(0, 32, 0, 10, ''),
(0, 33, 0, 11, ''),
(0, 34, 0, 12, ''),
(0, 35, 1, 1, ''),
(0, 36, 1, 1, ''),
(0, 37, 1, 1, ''),
(0, 38, 1, 1, ''),
(0, 39, 1, 1, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucustom`
--

CREATE TABLE `learning_menucustom` (
  `idCustom` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idCustom`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucustom`
--

INSERT INTO `learning_menucustom` (`idCustom`, `title`, `description`) VALUES
(1, 'Full menu', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucustom_main`
--

CREATE TABLE `learning_menucustom_main` (
  `idMain` int(11) NOT NULL auto_increment,
  `idCustom` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idMain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucustom_main`
--

INSERT INTO `learning_menucustom_main` (`idMain`, `idCustom`, `sequence`, `name`, `image`) VALUES
(1, 1, 1, 'Students area', 'room.gif'),
(2, 1, 2, 'Collaboration area', 'community.gif'),
(3, 1, 3, 'Teacher area', 'find.gif'),
(4, 1, 4, 'Stat area', 'report.gif');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucustom_under`
--

CREATE TABLE `learning_menucustom_under` (
  `idCustom` int(11) NOT NULL default '0',
  `idModule` int(11) NOT NULL default '0',
  `idMain` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `my_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idCustom`,`idModule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucustom_under`
--

INSERT INTO `learning_menucustom_under` (`idCustom`, `idModule`, `idMain`, `sequence`, `my_name`) VALUES
(0, 1, 0, 1, ''),
(0, 2, 0, 2, ''),
(0, 3, 0, 3, ''),
(0, 4, 0, 4, ''),
(0, 5, 0, 5, ''),
(0, 6, 0, 6, ''),
(0, 7, 0, 7, ''),
(0, 8, 0, 8, ''),
(0, 9, 0, 9, ''),
(0, 32, 0, 10, ''),
(0, 33, 0, 11, ''),
(1, 10, 1, 1, ''),
(1, 11, 1, 2, ''),
(1, 25, 1, 3, ''),
(1, 13, 1, 4, ''),
(1, 14, 1, 5, ''),
(1, 15, 1, 6, ''),
(1, 17, 1, 8, ''),
(1, 18, 1, 9, ''),
(1, 19, 2, 1, ''),
(1, 20, 2, 2, ''),
(1, 21, 2, 3, ''),
(1, 22, 2, 4, ''),
(1, 23, 2, 5, ''),
(1, 24, 2, 6, ''),
(1, 12, 3, 1, ''),
(1, 26, 3, 2, ''),
(1, 27, 3, 3, ''),
(1, 28, 3, 4, ''),
(1, 29, 4, 1, ''),
(1, 30, 4, 2, ''),
(1, 31, 4, 3, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menu_under`
--

CREATE TABLE `learning_menu_under` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menu_under`
--

INSERT INTO `learning_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'course', '_COURSE', 'course_list', 'view', NULL, 1, 'class.course.php', 'Module_Course'),
(2, 1, 'manmenu', '_MAN_MENU', 'mancustom', 'view', NULL, 2, 'class.manmenu.php', 'Module_Manmenu'),
(3, 1, 'coursepath', '_COURSEPATH', 'pathlist', 'view', NULL, 3, 'class.coursepath.php', 'Module_Coursepath'),
(4, 1, 'catalogue', '_CATALOGUE', 'catlist', 'view', NULL, 4, 'class.catalogue.php', 'Module_Catalogue'),
(5, 2, 'webpages', '_WEBPAGES', 'webpages', 'view', NULL, 1, 'class.webpages.php', 'Module_Webpages'),
(6, 2, 'news', '_NEWS', 'news', 'view', NULL, 2, 'class.news.php', 'Module_News'),
(7, 3, 'questcategory', '_QUESTCATEGORY', 'questcategory', 'view', NULL, 1, 'class.questcategory.php', 'Module_Questcategory'),
(8, 4, 'eportfolio', '_EPORTFOLIO', 'eportfolio', 'view', NULL, 1, 'class.eportfolio.php', 'Module_Eportfolio'),
(9, 5, 'report', '_REPORT', 'reportlist', 'view', NULL, 1, 'class.report.php', 'Module_Report'),
(10, 1, 'preassessment', '_PREASSESSMENT', 'assesmentlist', 'view', NULL, 5, 'class.preassessment.php', 'Module_PreAssessment'),
(11, 6, 'classevent', '_CLASSEVENT', 'main', 'view', NULL, 3, 'class.classevent.php', 'Module_Classevent'),
(12, 6, 'classlocation', '_CLASSLOCATION', 'main', 'view', NULL, 2, 'class.classlocation.php', 'Module_Classlocation'),
(13, 6, 'classroom', '_CLASSROOM', 'classroom', 'view', NULL, 3, 'class.classroom.php', 'Module_Classroom'),
(14, 7, 'certificate', '_CERTIFICATE', 'certificate', 'view', NULL, 1, 'class.certificate.php', 'Module_Certificate'),
(15, 7, 'certificate', '_REPORT_CERTIFICATE', 'report_certificate', 'view', NULL, 2, 'class.certificate.php', 'Module_Certificate'),
(17, 8, 'reservation', '_EVENTS', 'view_event', 'view', NULL, 1, 'class.reservation.php', 'Module_Reservation'),
(18, 8, 'reservation', '_CATEGORY', 'view_category', 'view', NULL, 2, 'class.reservation.php', 'Module_Reservation'),
(20, 8, 'reservation', '_RESERVATION', 'view_registration', 'view', NULL, 4, 'class.reservation.php', 'Module_Reservation'),
(21, 10, 'middlearea', '_MIDDLE_AREA', 'view_area', 'view', NULL, 1, 'class.middlearea.php', 'Module_MiddleArea'),
(22, 10, 'internal_news', '_NEWS_INTERNAL', 'news', 'view', NULL, 2, 'class.internal_news.php', 'Module_Internal_News'),
(23, 7, 'meta_certificate', '_META_CERTIFICATE', 'meta_certificate', 'view', NULL, 3, 'class.meta_certificate.php', 'Module_Meta_Certificate'),
(24, 1, 'competences', '_COMPETENCES', 'main', 'view', NULL, 5, 'class.competences.php', 'Module_Competences');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_middlearea`
--

CREATE TABLE `learning_middlearea` (
  `obj_index` varchar(255) NOT NULL default '',
  `disabled` tinyint(1) NOT NULL default '0',
  `idst_list` text NOT NULL,
  PRIMARY KEY  (`obj_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_middlearea`
--

INSERT INTO `learning_middlearea` (`obj_index`, `disabled`, `idst_list`) VALUES
('mo_4', 1, 'a:0:{}'),
('mo_5', 1, 'a:0:{}'),
('mo_6', 1, 'a:0:{}'),
('mo_8', 1, 'a:0:{}'),
('mo_9', 1, 'a:0:{}'),
('public_forum', 1, 'a:0:{}'),
('course_autoregistration', 1, 'a:0:{}'),
('user_details_short', 1, 'a:0:{}'),
('search_form', 1, 'a:0:{}'),
('news', 1, 'a:0:{}');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_module`
--

CREATE TABLE `learning_module` (
  `idModule` int(11) NOT NULL auto_increment,
  `module_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `token_associated` varchar(100) NOT NULL default '',
  `file_name` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `module_info` text NOT NULL,
  PRIMARY KEY  (`idModule`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_module`
--

INSERT INTO `learning_module` (`idModule`, `module_name`, `default_op`, `default_name`, `token_associated`, `file_name`, `class_name`, `module_info`) VALUES
(1, 'course', 'mycourses', '_MYCOURSES', 'view', 'class.course.php', 'Module_Course', ''),
(2, 'coursecatalogue', 'courselist', '_CATALOGUE', 'view', 'class.coursecatalogue.php', 'Module_Coursecatalogue', ''),
(3, 'profile', 'profile', '_PROFILE', 'view', 'class.profile.php', 'Module_Profile', 'type=user'),
(4, 'myfiles', 'myfiles', '_MYFILES', 'view', 'class.myfiles.php', 'Module_MyFiles', 'type=user'),
(5, 'mygroup', 'group', '_MYGROUP', 'view', 'class.mygroup.php', 'Module_MyGroup', 'type=user'),
(6, 'myfriends', 'myfriends', '_MYFRIENDS', 'view', 'class.myfriends.php', 'Module_MyFriends', 'type=user'),
(7, 'mycertificate', 'mycertificate', '_MY_CERTIFICATE', 'view', 'class.mycertificate.php', 'Module_MyCertificate', 'type=user'),
(8, 'eportfolio', 'eportfolio', '_EPORTFOLIO', 'view', 'class.eportfolio.php', 'Module_EPortfolio', 'type=user'),
(9, 'userevent', 'user_display', '_MYEVENTS', 'view', 'class.userevent.php', 'Module_UserEvent', 'type=user'),
(10, 'course', 'infocourse', '_INFCOURSE', 'view_info', 'class.course.php', 'Module_Course', ''),
(11, 'advice', 'advice', '_ADVICE', 'view', 'class.advice.php', 'Module_Advice', ''),
(12, 'storage', 'display', '_STORAGE', 'view', 'class.storage.php', 'Module_Storage', ''),
(13, 'calendar', 'calendar', '_CALENDAR', 'view', 'class.calendar.php', 'Module_Calendar', ''),
(14, 'gradebook', 'showgrade', '_GRADEBOOK', 'view', 'class.gradebook.php', 'Module_Gradebook', ''),
(15, 'notes', 'notes', '_NOTES', 'view', 'class.notes.php', 'Module_Notes', ''),
(16, 'reservation', 'reservation', '_RESERVATION', 'view', 'class.reservation.php', 'Module_Reservation', ''),
(17, 'light_repo', 'repolist', '_LIGHT_REPO', 'view', 'class.light_repo.php', 'Module_LightRepo', ''),
(18, 'htmlfront', 'showhtml', '_HTMLFRONT', 'view', 'class.htmlfront.php', 'Module_Htmlfront', ''),
(19, 'forum', 'forum', '_FORUM', 'view', 'class.forum.php', 'Module_Forum', ''),
(20, 'wiki', 'main', '_WIKI', 'view', 'class.wiki.php', 'Module_Wiki', ''),
(21, 'chat', 'chat', '_CHAT', 'view', 'class.chat.php', 'Module_Chat', ''),
(22, 'conference', 'list', '_CONFERENCE', 'view', 'class.conference.php', 'Module_Conference', ''),
(23, 'project', 'project', '_PROJECT', 'view', 'class.project.php', 'Module_Project', ''),
(24, 'groups', 'groups', '_GROUPS', 'view', 'class.groups.php', 'Module_Groups', ''),
(25, 'organization', 'organization', '_ORGANIZATION', 'view', 'class.organization.php', 'Module_Organization', ''),
(26, 'coursereport', 'coursereport', '_COURSEREPORT', 'view', 'class.coursereport.php', 'Module_CourseReport', ''),
(27, 'newsletter', 'view', '_NEWSLETTER', 'view', 'class.newsletter.php', 'Module_Newsletter', ''),
(28, 'manmenu', 'manmenu', '_MAN_MENU', 'view', 'class.manmenu.php', 'Module_CourseManmenu', ''),
(29, 'statistic', 'statistic', '_STAT', 'view', 'class.statistic.php', 'Module_Statistic', ''),
(30, 'stats', 'statuser', '_STATUSER', 'view_user', 'class.stats.php', 'Module_Stats', ''),
(31, 'stats', 'statcourse', '_STATCOURSE', 'view_course', 'class.stats.php', 'Module_Stats', ''),
(32, 'public_forum', 'public_forum', '_PUBLIC_FORUM', 'view', 'class.public_forum.php', 'Module_Public_Forum', ''),
(33, 'course_autoregistration', 'course_autoregistration', '_COURSE_AUTOREGISTRATION', 'view', 'class.course_autoregistration.php', 'Module_Course_Autoregistration', ''),
(34, 'mycompetences', 'mycompetences', '_MYCOMPETENCES', 'view', 'class.mycompetences.php', 'Module_MyCompetences', 'type=user'),
(35, 'public_user_admin', 'public_user_admin', '_PUBLIC_USER_ADMIN', 'view_org_chart', 'class.public_user_admin.php', 'Module_Public_User_Admin', 'type=public_admin'),
(36, 'public_course_admin', 'public_course_admin', '_PUBLIC_COURSE_ADMIN', 'view', 'class.public_course_admin.php', 'Module_Public_Course_Admin', 'type=public_admin'),
(37, 'public_subscribe_admin', 'public_subscribe_admin', '_PUBLIC_SUBSCRIBE_ADMIN', 'view', 'class.public_subscribe_admin.php', 'Module_Public_Subscribe_Admin', 'type=public_admin'),
(38, 'public_report_admin', 'reportlist', '_PUBLIC_REPORT_ADMIN', 'view', 'class.public_report_admin.php', 'Module_Public_Report_Admin', 'type=public_admin'),
(39, 'public_newsletter_admin', 'newsletter', '_PUBLIC_NEWSLETTER_ADMIN', 'view', 'class.public_newsletter_admin.php', 'Module_Public_Newsletter_Admin', 'type=public_admin'),
(40, 'quest_bank', 'main', '_QUEST_BANK', 'view', 'class.quest_bank.php', 'Module_QuestBank', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_news`
--

CREATE TABLE `learning_news` (
  `idNews` int(11) NOT NULL auto_increment,
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL default '',
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idNews`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_news`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_news_internal`
--

CREATE TABLE `learning_news_internal` (
  `idNews` int(11) NOT NULL auto_increment,
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL default '',
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `viewer` longtext NOT NULL,
  PRIMARY KEY  (`idNews`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_news_internal`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_notes`
--

CREATE TABLE `learning_notes` (
  `idNotes` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(150) NOT NULL default '',
  `textof` text NOT NULL,
  PRIMARY KEY  (`idNotes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_notes`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_organization`
--

CREATE TABLE `learning_organization` (
  `idOrg` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `idResource` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idAuthor` int(11) NOT NULL default '0',
  `version` varchar(8) NOT NULL default '',
  `difficult` enum('_VERYEASY','_EASY','_MEDIUM','_DIFFICULT','_VERYDIFFICULT') NOT NULL default '_VERYEASY',
  `description` text NOT NULL,
  `language` varchar(50) NOT NULL default '',
  `resource` varchar(255) NOT NULL default '',
  `objective` text NOT NULL,
  `dateInsert` datetime NOT NULL default '0000-00-00 00:00:00',
  `idCourse` int(11) NOT NULL default '0',
  `prerequisites` varchar(255) NOT NULL default '',
  `isTerminator` tinyint(4) NOT NULL default '0',
  `idParam` int(11) NOT NULL default '0',
  `visible` tinyint(4) NOT NULL default '1',
  `milestone` enum('start','end','-') NOT NULL default '-',
  `width` VARCHAR( 4 ) NOT NULL ,
  `height` VARCHAR( 4 ) NOT NULL ,
  `publish_from` DATETIME NULL ,
  `publish_to` DATETIME NULL ,
  PRIMARY KEY  (`idOrg`),
  KEY `idParent` (`idParent`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_organization`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_organization_access`
--

CREATE TABLE `learning_organization_access` (
  `idOrgAccess` int(11) NOT NULL default '0',
  `kind` set('user','group') NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idOrgAccess`,`kind`,`value`),
  KEY `idObject` (`idOrgAccess`),
  KEY `kind` (`kind`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Access to items in area lesson (organization)';

--
-- Dump dei dati per la tabella `learning_organization_access`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel`
--

CREATE TABLE `learning_pagel` (
  `id` int(11) NOT NULL auto_increment,
  `idc` int(11) NOT NULL default '0',
  `iduser` int(11) NOT NULL default '0',
  `idprof` int(11) NOT NULL default '0',
  `idatvt` int(11) NOT NULL default '0',
  `idcatval` int(11) NOT NULL default '0',
  `idvote` int(11) NOT NULL default '0',
  `adate` date NOT NULL default '0000-00-00',
  `title` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_atvt`
--

CREATE TABLE `learning_pagel_atvt` (
  `id` int(11) NOT NULL auto_increment,
  `idc` int(11) NOT NULL default '0',
  `idprof` int(11) NOT NULL default '0',
  `dsc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_atvt`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_sema`
--

CREATE TABLE `learning_pagel_sema` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_sema`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_sema_items`
--

CREATE TABLE `learning_pagel_sema_items` (
  `id` int(11) NOT NULL auto_increment,
  `idref` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `val` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_sema_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_vote`
--

CREATE TABLE `learning_pagel_vote` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_vote`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_vote_items`
--

CREATE TABLE `learning_pagel_vote_items` (
  `id` int(11) NOT NULL auto_increment,
  `idref` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `val` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_vote_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_poll`
--

CREATE TABLE `learning_poll` (
  `id_poll` int(11) NOT NULL auto_increment,
  `author` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`id_poll`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_poll`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pollquest`
--

CREATE TABLE `learning_pollquest` (
  `id_quest` int(11) NOT NULL auto_increment,
  `id_poll` int(11) NOT NULL default '0',
  `id_category` int(11) NOT NULL default '0',
  `type_quest` varchar(255) NOT NULL default '',
  `title_quest` text NOT NULL,
  `sequence` int(5) NOT NULL default '0',
  `page` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id_quest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pollquest`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pollquestanswer`
--

CREATE TABLE `learning_pollquestanswer` (
  `id_answer` int(11) NOT NULL auto_increment,
  `id_quest` int(11) NOT NULL default '0',
  `sequence` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  PRIMARY KEY  (`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pollquestanswer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pollquest_extra`
--

CREATE TABLE `learning_pollquest_extra` (
  `id_quest` int(11) NOT NULL default '0',
  `id_answer` int(11) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`id_quest`,`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pollquest_extra`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_polltrack`
--

CREATE TABLE `learning_polltrack` (
  `id_track` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `id_reference` int(11) NOT NULL default '0',
  `id_poll` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('valid','not_complete') NOT NULL default 'not_complete',
  PRIMARY KEY  (`id_track`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_polltrack`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_polltrack_answer`
--

CREATE TABLE `learning_polltrack_answer` (
  `id_track` int(11) NOT NULL default '0',
  `id_quest` int(11) NOT NULL default '0',
  `id_answer` int(11) NOT NULL default '0',
  `more_info` longtext NOT NULL,
  PRIMARY KEY  (`id_track`,`id_quest`,`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_polltrack_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj`
--

CREATE TABLE `learning_prj` (
  `id` int(11) NOT NULL auto_increment,
  `ptitle` varchar(255) NOT NULL default '',
  `pgroup` int(11) NOT NULL default '0',
  `pprog` tinyint(3) NOT NULL default '0',
  `psfiles` tinyint(1) NOT NULL default '0',
  `pstasks` tinyint(1) NOT NULL default '0',
  `psnews` tinyint(1) NOT NULL default '0',
  `pstodo` tinyint(1) NOT NULL default '0',
  `psmsg` tinyint(1) NOT NULL default '0',
  `cid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_files`
--

CREATE TABLE `learning_prj_files` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `ftitle` varchar(255) NOT NULL default '',
  `fver` varchar(255) NOT NULL default '',
  `fdesc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_files`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_news`
--

CREATE TABLE `learning_prj_news` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `ntitle` varchar(255) NOT NULL default '',
  `ndate` date NOT NULL default '0000-00-00',
  `ntxt` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_news`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_tasks`
--

CREATE TABLE `learning_prj_tasks` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tprog` tinyint(3) NOT NULL default '0',
  `tname` varchar(255) NOT NULL default '',
  `tdesc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_tasks`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_todo`
--

CREATE TABLE `learning_prj_todo` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `ttitle` varchar(255) NOT NULL default '',
  `ttxt` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_todo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_users`
--

CREATE TABLE `learning_prj_users` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `flag` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_users`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_quest_category`
--

CREATE TABLE `learning_quest_category` (
  `idCategory` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `textof` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_quest_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_quest_type`
--

CREATE TABLE `learning_quest_type` (
  `type_quest` varchar(255) NOT NULL default '',
  `type_file` varchar(255) NOT NULL default '',
  `type_class` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`type_quest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_quest_type`
--

INSERT INTO `learning_quest_type` (`type_quest`, `type_file`, `type_class`, `sequence`) VALUES
('associate', 'class.associate.php', 'Associate_Question', 8),
('break_page', 'class.break_page.php', 'BreakPage_Question', 10),
('choice', 'class.choice.php', 'Choice_Question', 1),
('choice_multiple', 'class.choice_multiple.php', 'ChoiceMultiple_Question', 2),
('extended_text', 'class.extended_text.php', 'ExtendedText_Question', 3),
('hot_text', 'class.hot_text.php', 'HotText_Question', 6),
('inline_choice', 'class.inline_choice.php', 'InlineChoice_Question', 5),
('text_entry', 'class.text_entry.php', 'TextEntry_Question', 4),
('title', 'class.title.php', 'Title_Question', 9),
('upload', 'class.upload.php', 'Upload_Question', 7);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_quest_type_poll`
--

CREATE TABLE `learning_quest_type_poll` (
  `type_quest` varchar(255) NOT NULL default '',
  `type_file` varchar(255) NOT NULL default '',
  `type_class` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`type_quest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_quest_type_poll`
--

INSERT INTO `learning_quest_type_poll` (`type_quest`, `type_file`, `type_class`, `sequence`) VALUES
('break_page', 'class.break_page.php', 'BreakPage_Question', 5),
('choice', 'class.choice.php', 'Choice_Question', 1),
('choice_multiple', 'class.choice_multiple.php', 'ChoiceMultiple_Question', 2),
('extended_text', 'class.extended_text.php', 'ExtendedText_Question', 3),
('title', 'class.title.php', 'Title_Question', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_repo`
--

CREATE TABLE `learning_repo` (
  `idRepo` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `idResource` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idAuthor` varchar(11) NOT NULL default '0',
  `version` varchar(8) NOT NULL default '',
  `difficult` enum('_VERYEASY','_EASY','_MEDIUM','_DIFFICULT','_VERYDIFFICULT') NOT NULL default '_VERYEASY',
  `description` text NOT NULL,
  `language` varchar(50) NOT NULL default '',
  `resource` varchar(255) NOT NULL default '',
  `objective` text NOT NULL,
  `dateInsert` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idRepo`),
  KEY `idParent` (`idParent`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_repo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report`
--

CREATE TABLE `learning_report` (
  `id_report` int(11) NOT NULL auto_increment,
  `report_name` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `file_name` varchar(255) NOT NULL default '',
  `use_user_selection` enum('true','false') NOT NULL default 'true',
  `enabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_report`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_report`
--

INSERT INTO `learning_report` (`id_report`, `report_name`, `class_name`, `file_name`, `use_user_selection`, `enabled`) VALUES
(1, 'general_report', 'Report_General', 'class.report_general.php', 'true', 0),
(2, 'user_report', 'Report_User', 'class.report_user.php', 'true', 1),
(3, 'competences_report', 'Report_Competences', 'class.report_competences.php', 'true', 0),
(4, 'courses_report', 'Report_Courses', 'class.report_courses.php', 'true', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report_filter`
--

CREATE TABLE `learning_report_filter` (
  `id_filter` int(10) unsigned NOT NULL auto_increment,
  `id_report` int(10) unsigned NOT NULL default '0',
  `author` int(10) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `filter_name` varchar(255) NOT NULL default '',
  `filter_data` text NOT NULL,
  `is_public` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_filter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_report_filter`
--

INSERT INTO `learning_report_filter` (`id_filter`, `id_report`, `author`, `creation_date`, `filter_name`, `filter_data`, `is_public`) VALUES
(1, 2, 0, '2008-08-11 12:00:00', '_STD_REPORT_USER', 'a:5:{s:9:"id_report";s:1:"2";s:11:"report_name";s:22:"all users, all courses";s:11:"rows_filter";a:2:{s:5:"users";a:0:{}s:9:"all_users";b:1;}s:23:"columns_filter_category";s:7:"courses";s:14:"columns_filter";a:7:{s:21:"org_chart_subdivision";i:0;s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}s:11:"sub_filters";a:0:{}s:16:"filter_exclusive";s:1:"1";s:14:"showed_columns";a:14:{i:0;s:7:"_TH_CAT";i:1;s:8:"_TH_CODE";i:2;s:14:"_TH_COURSEPATH";i:3;s:16:"_TH_COURSESTATUS";i:4;s:25:"_TH_USER_INSCRIPTION_DATE";i:5;s:19:"_TH_USER_START_DATE";i:6;s:17:"_TH_USER_END_DATE";i:7;s:20:"_TH_LAST_ACCESS_DATE";i:8;s:15:"_TH_USER_STATUS";i:9;s:20:"_TH_USER_START_SCORE";i:10;s:20:"_TH_USER_FINAL_SCORE";i:11;s:21:"_TH_USER_COURSE_SCORE";i:12;s:23:"_TH_USER_NUMBER_SESSION";i:13;s:21:"_TH_USER_ELAPSED_TIME";}s:13:"custom_fields";a:5:{i:0;a:3:{s:2:"id";i:9;s:5:"label";s:22:"test-filesupplementare";s:8:"selected";b:0;}i:1;a:3:{s:2:"id";i:10;s:5:"label";s:23:"test-camposupplementare";s:8:"selected";b:0;}i:2;a:3:{s:2:"id";i:1;s:5:"label";s:11:"Descrizione";s:8:"selected";b:0;}i:3;a:3:{s:2:"id";i:3;s:5:"label";s:13:"File di Prova";s:8:"selected";b:0;}i:4;a:3:{s:2:"id";i:7;s:5:"label";s:12:"Contatto MSN";s:8:"selected";b:0;}}}}', 1),
(2, 2, 0, '2008-08-11 12:00:00', '_STD_REPORT_LO', 'a:5:{s:9:"id_report";s:1:"2";s:11:"report_name";s:31:"all users, all learning objects";s:11:"rows_filter";a:2:{s:5:"users";a:0:{}s:9:"all_users";b:1;}s:23:"columns_filter_category";s:2:"LO";s:14:"columns_filter";a:6:{s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}s:8:"lo_types";a:8:{s:3:"faq";s:3:"faq";s:8:"glossary";s:8:"glossary";s:8:"htmlpage";s:8:"htmlpage";s:4:"item";s:4:"item";s:4:"link";s:4:"link";s:4:"poll";s:4:"poll";s:8:"scormorg";s:8:"scormorg";s:4:"test";s:4:"test";}s:13:"lo_milestones";a:3:{i:0;s:7:"ml_none";i:1;s:8:"ml_start";i:2;s:6:"ml_end";}s:14:"showed_columns";a:10:{i:0;s:9:"user_name";i:1;s:11:"course_name";i:2;s:13:"course_status";i:3;s:7:"lo_type";i:4;s:7:"lo_name";i:5;s:12:"lo_milestone";i:6;s:12:"firstAttempt";i:7;s:11:"lastAttempt";i:8;s:9:"lo_status";i:9;s:8:"lo_score";}s:13:"custom_fields";a:5:{i:0;a:3:{s:2:"id";i:9;s:5:"label";s:22:"test-filesupplementare";s:8:"selected";b:0;}i:1;a:3:{s:2:"id";i:10;s:5:"label";s:23:"test-camposupplementare";s:8:"selected";b:0;}i:2;a:3:{s:2:"id";i:1;s:5:"label";s:11:"Descrizione";s:8:"selected";b:0;}i:3;a:3:{s:2:"id";i:3;s:5:"label";s:13:"File di Prova";s:8:"selected";b:0;}i:4;a:3:{s:2:"id";i:7;s:5:"label";s:12:"Contatto MSN";s:8:"selected";b:0;}}}}', 1),
(3, 2, 0, '2008-08-11 12:00:00', '_STD_REPORT_DELAY', 'a:5:{s:9:"id_report";s:1:"2";s:11:"report_name";s:28:"Delay analysis for all users";s:11:"rows_filter";a:2:{s:5:"users";a:0:{}s:9:"all_users";b:1;}s:23:"columns_filter_category";s:5:"delay";s:14:"columns_filter";a:8:{s:11:"report_type";s:16:"course_completed";s:21:"day_from_subscription";s:0:"";s:20:"day_until_course_end";s:0:"";s:21:"date_until_course_end";s:0:"";s:21:"org_chart_subdivision";i:0;s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}s:14:"showed_columns";a:7:{i:0;s:9:"_LASTNAME";i:1;s:5:"_NAME";i:2;s:7:"_STATUS";i:3;s:5:"_MAIL";i:4;s:11:"_DATE_INSCR";i:5;s:18:"_DATE_FIRST_ACCESS";i:6;s:22:"_DATE_COURSE_COMPLETED";}}}', 1),
(4, 4, 0, '2008-08-11 12:00:00', '_STD_REPORT_COURSE', 'a:5:{s:9:"id_report";s:1:"4";s:11:"report_name";s:54:"Statistics on all courses for all users (last 30 days)";s:11:"rows_filter";a:2:{s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}}s:23:"columns_filter_category";s:5:"users";s:14:"columns_filter";a:5:{s:9:"all_users";b:1;s:5:"users";a:0:{}s:9:"time_belt";a:3:{s:10:"time_range";s:2:"31";s:10:"start_date";s:0:"";s:8:"end_date";s:0:"";}s:21:"org_chart_subdivision";i:0;s:11:"showed_cols";a:21:{i:0;s:12:"_CODE_COURSE";i:1;s:12:"_NAME_COURSE";i:2;s:16:"_COURSE_CATEGORY";i:3;s:13:"_COURSESTATUS";i:4;s:9:"_LANGUAGE";i:5;s:10:"_DIFFICULT";i:6;s:11:"_DATE_BEGIN";i:7;s:9:"_DATE_END";i:8;s:11:"_TIME_BEGIN";i:9;s:9:"_TIME_END";i:10;s:19:"_MAX_NUM_SUBSCRIBED";i:11;s:19:"_MIN_NUM_SUBSCRIBED";i:12;s:6:"_PRICE";i:13;s:8:"_ADVANCE";i:14;s:12:"_COURSE_TYPE";i:15;s:22:"_AUTOREGISTRATION_CODE";i:16;s:6:"_INSCR";i:17;s:10:"_MUSTBEGIN";i:18;s:18:"_USER_STATUS_BEGIN";i:19;s:15:"_COMPLETECOURSE";i:20;s:14:"_TOTAL_SESSION";}}}', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report_schedule`
--

CREATE TABLE `learning_report_schedule` (
  `id_report_schedule` int(11) unsigned NOT NULL auto_increment,
  `id_report_filter` int(11) unsigned NOT NULL,
  `id_creator` int(11) unsigned NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `period` varchar(255) collate utf8_bin NOT NULL,
  `time` time NOT NULL,
  `creation_date` datetime NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id_report_schedule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `learning_report_schedule`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report_schedule_recipient`
--

CREATE TABLE `learning_report_schedule_recipient` (
  `id_report_schedule` int(11) unsigned NOT NULL,
  `id_user` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id_report_schedule`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `learning_report_schedule_recipient`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_category`
--

CREATE TABLE `learning_reservation_category` (
  `idCategory` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `maxSubscription` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_events`
--

CREATE TABLE `learning_reservation_events` (
  `idEvent` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `idLaboratory` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` longtext,
  `date` date NOT NULL default '0000-00-00',
  `maxUser` int(11) NOT NULL default '0',
  `deadLine` date NOT NULL default '0000-00-00',
  `fromTime` time NOT NULL default '00:00:00',
  `toTime` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`idEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_events`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_perm`
--

CREATE TABLE `learning_reservation_perm` (
  `event_id` int(11) NOT NULL,
  `user_idst` int(11) NOT NULL,
  `perm` varchar(255) NOT NULL,
  PRIMARY KEY  (`event_id`,`user_idst`,`perm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_perm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_subscribed`
--

CREATE TABLE `learning_reservation_subscribed` (
  `idstUser` int(11) NOT NULL default '0',
  `idEvent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idstUser`,`idEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_subscribed`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_items`
--

CREATE TABLE `learning_scorm_items` (
  `idscorm_item` int(11) NOT NULL auto_increment,
  `idscorm_organization` int(11) NOT NULL default '0',
  `idscorm_parentitem` int(11) default NULL,
  `adlcp_prerequisites` varchar(200) default NULL,
  `adlcp_maxtimeallowed` varchar(24) default NULL,
  `adlcp_timelimitaction` varchar(24) default NULL,
  `adlcp_datafromlms` varchar(255) default NULL,
  `adlcp_masteryscore` varchar(200) default NULL,
  `item_identifier` varchar(255) default NULL,
  `identifierref` varchar(255) default NULL,
  `idscorm_resource` int(11) default NULL,
  `isvisible` set('true','false') default 'true',
  `parameters` varchar(100) default NULL,
  `title` varchar(100) NOT NULL default '',
  `nChild` int(11) NOT NULL default '0',
  `nDescendant` int(11) NOT NULL default '0',
  `adlcp_completionthreshold` varchar(10) NOT NULL,
  PRIMARY KEY  (`idscorm_item`),
  UNIQUE KEY `idscorm_organization` (`idscorm_organization`,`item_identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_items_track`
--

CREATE TABLE `learning_scorm_items_track` (
  `idscorm_item_track` int(11) NOT NULL auto_increment,
  `idscorm_organization` int(11) NOT NULL default '0',
  `idscorm_item` int(11) default NULL,
  `idReference` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idscorm_tracking` int(11) default NULL,
  `status` varchar(16) NOT NULL default 'not attempted',
  `nChild` int(11) NOT NULL default '0',
  `nChildCompleted` int(11) NOT NULL default '0',
  `nDescendant` int(11) NOT NULL default '0',
  `nDescendantCompleted` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idscorm_item_track`),
  KEY `idscorm_organization` (`idscorm_organization`),
  KEY `idscorm_item` (`idscorm_item`),
  KEY `idUser` (`idUser`),
  KEY `idscorm_tracking` (`idscorm_tracking`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Join table 3 factor';

--
-- Dump dei dati per la tabella `learning_scorm_items_track`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_organizations`
--

CREATE TABLE `learning_scorm_organizations` (
  `idscorm_organization` int(11) NOT NULL auto_increment,
  `org_identifier` varchar(255) NOT NULL default '',
  `idscorm_package` int(11) NOT NULL default '0',
  `title` varchar(100) default NULL,
  `nChild` int(11) NOT NULL default '0',
  `nDescendant` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idscorm_organization`),
  UNIQUE KEY `idsco_package_unique` (`org_identifier`,`idscorm_package`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_organizations`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_package`
--

CREATE TABLE `learning_scorm_package` (
  `idscorm_package` int(11) NOT NULL auto_increment,
  `idpackage` varchar(255) NOT NULL default '',
  `idProg` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `defaultOrg` varchar(255) NOT NULL default '',
  `idUser` int(11) NOT NULL default '0',
  `scormVersion` varchar(10) NOT NULL default '1.2',
  PRIMARY KEY  (`idscorm_package`),
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_package`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_resources`
--

CREATE TABLE `learning_scorm_resources` (
  `idscorm_resource` int(11) NOT NULL auto_increment,
  `idsco` varchar(255) NOT NULL default '',
  `idscorm_package` int(11) NOT NULL default '0',
  `scormtype` set('sco','asset') default NULL,
  `href` varchar(255) default NULL,
  PRIMARY KEY  (`idscorm_resource`),
  UNIQUE KEY `idsco_package_unique` (`idsco`,`idscorm_package`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_resources`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_tracking`
--

CREATE TABLE `learning_scorm_tracking` (
  `idscorm_tracking` int(11) NOT NULL auto_increment,
  `idUser` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idscorm_item` int(11) NOT NULL default '0',
  `user_name` varchar(255) default NULL,
  `lesson_location` varchar(255) default NULL,
  `credit` varchar(24) default NULL,
  `lesson_status` varchar(24) default NULL,
  `entry` varchar(24) default NULL,
  `score_raw` float default NULL,
  `score_max` float default NULL,
  `score_min` float default NULL,
  `total_time` varchar(15) default '0000:00:00.00',
  `lesson_mode` varchar(24) default NULL,
  `exit` varchar(24) default NULL,
  `session_time` varchar(15) default NULL,
  `suspend_data` blob,
  `launch_data` blob,
  `comments` blob,
  `comments_from_lms` blob,
  `xmldata` longblob,
  PRIMARY KEY  (`idscorm_tracking`),
  UNIQUE KEY `Unique_tracking_usersco` (`idUser`,`idReference`,`idscorm_item`),
  KEY `idUser` (`idUser`),
  KEY `idscorm_resource` (`idReference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_tracking`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_tracking_history`
--

CREATE TABLE `learning_scorm_tracking_history` (
  `idscorm_tracking` int(11) NOT NULL,
  `date_action` datetime NOT NULL,
  `score_raw` float default NULL,
  `score_max` float default NULL,
  `session_time` varchar(15) default NULL,
  `lesson_status` varchar(24) NOT NULL,
  PRIMARY KEY  (`idscorm_tracking`,`date_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_tracking_history`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_setting`
--

CREATE TABLE `learning_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` text NOT NULL,
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_setting`
--

INSERT INTO `learning_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('activeCommerce', 'on', 'enum', 3, 0, 5, 1, 1, ''),
('activeNews', 'block', 'sel_news', 3, 1, 7, 1, 0, ''),
('admin_mail', 'marco@docebo.com', 'string', 255, 0, 2, 1, 0, ''),
('course_block', 'off', 'enum', 3, 1, 9, 1, 0, ''),
('course_list_plan', 'on', 'enum', 3, 1, 8, 1, 0, ''),
('course_quota', '50', 'string', 255, 0, 5, 1, 0, ''),
('defaultTemplate', 'standard', 'template', 255, 1, 3, 1, 0, ''),
('do_debug', 'on', 'enum', 3, 1, 0, 1, 1, ''),
('first_coursecatalogue_tab', 'category', 'first_coursecatalogue_tab', 255, 5, 2, 1, 0, ''),
('forum_as_table', 'off', 'enum', 3, 1, 6, 1, 0, ''),
('lms_version', '3.0', 'string', 255, 0, 0, 1, 1, ''),
('max_pdp_answer', '10', 'int', 6, 1, 10, 1, 0, ''),
('on_catalogue_empty', 'on', 'enum', 3, 1, 10, 1, 0, ''),
('pathchat', 'chat/', 'string', 255, 2, 1, 1, 0, ''),
('pathcourse', 'course/', 'string', 255, 2, 2, 1, 0, ''),
('pathforum', 'forum/', 'string', 255, 2, 3, 1, 0, ''),
('pathlesson', 'item/', 'string', 255, 2, 4, 1, 0, ''),
('pathmessage', 'message/', 'string', 255, 2, 5, 1, 0, ''),
('pathprj', 'project/', 'string', 255, 2, 0, 1, 1, ''),
('pathscorm', 'scorm/', 'string', 255, 2, 7, 1, 0, ''),
('pathsponsor', 'sponsor/', 'string', 255, 2, 8, 1, 0, ''),
('pathtest', 'test/', 'string', 255, 2, 9, 1, 0, ''),
('stop_concurrent_user', 'on', 'enum', 3, 3, 1, 1, 0, ''),
('tablist_coursecatalogue', 'a%3A6%3A%7Bs%3A4%3A%22time%22%3Bi%3A1%3Bs%3A8%3A%22category%22%3Bi%3A1%3Bs%3A3%3A%22all%22%3Bi%3A1%3Bs%3A9%3A%22mostscore%22%3Bi%3A1%3Bs%3A7%3A%22popular%22%3Bi%3A1%3Bs%3A6%3A%22recent%22%3Bi%3A1%3B%7D', 'tablist_coursecatalogue', 255, 5, 1, 1, 0, ''),
('tracking', 'on', 'enum', 3, 0, 4, 1, 0, ''),
('ttlSession', '4000', 'int', 5, 0, 3, 1, 0, ''),
('use_coursepath', '0', 'menuvoice', 1, 7, 1, 1, 0, '/coursepath/view'),
('use_course_catalogue', '0', 'menuvoice', 1, 7, 2, 1, 0, '/catalogue/view'),
('use_social_courselist', 'off', 'enum', 3, 0, 0, 1, 0, ''),
('visuItem', '20', 'int', 5, 1, 1, 1, 0, ''),
('visuNewsHomePage', '4', 'int', 5, 1, 0, 1, 1, ''),
('visu_course', '25', 'int', 5, 1, 2, 1, 0, ''),
('no_answer_in_poll', 'off', 'enum', 3, 1, 11, 1, 0, ''),
('no_answer_in_test', 'off', 'enum', 3, 1, 12, 1, 0, ''),
('catalogue_hide_ended', 'on', 'enum', 3, 0, 8, 1, 0, ''),
('tablist_mycourses', 'status,name,code', 'tablist_mycourses', 255, 0, 6, 1, 0, ''),
('url', 'http://localhost/docebo/doceboLms/', 'string', 255, 0, 1, 1, 0, ''),
('first_catalogue', 'off', 'enum', 3, 0, 9, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_statuschangelog`
--

CREATE TABLE `learning_statuschangelog` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `status_user` tinyint(1) NOT NULL default '0',
  `when_do` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idUser`,`idCourse`,`status_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_statuschangelog`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_sysforum`
--

CREATE TABLE `learning_sysforum` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_teacher_profile`
--

CREATE TABLE `learning_teacher_profile` (
  `id_user` int(11) NOT NULL default '0',
  `curriculum` text NOT NULL,
  `publications` text NOT NULL,
  PRIMARY KEY  (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_teacher_profile`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_test`
--

CREATE TABLE `learning_test` (
  `idTest` int(11) NOT NULL auto_increment,
  `author` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `point_type` tinyint(1) NOT NULL default '0',
  `point_required` double NOT NULL default '0',
  `display_type` tinyint(1) NOT NULL default '0',
  `order_type` tinyint(1) NOT NULL default '0',
  `shuffle_answer` tinyint(1) NOT NULL default '0',
  `question_random_number` int(4) NOT NULL default '0',
  `save_keep` tinyint(1) NOT NULL default '0',
  `mod_doanswer` tinyint(1) NOT NULL default '1',
  `can_travel` tinyint(1) NOT NULL default '1',
  `show_only_status` tinyint(1) NOT NULL default '0',
  `show_score` tinyint(1) NOT NULL default '1',
  `show_score_cat` tinyint(1) NOT NULL default '0',
  `show_doanswer` tinyint(1) NOT NULL default '0',
  `show_solution` tinyint(1) NOT NULL default '0',
  `time_dependent` tinyint(1) NOT NULL default '0',
  `time_assigned` int(6) NOT NULL default '0',
  `penality_test` tinyint(1) NOT NULL default '0',
  `penality_time_test` double NOT NULL default '0',
  `penality_quest` tinyint(1) NOT NULL default '0',
  `penality_time_quest` double NOT NULL default '0',
  `max_attempt` int(11) NOT NULL default '0',
  `hide_info` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idTest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_test`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquest`
--

CREATE TABLE `learning_testquest` (
  `idQuest` int(11) NOT NULL auto_increment,
  `idTest` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `type_quest` varchar(255) NOT NULL default '',
  `title_quest` text NOT NULL,
  `difficult` int(1) NOT NULL default '3',
  `time_assigned` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `page` int(11) NOT NULL default '0',
  `shuffle` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idQuest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquest`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquestanswer`
--

CREATE TABLE `learning_testquestanswer` (
  `idAnswer` int(11) NOT NULL auto_increment,
  `idQuest` int(11) NOT NULL default '0',
  `sequence` int(11) NOT NULL default '0',
  `is_correct` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  `comment` text NOT NULL,
  `score_correct` double NOT NULL default '0',
  `score_incorrect` double NOT NULL default '0',
  PRIMARY KEY  (`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquestanswer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquestanswer_associate`
--

CREATE TABLE `learning_testquestanswer_associate` (
  `idAnswer` int(11) NOT NULL auto_increment,
  `idQuest` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  PRIMARY KEY  (`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquestanswer_associate`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquest_extra`
--

CREATE TABLE `learning_testquest_extra` (
  `idQuest` int(11) NOT NULL default '0',
  `idAnswer` int(11) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`idQuest`,`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquest_extra`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack`
--

CREATE TABLE `learning_testtrack` (
  `idTrack` int(11) NOT NULL auto_increment,
  `idUser` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idTest` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_attempt_mod` datetime default NULL,
  `date_end_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_page_seen` int(11) NOT NULL default '0',
  `last_page_saved` int(11) NOT NULL default '0',
  `number_of_save` int(11) NOT NULL default '0',
  `number_of_attempt` int(11) NOT NULL default '0',
  `score` double default NULL,
  `bonus_score` double NOT NULL default '0',
  `score_status` enum('valid','not_checked','not_passed','passed','not_complete','doing') NOT NULL default 'not_complete',
  `comment` text NOT NULL,
  PRIMARY KEY  (`idTrack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_answer`
--

CREATE TABLE `learning_testtrack_answer` (
  `idTrack` int(11) NOT NULL default '0',
  `idQuest` int(11) NOT NULL default '0',
  `idAnswer` int(11) NOT NULL default '0',
  `score_assigned` double NOT NULL default '0',
  `more_info` longtext NOT NULL,
  `manual_assigned` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`,`idQuest`,`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_page`
--

CREATE TABLE `learning_testtrack_page` (
  `idTrack` int(11) NOT NULL default '0',
  `page` int(3) NOT NULL default '0',
  `display_from` datetime default NULL,
  `display_to` datetime default NULL,
  `accumulated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`,`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_page`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_quest`
--

CREATE TABLE `learning_testtrack_quest` (
  `idTrack` int(11) NOT NULL default '0',
  `idQuest` int(11) NOT NULL default '0',
  `page` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`,`idQuest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_quest`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_times`
--

CREATE TABLE `learning_testtrack_times` (
  `idTrack` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idTest` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `number_time` tinyint(4) NOT NULL default '0',
  `score` double NOT NULL default '0',
  `score_status` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`idTrack`,`number_time`,`idTest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_times`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_trackingeneral`
--

CREATE TABLE `learning_trackingeneral` (
  `idTrack` int(11) NOT NULL auto_increment,
  `idEnter` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `function` varchar(250) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `timeof` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`idTrack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_trackingeneral`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_tracksession`
--

CREATE TABLE `learning_tracksession` (
  `idEnter` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `enterTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `numOp` int(5) NOT NULL default '0',
  `lastFunction` varchar(50) NOT NULL default '',
  `lastOp` varchar(5) NOT NULL default '',
  `lastTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip_address` varchar(40) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idEnter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_tracksession`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_webpages`
--

CREATE TABLE `learning_webpages` (
  `idPages` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `language` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `in_home` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idPages`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_webpages`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_wiki_course`
--

CREATE TABLE `learning_wiki_course` (
  `course_id` int(11) NOT NULL default '0',
  `wiki_id` int(11) NOT NULL default '0',
  `is_owner` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`course_id`,`wiki_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_wiki_course`
--

-- --------------------------------------------------------