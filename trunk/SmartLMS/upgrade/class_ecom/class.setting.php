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

class Upgrade_EcomSetting extends Upgrade {
	
	var $platfom = 'ecom';
	
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
				CREATE TABLE `ecom_setting` (
				  `param_name` varchar(255) NOT NULL default '',
				  `param_value` varchar(255) NOT NULL default '',
				  `value_type` varchar(255) NOT NULL default 'string',
				  `max_size` int(3) NOT NULL default '255',
				  `regroup` int(5) NOT NULL default '0',
				  `sequence` int(5) NOT NULL default '0',
				  `param_load` tinyint(1) NOT NULL default '1',
				  `hide_in_modify` tinyint(1) NOT NULL default '0',
				  `extra_info` text NOT NULL,
				  PRIMARY KEY  (`param_name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "
				INSERT INTO `ecom_setting` VALUES ('admin_mail', 'sample@localhost.com', 'string', 255, 0, 2, 1, 0, '');
				INSERT INTO `ecom_setting` VALUES ('ecom_type', 'standard', 'ecommerce_type', 30, 0, 4, 1, 0, '');
				INSERT INTO `ecom_setting` VALUES ('ttlSession', '1000', 'int', 5, 0, 3, 1, 0, '');
				INSERT INTO `ecom_setting` VALUES ('url', 'http://localhost/docebo_35/doceboEcom/', 'string', 255, 0, 1, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
			case "3.5.0" : {
				$i = 0;
     
				$content = "
				INSERT INTO `ecom_setting` VALUES ('company_details', '', 'textarea', 65535, 0, 5, 1, 0, '');
				INSERT INTO `ecom_setting` VALUES ('currency_label', '&euro;', 'string', 255, 0, 7, 1, 0, '');
				INSERT INTO `ecom_setting` VALUES ('send_order_email', '', 'string', 255, 0, 6, 1, 0, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "
				INSERT INTO `ecom_tax_cat_god` VALUES (1, 'Online courses', 'course');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$this->end_version = '3.5.0.1';
				return true;
			};break;
		}
		return true;
	}
}

?>