<?php

class Upgrade_SettingLms extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'setting';
	
	
	function getGroup($idst, $groupid) {
		
		$query = "SELECT idst"
				." FROM core_group "
				." WHERE groupid = '".$groupid."'";

		$rs = $this->db_man->query( $query );
		if( mysql_num_rows( $rs ) > 0 ) {
			$result = mysql_fetch_row($rs);
			return $result[0];
		} else
			return FALSE;
	}
	
	function getRole($idst, $roleid) {
		
		$query = "SELECT idst, roleid, description"
				." FROM core_role ";
		if( $idst !== FALSE )
				$query .= " WHERE idst = '".$idst."'";
		elseif( $roleid !== FALSE )
			$query .= " WHERE roleid = '".$roleid."'";
		else
			return FALSE;

		$rs = $this->db_man->query( $query );
		if( mysql_num_rows( $rs ) > 0 )
			return mysql_fetch_row($rs);
		else
			return FALSE;
	}
	

	function addToRole($id_role, $idst_group) {
	
		if(($id_role == 0) || ($idst_group == 0)) return;
		$query = "INSERT INTO core_role_members "
				." (idst, idstMember) VALUES "
				." ('".$id_role."','".$idst_group."')";

		return $this->db_man->query( $query );
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
				
				$query = "ALTER TABLE `learning_setting` ADD `extra_info` TEXT NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DELETE FROM learning_setting WHERE param_name = 'pathcourse';
				DELETE FROM learning_setting WHERE param_name = 'pathmessage';
				DELETE FROM learning_setting WHERE param_name = 'pathforum';
				DELETE FROM learning_setting WHERE param_name = 'pathlesson';
				DELETE FROM learning_setting WHERE param_name = 'pathsponsor';
				DELETE FROM learning_setting WHERE param_name = 'pathscorm';
				DELETE FROM learning_setting WHERE param_name = 'pathchat';
				DELETE FROM learning_setting WHERE param_name = 'pathprj';
				DELETE FROM learning_setting WHERE param_name = 'pathuserfiles';
				DELETE FROM learning_setting WHERE param_name = 'UserFilesPath';
				DELETE FROM learning_setting WHERE param_name = 'UserFilePath';
				
				DELETE FROM learning_setting WHERE regroup = '3';
				DELETE FROM learning_setting WHERE regroup = '4';
				DELETE FROM learning_setting WHERE regroup = '5';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				UPDATE `learning_setting` SET param_value = CONCAT(param_value, 'doceboLms/') WHERE param_name = 'url';
				INSERT INTO `learning_setting` VALUES ('pathcourse', 'course/', 'string', 255, 2, 2, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathmessage', 'message/', 'string', 255, 2, 5, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathforum', 'forum/', 'string', 255, 2, 3, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathproject', 'project/', 'string', 255, 2, 3, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathlesson', 'item/', 'string', 255, 2, 4, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('visu_course', '10', 'int', 5, 1, 2, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathsponsor', 'sponsor/', 'string', 255, 2, 8, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathscorm', 'scorm/', 'string', 255, 2, 7, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('pathchat', 'chat/', 'string', 255, 2, 1, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('user_use_mygroup', '1', 'menuvoice_course_public', 1, 7, 4, 1, 0, '/public/mygroup/view');
				INSERT INTO `learning_setting` VALUES ('course_list_plan', 'on', 'enum', 3, 1, 8, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('visuNewsHomePage', '4', 'int', 5, 1, 0, 1, 1, '');
				INSERT INTO `learning_setting` VALUES ('visuItem', '10', 'int', 5, 1, 1, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('user_use_alert', '1', 'menuvoice_course_public', 1, 7, 3, 1, 0, '/public/userevent/view');
				INSERT INTO `learning_setting` VALUES ('pathprj', 'project/', 'string', 255, 2, 0, 1, 1, '');
				INSERT INTO `learning_setting` VALUES ('user_use_profile', '1', 'menuvoice_course_public', 1, 7, 5, 1, 0, '/public/profile/view;/public/profile/mod');
				INSERT INTO `learning_setting` VALUES ('pathtest', 'test/', 'string', 255, 2, 9, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('use_coursepath', '1', 'menuvoice', 1, 7, 1, 1, 0, '/coursepath/view');
				INSERT INTO `learning_setting` VALUES ('use_course_catalogue', '1', 'menuvoice', 1, 7, 2, 1, 0, '/catalogue/view');
				INSERT INTO `learning_setting` VALUES ('on_catalogue_empty', 'on', 'enum', 3, 1, 10, 1, 0, '');
				INSERT INTO `learning_setting` VALUES ('forum_as_table', 'off', 'enum', 3, 1, 6, 1, 0, '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				UPDATE `learning_setting` 
				SET param_load = 1, param_value = '3.0' 
				WHERE param_name = 'lms_version'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "INSERT INTO `learning_setting` VALUES ('user_use_coursecatalogue', '1', 'menuvoice_course_public', 1, 7, 6, 1, 0, '/public/coursecatalogue/view')";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "INSERT INTO `learning_setting` VALUES ('home_course_catalogue', 'off', 'enum', 3, 1, 10, 1, 0, '')";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "INSERT INTO `learning_setting` VALUES ('stop_concurrent_user', 'on', 'enum', 3, 3, 1, 1, 0, '')";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "ALTER TABLE `learning_setting` CHANGE `param_value` `param_value` TEXT NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('course_quota', '50', 'string', 255, 0, 5, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('first_coursecatalogue_tab', 'category', 'first_coursecatalogue_tab', 255, 5, 2, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('max_pdp_answer', '10', 'int', 6, 1, 10, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `learning_setting` VALUES ('tablist_coursecatalogue', 'a%3A7%3A%7Bs%3A4%3A%22time%22%3Bs%3A1%3A%221%22%3Bs%3A8%3A%22category%22%3Bs%3A1%3A%221%22%3Bs%3A3%3A%22all%22%3Bs%3A1%3A%221%22%3Bs%3A10%3A%22pathcourse%22%3Bs%3A1%3A%221%22%3Bs%3A9%3A%22mostscore%22%3Bs%3A1%3A%221%22%3Bs%3A7%3A%22popular%22%3Bs%3A1%3A%221%22%3Bs%3A6%3A%22recent%22%3Bs%3A1%3A%221%22%3B%7D', 'tablist_coursecatalogue', 255, 5, 1, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `learning_setting` VALUES ('user_use_eportfolio', '1', 'menuvoice_course_public', 1, 4, 5, 1, 0, '/public/eportfolio/view')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('user_use_myfiles', '1', 'menuvoice_course_public', 1, 4, 3, 1, 0, '/public/myfiles/view\r\n\r\n')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('user_use_myfriend', '1', 'menuvoice_course_public', 1, 4, 7, 1, 0, '/public/myfriends/view\r\n')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('user_use_mycertificate', '1', 'menuvoice_course_public', 1, 4, 8, 1, 0, '/public/mycertificate/view\r\n')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` VALUES ('use_social_courselist', 'off', 'enum', 3, 1, 0, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
			
				$content = "UPDATE `learning_setting` 
				SET regroup = 4 
				WHERE param_name = 'user_use_alert'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
						
				$this->end_version = '3.5.0';
				return true;
			};break;
			
			case "3.5.0" : {
				
				$i = 0;
				$content = "UPDATE learning_menu SET sequence = sequence + 1 WHERE sequence > 3 ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'course_autoregistration', 'course_autoregistration', '_COURSE_AUTOREGISTRATION', 'view', 'class.course_autoregistration.php', 'Module_Course_Autoregistration', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				$id_auto = $this->db_man->lastInsertId();
				
        
				$content = "INSERT INTO `learning_module` VALUES (NULL, 'public_forum', 'public_forum', '_PUBLIC_FORUM', 'view', 'class.public_forum.php', 'Module_Public_Forum', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				$id_publi = $this->db_man->lastInsertId();
				
				
				$content = "INSERT INTO `learning_menucourse_under` VALUES (0, $id_publi, 0, 9, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;

				$content = "INSERT INTO `learning_menucourse_under` VALUES (0, $id_auto, 0, 10, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				
				$content = "INSERT INTO `learning_menucustom_under` VALUES (0, $id_publi, 0, 9, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;

				$content = "INSERT INTO `learning_menucustom_under` VALUES (0, $id_auto, 0, 10, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				$content = "INSERT INTO `learning_menu` VALUES (10, '_MIDDLE_AREA', '', 3, 'false');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				$content = "
				INSERT INTO `learning_menu_under` VALUES (NULL, 10, 'middlearea', '_MIDDLE_AREA', 'view_area', 'view', NULL, 1, 'class.middlearea.php', 'Module_MiddleArea');
				INSERT INTO `learning_menu_under` VALUES (NULL, 10, 'internal_news', '_NEWS_INTERNAL', 'news', 'view', NULL, 2, 'class.internal_news.php', 'Module_Internal_News');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
				$content = "SELECT extra_info FROM `learning_setting` WHERE regroup = '4' AND param_value = '0'";
				$re_to_activate = $this->db_man->query($content);
        		
        		
        		while(list($extra_info) = mysql_fetch_row($re_to_activate)) {
					
					if($extra_info != '') {
						$perm = explode(';', $extra_info);
						foreach($perm as $k => $perm_suffix) {
							
							$groupid = '/oc_0';
							$roleid = '/lms/course'.trim($perm_suffix);
							
							$group 		= $this->getGroup(false, $groupid);
							$idst_group	= $group[0];
							$role 		= $this->getRole(false, $roleid);
							$id_role 	= $role[0];
							$this->addToRole($id_role, $idst_group);
						}
					}
				}
				
				$content = "DELETE FROM `learning_setting` WHERE regroup = '4'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				// intelligere old module remove ----------------------------------------------------
				
				$content = "SELECT idModule FROM `learning_module` WHERE module_name = 'intelligere'";
				list($id_module) = mysql_fetch_row($this->db_man->query($content));
        		
        		$content = "DELETE FROM `learning_module` WHERE module_name = 'intelligere'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
        		$content = "DELETE FROM `learning_menucustom_under` WHERE idModule = '".$id_module."'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
        		$content = "DELETE FROM `learning_menucourse_under` WHERE idModule = '".$id_module."'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
				// teleskill old module remove-------------------------------------------------------
				
				$content = "SELECT idModule FROM `learning_module` WHERE module_name = 'teleskill'";
				list($id_module) = mysql_fetch_row($this->db_man->query($content));
        		
        		$content = "DELETE FROM `learning_module` WHERE module_name = 'teleskill'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
        		$content = "DELETE FROM `learning_menucustom_under` WHERE idModule = '".$id_module."'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
        		$content = "DELETE FROM `learning_menucourse_under` WHERE idModule = '".$id_module."'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
        		// -------------------------------------------------------------------------------------
				
				$content = "UPDATE `learning_module`
				SET `module_name` = 'conference', 
					`default_op` = 'list', 
					`default_name` = '_CONFERENCE', 
					`token_associated` = 'view', 
					`file_name` = 'class.conference.php', 
					`class_name` = 'Module_Conference', 
					`module_info` = ''
				WHERE module_name = 'teleskill_room'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
				$content = "ALTER TABLE `learning_certificate` ADD `code` VARCHAR( 255 ) NOT NULL AFTER `id_certificate` ;";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
        		$content = "DELETE FROM `learning_setting` WHERE param_name = 'home_course_catalogue'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
        		
				$this->end_version = '3.5.0.1';
				return true;
			};break;
			case "3.5.0.2" : {
				$i = 0;
				
				$content = "INSERT INTO `learning_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` ) VALUES (
					'no_answer_in_poll', 'on', 'enum', '3', '1', '11', '1', '0', ''
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				

				$content = "INSERT INTO `learning_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` ) VALUES (
					'no_answer_in_test', 'on', 'enum', '3', '1', '12', '1', '0', ''
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "DELETE FROM learning_menu_under WHERE module_name = 'reservation' AND default_name = '_LABORATORY';";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "UPDATE learning_course SET status = status + 2";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "
				INSERT INTO `learning_quest_type_poll` ( `type_quest` , `type_file` , `type_class` , `sequence` )
				VALUES ('extended_text', 'class.extended_text.php', 'ExtendedText_Question', 3);";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "ALTER TABLE `core_user_myfiles` ADD `size` VARCHAR( 255 ) NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				$content = "UPDATE `learning_course` SET `can_subscribe` = '1'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$found = false;
				$query = "DESCRIBE learning_forumthread";
				$re_query = $this->db_man->query($query);
				while($row = mysql_fetch_row($re_query)) {
					
					$text = implode(' ', $row);
					if(strpos($text, 'id_edition') !== false) {
						$found = true;
					}
				}
				if(!$found) {
					
					$content = "ALTER TABLE `learning_forumthread` ADD `id_edition` INT( 11 ) DEFAULT '0' NOT NULL";
					if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
					$i++;
				}
				
				$this->end_version = '3.5.0.3';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "INSERT INTO `learning_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
							VALUES ('catalogue_hide_ended', 'on', 'enum', '3', '0', '8', '1', '0', '');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
							VALUES ('first_catalogue', 'off', 'enum', '3', '0', '9', '1', '0', '');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
	
}

?>