<?php

class Upgrade_Message extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'message';
	
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
				ALTER TABLE `learning_message` 
				ADD `idCourse` INT( 11 ) NOT NULL AFTER `idMessage`,
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				SELECT DISTINCT idMessage, idCourse
				FROM learning_messagedest";
				$re_mes = $this->db_man->query($query);
				while(list($id_m, $id_c) = $this->db_man->fetchRow($re_mes)) {
					
					$query = "
					UPDATE learning_message 
					SET idCourse = '".$id_c."' 
					WHERE idMessage = '".$id_m."'";
					$this->db_man->query($query);
				}
				
				$query = "ALTER TABLE `learning_messagedest` RENAME `learning_message_user` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "ALTER TABLE `learning_message_user` 
				CHANGE `readFlag` `read` TINYINT( 1 ) NOT NULL DEFAULT '0';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$old_path = '/addons/fckeditor2rc2/editor/images/smiley/msn/';
				$new_path = '../doceboCore/addons/fckeditor/editor/images/smiley/msn/';
				
				$query = "UPDATE learning_message
				SET textof = REPLACE(textof, '$old_path', '$new_path')";
				$this->db_man->query($query);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>