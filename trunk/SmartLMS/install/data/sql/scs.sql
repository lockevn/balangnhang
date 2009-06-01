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
-- Struttura della tabella `conference_booking`
--

CREATE TABLE `conference_booking` (
  `booking_id` int(11) NOT NULL auto_increment,
  `room_id` int(11) NOT NULL default '0',
  `platform` varchar(255) NOT NULL default '',
  `module` varchar(100) NOT NULL default '',
  `user_idst` int(11) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`booking_id`)
) TYPE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_booking`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_chatperm`
--

CREATE TABLE `conference_chatperm` (
  `room_id` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `user_idst` int(11) NOT NULL default '0',
  `perm` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`room_id`,`module`,`user_idst`,`perm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_chatperm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_chat_msg`
--

CREATE TABLE `conference_chat_msg` (
  `msg_id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `id_room` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `send_to` int(11) default NULL,
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text NOT NULL,
  PRIMARY KEY  (`msg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_chat_msg`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_dimdim`
--

CREATE TABLE `conference_dimdim` (
  `id` bigint(20) NOT NULL auto_increment,
  `idConference` bigint(20) NOT NULL,
  `confkey` varchar(255) default NULL,
  `emailuser` varchar(255) default NULL,
  `displayname` varchar(255) default NULL,
  `timezone` varchar(255) default NULL,
  `audiovideosettings` int(11) default NULL,
  `maxmikes` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idConference` (`idConference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_dimdim`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_inte_room`
--

CREATE TABLE `conference_inte_room` (
  `id_room` int(11) NOT NULL auto_increment,
  `ext_key` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `room_parent` int(11) NOT NULL default '0',
  `room_path` text NOT NULL,
  `type` enum('public','private') NOT NULL default 'public',
  `max_user` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `blocked` tinyint(1) NOT NULL default '0',
  `room_perm` int(8) NOT NULL default '0',
  `zone` varchar(20) NOT NULL default '',
  `bookable` tinyint(1) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `logo` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_inte_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_inte_token`
--

CREATE TABLE `conference_inte_token` (
  `id_user` int(11) NOT NULL default '0',
  `token` varchar(64) NOT NULL default '',
  `role` varchar(20) NOT NULL default 'guest',
  PRIMARY KEY  (`id_user`,`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_inte_token`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_inte_user`
--

CREATE TABLE `conference_inte_user` (
  `id_user` int(11) NOT NULL auto_increment,
  `userid` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `role` tinyint(1) NOT NULL default '0',
  `in_room` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`in_room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_inte_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_menu`
--

CREATE TABLE `conference_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_menu`
--

INSERT INTO `conference_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_MAIN_CONFERENCE_MANAGMENT', '', 1, 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `conference_menu_under`
--

CREATE TABLE `conference_menu_under` (
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
-- Dump dei dati per la tabella `conference_menu_under`
--

INSERT INTO `conference_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(2, 1, 'room', '_ROOM', 'room', 'view', NULL, 2, 'class.room.php', 'Module_Room');

-- --------------------------------------------------------

--
-- Struttura della tabella `conference_room`
--

CREATE TABLE `conference_room` (
	`id`					BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	`idCal` 				BIGINT NOT NULL,
	`idCourse`				BIGINT NOT NULL,
	`idSt`					BIGINT NOT NULL,
	`name`					VARCHAR(255),
	`room_type`				VARCHAR(255),
	`starttime`				BIGINT,
	`endtime`				BIGINT,
	`meetinghours`			INT,
	`maxparticipants`		INT,
	INDEX (`idCourse`)
) TYPE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_admin`
--

CREATE TABLE `conference_rules_admin` (
  `server_status` enum('yes','no') NOT NULL default 'yes',
  `enable_recording_function` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_advice_insert` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_chat_recording` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_private_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_public_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_audio` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_webcam` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_stream_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_strem_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_remote_desktop` enum('admin','alluser','noone') NOT NULL default 'noone',
  PRIMARY KEY  (`server_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_admin`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_room`
--

CREATE TABLE `conference_rules_room` (
  `id_room` int(11) NOT NULL auto_increment,
  `enable_recording_function` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_advice_insert` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_chat_recording` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_private_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_public_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_audio` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_webcam` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_stream_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_strem_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_remote_desktop` enum('admin','alluser','noone') NOT NULL default 'noone',
  `room_name` varchar(255) NOT NULL default '',
  `room_type` enum('course','private','public') NOT NULL default 'course',
  `id_source` int(11) NOT NULL default '0',
  `room_parent` int(11) NOT NULL default '0',
  `advice_one` text,
  `advice_two` text,
  `advice_three` text,
  `room_logo` varchar(255) default NULL,
  `room_sponsor` varchar(255) default NULL,
  PRIMARY KEY  (`id_room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_root`
--

CREATE TABLE `conference_rules_root` (
  `system_type` enum('p2p','server') NOT NULL default 'p2p',
  `server_ip` varchar(255) default NULL,
  `server_port` int(5) unsigned default NULL,
  `server_path` varchar(255) default NULL,
  `max_user_at_time` int(11) unsigned NOT NULL default '0',
  `max_room_at_time` int(11) unsigned NOT NULL default '0',
  `max_subroom_for_room` int(11) unsigned NOT NULL default '0',
  `enable_drawboard` enum('yes','no') NOT NULL default 'no',
  `enable_livestream` enum('yes','no') NOT NULL default 'no',
  `enable_remote_desktop` enum('yes','no') NOT NULL default 'no',
  `enable_webcam` enum('yes','no') NOT NULL default 'no',
  `enable_audio` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`system_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_root`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_user`
--

CREATE TABLE `conference_rules_user` (
  `id_user` int(11) NOT NULL auto_increment,
  `last_hit` int(11) NOT NULL default '0',
  `id_room` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `user_ip` varchar(15) NOT NULL default '',
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `level` int(11) NOT NULL default '0',
  `auto_reload` tinyint(1) NOT NULL default '0',
  `banned_until` datetime default NULL,
  `chat_record` enum('yes','no') NOT NULL default 'no',
  `advice_insert` enum('yes','no') NOT NULL default 'no',
  `write_in_chat` enum('yes','no') NOT NULL default 'no',
  `request_to_chat` enum('yes','no') NOT NULL default 'no',
  `create_public_subroom` enum('yes','no') NOT NULL default 'no',
  `enable_webcam` enum('yes','no') NOT NULL default 'no',
  `enable_audio` enum('yes','no') NOT NULL default 'no',
  `enable_drawboard_watch` enum('yes','no') NOT NULL default 'no',
  `enable_drawboard_draw` enum('yes','no') NOT NULL default 'no',
  `enable_livestream_watch` enum('yes','no') NOT NULL default 'no',
  `enable_livestream_publish` enum('yes','no') NOT NULL default 'no',
  `accept_private_message` enum('yes','no') NOT NULL default 'no',
  `picture` varchar(255) default NULL,
  PRIMARY KEY  (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_setting`
--

CREATE TABLE `conference_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
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
-- Dump dei dati per la tabella `conference_setting`
--

INSERT INTO `conference_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('code_teleskill', '', 'string', 255, 2, 3, 1, 0, ''),
('conference_creation_limit_per_user', '1', 'string', 255, 0, 0, 1, 0, ''),
('defaultTemplate', 'standard', 'string', 255, 0, 1, 1, 1, ''),
('dimdim_max_mikes', '2', 'string', 255, 6, 7, 1, 0, ''),
('dimdim_max_participant', '15', 'string', 255, 6, 6, 1, 0, ''),
('dimdim_max_room', '1', 'string', 255, 6, 5, 1, 0, ''),
('dimdim_port', '80', 'string', 255, 6, 2, 1, 0, ''),
('dimdim_server', 'www1.dimdim.com', 'string', 255, 6, 1, 1, 0, ''),
('intelligere_application_code', '', 'string', 255, 4, 5, 1, 0, ''),
('intelligere_max_participant', '', 'string', 255, 4, 7, 1, 0, ''),
('intelligere_max_room', '', 'string', 255, 4, 6, 1, 0, ''),
('intelligere_remote_desktop_server_type', '', 'string', 255, 4, 3, 1, 0, ''),
('intelligere_streaming_server_type', '', 'string', 255, 4, 2, 1, 0, ''),
('intelligere_user', '', 'string', 255, 4, 4, 1, 0, ''),
('org_name_teleskill', 'Docebo', 'string', 255, 2, 4, 1, 0, ''),
('teleskill_max_participant', '', 'string', 255, 2, 6, 1, 0, ''),
('teleskill_max_room', '', 'string', 255, 2, 5, 1, 0, ''),
('url_checkin_teleskill', 'http://ews.teleskill.it/ews/server.asp', 'string', 255, 2, 1, 1, 0, ''),
('url_videoconference_intelligere', '', 'string', 255, 4, 1, 1, 0, ''),
('url_videoconference_teleskill', '', 'string', 255, 2, 2, 1, 0, ''),
('dimdim_user', '', 'string', 255, 6, 2, 1, 0, ''),
('dimdim_password', '', 'string', 255, 6, 2, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `conference_teleskill`
--

CREATE TABLE conference_teleskill (
	`id`				BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	`idConference`		BIGINT NOT NULL,
	`roomid`			BIGINT NOT NULL,
	INDEX (idConference)
) TYPE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_teleskill`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_teleskill_room`
--

CREATE TABLE `conference_teleskill_room` (
  `roomid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `zone` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `bookable` tinyint(1) NOT NULL default '0',
  `capacity` int(11) default NULL,
  PRIMARY KEY  (`roomid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_teleskill_room`
--


-- ---------------------------------------------------------- --------------------------------------------------------