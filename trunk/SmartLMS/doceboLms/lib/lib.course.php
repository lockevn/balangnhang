<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package DoceboLms
 * @subpackage Course managment
 * @author Fabio Pirovano <fabio [at] docebo-com>
 * @version  $Id: lib.course.php 1002 2007-03-24 11:55:51Z fabio $
 */

define("CST_PREPARATION", 	0);
define("CST_AVAILABLE", 	1);
define("CST_EFFECTIVE", 	2);
define("CST_CONCLUDED", 	3);
define("CST_CANCELLED", 	4);

// user course subscription

define("_CUS_CANCELLED",	-4);	// - > cancellato
define("_CUS_RESERVED",		-3);	// -> Prenotato, non confermato dal BUYER, ma in lista effettiva
define("_CUS_WAITING_LIST",	-2);	// -> In lista di attesa, non confermato dal buyer e in overbooking
define("_CUS_CONFIRMED",	-1);	// -> Confermato, confermato dal buyer ( non overbooking, il buyer nn può approvare corsi per cui l'utente è in overbooking )

define("_CUS_SUBSCRIBED", 	0);		// -> Invitato, la transazione è stata processato e l'utente è iscritto al corso
define("_CUS_BEGIN", 		1);		// -> tecnicamente loro non hanno questo status
define("_CUS_END", 			2);		// -> corso terminato
define("_CUS_SUSPEND", 		3);		// -> sospeso

// course quota

define("COURSE_QUOTA_INHERIT", -1);
define("COURSE_QUOTA_UNLIMIT", 	0);

define("_SHOW_COUNT", 	1);
define("_SHOW_INSTMSG", 2);

class Selector_Course {

	var $treeview = NULL;

	var $treeDB = NULL;

	var $show_filter = true;

	var $filter = array();

	var $current_page = array();

	var $current_selection = array();

	/**
	 * Class constructor
	 */
	function Selector_Course() {

		require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

		$lang =& DoceboLanguage::createInstance('admin_course_managment', 'lms');

		$this->show_filter = true;
		$this->treeDB 		= new TreeDb_CatDb($GLOBALS['prefix_lms'].'_category');
		$this->treeview 	= new TreeView_CatView($this->treeDB , 'course_category', $lang->def('_COURSE_CATEGORY'));
		$this->treeview->hideInlineAction();
	}

	function enableFilter() {

		$this->show_filter = true;
	}

	function disableFilter() {

		$this->show_filter = false;
	}

	/**
	 * return the current status in a pratic format
	 * @return string a string with the data used for reloading the current status
	 */
	function getStatus() {

		$status = array(
			'page' 					=> $this->current_page,
			'filter' 				=> serialize($this->filter),
			'show_filter' 			=> $this->show_filter,
			'current_selection' 	=> $this->current_selection,
			'treeview_status' 		=> serialize($this->treeview) );
		return serialize($status);
	}

	/**
	 * reset the current status to te given one
	 * @param string	$status_serialized a valid status saved using getStatus
	 */
	function loadStatus(&$status_serialized) {

		if($status_serialized == '') return ;
		$status = unserialize($status_serialized);
		$this->current_page			= $status['page'];
		$this->filter				= unserialize($status['filter']);
		$this->show_filter			= $status['show_filter'];
		$this->current_selection	= $status['current_selection'];
		$this->treeview				= unserialize($status['treeview_status']);
		$this->treeDB 				=& $this->treeview->getTreeDb();
	}

	function parseForAction($array_action) {

	}

	function parseForState($array_state) {

		// load change in treeview
		$this->treeview->parsePositionData($array_state, $array_state, $array_state);
		// older selection
		if(isset($array_state['course_selected'])) {

			$this->current_selection = unserialize(urldecode($array_state['course_selected']));
		}
		// add last selection
		if(isset($_POST['new_course_selected'])) {
			while(list($id_c) = each($array_state['new_course_selected'])) {

				$this->current_selection[$id_c] = $id_c;
			}
		}
	}

	function stateSelection() {

		return Form::getHidden('course_selected', 'course_selected', urlencode(serialize($this->current_selection)) );
	}

	function getSelection() {

		return $this->current_selection;
	}


	function resetSelection($new_selection) {

		$this->current_selection = $new_selection;
	}

	function loadCourseSelector($noprint=false) {

		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$lang =& DoceboLanguage::createInstance('admin_course_managment', 'lms');

		$output='';
		$output.=$this->treeview->load();

		// Filter

		$this->filter['course_flat'] = isset($_POST['c_flatview']);
		$this->filter['course_code'] = ( isset($_POST['c_filter_code']) ? $_POST['c_filter_code'] : '' );
		$this->filter['course_name'] = ( isset($_POST['c_filter_name']) ? $_POST['c_filter_name'] : '' );
		if($this->show_filter === true) {
			$form = new Form();
			$output.=//$GLOBALS['page']->add(
				$form->getOpenFieldset($lang->def('_COURSEFILTER'))
				.Form::getTextfield($lang->def('_FILTER_CODE'), 'c_filter_code', 'c_filter_code', '50',
					( isset($_POST['c_filter_code']) ? $_POST['c_filter_code'] : '' ))
				.Form::getTextfield($lang->def('_NAME'), 'c_filter_name', 'c_filter_name', '255',
					( isset($_POST['c_filter_name']) ? $_POST['c_filter_name'] : '' ))
				.Form::getCheckbox($lang->def('_FILTER_FLATVIEW'), 'c_flatview', 'c_flatview', '1',
					$this->filter['course_flat'],
					' onclick="submit();" ' )
				.$form->openButtonSpace()
				.$form->getButton('course_filter', 'course_filter', $lang->def('_SEARCH'))
				.$form->closeButtonSpace()
				.$form->getCloseFieldset()
			;//, 'content');
		}
		// End Filter

		$tb = new TypeOne($GLOBALS['lms']['visu_course'], $lang->def('_COURSE_LIST_CAPTION'), $lang->def('_COURSE_LIST_SUMMARY'));

		$tb->initNavBar('ini', 'button');
		$ini = $tb->getSelectedElement();

		$category_selected 	= $this->treeview->getSelectedFolderId();
		if($this->filter['course_flat']) {
			$id_categories 		= $this->treeDB->getDescendantsId($this->treeDB->getFolderById($category_selected));
			$id_categories[] 	= $category_selected;
		}
		$select = "
		SELECT c.idCourse, c.code, c.name, c.description, c.status, c.difficult,
			c.subscribe_method, c.permCloseLo, c.show_rules, c.max_num_subscribe ";
		$query_course = "
		FROM ".$GLOBALS['prefix_lms']."_course AS c
		WHERE c.course_type <> 'assessment'
			AND c.idCategory IN ( ".( !$this->filter['course_flat'] ? $category_selected  : implode(",", $id_categories) )." )";
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {

			require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');

			$course_man = new AdminCourseManagment();
			$all_courses =& $course_man->getUserAllCourses( getLogUserId() );

			if(empty($all_courses)) $query_course .= " AND 0 ";
			else $query_course .= " AND c.idCourse IN (".implode(',', $all_courses).") ";
		}
		if($this->filter['course_code'] != '') {
			$query_course .= " AND c.code LIKE '%".$this->filter['course_code']."%'";
		}
		if($this->filter['course_name'] != '') {
			$query_course .= " AND c.name LIKE '%".$this->filter['course_name']."%'";
		}
		list($tot_course) = mysql_fetch_row(mysql_query("SELECT COUNT(*) ".$query_course));
		$query_course .= " ORDER BY c.name
							LIMIT ".$ini.",".(int)$GLOBALS['lms']['visu_course'];

		$re_course = mysql_query($select.$query_course);

		$type_h = array('image', '', '', '');
		$cont_h = array(
			'<span class="access-only">'.$lang->def('_COURSE_SELECTION').'</span>',
			$lang->def('_CODE'),
			$lang->def('_COURSE_NAME'),
			$lang->def('_STATUS')
		);
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		while(list($id_course, $code, $name, $desc, $status, $difficult, $auto_sub, $end_mode, $show_rules, $max_user_sub) = mysql_fetch_row($re_course)) {

			$tb_content = array(
				Form::getInputCheckbox('new_course_selected_'.$id_course, 'new_course_selected['.$id_course.']', $id_course,
					isset($this->current_selection[$id_course]), ''),
				'<label for="new_course_selected_'.$id_course.'">'.$code.'</label>',
				'<label for="new_course_selected_'.$id_course.'">'.$name.'</label>'
			);
			switch($status) {
				case 0 : $tb_content[] = $lang->def('_CST_PRE_DO');break;
				case 1 : $tb_content[] = $lang->def('_CST_IN_DO');break;
				case 2 : $tb_content[] = $lang->def('_CST_AFTER_DO');break;
			}
			$tb->addBody($tb_content);
			if(isset($this->current_selection[$id_course])) unset($this->current_selection[$id_course]);
		}

		$output.=//$GLOBALS['page']->add(
			$tb->getTable()
			.$tb->getNavBar($ini, $tot_course)
			.$this->stateSelection();//, 'content');
		if ($noprint) return $output; else cout($output);
	}
}

