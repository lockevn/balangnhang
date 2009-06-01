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
-- Struttura della tabella `crm_contact_history`
--

CREATE TABLE `crm_contact_history` (
  `contact_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `type` enum('form','phone','email','meeting') NOT NULL default 'form',
  `reason_id` int(11) NOT NULL default '0',
  `meeting_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` text NOT NULL,
  PRIMARY KEY  (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_contact_history`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_contact_reason`
--

CREATE TABLE `crm_contact_reason` (
  `reason_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_contact_reason`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_contact_user`
--

CREATE TABLE `crm_contact_user` (
  `contact_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`contact_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_contact_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_file`
--

CREATE TABLE `crm_file` (
  `file_id` int(11) NOT NULL auto_increment,
  `type` varchar(20) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_menu`
--

CREATE TABLE `crm_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_menu`
--

INSERT INTO `crm_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_GENERAL_CRM', 'crm.gif', 1, 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_menu_under`
--

CREATE TABLE `crm_menu_under` (
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
-- Dump dei dati per la tabella `crm_menu_under`
--

INSERT INTO `crm_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'companytype', '_COMPANYTYPE', 'main', 'view', NULL, 1, 'class.companytype.php', 'Module_Companytype'),
(2, 1, 'companystatus', '_COMPANYSTATUS', 'main', 'view', NULL, 2, 'class.companystatus.php', 'Module_Companystatus'),
(3, 1, 'ticketstatus', '_TICKETSTATUS', 'main', 'view', NULL, 3, 'class.ticketstatus.php', 'Module_Ticketstatus'),
(4, 1, 'contactreason', '_CONTACT_REASON', 'main', 'view', NULL, 4, 'class.contactreason.php', 'Module_Contactreason');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_note`
--

CREATE TABLE `crm_note` (
  `note_id` int(11) NOT NULL auto_increment,
  `type` varchar(20) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note_author` int(11) NOT NULL default '0',
  `note_txt` text NOT NULL,
  PRIMARY KEY  (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=1;

--
-- Dump dei dati per la tabella `crm_note`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_project`
--

CREATE TABLE `crm_project` (
  `prj_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `cost` varchar(30) NOT NULL default '',
  `gain` varchar(30) NOT NULL default '',
  `priority` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `progress` tinyint(3) NOT NULL default '0',
  `sign_date` date NOT NULL default '0000-00-00',
  `expire` date NOT NULL default '0000-00-00',
  `deadline` date NOT NULL default '0000-00-00',
  `ticket` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_project`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_publicmenu_under`
--

CREATE TABLE `crm_publicmenu_under` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_publicmenu_under`
--

INSERT INTO `crm_publicmenu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'company', '_COMPANY', 'main', 'view', 1, 'class.company.php', 'Module_Company'),
(2, 1, 'abook', '_ADDRESS_BOOK', 'main', 'view', 2, 'class.abook.php', 'Module_Abook'),
(3, 1, 'task', '_TASKS', 'main', 'view', 3, 'class.task.php', 'Module_Task'),
(4, 1, 'todo', '_TODO', 'main', 'view', 4, 'class.todo.php', 'Module_Todo'),
(5, 1, 'ticket', '_TICKETS', 'main', 'view', 5, 'class.ticket.php', 'Module_Ticket'),
(6, 1, 'contacthistory', '_CONTACT_HISTORY', 'main', 'view', 6, 'class.contacthistory.php', 'Module_Contacthistory'),
(7, 1, 'storedform', '_FORM_CONTACT', 'main', 'view', 7, 'class.storedform.php', 'Module_Storedform');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_public_menu`
--

CREATE TABLE `crm_public_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_public_menu`
--

INSERT INTO `crm_public_menu` (`idMenu`, `name`, `image`, `sequence`) VALUES
(1, '_GENERAL_CRM', 'crm.gif', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_setting`
--

CREATE TABLE `crm_setting` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_setting`
--

INSERT INTO `crm_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('crm_version', '3.0', 'string', 255, 'main', 0, 3, 1, 1, ''),
('defaultTemplate', 'standard', 'template', 255, 'main', 0, 3, 1, 0, ''),
('default_language', 'italian', 'language', 255, 'main', 0, 2, 1, 0, ''),
('ttlSession', '2000', 'int', 5, 'main', 0, 4, 1, 0, ''),
('url', 'http://localhost/docebo_35/doceboCrm/', 'string', 255, 'main', 0, 1, 1, 1, ''),
('use_simplified', 'on', 'enum', 3, 'main', 0, 1, 1, 0, ''),
('visuItem', '20', 'int', 3, 'main', 0, 11, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_sysforum`
--

CREATE TABLE `crm_sysforum` (
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
-- Dump dei dati per la tabella `crm_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_task`
--

CREATE TABLE `crm_task` (
  `task_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `prj_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `expire` date NOT NULL default '0000-00-00',
  `status` tinyint(1) NOT NULL default '0',
  `priority` tinyint(1) NOT NULL default '0',
  `progress` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_task`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_ticket`
--

CREATE TABLE `crm_ticket` (
  `ticket_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `prj_id` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `priority` tinyint(1) NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_ticket`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_ticket_msg`
--

CREATE TABLE `crm_ticket_msg` (
  `message_id` int(11) NOT NULL auto_increment,
  `ticket_id` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `text_msg` text NOT NULL,
  `from_staff` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_ticket_msg`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_ticket_status`
--

CREATE TABLE `crm_ticket_status` (
  `status_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_ticket_status`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_todo`
--

CREATE TABLE `crm_todo` (
  `todo_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  `priority` tinyint(1) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `complete` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`todo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_todo`
--


-- --------------------------------------------------------