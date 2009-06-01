<?php

class Upgrade_Report extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'report';
	
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
				CREATE TABLE `learning_report` (
				  `id_report` int(11) NOT NULL auto_increment,
				  `report_name` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  `file_name` varchar(255) NOT NULL default '',
				  `use_user_selection` enum('true','false') NOT NULL default 'true',
				  PRIMARY KEY  (`id_report`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "INSERT INTO `learning_report` VALUES (1, 'general_report', 'Report_General', 'class.report_general.php', 'true');
				INSERT INTO `learning_report` VALUES (2, 'user_report', 'Report_User', 'class.report_user.php', 'true');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `learning_report` ADD `enabled` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_report_filter` (
							  `id_filter` int(10) unsigned NOT NULL auto_increment,
							  `id_report` int(10) unsigned NOT NULL default '0',
							  `author` int(10) unsigned NOT NULL default '0',
							  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
							  `filter_name` varchar(255) NOT NULL default '',
							  `filter_data` text NOT NULL,
							  `is_public` tinyint(1) unsigned NOT NULL default '0',
							  PRIMARY KEY  (`id_filter`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_report_schedule` (
						  `id_report_schedule` int(11) unsigned NOT NULL auto_increment,
						  `id_report_filter` int(11) unsigned NOT NULL,
						  `id_creator` int(11) unsigned NOT NULL,
						  `name` varchar(255) collate utf8_bin NOT NULL,
						  `period` varchar(255) collate utf8_bin NOT NULL,
						  `time` time NOT NULL,
						  `creation_date` datetime NOT NULL,
						  `enabled` tinyint(1) unsigned NOT NULL default '1',
						  PRIMARY KEY  (`id_report_schedule`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_report_schedule_recipient` (
						  `id_report_schedule` int(11) unsigned NOT NULL,
						  `id_user` int(11) unsigned NOT NULL,
						  PRIMARY KEY  (`id_report_schedule`,`id_user`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "TRUNCATE TABLE `learning_report`";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_report` (`id_report`, `report_name`, `class_name`, `file_name`, `use_user_selection`, `enabled`) VALUES 
							(1, 'general_report', 'Report_General', 'class.report_general.php', 'true', 0),
							(2, 'user_report', 'Report_User', 'class.report_user.php', 'true', 1),
							(3, 'competences_report', 'Report_Competences', 'class.report_competences.php', 'true', 0),
							(4, 'courses_report', 'Report_Courses', 'class.report_courses.php', 'true', 1);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
	
}

?>