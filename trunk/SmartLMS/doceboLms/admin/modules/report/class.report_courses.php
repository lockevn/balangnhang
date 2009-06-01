<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
require_once(dirname(__FILE__).'/class.report.php');

define('_RCS_CATEGORY_USERS', 'users');
define('_RCS_CATEGORY_LO', 'LO');

define('_SUBSTEP_USERS', 0);
define('_SUBSTEP_COLUMNS', 1);

class Report_Courses extends Report {

	var $status_u = array();
	var $status_c = array();

	function Report_Courses()
	{
		$this->lang =& DoceboLanguage::createInstance('report', 'framework');

		$this->usestandardtitle_rows = true;
		//$this->usestandardtitle_cols = false;

		$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');

		$this->_set_columns_category(_RCS_CATEGORY_USERS, $this->lang->def('_RCS_CAT_USER'), 'get_user_filter', 'show_report_user', '_get_users_query', false);

		$this->status_c = array(
			CST_PREPARATION => $lang->def('_CST_PREPARATION'),//, 'admin_course_managment', 'lms'),
			CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'),//, 'admin_course_managment', 'lms'),
			CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'),//, 'admin_course_managment', 'lms'),
			CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'),//, 'admin_course_managment', 'lms'),
			CST_CANCELLED 	=> $lang->def('_CST_CANCELLED')//, 'admin_course_managment', 'lms')
		);

		$lang =& DoceboLanguage::CreateInstance('course', 'lms');
		$this->status_u = array(
			_CUS_CONFIRMED 		=> $lang->def('_USER_STATUS_CONFIRMED'),//, 'subscribe', 'lms'),

			_CUS_SUBSCRIBED 	=> $lang->def('_USER_STATUS_SUBS'),//, 'subscribe', 'lms'),
			_CUS_BEGIN 			=> $lang->def('_USER_STATUS_BEGIN'),//, 'subscribe', 'lms'),
			_CUS_END 			=> $lang->def('_USER_STATUS_END'),//, 'lms'),
			_CUS_SUSPEND 		=> $lang->def('_USER_STATUS_SUSPEND')//, 'subscribe', 'lms')
		);
	}



	function get_rows_filter()
	{
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		//$sel = new Course_Manager();
		//$sel->setLink('index.php?modname=report&op=report_rows_filter');

		if (isset($_POST['undo_filter'])) jumpTo($back_url);

		//set $_POST data in $_SESSION['report_tempdata']
		if (!isset($_SESSION['report_tempdata']['rows_filter'])) {
			$_SESSION['report_tempdata']['rows_filter'] = array(
				'all_courses' => true,
				'selected_courses' => array()
			);
		}
		$ref =& $_SESSION['report_tempdata']['rows_filter'];
		$selector = new Selector_Course();

		if (isset($_POST['update_tempdata'])) {
			$selector->parseForState($_POST);
			$ref['all_courses'] = (get_req('all_courses', DOTY_INT, 1)==1 ? true : false);
		}	else	{
			$selector->resetSelection($ref['selected_courses']);
		}

		//filter setting done, go to next step
		if (isset($_POST['import_filter'])) {
			$ref['selected_courses'] = $selector->getSelection($_POST);
			jumpTo($next_url);
		}

		$ref =& $_SESSION['report_tempdata']['rows_filter'];
		$temp = count($ref['selected_courses']);


		$box = new ReportBox('courses_selector');
		$box->title = $this->lang->def('_COURSES_SELECTION_TITLE');
		$box->description = $this->lang->def('_COURSES_SELECTION_DESC');

		$boxlang =& DoceboLanguage::createInstance('report', 'framework');
		$box->body .= '<div class="fc_filter_line filter_corr">';
		$box->body .= '<input id="all_courses" name="all_courses" type="radio" value="1" '.($ref['all_courses'] ? 'checked="checked"' : '').' />';
		$box->body .= '<label for="all_courses">'.$boxlang->def('_ALL_COURSES').'</label>';
		$box->body .= '<input id="sel_courses" name="all_courses" type="radio" value="0" '.($ref['all_courses'] ? '' : 'checked="checked"').' />';
		$box->body .= '<label for="sel_courses">'.$boxlang->def('_SEL_COURSES').'</label>';
		$box->body .= '</div>';
		$box->body .= '<div id="selector_container"'.($ref['all_courses'] ? ' style="display:none"' : '').'>';
		$box->body .= $selector->loadCourseSelector(true).'</div>';

		$box->footer = $boxlang->def('_CURRENT_SELECTION').': <span id="csel_foot">'.($ref['all_courses'] ? $boxlang->def('_ALLCOURSES_FOOTER') : ($temp!='' ? $temp : '0')).'</span>';

		addYahooJs(array(
			'yahoo'           => 'yahoo-min.js',
			'yahoo-dom-event' => 'yahoo-dom-event.js',
			'element'         => 'element-beta-min.js',
			'datasource'      => 'datasource-beta-min.js',
			'connection'      => 'connection-min.js',
			'event'           => 'event-min.js',
			'json'            => 'json-beta-min.js'
			), array(
			'/assets/skins/sam' => 'skin.css'
			));
		addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/','courses_filter.js');

		cout('<script type="text/javascript"> '."\n".
		'var courses_count="'.($temp!='' ? $temp : '0').'";'."\n".
		'var courses_all="'.$boxlang->def('_ALLCOURSES_FOOTER').'";'."\n".
		'YAHOO.util.Event.addListener(window, "load", function(e){ courses_selector_init(); });'."\n".
		'</script>', 'page_head');

		cout(
			Form::openForm('first_step_user_filter', $jump_url, false, 'post').
			$box->get().
			Form::getHidden('update_tempdata', 'update_tempdata', 1).
			Form::openButtonSpace().
			//Form::getBreakRow().
			Form::getButton('ok_filter', 'import_filter', $lang->def('_NEXT')).
			Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO')).
			Form::closeButtonSpace().
			Form::closeForm() );
	}


