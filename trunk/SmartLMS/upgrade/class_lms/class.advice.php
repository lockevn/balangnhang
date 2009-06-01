<?php

class Upgrade_Advice extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'advice';
	
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
				ALTER TABLE `learning_advice` 
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				ALTER TABLE `learning_adviceuser` 
				CHANGE `readFlag` `archivied` TINYINT( 1 ) NOT NULL DEFAULT '0'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$old_path = '/addons/fckeditor2rc2/editor/images/smiley/msn/';
				$new_path = '../doceboCore/addons/fckeditor/editor/images/smiley/msn/';
				
				$query = "UPDATE learning_advice
				SET description  = REPLACE(description , '$old_path', '$new_path')";
				$this->db_man->query($query);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>