<?php

class Upgrade_LmsMenu extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'menu';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			
			case "3.0.2" : {
				
				$query = "DROP TABLE `learning_menu`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DROP TABLE `learning_menu_under`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `learning_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `learning_menu` VALUES (1, '_MANAGEMENT_COURSE', '', 1, 'false');
				INSERT INTO `learning_menu` VALUES (2, '_EXTERNAL_CONTENT', '', 2, 'false');
				INSERT INTO `learning_menu` VALUES (3, '', '', 3, 'true');
				INSERT INTO `learning_menu` VALUES (4, '', '', 4, 'true');
				INSERT INTO `learning_menu` VALUES (5, '', '', 5, 'true');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `learning_menu_under` (
				  `idUnder` int(11) NOT NULL auto_increment,
				  `idMenu` int(11) NOT NULL default '0',
				  `module_name` varchar(255) NOT NULL default '',
				  `default_name` varchar(255) NOT NULL default '',
				  `default_op` varchar(255) NOT NULL default '',
				  `associated_token` varchar(255) NOT NULL default '',
				  `of_platform` varchar(255) default NULL,
				  `sequence` int(3) NOT NULL default '0',
				  `class_file` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idUnder`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `learning_menu_under` VALUES (1, 1, 'course', '_COURSE', 'course_list', 'view', NULL, 1, 'class.course.php', 'Module_Course');
				INSERT INTO `learning_menu_under` VALUES (2, 1, 'manmenu', '_MAN_MENU', 'mancustom', 'view', NULL, 2, 'class.manmenu.php', 'Module_Manmenu');
				INSERT INTO `learning_menu_under` VALUES (4, 1, 'coursepath', '_COURSEPATH', 'pathlist', 'view', NULL, 3, 'class.coursepath.php', 'Module_Coursepath');
				INSERT INTO `learning_menu_under` VALUES (5, 1, 'catalogue', '_CATALOGUE', 'catlist', 'view', NULL, 4, 'class.catalogue.php', 'Module_Catalogue');
				INSERT INTO `learning_menu_under` VALUES (6, 2, 'news', '_NEWS', 'news', 'view', NULL, 2, 'class.news.php', 'Module_News');
				INSERT INTO `learning_menu_under` VALUES (7, 2, 'webpages', '_WEBPAGES', 'webpages', 'view', NULL, 1, 'class.webpages.php', 'Module_Webpages');
				INSERT INTO `learning_menu_under` VALUES (8, 4, 'report', '_REPORT', 'reportlist', 'view', NULL, 1, 'class.report.php', 'Module_Report');
				INSERT INTO `learning_menu_under` VALUES (9, 3, 'questcategory', '_QUESTCATEGORY', 'questcategory', 'view', NULL, 1, 'class.questcategory.php', 'Module_Questcategory');
				";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "ALTER TABLE `learning_module` ADD `module_info` TEXT NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menu` VALUES (NULL, '_CLASSROOMS', '', 4, 'false')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_class = $this->db_man->lastInsertId();
				
				$content = "INSERT INTO `learning_menu` VALUES (NULL, '_MAN_CERTIFICATE', '', 3, 'false')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_cert = $this->db_man->lastInsertId();
				
				$content = "INSERT INTO `learning_menu` VALUES (NULL, '_LMS_STATS', '', 9, 'false')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_menu = $this->db_man->lastInsertId();
				
				$content = "
				INSERT INTO `learning_menu_under` VALUES (14, $id_cert, 'certificate', '_CERTIFICATE', 'certificate', 'view', NULL, 1, 'class.certificate.php', 'Module_Certificate');
				INSERT INTO `learning_menu_under` VALUES (15, $id_cert, 'certificate', '_REPORT_CERTIFICATE', 'report_certificate', 'view', NULL, 2, 'class.certificate.php', 'Module_Certificate');
				INSERT INTO `learning_menu_under` VALUES (11, $id_class, 'classevent', '_CLASSEVENT', 'main', 'view', NULL, 3, 'class.classevent.php', 'Module_Classevent');
				INSERT INTO `learning_menu_under` VALUES (12, $id_class, 'classlocation', '_CLASSLOCATION', 'main', 'view', NULL, 2, 'class.classlocation.php', 'Module_Classlocation');
				INSERT INTO `learning_menu_under` VALUES (13, $id_class, 'classroom', '_CLASSROOM', 'classroom', 'view', NULL, 3, 'class.classroom.php', 'Module_Classroom');
				INSERT INTO `learning_menu_under` VALUES (8, 4, 'eportfolio', '_EPORTFOLIO', 'eportfolio', 'view', NULL, 1, 'class.eportfolio.php', 'Module_Eportfolio');
				INSERT INTO `learning_menu_under` VALUES (10, 1, 'preassessment', '_PREASSESSMENT', 'assesmentlist', 'view', NULL, 5, 'class.preassessment.php', 'Module_PreAssessment');
				INSERT INTO `learning_menu_under` VALUES (16, $id_menu, 'stats', '_LMS_STATS', 'fake', 'view', NULL, 0, 'class.stats.php', 'Module_Stats');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'calendar', 'calendar', '_CALENDAR', 'view', 'class.calendar.php', 'Module_Calendar', '');
			
				INSERT INTO `learning_module` VALUES (NULL, 'htmlfront', 'showhtml', '_HTMLFRONT', 'view', 'class.htmlfront.php', 'Module_Htmlfront', '');
				INSERT INTO `learning_module` VALUES (NULL, 'intelligere', 'intelligere', '_INTELLIGERE', 'view', 'class.intelligere.php', 'Module_Intelligere', '');
				INSERT INTO `learning_module` VALUES (NULL, 'light_repo', 'repolist', '_LIGHT_REPO', 'view', 'class.light_repo.php', 'Module_LightRepo', '');
				INSERT INTO `learning_module` VALUES (NULL, 'wiki', 'main', '_WIKI', 'view', 'class.wiki.php', 'Module_Wiki', '');
				INSERT INTO `learning_module` VALUES (NULL, 'newsletter', 'view', '_NEWSLETTER', 'view', 'class.newsletter.php', 'Module_Newsletter', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
        
				$content = "DELETE FROM learning_module WHERE module_name = 'event'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DELETE FROM learning_module WHERE module_name = 'message'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DELETE FROM learning_module WHERE module_name = 'pagella'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `learning_module` SET `module_name` = 'teleskill_room',
					`default_name` = '_TELESKILL_ROOM',
					`class_name` = 'Module_TeleskillRoom' 
					WHERE `module_name` = 'teleskill'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `learning_module` SET module_info = 'type=user' WHERE module_name='mygroup'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `learning_module` SET module_info = 'type=user' WHERE module_name='profile'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `learning_module` SET module_info = 'type=user' WHERE module_name='userevent'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `learning_module` SET module_info = 'type=user' WHERE module_name='userevent'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'eportfolio', 'eportfolio', '_EPORTFOLIO', 'view', 'class.eportfolio.php', 'Module_EPortfolio', 'type=user')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_eport = $this->db_man->lastInsertId();
				$content = "INSERT INTO `learning_menucustom_under` VALUES (0, $id_eport, 0, 1, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, $id_eport, 1, 6, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'mycertificate', 'mycertificate', '_MY_CERTIFICATE', 'view', 'class.mycertificate.php', 'Module_MyCertificate', 'type=user');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_mycert = $this->db_man->lastInsertId();
				$content = "INSERT INTO `learning_menucustom_under` VALUES (0, $id_mycert, 0, 1, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, $id_mycert, 1, 7, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'myfiles', 'myfiles', '_MYFILES', 'view', 'class.myfiles.php', 'Module_MyFiles', 'type=user');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_myfiles = $this->db_man->lastInsertId();
				$content = "INSERT INTO `learning_menucustom_under` VALUES (0, $id_myfiles, 0, 1, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, $id_myfiles, 1, 8, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'myfriends', 'myfriends', '_MYFRIENDS', 'view', 'class.myfriends.php', 'Module_MyFriends', 'type=user');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_myfriend = $this->db_man->lastInsertId();
				$content = "INSERT INTO `learning_menucustom_under` VALUES (0, $id_myfriend, 0, 1, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, $id_myfriend, 1, 9, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				
				
				$content = "INSERT INTO `learning_module` ( `module_name` , `default_op` , `default_name` , `token_associated` , `file_name` , `class_name` , `module_info` )
					VALUES ('reservation', 'reservation', '_RESERVATION', 'view', 'class.reservation.php', 'Module_Reservation', '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menu` ( `idMenu` , `name` , `image` , `sequence` , `collapse` )
				VALUES (NULL, '_MANAGEMENT_RESERVATION', '', '8', 'false')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$id_reservation = $this->db_man->lastInsertId();

				$content = "INSERT INTO `learning_menu_under` ( `idUnder` , `idMenu` , `module_name` , `default_name` , `default_op` , `associated_token` , `of_platform` , `sequence` , `class_file` , `class_name` )
				VALUES (NULL, '$id_reservation', 'reservation', '_EVENTS', 'view_event', 'view', NULL , '1', 'class.reservation.php', 'Module_Reservation')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `learning_menu_under` ( `idUnder` , `idMenu` , `module_name` , `default_name` , `default_op` , `associated_token` , `of_platform` , `sequence` , `class_file` , `class_name` )
				VALUES (NULL, '$id_reservation', 'reservation', '_CATEGORY', 'view_category', 'view', NULL , '2', 'class.reservation.php', 'Module_Reservation')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `learning_menu_under` ( `idUnder` , `idMenu` , `module_name` , `default_name` , `default_op` , `associated_token` , `of_platform` , `sequence` , `class_file` , `class_name` )
				VALUES (NULL, '$id_reservation', 'reservation', '_LABORATORY', 'view_laboratory', 'view', NULL , '3', 'class.reservation.php', 'Module_Reservation')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `learning_menu_under` ( `idUnder` , `idMenu` , `module_name` , `default_name` , `default_op` , `associated_token` , `of_platform` , `sequence` , `class_file` , `class_name` )
				VALUES (NULL, '$id_reservation', 'reservation', '_RESERVATION', 'view_registration', 'view', NULL , '4', 'class.reservation.php', 'Module_Reservation')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0.4": {
				$i = 0;
				
				$content = "INSERT INTO learning_module (idModule, module_name, default_op, default_name, token_associated, file_name, class_name, module_info)
							VALUES ('', 'mycompetences', 'mycompetences', '_MYCOMPETENCES', 'view', 'class.mycompetences.php', 'Module_MyCompetences', 'type=user');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menucourse_under` ( `idCourse` , `idModule` , `idMain` , `sequence` , `my_name` )
							VALUES ('0', LAST_INSERT_ID(), '0', '12', '');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO learning_menu_under (idUnder, idMenu, module_name, default_name, default_op, associated_token, of_platform, sequence, class_file, class_name)
							VALUES ('', 1, 'competences', '_COMPETENCES', 'main', 'view', NULL, 5, 'class.competences.php', 'Module_Competences');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_module` ( `idModule` , `module_name` , `default_op` , `default_name` , `token_associated` , `file_name` , `class_name` , `module_info` )
							VALUES (
							NULL, 'public_user_admin', 'public_user_admin', '_PUBLIC_USER_ADMIN', 'view_org_chart', 'class.public_user_admin.php', 'Module_Public_User_Admin', 'type=public_admin'
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menucourse_under` ( `idCourse` , `idModule` , `idMain` , `sequence` , `my_name` )
							VALUES (
							'0', LAST_INSERT_ID(), '1', '1', ''
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_module` ( `idModule` , `module_name` , `default_op` , `default_name` , `token_associated` , `file_name` , `class_name` , `module_info` )
							VALUES (
							NULL, 'public_course_admin', 'public_course_admin', '_PUBLIC_COURSE_ADMIN', 'view', 'class.public_course_admin.php', 'Module_Public_Course_Admin', 'type=public_admin'
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menucourse_under` ( `idCourse` , `idModule` , `idMain` , `sequence` , `my_name` )
							VALUES (
							'0', LAST_INSERT_ID(), '1', '1', ''
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_module` ( `idModule` , `module_name` , `default_op` , `default_name` , `token_associated` , `file_name` , `class_name` , `module_info` )
							VALUES (
							NULL, 'public_subscribe_admin', 'public_subscribe_admin', '_PUBLIC_SUBSCRIBE_ADMIN', 'view', 'class.public_subscribe_admin.php', 'Module_Public_Subscribe_Admin', 'type=public_admin'
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menucourse_under` ( `idCourse` , `idModule` , `idMain` , `sequence` , `my_name` )
							VALUES (
							'0', LAST_INSERT_ID(), '1', '1', ''
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_module` ( `idModule` , `module_name` , `default_op` , `default_name` , `token_associated` , `file_name` , `class_name` , `module_info` )
							VALUES (
							NULL, 'public_report_admin', 'reportlist', '_PUBLIC_REPORT_ADMIN', 'view', 'class.public_report_admin.php', 'Module_Public_Report_Admin', 'type=public_admin'
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menucourse_under` ( `idCourse` , `idModule` , `idMain` , `sequence` , `my_name` )
							VALUES (
							'0', LAST_INSERT_ID(), '1', '1', ''
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_module` ( `idModule` , `module_name` , `default_op` , `default_name` , `token_associated` , `file_name` , `class_name` , `module_info` )
							VALUES (
							NULL, 'public_newsletter_admin', 'newsletter', '_NEWSLETTER', 'view', 'class.public_newsletter_admin.php', 'Module_Public_Newsletter_Admin', 'type=public_admin'
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menucourse_under` ( `idCourse` , `idModule` , `idMain` , `sequence` , `my_name` )
							VALUES (
							'0', LAST_INSERT_ID(), '1', '1', ''
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			};break;
			case "3.6.0":{
				
				$content = "INSERT INTO `learning_module` (`idModule`, `module_name`, `default_op`, `default_name`, `token_associated`, `file_name`, `class_name`, `module_info`) 
				VALUES
				(NULL, 'quest_bank', 'main', '_QUEST_BANK', 'view', 'class.quest_bank.php', 'Module_QuestBank', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DELETE FROM learning_menu_under WHERE module_name = 'stats';";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DELETE FROM learning_menu WHERE name = '_LMS_STATS';";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0.1';
				return true;
			};break;
		}
		return true;
	}
}

?>