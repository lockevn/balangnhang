<?php

class Upgrade_Course extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'course';
	
	function _registerGroup($groupid, $descr = '') {
		
		$query = "
		INSERT INTO core_st ( idst ) VALUES ( '' );
		INSERT INTO core_group "
		." (idst, groupid, description, hidden ) VALUES ( LAST_INSERT_ID(), '".$groupid."', '".$descr."' ,'true' );";
		$this->db_man->query($query);
				
		return $this->db_man->lastInsertId();
	}
	
	function _registerGroupMembers($id_user, $id_group) {
		
		$query = "INSERT INTO core_group_members "
					."( idst, idstMember ) "
					."VALUES ( '".$id_group."','".$id_user."' )";
		$this->db_man->query($query);
	}
	
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
				
				$query = "CREATE TABLE `core_admin_course` (
				  `idst_user` int(11) NOT NULL default '0',
				  `type_of_entry` varchar(50) NOT NULL default '',
				  `id_entry` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idst_user`,`type_of_entry`,`id_entry`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				ALTER TABLE `learning_course` 
				CHANGE `name` `name` VARCHAR( 255 ) NOT NULL ,
				CHANGE `language` `lang_code` VARCHAR( 100 ) NOT NULL ,
				CHANGE `difficult` `difficult` ENUM( 'veryeasy', 'easy', 'medium', 'difficult', 'verydifficult' ) NOT NULL DEFAULT 'medium',
				CHANGE `canModifyPolicy` `show_extra_info` TINYINT( 1 ) NOT NULL DEFAULT '0',
				CHANGE `showProgress` `show_progress` TINYINT( 1 ) NOT NULL DEFAULT '1',
				CHANGE `hideCourse` `show_rules` TINYINT( 1 ) NOT NULL DEFAULT '0',
				CHANGE `prize` `prize` VARCHAR( 255 ) NOT NULL DEFAULT '',
				CHANGE `autosubscribe` `subscribe_method` TINYINT( 1 ) NOT NULL DEFAULT '0';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				ALTER TABLE `learning_course` 
				ADD `level_show_user` INT( 11 ) DEFAULT '0' NOT NULL AFTER `status` ,
				ADD `show_time` TINYINT( 1 ) NOT NULL AFTER `show_progress` ,
				ADD `img_course` VARCHAR( 255 ) NOT NULL AFTER `imgSponsor`,
				ADD `date_begin` DATE NOT NULL AFTER `show_rules` ,
				ADD `date_end` DATE NOT NULL AFTER `date_begin` ,
				ADD `valid_time` INT( 10 ) NOT NULL AFTER `date_end` ,
				ADD `max_num_subscribe` INT( 11 ) NOT NULL AFTER `valid_time` ,
				ADD `max_sms_budget` DOUBLE NOT NULL AFTER `max_num_subscribe` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				ALTER TABLE `learning_course` 
				DROP `levelUploadForum`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "ALTER TABLE `learning_coursefile` RENAME `learning_course_file` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "ALTER TABLE `learning_course_file` 
				CHANGE `idFile` `id_file` INT( 11 ) NOT NULL AUTO_INCREMENT,
				CHANGE `idCourse` `id_course` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$query = "ALTER TABLE `learning_courseuser` 
				ADD `waiting` TINYINT( 1 ) NOT NULL ,
				ADD `subscribed_by` INT( 11 ) NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				$query = "ALTER TABLE `learning_courseuser` DROP `onair`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				
				$query = "
				UPDATE `learning_courseuser` 
				SET level = 7 
				WHERE level = 8";
				$this->db_man->query($query);
				
				// invert user level 4 and 5
				
				$user_mentor = array();
				$query = "
				SELECT idUser, idCourse 
				FROM  `learning_courseuser` 
				WHERE level = 5";
				$re_mentor = $this->db_man->query($query);
				while(list($id_user, $id_c) = $this->db_man->fetchRow($re_mentor)) {
					
					$user_mentor[] = array('user' => $id_user, 'course' => $id_c);
				}
				
				$query = "
				UPDATE `learning_courseuser` 
				SET level = 5 
				WHERE level = 4";
				$this->db_man->query($query);
				reset($user_mentor);
				while(list(, $value) = each($user_mentor)) {
					
					$query = "
					UPDATE `learning_courseuser` 
					SET level = 4 
					WHERE idUser = '".$value['user']."' AND idCourse = '".$value['course']."'";
					$this->db_man->query($query);
				}
				
				// create all the group needed and subscribe user
				
				$levels = array(1, 2, 3, 4, 5, 6, 7);
				$query_course = "
				SELECT idCourse 
				FROM learning_course ";
				$re_courses = $this->db_man->query($query_course);
				while(list($id_course) = $this->db_man->fetchRow($re_courses)) {
					
					// all user group
					$id_all = $this->_registerGroup('/lms/course/'.$id_course.'/group/alluser', 'all the user of a course');
					foreach($levels as $lv_num) {
						
						$group_courselv[$id_course.'_'.$lv_num] = $this->_registerGroup('/lms/course/'.$id_course.'/subscribed/'.$lv_num);
						$this->_registerGroupMembers($group_courselv[$id_course.'_'.$lv_num], $id_all);
					}
				}
				$query = "
				SELECT idCourse, idUser, level 
				FROM learning_courseuser 
				WHERE idCourse <> 0";
				$re_courses = $this->db_man->query($query);
				while(list($id_course, $id_user, $level) = $this->db_man->fetchRow($re_courses)) {
					if($level == 8) $level = 7;
					$this->_registerGroupMembers($id_user, $group_courselv[$id_course.'_'.$level]);
				}
				
				
				$query = "UPDATE learning_course SET difficult = 'medium'";
				$this->db_man->query($query);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "INSERT INTO `core_event_class` (`idClass`, `class`, `platform`, `description`) VALUES ('', 'UserCourseBuy', 'lms', '_EVENT_USERCOURSEBUY');
        			INSERT INTO `core_event_consumer_class` ( `idConsumer` , `idClass` )
						VALUES (
						'1', LAST_INSERT_ID()
						);
					INSERT INTO `core_event_manager` ( `idEventMgr` , `idClass` , `permission` , `channel` , `recipients` , `show_level` )
						VALUES (
						'', LAST_INSERT_ID(), 'mandatory', 'email', 'user', 'godadmin,admin,user'
						);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "
				ALTER TABLE `learning_course` ADD `img_material` VARCHAR( 255 ) NOT NULL AFTER `img_course` ,
				ADD `img_othermaterial` VARCHAR( 255 ) NOT NULL AFTER `img_material` ,
				ADD `course_demo` VARCHAR( 255 ) NOT NULL AFTER `img_othermaterial` ;";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_course` ADD `advance` VARCHAR( 255 ) NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_course` ADD `min_num_subscribe` INT( 11 ) NOT NULL AFTER `max_num_subscribe`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_course` ADD `hour_begin` VARCHAR( 5 ) NOT NULL AFTER `date_end` ,
				ADD `hour_end` VARCHAR( 5 ) NOT NULL AFTER `hour_begin` ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_course` ADD `course_type` VARCHAR( 255 ) DEFAULT 'elearning' NOT NULL AFTER `prize` ,
				ADD `policy_point` VARCHAR( 255 ) NOT NULL AFTER `course_type` ,
				ADD `point_to_all` INT( 10 ) NOT NULL AFTER `policy_point` ,
				ADD `course_edition` TINYINT( 1 ) NOT NULL AFTER `point_to_all` ,
				ADD `classrooms` VARCHAR( 255 ) NOT NULL AFTER `course_edition` ,
				ADD `certificates` VARCHAR( 255 ) NOT NULL AFTER `classrooms` ,
				ADD `create_date` DATETIME NOT NULL AFTER `certificates` ,
				ADD `security_code` VARCHAR( 255 ) NOT NULL AFTER `create_date` ,
				ADD `imported_from_connection` VARCHAR( 255 ) DEFAULT NULL AFTER `security_code` ,
				ADD `course_quota` VARCHAR( 255 ) DEFAULT '-1' NOT NULL AFTER `imported_from_connection` ,
				ADD `used_space` VARCHAR( 255 ) NOT NULL DEFAULT 0 AFTER `course_quota` ,
				ADD `course_vote` DOUBLE NOT NULL AFTER `used_space` ,
				ADD `allow_overbooking` TINYINT( 1 ) NOT NULL AFTER `course_vote` ,
				ADD `can_subscribe` TINYINT( 1 ) NOT NULL AFTER `allow_overbooking` ,
				ADD `sub_start_date` DATETIME DEFAULT NULL AFTER `can_subscribe` ,
				ADD `sub_end_date` DATETIME DEFAULT NULL AFTER `sub_start_date`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_course_point` (
				  `idCourse` int(11) NOT NULL default '0',
				  `idField` int(11) NOT NULL default '0',
				  `point` int(11) NOT NULL default '0'
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0" : {
				$i = 0;
				$content = "ALTER TABLE `learning_course` ADD `show_who_online` TINYINT( 1 ) NOT NULL ,
					ADD `direct_play` TINYINT( 1 ) NOT NULL ,
					ADD `autoregistration_code` VARCHAR( 255 ) NOT NULL ,
					ADD `use_logo_in_courselist` TINYINT(1) NOT NULL ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `learning_instmsg` (
				  `id_msg` bigint(20) NOT NULL auto_increment,
				  `id_sender` int(11) NOT NULL default '0',
				  `id_receiver` int(11) NOT NULL default '0',
				  `msg` text,
				  `status` smallint(2) NOT NULL default '0',
				  `data` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_msg`),
				  KEY `id_sender` (`id_sender`,`id_receiver`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;				
					
				$content = "CREATE TABLE `learning_middlearea` (
				  `obj_index` varchar(255) NOT NULL default '',
				  `disabled` tinyint(1) NOT NULL default '0',
				  `idst_list` text NOT NULL,
				  PRIMARY KEY  (`obj_index`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "
				INSERT INTO `learning_middlearea` VALUES ('mo_4', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('mo_6', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('mo_8', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('mo_9', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('course_autoregistration', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('user_details_full', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('search_form', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('career', 1, 'a:0:{}');
				INSERT INTO `learning_middlearea` VALUES ('news', 1, 'a:0:{}') ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `learning_news_internal` (
				  `idNews` int(11) NOT NULL auto_increment,
				  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `title` varchar(100) NOT NULL default '',
				  `short_desc` text NOT NULL,
				  `long_desc` text NOT NULL,
				  `language` varchar(100) NOT NULL default '',
				  `important` tinyint(1) NOT NULL default '0',
				  `viewer` longtext NOT NULL,
				  PRIMARY KEY  (`idNews`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;	
				
				$content = "ALTER TABLE `learning_certificate` ADD `orientation` ENUM( 'P', 'L' ) NOT NULL DEFAULT 'P',
				ADD `bgimage` VARCHAR( 255 ) NOT NULL ;";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;	
				
				$this->end_version = '3.5.0.1';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `learning_course` ADD `show_result` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
							
				$content = "ALTER TABLE `learning_commontrack` ADD `firstAttempt` datetime NOT NULL default '0000-00-00 00:00:00';";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>