class Man_Course {

	function Man_Course() {

	}

	function saveCourseStatus() {/*
		require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');

		$categoryDb = new TreeDb_CatDb($GLOBALS['prefix_lms'].'_category');
		$treeView = new TreeView_CatView($categoryDb, 'course_category', def('_COURSE_CATEGORY', 'admin_course_managment', 'lms'));
		$treeView->parsePositionData($_POST, $_POST, $_POST);

		//save status
		$o_save = new Session_Save();
		$tree_status = $o_save->getName('course_category', true);
		$o_save->save($tree_status, $treeView);*/
	}

	/**
	 * @param int 	$id_course			the id of the course
	 *
	 * @return array	return som info about the course [code, name, description, status, difficult, subscribe_method, max_num_subscribe]
	 */
	function getCourseInfo($id_course) {

		$query = "
		SELECT *
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse = '".$id_course."'";
		$re = mysql_query($query);

		return mysql_fetch_assoc($re);
	}

	/**
   * @param int 	$edition_id			the id of the edition
	 * @param int 	$course_id			the id of the course
	 *
	 * @return array	return som info about the course [code, name, description, status, difficult, subscribe_method, max_num_subscribe]
	 */
	function getEditionInfo($edition_id, $course_id=FALSE) {

		$query = "
		SELECT *
		FROM ".$GLOBALS['prefix_lms']."_course_edition
		WHERE idCourseEdition = '".$edition_id."'";
		if (($course_id !== FALSE) && ($course_id > 0)) {
			$query.=" AND idCourse = '".$course_id."'";
		}
		$re = mysql_query($query);

		return mysql_fetch_assoc($re);
	}

	/**
	 * return the list of all the courses in the platform, or fillter by category
	 * @param int 	$id_category	filter for passed category
	 *
	 * @return array	[id_course] => ( [id_course], [name], [course] )
	 */
	function getAllCourses($id_category = false, $type_of = false, $arr_courses = false, $no_status = false) {

		$courses = array();
		$query_course = "
		SELECT idCourse, code, name, description
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE 1 ";
		if($no_status) $query_course.=" AND status <> '".CST_PREPARATION."' ";
		if($type_of != 'assessment') $query_course .= " AND course_type <> 'assessment' ";
		if($id_category !== false) $query_course .= " AND idCategory = '".$id_category."' ";
		if($type_of !== false) $query_course .= " AND course_type = '".$type_of."' ";
		if($arr_courses !== false) $query_course .= " AND idCourse IN ( ".implode(',', $arr_courses)." )";
		$query_course .= " ORDER BY name";
		
		$re_course = mysql_query($query_course);
		while(list($id, $code, $name, $description) = mysql_fetch_row($re_course)) {

			$courses[$id] = array(	'id_course' => $id,
									'code' => $code,
									'name' => $name,
									'description' => $description );
		}

		return $courses;
	}

	function listCourseName($arr_courses) {

		$list = '';
		if(!is_array($arr_courses) || count($arr_courses) == 0) return $list;
		$courses = array();
		$query_course = "
		SELECT name
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse IN ( ".implode(',', $arr_courses)." ) ";
		$query_course .= " ORDER BY name";

		$re_course = mysql_query($query_course);
		$first = true;
		while(list($name) = mysql_fetch_row($re_course)) {

			$list .= ( $first ? '' : ', ' ).$name;
			$first = false;
		}
		return $list;
	}

	function getCoursesCount($only_visible = false) {

		$courses = array();
		$query_course = "
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_course ";
		if($only_visible == true) $query_course .= " WHERE show_rules = 0";

		if(!$re_course = mysql_query($query_course)) return 0;
		list($number) = mysql_fetch_row($re_course);
		return $number;
	}

	/**
	 * return the list of all the courses in the platform, or fillter by category
	 * @param int 	$id_category	filter for passed category
	 *
	 * @return array	[id_course] => ( idCourse, idCategory, code, name, description, lang_code, status, subscribe_method, mediumTime, selling, prize  )
	 */
	function &getAllCoursesWithMoreInfo($id_category = false) {

		$courses = array();
		$query_course = "
		SELECT idCourse, idCategory, code, name, description, lang_code, status,
			subscribe_method, mediumTime, show_rules, selling, prize, course_demo, create_date
		FROM ".$GLOBALS['prefix_lms']."_course ";
		if($id_category !== false) $query_course .= " WHERE idCategory = '".$id_category."' ";
		$query_course .= " ORDER BY name";
		$re_course = mysql_query($query_course);
		while($course = mysql_fetch_array($re_course)) {

			$courses[$course['idCourse']] = $course;
		}
		return $courses;
	}

	function addCourse($course_info) {

		$field = array();
		$value = array();

		foreach($course_info as $key => $v) {
			$field[] = $key;
			$value[] = "'".$v."'";
		}
		$query_course = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_course
		( ".implode(',', $field)." ) VALUES (".implode(',', $value).") ";

		if(!mysql_query($query_course)) return false;

		list($id_course) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

		return $id_course;
	}


	function saveCourse($id_course, $course_info) {

		$field = array();
		$value = array();

		if(!is_array($course_info) || empty($course_info)) return $id_course;

		foreach($course_info as $key => $v) {
			$field[] = $key." = '".$v."'";
		}
		$query_course = "
		UPDATE ".$GLOBALS['prefix_lms']."_course
		SET ".implode(',', $field)."
		WHERE idCourse = '".$id_course."' ";
		if(!mysql_query($query_course)) return false;

		return $id_course;
	}

	function deleteCourse($id_course) {

		require_once($GLOBALS['where_lms'].'/admin/modules/course/course.php');
		// delete the course
		if(removeCourse($id_course)) return true;
		return false;
	}

	/**
	 * @param int 	$id_course			the id of the course
	 *
	 * @return array	contains the info of the waiting usersin [user_info] and all the id_user occurrency in [all_user_id]
	 */
	function &getWaitingSubscribed($id_course, $edition_id = 0) {

		$users['users_info'] 	= array();
		$users['all_users_id'] 	= array();

		$query_courseuser = "
		SELECT idUser, level, subscribed_by, status
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idCourse = '".$id_course."' AND waiting = '1' AND  edition_id = '".$edition_id."'";
		$re_courseuser = mysql_query($query_courseuser);
		while(list($id_user, $lv, $subscribed_by, $status) = mysql_fetch_row($re_courseuser)) {

			$users['users_info'][$id_user] 			= array(
				'id_user' => $id_user,
				'level' => $lv,
				'subscribed_by' => $subscribed_by,
				'status' => $status
			);

			$users['all_users_id'][$id_user] 		= $id_user;
			$users['all_users_id'][$subscribed_by] = $subscribed_by;
		}
		return $users;
	}

