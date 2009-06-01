<?php

class Upgrade_ScsMenu extends Upgrade {
	
	var $platfom = 'scs';
	
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
				
				$query = "CREATE TABLE `conference_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "INSERT INTO `conference_menu` VALUES (1, '_MAIN_CONFERENCE_MANAGMENT', 'conferece_managment.gif', 1);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `conference_menu_under` (
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
				
				$query = "INSERT INTO `conference_menu_under` VALUES (1, 1, 'admin_configuration', '_ADMIN_CONFIGURATION', 'conf', 'view', 1, 'class.admin_configuration.php', 'Module_AdminConfiguration');
				INSERT INTO `conference_menu_under` VALUES (2, 1, 'room', '_ROOM', 'room', 'view', 2, 'class.room.php', 'Module_Room');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "DROP TABLE `conference_menu`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DROP TABLE `conference_menu_under`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `conference_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `conference_menu` VALUES (1, '_MAIN_CONFERENCE_MANAGMENT', '', 1, 'true');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `conference_menu_under` (
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
				INSERT INTO `conference_menu_under` VALUES (2, 1, 'room', '_ROOM', 'room', 'view', NULL, 2, 'class.room.php', 'Module_Room');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
		}
		return true;
	}
}

?>