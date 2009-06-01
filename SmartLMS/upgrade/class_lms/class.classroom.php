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

class Upgrade_LmsClassroom extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'classroom';
	
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
				
				$content = "
				CREATE TABLE `learning_class_location` (
				  `location_id` int(11) NOT NULL auto_increment,
				  `location` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`location_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_classroom` (
				  `idClassroom` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `location_id` int(11) NOT NULL default '0',
				  `room` varchar(255) NOT NULL default '',
				  `street` varchar(255) NOT NULL default '',
				  `city` varchar(255) NOT NULL default '',
				  `state` varchar(255) NOT NULL default '',
				  `zip_code` varchar(255) NOT NULL default '',
				  `phone` varchar(255) NOT NULL default '',
				  `fax` varchar(255) NOT NULL default '',
				  `capacity` varchar(255) NOT NULL default '',
				  `disposition` text NOT NULL,
				  `instrument` text NOT NULL,
				  `available_instrument` text NOT NULL,
				  `note` text NOT NULL,
				  `responsable` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idClassroom`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_classroom_calendar` (
				  `id` int(11) NOT NULL auto_increment,
				  `classroom_id` int(11) NOT NULL default '0',
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `owner` int(11) NOT NULL default '0',
				  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
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