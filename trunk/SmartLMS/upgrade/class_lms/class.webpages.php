<?php

class Upgrade_Webpages extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'webpages';
	
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
				
				$query = "ALTER TABLE `learning_webpages` CHANGE `language` `language` VARCHAR( 255 ) NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "ALTER TABLE `learning_webpages` ADD `in_home` TINYINT( 1 ) NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				UPDATE `learning_webpages` 
				SET `in_home` = 1 
				WHERE publish = 1";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				UPDATE `learning_webpages` 
				SET publish = 1";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
	
}

?>