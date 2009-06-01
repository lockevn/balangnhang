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

class Upgrade_ScsIntelligere extends Upgrade {
	
	var $platfom = 'scs';
	
	var $mname = 'intelligere';
	
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
				
				$content = "CREATE TABLE `conference_inte_room` (
				  `id_room` int(11) NOT NULL auto_increment,
				  `ext_key` int(11) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `description` varchar(255) NOT NULL default '',
				  `room_parent` int(11) NOT NULL default '0',
				  `room_path` text NOT NULL,
				  `type` enum('public','private') NOT NULL default 'public',
				  `max_user` int(11) NOT NULL default '0',
				  `owner` int(11) NOT NULL default '0',
				  `blocked` tinyint(1) NOT NULL default '0',
				  `room_perm` int(8) NOT NULL default '0',
				  `zone` varchar(20) NOT NULL default '',
				  `bookable` tinyint(1) NOT NULL default '0',
				  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `logo` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id_room`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `conference_inte_token` (
				  `id_user` int(11) NOT NULL default '0',
				  `token` varchar(64) NOT NULL default '',
				  `role` varchar(20) NOT NULL default 'guest',
				  PRIMARY KEY  (`id_user`,`token`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `conference_inte_user` (
				  `id_user` int(11) NOT NULL auto_increment,
				  `userid` varchar(255) NOT NULL default '',
				  `firstname` varchar(255) NOT NULL default '',
				  `lastname` varchar(255) NOT NULL default '',
				  `role` tinyint(1) NOT NULL default '0',
				  `in_room` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_user`,`in_room`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES ('url_videoconference_intelligere', '', 'string', '255', '4', '1', '1', '0', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES ('intelligere_streaming_server_type', '', 'string', '255', '4', '2', '1', '0', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES ('intelligere_remote_desktop_server_type', '', 'string', '255', '4', '3', '1', '0', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES ('intelligere_user', '', 'string', '255', '4', '4', '1', '0', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES ('intelligere_application_code', '', 'string', '255', '4', '5', '1', '0', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>