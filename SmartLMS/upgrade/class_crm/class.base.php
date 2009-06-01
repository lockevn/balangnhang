<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

class Upgrade_CrmBase extends Upgrade {
	
	var $platfom = 'crm';
	
	var $mname = 'base';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.0.6" : {
				$i = 0;
					
				$content = "
				CREATE TABLE `crm_contact_history` (
				  `contact_id` int(11) NOT NULL auto_increment,
				  `company_id` int(11) NOT NULL default '0',
				  `title` varchar(255) NOT NULL default '',
				  `type` enum('form','phone','email','meeting') NOT NULL default 'form',
				  `reason_id` int(11) NOT NULL default '0',
				  `meeting_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `description` text NOT NULL,
				  PRIMARY KEY  (`contact_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `crm_contact_reason` (
				  `reason_id` int(11) NOT NULL auto_increment,
				  `label` varchar(255) NOT NULL default '',
				  `is_used` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`reason_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_contact_user` (
				  `contact_id` int(11) NOT NULL default '0',
				  `user_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`contact_id`,`user_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_file` (
				  `file_id` int(11) NOT NULL auto_increment,
				  `type` varchar(20) NOT NULL default '',
				  `parent_id` int(11) NOT NULL default '0',
				  `fname` varchar(255) NOT NULL default '',
				  `real_fname` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  PRIMARY KEY  (`file_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `crm_menu` VALUES (1, '_GENERAL_CRM', 'crm.gif', 1, 'false');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_menu_under` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `crm_menu_under` VALUES (1, 1, 'companytype', '_COMPANYTYPE', 'main', 'view', NULL, 1, 'class.companytype.php', 'Module_Companytype');
				INSERT INTO `crm_menu_under` VALUES (2, 1, 'companystatus', '_COMPANYSTATUS', 'main', 'view', NULL, 2, 'class.companystatus.php', 'Module_Companystatus');
				INSERT INTO `crm_menu_under` VALUES (3, 1, 'ticketstatus', '_TICKETSTATUS', 'main', 'view', NULL, 3, 'class.ticketstatus.php', 'Module_Ticketstatus');
				INSERT INTO `crm_menu_under` VALUES (4, 1, 'contactreason', '_CONTACT_REASON', 'main', 'view', NULL, 4, 'class.contactreason.php', 'Module_Contactreason');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_note` (
				  `note_id` int(11) NOT NULL auto_increment,
				  `type` varchar(20) NOT NULL default '',
				  `parent_id` int(11) NOT NULL default '0',
				  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `note_author` int(11) NOT NULL default '0',
				  `note_txt` text NOT NULL,
				  PRIMARY KEY  (`note_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_project` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_public_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_publicmenu_under` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `crm_publicmenu_under` VALUES (1, 1, 'company', '_COMPANY', 'main', 'view', 1, 'class.company.php', 'Module_Company');
				INSERT INTO `crm_publicmenu_under` VALUES (2, 1, 'abook', '_ADDRESS_BOOK', 'main', 'view', 2, 'class.abook.php', 'Module_Abook');
				INSERT INTO `crm_publicmenu_under` VALUES (3, 1, 'task', '_TASKS', 'main', 'view', 3, 'class.task.php', 'Module_Task');
				INSERT INTO `crm_publicmenu_under` VALUES (4, 1, 'todo', '_TODO', 'main', 'view', 4, 'class.todo.php', 'Module_Todo');
				INSERT INTO `crm_publicmenu_under` VALUES (5, 1, 'ticket', '_TICKETS', 'main', 'view', 5, 'class.ticket.php', 'Module_Ticket');
				INSERT INTO `crm_publicmenu_under` VALUES (6, 1, 'contacthistory', '_CONTACT_HISTORY', 'main', 'view', 6, 'class.contacthistory.php', 'Module_Contacthistory');
				INSERT INTO `crm_publicmenu_under` VALUES (7, 1, 'storedform', '_FORM_CONTACT', 'main', 'view', 7, 'class.storedform.php', 'Module_Storedform');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_sysforum` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_task` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_ticket` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_ticket_msg` (
				  `message_id` int(11) NOT NULL auto_increment,
				  `ticket_id` int(11) NOT NULL default '0',
				  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `author` int(11) NOT NULL default '0',
				  `text_msg` text NOT NULL,
				  `from_staff` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`message_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_ticket_status` (
				  `status_id` int(11) NOT NULL auto_increment,
				  `label` varchar(255) NOT NULL default '',
				  `is_used` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`status_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `crm_todo` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
								
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>