	function get_user_filter()
	{
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.directory.php');
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$org_chart_subdivision 	= importVar('org_chart_subdivision', true, 0);

		//detect the step in which we are
		$substep = _SUBSTEP_USERS; //first substep
		switch (get_req('substep', DOTY_STRING, 'no_step')) {
			case 'users_selection' : $substep = _SUBSTEP_USERS; break;
			case 'columns_selection' :$substep =_SUBSTEP_COLUMNS; break;
		}

		//draw page depending on the $substep variable
		if (!isset($_SESSION['report_tempdata']['columns_filter']))
			$_SESSION['report_tempdata']['columns_filter'] = array(
				'time_belt' => array('time_range'=> '', 'start_date'=>'', 'end_date'=>''),
				'org_chart_subdivision' => 0,
				'showed_cols' => array(),
				'show_percent'=> true
			);
		$ref = &$_SESSION['report_tempdata']['columns_filter'];
		
		switch ($substep) {

			case _SUBSTEP_COLUMNS: {
				//set session data
				if(get_req('is_updating', DOTY_INT, 0)>0)	{
					$ref['showed_cols'] = get_req('cols', DOTY_MIXED, array());
					$ref['show_percent'] = (get_req('show_percent', DOTY_INT, 0)>0 ? true : false);
					$ref['time_belt'] = array(	'time_range'=>$_POST['time_belt'],
												'start_date' => $GLOBALS['regset']->regionalToDatabase($_POST['start_time'], 'date'),
												'end_date' => $GLOBALS['regset']->regionalToDatabase($_POST['end_time'], 'date'));
					$ref['org_chart_subdivision'] = (isset($_POST['org_chart_subdivision']) ? 1 : 0);
				} else {
					//...
				}

				//check action
				if(isset($_POST['cancelselector']))
					jumpTo($jump_url.'&substep=users_selection');

				if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
					$temp_url = $next_url;
					if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
					if (isset($_POST['show_filter'])) $temp_url.='&show=1';
					jumpTo($temp_url);
				}

				cout($this->page_title);

				function is_showed($which, &$arr) {
					if(isset($arr['showed_cols'])) return in_array($which, $arr['showed_cols']);
					else return false;
				}

				/*$go_to_second_step = (isset($_POST['go_to_second_step']) ? true : false);
				$we_are_in_second_step = get_req('second_step', DOTY_INT, false);*/

				$time_belt = array(
					0 		=> $lang->def('_CUSTOM_BELT'),
					7 		=> $lang->def('_LAST_WEEK'),
					31		=> $lang->def('_LAST_MONTH'),
					93 		=> $lang->def('_LAST_THREE_MONTH'),
					186 	=> $lang->def('_LAST_SIX_MONTH'),
					365 	=> $lang->def('_LAST_YEAR'),);

				cout(
					Form::openForm('user_report_rows_courses', $jump_url).
					Form::getHidden('update_tempdata', 'update_tempdata', 1).
					Form::getHidden('is_updating', 'is_updating', 1).
					Form::getHidden('substep', 'substep', 'columns_selection'));

				//box for time belt
				$box = new ReportBox('timebelt_box');
				$box->title = $lang->def('_REPORT_USER_TITLE_TIMEBELT');
				$box->description = $lang->def('_REPORT_USER_TITLE_TIMEBELT_DESC');
				$box->body =
				Form::getDropdown($lang->def('_TIME_BELT'), 'time_belt_'.$this->id_report, 'time_belt', $time_belt, (isset($_SESSION['report_tempdata']['columns_filter']['time_belt']['time_range']) ? $_SESSION['report_tempdata']['columns_filter']['time_belt']['time_range'] : ''), '', '' ,
						' onchange="report_disableCustom( \'time_belt_'.$this->id_report.'\', \'start_time_'.$this->id_report.'\', \'end_time_'.$this->id_report.'\' )"')

				.Form::getOpenFieldset($lang->def('_CUSTOM_BELT'), 'fieldset_'.$this->id_report)
				.Form::getDatefield($lang->def('_START_TIME'), 'start_time_'.$this->id_report, 'start_time',
					$GLOBALS['regset']->databaseToRegional($ref['time_belt']['start_date'], 'date') )
				.Form::getDatefield($lang->def('_END_TIME'), 'end_time_'.$this->id_report, 'end_time',
					$GLOBALS['regset']->databaseToRegional($ref['time_belt']['end_date'], 'date') )
				.Form::getCloseFieldset()

				.Form::getCheckbox(	$lang->def('ORG_CHART_SUBDIVISION'),
										'org_chart_subdivision_'.$this->id_report,
										'org_chart_subdivision',
					1, ($ref['org_chart_subdivision']==1 ? true : false))
				.Form::getBreakRow();

				cout($box->get().Form::getBreakRow());

				$glang =& DoceboLanguage::createInstance('admin_course_managment', 'lms');

				$box = new ReportBox('columns_sel_box');
				//$box->title = $lang->def('_REPORT_COURSES_SELCOLUMNS');
				//$box->description = $lang->def('_REPORT_COURSES_SELCOLUMNS_DESC');
				$box->title = $lang->def('_SELECT_COLUMS');
				$box->description = $lang->def('_SELECT_THE_DATA_COL_NEEDED');
				$box->body = Form::getHidden('is_updating', 'is_updating', 2)

				//.Form::openElementSpace()
				.Form::getOpenFieldset($lang->def('_COURSE_FIELDS'), 'fieldset_course_fields')
				.Form::getCheckBox($lang->def('_COURSE_CODE'), 'col_sel_coursecode', 'cols[]', '_CODE_COURSE', is_showed('_CODE_COURSE', $ref))
				.Form::getCheckBox($glang->def('_COURSE_NAME'), 'col_sel_coursename', 'cols[]', '_NAME_COURSE', is_showed('_NAME_COURSE', $ref))
				.Form::getCheckBox($glang->def('_CATEGORY_COURSE'), 'col_sel_category', 'cols[]', '_COURSE_CATEGORY', is_showed('_COURSE_CATEGORY', $ref))
				.Form::getCheckBox($glang->def('_COURSESTATUS'), 'col_sel_status', 'cols[]', '_COURSESTATUS', is_showed('_COURSESTATUS', $ref))
				.Form::getCheckBox($glang->def('_COURSECATALOGUE'), 'col_sel_catalogue', 'cols[]', '_COURSECATALOGUE', is_showed('_COURSECATALOGUE', $ref))
				.Form::getCheckBox($glang->def('_PUBLICATION_DATE'), 'col_sel_publication', 'cols[]', '_PUBLICATION_DATE', is_showed('_PUBLICATION_DATE', $ref))
				.Form::getCloseFieldset()

				.Form::getOpenFieldset($lang->def('_COURSE_FIELDS_INFO'), 'fieldset_course_fields')
				.Form::getCheckBox($glang->def('_COURSE_LANG_METHOD'), 'col_course_lang_method', 'cols[]', '_LANGUAGE', is_showed('_LANGUAGE', $ref))
				.Form::getCheckBox($glang->def('_DIFFICULT'), 'col_course_difficult', 'cols[]', '_DIFFICULT', is_showed('_DIFFICULT', $ref))
				.Form::getCheckBox($glang->def('_DATE_BEGIN'), 'col_date_begin', 'cols[]', '_DATE_BEGIN', is_showed('_DATE_BEGIN', $ref))
				.Form::getCheckBox($glang->def('_DATE_END'), 'col_date_end', 'cols[]', '_DATE_END', is_showed('_DATE_END', $ref))
				.Form::getCheckBox($glang->def('_HOUR_BEGIN'), 'col_time_begin', 'cols[]', '_TIME_BEGIN', is_showed('_TIME_BEGIN', $ref))
				.Form::getCheckBox($glang->def('_HOUR_END'), 'col_time_end', 'cols[]', '_TIME_END', is_showed('_TIME_END', $ref))
				.Form::getCheckBox($glang->def('_MAX_NUM_SUBSCRIBE'), 'col_max_num_subscribe', 'cols[]', '_MAX_NUM_SUBSCRIBED', is_showed('_MAX_NUM_SUBSCRIBED', $ref))
				.Form::getCheckBox($glang->def('_MIN_NUM_SUBSCRIBE'), 'col_min_num_subscribe', 'cols[]', '_MIN_NUM_SUBSCRIBED', is_showed('_MIN_NUM_SUBSCRIBED', $ref))
				.Form::getCheckBox($glang->def('_COURSE_PRIZE'), 'col_course_price', 'cols[]', '_PRICE', is_showed('_PRICE', $ref))
				.Form::getCheckBox($glang->def('_COURSE_ADVANCE'), 'col_course_advance', 'cols[]', '_ADVANCE', is_showed('_ADVANCE', $ref))
				.Form::getCheckBox($glang->def('_COURSE_TYPE'), 'col_course_type', 'cols[]', '_COURSE_TYPE', is_showed('_COURSE_TYPE', $ref))
				.Form::getCheckBox($glang->def('_COURSE_AUTOREGISTRATION_CODE'), 'col_autoregistration_code', 'cols[]', '_AUTOREGISTRATION_CODE', is_showed('_AUTOREGISTRATION_CODE', $ref))
				.Form::getCloseFieldset()

				.Form::getOpenFieldset($lang->def('_STATS_FIELDS_INFO'), 'fieldset_course_fields')
				.Form::getCheckBox($lang->def('_INSCR'), 'col_inscr', 'cols[]', '_INSCR', is_showed('_INSCR', $ref))
				.Form::getCheckBox($lang->def('_MUSTBEGIN'), 'col_mustbegin', 'cols[]', '_MUSTBEGIN', is_showed('_MUSTBEGIN', $ref))
				.Form::getCheckBox($lang->def('_USER_STATUS_BEGIN'), 'col_user_status_begin', 'cols[]', '_USER_STATUS_BEGIN', is_showed('_USER_STATUS_BEGIN', $ref))
				.Form::getCheckBox($lang->def('_COMPLETECOURSE'), 'col_completecourse', 'cols[]', '_COMPLETECOURSE', is_showed('_COMPLETECOURSE', $ref))
				.Form::getCheckBox($lang->def('_TOTAL_SESSION'), 'col_total_session', 'cols[]', '_TOTAL_SESSION', is_showed('_TOTAL_SESSION', $ref))
				.Form::getBreakRow()
				.Form::getCheckBox($lang->def('_SHOW_PERCENTAGES'), 'show_percent', 'show_percent', '1', $ref['show_percent'])
				.Form::getCloseFieldset();

				cout($box->get());

				cout(	Form::openButtonSpace()
					.Form::getBreakRow()
					.Form::getButton('pre_filter', 'pre_filter', $lang->def('_SHOW_NOSAVE', 'report'))
					.Form::getButton('ok_filter', 'import_filter', $lang->def('_SAVE_BACK'))
					.Form::getButton('show_filter', 'show_filter', $lang->def('_SAVE_SHOW'))
					.Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO'))
					.Form::closeButtonSpace()
					.Form::closeForm());
				cout('</div>'); //stdblock div

			} break;

			case _SUBSTEP_USERS: {
				//$aclManager = new DoceboACLManager();
				$user_select = new Module_Directory();

				if (get_req('is_updating', DOTY_INT, 0)>0) {
					$ref['all_users'] = ( get_req('all_users', DOTY_INT, 0)>0 ? true : false );
				} else { //maybe redoundant
					if (!isset($ref['all_users'])) $ref['all_users'] = false;
					if (!isset($ref['users'])) $ref['users'] = array();
					$user_select->requested_tab = PEOPLEVIEW_TAB;
					$user_select->resetSelection($ref['users']);
					//$ref['users'] = array(); it should already have been set to void array, if non existent
				}

				if(isset($_POST['cancelselector'])) {
					jumpTo($back_url);
				} elseif(isset($_POST['okselector'])) {
					$elem_selected 	= $user_select->getSelection($_POST);
					$ref['all_users'] = ( get_req('all_users', DOTY_INT, 0)>0 ? true : false );
					$ref['users'] = $elem_selected;
					jumpTo($jump_url.'&substep=columns_selection' );
				}
				
				//set page
				if($org_chart_subdivision == 0) {
					$user_select->show_user_selector = TRUE;
					$user_select->show_group_selector = TRUE;
				} else {
					$user_select->show_user_selector = FALSE;
					$user_select->show_group_selector = FALSE;
				}
				$user_select->show_orgchart_selector = TRUE;
				//$user_select->show_orgchart_simple_selector = FALSE;
				//$user_select->multi_choice = TRUE;

				$user_select->addFormInfo(
					Form::getCheckbox($lang->def('_REPORT_FOR_ALL'), 'all_users', 'all_users', 1, ($ref['all_users'] ? 1 : 0)).
					Form::getBreakRow().
					Form::getHidden('org_chart_subdivision', 'org_chart_subdivision', $org_chart_subdivision).
					Form::getHidden('is_updating', 'is_updating', 1).
					Form::getHidden('substep', 'substep', 'user_selection').
					Form::getHidden('second_step', 'second_step', 1)
				);

				$user_select->setPageTitle($this->page_title);
				$user_select->loadSelector(str_replace('&', '&amp;', $jump_url),
					false,
					$this->lang->def('_CHOOSE_USER_FOR_REPORT'),
					true,
					true );

			} break;

		}

	}

	function show_report_user($report_data = NULL, $other = '')
	{
		if ($report_data===NULL)
			cout( $this->_get_users_query() );
		else
			cout( $this->_get_users_query('html', $report_data, $other) );
	}

	function _get_users_query($type='html', $report_data = NULL, $other = '') {
	
		//$jump_url, $alluser, $org_chart_subdivision, $start_time, $end_time
		if ($report_data==NULL) $ref =& $_SESSION['report_tempdata'];
		else $ref =& $report_data;
		$time_range 			= $ref['columns_filter']['time_belt']['time_range'];
		$start_time 			= $ref['columns_filter']['time_belt']['start_date'];
		$end_time 				= $ref['columns_filter']['time_belt']['end_date'];
		$org_chart_subdivision 	= $ref['columns_filter']['org_chart_subdivision'];
		$filter_cols			= $ref['columns_filter']['showed_cols'];
		$show_percent     = (isset($ref['columns_filter']['show_percent']) ? $ref['columns_filter']['show_percent'] : true);

		if($time_range != 0) {
			$start_time = date("Y-m-d H:i:s", time() - $time_range*24*3600);
			$end_time = date("Y-m-d H:i:s");
		} else {
			$start_time = $start_time;
			$end_time 	= $end_time;
		}
		$alluser = $ref['columns_filter']['all_users'];

		$output = '';

		$lang =& DoceboLanguage::createInstance('course', 'framework');

		require_once($GLOBALS['where_framework'].'/lib/lib.directory.php');
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');

		$acl_man 		= new DoceboACLManager();
		$acl_man->include_suspended = TRUE;
		$course_man 	= new Man_Course();

		if($alluser == 0)
			$user_selected = $acl_man->getAllUsersFromIdst($ref['columns_filter']['users']);
		else
		{
			$user_level = $GLOBALS['current_user']->getUserLevelId();
			
			if($user_level != ADMIN_GROUP_GODADMIN)
			{
				require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');
				
				$adminManager = new AdminManager();
				$acl_manager = new DoceboACLManager();
				
				$idst_associated = $adminManager->getAdminTree(getLogUserId());
				$user_selected =& $acl_manager->getAllUsersFromIdst($idst_associated);
			}
			else
				$user_selected =& $acl_man->getAllUsersIdst();
		}
		
		if($org_chart_subdivision == 1) {

			require_once($GLOBALS['where_framework'].'/lib/lib.orgchart.php');
			$org_man 	= new OrgChartManager();
			if($alluser == 1)
			{
				$user_level = $GLOBALS['current_user']->getUserLevelId();
				
				if($user_level != ADMIN_GROUP_GODADMIN)
					$elem_selected = $user_selected;
				else
					$elem_selected = $org_man->getAllGroupIdFolder();
			}
			$org_name = $org_man->getFolderFormIdst($elem_selected);

			$userlevelid = $GLOBALS['current_user']->getUserLevelId();
			if( $userlevelid != ADMIN_GROUP_GODADMIN ) {
				require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');
				$adminManager = new AdminManager();
				$user_filter = $adminManager->getAdminTree($GLOBALS['current_user']->getIdSt());
				$user_filter = array_flip($user_filter);

				$org_name_temp = $org_name;
				$org_name  = array();
				foreach($org_name_temp as $id => $value) {

					if(isset($user_filter[$id])) $org_name[$id] = $value;
				}
			}

		} else {
			$elem_selected = array();
		}

		if(empty($user_selected)) {

			$GLOBALS['page']->add($lang->def('_NULL_SELECTION'), 'content');
			return;
		}

		// Retrive all the course
		$id_courses = $course_man->getAllCourses();
		if(empty($id_courses)) {

			$GLOBALS['page']->add($lang->def('_NULL_COURSE_SELECTION'), 'content');
			return;
		}
		
		if($org_chart_subdivision == 0) {

			$date_now = $GLOBALS['regset']->databaseToRegional(date("Y-m-d H:i:s"));

			$all_courses = $ref['rows_filter']['all_courses'];
			$course_selected =& $ref['rows_filter']['selected_courses'];

			$query_course_user = "
			SELECT cu.idUser, cu.idCourse, cu.date_first_access, cu.date_complete, cu.status
			FROM ".$GLOBALS['prefix_lms']."_courseuser AS cu
			WHERE cu.idUser IN ( ".implode(',', $user_selected)." ) ".
			($all_courses ? "" : "AND cu.idCourse IN (".implode(',', $course_selected).")");
			if($start_time != '' && $start_time != '0000-00-00') $query_course_user .= " AND cu.date_complete >= '".$start_time."' ";
			if($end_time != '' && $end_time != '0000-00-00') $query_course_user .= " AND cu.date_complete <= '".$end_time."'";

			$num_iscr 		= array();
			$num_nobegin 	= array();
			$num_itinere 	= array();
			$num_end 		= array();
			$time_in_course = array();
			$effective_user = array();

			$re_course_user = mysql_query($query_course_user);
			while(list($id_u, $id_c, $fisrt_access, $date_complete, $status) = mysql_fetch_row($re_course_user)) {

				if(isset($num_iscr[$id_c])) ++$num_iscr[$id_c];
				else $num_iscr[$id_c] = 1;
				switch($status) {
					case _CUS_CONFIRMED : {};break;
					case _CUS_SUSPEND : {};break;
					case _CUS_SUBSCRIBED : {
						if(isset($num_nobegin[$id_c])) ++$num_nobegin[$id_c];
						else $num_nobegin[$id_c] = 1;
					};break;
					case _CUS_BEGIN : {
						if(isset($num_itinere[$id_c])) ++$num_itinere[$id_c];
						else $num_itinere[$id_c] = 1;
					};break;
					case _CUS_END : {
						if(isset($num_end[$id_c])) ++$num_end[$id_c];
						else $num_end[$id_c] = 1;
					};break;
				}

				$effective_user[] = $id_u;
			}
			if(!empty($effective_user)) {

				$query_time = "
					SELECT idCourse, SUM(UNIX_TIMESTAMP(lastTime) - UNIX_TIMESTAMP(enterTime))
					FROM ".$GLOBALS['prefix_lms']."_tracksession
					WHERE  idUser IN ( ".implode(',', $effective_user)." )  ";
				if($start_time != '' && $start_time != '0000-00-00') $query_time .= " AND enterTime >= '".$start_time."' ";
				if($end_time != '' && $end_time != '0000-00-00') $query_time .= " AND enterTime <= '".$end_time."' ";
				$query_time .= " GROUP BY idCourse ";

				$re_time = mysql_query($query_time);

				while(list($id_c, $time_num) = mysql_fetch_row($re_time)) {

					$time_in_course[$id_c] = $time_num;
				}
			}

			$output .= $this->_printTable_users($type, $acl_man, $id_courses, $num_iscr , $num_nobegin, $num_itinere, $num_end, $time_in_course, $filter_cols, $show_percent);
			
		} else {

			$date_now = $GLOBALS['regset']->databaseToRegional(date("Y-m-d H:i:s"));

			$course_selected = $ref['rows_filter']['selected_courses'];

			reset($org_name);
			while(list($idst_group, $folder_name) = each($org_name)) {

				$GLOBALS['page']->add('<div class="datasummary">'
					.'<b>'.$lang->def('_FOLDER_NAME').' :</b> '.$folder_name['name']
					.( $folder_name['type_of_folder'] == ORG_CHART_WITH_DESCENDANTS ? ' ('.$lang->def('_WITH_DESCENDANTS').')' : '' ).'<br />'
					, 'content');
				if(($start_time != '' && $start_time != '0000-00-00') || ($end_time != '' && $end_time != '0000-00-00')) {

					$GLOBALS['page']->add('<b>'.$lang->def('_TIME_BELT_2').' :</b> '
						.( $start_time != '' && $start_time != '0000-00-00'
							? ' <b>'.$lang->def('_START_TIME').' </b>'.$GLOBALS['regset']->databaseToRegional($start_time, 'date')
							: '' )
						.( $end_time != '' && $end_time != '0000-00-00'
							? ' <b>'.$lang->def('_END_TIME').' </b>'.$GLOBALS['regset']->databaseToRegional($end_time, 'date')
							: '' )
						.'<br />'
						, 'content');
				}


				$group_user = $acl_man->getGroupAllUser($idst_group);

				$query_course_user = "
				SELECT cu.idUser, cu.idCourse, cu.date_first_access, cu.date_complete
				FROM ".$GLOBALS['prefix_lms']."_courseuser AS cu
				WHERE cu.idUser IN ( ".implode(',', $group_user)." )
				AND cu.idCourse IN (".implode(',', $course_selected).")";
				if($start_time != '' && $start_time != '0000-00-00') $query_course_user .= " AND cu.date_complete >= '".$start_time."' ";
				if($end_time != '' && $end_time != '0000-00-00') $query_course_user .= " AND cu.date_complete <= '".$end_time."'  AND cu.level='3'";

				$num_iscr 		= array();
				$num_nobegin 	= array();
				$num_itinere 	= array();
				$num_end 		= array();
				$time_in_course = array();
				$effective_user = array();

				$re_course_user = mysql_query($query_course_user);
				while(list($id_u, $id_c, $fisrt_access, $date_complete) = mysql_fetch_row($re_course_user)) {

					if(isset($num_iscr[$id_c])) ++$num_iscr[$id_c];
					else $num_iscr[$id_c] = 1;

					if($fisrt_access === NULL) {
						//never enter
						if(isset($num_nobegin[$id_c])) ++$num_nobegin[$id_c];
						else $num_nobegin[$id_c] = 1;
					} elseif($date_complete === NULL) {
						//enter
						if(isset($num_itinere[$id_c])) ++$num_itinere[$id_c];
						else $num_itinere[$id_c] = 1;
					} else {
						//complete
						if(isset($num_end[$id_c])) ++$num_end[$id_c];
						else $num_end[$id_c] = 1;
					}
					$effective_user[] = $id_u;
				}
				if(!empty($group_user)) {

					$query_time = "
					SELECT idCourse, SUM(UNIX_TIMESTAMP(lastTime) - UNIX_TIMESTAMP(enterTime))
					FROM ".$GLOBALS['prefix_lms']."_tracksession
					WHERE  idUser IN ( ".implode(',', $group_user)." )  ";
					if($start_time != '' && $start_time != '0000-00-00') $query_time .= " AND enterTime >= '".$start_time."' ";
					if($end_time != '' && $end_time != '0000-00-00') $query_time .= " AND enterTime <= '".$end_time."' ";
					$query_time .= " GROUP BY idCourse ";

					$re_time = mysql_query($query_time);
					while(list($id_c, $time_num) = mysql_fetch_row($re_time)) {

						$time_in_course[$id_c] = $time_num;
					}
				}
				reset($id_courses);

				$output .= $this->_printTable_users($type, $acl_man, $id_courses, $num_iscr , $num_nobegin, $num_itinere, $num_end, $time_in_course, $filter_cols, $show_percent);
				/*switch ($type) {
					case 'html': {
						$output .= $this->_printTable_users('html', $acl_man, $id_courses, $num_iscr , $num_nobegin, $num_itinere, $num_end, $time_in_course, $filter_cols);
					} break;
					case 'csv': {
						$output .= $this->_printTable_users('csv', $acl_man, $id_courses, $num_iscr , $num_nobegin, $num_itinere, $num_end, $time_in_course, $filter_cols);
					} break;
					case 'xls': {
						//$output .= $this->_printXlsTable_users($acl_man, $id_courses, $num_iscr , $num_nobegin, $num_itinere, $num_end, $time_in_course, $filter_cols);
						$output .= ''; //yet not available
					} break;
			}*/

			}
		}

		return $output;
	}

	function _printTable_users($type, &$acl_man, &$id_courses, &$num_iscr , &$num_nobegin, &$num_itinere, &$num_end, &$time_in_course, $filter_cols, $show_percent) {
		require_once('report_tableprinter.php');
		$buffer = new ReportTablePrinter($type);

		$output = '';

		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$lang =& DoceboLanguage::createInstance('admin_course_managment', 'lms');
		$course_lang =& DoceboLanguage::createInstance('course', 'lms');
		$rg_lang =& DoceboLanguage::createInstance('report', 'framework');

		$colspan_course = 0;
		if(in_array('_CODE_COURSE', $filter_cols)) $colspan_course++;
		if(in_array('_NAME_COURSE', $filter_cols)) $colspan_course++;
		if(in_array('_COURSE_CATEGORY', $filter_cols)) $colspan_course++;
		if(in_array('_COURSESTATUS', $filter_cols)) $colspan_course++;

		if(in_array('_COURSECATALOGUE', $filter_cols)) $colspan_course++;
		if(in_array('_PUBLICATION_DATE', $filter_cols)) $colspan_course++;

		if(in_array('_LANGUAGE', $filter_cols)) $colspan_course++;
		if(in_array('_DIFFICULT', $filter_cols)) $colspan_course++;
		if(in_array('_DATE_BEGIN', $filter_cols)) $colspan_course++;
		if(in_array('_DATE_END', $filter_cols)) $colspan_course++;
		if(in_array('_TIME_BEGIN', $filter_cols)) $colspan_course++;
		if(in_array('_TIME_END', $filter_cols)) $colspan_course++;
		if(in_array('_MAX_NUM_SUBSCRIBED', $filter_cols)) $colspan_course++;
		if(in_array('_MIN_NUM_SUBSCRIBED', $filter_cols)) $colspan_course++;
		if(in_array('_PRICE', $filter_cols)) $colspan_course++;
		if(in_array('_ADVANCE', $filter_cols)) $colspan_course++;
		if(in_array('_COURSE_TYPE', $filter_cols)) $colspan_course++;
		if(in_array('_AUTOREGISTRATION_CODE', $filter_cols)) $colspan_course++;

		$colspan_stats = 0;
		if(in_array('_INSCR', $filter_cols)) $colspan_stats++;
		if(in_array('_MUSTBEGIN', $filter_cols)) $colspan_stats += ($show_percent ? 2 : 1);
		if(in_array('_USER_STATUS_BEGIN', $filter_cols)) $colspan_stats += ($show_percent ? 2 : 1);
		if(in_array('_COMPLETECOURSE', $filter_cols)) $colspan_stats += ($show_percent ? 2 : 1);

		$buffer->openTable($rg_lang->def('_RG_CAPTION'), $rg_lang->def('_RG_SUMMAMRY_MANAGMENT'));

		$th1 = array(
			array('colspan'=>$colspan_course, 'value'=>$lang->def('_COURSE')),
			array('colspan'=>$colspan_stats,  'value'=>$rg_lang->def('_USERS'))
		);
		//if (in_array('_TOTAL_SESSION', $filter_cols)) $th1[] = array( 'rowspan'=>3, 'value'=>$rg_lang->def('_TOTAL_SESSION'));

		$th2 = array( array('colspan'=>$colspan_course, 'value'=>'') );  //rowspan?
		if (in_array('_INSCR', $filter_cols)) $th2[] = $rg_lang->def('_INSCR');
		if (in_array('_MUSTBEGIN', $filter_cols)) $th2[] = array('colspan'=>($show_percent ? 2 : 1), 'value'=>$rg_lang->def('_MUSTBEGIN'));
		if (in_array('_USER_STATUS_BEGIN', $filter_cols)) $th2[] = array('colspan'=>($show_percent ? 2 : 1), 'value'=>$rg_lang->def('_USER_STATUS_BEGIN'));
		if (in_array('_COMPLETECOURSE', $filter_cols)) $th2[] = array('colspan'=>($show_percent ? 2 : 1), 'value'=>$rg_lang->def('_COMPLETECOURSE'));

		$th3 = array();

		if (in_array('_CODE_COURSE', $filter_cols)) $th3[] = $lang->def('_COURSE_CODE');
		if (in_array('_NAME_COURSE', $filter_cols)) $th3[] = $lang->def('_COURSE_NAME');
		if (in_array('_COURSE_CATEGORY', $filter_cols)) $th3[] = $lang->def('_COURSE_CATEGORY');
		if (in_array('_COURSESTATUS', $filter_cols)) $th3[] = $lang->def('_COURSE_STATUS');

		if(in_array('_COURSECATALOGUE', $filter_cols)) $th3[] = $lang->def('_COURSECATALOGUE');
		if(in_array('_PUBLICATION_DATE', $filter_cols)) $th3[] = $lang->def('_PUBLICATION_DATE');

		if (in_array('_LANGUAGE', $filter_cols)) $th3[] = $lang->def('_COURSE_LANG_METHOD');
		if (in_array('_DIFFICULT', $filter_cols)) $th3[] = $lang->def('_DIFFICULT');
		if (in_array('_DATE_BEGIN', $filter_cols)) $th3[] = $lang->def('_DATE_BEGIN');
		if (in_array('_DATE_END', $filter_cols)) $th3[] = $lang->def('_DATE_END');
		if (in_array('_TIME_BEGIN', $filter_cols)) $th3[] = $lang->def('_HOUR_BEGIN');
		if (in_array('_TIME_END', $filter_cols)) $th3[] = $lang->def('_HOUR_END');
		if (in_array('_MAX_NUM_SUBSCRIBED', $filter_cols)) $th3[] = $lang->def('_MAX_NUM_SUBSCRIBE');
		if (in_array('_MIN_NUM_SUBSCRIBED', $filter_cols)) $th3[] = $lang->def('_MIN_NUM_SUBSCRIBE');
		if (in_array('_PRICE', $filter_cols)) $th3[] = $lang->def('_COURSE_PRIZE');
		if (in_array('_ADVANCE', $filter_cols)) $th3[] = $lang->def('_COURSE_ADVANCE');
		if (in_array('_COURSE_TYPE', $filter_cols)) $th3[] = $lang->def('_COURSE_TYPE');
		if (in_array('_AUTOREGISTRATION_CODE', $filter_cols)) $th3[] = $lang->def('_AUTOREGISTRATION_CODE');

		if (in_array('_INSCR', $filter_cols)) $th3[] = $rg_lang->def('_NUM');
		if (in_array('_MUSTBEGIN', $filter_cols)) { $th3[] = $rg_lang->def('_NUM','report', 'framework'); if ($show_percent) $th3[] = $rg_lang->def('_PERC'); }
		if (in_array('_USER_STATUS_BEGIN', $filter_cols)) { $th3[] = $rg_lang->def('_NUM','report', 'framework'); if ($show_percent) $th3[] = $rg_lang->def('_PERC'); }
		if (in_array('_COMPLETECOURSE', $filter_cols)) { $th3[] = $rg_lang->def('_NUM','report', 'framework'); if ($show_percent) $th3[] = $rg_lang->def('_PERC'); }


		if (in_array('_TOTAL_SESSION', $filter_cols)) {
			$th1[] = $rg_lang->def('_TOTAL_SESSION');
			$th2[] = '';
			$th3[] = '';
		}

		$buffer->openHeader();
		$buffer->addHeader($th1);
		$buffer->addHeader($th2);
		$buffer->addHeader($th3);
		$buffer->closeHeader();

		$i = 0;
		$tot_iscr = $tot_itinere = $tot_nobegin = $tot_comple = '';
		$tot_perc_itinere = $tot_perc_nobegin = $tot_perc_comple = '';
		$total_time = 0;

		$array_status = array(	CST_PREPARATION => $lang->def('_CST_PREPARATION', 'admin_course_managment', 'lms'),
			CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE', 'admin_course_managment', 'lms'),
			CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED', 'admin_course_managment', 'lms'),
			CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED', 'admin_course_managment', 'lms'),
			CST_CANCELLED 	=> $lang->def('_CST_CANCELLED', 'admin_course_managment', 'lms') );

		//extract course categories
		$query =	"SELECT idCategory, path"
		." FROM ".$GLOBALS['prefix_lms']."_category";
		$result = mysql_query($query);
		$array_category = array(0 => $lang->def('_ROOT_CATEGORY'));
		while(list($id_cat, $name_cat) = mysql_fetch_row($result))
			$array_category[$id_cat] = substr($name_cat, 5, (strlen($name_cat)-5));//strrpos($name_cat, '/') + 1 );


		//extract course catalogues and relations
		$query =	"SELECT idCatalogue, name"
		." FROM ".$GLOBALS['prefix_lms']."_catalogue";
		$result = mysql_query($query);
		$array_catalogue = array();
		while(list($id_cat, $name_cat) = mysql_fetch_row($result))
			$array_catalogue[$id_cat] = $name_cat;//strrpos($name_cat, '/') + 1 );


		$catalogue_entries = array();
		$query = "select * FROM ".$GLOBALS['prefix_lms']."_catalogue_entry "; //where idst_member in (...)
		$result = mysql_query($query);
		while (list($idcat, $entry, $type) = mysql_fetch_row($result)) {
			switch ($type) {
				case 'course': {
					if (!isset($catalogue_entries[$entry])) $catalogue_entries[$entry] = array();
					$catalogue_entries[$entry][] = $idcat;
				} break;
				
				case 'coursepath': {
					//...
				} break;
			} //end switch
		}

		while(list($idc, $course_info) = each($id_courses) ) {

			$course_man = new Man_Course();

			$info_course = $course_man->getCourseInfo($idc);
			
			$code_c = $course_info['code'];
			$name_c = $course_info['name'];

			
			$trow = array();
			if (in_array('_CODE_COURSE', $filter_cols)) $trow[] = addslashes($code_c);
			if (in_array('_NAME_COURSE', $filter_cols)) $trow[] = addslashes($name_c);
			if (in_array('_COURSE_CATEGORY', $filter_cols)) $trow[] = $array_category[$info_course['idCategory']];
			if (in_array('_COURSESTATUS', $filter_cols)) $trow[] = (isset($array_status[$info_course['status']]) ? $array_status[$info_course['status']] : '');

			if(in_array('_COURSECATALOGUE', $filter_cols)) {
				$temp = array();
				if (isset($catalogue_entries[$info_course['idCourse']])) {
					foreach ($catalogue_entries[$info_course['idCourse']] as $idcat)
						$temp[] = $array_catalogue[$idcat];
				}
				$trow[] = implode(', ', $temp);
			}
			if(in_array('_PUBLICATION_DATE', $filter_cols)) $trow[] = $GLOBALS['regset']->databaseToRegional($info_course['create_date']);

			if (in_array('_LANGUAGE', $filter_cols)) $trow[] = $info_course['lang_code'];
			if (in_array('_DIFFICULT', $filter_cols)) $trow[] = $info_course['difficult'];
			if (in_array('_DATE_BEGIN', $filter_cols)) $trow[] = $GLOBALS['regset']->databaseToRegional($info_course['date_begin']);
			if (in_array('_DATE_END', $filter_cols)) $trow[] = $GLOBALS['regset']->databaseToRegional($info_course['date_end']);
			if (in_array('_TIME_BEGIN', $filter_cols)) $trow[] = ($info_course['hour_begin']<0 ? '' : $info_course['hour_begin']);
			if (in_array('_TIME_END', $filter_cols)) $trow[] = ($info_course['hour_end']<0 ? '' : $info_course['hour_end']);
			if (in_array('_MAX_NUM_SUBSCRIBED', $filter_cols)) $trow[] = ($info_course['max_num_subscribe'] ? $info_course['max_num_subscribe'] : '');
			if (in_array('_MIN_NUM_SUBSCRIBED', $filter_cols)) $trow[] = ($info_course['min_num_subscribe'] ? $info_course['min_num_subscribe'] : '');
			if (in_array('_PRICE', $filter_cols)) $trow[] = ($info_course['prize'] != '' ? $info_course['prize'] : '0');
			if (in_array('_ADVANCE', $filter_cols)) $trow[] = ($info_course['advance'] != '' ? $info_course['advance'] : '0');
			if (in_array('_COURSE_TYPE', $filter_cols)) $trow[] = $info_course['course_type'];
			if (in_array('_AUTOREGISTRATION_CODE', $filter_cols)) $trow[] = $info_course['autoregistration_code'];


			$buffer->openBody();
		
			if( isset($num_iscr[$idc]) )
			{
				if(in_array('_INSCR', $filter_cols))
				{
					$trow[] = $num_iscr[$idc];
				}
				$tot_iscr += $num_iscr[$idc];
				
				//no begin course
				if(in_array('_MUSTBEGIN', $filter_cols))
				{
					if(isset($num_nobegin[$idc]))
					{
						$perc = (($num_nobegin[$idc] / $num_iscr[$idc])*100);
						$tot_nobegin += $num_nobegin[$idc];
						$tot_perc_nobegin += $perc;

						$trow[] = $num_nobegin[$idc];
						if ($show_percent) $trow[] = number_format($perc , 2, '.', '').'%';
					}
					else
					{
						$trow[] = '';
						if ($show_percent) $trow[] = '';
					}
				}

				//begin
				if(in_array('_USER_STATUS_BEGIN', $filter_cols))
				{
					if(isset($num_itinere[$idc]))
					{
						$perc = (($num_itinere[$idc] / $num_iscr[$idc])*100);
						$tot_itinere += $num_itinere[$idc];
						$tot_perc_itinere += $perc;

						$trow[] = $num_itinere[$idc];
						if ($show_percent) $trow[] = number_format($perc , 2, '.', '').'%';
					}
					else
					{
						$trow[] = '';
						if ($show_percent) $trow[] = '';
					}
				}

				//end course
				if(in_array('_COMPLETECOURSE', $filter_cols))
				{
					if(isset($num_end[$idc]))
					{
						$perc = (($num_end[$idc] / $num_iscr[$idc])*100);
						$tot_comple += $num_end[$idc];
						$tot_perc_comple += $perc;

						$trow[] = $num_end[$idc];
						if ($show_percent) $trow[] = number_format($perc , 2, '.', '').'%';
					}
					else
					{
						$trow[] = '';
						if ($show_percent) $trow[] = '';
					}
				}

				// time in
				if(in_array('_TOTAL_SESSION', $filter_cols))
				{
					if(isset($time_in_course[$idc]))
					{

						$total_time += $time_in_course[$idc];

						$trow[] = ((int)($time_in_course[$idc]/3600)).'h '
						.substr('0'.((int)(($time_in_course[$idc]%3600)/60)),-2).'m '
						.substr('0'.((int)($time_in_course[$idc]%60)),-2).'s ';
					}
					else
					{
						$trow[] = '';
					}
				}
			}
			else
			{
				if(!in_array('_INSCR', $filter_cols)) {
					$trow[] = ''; }

				//no begin course
				if(!in_array('_MUSTBEGIN', $filter_cols)) {
					$trow[]=''; $trow[]=''; }

				//begin
				if(!in_array('_USER_STATUS_BEGIN', $filter_cols)) {
					$trow[]=''; $trow[]=''; }

				//end course
				if(!in_array('_COMPLETECOURSE', $filter_cols)) {
					$trow[]=''; $trow[]=''; }

				// time in
				if(!in_array('_TOTAL_SESSION', $filter_cols)){
					$trow[] = ''; }

			}

			//print row
			if( isset($num_iscr[$idc]) && $num_iscr[$idc]) {
				$buffer->addLine($trow);
			} else $i--;
		}

		$buffer->closeBody();

		$tfoot = array( array('colspan'=>$colspan_course, 'value'=>$lang->def('_TOTAL')) );
		
		if (in_array('_INSCR', $filter_cols)) $tfoot[] = $tot_iscr;
		if (in_array('_MUSTBEGIN', $filter_cols)) { 
			$tfoot[] = $tot_nobegin;
			if ($show_percent) $tfoot[] = ( $tot_nobegin ? number_format(( ($tot_nobegin/$tot_iscr)*100 ), 2 , '.', '').'%' : 'n.d.' );
		}
		if (in_array('_USER_STATUS_BEGIN', $filter_cols)) {
			$tfoot[] = $tot_itinere;
			if ($show_percent) $tfoot[] = ( $tot_itinere ? number_format(($tot_itinere/$tot_iscr)*100, 2 , '.', '').'%' : 'n.d.' );
		}
		if (in_array('_COMPLETECOURSE', $filter_cols)) {
			$tfoot[] = $tot_comple;
			if ($show_percent) $tfoot[] = ( $tot_comple ? number_format(($tot_comple/$tot_iscr)*100, 2 , '.', '').'%' : 'n.d.');
		}
		if (in_array('_TOTAL_SESSION', $filter_cols)) {
			$tfoot[] = ((int)($total_time/3600)).'h '.substr('0'.((int)($total_time/60)),-2).'m '.substr('0'.((int)$total_time),-2).'s ';
		}
		
		$buffer->setFoot($tfoot);
		$buffer->closeTable();

		//return $output;
		return $buffer->get();
	}



	//----------------------------------------------------------------------------


}

?>