<?php

class Upgrade_Category extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'category';
	
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
				ALTER TABLE `learning_category` ADD `idParent` INT( 11 ) NOT NULL AFTER `idCategory` ,
				ADD `lev` INT( 11 ) NOT NULL DEFAULT 1 AFTER `idParent`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				ALTER TABLE `learning_category` CHANGE `name` `path` TEXT NOT NULL ,
				CHANGE `textof` `description` TEXT NOT NULL";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				UPDATE `learning_category` 
				SET `path` = CONCAT('/root/' , `path`)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>