<?php

class Upgrade_Link extends Upgrade {
	
	var $platfom = 'cms';
	
	var $mname = 'links';
	
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
				
				$query = "ALTER TABLE `cms_links_info` ADD `keywords` TEXT NOT NULL AFTER `title` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			
		}
		return true;
	}
}

?>