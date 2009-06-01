<?php

class Upgrade_Regset extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'regset';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "2.0.4" : {
				
				$query = "
				CREATE TABLE `core_reg_list` (
				  `region_id` varchar(100) NOT NULL default '',
				  `lang_code` varchar(50) NOT NULL default '',
				  `region_desc` varchar(255) NOT NULL default '',
				  `default_region` tinyint(1) NOT NULL default '0',
				  `browsercode` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`region_id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `core_reg_list` VALUES ('italy', 'italian', 'Italia', 1, 'it');
				INSERT INTO `core_reg_list` VALUES ('england', 'english', 'england, usa, ...', 0, 'en-EN, en-US');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				
				$query = "CREATE TABLE `core_reg_setting` (
				  `region_id` varchar(100) NOT NULL default '',
				  `val_name` varchar(100) NOT NULL default '',
				  `value` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`region_id`,`val_name`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				
				$query = "INSERT INTO `core_reg_setting` VALUES ('england', 'custom_date_format', '');
				INSERT INTO `core_reg_setting` VALUES ('england', 'date_sep', '/');
				INSERT INTO `core_reg_setting` VALUES ('italy', 'custom_date_format', '');
				INSERT INTO `core_reg_setting` VALUES ('italy', 'time_format', 'H_i');
				INSERT INTO `core_reg_setting` VALUES ('italy', 'date_sep', '-');
				INSERT INTO `core_reg_setting` VALUES ('england', 'date_format', 'd_m_Y');
				INSERT INTO `core_reg_setting` VALUES ('italy', 'date_format', 'd_m_Y');
				INSERT INTO `core_reg_setting` VALUES ('england', 'time_format', 'H_i');
				INSERT INTO `core_reg_setting` VALUES ('italy', 'custom_time_format', '');
				INSERT INTO `core_reg_setting` VALUES ('england', 'custom_time_format', '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>