	/**
	 * Find the idst of the group of a course that represent the level
	 * @param 	int 	$id_course 	the id of the course
	 *
	 * @return 	array	[lv] => idst, [lv] => idst
	 */
	function &getCourseIdstGroupLevel($id_course) {

		$map 		= array();
		$levels 	= CourseLevel::getLevels();
		$acl_man	=& $GLOBALS['current_user']->getAclManager();


		// find all the group created for this menu custom for permission management
		foreach($levels as $lv => $name_level) {

			$group_info = $acl_man->getGroup(FALSE, '/lms/course/'.$id_course.'/subscribed/'.$lv);
			$map[$lv] 	= $group_info[ACL_INFO_IDST];
		}
		/*
		if($also_waiting === true) {
			$group_info = $acl_man->getGroup(FALSE, '/lms/course/'.$id_course.'/subscribed/waiting');
			$map['waiting'] 	= $group_info[ACL_INFO_IDST];
		}*/
		return $map;
	}

	function getIdUserOfLevel($id_course, $level_number = false, $id_edition = false) {

		$users = array();
		$query_courseuser = "
		SELECT c.idUser
		FROM ".$GLOBALS['prefix_lms']."_courseuser AS c";
		$query_courseuser .= " WHERE c.idCourse = '".$id_course."'";
		if ($id_edition)
			$query_courseuser .= " AND c.edition_id = '".$id_edition."'";
		if($level_number !== false) $query_courseuser .= " AND c.level = '".$level_number."'";
		$re_courseuser = mysql_query($query_courseuser);
		while(list($id_user) = mysql_fetch_row($re_courseuser)) {

			$users[$id_user] = $id_user;
		}
		return $users;
	}

	/**
	 * @param int 		$id_course		the id of the course
	 * @param array 	$arr_users		if specified filter the user
	 *
	 * @return array	contains the id_user as key and level number as value
	 */
	function getLevelsOfUsers($id_course, $arr_users = false, $edition_id = false) {

		$id_users 	= array();
		if(!is_array($arr_users)) $arr_users = array($arr_users);
		if(count($arr_users) == 0) return $id_users;
		$query_courseuser = "
		SELECT idUser, level
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idCourse = '".$id_course."'";
		if($arr_users !== false) {
			$query_courseuser .= " AND idUser IN ( ".implode(',', $arr_users)." )";
		}
		if($edition_id !== false) {
			$query_courseuser .= " AND edition_id  = '".$edition_id."' ";
		}

		$re_courseuser = mysql_query($query_courseuser);
		while(list($id_user, $lv) = mysql_fetch_row($re_courseuser)) {

			$id_users[$id_user] = $lv;
		}
		return $id_users;
	}

