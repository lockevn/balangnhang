<?php

class Upgrade_KmsMenu extends Upgrade {
	
	var $platfom = 'kms';
	
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
			
			case "3.0.2" : {
				
				$query = "DROP TABLE `kms_menu`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DROP TABLE `kms_menu_under`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `kms_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `kms_menu` VALUES (1, '_GENERAL_KMS', 'general', 1, 'true');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `kms_menu_under` (
				  `idUnder` int(11) NOT NULL auto_increment,
				  `idMenu` int(11) NOT NULL default '0',
				  `module_name` varchar(255) NOT NULL default '',
				  `default_name` varchar(255) NOT NULL default '',
				  `default_op` varchar(255) NOT NULL default '',
				  `associated_token` varchar(255) NOT NULL default '',
				  `of_platform` varchar(255) default NULL,
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idUnder`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `kms_menu_under` VALUES (1, 1, 'news', '_NEWS', 'news', 'view', NULL, 1);
				INSERT INTO `kms_menu_under` VALUES (2, 1, 'webpages', '_WEBPAGES', 'webpages', 'view', NULL, 2);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			
		}
		return true;
	}
}

?>