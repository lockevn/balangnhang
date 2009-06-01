<?php

class Upgrade_Menucustom extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'menucustom';
	
	
	function _getIdstGroup($groupid) {
		
		$query = "
		SELECT idst 
		FROM core_group 
		WHERE groupid = '".$groupid."'";
		$re = $this->db_man->query($query);
		if(!$re) return false;
		
		list($id) = $this->db_man->fetchRow($re);
		return $id;
	}
	
	function _registerGroup($groupid) {
		
		$query = "
		INSERT INTO core_st ( idst ) VALUES ( '' );
		INSERT INTO core_group "
		." (idst, groupid, description, hidden ) "
		."VALUES ( LAST_INSERT_ID(), '".$groupid."', '' ,'true' );";
		$this->db_man->query($query);
		
		return $this->db_man->lastInsertId();
	}
	
	function _registerRole($roleid) {
		
		$query = "
		INSERT INTO core_st ( idst ) VALUES ( '' );
		INSERT INTO core_role "
		." ( idst, roleid, description ) VALUES ( LAST_INSERT_ID(), '".$roleid."', '' );";
		$this->db_man->query($query);
		
		return $this->db_man->lastInsertId();
	}
	
	function _registerRoleMemebers($roleidst, $member) {
		
		$query = "
		INSERT INTO core_role_members "
		." ( idst, idstMember ) VALUES ( '".$roleidst."', '".$member."' );";
		$this->db_man->query($query);
		
	}
	
	/**
	 * upgrade the module version
	 * @param string:$start_version:the start version, automaticaly prooceed to the next
	 *
	 * @return mixed:true if the version jump was successful, else an array with an error code and an error message
	 *:				array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "2.0.4" : {
				
				$query = "
				CREATE TABLE `learning_module` (
				  `idModule` int(11) NOT NULL auto_increment,
				  `module_name` varchar(255) NOT NULL default '',
				  `default_op` varchar(255) NOT NULL default '',
				  `default_name` varchar(255) NOT NULL default '',
				  `token_associated` varchar(100) NOT NULL default '',
				  `file_name` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idModule`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `learning_module` VALUES (1, 'course', 'mycourses', '_MYCOURSES', 'view', 'class.course.php', 'Module_Course');
				INSERT INTO `learning_module` VALUES (2, 'coursecatalogue', 'courselist', '_CATALOGUE', 'view', 'class.coursecatalogue.php', 'Module_Coursecatalogue');
				INSERT INTO `learning_module` VALUES (3, 'profile', 'profile', '_PROFILE', 'view', 'class.profile.php', 'Module_Profile');
				INSERT INTO `learning_module` VALUES (4, 'course', 'infocourse', '_INFCOURSE', 'view_info', 'class.course.php', 'Module_Course');
				INSERT INTO `learning_module` VALUES (5, 'manmenu', 'manmenu', '_MAN_MENU', 'view', 'class.manmenu.php', 'Module_CourseManmenu');
				INSERT INTO `learning_module` VALUES (6, 'advice', 'advice', '_ADVICE', 'view', 'class.advice.php', 'Module_Advice');
				INSERT INTO `learning_module` VALUES (7, 'chat', 'chat', '_CHAT', 'view', 'class.chat.php', 'Module_Chat');
				INSERT INTO `learning_module` VALUES (8, 'groups', 'groups', '_GROUPS', 'view', 'class.groups.php', 'Module_Groups');
				INSERT INTO `learning_module` VALUES (9, 'message', 'message', '_MESSAGE', 'view', 'class.message.php', 'Module_Message');
				INSERT INTO `learning_module` VALUES (10, 'forum', 'forum', '_FORUM', 'view', 'class.forum.php', 'Module_Forum');
				INSERT INTO `learning_module` VALUES (11, 'notes', 'notes', '_NOTES', 'view', 'class.notes.php', 'Module_Notes');
				INSERT INTO `learning_module` VALUES (12, 'project', 'project', '_PROJECT', 'view', 'class.project.php', 'Module_Project');
				INSERT INTO `learning_module` VALUES (13, 'storage', 'display', '_STORAGE', 'view', 'class.storage.php', 'Module_Storage');
				INSERT INTO `learning_module` VALUES (14, 'statistic', 'statistic', '_STAT', 'view', 'class.statistic.php', 'Module_Statistic');
				INSERT INTO `learning_module` VALUES (15, 'stats', 'statuser', '_STATUSER', 'view_user', 'class.stats.php', 'Module_Stats');
				INSERT INTO `learning_module` VALUES (16, 'stats', 'statcourse', '_STATCOURSE', 'view_course', 'class.stats.php', 'Module_Stats');
				INSERT INTO `learning_module` VALUES (17, 'organization', 'organization', '_ORGANIZATION', 'view', 'class.organization.php', 'Module_Organization');
				INSERT INTO `learning_module` VALUES (18, 'mygroup', 'group', '_MYGROUP', 'view', 'class.mygroup.php', 'Module_MyGroup');
				INSERT INTO `learning_module` VALUES (19, 'coursereport', 'coursereport', '_COURSEREPORT', 'view', 'class.coursereport.php', 'Module_CourseReport');
				INSERT INTO `learning_module` VALUES (20, 'userevent', 'user_display', '_MYEVENTS', 'view', 'class.userevent.php', 'Module_UserEvent');
				INSERT INTO `learning_module` VALUES (21, 'teleskill', 'teleskill', '_TELESKILL', 'view', 'class.teleskill.php', 'Module_Teleskill');
				INSERT INTO `learning_module` VALUES (22, 'gradebook', 'showgrade', '_GRADEBOOK', 'view', 'class.gradebook.php', 'Module_Gradebook');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "ALTER TABLE learning_menucustom_category RENAME learning_menucustom_main";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "ALTER TABLE learning_menucustomunder_custom RENAME learning_menucustom_under";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "ALTER TABLE learning_menucustom 
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL DEFAULT ''";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "ALTER TABLE learning_menucustom_main 
				CHANGE `name` `name` VARCHAR( 255 ) NOT NULL DEFAULT ''";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$map_module_name = array(
					'manmenu:manmenu'				=> 'manmenu:manmenu',
					'advice:advice'					=> 'advice:advice',
					'chat:chat'						=> 'chat:chat',
					'course:unregistercourse'		=> 'course:mycourses',
					'course:infocourse'				=> 'course:infocourse',
					'statistic:statistic'			=> 'statistic:statistic',
					'stats:statuser'				=> 'stats:statuser',
					'groups:groups'					=> 'groups:groups',
					'message:message'				=> 'message:message',
					'forum:forum'					=> 'forum:forum',
					'notes:notes'					=> 'notes:notes',
					'profile:profile'				=> 'profile:profile',
					'storage:display'				=> 'storage:display',
					'homerepo:homerepo'				=> 'storage:display',
					'pubrepo:pubrepo'				=> 'storage:display',
					'organization:organization'	=> 'organization:organization',
					'stats:statcourse'				=> 'stats:statcourse',
					'freecourse:listfree'			=> 'coursecatalogue:courselist',
					'pagella:pagella'				=> 'gradebook:showgrade',
					'pagella:pag_atvt'				=> 'coursereport:coursereport',
					'project:project'				=> 'project:project'
					//'new_1'						=> 'mygroup:group',
					//'new_2'						=> 'userevent:user_display',
					//'new_3'						=> 'teleskill:teleskill' 
				);
				
				// convert into id
				$map_module_id = array();
				foreach($map_module_name as $old_m => $new_m) {
					
					$pos = strpos($new_m, ':');
					$query = "
					SELECT idModule
					FROM learning_module 
					WHERE module_name = '".substr($new_m, 0, $pos)."' AND default_op = '".substr($new_m, ($pos+1))."'";
					list($id_new) = $this->db_man->fetchRow($this->db_man->query($query)); 
					
					$pos = strpos($old_m, ':');
					$query = "
					SELECT idUnder 
					FROM learning_menucourseunder 
					WHERE module_name = '".substr($old_m, 0, $pos)."' AND case_op = '".substr($old_m, ($pos+1))."'";
					list($id_old) = $this->db_man->fetchRow($this->db_man->query($query));
					
					$map_module_id[$id_old] = $id_new;
					
					$map_old_module[$old_m] = $id_old;    
					$map_new_module[$new_m] = $id_new;
				}
				
				// now i must create all the role and group needed for 
				
				$levels = array(1, 2, 3, 4, 5, 6, 7);
				
				$query = "
				SELECT idCustom 
				FROM learning_menucustom";
				$re_customs = $this->db_man->query($query);
				while(list($id_cust) = $this->db_man->fetchRow($re_customs)) {
					
					foreach($levels as $lv_num) {
						
						$custom_groups[$id_cust.'_'.$lv_num] = $this->_registerGroup('/lms/custom/'.$id_cust.'/'.$lv_num);
					}
				}
				
				// for gradebook modify
				$query = "
				UPDATE learning_menucustom_under AS mu 
				SET perm_mod = '192'
				WHERE module_name = 'pagella' AND case_op = 'pag_atvt'";
				$this->db_man->query($query);
				
				$role_comparison = array(
					'storage:display:op' => array( '/lms/course/private/storage/view',
													'/lms/course/private/storage/public',
													'/lms/course/private/storage/lesson',
													'/lms/course/private/storage/home' ),
					'stats:statuser:op' 	=> '/lms/course/private/stats/view_user',
					'stats:statcourse:op' 	=> '/lms/course/private/stats/view_course',
					'statistic:statistic:op' => '/lms/course/private/statistic/view',
					'project:project:op' => '/lms/course/private/project/view',
					'project:project:new' => '/lms/course/private/project/add',
					'project:project:mod' => '/lms/course/private/project/mod',
					'project:project:del' => '/lms/course/private/project/del',
					'organization:organization:op' => '/lms/course/private/organization/view',
					'notes:notes:op' => '/lms/course/private/notes/view',
					'message:message:op' => '/lms/course/private/message/view',
					'message:message:mod' => '/lms/course/private/message/send_all',
					'manmenu:manmenu:op'	=> '/lms/course/private/manmenu/view',
					'manmenu:manmenu:mod'	=> '/lms/course/private/manmenu/mod',
					'groups:groups:op' => '/lms/course/private/groups/view',
					'groups:groups:mod' => array('/lms/course/private/groups/subscribe',
													'/lms/course/private/groups/mod'),
					'pagella:pagella:op' => '/lms/course/private/gradebook/view',
					'forum:forum:op' => array('/lms/course/private/forum/write',
												'/lms/course/private/forum/view'),
					'forum:forum:mod' => array('/lms/course/private/forum/upload',
											'/lms/course/private/forum/sema',
											'/lms/course/private/forum/moderate',
											'/lms/course/private/forum/mod'),
					'forum:forum:del' => '/lms/course/private/forum/del',
					'forum:forum:add' => '/lms/course/private/forum/add',
					'pagella:pag_atvt:op' => '/lms/course/private/coursereport/view',
					'pagella:pag_atvt:mod' => '/lms/course/private/coursereport/mod',
					'course:infocourse:op' => '/lms/course/private/course/view_info',
					'course:infocourse:mod' => '/lms/course/private/course/mod',
					'chat:chat:op' => '/lms/course/private/chat/view',
					'advice:advice:op' => '/lms/course/private/advice/view',
					'advice:advice:mod' => '/lms/course/private/advice/mod' );
				foreach($role_comparison as $old_role => $new_role) {
					
					if(!is_array($new_role)) $new_role = array($new_role);
					foreach($new_role as $key => $roleid) {
						$role_register[$old_role][] = $this->_registerRole($roleid);
					}
				}
				
				// convert old perm into the new one
				$query = "
				SELECT mu.idUnder, mu.idCustom, mu.perm_op, mu.perm_new, mu.perm_mod, mu.perm_rem, old_m.module_name, old_m.case_op 
				FROM learning_menucustom_under AS mu JOIN learning_menucourseunder AS old_m
				WHERE mu.idUnder = old_m.idUnder";
				$re_custom_construction = $this->db_man->query($query);
				while($data = $this->db_man->fetchArray($re_custom_construction)) {
					
					$base_m_tag = $data['module_name'].':'.$data['case_op'];
					foreach($levels as $level_num) {
						
						// if the old permission was granted
						if($data['perm_op'] & (1 << $level_num)) {
							
							// if there are new roles
							if(isset($role_register[$base_m_tag.':op'])) {
								
								foreach($role_register[$base_m_tag.':op'] as $k => $new_role_id) {
									
									$this->_registerRoleMemebers($new_role_id, $custom_groups[$data['idCustom'].'_'.$level_num]);
								}
							}
						}
						// if the old permission was granted
						if($data['perm_new'] & (1 << $level_num)) {
							
							// if there are new roles
							if(isset($role_register[$base_m_tag.':add'])) {
								
								foreach($role_register[$base_m_tag.':add'] as $k => $new_role_id) {
									
									
									$this->_registerRoleMemebers($new_role_id, $custom_groups[$data['idCustom'].'_'.$level_num]);
								}
							}
						}
						// if the old permission was granted
						if($data['perm_mod'] & (1 << $level_num)) {
							
							// if there are new roles
							if(isset($role_register[$base_m_tag.':mod'])) {
								
								foreach($role_register[$base_m_tag.':mod'] as $k => $new_role_id) {
									
									
									$this->_registerRoleMemebers($new_role_id, $custom_groups[$data['idCustom'].'_'.$level_num]);
								}
							}
						}
						// if the old permission was granted
						if($data['perm_rem'] & (1 << $level_num)) {
							
							// if there are new roles
							if(isset($role_register[$base_m_tag.':del'])) {
								
								foreach($role_register[$base_m_tag.':del'] as $k => $new_role_id) {
									
									
									$this->_registerRoleMemebers($new_role_id, $custom_groups[$data['idCustom'].'_'.$level_num]);
								}
							}
						}
					
					}
				}
				$query = "ALTER TABLE `learning_menucustom_under` DROP PRIMARY KEY ";
				$this->db_man->query($query);
				
				$query = "ALTER TABLE learning_menucustom_under 
				ADD `idModule` INT( 11 ) NOT NULL AFTER idCustom";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				// convert all id into the new
				foreach($map_module_id as $old_id => $new_id) {
				
					$query = "
					UPDATE learning_menucustom_under
					SET idModule = '".$new_id."' 
					WHERE idUnder = '".$old_id."'";
					$this->db_man->query($query);
				}
				
				// remove duplicates
				$query = "
				SELECT idCustom, idModule, COUNT(*) 
				FROM learning_menucustom_under 
				GROUP BY idCustom, idModule
				HAVING COUNT(*) > 1 ";
				$re = $this->db_man->query($query);
				while(list($idCustom, $idModule, $how_much) = $this->db_man->fetchRow($re)) {
					
					$query = "
					DELETE FROM learning_menucustom_under 
					WHERE idCustom = '".$idCustom."' AND idModule = '".$idModule."'
					LIMIT ".($how_much-1);
					$this->db_man->query($query);
				}
					
				$query = "ALTER TABLE learning_menucustom 
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL DEFAULT ''";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				$query = "ALTER TABLE learning_menucustom_main 
				CHANGE `name` `name` VARCHAR( 255 ) NOT NULL DEFAULT '',
				DROP `perm`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				
				$query = "ALTER TABLE learning_menucustom_under 
				CHANGE `my_name` `my_name` VARCHAR( 255 ) NOT NULL DEFAULT '',
				DROP `perm_op`,
				DROP `perm_new`,
				DROP `perm_mod`,
				DROP `perm_rem`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 9);
				
				$query = "ALTER TABLE learning_menucustom_under 
				DROP `idUnder`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 12);
				
				$query = "
				DELETE FROM learning_menucustom_under
				WHERE idCustom = '0' OR idModule = '0'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 11);
				
				$query = "
				INSERT INTO `learning_menucustom_under` VALUES (0, 1, 0, 0, '');
				INSERT INTO `learning_menucustom_under` VALUES (0, 3, 0, 0, '');
				INSERT INTO `learning_menucustom_under` VALUES (0, 2, 0, 0, '');
				INSERT INTO `learning_menucustom_under` VALUES (0, 18, 0, 0, '');
				INSERT INTO `learning_menucustom_under` VALUES (0, 20, 0, 0, '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 13);
				
				$query = "UPDATE `learning_menucustom_under` 
				SET my_name = ''";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 14);
				
				$query = "ALTER TABLE `learning_menucustom_under` ADD PRIMARY KEY ( `idCustom` , `idModule` )";
				$this->db_man->query($query);
				
				/*******************************************************************************
				 ********************************************************************************/
				
				// now the course 
				$levels = array(1, 2, 3, 4, 5, 6, 7);
				$query = "
				SELECT idCourse 
				FROM learning_course ";
				$re_courses = $this->db_man->query($query);
				while(list($id_course) = $this->db_man->fetchRow($re_courses)) {
					
					$course_role_register = array();
					
					// create role for this course
					$role_comparison = array(
						'storage:display:op' => array( '/lms/course/private/'.$id_course.'/storage/view',
														'/lms/course/private/'.$id_course.'/storage/public',
														'/lms/course/private/'.$id_course.'/storage/lesson',
														'/lms/course/private/'.$id_course.'/storage/home' ),
						'stats:statuser:op' 	=> '/lms/course/private/'.$id_course.'/stats/view_user',
						'stats:statcourse:op' 	=> '/lms/course/private/'.$id_course.'/stats/view_course',
						'statistic:statistic:op' => '/lms/course/private/'.$id_course.'/statistic/view',
						'project:project:op' => '/lms/course/private/'.$id_course.'/project/view',
						'project:project:new' => '/lms/course/private/'.$id_course.'/project/add',
						'project:project:mod' => '/lms/course/private/'.$id_course.'/project/mod',
						'project:project:del' => '/lms/course/private/'.$id_course.'/project/del',
						'organization:organization:op' => '/lms/course/private/'.$id_course.'/organization/view',
						'notes:notes:op' => '/lms/course/private/'.$id_course.'/notes/view',
						'message:message:op' => '/lms/course/private/'.$id_course.'/message/view',
						'message:message:add' => '/lms/course/private/'.$id_course.'/message/send_all',
						'manmenu:manmenu:op'	=> '/lms/course/private/'.$id_course.'/manmenu/view',
						'manmenu:manmenu:mod'	=> '/lms/course/private/'.$id_course.'/manmenu/mod',
						'groups:groups:op' => '/lms/course/private/'.$id_course.'/groups/view',
						'groups:groups:mod' => array('/lms/course/private/'.$id_course.'/groups/subscribe',
														'/lms/course/private/'.$id_course.'/groups/mod'),
						'pagella:pagella:op' => '/lms/course/private/'.$id_course.'/gradebook/view',
						'forum:forum:op' => array('/lms/course/private/'.$id_course.'/forum/write',
													'/lms/course/private/'.$id_course.'/forum/view'),
						'forum:forum:mod' => array('/lms/course/private/'.$id_course.'/forum/upload',
												'/lms/course/private/'.$id_course.'/forum/sema',
												'/lms/course/private/'.$id_course.'/forum/moderate',
												'/lms/course/private/'.$id_course.'/forum/mod'),
						'forum:forum:del' => '/lms/course/private/'.$id_course.'/forum/del',
						'forum:forum:add' => '/lms/course/private/'.$id_course.'/forum/add',
						'pagella:pag_atvt:op' => '/lms/course/private/'.$id_course.'/coursereport/view',
						'pagella:pag_atvt:mod' => '/lms/course/private/'.$id_course.'/coursereport/mod',
						'course:infocourse:op' => '/lms/course/private/'.$id_course.'/course/view_info',
						'course:infocourse:mod' => '/lms/course/private/'.$id_course.'/course/mod',
						'chat:chat:op' => '/lms/course/private/'.$id_course.'/chat/view',
						'advice:advice:op' => '/lms/course/private/'.$id_course.'/advice/view',
						'advice:advice:mod' => '/lms/course/private/'.$id_course.'/advice/mod' );
					foreach($role_comparison as $old_role => $new_role) {
						
						if(!is_array($new_role)) $new_role = array($new_role);
						foreach($new_role as $key => $roleid) {
							$course_role_register[$old_role][] = $this->_registerRole($roleid);
						}
					}
					
					// find group for levels
					$course_group = array();
					$levels = array(1, 2, 3, 4, 5, 6, 7);
					foreach($levels as $k => $lv) {
						
						$course_group[$lv] = $this->_getIdstGroup('/lms/course/'.$id_course.'/subscribed/'.$lv);
					}
					// for gradebook modify
					$query = "
					UPDATE learning_menucourseunder_custom 
					SET perm_mod = '192'
					WHERE idUnder = '".$map_old_module['pagella:pag_atvt']."'";
					$this->db_man->query($query);
					
					$query = "
					SELECT mu.idUnder, mu.idCourse, mu.perm_op, mu.perm_new, mu.perm_mod, mu.perm_rem, old_m.module_name, old_m.case_op 
					FROM learning_menucourseunder_custom AS mu JOIN learning_menucourseunder AS old_m
					WHERE mu.idUnder = old_m.idUnder AND mu.idCourse = '".$id_course."'";
					$re_course_construction = $this->db_man->query($query);
					while($data = $this->db_man->fetchArray($re_course_construction)) {
						
						$base_m_tag = $data['module_name'].':'.$data['case_op'];
						foreach($levels as $level_num) {
							
							// if the old permission was granted
							if($data['perm_op'] & (1 << $level_num)) {
								
								// if there are new roles
								if(isset($course_role_register[$base_m_tag.':op'])) {
									
									foreach($course_role_register[$base_m_tag.':op'] as $k => $new_role_id) {
										
										$this->_registerRoleMemebers($new_role_id, $course_group[$level_num]);
									}
								}
							}
							// if the old permission was granted
							if($data['perm_new'] & (1 << $level_num)) {
								
								// if there are new roles
								if(isset($course_role_register[$base_m_tag.':add'])) {
									
									foreach($course_role_register[$base_m_tag.':add'] as $k => $new_role_id) {
										
										
										$this->_registerRoleMemebers($new_role_id, $course_group[$level_num]);
									}
								}
							}
							// if the old permission was granted
							if($data['perm_mod'] & (1 << $level_num)) {
								
								// if there are new roles
								if(isset($course_role_register[$base_m_tag.':mod'])) {
									
									foreach($course_role_register[$base_m_tag.':mod'] as $k => $new_role_id) {
										
										
										$this->_registerRoleMemebers($new_role_id, $course_group[$level_num]);
									}
								}
							}
							// if the old permission was granted
							if($data['perm_rem'] & (1 << $level_num)) {
								
								// if there are new roles
								if(isset($course_role_register[$base_m_tag.':del'])) {
									
									foreach($course_role_register[$base_m_tag.':del'] as $k => $new_role_id) {
										
										
										$this->_registerRoleMemebers($new_role_id, $course_group[$level_num]);
									}
								}
							}
						
						}
					}
					
				}
				
				/*******************************************************************************
				 ********************************************************************************/
				
				$query = "
				DROP TABLE learning_menucourseunder";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 14);
				
				$query = "ALTER TABLE learning_menucourseunder_custom RENAME learning_menucourse_under";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 15);
				
				$query = "ALTER TABLE `learning_menucourse_under` DROP PRIMARY KEY ";
				$this->db_man->query($query);
				
				$query = "ALTER TABLE learning_menucourse_under 
				ADD `idModule` INT( 11 ) NOT NULL AFTER idCourse";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 16);
				
				// convert all id into the new
				foreach($map_module_id as $old_id => $new_id) {
				
					$query = "
					UPDATE learning_menucourse_under
					SET idModule = '".$new_id."' 
					WHERE idUnder = '".$old_id."'";
					$this->db_man->query($query);
				}
				
				// remove duplicates
				$query = "
				SELECT idCourse, idModule, COUNT(*) 
				FROM learning_menucourse_under 
				GROUP BY idCourse, idModule
				HAVING COUNT(*) > 1 ";
				$re = $this->db_man->query($query);
				while(list($idCourse, $idModule, $how_much) = $this->db_man->fetchRow($re)) {
					
					$query = "
					DELETE FROM learning_menucourse_under 
					WHERE idCourse = '".$idCourse."' AND idModule = '".$idModule."'
					LIMIT ".($how_much-1);
					$this->db_man->query($query);
				}
				
				$query = "ALTER TABLE learning_menucourse_under 
				CHANGE `my_name` `my_name` VARCHAR( 255 ) NOT NULL DEFAULT '',
				DROP `perm_op`,
				DROP `perm_new`,
				DROP `perm_mod`,
				DROP `perm_rem`, 
				DROP `idUnder`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 17);
				
				$query = "UPDATE `learning_menucourse_under` 
				SET my_name = ''";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 18);
				
				$query = "
				DELETE FROM learning_menucourse_under
				WHERE idCourse = '0' OR idModule = '0'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 19);
				
				$query = "ALTER TABLE `learning_menucourse_under` ADD PRIMARY KEY ( `idCourse` , `idModule` )";
				$this->db_man->query($query);
				
				
				$query = "ALTER TABLE learning_menucourse_category RENAME learning_menucourse_main";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 20);
				
				$query = "ALTER TABLE learning_menucourse_main 
				DROP `perm`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 21);
				
				$query = "DELETE FROM `learning_menucourse_main` WHERE idMain = 1";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 22);
				
				$query = "INSERT INTO `learning_menucourse_main` VALUES (1, 0, 0, '_MENUGEN', 'general.gif');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 23);
				
				$idMain = $this->db_man->lastInsertId();
				
				$query = "INSERT INTO `learning_menucourse_under` VALUES (0, 1, 1, 1, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, 2, 1, 2, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, 3, 1, 3, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, 18, 1, 5, '');
				INSERT INTO `learning_menucourse_under` VALUES (0, 20, 1, 4, '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 24);
				
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
	
}

?>