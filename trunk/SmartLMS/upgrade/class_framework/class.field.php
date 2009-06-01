<?php

class Upgrade_Field extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'field';
	
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
				
				$query = "
				CREATE TABLE `core_field` (
				  `idField` int(11) NOT NULL auto_increment,
				  `id_common` int(11) NOT NULL default '0',
				  `type_field` varchar(255) NOT NULL default '',
				  `lang_code` varchar(255) NOT NULL default '',
				  `translation` varchar(255) NOT NULL default '',
				  `sequence` int(5) NOT NULL default '0',
				  `show_on_platform` varchar(255) NOT NULL default 'framework,',
				  PRIMARY KEY  (`idField`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `core_field_son` (
				  `idSon` int(11) NOT NULL auto_increment,
				  `idField` int(11) NOT NULL default '0',
				  `id_common_son` int(11) NOT NULL default '0',
				  `lang_code` varchar(50) NOT NULL default '',
				  `translation` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idSon`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `core_field_type` (
				  `type_field` varchar(255) NOT NULL default '',
				  `type_file` varchar(255) NOT NULL default '',
				  `type_class` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`type_field`)
				) ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `core_field_type` VALUES ('textfield', 'class.textfield.php', 'Field_Textfield');
				INSERT INTO `core_field_type` VALUES ('dropdown', 'class.dropdown.php', 'Field_Dropdown');
				INSERT INTO `core_field_type` VALUES ('freetext', 'class.freetext.php', 'Field_Freetext');
				INSERT INTO `core_field_type` VALUES ('date', 'class.date.php', 'Field_Date');
				INSERT INTO `core_field_type` VALUES ('upload', 'class.upload.php', 'Field_Upload');
				INSERT INTO `core_field_type` VALUES ('yesno', 'class.yesno.php', 'Field_Yesno');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `core_field_userentry` (
				  `id_common` varchar(11) NOT NULL default '',
				  `id_common_son` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `user_entry` text NOT NULL,
				  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `core_field_son` ADD `sequence` INT( 11 ) NOT NULL ;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>