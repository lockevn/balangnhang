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

class Upgrade_CoreWiki extends Upgrade {
	
	var $platfom = 'core';
	
	var $mname = 'wiki';
	
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
				
				$content = "CREATE TABLE `core_wiki` (
				  `wiki_id` int(11) NOT NULL auto_increment,
				  `source_platform` varchar(255) NOT NULL default '',
				  `public` tinyint(1) NOT NULL default '0',
				  `language` varchar(50) NOT NULL default '',
				  `other_lang` text NOT NULL,
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `page_count` int(11) NOT NULL default '0',
				  `revision_count` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`wiki_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `core_wiki_page` (
				  `page_id` int(11) NOT NULL auto_increment,
				  `page_code` varchar(60) NOT NULL default '',
				  `parent_id` int(11) NOT NULL default '0',
				  `page_path` varchar(255) NOT NULL default '',
				  `lev` int(3) NOT NULL default '0',
				  `wiki_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`page_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_wiki_page_info` (
				  `page_id` int(11) NOT NULL default '0',
				  `language` varchar(50) NOT NULL default '',
				  `title` varchar(255) NOT NULL default '',
				  `version` int(11) NOT NULL default '0',
				  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
				  `wiki_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`page_id`,`language`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_wiki_revision` (
				  `wiki_id` int(11) NOT NULL default '0',
				  `page_id` int(11) NOT NULL default '0',
				  `version` int(11) NOT NULL default '0',
				  `language` varchar(50) NOT NULL default '0',
				  `author` int(11) NOT NULL default '0',
				  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `content` longtext NOT NULL,
				  PRIMARY KEY  (`wiki_id`,`page_id`,`version`,`language`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
			case '3.6.0':
				$i = 0;
				
				$content = "ALTER TABLE `core_wiki_page` CHANGE `page_code` `page_code` VARCHAR( 255 );";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0.1';
				return true;
			break;
		}
		return true;
	}
}

?>