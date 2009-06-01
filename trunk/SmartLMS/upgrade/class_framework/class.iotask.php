<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

class Upgrade_CoreIotask extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'iotask';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.0.6" : {
				$i = 0;
				
				$content = "CREATE TABLE `core_connection` (
				  `name` varchar(50) NOT NULL default '',
				  `description` varchar(255) default NULL,
				  `type` varchar(50) NOT NULL default '',
				  `params` text,
				  PRIMARY KEY  (`name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `core_connector` (
				  `type` varchar(25) NOT NULL default '',
				  `file` varchar(255) NOT NULL default '',
				  `class` varchar(50) NOT NULL default '',
				  PRIMARY KEY  (`type`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `core_task` (
				  `name` varchar(50) NOT NULL default '',
				  `description` varchar(255) NOT NULL default '',
				  `conn_source` varchar(50) NOT NULL default '',
				  `conn_destination` varchar(50) NOT NULL default '',
				  `schedule_type` enum('at','any') NOT NULL default 'at',
				  `schedule` varchar(50) NOT NULL default '',
				  `import_type` varchar(50) NOT NULL default '',
				  `map` text NOT NULL,
				  `last_execution` datetime default NULL,
				  PRIMARY KEY  (`name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `core_task` ADD `sequence` INT( 3 ) NOT NULL ;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>