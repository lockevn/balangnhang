<?php

class Upgrade_Adminmenu extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'adminmenu';
	
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
				
				$query = "CREATE TABLE `learning_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idMenu`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `learning_menu_under` (
				  `idUnder` int(11) NOT NULL auto_increment,
				  `idMenu` int(11) NOT NULL default '0',
				  `module_name` varchar(255) NOT NULL default '',
				  `default_name` varchar(255) NOT NULL default '',
				  `default_op` varchar(255) NOT NULL default '',
				  `associated_token` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `class_file` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idUnder`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "INSERT INTO `learning_menu` VALUES (1, '_MANAGEMENT_COURSE', 'managment_course.gif', 1);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `learning_menu_under` VALUES (1, 1, 'course', '_COURSE', 'course_list', 'view', 1, 'class.course.php', 'Module_Course');
				INSERT INTO `learning_menu_under` VALUES (2, 1, 'manmenu', '_MAN_MENU', 'mancustom', 'view', 4, 'class.manmenu.php', 'Module_Manmenu');
				INSERT INTO `learning_menu_under` VALUES (4, 1, 'coursepath', '_COURSEPATH', 'pathlist', 'view', 2, 'class.coursepath.php', 'Module_Coursepath');
				INSERT INTO `learning_menu_under` VALUES (5, 1, 'catalogue', '_CATALOGUE', 'catlist', 'view', 3, 'class.catalogue.php', 'Module_Catalogue');
				INSERT INTO `learning_menu_under` VALUES (6, 1, 'news', '_NEWS', 'news', 'view', 6, 'class.news.php', 'Module_News');
				INSERT INTO `learning_menu_under` VALUES (7, 1, 'webpages', '_WEBPAGES', 'webpages', 'view', 7, 'class.webpages.php', 'Module_Webpages');
				INSERT INTO `learning_menu_under` VALUES (8, 1, 'report', '_REPORT', 'reportlist', 'view', 8, 'class.report.php', 'Module_Report');
				INSERT INTO `learning_menu_under` VALUES (9, 1, 'questcategory', '_QUESTCATEGORY', 'questcategory', 'view', 5, 'class.questcategory.php', 'Module_Questcategory');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>