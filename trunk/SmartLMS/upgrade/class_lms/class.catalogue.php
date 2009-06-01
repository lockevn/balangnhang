<?php

class Upgrade_Catalogue extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'catalogue';
	
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
				CREATE TABLE `learning_catalogue` (
				  `idCatalogue` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  PRIMARY KEY  (`idCatalogue`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				CREATE TABLE `learning_catalogue_entry` (
				  `idCatalogue` int(11) NOT NULL default '0',
				  `idEntry` int(11) NOT NULL default '0',
				  `type_of_entry` enum('course','coursepath') NOT NULL default 'course',
				  PRIMARY KEY  (`idCatalogue`,`idEntry`,`type_of_entry`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				CREATE TABLE `learning_catalogue_member` (
				  `idCatalogue` int(11) NOT NULL default '0',
				  `idst_member` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idCatalogue`,`idst_member`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>