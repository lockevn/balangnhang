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

class Upgrade_LmsEdition extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'edition';
	
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
				
				$content = "CREATE TABLE `learning_course_edition` (
				  `idCourseEdition` int(11) NOT NULL auto_increment,
				  `idCourse` int(11) NOT NULL default '0',
				  `code` varchar(50) NOT NULL default '',
				  `name` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `status` int(1) NOT NULL default '0',
				  `img_material` varchar(255) NOT NULL default '',
				  `img_othermaterial` varchar(255) NOT NULL default '',
				  `date_begin` date NOT NULL default '0000-00-00',
				  `date_end` date NOT NULL default '0000-00-00',
				  `hour_begin` varchar(5) NOT NULL default '',
				  `hour_end` varchar(5) NOT NULL default '',
				  `classrooms` varchar(255) NOT NULL default '',
				  `max_num_subscribe` int(11) NOT NULL default '0',
				  `min_num_subscribe` int(11) NOT NULL default '0',
				  `price` varchar(255) NOT NULL default '',
				  `advance` varchar(255) NOT NULL default '',
				  `edition_type` varchar(255) NOT NULL default 'elearning',
				  `allow_overbooking` tinyint(1) NOT NULL default '0',
				  `can_subscribe` tinyint(1) NOT NULL default '0',
				  `sub_start_date` datetime default NULL,
				  `sub_end_date` datetime default NULL,
				  PRIMARY KEY  (`idCourseEdition`)
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