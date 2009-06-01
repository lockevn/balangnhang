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

class Upgrade_CoreUser extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'user';
	
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
				
				$content = "CREATE TABLE `core_user_friend` (
				  `id_user` int(11) NOT NULL default '0',
				  `id_friend` int(11) NOT NULL default '0',
				  `waiting` int(1) NOT NULL default '0',
				  `request_msg` text NOT NULL,
				  PRIMARY KEY  (`id_user`,`id_friend`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `core_user_myfiles` (
				  `id_file` int(11) NOT NULL auto_increment,
				  `area` varchar(255) NOT NULL default '',
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `file_name` varchar(255) NOT NULL default '',
				  `owner` int(11) NOT NULL default '0',
				  `file_policy` int(1) NOT NULL default '0',
				  PRIMARY KEY  (`id_file`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_user_profileview` (
				  `id_owner` int(11) NOT NULL default '0',
				  `id_viewer` int(11) NOT NULL default '0',
				  `date_view` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_owner`,`id_viewer`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `core_user_file` ADD `media_url` VARCHAR( 255 ) NOT NULL AFTER `real_fname`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>