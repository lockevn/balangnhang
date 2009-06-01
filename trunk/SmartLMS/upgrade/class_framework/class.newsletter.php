<?php

class Upgrade_Newsletter extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'newsletter';
	
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
				
				$query = "CREATE TABLE `core_newsletter` (
				  `id` int(11) NOT NULL auto_increment,
				  `id_send` int(11) NOT NULL default '0',
				  `sub` varchar(255) NOT NULL default '',
				  `msg` text NOT NULL,
				  `fromemail` varchar(255) NOT NULL default '',
				  `language` varchar(255) NOT NULL default '',
				  `tot` int(11) NOT NULL default '0',
				  `stime` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `core_newsletter_sendto` (
				  `id_send` int(11) NOT NULL default '0',
				  `idst` int(11) NOT NULL default '0',
				  `stime` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_send`,`idst`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "ALTER TABLE `core_newsletter` ADD `send_type` ENUM( 'email', 'sms' ) DEFAULT 'email' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `core_newsletter` ADD `file` TEXT NOT NULL ;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>