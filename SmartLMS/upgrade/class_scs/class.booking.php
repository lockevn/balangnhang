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

class Upgrade_ScsBooking extends Upgrade {
	
	var $platfom = 'scs';
	
	var $mname = 'booking';
	
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
				
				$content = "CREATE TABLE `conference_booking` (
				  `booking_id` int(11) NOT NULL auto_increment,
				  `room_id` int(11) NOT NULL default '0',
				  `platform` varchar(255) NOT NULL default '',
				  `module` varchar(100) NOT NULL default '',
				  `user_idst` int(11) NOT NULL default '0',
				  `approved` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`booking_id`)
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