	function getUserCourses($id_user) {

		// List of  courses
		$re_courses = mysql_query("
		SELECT course.idCourse, course.name
		FROM ".$GLOBALS['prefix_lms']."_course AS course JOIN ".$GLOBALS['prefix_lms']."_courseuser AS user
		WHERE course.idCourse = user.idCourse
			AND user.idUser = '".$id_user."'");

		$courses_subscribed = array();
		while(list($id_c, $name_c) = mysql_fetch_row($re_courses)) {
			$courses_subscribed[$id_c] = $name_c;
		}
		return $courses_subscribed;
	}

	function &getModulesName($id_course) {

		$ml_lang = DoceboLanguage::createInstance('menu_course');
		$mods_names = array();
		$query_menu = "
		SELECT mo.module_name, mo.default_op, mo.default_name, mo.token_associated, under.my_name
		FROM ".$GLOBALS['prefix_lms']."_module AS mo JOIN
			".$GLOBALS['prefix_lms']."_menucourse_under AS under
		WHERE mo.idModule = under.idModule AND under.idCourse = '".$id_course."'";
		$re_menu_voice = mysql_query($query_menu);
		while(list($module_name, $default_op, $default_name, $token, $my_name) = mysql_fetch_row($re_menu_voice)) {

			$mods_names[$module_name] = ( ($my_name != '' ) ? $my_name : ( $ml_lang->isDef($default_name) ? $ml_lang->def($default_name) : $default_name ) );
		}
		$mods_names['_LOGOUT'] = $ml_lang->def('_LOGOUT');
		$mods_names['_ELECOURSE'] = $ml_lang->def('_ELECOURSE');
		return $mods_names;
	}
	
	function addMainToCourse($id_course, $name) {
		
		$id_main = false;
		if(!mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_menucourse_main ( idCourse, sequence, name, image ) 
			VALUES ( '".$id_course."','0', '".$name."', '')")) return false;
		list($id_main) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		return $id_main;
	}
	
	/**
	 * this function detect modules useing id_module, module_name and
	 * default_op and add the module to the specified course, alsoassign
	 * the specified permission to the level_idst
	 * @param int 		$id_course 				the id of the course
	 * @param array		$level_idst 			the list of the idst assigned to each level
	 * @param int 		$id_amin 				the id of the main menu
	 * @param string 	$m_name 				the module name
	 * @param string 	$d_op 					the default module op
	 * @param array 	$level_token_to_assign 	for each level the token to assign array(level => array(token, token))
	 *
	 * @return bool true if success, false otherwise
	 */
	function addModuleToCourse($id_course, $level_idst, $id_main, $id_m = false, $m_name = false, $d_op = false, $level_token_to_assign = false) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.istance.php');

		$acl_man =& $GLOBALS['current_user']->getAclManager();

		$re = true;
		$query_menu = "
		SELECT idModule, module_name, default_op, file_name, class_name
		FROM ".$GLOBALS['prefix_lms']."_module
		WHERE 1";
		if($id_m !== false) 	$query_menu .= " AND idModule = '".$id_m."' ";
		if($m_name !== false) 	$query_menu .= " AND module_name = '".$m_name."' ";
		if($d_op !== false) 	$query_menu .= " AND default_op = '".$d_op."' ";
		
		$re_query = mysql_query($query_menu);
		if(!$re_query || (mysql_num_rows($re_query) == 0)) return false;
		
		$i = 0;
		while(list($id_module, $module_name, $module_op, $file_name, $class_name) = mysql_fetch_row($re_query)) {

			$module_obj 	=& createLmsModule($file_name, $class_name);
			$tokens 		= $module_obj->getAllToken($module_op);
			$module_role 	=& createModuleRoleForCourse($id_course, $module_name, $tokens);

			foreach($level_token_to_assign as $level => $token_list) {

				foreach($token_list as $token) {
					
					$re &= $acl_man->addToRole( $module_role[$token], $level_idst[$level] );

				} // end foreach

			} // end foreach
			
			$re &= mysql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_menucourse_under ( idCourse, idModule, idMain, sequence, my_name ) 
			VALUES ('".$id_course."', '".$id_module."', '".$id_main."', '".$i++."', '')");
			
		} // end while
		return $re;
	}

	function removeCourseRole($id_course) {

		$acl_man =& $GLOBALS['current_user']->getAclManager();
		$base_path = '/lms/course/private/'.$id_course.'/';
		$acl_man->deleteRoleFromPath($base_path);
	}

	function removeCourseMenu($id_course) {

		$query_del = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucourse_main
		WHERE idCourse = '".$id_course."'";

		$query_del_voice = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucourse_under
		WHERE idCourse = '".$id_course."'";

		if(!mysql_query($query_del)) return false;
		if(!mysql_query($query_del_voice)) return false;
	}

	/**
	 * this function need the user course stat and calculate if the user can enter into the course or not
	 * return the access status and the reason for a blocked access
	 * 
	 * @return array 	on key 'can'' => true or false
	 * 					on key 'reason' => it's possible to find this values : 'prerequisites', 'waiting', 
	 * 						'course_date', 'course_valid_time', 'user_status', 'course_status'
	 * 					on key 'expiring_in' => report the day remaining before the course expire
	 * 
	 */
	function canEnterCourse(&$course) {
		
		// control if the user is in a status that cannot enter
		$now = time();
		$expiring = false;
		
		if($course['date_end'] != '0000-00-00') {

			$time_end = fromDatetimeToTimestamp($course['date_end']);
			$exp_time = $time_end - $now;
			if($exp_time > 0) $expiring = round($exp_time / (24*60*60));
		}
		if($course['valid_time'] != '0' && $course['valid_time'] != '' && $course['date_first_access'] != '') {

			$time_first_access = fromDatetimeToTimestamp($course['date_first_access']);

			$exp_time = ( $time_first_access + ($course['valid_time'] * 24 * 3600 ) ) - $now;
			if($exp_time > 0) $expiring = round($exp_time / (24*60*60));
		}
		
		if($course['course_status'] == CST_CANCELLED)
			return array('can' => false, 'reason' => 'course_status', 'expiring_in' => $expiring);
		
		if(isset($course['edition_id']) && $course['edition_id'] && $course['edition_list'][$course['edition_id']]['status'] == CST_PREPARATION)
		{
			$query =	"SELECT level"
						." FROM ".$GLOBALS['prefix_lms']."_courseuser"
						." WHERE edition_id = '".$course['edition_id']."'"
						." AND idUser = '".getLogUserId()."'";
			
			list($level) = mysql_fetch_row(mysql_query($query));
			
			if($level > 3)
				return array('can' => true, 'reason' => 'user_status', 'expiring_in' => $expiring);
			else
				return array('can' => false, 'reason' => 'course_status', 'expiring_in' => $expiring);
		}
		elseif($course['course_status'] == CST_PREPARATION)
		{
			$query =	"SELECT level"
						." FROM ".$GLOBALS['prefix_lms']."_courseuser"
						." WHERE idCourse = '".$course['idCourse']."'"
						." AND idUser = '".getLogUserId()."'";
			
			list($level) = mysql_fetch_row(mysql_query($query));
			
			if($level > 3)
				return array('can' => true, 'reason' => 'user_status', 'expiring_in' => $expiring);
			else
				return array('can' => false, 'reason' => 'course_status', 'expiring_in' => $expiring);
		}
		
		if(isset($course['edition_id']) && $course['edition_id'] && $course['edition_list'][$course['edition_id']]['status'] == CST_CONCLUDED)
		{
			$query =	"SELECT status, level"
						." FROM ".$GLOBALS['prefix_lms']."_courseuser"
						." WHERE edition_id = '".$course['edition_id']."'"
						." AND idUser = '".getLogUserId()."'";
			
			list($status, $level) = mysql_fetch_row(mysql_query($query));
			
			if($status == _CUS_END || $level > 3)
				return array('can' => true, 'reason' => 'user_status', 'expiring_in' => $expiring);
			else
				return array('can' => false, 'reason' => 'course_status', 'expiring_in' => $expiring);
		}
		elseif($course['course_status'] == CST_CONCLUDED)
		{
			$query =	"SELECT status, level"
						." FROM ".$GLOBALS['prefix_lms']."_courseuser"
						." WHERE idCourse = '".$course['idCourse']."'"
						." AND idUser = '".getLogUserId()."'";
			
			list($status, $level) = mysql_fetch_row(mysql_query($query));
			
			if($status == _CUS_END || $level > 3)
				return array('can' => true, 'reason' => 'user_status', 'expiring_in' => $expiring);
			else
				return array('can' => false, 'reason' => 'course_status', 'expiring_in' => $expiring);
		}
		
		//if($course['user_status'] == _CUS_CANCELLED) return array('can' => false, 'reason' => 'user_status');
		if($course['level'] > 3) return array('can' => true, 'reason' => '', 'expiring_in' => $expiring);
		if(isset($course['prerequisites_satisfied']) && $course['prerequisites_satisfied'] == false) return array('can' => false, 'reason' => 'prerequisites', 'expiring_in' => $expiring);
		if(isset($course['waiting']) && $course['waiting'] >= 1) return array('can' => false, 'reason' => 'waiting', 'expiring_in' => $expiring);
		// control if the course is elapsed
		/*if($course['date_begin'] != '0000-00-00') {

			$time_begin = fromDatetimeToTimestamp($course['date_begin']);

			if($now < $time_begin) return array('can' => false, 'reason' => 'course_date', 'expiring_in' => $expiring);
		}
		if($course['date_end'] != '0000-00-00') {

			$time_end = fromDatetimeToTimestamp($course['date_end']);

			if($now > $time_end) return array('can' => false, 'reason' => 'course_date', 'expiring_in' => $expiring);
		}
		if($course['valid_time'] != '0' && $course['valid_time'] != '' && $course['date_first_access'] != '') {

			$time_first_access = fromDatetimeToTimestamp($course['date_first_access']);

			if($now > ( $time_first_access + ($course['valid_time'] * 24 * 3600 ) )) return array('can' => true, 'reason' => 'course_valid_time', 'expiring_in' => $expiring);
		}*/
		if( $course['userStatusOp'] & (1 << $course['user_status']) ) return array('can' => false, 'reason' => 'user_status', 'expiring_in' => $expiring);
		// user is not a tutor or a prof and the course isn't active
		if($course['course_status'] != 1 && $course['level'] < 4) return array('can' => true, 'reason' => 'course_status', 'expiring_in' => $expiring);
		return array('can' => true, 'reason' => '', 'expiring_in' => $expiring);
	}

	function getNumberOfCoursesForCategories($show_rules  = 0) {

		$courses = array();
		$query_course = "
		SELECT idCategory, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE show_rules  = '".$show_rules ."'
		GROUP BY idCategory";

		$re_course = mysql_query($query_course);
		while(list($id_cat, $number_of_course) = mysql_fetch_row($re_course)) {

			$courses[$id_cat] = $number_of_course;
		}
		return $courses;
	}

	function getCategoryCourseAndSonCount($id_parent = false) {

		$count = array();
		$query_cat = "
		SELECT idCategory, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE show_rules = '0' ".( !$GLOBALS['current_user']->isAnonymous() ? " OR show_rules = '1' " : "" )."
		GROUP BY idCategory";
		$re_category = mysql_query($query_cat);
		while(list($id, $num) = mysql_fetch_array($re_category)) {

			$count[$id]['course'] = $num;
		}

		$query_cat = "
		SELECT idCategory, idParent
		FROM ".$GLOBALS['prefix_lms']."_category ";
		if($id_parent !== false) $query_cat .= " WHERE idParent = '".$id_parent."' ";
		$query_cat .= " ORDER BY path DESC";

		$re_category = mysql_query($query_cat);
		while(list($id_cat, $id_parent) = mysql_fetch_array($re_category)) {

			$categories[$id_cat]['category'] = 0;
			if(isset($count[$id_parent]['category'])) $count[$id_parent]['category'] += 1;
			else $count[$id_parent]['category'] = 1;
			if(isset($categories[$id_cat])) $count[$id_parent]['category'] += $categories[$id_cat]['category'];
		}
		return $count;
	}

	function getCategory($id_cat) {

		$categories = array();
		$query_cat = "
		SELECT idCategory, idParent, lev, path, description
		FROM ".$GLOBALS['prefix_lms']."_category  
		WHERE idCategory = '".$id_cat."'";

		$re_category = mysql_query($query_cat);
		return mysql_fetch_array($re_category);
	}

	function &getCategoriesInfo($id_parent = false, $also_itself = false, $entire_path = false) {

		$categories = array();
		$query_cat = "
		SELECT idCategory, idParent, lev, path, description
		FROM ".$GLOBALS['prefix_lms']."_category  ";
		if($id_parent !== false) {
			$query_cat .= " WHERE idParent = '".$id_parent."'";
			if($also_itself !== false) $query_cat .= " OR idCategory = '".$id_parent."' ";
		}
		$query_cat .= " ORDER BY description";

		$re_category = mysql_query($query_cat);
		while($cat = mysql_fetch_array($re_category)) {

			if($entire_path === false) {

				$categories[$cat['idCategory']] = $cat;
				$categories[$cat['idCategory']]['name'] = (
					($pos = strrpos($cat['path'], '/')) === FALSE
						? $cat['path']
						: substr($cat['path'], $pos+1)
				);
			} else {

				$categories[$cat['idCategory']] = substr($cat['path'], strlen('/root/'));
			}
		}
		return $categories;
	}

	function _recurseCategory($id_cat, $title_link, $link, $parent_name) {

		if(!$id_cat) return '';
		$query_cat = "
		SELECT idParent, lev, path
		FROM ".$GLOBALS['prefix_lms']."_category
		WHERE idCategory = '".$id_cat."'";
		if(!$re_category = mysql_query($query_cat)) return '';
		list($id_parent, $lev, $path) = mysql_fetch_row($re_category);

		$name = ( ($pos = strrpos($path, '/')) === FALSE ? $path : substr($path, $pos+1) );
		if($lev <= 1) {

			return ' &gt; '.( $link !== false
					? '<a title="'.$title_link.' : '.$name.'" href="'.$link.'&amp;'.$parent_name.'='.$id_cat.'">'.$name.'</a>'
					: $name );
		} else {

			return $this->_recurseCategory($id_parent, $title_link, $link, $parent_name)
				.' &gt; '.( $link !== false
					? '<a title="'.$title_link.' : '.$name.'" href="'.$link.'&amp;'.$parent_name.'='.$id_cat.'">'.$name.'</a>'
					: $name );
		}
	}

	function getCategoryPath($id_cat, $lang_main, $title_link, $link, $parent_name) {

		$categories = array();
		return ( $link !== false
					? '<a title="'.$title_link.' : '.$lang_main.'"  href="'.$link.'">'.$lang_main.'</a>'
					: $lang_main )
			.$this->_recurseCategory($id_cat, $title_link, $link, $parent_name);
	}
}


/**
 * This class purpose is to retrive information about users in relation with courses
 */
class Man_CourseUser {

