<?php

class Upgrade_menu extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'menu';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "2.0.4" : {
				
				$query = "CREATE TABLE `core_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				
				$query = "
				INSERT INTO `core_menu` VALUES (1, '_USER_MANAGMENT', 'user_managment.gif', 1);
				INSERT INTO `core_menu` VALUES (2, '_TRASV_MANAGMENT', 'trans_managment.gif', 2);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `core_menu_under` (
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
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `core_menu_under` VALUES (1, 1, 'field_manager', '_FIELD_MANAGER', 'field_list', 'view', 3, 'class.field_manager.php', 'Module_Field_Manager');
				INSERT INTO `core_menu_under` VALUES (2, 2, 'lang', '_LANG', 'lang', 'view', 2, 'class.lang.php', 'Module_Lang');
				INSERT INTO `core_menu_under` VALUES (3, 1, 'directory', '_LISTUSER', 'org_chart', 'view_org_chart', 1, 'class.directory.php', 'Module_Directory');
				INSERT INTO `core_menu_under` VALUES (4, 2, 'lang', '_LANG_IMPORT_EXPORT', 'importexport', 'view', 3, 'class.lang.php', 'Module_Lang');
				INSERT INTO `core_menu_under` VALUES (8, 2, 'configuration', '_CONFIGURATION', 'config', 'view', 0, 'class.configuration.php', 'Module_Configuration');
				INSERT INTO `core_menu_under` VALUES (7, 1, 'directory', '_LISTGROUP', 'listgroup', 'view_group', 2, 'class.directory.php', 'Module_Directory');
				INSERT INTO `core_menu_under` VALUES (10, 1, 'admin_manager', '_ADMIN_MANAGER', 'view', 'view', 5, 'class.admin_manager.php', 'Module_Admin_Manager');
				INSERT INTO `core_menu_under` VALUES (11, 2, 'regional_settings', '_REGSET', 'regset', 'view', 5, 'class.regional_settings.php', 'Module_Regional_settings');
				INSERT INTO `core_menu_under` VALUES (12, 2, 'publication_flow', '_PUBFLOW', 'pubflow', 'view', 6, '', '');
				INSERT INTO `core_menu_under` VALUES (13, 2, 'event_manager', '_EVENTMANAGER', 'display', 'view_event_manager', 7, 'class.event_manager.php', 'Module_Event_Manager');
				INSERT INTO `core_menu_under` VALUES (14, 1, 'newsletter', '_NEWSLETTER', 'newsletter', 'view', 6, 'class.newsletter.php', 'Module_Newsletter');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "DROP TABLE `core_menu`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DROP TABLE `core_menu_under`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `core_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `core_menu` VALUES (1, '_USER_MANAGMENT', 'user_management', 2, 'false');
				INSERT INTO `core_menu` VALUES (2, '_TRASV_MANAGMENT', 'trasv_management', 3, 'false');
				INSERT INTO `core_menu` VALUES (3, '_PERMISSION', 'permission', 4, 'false');
				INSERT INTO `core_menu` VALUES (4, '_LANGUAGE', 'main_language', 5, 'false');
				INSERT INTO `core_menu` VALUES (5, '', '', 6, 'true');
				INSERT INTO `core_menu` VALUES (6, '', '', 1, 'true');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `core_menu_under` (
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
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `core_menu_under` VALUES (1, 1, 'field_manager', '_FIELD_MANAGER', 'field_list', 'view', NULL, 3, 'class.field_manager.php', 'Module_Field_Manager');
				INSERT INTO `core_menu_under` VALUES (2, 4, 'lang', '_LANG', 'lang', 'view', NULL, 2, 'class.lang.php', 'Module_Lang');
				INSERT INTO `core_menu_under` VALUES (3, 1, 'directory', '_LISTUSER', 'org_chart', 'view_org_chart', NULL, 1, 'class.directory.php', 'Module_Directory');
				INSERT INTO `core_menu_under` VALUES (4, 4, 'lang', '_LANG_IMPORT_EXPORT', 'importexport', 'view', NULL, 3, 'class.lang.php', 'Module_Lang');
				INSERT INTO `core_menu_under` VALUES (8, 2, 'configuration', '_CONFIGURATION', 'config', 'view', NULL, 1, 'class.configuration.php', 'Module_Configuration');
				INSERT INTO `core_menu_under` VALUES (7, 1, 'directory', '_LISTGROUP', 'listgroup', 'view_group', NULL, 2, 'class.directory.php', 'Module_Directory');
				INSERT INTO `core_menu_under` VALUES (10, 3, 'admin_manager', '_ADMIN_MANAGER', 'view', 'view', NULL, 5, 'class.admin_manager.php', 'Module_Admin_Manager');
				INSERT INTO `core_menu_under` VALUES (11, 4, 'regional_settings', '_REGSET', 'regset', 'view', NULL, 5, 'class.regional_settings.php', 'Module_Regional_settings');
				INSERT INTO `core_menu_under` VALUES (12, 2, 'publication_flow', '_PUBFLOW', 'pubflow', 'view', NULL, 3, '', '');
				INSERT INTO `core_menu_under` VALUES (13, 2, 'event_manager', '_EVENTMANAGER', 'display', 'view_event_manager', NULL, 2, 'class.event_manager.php', 'Module_Event_Manager');
				INSERT INTO `core_menu_under` VALUES (14, 5, 'newsletter', '_NEWSLETTER', 'newsletter', 'view', NULL, 1, 'class.newsletter.php', 'Module_Newsletter');
				INSERT INTO `core_menu_under` VALUES (20, 6, 'dashboard', '_DASHBOARD', 'dashboard', 'view', NULL, 1, 'class.dashboard.php', 'Module_Dashboard');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "INSERT INTO `core_menu_under` ( `idUnder` , `idMenu` , `module_name` , `default_name` , `default_op` , `associated_token` , `of_platform` , `sequence` , `class_file` , `class_name` )
							VALUES (
							'', '4', 'public_admin_manager', '_PUBLIC_ADMIN_MANAGER', 'view', 'view', NULL , '2', 'class.public_admin_manager.php', 'Module_Public_Admin_Manager'
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