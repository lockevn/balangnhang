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

class Upgrade_CoreBase extends Upgrade {
	
	var $platfom = 'framework';
	
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
								
				$content = "CREATE TABLE `core_faq` (
				  `faq_id` int(11) NOT NULL auto_increment,
				  `category_id` int(11) NOT NULL default '0',
				  `question` varchar(255) NOT NULL default '',
				  `title` varchar(255) NOT NULL default '',
				  `keyword` text NOT NULL,
				  `answer` text NOT NULL,
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`faq_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_faq_cat` (
				  `category_id` int(11) NOT NULL auto_increment,
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `author` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`category_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_feed_out` (
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
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_revision` (
				  `type` enum('wiki','faq') NOT NULL default 'faq',
				  `parent_id` int(11) NOT NULL default '0',
				  `version` int(11) NOT NULL default '0',
				  `sub_key` varchar(80) NOT NULL default '0',
				  `author` int(11) NOT NULL default '0',
				  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `content` longtext NOT NULL,
				  PRIMARY KEY  (`type`,`parent_id`,`version`,`sub_key`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_sysforum` (
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
				
				$content = "ALTER TABLE `core_field` ADD `use_multilang` TINYINT( 1 ) NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `core_field_type` ADD `type_category` VARCHAR( 255 ) DEFAULT 'standard' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `core_field_type` VALUES ('codicefiscale', 'class.cf.php', 'Field_Cf', 'standard');
				INSERT INTO `core_field_type` VALUES ('gmail', 'class.gmail.php', 'CField_Gmail', 'contact');
				INSERT INTO `core_field_type` VALUES ('icq', 'class.icq.php', 'CField_Icq', 'contact');
				INSERT INTO `core_field_type` VALUES ('msn', 'class.msn.php', 'CField_Msn', 'contact');
				INSERT INTO `core_field_type` VALUES ('skype', 'class.skype.php', 'CField_Skype', 'contact');
				INSERT INTO `core_field_type` VALUES ('yahoo', 'class.yahoo.php', 'CField_Yahoo', 'contact')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
     			
     			
     			
				$content = "INSERT INTO `core_st` VALUES (NULL)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$new_id = $this->db_man->lastInsertId();
				
				$content = "INSERT INTO `core_group` VALUES ($new_id  , '/framework/company/fields', 'Used to associate a set of fields', 'true', 'free', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
     
     
     
				$content = "INSERT INTO `core_hteditor` VALUES ('widgeditor', '_WIDGEDITOR');
				INSERT INTO `core_hteditor` VALUES ('xinha', '_XINHA');
				INSERT INTO `core_hteditor` VALUES ('xstandard', '_XSTANDARD')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$re_user = mysql_query("SELECT idMenu FROM core_menu WHERE name = '_USER_MANAGMENT'");
				list($menu_user) = mysql_fetch_row($re_user);
				$re_config = mysql_query("SELECT idMenu FROM core_menu WHERE name = '_TRASV_MANAGMENT'");
				list($menu_config) = mysql_fetch_row($re_config);
				
				$content = "INSERT INTO `core_menu_under` VALUES (NULL, $menu_user, 'company', '_COMPANY', 'main', 'view', 'crm', 3, 'class.company.php', 'Module_Company');
				INSERT INTO `core_menu_under` VALUES (NULL, $menu_config, 'iotask', '_IOTASK', 'iotask', 'view', NULL, 4, 'class.iotask.php', 'Module_IOTask')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				        
				
				$content = "INSERT INTO `core_platform` VALUES ('crm', '', '', 'class.admin_menu_crm.php', 'Admin_Crm', 'Admin_Managment_Crm', 'class.conf_crm.php', 'Config_Crm', 'defaultTemplate', 'CrmAdminModule', 6, 'true', 'false', '', 'false');
				INSERT INTO `core_platform` VALUES ('ecom', '', '', 'class.admin_menu_ecom.php', 'Admin_Ecom', 'Admin_Managment_Ecom', 'class.conf_ecom.php', 'Config_Ecom', 'defaultTemplate', 'EcomAdminModule', 7, 'true', 'false', '', 'false')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
       			$content = "DELETE FROM core_platform WHERE platform = 'kms'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `core_setting` SET `param_name` = 'htmledit_image_admin' WHERE `param_name` = 'fck_image_admin'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "UPDATE `core_setting` SET `param_name` = 'htmledit_image_godadmin' WHERE `param_name` = 'fck_image_godadmin'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "UPDATE `core_setting` SET `param_name` = 'htmledit_image_user' WHERE `param_name` = 'fck_image_user'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `core_setting` VALUES ('company_idref_code', 'code', 'string', 255, 'main', 0, 0, 1, 1, '');
				INSERT INTO `core_setting` VALUES ('field_tree', '0', 'field_tree', 255, 'log_option', 0, 11, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('google_stat_code', '', 'textarea', 65535, 'main', 8, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('google_stat_in_cms', '1', 'check', 1, 'main', 8, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('google_stat_in_lms', '', 'check', 1, 'main', 8, 3, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('lastfirst_mandatory', 'on', 'enum', 3, 'log_option', 0, 2, 2, 0, '');
				INSERT INTO `core_setting` VALUES ('pass_change_first_login', 'on', 'enum', 3, 'log_option', 0, 13, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('register_in_company', 'on', 'enum', 3, 'log_option', 0, 12, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('register_tree', 'off', 'register_tree', 255, 'log_option', 0, 10, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sserver_host', '', 'string', 255, 'main', 7, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sserver_user', '', 'string', 255, 'main', 7, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('user_quota', '50', 'string', 255, 'main', 0, 15, 1, 0, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				$content = "ALTER TABLE `core_platform` ADD `hidden_in_config` ENUM( 'true', 'false' ) DEFAULT 'false' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `core_platform` SET `mandatory` = 'false' WHERE `platform` = 'scs'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_deleted_user` (
					`id_deletion` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`idst` INT( 11 ) NOT NULL ,
					`userid` VARCHAR( 255 ) NOT NULL ,
					`firstname` VARCHAR( 255 ) NOT NULL ,
					`lastname` VARCHAR( 255 ) NOT NULL ,
					`pass` VARCHAR( 50 ) NOT NULL ,
					`email` VARCHAR( 255 ) NOT NULL ,
					`photo` VARCHAR( 255 ) NOT NULL ,
					`avatar` VARCHAR( 255 ) NOT NULL ,
					`signature` TEXT NOT NULL ,
					`level` INT( 11 ) NOT NULL ,
					`lastenter` DATETIME NOT NULL ,
					`valid` TINYINT( 1 ) NOT NULL ,
					`pwd_expire_at` DATETIME NOT NULL ,
					`register_date` DATETIME NOT NULL ,
					`deletion_date` DATETIME NOT NULL ,
					`deleted_by` INT( 11 ) NOT NULL ,
					PRIMARY KEY ( `id_deletion` )
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `pack` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES (
				'register_deleted_user', 'off', 'enum', '3', 'log_option', '0', '14', '1', '0', ''
				);
				";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `core_lang_translation` ADD `save_date` DATETIME NOT NULL ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `core_feed_cache` SET `content` = '',`last_update` = '' WHERE `core_feed_cache`.`feed_id` = 1";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `core_feed_cache` SET `content` = '',`last_update` = '' WHERE `core_feed_cache`.`feed_id` = 2";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `core_feed_cache` SET `title` = 'Docebo Corporate Blog',
				`url` = 'http://feeds.feedburner.com/DoceboCorpBlog',
				`last_update` = '' WHERE `core_feed_cache`.`feed_id` =3";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
					
				
				$content = "INSERT INTO `core_event_class` VALUES (NULL, 'SettingUpdate', 'framework', '_CONFIG_SETTINGS_HAS_BEEN_UPDATED')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_setting = $this->db_man->lastInsertId();

				$content = "INSERT INTO `core_event_consumer` VALUES (NULL, 'DoceboOrgchartNotifier', '/lib/lib.orgchartnotifier.php')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_orgchart = $this->db_man->lastInsertId();
				
				$content = "INSERT INTO `core_event_consumer` VALUES (NULL, 'DoceboCompanyNotifier', '/lib/lib.companynotifier.php')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_company = $this->db_man->lastInsertId();
				
				$content = "INSERT INTO `core_event_consumer` VALUES (NULL, 'DoceboSettingNotifier', '/lib/lib.settingnotifier.php')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_sett_notifier = $this->db_man->lastInsertId();
				
				$content = "INSERT INTO `core_event_consumer_class` VALUES ($id_orgchart, $id_setting)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
	
				$content = "INSERT INTO `core_event_consumer_class` VALUES ($id_company, $id_setting)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_event_consumer_class` VALUES ($id_sett_notifier, $id_setting)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
	
					
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `core_deleted_user` CHANGE `idst` `idst` INT( 11 ) NOT NULL DEFAULT '0',
							CHANGE `level` `level` INT( 11 ) NOT NULL DEFAULT '0',
							CHANGE `valid` `valid` TINYINT( 1 ) NOT NULL DEFAULT '0',
							CHANGE `pwd_expire_at` `pwd_expire_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
							CHANGE `register_date` `register_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
							CHANGE `deletion_date` `deletion_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
							CHANGE `deleted_by` `deleted_by` INT( 11 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES 
							('rest_auth_code', '', 'string', 255, 'main', 10, 1, 1, 0, ''),
							('rest_auth_lifetime', '60', 'int', 3, 'main', 10, 2, 1, 0, ''),
							('rest_auth_method', '1', 'rest_auth_sel_method', 2, 'main', 10, 0, 1, 0, ''),
							('rest_auth_update', 'off', 'enum', 3, 'main', 10, 3, 1, 0, '');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_st` ( `idst` )
							VALUES (NULL);";
				
				$new_id = $this->db_man->lastInsertId();
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_group` ( `idst` , `groupid` , `description` , `hidden` , `type` , `show_on_platform` )
							VALUES (
							'".$new_id."', '/framework/level/publicadmin', 'Group of Public Admin', 'true', 'free', 'framework, '
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>