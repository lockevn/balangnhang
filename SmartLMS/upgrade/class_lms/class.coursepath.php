<?php

class Upgrade_Coursepath extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'coursepath';
	
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
				
				$query = "CREATE TABLE `learning_coursepath` (
				  `idPath` int(11) NOT NULL auto_increment,
				  `path_code` varchar(255) NOT NULL default '',
				  `path_name` varchar(255) NOT NULL default '',
				  `path_descr` text NOT NULL,
				  `subscribe_method` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`idPath`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				
				$query = "CREATE TABLE `learning_coursepath_courses` (
				  `idPath` int(11) NOT NULL default '0',
				  `idCourse` int(11) NOT NULL default '0',
				  `prerequisites` text NOT NULL,
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idPath`,`idCourse`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				
				$query = "CREATE TABLE `learning_coursepath_user` (
				  `idPath` int(11) NOT NULL default '0',
				  `idUser` int(11) NOT NULL default '0',
				  `waiting` tinyint(1) NOT NULL default '0',
				  `subscribed_by` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idPath`,`idUser`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "CREATE TABLE `learning_coursepath_slot` (
				  `id_slot` int(11) NOT NULL auto_increment,
				  `id_path` int(11) NOT NULL default '0',
				  `min_selection` int(3) NOT NULL default '0',
				  `max_selection` int(3) NOT NULL default '0',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`id_slot`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_coursepath` CHANGE `idPath` `id_path` INT( 11 ) NOT NULL AUTO_INCREMENT";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_coursepath_user` CHANGE `idPath` `id_path` INT( 11 ) DEFAULT '0' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_coursepath_courses` CHANGE `idPath` `id_path` INT( 11 ) DEFAULT '0' NOT NULL ,
				CHANGE `idCourse` `id_item` INT( 11 ) DEFAULT '0' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_coursepath_courses` ADD `in_slot` INT( 11 ) NOT NULL AFTER `id_item`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_coursepath_courses` DROP PRIMARY KEY ,
				ADD PRIMARY KEY ( `id_path` , `id_item` , `in_slot` )";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
		}
		return true;
	}
}

?>