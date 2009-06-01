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

class Upgrade_CmsForm extends Upgrade {
	
	var $platfom = 'cms';
	
	var $mname = 'form';
	
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
				
				$content = "CREATE TABLE `cms_form_map` (
				  `form_id` int(11) NOT NULL default '0',
				  `field_id` int(11) NOT NULL default '0',
				  `field_map_resource` enum('user','company','chistory') NOT NULL default 'user',
				  `field_type` enum('predefined','custom') NOT NULL default 'predefined',
				  `field_map_to` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`form_id`,`field_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `cms_form_sendinfo` (
				  `send_id` int(11) NOT NULL auto_increment,
				  `form_id` int(11) NOT NULL default '0',
				  `form_type` enum('normal','crm_contact') NOT NULL default 'normal',
				  `send_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `email` varchar(255) NOT NULL default '',
				  `user_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`send_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `cms_form_storage` (
				  `id_common` varchar(11) NOT NULL default '',
				  `id_common_son` int(11) NOT NULL default '0',
				  `id_user` varchar(100) NOT NULL default '0',
				  `user_entry` text NOT NULL,
				  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `cms_form` ADD `storeinfo` TINYINT( 1 ) NOT NULL ,
				ADD `form_type` ENUM( 'normal', 'crm_contact' ) DEFAULT 'normal' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>