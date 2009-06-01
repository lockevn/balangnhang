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

class Upgrade_LmsReservation extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'reservation';
	
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
								
				$content = "CREATE TABLE `learning_reservation_category` (
				  `idCategory` int(11) NOT NULL auto_increment,
				  `idCourse` int(11) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `maxSubscription` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idCategory`)
				) ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_reservation_events` (
				  `idEvent` int(11) NOT NULL auto_increment,
				  `idCourse` int(11) NOT NULL default '0',
				  `idLaboratory` int(11) NOT NULL default '0',
				  `idCategory` int(11) NOT NULL default '0',
				  `title` varchar(255) NOT NULL default '',
				  `description` longtext,
				  `date` date NOT NULL default '0000-00-00',
				  `maxUser` int(11) NOT NULL default '0',
				  `deadLine` date NOT NULL default '0000-00-00',
				  `fromTime` time NOT NULL default '00:00:00',
				  `toTime` time NOT NULL default '00:00:00',
				  PRIMARY KEY  (`idEvent`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_reservation_laboratories` (
				  `idLaboratory` int(11) NOT NULL auto_increment,
				  `idCourse` int(11) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `location` varchar(255) NOT NULL default '',
				  `description` longtext NOT NULL,
				  PRIMARY KEY  (`idLaboratory`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_reservation_subscribed` (
				  `idstUser` int(11) NOT NULL default '0',
				  `idEvent` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idstUser`,`idEvent`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `learning_reservation_perm` (
					`event_id` INT NOT NULL ,
					`user_idst` INT NOT NULL ,
					`perm` VARCHAR( 255 ) NOT NULL ,
					PRIMARY KEY ( `event_id` , `user_idst` , `perm` )
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