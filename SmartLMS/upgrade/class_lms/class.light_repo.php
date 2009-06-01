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

class Upgrade_LmsLight_repo extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'light_repo';
	
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
				
				$content = "CREATE TABLE `learning_light_repo` (
				  `id_repository` int(11) NOT NULL auto_increment,
				  `id_course` int(11) NOT NULL default '0',
				  `repo_title` varchar(255) NOT NULL default '',
				  `repo_descr` text NOT NULL,
				  PRIMARY KEY  (`id_repository`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_light_repo_user` (
				  `id_repo` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `last_enter` datetime NOT NULL default '0000-00-00 00:00:00',
				  `repo_lock` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`id_repo`,`id_user`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_light_repo_files` (
				  `id_file` int(11) NOT NULL auto_increment,
				  `id_repository` int(11) NOT NULL default '0',
				  `file_name` varchar(255) NOT NULL default '',
				  `file_descr` text NOT NULL,
				  `id_author` int(11) NOT NULL default '0',
				  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_file`)
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