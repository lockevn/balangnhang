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

class Upgrade_CrmSetting extends Upgrade {
	
	var $platfom = 'crm';
	
	var $mname = 'setting';
	
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
				CREATE TABLE `crm_setting` (
				  `param_name` varchar(255) NOT NULL default '',
				  `param_value` text NOT NULL,
				  `value_type` varchar(255) NOT NULL default 'string',
				  `max_size` int(3) NOT NULL default '255',
				  `pack` varchar(255) NOT NULL default 'main',
				  `regroup` int(5) NOT NULL default '0',
				  `sequence` int(5) NOT NULL default '0',
				  `param_load` tinyint(1) NOT NULL default '1',
				  `hide_in_modify` tinyint(1) NOT NULL default '0',
				  `extra_info` text NOT NULL,
				  PRIMARY KEY  (`param_name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "
				INSERT INTO `crm_setting` VALUES ('crm_version', '3.0', 'string', 255, 'main', 0, 3, 1, 1, '');
				INSERT INTO `crm_setting` VALUES ('defaultTemplate', 'standard', 'template', 255, 'main', 0, 3, 1, 0, '');
				INSERT INTO `crm_setting` VALUES ('default_language', 'italian', 'language', 255, 'main', 0, 2, 1, 0, '');
				INSERT INTO `crm_setting` VALUES ('ttlSession', '2000', 'int', 5, 'main', 0, 4, 1, 0, '');
				INSERT INTO `crm_setting` VALUES ('url', 'http://localhost/docebo_35/doceboCrm/', 'string', 255, 'main', 0, 1, 1, 0, '');
				INSERT INTO `crm_setting` VALUES ('use_simplified', 'on', 'enum', 3, 'main', 0, 1, 1, 0, '');
				INSERT INTO `crm_setting` VALUES ('visuItem', '20', 'int', 3, 'main', 0, 11, 1, 0, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>