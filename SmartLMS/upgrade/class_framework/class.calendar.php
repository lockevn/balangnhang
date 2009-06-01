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

class Upgrade_CoreCalendar extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'calendar';
	
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
				
				$content = "CREATE TABLE `core_calendar` (
				  `id` bigint(20) NOT NULL auto_increment,
				  `class` varchar(30) default NULL,
				  `create_date` datetime default NULL,
				  `start_date` datetime default NULL,
				  `end_date` datetime default NULL,
				  `title` varchar(255) default NULL,
				  `description` text,
				  `private` varchar(2) default NULL,
				  `category` varchar(255) default NULL,
				  `type` bigint(20) default NULL,
				  `visibility_rules` tinytext,
				  `_owner` int(11) default NULL,
				  `_day` smallint(2) default NULL,
				  `_month` smallint(2) default NULL,
				  `_year` smallint(4) default NULL,
				  PRIMARY KEY  (`id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>