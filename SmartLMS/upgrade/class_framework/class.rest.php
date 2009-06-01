<?php

class Upgrade_Rest extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'rest';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.5.0.4":
				$i = 0;
				
				$content = "CREATE TABLE `core_rest_authentication` (
						  `id_user` int(11) NOT NULL,
						  `user_level` int(11) NOT NULL,
						  `token` varchar(255) collate utf8_unicode_ci NOT NULL,
						  `generation_date` datetime NOT NULL default '0000-00-00 00:00:00',
						  `last_enter_date` datetime default NULL,
						  `expiry_date` datetime NOT NULL default '0000-00-00 00:00:00',
						  PRIMARY KEY  (`token`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>