	/**
	 * @access private
	 * @var resource_id 	the db_connection
	 */
	var $_db_conn;

	/**
	 * @access private
	 * @var string 	the name of the course table
	 */
	var $_table_course;

	/**
	 * @access private
	 * @var string 	the name of the table that contains users course subscription
	 */
	var $_table_user_subscription;

	/**
	 * class constructor
	 * @access public
	 * @param resource_id	$db_conn	the resource id for database connection
	 */
	function Man_CourseUser($db_conn = NULL) {

		$this->_db_conn 					= $db_conn;
		$this->_table_course 				= $GLOBALS['prefix_lms'].'_course';
		$this->_table_user_subscription 	= $GLOBALS['prefix_lms'].'_courseuser';

	}

	/**
	 * return the current name of the course table
	 * @access public
	 * @return string	the name of the course table
	 */
	function getTableCourse() {

		return $this->_table_course;
	}

	/**
	 * set the name of the course table
	 * @access public
	 * @param string	$table_course the name of the course table
	 */
	function setTableCourse($table_course) {

		$this->_table_course = $table_course;
	}

	/**
	 * return the current name of the table that associate users with course
	 * @access public
	 * @return string	the name of the course table
	 */
	function getTableUserSubscription() {

		return $this->_table_user_subscription;
	}

	/**
	 * set the name of the course table
	 * @access public
	 * @param string	$table_course the name of the course table
	 */
	function setTableUserSubscription($table_user_subscription) {

		$this->_table_user_subscription = $table_user_subscription;
	}

	/**
	 * execute a query
	 * @access private
	 * @param string	$query_text	the query that you want to exe
	 *
	 * @return resource_id 	the id of the mysql resource
	 */
	function _query($query_text) {

		if($this->_db_conn)
			$re = mysql_query($query_text, $this->_db_conn);
		else
			$re = mysql_query($query_text);
		doDebug($query_text);
		return $re;
	}

	/**
	 * Return the complete course list in which a user is subscribe, you can filter the result with
	 * course status or user status in the course
	 *
	 * @access public
	 *
	 * @param int 	$id_user 		the idst of the user
	 * @param int 	$id_category 	filter for course category
	 * @param int 	$course_status 	filter for course statsus the result
	 * @param int 	$user_status 	filter for the user status in the course
	 *
	 * @return array the list of the course with the carachteristic of it array( id_course => array(
	 * 					idCourse, code, name, description, date_begin, date_end, valid_time, course_status,
	 * 					waiting, userStatusOp, level, user_status, date_inscr, date_first_access,date_complete), ...)
	 */
	 function &getUserCourses($id_user, $id_category = false, $course_status = false, $user_status = false) {

		$courses = array();
		$query_courses = "
		SELECT c.idCourse, c.idCategory, c.code, c.name, c.description,
			c.date_begin, c.date_end, c.valid_time, c.status AS course_status, u.waiting,
			c.userStatusOp, u.level, u.status as user_status, u.date_inscr, u.date_first_access, u.date_complete
		FROM ".$GLOBALS['prefix_lms']."_course AS c JOIN
			".$GLOBALS['prefix_lms']."_courseuser AS u
		WHERE c.idCourse = u.idCourse AND u.idUser = '".$id_user."'";

		if($id_category !== false) 		$query_courses .= " AND c.idCategory = '".$id_category."' ";
		if($course_status !== false) 	$query_courses .= " AND c.status = '".$course_status."' ";
		if($user_status !== false) 		$query_courses .= " AND u.status = '".$user_status."' ";

		$query_courses .= "ORDER BY c.name";

		$re_course = mysql_query($query_courses);
		while($course = mysql_fetch_assoc($re_course)) {

			$courses[$course['idCourse']] = $course;
		}
		return $courses;
	}
	
	/**
	 * Return the complete id list in which a user is subscribe, you can filter the result with
	 * course status or user status in the course
	 *
	 * @access public
	 *
	 * @param int 	$id_user 		the idst of the user
	 * @param int 	$id_category 	filter for course category
	 * @param int 	$course_status 	filter for course statsus the result
	 * @param int 	$user_status 	filter for the user status in the course
	 *
	 * @return array the list of the course with the carachteristic of it array( id_course => array(
	 * 					idCourse, code, name, description, date_begin, date_end, valid_time, course_status,
	 * 					waiting, userStatusOp, level, user_status, date_inscr, date_first_access,date_complete), ...)
	 */
	 function getUserCourseList($id_user, $id_category = false, $course_status = false, $user_status = false) {

		$courses = array();
		$query_courses = "
		SELECT c.idCourse
		FROM ".$GLOBALS['prefix_lms']."_course AS c JOIN
			".$GLOBALS['prefix_lms']."_courseuser AS u
		WHERE c.idCourse = u.idCourse AND u.idUser = '".$id_user."'";

		if($id_category !== false) 		$query_courses .= " AND c.idCategory = '".$id_category."' ";
		if($course_status !== false) 	$query_courses .= " AND c.status = '".$course_status."' ";
		if($user_status !== false) 		$query_courses .= " AND u.status = '".$user_status."' ";

		$query_courses .= "ORDER BY c.name";

		$re_course = mysql_query($query_courses);
		while($course = mysql_fetch_assoc($re_course)) {

			$courses[$course['idCourse']] = $course['idCourse'];
		}
		return $courses;
	}

