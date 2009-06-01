<?php

class Upgrade_Sysforum extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'sysforum';
	
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
				
				$query = "CREATE TABLE `learning_sysforum` (
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
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
	
}

?>