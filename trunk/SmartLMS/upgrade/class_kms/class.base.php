<?php

class Upgrade_Basekms extends Upgrade {
	
	var $platfom = 'kms';
	
	var $mname = 'base';
	
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
				
				$fn = $GLOBALS['where_upgrade'].'/data/sql/kms.sql';
				
				$handle = fopen($fn, "r");
				$content = fread($handle, filesize($fn));
				fclose($handle);
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0';
				return true;
			};break;
		}
		return true;
	}
}

?>