	/**
	 * Return the complete course list in which a user is subscribe with the level requested
	 *
	 * @access public
	 *
	 * @param int 	$id_user 		the idst of the user
	 * @param int 	$id_category 	filter for course category
	 *
	 * @return array the list of the course with the carachteristic of it array( id_course => array(
	 * 					idCourse, code, name, description
	 */
	 function &getUserCoursesLevelFilter($id_user, $level_number, $not_assessment = false) {

		$courses = array();
		$query_courses = "
		SELECT c.idCourse, c.code, c.name, c.description
		FROM ".$GLOBALS['prefix_lms']."_course AS c JOIN
			".$GLOBALS['prefix_lms']."_courseuser AS u
		WHERE c.idCourse = u.idCourse
			AND u.idUser = '".$id_user."'
			AND u.level = '".$level_number."'";
		if($not_assessment === true) $query_courses .= " AND c.course_type <> 'assessment' ";
		$query_courses .= " ORDER BY c.name";
		
		$re_course = mysql_query($query_courses);
		while($course = mysql_fetch_assoc($re_course)) {

			$courses[$course['idCourse']] = $course;
		}
		return $courses;
	}

	/**
	 * Return the complete user list that have the requested level
	 *
	 * @access public
	 *
	 * @param int 	$id_user 	the idst of the user
	 * @param int 	$level 		the level number
	 *
	 * @return array the list of the course with the carachteristic of it array( id_course => array(
	 * 					idCourse, code, name, description
	 */
	 function getUserWithLevelFilter($level, $arr_user = false) {

		$users = array();
		$query_courses = "
		SELECT DISTINCT idUser
		FROM ".$GLOBALS['prefix_lms']."_courseuser AS u
		WHERE ";
		if(is_array($level)) $query_courses .= " level IN ( ".implode(',', $level)." ) ";
		else $query_courses .= " level = '".$level."'";
		if($arr_user != false) $query_courses .= " AND idUser IN ( ".implode(',', $arr_user)." )";

		$re_course = mysql_query($query_courses);

		while(list($id) = mysql_fetch_row($re_course)) {

			$users[$id] = $id;
		}
		return $users;
	}
	
	function getUserSubscriptionsInfo($id_user, $exclude_waiting = false) {

		$courses 	= array();
	
		$query_courseuser = "
		SELECT idCourse, level, waiting, status
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser = '".$id_user."'";
		if($exclude_waiting) $query_courseuser .= " AND waiting = 0";
		$re_courseuser = mysql_query($query_courseuser);
		while(list($id_course, $lv, $is_waiting, $status) = mysql_fetch_row($re_courseuser)) {
	
			$courses[$id_course] = array( 	'idUser' => $id_user,
											'level' => $lv,
											'waiting' => $is_waiting,
											'status' => $status );
		}
		return $courses;
	}
	
	/**
	 * return the courses that the user have score
	 * @param int $id_user the id of the user
	 * 
	 * @return array (id_course => score, id_course => score, ...)
	 */
	function getUserCourseScored($id_user) {
		
		$courses = array();
		$query_courseuser = "
		SELECT idCourse, score_given
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser = '".$id_user."' AND score_given IS NOT NULL ";
		$re_courseuser = mysql_query($query_courseuser);
		while(list($id_course, $score_given) = mysql_fetch_row($re_courseuser)) {
	
			$courses[$id_course] = $score_given;
		}
		return $courses;
		
	}
	
	function subscribeUserWithCode ($code, $id_user, $level = 3)
	{
		require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
		
		$subscriber = new CourseSubscribe_Management();
		
		$acl_man =& $GLOBALS['current_user']->getAclManager();
		
		$query_course = "SELECT idCourse" .
						" FROM ".$GLOBALS['prefix_lms']."_course" .
						" WHERE autoregistration_code = '".$code."'"
						." AND autoregistration_code <> ''";
		
		$result_course = mysql_query($query_course);
		
		$counter = 0;
		$subs = $this->getUserSubscriptionsInfo($id_user);
		
		if(!mysql_num_rows($result_course)) return 0;
		while (list($id_course) = mysql_fetch_row($result_course))
		{
			if(!isset($subs[$id_course])) {
				
				$result = $subscriber->subscribeUser($id_user, $id_course, $level);
				if($result) $counter++;
			}
		}
		if(mysql_num_rows($result_course)!= 0 && $counter == 0) return -1;
		return $counter;
	}
	
	function checkCode ($code) {
		$query_course = "SELECT idCourse" .
						" FROM ".$GLOBALS['prefix_lms']."_course" .
						" WHERE autoregistration_code = '".$code."'";
		$result_course = mysql_query($query_course);
		
		if(!mysql_num_rows($result_course)) return 0;
		return mysql_num_rows($result_course);
	}
	
}

class DoceboCourse {

	var $id_course;

	var $course_info;

	function _executeQuery($query_text) {

		$re = mysql_query($query_text);
		doDebug($query_text);
		return $re;
	}

	function _load() {

		$query_load = "
		SELECT *
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse = '".$this->id_course."'";
		$re_load = $this->_executeQuery($query_load);
		$this->course_info = mysql_fetch_assoc($re_load);
	}

	function DoceboCourse($id_course) {

		$this->id_course = $id_course;
		$this->_load();
	}

	function getAllInfo() {

		return $this->course_info;
	}

	function getValue($param) {

		return $this->course_info[$param];
	}

	function setValues($arr_new_values) {

		$re = true;
		if(empty($arr_new_values)) return $re;
		while(list($key, $value) = each($arr_new_values)) {

			$params[] = " ".$key." = '".$value."'";
		}
		$query = "
		UPDATE ".$GLOBALS['prefix_lms']."_course
		SET ".implode(',', $params)
		." WHERE idCourse = '".$this->id_course."'";
		return $this->_executeQuery($query);
	}
	
	function voteCourse($id_user, $score, $user_score_to_save) {
		
		$query = "
		UPDATE ".$GLOBALS['prefix_lms']."_courseuser
		SET score_given = ".$user_score_to_save." "
		." WHERE idCourse = '".$this->id_course."' AND idUser = '".$id_user."'";
		if(!$this->_executeQuery($query)) return false;
		
		$query = "
		UPDATE ".$GLOBALS['prefix_lms']."_course 
		SET course_vote  = course_vote  ".( $score > 0 ? '+' : '-' ).abs($score)." "
		." WHERE idCourse = '".$this->id_course."'";
		if(!$this->_executeQuery($query)) return false;
		
		$this->course_info['course_vote'] = $this->course_info['course_vote'] + $score;
		return $this->course_info['course_vote'];
	}
	
	/**
	 * Find the idst of all the user subscribed to the course
	 * @param 	int 	$id_course 	the id of the course
	 *
	 * @return 	array	idst
	 */
	function getSubscribed() {

		$acl_man	=& $GLOBALS['current_user']->getAclManager();
/*
		$group_info = $acl_man->getGroup(FALSE, '/lms/course/'.$this->id_course.'/subscribed/alluser');
		$idst_group = $group_info[ACL_INFO_IDST];

		$members = $acl_man->getGroupAllUser($idst_group);*/
		$members = array();
		$re_course= mysql_query("SELECT idUser FROM ".$GLOBALS['prefix_lms']."_courseuser WHERE idCourse = '".(int)$this->id_course."'");
		while(list($idu) = mysql_fetch_row($re_course)) {
			$members[$idu] = $idu;
		}
		return $members;
	}

	function getQuotaLimit() {

		$course_quota = $this->course_info['course_quota'];
		if($course_quota == COURSE_QUOTA_INHERIT) $course_quota = $GLOBALS['lms']['course_quota'];
		return $course_quota;
	}

	function getUsedSpace() {

		$course_size = $this->course_info['used_space'];
		return $course_size;
	}

	function addFileToUsedSpace($path = false, $manual_size = false) {

		if($manual_size === false) $size = getFileSize($path);
		else $size = $manual_size;

		$this->course_info['used_space'] = $this->course_info['used_space'] + $size;

		return $this->setValues(array('used_space' => $this->course_info['used_space']));
	}

