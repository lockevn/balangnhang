<?php

class Upgrade_Scorm extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'scorm';
	
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
				SELECT idscorm_package, path 
				FROM learning_scorm_package";
				$result = $this->db_man->query($query);
				
				while(list($id, $path) = $this->db_man->fetchRow($result)) {
					
					$new_path = substr($path, (strrpos($path, '/') + 1));
					
					$query = "
					UPDATE learning_scorm_package
					SET path = '".$new_path."'
					WHERE idscorm_package = '".$id."'";
					$re = $this->db_man->query($query);
				}
				
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `learning_scorm_items` ADD `adlcp_completionthreshold` VARCHAR( 10 ) NOT NULL ;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_scorm_package` ADD `scormVersion` VARCHAR( 10 ) NOT NULL DEFAULT '1.2';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_scorm_tracking` CHANGE `xmldata` `xmldata` LONGBLOB NULL DEFAULT NULL;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_scorm_tracking` 
							CHANGE `score_raw` `score_raw` FLOAT NULL DEFAULT NULL,
							CHANGE `score_min` `score_min` FLOAT NULL DEFAULT NULL,
							CHANGE `score_max` `score_max` FLOAT NULL DEFAULT NULL;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_scorm_tracking_history` (
							 `idscorm_tracking` INT( 11 ) NOT NULL ,
							 `date_action` DATETIME NOT NULL ,
							 `score_raw` FLOAT NULL DEFAULT NULL,
							 `score_max` FLOAT NULL DEFAULT NULL,
							 `session_time` VARCHAR( 15 ),
							 `lesson_status` VARCHAR( 24 ) NOT NULL ,
							 PRIMARY KEY ( `idscorm_tracking` , `date_action` )
							) ENGINE = innodb DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_organization` ADD `width` VARCHAR( 4 ) NOT NULL ,
					ADD `height` VARCHAR( 4 ) NOT NULL ,
					ADD `publish_from` DATETIME NULL ,
					ADD `publish_to` DATETIME NULL";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>