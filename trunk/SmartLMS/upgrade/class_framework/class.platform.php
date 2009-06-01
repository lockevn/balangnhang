<?php

class Upgrade_Platform extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'platform';
	
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
				
				$query = "CREATE TABLE `core_platform` (
				  `platform` varchar(255) NOT NULL default '',
				  `class_file` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  `class_file_menu` varchar(255) NOT NULL default '',
				  `class_name_menu` varchar(255) NOT NULL default '',
				  `class_name_menu_managment` varchar(255) NOT NULL default '',
				  `file_class_config` varchar(255) NOT NULL default '',
				  `class_name_config` varchar(255) NOT NULL default '',
				  `var_default_template` varchar(255) NOT NULL default '',
				  `class_default_admin` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `is_active` enum('true','false') NOT NULL default 'true',
				  `mandatory` enum('true','false') NOT NULL default 'true',
				  `dependencies` text NOT NULL,
				  `main` enum('true','false') NOT NULL default 'true',
				  PRIMARY KEY  (`platform`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `core_platform` VALUES ('framework', '', '', 'class.admin_menu_fw.php', 'Admin_Framework', 'Admin_Managment_Framework', 'class.conf_fw.php', 'Config_Framework', 'defaultTemplate', 'Module', 1, 'true', 'true', '', 'false');
				INSERT INTO `core_platform` VALUES ('lms', '', '', 'class.admin_menu_lms.php', 'Admin_Lms', 'Admin_Managment_Lms', 'class.conf_lms.php', 'Config_Lms', 'defaultTemplate', 'LmsAdminModule', 2, 'true', 'false', '', 'true');
				INSERT INTO `core_platform` VALUES ('scs', '', '', 'class.admin_menu_scs.php', 'Admin_Scs', 'Admin_Managment_Scs', 'class.conf_scs.php', 'Config_Scs', 'defaultTemplate', 'ScsAdminModule', 3, 'true', 'true', '', 'false');
				INSERT INTO `core_platform` VALUES ('kms', '', '', 'class.admin_menu_kms.php', 'Admin_Kms', 'Admin_Managment_Kms', 'class.conf_kms.php', 'Config_Kms', 'defaultTemplate', 'kmsAdminModule', 4, 'false', 'false', '', 'false');
				INSERT INTO `core_platform` VALUES ('cms', '', '', 'class.admin_menu_cms.php', 'Admin_Cms', 'Admin_Managment_Cms', 'class.conf_cms.php', 'Config_Cms', 'defaultCmsTemplate', 'CmsAdminModule', 5, 'false', 'false', '', 'false');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>