	function subFileToUsedSpace($path = false, $manual_size = false) {

		if($manual_size === false) $size = getFileSize($path);
		else $size = $manual_size;

		$course_size = $this->course_info['used_space'] - $size;
		$this->course_info['used_space'] = ( $course_size < 0 ? 0 : $course_size);

		return $this->setValues(array('used_space' => $course_size));
	}
}

/*******************************************************************************************/

/**
 * @param int 	$id_course			the id of the course
 * @param bool 	$subdived_for_level	if is true the array is in the form
 *									[id_lv] => ([] => id_user, [] => id_user, ...), [id_lv] => ([] => id_user, ...)
 * @param int	$id_level			if is not false the array contains only a list of id_user of the level passed
 * @param bool	$exclude_waiting	if true exclude the user in wait status
 *
 * @return array	contains the id_user of the user subscribed, the structure is dependent of the other param
 */
function getSubscribed($id_course, $subdived_for_level = false, $id_level = false, $exclude_waiting = false, $edition_id=0) {

	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	$id_users 	= array();

	$query_courseuser = "
	SELECT idUser, level, waiting
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idCourse = '".$id_course."' AND edition_id='".(int)$edition_id."'";
	if($exclude_waiting) $query_courseuser .= " AND waiting = 0";
	if($id_level !== false) {
		$query_courseuser .= " AND level = '".$id_level."'";
	}
	$re_courseuser = mysql_query($query_courseuser);
	while(list($id_user, $lv, $is_waiting) = mysql_fetch_row($re_courseuser)) {

		if($subdived_for_level === false) {

			$id_users[$id_user] = $id_user;
		} else {

			if( $is_waiting) {

				$id_users['waiting'][$id_user] = $id_user;
			} else {

				$id_users[$lv][$id_user] = $id_user;
			}
		}
	}
	return $id_users;
}

/**
 * @param int 	$id_course			the id of the course
 * @param bool 	$subdived_for_level	if is true the array is in the form
 *									[id_lv] => ([] => id_user, [] => id_user, ...), [id_lv] => ([] => id_user, ...)
 * @param int	$id_level			if is not false the array contains only a list of id_user of the level passed
 * @param bool	$exclude_waiting	if true exclude the user in wait status
 *
 * @return array	contains the id_user of the user subscribed, the structure is dependent of the other param
 */
function getSubscribedInfo($id_course, $subdived_for_level = false, $id_level = false, $exclude_waiting = false, $status = false, $edition_id = false, $sort = false) {

	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	$id_users 	= array();
	
	$query_courseuser = "
	SELECT c.idUser, c.level, c.waiting, c.status, c.absent
	FROM ".$GLOBALS['prefix_lms']."_courseuser AS c";
	if ($sort)
		$query_courseuser .= " JOIN ".$GLOBALS['prefix_fw']."_user AS u ON u.idst = c.idUser";
	$query_courseuser .= " WHERE c.idCourse = '".$id_course."'";
	if($exclude_waiting) $query_courseuser .= " AND c.waiting = 0";
	if($id_level !== false) {
		$query_courseuser .= " AND c.level = '".$id_level."'";
	}
	if($status !== false) {
		$query_courseuser .= " AND c.status = '".$status."'";
	}
	if($edition_id !== false) {
		$query_courseuser .= " AND c.edition_id = '".$edition_id."'";
	}
	if ($sort)
		$query_courseuser .= " ORDER BY u.lastname, u.firstname, u.userid";
	$re_courseuser = mysql_query($query_courseuser);
	while(list($id_user, $lv, $is_waiting, $status, $absent) = mysql_fetch_row($re_courseuser)) {

		if($subdived_for_level === false) {

			$id_users[$id_user] = array( 	'idUser' => $id_user,
											'level' => $lv,
											'waiting' => $is_waiting,
											'status' => $status,
											'absent' => $absent );
		} else {

			if( $is_waiting) {

				$id_users['waiting'][$id_user] = array( 'idUser' => $id_user,
														'level' => $lv,
														'waiting' => $is_waiting,
														'status' => $status,
														'absent' => $absent );
			} else {

				$id_users[$lv][$id_user] = array( 	'idUser' => $id_user,
													'level' => $lv,
													'waiting' => $is_waiting,
													'status' => $status,
													'absent' => $absent );
			}
		}
	}
	return $id_users;
}

/**
 * @param int 	$id_course			the id of the course
 * @param bool 	$subdived_for_level	if is true the array is in the form
 *									[id_lv] => ([] => id_user, [] => id_user, ...), [id_lv] => ([] => id_user, ...)
 * @param int	$id_level			if is not false the array contains only a list of id_user of the level passed
 *
 * @return array	contains the id_user of the user subscribed and the relative level
 */
function getSubscribedLevel($id_course, $subdived_for_level = false, $id_level = false, $edition_id=0) {

	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	$id_users 	= array();

	$query_courseuser = "
	SELECT idUser, level, waiting
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idCourse = '".$id_course."' AND edition_id='".(int)$edition_id."'";
	if($id_level !== false) {
		$query_courseuser .= " AND level = '".$id_level."'";
	}
	$re_courseuser = mysql_query($query_courseuser);
	while(list($id_user, $lv, $is_waiting) = mysql_fetch_row($re_courseuser)) {

		if($subdived_for_level === false) {

			$id_users[$id_user] = $lv;
		} else {

			if( $is_waiting) {

				$id_users['waiting'][$id_user] = $id_user;
			} else {

				$id_users[$lv][$id_user] = $id_user;
			}
		}
	}
	return $id_users;
}

/**
 * Find the idst of the group of a course that represent the level
 * @param 	int 	$id_course 	the id of the course
 *
 * @return 	array	[lv] => idst, [lv] => idst
 */
function &getCourseLevel($id_course, $also_waiting = false) {

	$map 		= array();
	$levels 	= CourseLevel::getLevels();
	$acl_man	=& $GLOBALS['current_user']->getAclManager();


	// find all the group created for this menu custom for permission management
	foreach($levels as $lv => $name_level) {

		$group_info = $acl_man->getGroup(FALSE, '/lms/course/'.$id_course.'/subscribed/'.$lv);
		$map[$lv] 	= $group_info[ACL_INFO_IDST];
	}
	return $map;
}


/**
 * Create the group of a course that represent the level
 * @param 	int 	$id_course 	the id of the course
 *
 * @return 	array	[lv] => idst, [lv] => idst
 */
function &createCourseLevel($id_course) {

	require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

	$map 		= array();
	$levels 	= CourseLevel::getLevels();
	$acl_man	=& $GLOBALS['current_user']->getAclManager();

	$idst_main = $acl_man->registerGroup( '/lms/course/'.$id_course.'/group/alluser',
								'all the user of a course',
								true );

	foreach($levels as $lv => $value) {
		$idst = $acl_man->registerGroup( '/lms/course/'.$id_course.'/subscribed/'.$lv,
								'for course subscription in lms',
								true );
		$map[$lv] = $idst;
	}

	foreach($map as $k => $id_g) {

		$acl_man->addToGroup($idst_main, $id_g);
	}
	return $map;
}

function getIDGroupAlluser($id_course) {

	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	$info = $acl_man->getGroup(FALSE, '/lms/course/'.$id_course.'/group/alluser');

	return $info[ACL_INFO_IDST];
}

/**
 * @param int	$id_user 	the idst of the user
 *
 * @return
 */
function &fromIdstToUser($id_user) {

	$users = array();
	if(!is_array($id_user) || (count($id_user) == 0) ) {
		return $users;
	}

	$acl_man	=& $GLOBALS['current_user']->getAclManager();

	while(list(, $id_u) = each($id_user)) {

		$user_info = $acl_man->getUser($id_u, false);
		if( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME] == '') {

			$users[] = $user_info[ACL_INFO_USERID];
		} else {

			$users[] = $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME];
		}
	}
	return $users;
}


