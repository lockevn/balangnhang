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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dump dei dati per la tabella `conference_chat_msg`
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
  PRIMARY KEY  (`idMenu`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dump dei dati per la tabella `conference_menu`
-- 

INSERT INTO `conference_menu` VALUES (1, '_MAIN_CONFERENCE_MANAGMENT', 'conferece_managment.gif', 1);

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
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dump dei dati per la tabella `conference_menu_under`
-- 

INSERT INTO `conference_menu_under` VALUES (1, 1, 'admin_configuration', '_ADMIN_CONFIGURATION', 'conf', 'view', 1, 'class.admin_configuration.php', 'Module_AdminConfiguration');
INSERT INTO `conference_menu_under` VALUES (2, 1, 'room', '_ROOM', 'room', 'view', 2, 'class.room.php', 'Module_Room');

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
) TYPE=MyISAM;

-- 
-- Dump dei dati per la tabella `conference_rules_admin`
-- 

INSERT INTO `conference_rules_admin` VALUES ('yes', 'admin', 'admin', 'admin', 'admin', 'admin', 'admin', 'alluser', 'admin', 'alluser', 'alluser', 'alluser', 'admin', 'admin');

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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

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
) TYPE=MyISAM;

-- 
-- Dump dei dati per la tabella `conference_rules_root`
-- 

INSERT INTO `conference_rules_root` VALUES ('server', '127.0.0.1', 123, '/', 60, 0, 0, 'yes', 'no', 'no', 'yes', 'yes');

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
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1 ;

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
) TYPE=MyISAM;

-- 
-- Dump dei dati per la tabella `conference_setting`
-- 

INSERT INTO `conference_setting` VALUES ('defaultTemplate', 'standard', 'string', 255, 0, 1, 1, 1, '');
INSERT INTO `conference_setting` VALUES ('url_checkin_teleskill', 'http://ews.teleskill.it/ews/checkin.asp', 'string', 255, 2, 1, 1, 0, '');
INSERT INTO `conference_setting` VALUES ('url_videoconference_teleskill', '', 'string', 255, 2, 2, 1, 0, '');
INSERT INTO `conference_setting` VALUES ('org_name_teleskill', '', 'string', 255, 2, 4, 1, 0, '');
INSERT INTO `conference_setting` VALUES ('code_teleskill', '', 'string', 255, 2, 3, 1, 0, '');
