<?php

class Upgrade_Hteditor extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'hteditor';
	
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
				
				$query = "CREATE TABLE `core_hteditor` (
				  `hteditor` varchar(255) NOT NULL default '',
				  `hteditorname` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`hteditor`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `core_hteditor` VALUES ('textarea', '_TEXTAREA');
				INSERT INTO `core_hteditor` VALUES ('fckeditor', '_FCKEDITOR');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>