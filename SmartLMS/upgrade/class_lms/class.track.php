<?php

class Upgrade_Track extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'track';
	
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
				
				$query = "ALTER TABLE `learning_trackingeneral` 
				CHANGE `type` `type` VARCHAR( 255 ) NOT NULL, 
				ADD `idEnter` INT( 11 ) NOT NULL AFTER `idTrack` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "ALTER TABLE `learning_tracksession` CHANGE `lastOp` `lastOp` CHAR( 5 ) NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>