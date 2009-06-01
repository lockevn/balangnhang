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


define('_RA_CATEGORY_COURSES', 'courses');
define('_RA_CATEGORY_COURSECATS', 'coursecategories');
define('_RA_CATEGORY_TIME', 'time');

define('_DECIMAL_SEPARATOR', '.');
define('_PERCENT_SIMBOL', '%');

class Report_Aggregate extends Report {

	var $page_title = false;

	function Report_Aggregate() {
		$this->lang =& DoceboLanguage::createInstance('report', 'framework');
		$this->_set_columns_category(_RA_CATEGORY_COURSES, $this->lang->def('_RU_CAT_COURSES'), 'get_courses_filter', 'show_report_courses', '_get_courses_query');
		$this->_set_columns_category(_RA_CATEGORY_COURSECATS, $this->lang->def('_RA_CAT_COURSECATS'), 'get_coursecategories_filter', 'show_report_coursecategories', '_get_coursecategories_query');
		$this->_set_columns_category(_RA_CATEGORY_TIME, $this->lang->def('_RA_CAT_TIME'), 'get_time_filter', 'show_report_time', '_get_time_query');
	}
	
	
	//users and orgchart selection
	function get_rows_filter() {
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.directory.php');
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		//update session
		$ref =& $_SESSION['report_tempdata'];
		if (!isset($ref['rows_filter'])) {
			$ref['rows_filter'] = array( //default values
				'select_all' => false,
				'selection_type' => 'users',
				'selection' => array()
			);
		} else {
			//already resolved in switch block
		}

		$step = get_req('step', DOTY_ALPHANUM, 'sel_type');
		switch ($step) {
		
			case 'sel_type': {
				$values = array('users' => $this->lang->def('_USERS'), 'groups'=>$this->lang->def('_GROUPS'));
				$sel_val = (isset($ref['rows_filter']['selection_type']) ? $ref['rows_filter']['selection_type'] : 'users');

				$box = new ReportBox('type_selector');
				$box->title = $this->lang->def('_SELECT_TYPE_TITLE');
				//$box->description = $this->lang->def('_SELECT_TYPE_DESC');

				$box->body .= Form::getRadioSet($this->lang->def('_SELECT_TYPE'), 'selection_type', 'selection_type', array_flip($values) , $sel_val);
				$box->body .= '<div class="no_float"></div>';

				$out  = Form::openForm('selection_type_form', $jump_url);

				$out .= $box->get();
				
				$out .= Form::openButtonSpace();
				$out .= Form::getButton('ok_selection', 'ok_selection', $this->lang->def('_CONFIRM'));
				$out .= Form::getButton('undo', 'undo', $this->lang->def('_UNDO'));
				$out .= Form::closeButtonSpace();
				$out .= Form::getHidden('step', 'step', 'sel_data');

				$out .= Form::closeForm();

				cout($out);		
			} break;
			
			
			case 'sel_data': {
				$type = get_req('selection_type', DOTY_ALPHANUM, 'users');
			
				//$aclManager = new DoceboACLManager();
				$user_select = new Module_Directory();

				if (get_req('is_updating', DOTY_INT, 0)>0) {
					$ref['rows_filter']['select_all'] = ( get_req('select_all', DOTY_INT, 0)>0 ? true : false );
					$ref['rows_filter']['selection_type'] = $type;
					//$ref['rows_filter']['selection'] = $user_select->getSelection($_POST);
				} else { //maybe redoundant
					if (!isset($ref['rows_filter']['select_all'])) $ref['rows_filter']['select_all'] = false;
					if (!isset($ref['rows_filter']['selection_type'])) $ref['rows_filter']['selection_type'] = 'groups';
					if (!isset($ref['rows_filter']['selection'])) $ref['rows_filter']['selection'] = array();
					$user_select->resetSelection($ref['rows_filter']['selection']);
					//$ref['users'] = array(); it should already have been set to void array, if non existent
				}

				if(isset($_POST['cancelselector']))
					jumpTo($back_url);
				elseif(isset($_POST['okselector'])) {
					$ref['rows_filter']['selection'] = $user_select->getSelection($_POST);
					jumpTo($next_url);
				}

				//set page
				switch ($type) {
					case 'groups': {
						$user_select->show_user_selector = FALSE;
						$user_select->show_group_selector = TRUE;
						$user_select->show_orgchart_selector = TRUE;
					} break;
					case 'users': {
						$user_select->show_user_selector = TRUE;
						$user_select->show_group_selector = TRUE;
						$user_select->show_orgchart_selector = TRUE;
					} break;
				}
				//$user_select->show_orgchart_simple_selector = FALSE;
				//$user_select->multi_choice = TRUE;

				$user_select->addFormInfo(
					($type=='users' ? Form::getCheckbox($lang->def('_REPORT_FOR_ALL'), 'select_all', 'select_all', 1, $ref['rows_filter']['select_all']) : '').
					Form::getBreakRow().
					Form::getHidden('selection_type', 'selection_type', $type).
					Form::getHidden('step', 'step', 'sel_data').
					Form::getHidden('is_updating', 'is_updating', 1).
					Form::getHidden('substep', 'substep', 'user_selection').
					Form::getHidden('second_step', 'second_step', 1));
				$user_select->setPageTitle('');
				$user_select->loadSelector(str_replace('&', '&amp;', $jump_url),
					false,
					$this->lang->def('_CHOOSE_USER_FOR_REPORT'),
					true,
					true );
				
			} break;
		
		} 
	}
	
	
	
	
	function get_courses_filter() {
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
		$selector = new Selector_Course();

		if (!isset($_SESSION['report_tempdata']['columns_filter'])) {
			$_SESSION['report_tempdata']['columns_filter'] = array(
				'all_courses' => true,
				'selected_courses' => array(),
				'showed_columns' => array('completed'=>true, 'initinere'=>true, 'notstarted'=>true, 'show_percentages'=>true)
			);
		}
		$ref =& $_SESSION['report_tempdata']['columns_filter'];

		if (isset($_POST['update_tempdata'])) {
			$selector->parseForState($_POST);
			$temp = $selector->getSelection($_POST);
			$ref['selected_courses'] = $temp;
			$ref['all_courses'] = (get_req('all_courses', DOTY_INT, 1)==1 ? true : false);
			$ref['showed_columns'] = array(
				'completed' => (get_req('cols_completed', DOTY_INT, 0)>0 ? true : false),
				'initinere' => (get_req('cols_initinere', DOTY_INT, 0)>0 ? true : false),
				'notstarted' => (get_req('cols_notstarted', DOTY_INT, 0)>0 ? true : false),
				'show_percentages' => (get_req('cols_show_percentages', DOTY_INT, 0)>0 ? true : false));
		}
		else
		{
			$selector->resetSelection($ref['selected_courses']);
		}

		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			jumpTo($back_url);
		}
		
		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}

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

		$box->footer = $boxlang->def('_CURRENT_SELECTION').':&nbsp;<span id="csel_foot">'.($ref['all_courses'] ? $boxlang->def('_ALLCOURSES_FOOTER') : ($temp!='' ? $temp : '0')).'</span>';

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

		//columns selection
		$col_box = new ReportBox('columns_selection');
		$col_box->title = $this->lang->def('_SELECT_COLUMNS');
		$col_box->description = $this->lang->def('_SELECT_THE_DATA_COL_NEEDED');

		$text_completed = def('_PROGRESS_COMPLETE', 'course', 'lms');
		$text_initinere = $this->lang->def('_INITINERE');
		$text_notstarted = $this->lang->def('_NOTSTARTED');
		$text_show_percentage = $this->lang->def('_SHOW_PERCENTAGES');

		$col_box->body .= Form::getOpenFieldSet($this->lang->def('_COURSESTATUS'));
		$col_box->body .= Form::getCheckBox($text_completed, 'cols_completed', 'cols_completed', 1, $ref['showed_columns']['completed']);
		$col_box->body .= Form::getCheckBox($text_initinere, 'cols_initinere', 'cols_initinere', 1, $ref['showed_columns']['initinere']);
		$col_box->body .= Form::getCheckBox($text_notstarted, 'cols_notstarted', 'cols_notstarted', 1, $ref['showed_columns']['notstarted']);
		$col_box->body .= Form::getCheckBox($text_show_percentage, 'cols_show_percentages', 'cols_show_percentages', 1, $ref['showed_columns']['show_percentages']);
		$col_box->body .= Form::getCloseFieldSet();

		cout(
			Form::openForm('first_step_user_filter', $jump_url, false, 'post').
			$box->get().
			$col_box->get().
			Form::getHidden('update_tempdata', 'update_tempdata', 1)/*.
			Form::openButtonSpace().
			//Form::getBreakRow().
			Form::getButton('ok_filter', 'import_filter', $lang->def('_NEXT')).
			Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO')).
			Form::closeButtonSpace().
			Form::closeForm()*/ );
	}
	


	function show_report_courses($data = NULL, $other = '') {
		if ($data===NULL)
			cout( $this->_get_courses_query() );
		else
			cout( $this->_get_courses_query('html', $data, $other) );
	}
	
	function _get_courses_query($type = 'html', $report_data = NULL, $other = '') {
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once(dirname(__FILE__).'/report_tableprinter.php');
			
		if ($report_data==NULL) $ref =& $_SESSION['report_tempdata']; else $ref =& $report_data;

		$fw  = $GLOBALS['prefix_fw'];
		$lms = $GLOBALS['prefix_lms'];
		
		$sel_all = $ref['rows_filter']['select_all'];
		$sel_type = $ref['rows_filter']['selection_type'];
		$selection = $ref['rows_filter']['selection'];
		
		$all_courses = $ref['columns_filter']['all_courses'];
		$courses = $ref['columns_filter']['selected_courses']; 
		$cols =& $ref['columns_filter']['showed_columns'];

		$acl = new DoceboACLManager();
		$html = '';
		
		$man = new Man_Course();
		$courses_codes = $man->getAllCourses();
		if ($all_courses) {
			$courses = array();
			foreach ($courses_codes as $key=>$val) $courses[] = $key; 
		}
		
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
		
			// if the usre is a subadmin with only few course assigned
			require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
	
			$course_man = new AdminCourseManagment();
			$user_courses =& $course_man->getUserAllCourses( getLogUserId() );
			$courses = array_intersect($courses, $user_courses);
		}
		
		$increment = 0;
		if ($cols['completed']) $increment++;
		if ($cols['initinere']) $increment++;
		if ($cols['notstarted']) $increment++;
		if ($cols['show_percentages']) $increment = $increment*2;

		//admin users filter
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();
		if ( $userlevelid != ADMIN_GROUP_GODADMIN ) {
			require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');
			$adminManager = new AdminManager();
			//$user_filter = $adminManager->getAdminTree($GLOBALS['current_user']->getIdSt());
			//$user_filter = array_flip($user_filter);
			$idst_associated = $adminManager->getAdminTree(getLogUserId());
			$admin_users =& $acl->getAllUsersFromIdst($idst_associated);
			$admin_users = array_unique($admin_users);
		}
		
		switch ($sel_type) {


			case 'groups': {

				//retrieve all labels
				$orgchart_labels = array();
				$query = "SELECT * FROM ".$fw."_org_chart WHERE lang_code='".getLanguage()."'";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					$orgchart_labels[$row['id_dir']] = $row['translation'];
				}
				
				$labels = array();
				$query = "SELECT * FROM ".$fw."_group WHERE (hidden='false' OR groupid LIKE '/oc_%' OR groupid LIKE '/ocd_%') AND type='free'";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					if ($row['hidden']=='false') {
						$labels[$row['idst']] = $acl->relativeId($row['groupid']);
					} else {
						$temp = explode("_", $row['groupid']); //echo '<div>'.print_r($temp,true).'</div>';
						if ($temp[0]=='/oc') {
							$labels[$row['idst']] = ($temp[1]!=0 ? $orgchart_labels[$temp[1]] : '');
						} elseif ($temp[0]=='/ocd') {
							$labels[$row['idst']] = ($temp[1]!=0 ? $orgchart_labels[$temp[1]] : '');
						}
					}
				}
				
				
				$tot_size = 2;
				$head1 = array( array('colspan'=>2, 'value'=>$this->lang->def('_GROUP')) );
				$head2 = array($this->lang->def('_NAME'), $this->lang->def('_TOTAL'));
				
				foreach ($courses as $course) { 
					$head1[] = array(
						'value' => $courses_codes[$course]['code'].' - '.$courses_codes[$course]['name'],
						'colspan' => $increment
					);
							
					if ($cols['completed']) $head2[] = $this->lang->def('_COMPLETED');
					if ($cols['completed'] && $cols['show_percentages']) $head2[] = _PERCENT_SIMBOL;
					if ($cols['initinere']) $head2[] = $this->lang->def('_INITINERE');
					if ($cols['initinere'] && $cols['show_percentages']) $head2[] = _PERCENT_SIMBOL;
					if ($cols['notstarted']) $head2[] = $this->lang->def('_NOT_STARTED');
					if ($cols['notstarted'] && $cols['show_percentages']) $head2[] = _PERCENT_SIMBOL;
					
					$tot_size += $increment;
				}

				$buffer = new ReportTablePrinter($type, true);
				$buffer->openTable('','');

				$buffer->openHeader();
				$buffer->addHeader($head1);
				$buffer->addHeader($head2);
				$buffer->closeHeader();
				
				$tot_users = 0;
				$course_stats = array();
				
				//for each group, retrieve label and user statistics
				foreach ($selection as $dir_id=>$group_id) {
					$group_users = $acl->getGroupAllUser($group_id);
					if ( $userlevelid != ADMIN_GROUP_GODADMIN ) { $group_users = array_intersect($group_users, $admin_users); }
					$users_num = count($group_users);
					
					$line = array();
					$line[] = $labels[$group_id];
					$line[] = $users_num;
					$tot_users += $users_num; 
					
					if (count($group_users)>0) {
						$query = "SELECT cu.idUser, cu.idCourse, cu.status ".
							" FROM ".$lms."_courseuser as cu, ".$lms."_course as c, ".$fw."_user as u ".
							" WHERE cu.idUser=u.idst AND cu.idCourse=c.idCourse ".
							" AND u.idst IN (".implode(",", $group_users).") ".
							(!$all_courses ? " AND c.idCourse IN (".implode(",", $courses)." )" : "");

						$res = mysql_query($query);
						
						//$tot_completed = 0;
						while ($row = mysql_fetch_assoc($res) ) {
							if (!isset($course_stats[$row['idCourse']][$group_id])) {
								$course_stats[$row['idCourse']][$group_id] = array(
									'completed' => 0,
									'initinere' => 0,
									'notstarted' => 0,
									'total' => 0
								);
							}
							switch ((int)$row['status']) {
								case 2: $course_stats[$row['idCourse']][$group_id]['completed']++; break;
								case 1: $course_stats[$row['idCourse']][$group_id]['initinere']++; break;
								case 0: $course_stats[$row['idCourse']][$group_id]['notstarted']++; break;
							}
							$course_stats[$row['idCourse']][$group_id]['total']++;
						}

						foreach ($courses as $course) {
							if (isset($course_stats[$course][$group_id])) {
								if ($course_stats[$course][$group_id]['total']==0) $dividend = 1; else $dividend = $course_stats[$course][$group_id]['total'];
								if ($cols['completed']) $line[] = $course_stats[$course][$group_id]['completed'];
								if ($cols['completed'] && $cols['show_percentages']) $line[] = number_format(100.0*$course_stats[$course][$group_id]['completed']/$dividend, 2, ',', '')._PERCENT_SIMBOL;
								if ($cols['initinere']) $line[] = $course_stats[$course][$group_id]['initinere'];
								if ($cols['initinere'] && $cols['show_percentages']) $line[] = number_format(100.0*$course_stats[$course][$group_id]['initinere']/$dividend, 2, ',', '')._PERCENT_SIMBOL;
								if ($cols['notstarted']) $line[] = $course_stats[$course][$group_id]['notstarted'];
								if ($cols['notstarted'] && $cols['show_percentages']) $line[] = number_format(100.0*$course_stats[$course][$group_id]['notstarted']/$dividend, 2, ',', '')._PERCENT_SIMBOL;
							} else {
								if ($cols['completed']) $line[] = '0';
								if ($cols['completed'] && $cols['show_percentages']) $line[] = '0,00%';
								if ($cols['initinere']) $line[] = '0';
								if ($cols['initinere'] && $cols['show_percentages']) $line[] = '0,00%';
								if ($cols['notstarted']) $line[] = '0';
								if ($cols['notstarted'] && $cols['show_percentages']) $line[] = '0,00%';
							}
						}

						//$line[] = $tot_completed;

					} else {
						foreach ($courses as $course) {
							if ($cols['completed']) $line[] = '0';
							if ($cols['completed'] && $cols['show_percentages']) $line[] = '0,00%';
							if ($cols['initinere']) $line[] = '0';
							if ($cols['initinere'] && $cols['show_percentages']) $line[] = '0,00%';
							if ($cols['notstarted']) $line[] = '0';
							if ($cols['notstarted'] && $cols['show_percentages']) $line[] = '0,00%';
						}
					}
					$buffer->addLine($line);
				
				
				}
				
				$buffer->closeBody();
				//echo '<pre>'.print_r($course_stats,true).'</pre>';
				//calc totals
				$foot = array('', $tot_users);
				foreach ($courses as $course) {
					
					$completed_total = 0;
					$initinere_total = 0;
					$notstarted_total = 0;
					$total_total = 0;
					foreach ($selection as $dir_id=>$group_id) {
						$completed_total += (isset($course_stats[$course][$group_id]['completed']) ? $course_stats[$course][$group_id]['completed'] : 0);
						$initinere_total += (isset($course_stats[$course][$group_id]['initinere']) ? $course_stats[$course][$group_id]['initinere'] : 0);
						$notstarted_total += (isset($course_stats[$course][$group_id]['notstarted']) ? $course_stats[$course][$group_id]['notstarted'] : 0);
						$total_total += (isset($course_stats[$course][$group_id]['total']) ? $course_stats[$course][$group_id]['total'] : 0);
					}
					if ($cols['completed']) $foot[] = $completed_total;
					if ($cols['completed'] && $cols['show_percentages']) $foot[] = ($total_total!=0 ? number_format(100.0*$completed_total/$total_total, 2, _DECIMAL_SEPARATOR, '')._PERCENT_SIMBOL : '0'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL);
					if ($cols['initinere']) $foot[] = $initinere_total;
					if ($cols['initinere'] && $cols['show_percentages']) $foot[] = ($total_total!=0 ? number_format(100.0*$initinere_total/$total_total, 2, _DECIMAL_SEPARATOR, '')._PERCENT_SIMBOL : '0'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL);
					if ($cols['notstarted']) $foot[] = $notstarted_total;
					if ($cols['notstarted'] && $cols['show_percentages']) $foot[] = ($total_total!=0 ? number_format(100.0*$notstarted_total/$total_total, 2, _DECIMAL_SEPARATOR, '')._PERCENT_SIMBOL : '0'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL);
				}
				
				$buffer->setFoot($foot);
				$buffer->closeTable();
				$html .= $buffer->get();
			} break;
			
			
			
			case 'users': {
				
				$temp = array();
				// resolve the user selection
				$users 	=& $acl->getAllUsersFromIdst($selection);
				if ( $userlevelid != ADMIN_GROUP_GODADMIN ) { $users = array_intersect($users, $admin_users); }
				if (count($users)<=0) {
					$html .= '<p>'.$this->lang->def('_NULL_USER_SELECTION').'</p>';
					break;
				}

				$query = "SELECT cu.idUser, cu.idCourse, cu.status, u.userId, c.code, u.firstname, u.lastname ".
					" FROM ( ".$lms."_courseuser as cu ".
					" JOIN  ".$lms."_course as c ON ( cu.idCourse = c.idCourse) ) ".
					" JOIN ".$fw."_user as u ON (cu.idUser = u.idst)  ".
					" WHERE 1 ".
					" AND cu.idCourse IN (".implode(",", $courses).") ".
					($sel_all ? "" : " AND idUser IN (".implode(",", $users).")")."";

				$res = mysql_query($query);
				
				while ($row = mysql_fetch_array($res) ) {
					
					if(!isset($temp[$row['idUser']])) {
						$temp[$row['idUser']] = array (
							'username' => $acl->relativeId($row['userId']),
							'fullname' => $row['lastname'].' '.$row['firstname'],
							'courses' => array()
						);
					}
					$temp[$row['idUser']]['courses'][$row['idCourse']] = $row['status'];
				}
				//echo '<pre>';
				//print_r($temp);

				//draw table
				$tot_size = 1;
				$head2 = array($this->lang->def('_USERID'), $this->lang->def('_FULLNAME'));
				$head1 = array(array('colspan'=>2, 'value'=>$this->lang->def('_USER')));
				foreach ($courses as $course) { 
					$head1[] = array(
						'value' => $courses_codes[$course]['code'].' - '.$courses_codes[$course]['name'],
						'colspan' => $increment
					);
							
					if ($cols['completed']) $head2[] = $this->lang->def('_COMPLETED');
					if ($cols['completed'] && $cols['show_percentages']) $head2[] = _PERCENT_SIMBOL;
					if ($cols['initinere']) $head2[] = $this->lang->def('_INITINERE');
					if ($cols['initinere'] && $cols['show_percentages']) $head2[] = _PERCENT_SIMBOL;
					if ($cols['notstarted']) $head2[] = $this->lang->def('_NOT_STARTED');
					if ($cols['notstarted'] && $cols['show_percentages']) $head2[] = _PERCENT_SIMBOL;
					
					$tot_size += $increment;
				}

				$buffer = new ReportTablePrinter($type, true);
				$buffer->openTable('','');

				$buffer->openHeader();
				$buffer->addHeader($head1);
				$buffer->addHeader($head2);
				$buffer->closeHeader();
				
				$completed_total = array();
				$initinere_total = array();
				$notstarted_total = array();
				$courses_total = array();
				
				foreach($courses as $course) { 
					$completed_total[$course] = 0;
					$initinere_total[$course] = 0;
					$notstarted_total[$course] = 0;
					$courses_total[$course] = 0;
				}
				
				$buffer->openBody();
				foreach ($temp as $id_user => $table_row) {
					$line = array();
					$line[] = $table_row['username'];
					$line[] = $table_row['fullname'];
					foreach ($courses as $course) {
						if(isset($table_row['courses'][$course])) {
							
							if ($cols['completed']) $line[] = ($table_row['courses'][$course] == 2 ? 1 : 0);
							if ($cols['completed'] && $cols['show_percentages']) $line[] = ($table_row['courses'][$course] == 2 ? '100'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL : '0'._PERCENT_SIMBOL);
							if ($cols['initinere']) $line[] = ($table_row['courses'][$course] == 1 ? 1 : 0);
							if ($cols['initinere'] && $cols['show_percentages']) $line[] = ($table_row['courses'][$course] == 1 ? '100'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL : '0'._PERCENT_SIMBOL);
							if ($cols['notstarted']) $line[] = ($table_row['courses'][$course] == 0 ? 1 : 0);
							if ($cols['notstarted'] && $cols['show_percentages']) $line[] = ($table_row['courses'][$course] == 0 ? '100'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL : '0'._PERCENT_SIMBOL);

							switch ((int)$table_row['courses'][$course]) {
								case 2: if (isset($completed_total[$course])) $completed_total[$course] += 1; else $completed_course[$course] = 1; break;
								case 1: if (isset($initinere_total[$course])) $initinere_total[$course] += 1; else $initinere_course[$course] = 1; break;
								case 0: if (isset($notstarted_total[$course])) $notstarted_total[$course] += 1; else $notstarted_course[$course] = 1; break;
							}

							if (isset($courses_total[$course])) $courses_total[$course] += 1; else $courses_total[$course] = 1;
						} else {

							if ($cols['completed']) $line[] = '0';
							if ($cols['completed'] && $cols['show_percentages']) $line[] = '0'._PERCENT_SIMBOL;
							if ($cols['initinere']) $line[] = '0';
							if ($cols['initinere'] && $cols['show_percentages']) $line[] = '0'._PERCENT_SIMBOL;
							if ($cols['notstarted']) $line[] = '0';
							if ($cols['notstarted'] && $cols['show_percentages']) $line[] = '0'._PERCENT_SIMBOL;

							if (isset($courses_total[$course])) $courses_total[$course] += 1; else $courses_total[$course] = 1;
						}
					}
					$buffer->addLine($line);
				}
				$buffer->closeBody();

				$totals_line = array('', '');
				foreach ($courses as $course) {
				
					$completed_num = isset($completed_total[$course]) ? $completed_total[$course] : '0';
					$initinere_num = isset($initinere_total[$course]) ? $initinere_total[$course] : '0';
					$notstarted_num = isset($notstarted_total[$course]) ? $notstarted_total[$course] : '0';
					$total_num = isset($courses_total[$course]) ? $courses_total[$course] : '0';
				
					if ($cols['completed']) $totals_line[] = $completed_num;
					if ($cols['completed'] && $cols['show_percentages']) $totals_line[] = $total_num!=0 ? number_format(100.0*$completed_num/$total_num, 2, _DECIMAL_SEPARATOR, '')._PERCENT_SIMBOL : '0'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL;
					if ($cols['initinere']) $totals_line[] = $initinere_num;
					if ($cols['initinere'] && $cols['show_percentages']) $totals_line[] = $total_num!=0 ? number_format(100.0*$initinere_num/$total_num, 2, _DECIMAL_SEPARATOR, '')._PERCENT_SIMBOL : '0'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL;
					if ($cols['notstarted']) $totals_line[] = $notstarted_num;
					if ($cols['notstarted'] && $cols['show_percentages']) $totals_line[] = $total_num!=0 ? number_format(100.0*$notstarted_num/$total_num, 2, _DECIMAL_SEPARATOR, '')._PERCENT_SIMBOL : '0'._DECIMAL_SEPARATOR.'00'._PERCENT_SIMBOL;
				}
				$buffer->setFoot($totals_line);
				
				$buffer->closeTable();
				
				$html .= $buffer->get();
			} break;
		
		}
		
		
		return $html;
	
	}


	//----------------------------------------------------------------------------

	function show_report_coursecategories($data = NULL, $other = '') {
		if ($data===NULL)
			cout( $this->_get_coursecategories_query() );
		else
			cout( $this->_get_coursecategories_query('html', $data, $other) );
	}

	function get_coursecategories_filter() {
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_lms'].'/lib/category/lib.categorytree.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		//$sel = new Course_Manager();
		//$sel->setLink('index.php?modname=report&op=report_rows_filter');

		if (isset($_POST['undo_filter'])) jumpTo($back_url);

		
		if (!isset($_SESSION['report_tempdata']['columns_filter'])) {
			$_SESSION['report_tempdata']['columns_filter'] = array(
				'all_categories' => true,
				'selected_categories' => array(),
				'showed_columns' => array(/*'completed'=>true, 'initinere'=>true, 'notstarted'=>true, 'show_percentages'=>true*/)
			);
		}
		$ref =& $_SESSION['report_tempdata']['columns_filter'];

		$tree = new CourseCategoryTree('course_categories_selector', false, false, _TREE_COLUMNS_TYPE_RADIO);
		$tree->init();

		if (isset($_POST['update_tempdata'])) {

			$ref['selected_categories'] = isset($_POST['course_categories_selector_input']) ? explode(",", $_POST['course_categories_selector_input']) : array();
			$ref['showed_columns'] = array(/*
				'completed' => (get_req('cols_completed', DOTY_INT, 0)>0 ? true : false),
				'initinere' => (get_req('cols_initinere', DOTY_INT, 0)>0 ? true : false),
				'notstarted' => (get_req('cols_notstarted', DOTY_INT, 0)>0 ? true : false),
				'show_percentages' => (get_req('cols_show_percentages', DOTY_INT, 0)>0 ? true : false)*/);
		}
		else
		{
			if ( count($ref['selected_categories'])>0 )
				$tree->setInitialSelection($ref['selected_categories']);
		}

		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			jumpTo($back_url);
		}

		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}


		//produce output
		$html = '';
		$output = $tree->get(true, true, 'treeCat');

		cout($output['js'], 'page_head');
		
		$box = new ReportBox('coursecategories_selector');
		$box->title = $this->lang->def('_COURSES_SELECTION_TITLE');
		$box->description = $this->lang->def('_COURSES_SELECTION_DESC');

		$boxlang =& DoceboLanguage::createInstance('report', 'framework');
		$box->body .= '<div class="">'.$output['html'].'</div>';
		$box->body .= Form::getHidden('update_tempdata', 'update_tempdata', 1);
		$box->body .= Form::openButtonSpace();
		$box->body .= '<button class="button" type="button" onclick="treeCat.clearSelection();">'.def('_CLEAR', 'mygroup', 'lms').'</button>';
		$box->body .= Form::closeButtonSpace();

		$html = $box->get();
		return $html;//cout($html);
	}



	function _get_coursecategories_query($type = 'html', $report_data = NULL, $other = '') {
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once(dirname(__FILE__).'/report_tableprinter.php');

		if ($report_data==NULL) $ref =& $_SESSION['report_tempdata']; else $ref =& $report_data;

		$fw  = $GLOBALS['prefix_fw'];
		$lms = $GLOBALS['prefix_lms'];

		$sel_all = $ref['rows_filter']['select_all'];
		$sel_type = $ref['rows_filter']['selection_type'];
		$selection = $ref['rows_filter']['selection'];

		$categories = $ref['columns_filter']['selected_categories'];
		$cols =& $ref['columns_filter']['showed_columns'];

		if (!$sel_all && count($selection)<=0) {
			cout( '<p>'.$this->lang->def('_NO_USERS_SELECTED').'</p>' );
			return;
		}

		$acl = new DoceboACLManager();
		$acl->include_suspended = true;
		$html = '';


		//admin users filter
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();
		if ( $userlevelid != ADMIN_GROUP_GODADMIN ) {
			require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');
			$adminManager = new AdminManager();
			//$user_filter = $adminManager->getAdminTree($GLOBALS['current_user']->getIdSt());
			//$user_filter = array_flip($user_filter);
			$idst_associated = $adminManager->getAdminTree(getLogUserId());
			$admin_users =& $acl->getAllUsersFromIdst($idst_associated);
			$admin_users = array_unique($admin_users);
		}

		//course categories names
		$res = mysql_query("SELECT * FROM ".$lms."_category ");
		$categories_names = array();
		$categories_limit = array();
		while ($row = mysql_fetch_assoc($res)) {
			$categories_names[ $row['idCategory'] ] = ($row['path']!='/root/' ? end( explode("/", $row['path'])) : def('_COURSE_CATEGORY', 'admin_course_management', 'lms'));//def('_ROOT'));
			$categories_paths[ $row['idCategory'] ] = ($row['path']!='/root/' ? substr($row['path'], 5, (strlen($row['path'])-5)) : def('_COURSE_CATEGORY', 'admin_course_management', 'lms'));//def('_ROOT'));
			$categories_limit[ $row['idCategory'] ] = array($row['iLeft'], $row['iRight']);
		}
		
		$user_courses = false;
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
		
			// if the usre is a subadmin with only few course assigned
			require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
			$course_man = new AdminCourseManagment();
			$user_courses =& $course_man->getUserAllCourses( getLogUserId() );
		}
		//create table
		switch ($sel_type) {
				
			case 'users': {
				//table data
				$data = array();
				
				$head1 = array('');
				$head2 = array( $this->lang->def('_USER'));

				$totals = array();

				foreach ($categories as $idcat) {
					$index = (int)str_replace("d", "", $idcat);
					$head1[] = array('colspan'=>2, 'value'=>$categories_paths[$index]);
					$head2[] = $this->lang->def('_COMPLETECOURSE'); //recycle key
					$head2[] = $this->lang->def('incomplete');

					$is_descendant = strpos($idcat, "d");
					if ($is_descendant === false) {
						$condition = " AND cat.idCategory=".$index." ";
					} else {
						list($left, $right) = $categories_limit[$index];//mysql_fetch_row( mysql_query("SELECT iLeft, iRight FROM ".$lms."_category WHERE idCAtegory=".$index) );
						$condition = " AND cat.iLeft >= ".$left." AND cat.iRight <= ".$right." ";
					}

					//resolve user selection
					if ($sel_all)
						$selection = $acl->getAllUsersIdst();
					else
						$selection = $acl->getAllUsersFromIdst( $selection ); //resolve group and orgchart selection

					$query = "SELECT cu.idUser, cat.idCategory, c.idCourse, c.code, cu.status "
						." FROM ".$lms."_course as c JOIN ".$lms."_category as cat JOIN ".$lms."_courseuser as cu "
						." ON (c.idCourse=cu.idCourse AND c.idCategory=cat.idCategory) "
						." WHERE ".($sel_all ? " 1 " : " cu.idUser IN (".implode(",", $selection).") " )
						.$condition
						.( $user_courses != false ? " AND c.idCourse IN ( '".implode("','", $user_courses)."' ) " : '' );

					$res = mysql_query($query);
					$temp = array();
					$total_1 = 0;
					$total_2 = 0;
					while ($row = mysql_fetch_assoc($res)) {
						$iduser = $row['idUser'];

						if (!isset($temp[ $iduser ]))
							$temp[ $iduser ] = array(
								'completed' => 0,
								'not_completed' => 0
							);

						switch ($row['status']) {
							case 0:
							case 1: { $temp[$iduser]['not_completed']++; $total_2++; } break;
							case 2: { $temp[$iduser]['completed']++; $total_1++; } break;
						}
					}

					$totals[] = $total_1;
					$totals[] = $total_2;

					$data[ $index ] = $temp;
					//unset($temp); //free memory
				}

				$buffer = new ReportTablePrinter($type, true);
				$buffer->openTable('','');

				$buffer->openHeader();
				$buffer->addHeader($head1);
				$buffer->addHeader($head2);
				$buffer->closeHeader();

				//retrieve usernames
				$usernames = array();
				$res = mysql_query("SELECT idst, userid FROM ".$fw."_user WHERE idst IN (".implode(",", $selection).")");
				while (list($idst, $userid) = mysql_fetch_row($res))
					$usernames[$idst] = $acl->relativeId( $userid );

				//user cycle
				$buffer->openBody();
				foreach ($selection as $user) {
					$line = array();

					$line[] = $usernames[ $user ];
					foreach ($categories as $idcat) {
						if ($idcat != '') {
							$index = (int)str_replace("d", "", $idcat);
							if (isset($data[$index][$user])) {
								$line[] = $data[$index][$user]['completed'];
								$line[] = $data[$index][$user]['not_completed'];
							} else {
								$line[] = '0';
								$line[] = '0';
							}
						}
					}

					$buffer->addLine($line);
				}
				$buffer->closeBody();

				//set totals
				$foot = array('');
				foreach ($totals as $total) { $foot[] = $total; }
				$buffer->setFoot($foot);

				//unset($data); //free memory
				$buffer->closeTable();
				$html .= $buffer->get();
			} break;

			//-----------------------------------------

			case 'groups': {
				//table data
				$data = array();

				//retrieve all labels
				$orgchart_labels = array();
				$query = "SELECT * FROM ".$fw."_org_chart WHERE lang_code='".getLanguage()."'";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					$orgchart_labels[$row['id_dir']] = $row['translation'];
				}

				$labels = array();
				//$query = "SELECT * FROM ".$fw."_group WHERE (hidden='false' OR groupid LIKE '/oc_%' OR groupid LIKE '/ocd_%') AND type='free'";
				$query = "SELECT * FROM ".$fw."_group WHERE groupid LIKE '/oc\_%' OR groupid LIKE '/ocd\_%' OR hidden = 'false' ";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					if ($row['hidden']=='false') {
						$labels[$row['idst']] = $acl->relativeId($row['groupid']);
					} else {
						$temp = explode("_", $row['groupid']); //echo '<div>'.print_r($temp,true).'</div>';
						if ($temp[0]=='/oc') {
							$labels[$row['idst']] = ($temp[1]!=0 ? $orgchart_labels[$temp[1]] : '');
						} elseif ($temp[0]=='/ocd') {
							$labels[$row['idst']] = ($temp[1]!=0 ? $orgchart_labels[$temp[1]] : '');
						}
					}
				}


				//solve groups user
				$solved_groups = array();
				$subgroups_list = array();
				foreach ($selection as $group) {
					$temp = $acl->getGroupGDescendants($group);
					$temp[] = $group;
					foreach ($temp as $idst_subgroup) {
						$solved_groups[$idst_subgroup] = $group;
					}
					$subgroups_list = array_merge( $subgroups_list, $temp );
				}

				$tot_size = 2;
				$totals = array();
				$head1 = array( array('colspan'=>2, 'value'=>$this->lang->def('_GROUP')) );
				$head2 = array($this->lang->def('_NAME'), $this->lang->def('_TOTAL'));

				foreach ($categories as $idcat) {
					$index = (int)str_replace("d", "", $idcat);
					$head1[] = array('colspan'=>2, 'value'=>$categories_paths[$index]);
					$head2[] = $this->lang->def('_COMPLETECOURSE');
					$head2[] = $this->lang->def('incomplete');

					$is_descendant = strpos($idcat, "d");
					$condition = '';
					if ($is_descendant === false) {
						$condition = " AND cat.idCategory=".$index." ";
					} else {
						list($left, $right) = $categories_limit[$index];//mysql_fetch_row( mysql_query("SELECT iLeft, iRight FROM ".$lms."_category WHERE idCAtegory=".$index) );
						$condition = " AND cat.iLeft >= ".$left." AND cat.iRight <= ".$right." ";
					}


					$query = "SELECT gm.idst as idGroup, cu.idUser, cat.idCategory, c.idCourse, c.code, cu.status "
						." FROM ".$lms."_course as c JOIN ".$lms."_category as cat JOIN ".$lms."_courseuser as cu JOIN ".$fw."_group_members as gm "
						." ON (c.idCourse=cu.idCourse AND c.idCategory=cat.idCategory AND cu.idUser=gm.idstMember) "
						." WHERE ".($sel_all ? " 1 " : " gm.idst IN (".implode(",", $subgroups_list).") " ) //idst of the groups
						.$condition
						.( $user_courses != false ? " AND c.idCourse IN ( '".implode("','", $user_courses)."' ) " : '' );

					$res = mysql_query($query);
					$temp = array();
					$total_1 = 0;
					$total_2 = 0;
					while ($row = mysql_fetch_assoc($res)) {
						$id_group = $solved_groups[ $row['idGroup'] ];

						if (!isset($temp[ $id_group ]))
							$temp[ $id_group ] = array(
								'completed' => 0,
								'not_completed' => 0
							);

						switch ($row['status']) {
							case 0:
							case 1: { $temp[$id_group]['not_completed']++; $total_2++; } break;
							case 2: { $temp[$id_group]['completed']++; $total_1++; } break;
						}
					}

					$totals[]= $total_1;
					$totals[]= $total_2;

					$data[ $index ] = $temp;
					//unset($temp); //free memory
				}


				$buffer = new ReportTablePrinter($type, true);
				$buffer->openTable('','');

				$buffer->openHeader();
				$buffer->addHeader($head1);
				$buffer->addHeader($head2);
				$buffer->closeHeader();

				$tot_users = 0;
				$buffer->openBody();

				foreach ($selection as $dir_id=>$group_id) {
					$group_users = $acl->getGroupAllUser($group_id);
					if ( $userlevelid != ADMIN_GROUP_GODADMIN ) { $group_users = array_intersect($group_users, $admin_users); }
					$users_num = count($group_users);

					$line = array();
					$line[] = $labels[$group_id];
					$line[] = $users_num;
					$tot_users += $users_num;


					foreach ($categories as $idcat) {
						if ($idcat != '') {
							$index = (int)str_replace("d", "", $idcat);
							if (isset($data[$index][$group_id])) {
								$line[] = $data[$index][$group_id]['completed'];
								$line[] = $data[$index][$group_id]['not_completed'];
							} else {
								$line[] = '0';
								$line[] = '0';
							}
						}
					}

					$buffer->addLine($line);
				}
				$buffer->closeBody();

				//totals ...

				$foot = array('', $tot_users);
				foreach ($totals as $total) {	$foot[] = $total;	}
				$buffer->setFoot($foot);

				$buffer->closeTable();
				$html .= $buffer->get();
			} break;

		} //end switch

		return $html;//$GLOBALS['page']->add($html, 'content');
	}

	//----------------------------------------------------------------------------

	function show_report_time($data = NULL, $other = '') {
		if ($data===NULL)
			cout( $this->_get_time_query() );
		else
			cout( $this->_get_time_query('html', $data, $other) );
	}

	function get_time_filter() {
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		//$sel = new Course_Manager();
		//$sel->setLink('index.php?modname=report&op=report_rows_filter');

		if (isset($_POST['undo_filter'])) jumpTo($back_url);


		if (!isset($_SESSION['report_tempdata']['columns_filter'])) {
			$_SESSION['report_tempdata']['columns_filter'] = array(
				'timetype' => 'years',
				'years' => 1,
				'months' => 12
			);
		}
		$ref =& $_SESSION['report_tempdata']['columns_filter'];


		if (isset($_POST['update_tempdata'])) {
			$ref['years'] = get_req('years', DOTY_INT, 1);
		} else {
			//...
		}

		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			jumpTo($back_url);
		}

		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}

		$box = new ReportBox('choose_time');
		$box->title = $this->lang->def('_CHOOSE_TIME');
		$box->description = $this->lang->def('_CHOOSE_TIME_DESC');

		$dropdownyears = array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			5 => 5,
			6 => 6,
			7 => 7
		);
		$box->body .= Form::getHidden('update_tempdata', 'update_tempdata', 1);
		$box->body .= Form::getDropDown($this->lang->def('_YEARS'), 'years', 'years', $dropdownyears, $ref['years']);

		$html = $box->get();
		return $html;//cout($html);

	}

	function _get_time_query($type = 'html', $report_data = NULL, $other = '') {
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once(dirname(__FILE__).'/report_tableprinter.php');

		if ($report_data==NULL) $ref =& $_SESSION['report_tempdata']; else $ref =& $report_data;

		$fw  = $GLOBALS['prefix_fw'];
		$lms = $GLOBALS['prefix_lms'];

		$sel_all = $ref['rows_filter']['select_all'];
		$sel_type = $ref['rows_filter']['selection_type'];
		$selection = $ref['rows_filter']['selection'];

		$timetype = $ref['columns_filter']['timetype'];
		$years =& $ref['columns_filter']['years'];
		$months =& $ref['columns_filter']['months'];

		if (!$sel_all && count($selection)<=0) {
			cout( '<p>'.$this->lang->def('_NO_USERS_SELECTED').'</p>' );
			return;
		}

		$acl = new DoceboACLManager();
		$acl->include_suspended = true;

		//admin users filter
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();
		if ( $userlevelid != ADMIN_GROUP_GODADMIN ) {
			require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');
			$adminManager = new AdminManager();
			//$user_filter = $adminManager->getAdminTree($GLOBALS['current_user']->getIdSt());
			//$user_filter = array_flip($user_filter);
			$idst_associated = $adminManager->getAdminTree(getLogUserId());
			$admin_users =& $acl->getAllUsersFromIdst($idst_associated);
			$admin_users = array_unique($admin_users);
		}

		$html = '';
		$times = array();
		switch ($timetype) {
			case 'years': {
				$now = date('Y');
				for ($i = $now-$years+1; $i<=$now; $i++) { $times[] = $i; }
			} break;
			case 'months':{
				//...
			} break;
		}

		switch ($sel_type) {

			case 'users': {
				$data = array();
				
				$users_list = ($sel_all ? $acl->getAllUsersIdst() : $acl->getAllUsersFromIdst($selection) );
				$users_list = array_unique($users_list);
				if ( $userlevelid != ADMIN_GROUP_GODADMIN ) $users_list = array_intersect($users_list, $admin_users);

				$query = "SELECT idUser, YEAR(date_complete) as yearComplete "
					." FROM ".$lms."_courseuser "
					." WHERE status=2 "
					.( $userlevelid != ADMIN_GROUP_GODADMIN ? " AND idUser IN (".implode(",", $users_list).") " : "" );


				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					//$data[ $row['idUser'] ][ $row['yearComplete'] ] = $row['complete'];
					$idUser = $row['idUser'];
					$year = $row['yearComplete'];
					if (!isset($data[ $idUser ][ $year ])) $data[ $idUser ][ $year ] = 0;
					$data[ $idUser ][ $year ]++;
				}

				$usernames = array();
				$query = "SELECT idst, userid FROM ".$fw."_user WHERE idst IN (".implode(",", $users_list).")";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					$usernames[ $row['idst'] ] = $acl->relativeId( $row['userid'] );
				}

				//draw table
				$buffer = new ReportTablePrinter($type, true);
				$buffer->openTable('','');

				$head = array($this->lang->def('_USER'));
				foreach ($times as $time) {
					$head[] = $time;
				}
				$head[] = $this->lang->def('_TOTAL');

				$buffer->openHeader();
				$buffer->addHeader($head);
				$buffer->closeHeader();

				$tot_total = 0;
				$buffer->openBody();
				foreach ($users_list as $user) {

					$line = array();
					$line_total = 0;
					$line[] = $usernames[$user];
					foreach ($times as $time) { //years or months

						switch ($timetype) {

							case 'years': {
								if (isset($data[$user][$time])) {
										$line[] = $data[$user][$time];
										$line_total += $data[$user][$time];
								} else
										$line[] = '0';
							} break;

							case 'months': {
								//$year = ...
								//$month = ...
								//$line[] = (isset($data[$group][$year][$month]) ? $data[$group][$year][$month] : '0'); break;
							}

						}

					}

					$line[] = $line_total;
					$tot_total += $line_total;
					$buffer->addLine($line);
				}

				$buffer->closeBody();

				//totals
				$foot = array('');
				foreach ($times as $time) {
					$temp = 0;
					foreach ($users_list as $user) {
						if (isset($data[$user][$time])) $temp += $data[$user][$time];
					}
					$foot[] = $temp;
				}
				$foot[] = $tot_total;
				$buffer->setFoot($foot);

				$buffer->closeTable();
				$html .= $buffer->get();
			} break;



			//--------------------

			case 'groups': {
				//retrieve all labels
				$orgchart_labels = array();
				$query = "SELECT * FROM ".$fw."_org_chart WHERE lang_code='".getLanguage()."'";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					$orgchart_labels[$row['id_dir']] = $row['translation'];
				}

				$labels = array();
				//$query = "SELECT * FROM ".$fw."_group WHERE (hidden='false' OR groupid LIKE '/oc_%' OR groupid LIKE '/ocd_%') AND type='free'";
				$query = "SELECT * FROM ".$fw."_group WHERE groupid LIKE '/oc\_%' OR groupid LIKE '/ocd\_%' OR hidden = 'false' ";
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					if ($row['hidden']=='false') {
						$labels[$row['idst']] = $acl->relativeId($row['groupid']);
					} else {
						$temp = explode("_", $row['groupid']); //echo '<div>'.print_r($temp,true).'</div>';
						if ($temp[0]=='/oc') {
							$labels[$row['idst']] = ($temp[1]!=0 ? $orgchart_labels[$temp[1]] : '');
						} elseif ($temp[0]=='/ocd') {
							$labels[$row['idst']] = ($temp[1]!=0 ? $orgchart_labels[$temp[1]] : '');
						}
					}
				}


				//solve groups user
				$solved_groups = array();
				$subgroups_list = array();
				foreach ($selection as $group) {
					$temp = $acl->getGroupGDescendants($group);
					$temp[] = $group;
					foreach ($temp as $idst_subgroup) {
						$solved_groups[$idst_subgroup] = $group;
					}
					$subgroups_list = array_merge( $subgroups_list, $temp );
				}



				$query = "SELECT gm.idst as idGroup, YEAR(cu.date_complete) as yearComplete, MONTH(cu.date_complete) as monthComplete "
					." FROM ".$lms."_courseuser as cu JOIN ".$fw."_group_members as gm ON (cu.idUser=gm.idstMember) "
					." WHERE status=2 AND gm.idst IN (".implode(",", $subgroups_list).") GROUP BY cu.idUser, cu.idCourse";

				$data = array();
				$res = mysql_query($query);
				while ($row = mysql_fetch_assoc($res)) {
					$idGroup = $solved_groups[ $row['idGroup'] ];
					$year = $row['yearComplete'];
					$month = $row['monthComplete'];

					switch ($timetype) {

						case 'years': {
							if (!isset($data[ $idGroup ][$year])) $data[ $idGroup ][$year] = 0;
							$data[ $idGroup ][$year]++;
						} break;

						case 'months': {
							if (!isset($data[ $idGroup ][$year][$month])) $data[ $idGroup ][$year][$month] = 0;
							$data[ $idGroup ][$year][$month]++;
						} break;

					} //end switch
				}


				//draw table
				$buffer = new ReportTablePrinter($type, true);
				$buffer->openTable('','');

				$head = array($this->lang->def('_GROUP'), $this->lang->def('_USERS'));
				foreach ($times as $time) {
					$head[] = $time;
				}
				$head[] = $this->lang->def('_TOTAL');

				$buffer->openHeader();
				$buffer->addHeader($head);
				$buffer->closeHeader();

				$tot_users = 0;
				$tot_total = 0;
				$buffer->openBody();
				foreach ($selection as $group) {
					$group_users = $acl->getGroupAllUser($group);
					if ( $userlevelid != ADMIN_GROUP_GODADMIN ) { $group_users = array_intersect($group_users, $admin_users); }
					$users_num = count($group_users);

					$line = array();
					$line_total = 0;
					$line[] = $labels[$group];
					$line[] = $users_num;
					foreach ($times as $time) { //years or months

						switch ($timetype) {
							
							case 'years': {
								if (isset($data[$group][$time])) {
										$line[] = $data[$group][$time];
										$line_total += $data[$group][$time];
								} else
										$line[] = '0';
							} break;

							case 'months': {
								//$year = ...
								//$month = ...
								//$line[] = (isset($data[$group][$year][$month]) ? $data[$group][$year][$month] : '0'); break;
							}

						}
					
					}

					$line[] = $line_total;
					$tot_users += $users_num;
					$tot_total += $line_total;
					$buffer->addLine($line);
				}

				$buffer->closeBody();

				//totals
				$foot = array('', $tot_users);
				foreach ($times as $time) {
					$temp = 0;
					foreach ($selection as $group) {
						if (isset($data[$group][$time])) $temp += $data[$group][$time];
					}
					$foot[] = $temp;
				}
				$foot[] = $tot_total;
				$buffer->setFoot($foot);

				$buffer->closeTable();
				$html .= $buffer->get();
			} break;

		} //end switch

		return $html;//cout($html);
	}

}