<?php

class Upgrade_Teleskill extends Upgrade {
	
	var $platfom = 'scs';
	
	var $mname = 'teleskill';
	
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
				
				$query = "CREATE TABLE `conference_teleskill_room` (
				  `roomid` int(11) NOT NULL default '0',
				  `uid` int(11) NOT NULL default '0',
				  `zone` varchar(255) NOT NULL default '',
				  `title` varchar(255) NOT NULL default '',
				  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`roomid`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "ALTER TABLE `conference_teleskill_room` ADD `bookable` TINYINT( 1 ) NOT NULL ,
				ADD `capacity` INT( 11 ) DEFAULT NULL ;";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `conference_teleskill` CHANGE `idConference` `idConference` BIGINT( 20 ) NOT NULL DEFAULT '0',
							CHANGE `roomid` `roomid` BIGINT( 20 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
			
		}
		
		return true;
	}
}

?>