<?php

class Upgrade_Pflow extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'pfolw';
	
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
				
				$query = "CREATE TABLE `core_pflow_lang` (
				  `id` int(11) NOT NULL default '0',
				  `type` varchar(30) NOT NULL default '',
				  `language` varchar(40) NOT NULL default '',
				  `val_name` varchar(30) NOT NULL default '',
				  `value` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id`,`type`,`language`,`val_name`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `core_pflow_lang` VALUES (1, 'flow', 'english', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (1, 'flow', 'italian', 'label', 'Pubblicazione immediata');
				INSERT INTO `core_pflow_lang` VALUES (1, 'step', 'italian', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (1, 'flow', 'italian', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (2, 'flow', 'italian', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (2, 'flow', 'italian', 'label', 'Pubblica / spubblica');
				INSERT INTO `core_pflow_lang` VALUES (1, 'step', 'english', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (1, 'step', 'italian', 'label', 'Pubblicato');
				INSERT INTO `core_pflow_lang` VALUES (1, 'flow', 'english', 'label', 'Immediate publish');
				INSERT INTO `core_pflow_lang` VALUES (2, 'step', 'italian', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (2, 'flow', 'english', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (2, 'flow', 'english', 'label', 'Publish / Unpublish');
				INSERT INTO `core_pflow_lang` VALUES (2, 'step', 'italian', 'label', 'Non pubblicato');
				INSERT INTO `core_pflow_lang` VALUES (2, 'step', 'english', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (2, 'step', 'english', 'label', 'Unpublished');
				INSERT INTO `core_pflow_lang` VALUES (1, 'step', 'english', 'label', 'Published');
				INSERT INTO `core_pflow_lang` VALUES (7, 'step', 'italian', 'label', 'Step 2');
				INSERT INTO `core_pflow_lang` VALUES (7, 'step', 'italian', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (8, 'step', 'english', 'label', 'Step 3');
				INSERT INTO `core_pflow_lang` VALUES (8, 'step', 'english', 'description', '');
				INSERT INTO `core_pflow_lang` VALUES (8, 'step', 'italian', 'label', 'Step 3');
				INSERT INTO `core_pflow_lang` VALUES (8, 'step', 'italian', 'description', '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `core_pflow_list` (
				  `flow_id` int(11) NOT NULL auto_increment,
				  `flow_code` varchar(20) default NULL,
				  `default` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`flow_id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `core_pflow_list` VALUES (1, 'pub_onestate', 1, 1);
				INSERT INTO `core_pflow_list` VALUES (2, 'pub_twostate', 1, 2);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "
				CREATE TABLE `core_pflow_step` (
				  `step_id` int(11) NOT NULL auto_increment,
				  `flow_id` int(11) NOT NULL default '0',
				  `st_id` int(11) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  `is_published` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`step_id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), '/pubflow_step_1', '', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				$query = "
				SELECT LAST_INSERT_ID()";
				list($p1) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), '/pubflow_step_2', '', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				$query = "
				SELECT LAST_INSERT_ID()";
				list($p2) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), '/pubflow_step_3', '', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				$query = "
				SELECT LAST_INSERT_ID()";
				list($p3) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "
				INSERT INTO `core_pflow_step` VALUES (1, 1, $p1, 3, 1);
				INSERT INTO `core_pflow_step` VALUES (2, 2, $p2, 4, 0);
				INSERT INTO `core_pflow_step` VALUES (3, 2, $p3, 5, 1);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 9);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>