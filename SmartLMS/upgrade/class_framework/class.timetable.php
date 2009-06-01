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

class Upgrade_CoreTimetable extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'timetable';
	
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
				
				$content = "CREATE TABLE `core_resource` (
				  `resource_code` varchar(60) NOT NULL default '',
				  `platform` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`resource_code`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `core_resource_timetable` (
				  `id` int(11) NOT NULL auto_increment,
				  `resource` varchar(60) NOT NULL default '',
				  `resource_id` int(11) NOT NULL default '0',
				  `consumer` varchar(60) NOT NULL default '',
				  `consumer_id` int(11) NOT NULL default '0',
				  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `core_resource` VALUES ('classroom', 'lms');
				INSERT INTO `core_resource` VALUES ('course', 'lms');
				INSERT INTO `core_resource` VALUES ('course_edition', 'lms');
				INSERT INTO `core_resource` VALUES ('user', 'framework');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>