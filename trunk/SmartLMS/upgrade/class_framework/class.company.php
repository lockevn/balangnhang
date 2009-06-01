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

class Upgrade_CoreCompany extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'company';
	
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
				
				$content = "CREATE TABLE `core_company` (
				  `company_id` int(11) NOT NULL auto_increment,
				  `code` varchar(255) default NULL,
				  `name` varchar(255) NOT NULL default '',
				  `ctype_id` int(11) NOT NULL default '0',
				  `cstatus_id` int(11) NOT NULL default '0',
				  `address` text NOT NULL,
				  `tel` varchar(255) NOT NULL default '',
				  `email` varchar(255) NOT NULL default '',
				  `vat_number` varchar(255) NOT NULL default '',
				  `restricted_access` tinyint(1) NOT NULL default '0',
				  `is_used` tinyint(1) NOT NULL default '0',
				  `imported_from_connection` varchar(255) default NULL,
				  PRIMARY KEY  (`company_id`),
				  UNIQUE KEY `code` (`code`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "CREATE TABLE `core_company_field` (
				  `idst` varchar(14) NOT NULL default '0',
				  `id_field` int(11) NOT NULL default '0',
				  `mandatory` enum('true','false') NOT NULL default 'false',
				  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
				  PRIMARY KEY  (`idst`,`id_field`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_company_fieldentry` (
				  `id_common` int(11) NOT NULL default '0',
				  `id_common_son` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `user_entry` text NOT NULL,
				  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_company_user` (
				  `company_id` int(11) NOT NULL default '0',
				  `user_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`company_id`,`user_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_companystatus` (
				  `cstatus_id` int(11) NOT NULL auto_increment,
				  `label` varchar(255) NOT NULL default '',
				  `is_used` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`cstatus_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_companytype` (
				  `ctype_id` int(11) NOT NULL auto_increment,
				  `label` varchar(255) NOT NULL default '',
				  `is_used` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`ctype_id`)
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