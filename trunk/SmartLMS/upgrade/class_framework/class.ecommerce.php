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

class Upgrade_CoreEcommerce extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'ecommerce';
	
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
				
				$content = "CREATE TABLE `core_country` (
				  `id_country` int(11) NOT NULL auto_increment,
				  `name_country` varchar(64) NOT NULL default '',
				  `iso_code_country` varchar(3) NOT NULL default '',
				  `id_zone` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_country`),
				  KEY `IDX_COUNTRIES_NAME` (`name_country`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "ALTER TABLE `core_country` CHANGE `iso_code_country` `iso_code_country` VARCHAR( 3 ) NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>