function &getCoursesInfo(&$courses) {

	if(empty($courses)) return array();

	$select = "
	SELECT idCourse, code, name, description
	FROM ".$GLOBALS['prefix_lms']."_course
	WHERE idCourse IN (".implode(',', $courses).")";
	$re_select = mysql_query($select);
	while($assoc = mysql_fetch_assoc($re_select)) {

		$re_courses[$assoc['idCourse']] = array(
			'id' => $assoc['idCourse'],
			'code' => $assoc['code'],
			'name' => $assoc['name'],
			'description' => $assoc['description'],
		);
	}
	return $re_courses;
}


function getCoursesName(&$courses) {

	if(empty($courses)) return array();

	$select = "
	SELECT idCourse, name
	FROM ".$GLOBALS['prefix_lms']."_course
	WHERE idCourse IN (".implode(',', $courses).")";
	$re_select = mysql_query($select);
	while(list($id, $name) = mysql_fetch_row($re_select)) {

		$re_courses[$id] = $name;
	}
	return $re_courses;
}


function isUserCourseSubcribed($id_user, $id_course, $edition_id=FALSE) {

	$course = array();
	$query_course = "
	SELECT idCourse
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$id_user."' AND idCourse = '".$id_course."'";

	if (($edition_id !== FALSE) && ($edition_id > 0)) {
		$query_course.=" AND edition_id='".$edition_id."'";
	}

	$re_course = mysql_query($query_course);
	return (mysql_num_rows($re_course) > 0) ;
}

function logIntoCourse($id_course, $gotofirst_page = true) {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');
	
	if(!$GLOBALS['current_user']->isAnonymous() && isset($_SESSION['idCourse'])) {
	
		TrackUser::setActionTrack(getLogUserId(), $_SESSION['idCourse'], '', '');
	}
	
	$re_course = mysql_query("
	SELECT edition_id, level, status, waiting 
	FROM ".$GLOBALS['prefix_lms']."_courseuser 
	WHERE idCourse = '".$id_course."' AND idUser = '".getLogUserId()."'");
	
	//control if user can enter into the course selected
	while($row = mysql_fetch_row($re_course)) {
		
		$subs[$row[0]] = $row;
	}
	$passed_id_e = importVar('id_e', true, 0);
	if(count($subs) == 1) {
		// one edition availabel
		reset($subs);
		list($id_e, $level_c, $status_user, $waiting_user) = current($subs);
	} elseif(isset($subs[$passed_id_e])) {
		// more than one edition availabel
		$id_e 			= $subs[$passed_id_e][0];
		$level_c 		= $subs[$passed_id_e][1];
		$status_user 	= $subs[$passed_id_e][2];
		$waiting_user 	= $subs[$passed_id_e][3];
	} else {
		return false;
	}
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	$GLOBALS['course_descriptor'] = new DoceboCourse($id_course);
	$course_info = $GLOBALS['course_descriptor']->getAllInfo();
	$course_info['course_status'] 	= $GLOBALS['course_descriptor']->getValue('status');
	$course_info['user_status'] 	= $status_user;
	$course_info['waiting'] 		= $waiting_user;
	$course_info['level'] 			= $level_c;
	if($level_c == 2) $_SESSION['is_ghost'] = true;
	else $_SESSION['is_ghost'] = false;
	
	if($id_e != 0) {
		
		$ed_info = Man_Course::getEditionInfo($id_e);
		$course_info['status'] = $ed_info['status'];
		$course_info['date_begin'] = $ed_info['date_begin'];
		$course_info['date_end'] = $ed_info['date_end'];
	}
	
	if(Man_Course::canEnterCourse($course_info)) {
		
		if($status_user == _CUS_SUBSCRIBED) {
			
			require_once($GLOBALS['where_lms'].'/lib/lib.stats.php');
			saveTrackStatusChange(getLogUserId(), $id_course, _CUS_BEGIN);
		}
	
		$now = date("Y-m-d H:i:s");
		$_SESSION['timeEnter'] 	= $now;
		$_SESSION['idCourse'] 		= $id_course;
		$_SESSION['idEdition'] 		= $id_e;
		$_SESSION['levelCourse'] 	= $level_c;
		
		$GLOBALS['current_user']->loadUserSectionST('/lms/course/private/'.$level_c.'/');
		$GLOBALS['current_user']->SaveInSession();
		
		TrackUser::createSessionCourseTrack();
		
		// now analyze the course type and select the acton to perform  
		
		if(isset($_GET['showresult']))
		{
			require_once($GLOBALS['where_lms'].'/lib/lib.orgchart.php');
			$orgman = new OrganizationManagement($_SESSION['idCourse']);
			$scorm =& $orgman->getInfoWhereType('scormorg', $_SESSION['idCourse']);
			
			if(count($scorm) == '1')
			{
				$obj = array_shift($scorm);
				jumpTo('index.php?modname=organization&op=scorm_track&id_user='.getLogUserId().'&id_org='.$obj['id_resource'].'&amp;back='.$GLOBALS['course_descriptor']->getValue('direct_play'));
			}
			
			jumpTo('index.php?modname=course&op=showresults&id_course='.$_SESSION['idCourse']);
		}
		
		$first_page = firstPage();
		$_SESSION['current_main_menu'] = $first_page['idMain'];
		$jumpurl = 'index.php?modname='.$first_page['modulename'].'&op='.$first_page['op'].'&id_module_sel='.$first_page['idModule'];
		
		
		if(isset($_SESSION['direct_play'])) unset($_SESSION['direct_play']); 
		
		$direct_play = $GLOBALS['course_descriptor']->getValue('direct_play');
		
		if($direct_play == 1) {
		
			if($_SESSION['levelCourse'] < 4) {
					
				// i need to play directly the test
				require_once($GLOBALS['where_lms'].'/lib/lib.orgchart.php');
				$orgman = new OrganizationManagement($_SESSION['idCourse']);
				$first_lo =& $orgman->getInfoWhereType(false, $_SESSION['idCourse']);
				
				if(count($first_lo) >= 1) {
					$obj = array_shift($first_lo);
					$_SESSION['direct_play'] = 1;
					jumpTo('index.php?modname=organization&op=custom_playitem&id_item='.$obj['id_org'].'');
				}
				if($gotofirst_page) jumpTo($jumpurl);
				else return true;
			} else {
				
				if($gotofirst_page) jumpTo($jumpurl);
				else return true;
			}
		}
		
		$type_of = $GLOBALS['course_descriptor']->getValue('course_type');
		if(isset($_SESSION['test_assessment'])) unset($_SESSION['test_assessment']); 
		
		switch($type_of) {
			case "assessment" : {
				
				if($_SESSION['levelCourse'] <= 3) {
					
					// i need to play directly the test
					require_once($GLOBALS['where_lms'].'/lib/lib.orgchart.php');
					$orgman = new OrganizationManagement($_SESSION['idCourse']);
					$test =& $orgman->getInfoWhereType('test', $_SESSION['idCourse']);
					
					if(count($test) == 1) {
						$obj = array_shift($test);
						$_SESSION['test_assessment'] = 1;
						jumpTo('index.php?modname=organization&op=custom_playitem&id_item='.$obj['id_org'].'');
					}
					if($gotofirst_page) jumpTo($jumpurl);
					else return true;
				} else {
					
					if($gotofirst_page) jumpTo($jumpurl);
					else return true;
				}
			};break;
			default: {
				if($gotofirst_page) jumpTo($jumpurl);
				else return true;
			}
		}
	}
	return false;
}

function getModuleFromId($id_module) {
	
	$query_menu = "
	SELECT module_name, default_op
	FROM ".$GLOBALS['prefix_lms']."_module
	WHERE idModule = ".(int)$id_module." ";
	
	$re_module = mysql_query($query_menu);
	if(!$re_module || mysql_num_rows($re_module) == 0) return false; 
	$result = mysql_fetch_row($re_module);
	return $result;
}

?>