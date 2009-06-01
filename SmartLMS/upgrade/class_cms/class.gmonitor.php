<?php

class Upgrade_Gmonitor extends Upgrade {
	
	var $platfom = 'cms';
	
	var $mname = 'gmonitor';
	
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
				
				$query = "CREATE TABLE `cms_area_option_text` (
				  `idBlock` int(11) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `text` text,
				  PRIMARY KEY  (`idBlock`,`name`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DELETE FROM `cms_area_option` WHERE `name` = 'gmonitoring';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			
		}
		return true;
	}
}

?>