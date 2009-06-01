<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
require_once($GLOBALS['where_framework'].'/lib/lib.mailer.php');
require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
require_once(dirname(__FILE__).'/class.report.php');

define('_RU_CATEGORY_COURSES', 'courses');
define('_RU_CATEGORY_GENERAL', 'general');
define('_RU_CATEGORY_COMPETENCES', 'competences');
define('_RU_CATEGORY_DELAY', 'delay');
define('_RU_CATEGORY_LO', 'LO');

define('_COURSES_FILTER_SESSION_NUMBER'   , 'opt1');
define('_COURSES_FILTER_SCORE_INIT'       , 'opt2');
define('_COURSES_FILTER_SCORE_END'        , 'opt3');
define('_COURSES_FILTER_INSCRIPTION_DATE' , 'opt4');
define('_COURSES_FILTER_END_DATE'         , 'opt5');
define('_COURSES_FILTER_LASTACCESS_DATE'  , 'opt6');
define('_COURSES_FILTER_FIRSTACCESS_DATE' , 'opt7');
define('_COURSES_FILTER_SCORE_COURSE'     , 'opt8');

define('_FILTER_INTEGER', 'int');
define('_FILTER_DATE', 'date');

define('_MILESTONE_NONE', 'ml_none');
define('_MILESTONE_START', 'ml_start');
define('_MILESTONE_END', 'ml_end');


class Report_User extends Report {

	//var $rows_filter = array();

	var $status_u = array();
	var $status_c = array();

	var $page_title = false;
	var $use_mail = true;

	var $courses_filter_definition = array();

	function Report_User($id_report, $report_name = false) {
		parent::Report($id_report, $report_name);
		$this->lang =& DoceboLanguage::createInstance('report', 'framework');
		$this->usestandardtitle_rows = false;


		$this->_set_columns_category(_RU_CATEGORY_COURSES, $this->lang->def('_RU_CAT_COURSES'), 'get_courses_filter', 'show_report_courses', '_get_courses_query');
		//$this->_set_columns_category(_RU_CATEGORY_GENERAL, $this->lang->def('_RU_CAT_GENERAL'), 'get_general_filter', 'show_report_general');
		$this->_set_columns_category(_RU_CATEGORY_COMPETENCES, $this->lang->def('_RU_CAT_COMPETENCES'), 'get_competences_filter', 'show_report_competences', '_get_competences_query');
		$this->_set_columns_category(_RU_CATEGORY_DELAY, $this->lang->def('_RU_CAT_DELAY'), 'get_delay_filter', 'show_report_delay', '_get_delay_query');
		$this->_set_columns_category(_RU_CATEGORY_LO, $this->lang->def('_RU_CAT_LO'), 'get_LO_filter', 'show_report_LO', '_get_LO_query');

		$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
		$this->status_c = array(
			CST_PREPARATION => $lang->def('_CST_PREPARATION'),//, 'admin_course_managment', 'lms'),
			CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'),//, 'admin_course_managment', 'lms'),
			CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'),//, 'admin_course_managment', 'lms'),
			CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'),//, 'admin_course_managment', 'lms'),
			CST_CANCELLED 	=> $lang->def('_CST_CANCELLED')//, 'admin_course_managment', 'lms')
		);

		$lang =& DoceboLanguage::CreateInstance('subscribe', 'lms');
		$this->status_u = array(
			_CUS_CONFIRMED 		=> $lang->def('_USER_STATUS_CONFIRMED'),//, 'subscribe', 'lms'),

			_CUS_SUBSCRIBED 	=> $lang->def('_USER_STATUS_SUBS'),//, 'subscribe', 'lms'),//_USER_STATUS_SUBS(?)
			_CUS_BEGIN 			=> $lang->def('_USER_STATUS_BEGIN'),//, 'subscribe', 'lms'),
			_CUS_END 			=> $lang->def('_USER_STATUS_END'),//, 'subscribe', 'lms'),
			_CUS_SUSPEND 		=> $lang->def('_USER_STATUS_SUSPEND')//, 'subscribe', 'lms')
		);

		$this->courses_filter_definition = array(
			array('key'=>_COURSES_FILTER_SESSION_NUMBER, 'label'=>$this->lang->def('_COURSES_FILTER_SESSION_NUMBER'), 'type'=>_FILTER_INTEGER),
			array('key'=>_COURSES_FILTER_SCORE_INIT, 'label'=>$this->lang->def('_COURSES_FILTER_SCORE_INIT'), 'type'=>_FILTER_INTEGER),
			array('key'=>_COURSES_FILTER_SCORE_END, 'label'=>$this->lang->def('_COURSES_FILTER_SCORE_END'), 'type'=>_FILTER_INTEGER),
			array('key'=>_COURSES_FILTER_SCORE_COURSE, 'label'=>$this->lang->def('_COURSES_FILTER_SCORE_COURSE'), 'type'=>_FILTER_INTEGER),
			array('key'=>_COURSES_FILTER_INSCRIPTION_DATE, 'label'=>$this->lang->def('_COURSES_FILTER_INSCRIPTION_DATE'), 'type'=>_FILTER_DATE),
			array('key'=>_COURSES_FILTER_FIRSTACCESS_DATE, 'label'=>$this->lang->def('_COURSES_FILTER_FIRSTACCESS_DATE'), 'type'=>_FILTER_DATE),
			array('key'=>_COURSES_FILTER_END_DATE, 'label'=>$this->lang->def('_COURSES_FILTER_END_DATE'), 'type'=>_FILTER_DATE),
			array('key'=>_COURSES_FILTER_LASTACCESS_DATE, 'label'=>$this->lang->def('_COURSES_FILTER_LASTACCESS_DATE'), 'type'=>_FILTER_DATE)
		);

		$this->LO_columns=array(
			array('key'=>'userid',          'select'=>false, 'group'=>'user',   'label'=>$this->lang->def('_USERID')),
			array('key'=>'user_name',       'select'=>true,  'group'=>'user',   'label'=>$this->lang->def('_USERNAME')),
			array('key'=>'_CUSTOM_FIELDS_', 'select'=>false, 'group'=>'user',   'label'=>false),
			array('key'=>'course_code',     'select'=>false, 'group'=>'course', 'label'=>$this->lang->def('_CODE')),
			array('key'=>'course_name',     'select'=>true,  'group'=>'course', 'label'=>$this->lang->def('_TH_COURSENAME')),
			array('key'=>'course_status',   'select'=>true,  'group'=>'course', 'label'=>$this->lang->def('_STATUS')),
			array('key'=>'lo_type',         'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_LO_COL_TYPE')),
			array('key'=>'lo_name',         'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_TITLE')),
			array('key'=>'lo_milestone',    'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_LO_COL_MILESTONE')),
			array('key'=>'firstAttempt',    'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_LO_COL_FIRSTATT')),
			array('key'=>'lastAttempt',     'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_LO_COL_LASTATT')),
			array('key'=>'lo_status',       'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_STATUS')),
			array('key'=>'lo_score',        'select'=>true,  'group'=>'lo',     'label'=>$this->lang->def('_LO_COL_SCORE'))
		);
	}


	function get_rows_filter() {

		if (!isset($_SESSION['report_tempdata']['rows_filter'])) {
			$_SESSION['report_tempdata']['rows_filter'] = array(
				'users'=>array(),
				'all_users'=> false
			);
		}

		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.directory.php');
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$org_chart_subdivision 	= importVar('org_chart_subdivision', true, 0);

		$aclManager = new DoceboACLManager();
		$user_select = new Module_Directory();
		
		if(isset($_POST['cancelselector'])) {

			jumpTo($back_url);
		} elseif(isset($_POST['okselector'])) {

			$aclManager = new DoceboACLManager();

			$temp = $user_select->getSelection($_POST);
			
			$_SESSION['report_tempdata']['rows_filter']['users'] = $temp;
			$_SESSION['report_tempdata']['rows_filter']['all_users'] = ( get_req('all_users', DOTY_INT, 0) > 0 ? true : false );
			
			jumpTo($next_url);
		} else {

			// first step load selector
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
			
			if (get_req('is_updating', DOTY_INT, false)) {
				//$_SESSION['report_tempdata']['rows_filter']['users'] = $user_select->getSelection($_POST);
				$_SESSION['report_tempdata']['rows_filter']['all_users'] = (get_req('all_users', DOTY_INT, 0)>0 ? true : false);
			} else {
				$user_select->requested_tab = PEOPLEVIEW_TAB;
				$user_select->resetSelection($_SESSION['report_tempdata']['rows_filter']['users']);
			}
			$user_select->addFormInfo(
				Form::getCheckbox($lang->def('_REPORT_FOR_ALL'), 'all_users', 'all_users', 1, $_SESSION['report_tempdata']['rows_filter']['all_users']).//($_SESSION['report_tempdata']['rows_filter']['all_users'] ? 1 : 0)).
				Form::getBreakRow().
				Form::getHidden('org_chart_subdivision', 'org_chart_subdivision', $org_chart_subdivision).
				Form::getHidden('is_updating', 'is_updating', 1)
			);
			$user_select->setPageTitle($this->page_title);
			
			//$user_select->resetSelection($_SESSION['report_tempdata']['rows_filter']['users']);
			$user_select->loadSelector(str_replace('&', '&amp;', $jump_url),
				false,
				$lang->def('_CHOOSE_USER_FOR_REPORT'),
				true,
				true );
				
		}

	}

	//filter functions
	function get_courses_filter() {

		//style for boxes
		addCss('style_filterbox');

		$out=&$GLOBALS['page'];
		$out->setWorkingZone('content');

		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

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
		
		if(!isset($GLOBALS['jscal_loaded']) || $GLOBALS['jscal_loaded'] == false) {

			$lang_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
			$cut_at = strpos($lang_code, ';');
			if($cut_at == 0) {
				$lang_code = substr($lang_code, 0);
			} else {
				$lang_code = substr($lang_code, 0, $cut_at);
			}

			$sep = ( substr($GLOBALS['where_framework_relative'], -1) != '/' ? '/' : '' );
			if(file_exists($GLOBALS['where_framework_relative'].$sep.'/addons/calendar/lang/calendar-'.$lang_code.'.js')) {

				$lang_script = $GLOBALS['where_framework_relative'].$sep.'addons/calendar/lang/calendar-'.$lang_code.'.js';
			} else {

				$lang_script = $GLOBALS['where_framework_relative'].$sep.'addons/calendar/lang/calendar-en.js';
			}
			if(isset($GLOBALS['page'])) {
				$GLOBALS['page']->add("\n"
					.'<link href="'.getPathTemplate('framework').'style/calendar/calendar-blue.css" rel="stylesheet" type="text/css" />'."\n"
					.'<script type="text/javascript" src="'
					.$GLOBALS['where_framework_relative'].$sep.'addons/calendar/calendar.js"></script>'."\n"
					.'<script type="text/javascript" src="'.$lang_script.'"></script>'."\n"
					.'<script type="text/javascript" src="'
					.$GLOBALS['where_framework_relative'].$sep.'addons/calendar/calendar-setup.js"></script>'."\n", 'page_head');
			}
			$GLOBALS['jscal_loaded'] = true;
		}


		$time_belt = array(
			0 		=> $lang->def('_CUSTOM_BELT'),
			7 		=> $lang->def('_LAST_WEEK'),
			31		=> $lang->def('_LAST_MONTH'),
			93 		=> $lang->def('_LAST_THREE_MONTH'),
			186 	=> $lang->def('_LAST_SIX_MONTH'),
			365 	=> $lang->def('_LAST_YEAR'),
		);


		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			//go back at the previous step
			jumpTo($back_url);
			//...
		}

		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		$fman = new FieldList();
		$fields = $fman->getFlatAllFields();
		$custom = array();
		foreach ($fields as $key=>$val) {
			$custom[] = array('id'=>$key, 'label'=>$val, 'selected'=>false);
		}

		//set $_POST data in $_SESSION['report_tempdata']
		if (!isset($_SESSION['report_tempdata']['columns_filter'])) {
			$_SESSION['report_tempdata']['columns_filter'] = array(
					'time_belt' => array(
						'time_range'=>'',
						'start_date'=>'',
						'end_date'=>''
					),	//2 dates
					'org_chart_subdivision' => 0,
					'all_courses' => true,
					'selected_courses' => array(),
					'sub_filters' => array(),
					'filter_exclusive' => 1,
					'showed_columns' => array(),
					'custom_fields' => $custom
				);
		}
		$ref =& $_SESSION['report_tempdata']['columns_filter']; //echo print_r($ref,true);

		$selector = new Selector_Course();
		$selection =& $ref['selected_courses'];

		if (isset($_POST['update_tempdata'])) {
			$selector->parseForState($_POST);
			
			// parse for date fields

			$opt_type = array();
			foreach($this-> courses_filter_definition as $fd) {	
				$opt_type[$fd['key']] = $fd['type'];
			}

			if (isset($_POST['courses_filter']))
				while(list($ind, $filter_data) = each($_POST['courses_filter'])) {
					if($opt_type[$filter_data['option']] == _FILTER_DATE)
						$_POST['courses_filter'][$ind]['value'] = $GLOBALS['regset']->regionalToDatabase($filter_data['value'], 'date');
				}

			$temp=array(
				/*'time_belt' => array(
												'time_range'=>$_POST['time_belt'],
												'start_date'=>$_POST['start_time'],
												'end_date'=>$_POST['end_time']
											),	//2 dates	*/
				'org_chart_subdivision' 	=> (isset($_POST['org_chart_subdivision']) ? 1 : 0),
				'all_courses'				 => ($_POST['all_courses']==1 ? true : false),
				'selected_courses' 			=> $selector->getSelection(),
				'sub_filters' 				=> (isset($_POST['courses_filter']) ? $_POST['courses_filter'] : array()),
				'filter_exclusive' 			=> ( isset($_POST['filter_exclusive']) ? $_POST['filter_exclusive'] : false ),
				'showed_columns' 			=> (isset($_POST['cols']) ? $_POST['cols'] : array()),
				'custom_fields' => $custom
			);

			foreach ($ref['custom_fields'] as $val) {
				$temp['custom_fields'][]=array(
					'id'=>$val['id'],
					'label'=>$val['label'],
					'selected'=>(isset($_POST['custom'][ $val['id'] ]) ? true : false)
				);
			}

			$ref = $temp;
		} else {
			$selector->resetSelection($selection);

			//get users' custom fields
			if (!isset($ref['custom_fields'])) {
				require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
				$fman = new FieldList();
				$fields = $fman->getFlatAllFields();
				$custom = array();
				foreach ($fields as $key=>$val) {
					$custom[] = array('id'=>$key, 'label'=>$val, 'selected'=>false);
				}
				$ref['custom_fields'] = $custom;
			} else {
					foreach ($ref['custom_fields'] as $val) {
						$temp['custom_fields'][]=array(
								'id'=>$val['id'],
								'label'=>$val['label'],
								'selected'=>(isset($_POST['custom'][ $val['id'] ]) ? true : false)
						);
					}
			}
		}
		
		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}

		cout(
			//Form::openForm('user_report_columns_courses', $jump_url)
			Form::getHidden('update_tempdata', 'update_tempdata', 1)
		);

		$lang = $this->lang;
		$temp = count($selection);

		$box = new ReportBox('course_selector');
		$box->title = $lang->def('_REPORT_COURSE_SELECTION');
		$box->description = false;
		$box->body .= '<div class="fc_filter_line filter_corr">';
		$box->body .= '<input id="all_courses" name="all_courses" type="radio" value="1" '.($ref['all_courses'] ? 'checked="checked"' : '').' />';
		$box->body .= '<label for="all_courses">'.$lang->def('_ALL_COURSES').'</label>';
		$box->body .= '<input id="sel_courses" name="all_courses" type="radio" value="0" '.($ref['all_courses'] ? '' : 'checked="checked"').' />';
		$box->body .= '<label for="sel_courses">'.$lang->def('_SEL_COURSES').'</label>';
		$box->body .= '</div>';

		$box->body .= '<div id="selector_container"'.($ref['all_courses'] ? ' style="display:none"' : '').'>';
		//$box->body .= Form::openElementSpace();
		$box->body .= $selector->loadCourseSelector(true);
		//$box->body .= Form::closeElementSpace();
		$box->body .= '</div>';
		$box->footer = $lang->def('_CURRENT_SELECTION').':&nbsp;<span id="csel_foot">'.($ref['all_courses'] ? $lang->def('_ALLCOURSES_FOOTER') : ($temp!='' ? $temp : '0')).'</span>';
		cout($box->get());

		cout(
			'<script type="text/javascript">courses_count='.($temp=='' ? '0' : $temp).';'.
			'courses_all="'.$lang->def('_ALLCOURSES_FOOTER').'";</script>' );

		//example selection options

		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$seldata = $this->courses_filter_definition;

		$filter_cases = array(
			'_FILTER_INTEGER' => _FILTER_INTEGER,
			'_FILTER_DATE' => _FILTER_DATE
		);

		$json = new Services_JSON();
		$js_seldata = $json->encode($seldata);
		$js_filter_cases = $json->encode($filter_cases);
		$out->add('<script type="text/javascript">'."\n".
			//'_temp_seldata='.$js_seldata.';'."\n".
			  'seldata_JSON='.$js_seldata.';'."\n".
							'filter_cases_JSON=\''.$js_filter_cases.'\';'."\n".
			  'courses_sel_opt_0=\''.$lang->def('_COURSES_DROPDOWN_NULL_SELECT').'\';'."\n".
			  'courses_remove_filter=\''.$lang->def('_REMOVE_FILTER').'\';'."\n".
			  'var course_date_token=\''.$GLOBALS['regset']->date_token.'\';'."\n".
			  'YAHOO.util.Event.addListener(window,"load",courses_init);'."\n".
			  '</script>', 'page_head');

		//box for course filter conditions
		$temp =& $_SESSION['report_tempdata']['columns_filter']['sub_filters'];
		$inc_counter = count($temp);
		$already = '';
		$script_init = 'YAHOO.util.Event.addListener(window, "load", function() {';

		if(is_array($temp))
		foreach ($temp as $key=>$value) { //create filters html
			$index=str_replace('i', '', $key);

			$already.='<div id="courses_filter_'.$index.'">';
			
			//generate option selection
			$already.='<select id="courses_filter_sel_'.$index.'" name="courses_filter_sel[]">';
			$already.='<option value="0">'.$lang->def('_COURSES_DROPDOWN_NULL_SELECT').'</option>';
			foreach ($seldata as $selval) {
				if ($value['option']==$selval['key']) $selected=' selected="selected"'; else $selected='';
				$already.='<option value="'.$selval['key'].'"'.$selected.'>'.$selval['label'].'</option>';
			}
			$already.='</select>';

			$already.='<span id="courses_filter_params_'.$index.'">';

			//generate sign selection
			$signs=array('<','<=','=','>=','>');
			$already.='<select name="courses_filter['.$key.'][sign]">';
			foreach ($signs as $k2=>$v2) {
				if ($value['sign']==$v2) $selected=' selected="selected"'; else $selected='';
				$already.='<option value="'.$v2.'"'.$selected.'>'.$v2.'</option>';
			}
			$already.='</select>';

			//generate value input
			$type=false;
			foreach ($this->courses_filter_definition as $def) { //this should be a switch
				if ($value['option']==$def['key']) $type=$def['type'];
			}

			$already.='<input class="align_right" type="text" style="width: '.
			($type==_FILTER_DATE ? '7' : '9').'em;" '.
				'name="courses_filter['.$key.'][value]" value="'.$GLOBALS['regset']->databaseToRegional($value['value'], 'date').'"'.
				' id="courses_filter_'.$index.'_value" />';

			if ($type==_FILTER_DATE) {
				$already.='<button class="trigger_calendar" id="trigger_'.$index.'"></button>';
				$script_init.='cals.push(new Calendar.setup({
					inputField : "courses_filter_'.$index.'_value",
					ifFormat : "'.$GLOBALS["regset"]->date_token.'",
					button : "trigger_'.$index.'",
					showsTime : false }) );';
			}

			//generate hidden index
			$already.='<input type="hidden" name="courses_filter['.$key.'][option]" '.
				'value="'.$value['option'].'" /></span>';

			//generate remove link
			$already.='<a href="javascript:courses_removefilter('.$index.');">'.
			$lang->def('_REMOVE_FILTER').'</a>';

			$already.='</div>';

			$script_init.='YAHOO.util.Event.addListener("courses_filter_sel_'.$index.'", "change", courses_create_filter);';
		}

		$script_init .= '} );';
		$already .= '<script type="text/javascript">'.$script_init.'</script>';

		$temp = ( isset($_SESSION['report_tempdata']['columns_filter']['filter_exclusive']) ? $_SESSION['report_tempdata']['columns_filter']['filter_exclusive'] : 1 );
		$selected = ' checked="checked"';

		$box = new ReportBox('course_subfilters');
		$box->title = $lang->def('_REPORT_COURSE_CONDITIONS');
		$box->description = '';
		$box->body =
		Form::getBreakRow()
		//.Form::openElementSpace()
		//.Form::getOpenFieldset('', 'fieldset_courses_conditions')
		.'<div id="courses_filter_list">'.$already.'</div>'

		.'<div class="fc_filter_line filter_corr">'
		.'<input type="radio" id="filter_exclusive_and" name="filter_exclusive" value="1" '.($temp>0 ? $selected : '').' />
				<label for="filter_exclusive_and">'.$lang->def('_FILTER_ALL_CONDS').'</label>&nbsp;'

		.'<input type="radio" id="filter_exclusive_or" name="filter_exclusive" value="0" '.($temp==0 ? $selected : '').' />
				<label for="filter_exclusive_or">'.$lang->def('_FILTER_ONE_COND').'</label>'
		.'</div>'

		.'<div class="fc_filter_line">'
		.'<span class="yui-button yui-link-button" id="fc_addfilter">
					<span class="first-child">
						<a href="#" onclick="courses_addfilter();return false;">'.$lang->def('_FILTER_ADD').'</a>
					</span>
				</span>'
		.'<span class="yui-button yui-link-button" id="fc_cancfilter">
					<span class="first-child">
						<a href="#" onclick="courses_resetfilters();return false;">'.$lang->def('_FILTER_RESET').'</a>
					</span>
				</span>'
		.'</div>'


		.Form::getHidden('inc_counter', 'inc_counter', $inc_counter)

		.'</div>';
		cout($box->get());

		function is_showed($which) {
			if (isset($_SESSION['report_tempdata']['columns_filter'])) {
				return in_array($which, $_SESSION['report_tempdata']['columns_filter']['showed_columns']);
			} else return false;
		}

		//box for columns selection
		$box = new ReportBox('columns_selection');
		$box->title = $lang->def('_SELECT_COLUMS');
		$box->description = $lang->def('_SELECT_THE_DATA_COL_NEEDED');

		//Form::openElementSpace()
		if (count($ref['custom_fields'])>0) {
			$box->body .= Form::getOpenFieldset($lang->def('_USER_CUSTOM_FIELDS'), 'fieldset_course_fields');
			foreach ($ref['custom_fields'] as $key=>$val) {
				$box->body .= Form::getCheckBox($val['label'], 'col_custom_'.$val['id'], 'custom['.$val['id'].']', $val['id'], $val['selected']);
			}
			$box->body .= Form::getCloseFieldset();
		}

		$box->body.=
		Form::getOpenFieldset($lang->def('_COURSE_FIELDS'), 'fieldset_course_fields')
		.Form::getCheckBox($lang->def('_TH_CAT'), 'col_sel_category', 'cols[]', '_TH_CAT', is_showed('_TH_CAT'))
		.Form::getCheckBox($lang->def('_CODE'), 'col_sel_coursecode', 'cols[]', '_TH_CODE', is_showed('_TH_CODE'))
		.Form::getCheckBox($lang->def('_TH_COURSEPATH'), 'col_sel_coursepath', 'cols[]', '_TH_COURSEPATH', is_showed('_TH_COURSEPATH'))
		.Form::getCheckBox($lang->def('_STATUS'), 'col_sel_status', 'cols[]', '_TH_COURSESTATUS', is_showed('_TH_COURSESTATUS'))
		.Form::getCloseFieldset()

		.Form::getOpenFieldset($lang->def('_COURSE_FIELDS_INFO'), 'fieldset_course_fields')
		.Form::getCheckBox($lang->def('_TH_USER_INSCRIPTION_DATE'), 'user_inscription_date', 'cols[]', '_TH_USER_INSCRIPTION_DATE', is_showed('_TH_USER_INSCRIPTION_DATE'))
		.Form::getCheckBox($lang->def('_TH_USER_START_DATE'), 'user_start_date', 'cols[]', '_TH_USER_START_DATE', is_showed('_TH_USER_START_DATE'))
		.Form::getCheckBox($lang->def('_TH_USER_END_DATE'), 'user_end_date', 'cols[]', '_TH_USER_END_DATE', is_showed('_TH_USER_END_DATE'))
		.Form::getCheckBox($lang->def('_TH_LAST_ACCESS_DATE'), 'last_access_date', 'cols[]', '_TH_LAST_ACCESS_DATE', is_showed('_TH_LAST_ACCESS_DATE'))
		.Form::getCheckBox($lang->def('_STATUS'), 'user_status', 'cols[]', '_TH_USER_STATUS', is_showed('_TH_USER_STATUS'))
		.Form::getCheckBox($lang->def('_TH_USER_START_SCORE'), 'user_start_score', 'cols[]', '_TH_USER_START_SCORE', is_showed('_TH_USER_START_SCORE'))
		.Form::getCheckBox($lang->def('_TH_USER_FINAL_SCORE'), 'user_final_score', 'cols[]', '_TH_USER_FINAL_SCORE', is_showed('_TH_USER_FINAL_SCORE'))
		.Form::getCheckBox($lang->def('_TH_USER_COURSE_SCORE'), 'user_course_score', 'cols[]', '_TH_USER_COURSE_SCORE', is_showed('_TH_USER_COURSE_SCORE'))
		.Form::getCheckBox($lang->def('_TH_USER_NUMBER_SESSION'), 'user_number_session', 'cols[]', '_TH_USER_NUMBER_SESSION', is_showed('_TH_USER_NUMBER_SESSION'))
		.Form::getCheckBox($lang->def('_TH_USER_ELAPSED_TIME'), 'user_elapsed_time', 'cols[]', '_TH_USER_ELAPSED_TIME', is_showed('_TH_USER_ELAPSED_TIME'))
		.Form::getCheckBox($lang->def('_TH_ESTIMATED_TIME'), 'estimated_time', 'cols[]', '_TH_ESTIMATED_TIME', is_showed('_TH_ESTIMATED_TIME'))
		.Form::getCloseFieldset()

		//.Form::closeElementSpace()
		;

		cout($box->get());

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		//buttons
		/*cout(
			 Form::openButtonSpace()
			.Form::getBreakRow()
		.Form::getButton('ok_filter', 'import_filter', $lang->def('_SAVE_BACK', 'report'))
		.Form::getButton('show_filter', 'show_filter', $lang->def('_SAVE_SHOW', 'report'))
		.Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO', 'report'))
		.Form::closeButtonSpace()
			.Form::closeForm());

		cout('</div>'); //close std_block div*/
	}

	function get_competences_filter() {
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

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
		addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/','competences_filter.js');

		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			jumpTo($back_url);
		}
		if (get_req('is_updating', DOTY_INT, 0)>0) {
			$_SESSION['report_tempdata']['columns_filter'] = array(
				'filters_list' => get_req('rc_filter', DOTY_MIXED, array()),
				'exclusive' => (get_req('rc_filter_exclusive', DOTY_INT, 0)>0 ? true : false)
			);
		} else {
			if (!isset($_SESSION['report_tempdata']['columns_filter']))
			$_SESSION['report_tempdata']['columns_filter'] = array(
					'filters_list' => array(),
					'exclusive' => true
			);
		}
		$ref =& $_SESSION['report_tempdata']['columns_filter'];

		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}

		$_man = new Competences_Manager();

		$opts=$_man->GetCompetencesGroupedList();
		$optdata=array();
		foreach($opts as $k=>$val) { //categories cycle
			$temp=array();
			$temp['name'] = $val['name'];
			$temp['rows'] = array();
			foreach ($val['rows'] as $key=>$value) { //competences cycle
				$temp2=array();
				$temp2['id']=$key;
				$temp2['name']=$value;//str_replace("'", "\'", $value);
				$temp2['type']=$_man->GetCompetenceType($key);
				$scores=$_man->GetCompetenceScores($key);
				$temp2['score_min']=$scores['score_min'];
				$temp2['score_max']=$scores['score'];
				$temp['rows'][]=$temp2;
			}
			$optdata[]=$temp;
		}

		$prevdata = $ref['filters_list'];//array();
		
		$json = new Services_JSON();

		$js_prevdata = $json->encode($prevdata);
		$js_optdata = str_replace("'", "\'", $json->encode($optdata));

		cout( '<script type="text/javascript">'.
		  'optdata_JSON=\''.$js_optdata.'\';'.
		  'rc_sel_opt_0=\''.$lang->def('_COMPETENCES_DROPDOWN_NULL_SELECT').'\';'.
		  'rc_remove_filter=\''.$lang->def('_COMPETENCES_REMOVE_FILTER').'\';'.
		  'rc_initial_filters='.$js_prevdata.';'.
		  'YAHOO.util.Event.addListener(window,"load",rc_init);'.
			//'rc_auto_inc='.(count($ref)+1).';'.
		  '</script>', 'page_head');

		$clang = $this->lang;
		$sel = ($ref['exclusive'] ? 1 : 0);
		$selected = ' checked="checked"';
		$box = new ReportBox();

		$box->title = $this->lang->def('_COMPETENCESFILTER_TITLE');
		$box->description = $this->lang->def('_COMPETENCESFILTER_TITLE_DESC');
		$box->body = Form::getBreakRow()
		.Form::getHidden('is_updating', 'is_updating', 1)
		/*	.Form::getOpenFieldset($lang->def('_COMPETENCES_FILTER'), 'fieldset_'.$this->id_report)
			.'<div id="rc_filter_list" style="padding-bottom:10px;"></div>'
			.'<button type="button" class="button" onclick="rc_addfilter();">'.$lang->def('_RC_FILTER_ADD').'</button>'
			.'<button type="button" class="button" onclick="rc_resetfilters();">'.$lang->def('_RC_FILTER_RESET').'</button>'
			.Form::getCloseFieldset()
			.Form::getCheckbox(	$lang->def('_COMPETENCES_FILTER_EXCLUSIVE'),
								'rc_filter_exclusive',//_'.$this->id_report,
								'rc_filter_exclusive',
								1 )*/

		.'<div id="rc_filter_list"></div>'

		.'<div class="fc_filter_line filter_corr">'
		.'<input type="radio" id="rc_filter_exclusive_and" name="rc_filter_exclusive" value="1" '.($sel>0 ? $selected : '').' />
				<label for="rc_filter_exclusive_and">'.$clang->def('_FILTER_ALL_CONDS').'</label>&nbsp;'

		.'<input type="radio" id="rc_filter_exclusive_or" name="rc_filter_exclusive" value="0" '.($sel==0 ? $selected : '').' />
				<label for="rc_filter_exclusive_or">'.$clang->def('_FILTER_ONE_COND').'</label>'
		.'</div>'

		.'<div class="fc_filter_line">'
		.'<span class="yui-button yui-link-button" id="fc_addfilter">
					<span class="first-child">
						<a href="#" onclick="rc_addfilter();return false;">'.$clang->def('_FILTER_ADD').'</a>
					</span>
				</span>'
		.'<span class="yui-button yui-link-button" id="fc_cancfilter">
					<span class="first-child">
						<a href="#" onclick="rc_resetfilters();return false;">'.$clang->def('_FILTER_RESET').'</a>
					</span>
				</span>'
		.'</div>';

		//cout(Form::openForm('report_'.$this->id_report.'_comptencesfilter', $jump_url));
		cout($box->get());
		cout(Form::getBreakRow());
		/*cout(
			.Form::openButtonSpace()
			.Form::getBreakRow()
		.Form::getButton('ok_filter', 'import_filter', $lang->def('_SAVE_BACK'))
		.Form::getButton('show_filter', 'show_filter', $lang->def('_SAVE_SHOW'))
		.Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
			.Form::closeForm());*/
	}


	function show_report_courses($report_data = NULL, $other = '') {
		$jump_url = ''; //show_report

		checkPerm('view');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		if (isset($_POST['send_mail_confirm']))
		$op = 'send_mail_confirm';
		elseif (isset($_POST['send_mail'])) {
			$op = 'send_mail';
		} else {
			$op = 'show_result';
		}

		switch ($op) {

			case 'send_mail_confirm': {
				$subject = importVar('mail_object', false, '['.$lang->def('_SUBJECT').']' );//'[No subject]');
				$body = importVar('mail_body', false, '');
				$acl_man = new DoceboACLManager();
				$user_info = $acl_man->getUser(getLogUserId(), false);
				if ($user_info)
				{
					$sender = $user_info[ACL_INFO_EMAIL];
				}
				$mail_recipients = unserialize(urldecode(get_req('mail_recipients', DOTY_STRING, '')));

				// prepare intestation for email
				$from = "From: ".$sender.$GLOBALS['mail_br'];
				$header  = "MIME-Version: 1.0".$GLOBALS['mail_br']
				."Content-type: text/html; charset=".getUnicode().$GLOBALS['mail_br'];
				$header .= "Return-Path: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "Reply-To: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Sender: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Mailer: PHP/". phpversion().$GLOBALS['mail_br'];

				// send mail
				$arr_recipients = array();
				foreach($mail_recipients as $recipient) {
					$rec_data = $acl_man->getUser($recipient, false);
					//mail($rec_data[ACL_INFO_EMAIL] , stripslashes($subject), stripslashes(nl2br($body)), $from.$header."\r\n");
					$arr_recipients[] = $rec_data[ACL_INFO_EMAIL];
				}
				$mailer = DoceboMailer::getInstance();
				$mailer->SendMail($sender, $arr_recipients, stripslashes($subject), stripslashes(nl2br($body)));

				$result = getResultUi($lang->def('_MAIL_SEND_OK'));

				cout( $this->_get_courses_query('html',NULL,$result) );
			} break;

			case 'send_mail': {
				require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
				$mail_recipients = get_req('mail_recipients', DOTY_MIXED, array());
				cout(''//Form::openForm('course_selection', str_replace('&', '&amp;', $jump_url))
					.Form::openElementSpace()
					.Form::getTextfield($lang->def('_MAIL_OBJECT'), 'mail_object', 'mail_object', 255)
					.Form::getTextarea($lang->def('_MAIL_BODY'), 'mail_body', 'mail_body')
					.Form::getHidden('mail_recipients', 'mail_recipients', urlencode(serialize($mail_recipients)))
					.Form::closeElementSpace()
					.Form::openButtonSpace()
					.Form::getButton('send_mail_confirm', 'send_mail_confirm', $lang->def('_SEND_MAIL'))
					.Form::getButton('undo_mail', 'undo_mail', $lang->def('_UNDO'))
					.Form::closeButtonSpace()
					//.Form::closeForm()
					.'</div>', 'content');
			} break;

			default: {
				cout( $this->_get_courses_query('html', $report_data, $other) );
			}

		}
	}

	/**
	 * Return the output in the selected format for the report with the filters given
	 * @param string $type output type
	 * @param array $report_data a properly formatted list of rule to follow
	 * @param string $other
	 * @return string the properly formated report
	 */
	function _get_courses_query($type='html', $report_data = NULL, $other='') {

		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$output = '';
		$jump_url = '';
		$org_chart_subdivision = 0; // not implemented
		$elem_selected = array();

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$acl_man 		= new DoceboACLManager();
		$acl_man->include_suspended = TRUE;
		$course_man 	= new Man_Course();

		// read form _SESSION (XXX: change this) the report setting
		$filter_userselection	= ( !$report_data && isset($_SESSION['report_tempdata']['rows_filter']['users']) 
			? $_SESSION['report_tempdata']['rows_filter']['users'] : $report_data['rows_filter']['users'] );
		
		$filter_columns			= ( !$report_data && $_SESSION['report_tempdata']['columns_filter']
			? $_SESSION['report_tempdata']['columns_filter'] : $report_data['columns_filter'] );
		
		if(!$report_data && isset($_SESSION['report_tempdata']['rows_filter']['all_users'])) {
			$alluser =  ( $_SESSION['report_tempdata']['rows_filter']['all_users'] ? 1 : 0);
		} else {
			$alluser =  ( $report_data['rows_filter']['all_users'] ? 1 : 0);
		}
		// break filters into a more usable format
		$filter_allcourses		= $filter_columns['all_courses'];
		$filter_courseselection =& $filter_columns['selected_courses'];
		
		// retrive the user selected
		if($alluser > 0) {
			// all the user selected (we can avoid this ? no we need to hide the suspended users)
			$user_selected 	=& $acl_man->getAllUsersIdst();
		} else {
			// resolve the user selection
			$user_selected 	=& $acl_man->getAllUsersFromIdst($filter_userselection);
		}
		// if we must subdived the users into the org_chart folders we must retrive some extra info
		if($org_chart_subdivision == 1) {

			require_once($GLOBALS['where_framework'].'/lib/lib.orgchart.php');
			$org_man 	= new OrgChartManager();
			if($alluser == 1) $elem_selected = $org_man->getAllGroupIdFolder();
			else $elem_selected = $user_selected;

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
		}
		if(empty($user_selected)) {

			$GLOBALS['page']->add($lang->def('_NULL_SELECTION'), 'content');
			return;
		}
		$users_info =& $acl_man->getUsers($user_selected);

		// Retrive all the course
		$id_courses = $course_man->getAllCourses();
		if(empty($id_courses)) {

			return $lang->def('_NULL_COURSE_SELECTION');
		}

		$re_category = mysql_query("
		SELECT idCategory, path
		FROM ".$GLOBALS['prefix_lms']."_category");
		$category_list = array(0 => $lang->def('_ROOT_CATEGORY'));
		$category_path_list = array(0 => '/');
		while(list($id_cat, $name_cat) = mysql_fetch_row($re_category)) {
			$category_list[$id_cat] = substr($name_cat, strrpos($name_cat, '/') + 1 );
			$category_path_list[$id_cat] = substr( $name_cat, 5 , (strlen($name_cat)-5) ); //eliminates "/root"
		}

		$time_list = array();
		$session_list = array();
		$lastaccess_list = array();

		$query = "
		SELECT idUser, idCourse, COUNT(*), SUM(UNIX_TIMESTAMP(lastTime) - UNIX_TIMESTAMP(enterTime)), MAX(lastTime)
		FROM ".$GLOBALS['prefix_lms']."_tracksession
		WHERE 1 ".
			( $alluser > 0 ? "" : "AND idUser IN ( ".implode(',', $user_selected)." ) ").
			( $filter_allcourses ? "" : "AND idCourse IN (".implode(',', $filter_courseselection).") ");
		//if($start_time != '') $query .= " AND enterTime >= '".$start_time."' ";
		//if($end_time != '') $query .= " AND lastTime <= '".$end_time."' ";
		$query .= "GROUP BY idUser, idCourse ";
		$re_time = mysql_query($query);
		while(list($id_u, $id_c, $session_num, $time_num, $last_num) = mysql_fetch_row($re_time)) {

			$session_list[$id_u.'_'.$id_c] 		= $session_num;
			$time_list[$id_u.'_'.$id_c] 		= $time_num;
			$lastaccess_list[$id_u.'_'.$id_c] 	= $last_num;
		}
		//recover start and final score
		require_once($GLOBALS['where_lms'].'/lib/lib.orgchart.php');
		$org_man = new OrganizationManagement(false);

		$score_start = $org_man->getStartObjectScore($user_selected, array_keys($id_courses));
		$score_final = $org_man->getFinalObjectScore($user_selected, array_keys($id_courses));


		require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
		$rep_man = new CourseReportManager();

		$score_course = $rep_man->getUserFinalScore($user_selected, array_keys($id_courses));

		if($org_chart_subdivision == 0) {

			// find some information
			$query_course_user = "
			SELECT cu.idUser,
				c.code, c.idCourse, c.idCategory, c.name, c.status,
				cu.status, cu.date_inscr, cu.date_first_access, cu.date_complete, c.mediumTime
			FROM  ".$GLOBALS['prefix_lms']."_courseuser AS cu
				JOIN ".$GLOBALS['prefix_lms']."_course AS c ".   ", ".$GLOBALS['prefix_fw']."_user as u "."
			WHERE cu.idCourse = c.idCourse "
			." AND cu.idUser = u.idst AND u.valid = 1 ".
			( $alluser > 0 ? "" : "AND cu.idUser IN ( ".implode(',', $user_selected)." ) ").
			( $filter_allcourses ? "" : "AND c.idCourse IN (".implode(',', $filter_courseselection).") ")." ORDER BY u.userid, u.lastname, u.firstname, c.name";
			//if($start_time != '') $query_course_user .= " AND cu.date_inscr >= '".$start_time."' ";
			//if($end_time != '') $query_course_user .= " AND cu.date_inscr <= '".$end_time."' AND cu.level='3' ";

			//$output .= $this->_printTable_courses($type, $acl_man, $query_course_user, $users_info, $category_list, $session_list, $lastaccess_list, $time_list, $score_start, $score_final, $score_course, $filter_columns);
			$output .= $this->_printTable_courses($type, $acl_man, $query_course_user, $users_info, $category_list, $category_path_list, $session_list, $lastaccess_list, $time_list, $score_start, $score_final, $score_course, /*$filter_userselection*/$user_selected, $filter_columns);

		} else {

			$date_now = $GLOBALS['regset']->databaseToRegional(date("Y-m-d H:i:s"));

			reset($org_name);
			while(list($idst_group, $folder_name) = each($org_name)) {

				$GLOBALS['page']->add('<div class="datasummary">'
					.'<b>'.$lang->def('_FOLDER_NAME').' :</b> '.$folder_name['name']
					.( $folder_name['type_of_folder'] == ORG_CHART_WITH_DESCENDANTS ? ' ('.$lang->def('_WITH_DESCENDANTS').')' : '' ).'<br />'
					, 'content');
				/*if($start_time != '' || $end_time != '') {

					$GLOBALS['page']->add('<b>'.$lang->def('_TIME_BELT_2').' :</b> '
						.( $start_time != '' 	? ' <b>'.$lang->def('_START_TIME').' </b>'.$GLOBALS['regset']->databaseToRegional($start_time, 'date') 	: '' )
						.( $end_time != '' 		? ' <b>'.$lang->def('_END_TIME').' </b>'.$GLOBALS['regset']->databaseToRegional($end_time, 'date') 		: '' )
						.'<br />'
						, 'content');
				}*/
				$GLOBALS['page']->add( '<b>'.$lang->def('_ANALYSIS_DATE').' :</b> '.$date_now.'<br />'
					.'</div>', 'content');

				$group_user = $acl_man->getGroupAllUser($idst_group);

				// find some information
				$query_course_user = "
				SELECT cu.idUser,
					c.code, c.idCourse, c.idCategory, c.name, c.status,
					cu.status, cu.date_inscr, cu.date_first_access, cu.date_complete, c.mediumTime
				FROM  ".$GLOBALS['prefix_lms']."_courseuser AS cu
					JOIN ".$GLOBALS['prefix_lms']."_course AS c ".   ", ".$GLOBALS['prefix_fw']."_user as u "."
				WHERE cu.idCourse = c.idCourse AND cu.idUser IN ( ".implode(',', $group_user)." ) ".   " AND cu.idUser=u.idst AND u.valid=1 ".
				($filter_allcourses ? "" : " AND c.idCourse IN (".implode(',', $filter_courseselection).") ")." ORDER BY u.userid, u.lastname, u.firstname, c.name";
				//if($start_time != '') $query_course_user .= " AND cu.date_inscr >= '".$start_time."' ";
				//if($end_time != '') $query_course_user .= " AND cu.date_inscr <= '".$end_time."'  AND cu.level='3'";

				//$output .= $this->_printTable_courses($type, $acl_man, $query_course_user, $users_info, $category_list, $session_list, $lastaccess_list, $time_list, $score_start, $score_final, $score_course, $filter_columns);
				$output .= $this->_printTable_courses($type, $acl_man, $query_course_user, $users_info, $category_list, $category_path_list, $session_list, $lastaccess_list, $time_list, $score_start, $score_final, $score_course, /*$filter_userselection*/$user_selected, $filter_columns);
			}
		}

		return $output;
	}


	function _check($cmp1, $cmp2, $sign, $type=_FILTER_INTEGER) {
		$output = false;

		switch ($type) {

			case _FILTER_INTEGER: {
				if ($cmp1=='') $cmp1 = 0;
				if ($cmp2=='') $cmp2 = 0;
			} break;

			case _FILTER_DATE: {
				$cmp1 = ($cmp1!="" ? substr($cmp1, 0, 10) : 0);
				$cmp2 = ($cmp2!="" ? substr($cmp2, 0, 10) : 0);
			}

		}
		
		//make comparison
		switch ($sign) {
			case '<'  : $output = $cmp1 <  $cmp2; break;
			case '<=' :	$output = $cmp1 <= $cmp2; break;
			case '='  : $output = $cmp1 ==  $cmp2; break;
			case '>=' : $output = $cmp1 >= $cmp2; break;
			case '>'  : $output = $cmp1 >  $cmp2; break;
		}

		return $output;
	}



	//function _printTable_courses($type='html', &$acl_man, $query_course_user, &$users_info, $category_list, &$session_list, &$lastaccess_list, &$time_list, &$score_start, &$score_final, &$score_course, &$filter_columns) {
	function _printTable_courses($type, &$acl_man, $query_course_user, &$users_info, &$category_list, &$category_path_list, &$session_list, &$lastaccess_list, &$time_list, &$score_start, &$score_final, &$score_course, &$filter_rows, &$filter_columns) {
		require_once('report_tableprinter.php');

		if(!$type) $type = 'html';
		$buffer = new ReportTablePrinter($type);
        
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$cols = $filter_columns['showed_columns'];
		$output = '';

		$buffer->openTable($lang->def('_RU_CAPTION'), $lang->def('_RU_SUMMAMRY_MANAGMENT'));

		$th1 = array();
		$th2 = array();

		//$theads = '<th>'.$lang->def('_TH_USERNAME').'</th>'."\n\t";
		$th2[] = $lang->def('_TH_USERNAME');
		$th2[] = $lang->def('_FULLNAME');
		
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
		$aclManager = new DoceboACLManager();
		$aclManager->include_suspended = TRUE;
		$_users = $aclManager->getAllUsersFromIdst($filter_rows);
		$fman = new FieldList();
		$colspanuser = 2;
		$field_values = array();
		$customcols =& $filter_columns['custom_fields'];
		foreach ($customcols as $val) {
			if ($val['selected']) {
				$colspanuser++;
				$th2[] = $val['label'];
				$field_values[$val['id']] = $fman->fieldValue((int)$val['id'], $_users);
			}
		}

		$colspan1 = 0;
		$colspan2 = 0;
		$colspan3 = 1;

		if (in_array('_TH_CAT', $cols))	{ $th2[] = $lang->def('_TH_CAT'); $colspan1++; }//{ $theads .= '<th>'.$lang->def('_TH_CAT').'</th>'."\n\t"; $colspan1++; }
		if (in_array('_TH_CODE', $cols)) { $th2[] = $lang->def('_CODE'); $colspan1++; } //{ $theads .= '<th>'.$lang->def('_TH_CODE').'</th>'."\n\t"; $colspan1++; }
		$th2[] = $lang->def('_TH_COURSENAME');//$theads .= '<th>'.$lang->def('_TH_COURSENAME').'</th>'."\n\t";
		if (in_array('_TH_COURSEPATH', $cols)) { $th2[] = $lang->def('_TH_COURSEPATH'); $colspan1++; }
		if (in_array('_TH_COURSESTATUS', $cols)) { $th2[] = $lang->def('_STATUS'); $colspan1++; } //{ $theads .= '<th>'.$lang->def('_TH_COURSESTATUS').'</th>'."\n\t"; $colspan1++; }

		if (in_array('_TH_USER_INSCRIPTION_DATE', $cols)) { $th2[] = $lang->def('_TH_USER_INSCRIPTION_DATE'); $colspan2++; }//{	$theads .= '<th>'.$lang->def('_TH_USER_INSCRIPTION_DATE').'</th>'."\n\t";; $colspan2++; }
		if (in_array('_TH_USER_START_DATE', $cols)) { $th2[] = $lang->def('_TH_USER_START_DATE'); $colspan2++; }//{ $theads .= '<th>'.$lang->def('_TH_USER_START_DATE').'</th>'."\n\t"; $colspan2++; }
		if (in_array('_TH_USER_END_DATE', $cols)) { $th2[] = $lang->def('_TH_USER_END_DATE'); $colspan2++; }//{ $theads .= '<th>'.$lang->def('_TH_USER_END_DATE').'</th>'."\n\t"; $colspan2++; }
		if (in_array('_TH_LAST_ACCESS_DATE', $cols)) { $th2[] = $lang->def('_TH_LAST_ACCESS_DATE'); $colspan2++; }//{ $theads .= '<th>'.$lang->def('_TH_LAST_ACCESS_DATE').'</th>'."\n\t"; $colspan2++; }
		if (in_array('_TH_USER_STATUS', $cols)) { $th2[] = $lang->def('_STATUS'); $colspan2++; }//{ $theads .= '<th>'.$lang->def('_TH_USER_STATUS').'</th>'."\n\t"; $colspan2++; }
		if (in_array('_TH_USER_START_SCORE', $cols)) { $th2[] = $lang->def('_TH_USER_START_SCORE'); $colspan2++; }//{ $theads .= '<th class="image">'.$lang->def('_TH_USER_START_SCORE').'</th>'."\n\t"; $colspan2++; }
		if (in_array('_TH_USER_FINAL_SCORE', $cols)) { $th2[] = $lang->def('_TH_USER_FINAL_SCORE'); $colspan2++; }//{ $theads .= '<th class="image">'.$lang->def('_TH_USER_FINAL_SCORE').'</th>'."\n"; $colspan2++; }
		if (in_array('_TH_USER_COURSE_SCORE', $cols)) { $th2[] = $lang->def('_TH_USER_COURSE_SCORE'); $colspan2++; }//{ $theads .= '<th class="image">'.$lang->def('_TH_USER_COURSE_SCORE').'</th>'."\n"; $colspan2++; }
		if (in_array('_TH_USER_NUMBER_SESSION', $cols)) { $th2[] = $lang->def('_TH_USER_NUMBER_SESSION'); $colspan2++; }//{ $theads .= '<th class="image">'.$lang->def('_TH_USER_NUMBER_SESSION').'</th>'."\n\t"; $colspan2++; }
		if (in_array('_TH_USER_ELAPSED_TIME', $cols)) { $th2[] = $lang->def('_TH_USER_ELAPSED_TIME'); $colspan2++; }//{ $theads .= '<th class="image">'.$lang->def('_TH_USER_ELAPSED_TIME').'</th>'."\n"; $colspan2++; }
		if (in_array('_TH_ESTIMATED_TIME', $cols)) { $th2[] = $lang->def('_TH_ESTIMATED_TIME'); $colspan2++; }//{ $theads .= '<th class="image">'.$lang->def('_TH_ESTIMATED_TIME').'</th>'."\n"; $colspan2++; }
		//checkbox for mail
		if ($this->use_mail) $th2[] = '<img src="'.getPathImage().'standard/email.gif"/>';//'';//$theads .= '<th class="image"></th>';

		$th1 = array(
			array('colspan'=>$colspanuser,  'value'=>$lang->def('_USERS')),
			array('colspan'=>(1+$colspan1), 'value'=>$lang->def('_COURSES')),
			array('colspan'=>(0+$colspan2), 'value'=>$lang->def('_STATUS')),
			''
		);

		$buffer->openHeader();
		$buffer->addHeader($th1);
		$buffer->addHeader($th2);
		$buffer->closeHeader();

		$re_course_user = mysql_query($query_course_user);

		$i = 0;
		$count_rows = 0;

		$buffer->openBody();
		$exclusive = ($filter_columns['filter_exclusive']==1 ? true : false); //1 if exclusive, 0 if inclusive
		while(list($id_user, $code, $id_course, $id_category, $name, $status,
				$status_user, $date_inscr, $date_first_access, $date_complete, $medium_time) =  mysql_fetch_row($re_course_user) ) {

			//$draw_row = $exclusive;
			if (!isset($filter_columns['sub_filters'])) {
				$filter_columns['sub_filters']=array();
			}

			if (count($filter_columns['sub_filters'])<=0) {
				$condition = true; //no conditions to check
			} else {

				$condition = $exclusive;
				foreach ($filter_columns['sub_filters'] as $key=>$value) {
					$temp = false;

					switch ($value['option']) {
						case _COURSES_FILTER_SESSION_NUMBER: {
							if (isset($session_list[$id_user.'_'.$id_course]))
							$temp = $this->_check($session_list[$id_user.'_'.$id_course], $value['value'], $value['sign'], _FILTER_INTEGER);
						} break;

						case _COURSES_FILTER_SCORE_INIT: {
							if (isset($score_start[$id_course][$id_user]))
							$temp = $this->_check($score_start[$id_course][$id_user]['score'], $value['value'], $value['sign'], _FILTER_INTEGER);
						} break;

						case _COURSES_FILTER_SCORE_END: {
							if ( isset($score_final[$id_course][$id_user]))
							$temp = $this->_check($score_final[$id_course][$id_user]['score'], $value['value'], $value['sign'], _FILTER_INTEGER);
						} break;

						case _COURSES_FILTER_INSCRIPTION_DATE: {
							$temp = $this->_check($date_inscr, $value['value'], $value['sign'], _FILTER_DATE);
						} break;

						case _COURSES_FILTER_END_DATE: {
							$temp = $this->_check($date_complete, $value['value'], $value['sign'], _FILTER_DATE);
						} break;

						case _COURSES_FILTER_FIRSTACCESS_DATE: {
							$temp = $this->_check($date_first_access, $value['value'], $value['sign'], _FILTER_DATE);
						} break;

						case _COURSES_FILTER_LASTACCESS_DATE: {
							if (isset($lastaccess_list[$id_user.'_'.$id_course]))
							$temp = $this->_check($lastaccess_list[$id_user.'_'.$id_course], $value['value'], $value['sign'], _FILTER_DATE);
						} break;

						case _COURSES_FILTER_SCORE_COURSE: {
							if (isset($score_course[$id_user][$id_course]))
							$temp = $this->_check($score_course[$id_user][$id_course]['score'], $value['value'], $value['sign'], _FILTER_INTEGER);
						} break;
					}

					if ($exclusive) {
						$condition = ($condition && $temp);
						if (!$condition) break; //if false, no more conditions needed
					} else {
						$condition = ($condition || $temp);
						if ($condition) break; //if true, no more conditions needed
					}

				}
			}

			//cout('<div>'.($condition ? 'true' : 'false').'</div>');
			if ($condition) {

				$username = $acl_man->relativeId($users_info[$id_user][ACL_INFO_USERID]);
				$fullname = ( $users_info[$id_user][ACL_INFO_LASTNAME].$users_info[$id_user][ACL_INFO_FIRSTNAME]
						? $users_info[$id_user][ACL_INFO_LASTNAME].' '.$users_info[$id_user][ACL_INFO_FIRSTNAME]
						: '' );

				$row = array();
				$row[] =  $username;
				$row[] =	$fullname;

				foreach ($customcols as $val) {
					if ($val['selected']) {
						if (isset($field_values[ $val['id'] ][$id_user]))
						$row[] = $field_values[ $val['id'] ][$id_user];//[ $val['id'] ];
						else
						$row[] = '';
					}
				}

				if (in_array('_TH_CAT', $cols)) $row[] = $category_list[$id_category];
				if (in_array('_TH_CODE', $cols)) $row[] = $code;
				$row[] = $name;
				if (in_array('_TH_COURSEPATH', $cols)) $row[] = $category_path_list[$id_category];
				if (in_array('_TH_COURSESTATUS', $cols)) $row[] = $this->_convertStatusCourse($status);

				if (in_array('_TH_USER_INSCRIPTION_DATE', $cols)) $row[] = $GLOBALS['regset']->databaseToRegional($date_inscr);
				if (in_array('_TH_USER_START_DATE', $cols)) $row[] = ( $date_first_access !== NULL ? $GLOBALS['regset']->databaseToRegional($date_first_access) : '&nbsp;');
				if (in_array('_TH_USER_END_DATE', $cols)) $row[] = ( $date_complete !== NULL ? $GLOBALS['regset']->databaseToRegional($date_complete) : '&nbsp;');
				if (in_array('_TH_LAST_ACCESS_DATE', $cols)) $row[] = ( isset($lastaccess_list[$id_user.'_'.$id_course]) ? $GLOBALS['regset']->databaseToRegional($lastaccess_list[$id_user.'_'.$id_course]) : '' );
				if (in_array('_TH_USER_STATUS', $cols)) $row[] = $this->_convertStatusUser($status_user);

				if (in_array('_TH_USER_START_SCORE', $cols)) $row[] = ( isset($score_start[$id_course][$id_user])
					? $score_start[$id_course][$id_user]['score'].' / '.$score_start[$id_course][$id_user]['max_score']
					: '' );

				if (in_array('_TH_USER_FINAL_SCORE', $cols)) $row[] = ( isset($score_final[$id_course][$id_user])
					? $score_final[$id_course][$id_user]['score'].' / '.$score_final[$id_course][$id_user]['max_score']
					: '' );

				if (in_array('_TH_USER_COURSE_SCORE', $cols)) $row[] =  ( isset($score_course[$id_user][$id_course])
					? $score_course[$id_user][$id_course]['score'].' / '.$score_course[$id_user][$id_course]['max_score']
					: '' );

				if (in_array('_TH_USER_NUMBER_SESSION', $cols)) $row[] = ( isset($session_list[$id_user.'_'.$id_course]) ? $session_list[$id_user.'_'.$id_course] : '' );

				if (in_array('_TH_USER_ELAPSED_TIME', $cols)) $row[] = ( isset($time_list[$id_user.'_'.$id_course]) ?
					substr('0'.((int)($time_list[$id_user.'_'.$id_course]/3600)),-2).'h '
					.substr('0'.((int)(($time_list[$id_user.'_'.$id_course]%3600)/60)),-2).'m '
					.substr('0'.((int)($time_list[$id_user.'_'.$id_course]%60)),-2).'s ' : '&nbsp;' );


				if (in_array('_TH_ESTIMATED_TIME', $cols)) $row[] = $medium_time.'h';

				//checkbox for mail
				if ($this->use_mail) $row[] = //Form::getCheckbox('', 'mail_'.$id_user, 'mail_recipients[]', $id_user);
			 '<div class="align_center">'.Form::getInputCheckbox('mail_'.$id_user, 'mail_recipients[]', $id_user, isset($_POST['select_all']), '').'</div>';
				$buffer->addLine($row);

				$count_rows++;
			}
		}

		$buffer->closeBody();
		$buffer->closeTable();

		$output .= $buffer->get();

		addYahooJs(array('selector' => 'selector-beta-min.js'));
		addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/', '_selectall.js');


		if ($this->use_mail) {
			$mlang =& DoceboLanguage::createInstance('report', 'framework');
			//$output .= Form::getHidden('no_show_repdownload', 'no_show_repdownload', 1);
			$output .= Form::openButtonSpace()
			.Form::getHidden('no_show_repdownload', 'no_show_repdownload', 1)
			.Form::openButtonSpace()
			.Form::getButton('send_mail', 'send_mail', $mlang->def('_SEND_MAIL'))
			.'<button type="button" class="button" id="select_all" name="select_all" onclick="selectAll();">'.$mlang->def('_SELECT_ALL').'</button>'//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
			.'<button type="button" class="button" id="unselect_all" name="unselect_all" onclick="unselectAll();">'.$mlang->def('_UNSELECT_ALL').'</button>'//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
			//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
			//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
			.Form::closeButtonSpace();
			//cout(Form::closeForm());
		}
		if ($count_rows>0)
		return $output;
		else
		return $lang->def('_NULL_REPORT_RESULT'); //null result string
	}


	function _convertStatusCourse($status) {
		if (isset($this->status_c[$status]) ) //just debug, sometimes it receives an inexistent status
		return $this->status_c[$status];
		else return '';
	}

	function _convertStatusUser($status) {
		return $this->status_u[$status];
	}


	//competences section **********************************************************


	function _printTable_competences($type, &$data) {
		$acl_man = new DoceboACLManager();

		if(!$type) $type = 'html';
		if (!is_array($data)) return '';
		if (count($data)<=0) return '';
		// css -----------------------------------------------------------
		$GLOBALS['page']->add(
			'<link href="'.getPathTemplate('lms').'style/report/style_report_user.css" rel="stylesheet" type="text/css" />'."\n"
			, 'page_head');

		$lang =& DoceboLanguage::createInstance('report', 'framework');  //ru or rc ?

		require_once('report_tableprinter.php');
		$buffer = new ReportTablePrinter($type, true);

		/*$head = '<th>'.$lang->def('_RC_USERID').'</th>';
		if ($this->use_mail) $head .= '<th></th>'; //checkboxes for mail
		foreach ($data['cols'] as $key=>$value) {
	  $head.='<th class="align_center">'.$value.'</th>';
	}*/
		$buffer->openTable($lang->def('_RC_CAPTION'), $lang->def('RC_CAPTION'));

		$buffer->openHeader();

		//$_head = array($lang->def('_RC_USERID'));
		//if ($this->use_mail) $_head[] = '<img src="'.getPathImage().'standard/email.gif"/>';//'';

		$_head = array($lang->def('_USER'));

		foreach ($data['cols'] as $key=>$value) {
			$_head[] = $value;
		}

		if ($this->use_mail) $_head[] = '<img src="'.getPathImage().'standard/email.gif"/>';//'';

		$buffer->addHeader($_head);
		$buffer->closeHeader();

		$buffer->openBody();

		if (isset($data['rows']))
		foreach ($data['rows'] as $key1=>$value1) {
			$idst_user = $data['users'][$key1];//['id_user'];

			$_line = array( $acl_man->relativeId($data['users'][ $key1 ]) );

			//foreach ($value1 as $key2=>$value2) {
			foreach ($data['cols'] as $key2=>$value2) {
				$_line[] = (array_key_exists($key2,$value1) ? $value1[ $key2 ].'&nbsp;' : '-');
			}

			if ($this->use_mail) $_line[] = //Form::getCheckbox('', 'mail_'.$idst_user, 'mail_recipients[]', $idst_user);
				'<div class="align_center">'.Form::getInputCheckbox('mail_'.$idst_user, 'mail_recipients[]', $idst_user, isset($_POST['select_all']), '').'</div>';


			$buffer->addLine($_line);
		}

		$buffer->closeBody();
		$buffer->closeTable();

		addYahooJs(array('selector' => 'selector-beta-min.js'));
		addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/', '_selectall.js');

		$output = $buffer->get();
		if ($this->use_mail) {
			$mlang =& DoceboLanguage::createInstance('report', 'framework');
			$output .= Form::getHidden('no_show_repdownload', 'no_show_repdownload', 1);
			$output .= Form::openButtonSpace()
			.Form::getHidden('no_show_repdownload', 'no_show_repdownload', 1)
			.Form::openButtonSpace()
			.Form::getButton('send_mail', 'send_mail', $mlang->def('_SEND_MAIL'))
			.'<button type="button" class="button" id="select_all" name="select_all" onclick="selectAll();">'.$mlang->def('_SELECT_ALL').'</button>'//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
			.'<button type="button" class="button" id="unselect_all" name="unselect_all" onclick="unselectAll();">'.$mlang->def('_UNSELECT_ALL').'</button>'//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
			//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
			//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
			.Form::closeButtonSpace();
			//cout(Form::closeForm());
		}

		return $output;
	}

	function show_report_competences($report_data = NULL, $other = '') {
		$jump_url = ''; //show_report

		checkPerm('view');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		if (isset($_POST['send_mail_confirm']))
		$op = 'send_mail_confirm';
		elseif (isset($_POST['send_mail'])) {
			$op = 'send_mail';
		} else {
			$op = 'show_result';
		}

		switch ($op) {

			case 'send_mail_confirm': {
				$subject = importVar('mail_object', false, '['.$lang->def('_SUBJECT').']' );//'[No subject]');
				$body = importVar('mail_body', false, '');
				$acl_man = new DoceboACLManager();
				$user_info = $acl_man->getUser(getLogUserId(), false);
				if ($user_info)
				{
					$sender = $user_info[ACL_INFO_EMAIL];
				}
				$mail_recipients = unserialize(urldecode(get_req('mail_recipients', DOTY_STRING, '')));

				// prepare intestation for email
				$from = "From: ".$sender.$GLOBALS['mail_br'];
				$header  = "MIME-Version: 1.0".$GLOBALS['mail_br']
				."Content-type: text/html; charset=".getUnicode().$GLOBALS['mail_br'];
				$header .= "Return-Path: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "Reply-To: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Sender: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Mailer: PHP/". phpversion().$GLOBALS['mail_br'];

				// send mail
				$arr_recipients = array();
				foreach($mail_recipients as $recipient) {
					$rec_data = $acl_man->getUser($recipient, false);
					//mail($rec_data[ACL_INFO_EMAIL] , stripslashes($subject), stripslashes(nl2br($body)), $from.$header."\r\n");
					$arr_recipients[] = $rec_data[ACL_INFO_EMAIL];
				}
				$mailer = DoceboMailer::getInstance();
				$mailer->SendMail($sender, $arr_recipients, stripslashes($subject), stripslashes(nl2br($body)));

				$result = getResultUi($lang->def('_MAIL_SEND_OK'));

				//$this->show_report($alluser, $jump_url, $org_chart_subdivision, $day_from_subscription, $day_until_course_end, $date_until_course_end, $report_type, $course_selected, $user_selected);
				cout( $this->_get_competences_query('html',NULL,$result) );
			} break;

			case 'send_mail': {
				require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
				$mail_recipients = get_req('mail_recipients', DOTY_MIXED, array());
				cout(
				''//Form::openForm('course_selection', str_replace('&', '&amp;', $jump_url))
					.Form::openElementSpace()
					.Form::getTextfield($lang->def('_MAIL_OBJECT'), 'mail_object', 'mail_object', 255)
					.Form::getTextarea($lang->def('_MAIL_BODY'), 'mail_body', 'mail_body')
					.Form::getHidden('mail_recipients', 'mail_recipients', urlencode(serialize($mail_recipients)))
					.Form::closeElementSpace()
					.Form::openButtonSpace()
					.Form::getButton('send_mail_confirm', 'send_mail_confirm', $lang->def('_SEND_MAIL'))
					.Form::getButton('undo_mail', 'undo_mail', $lang->def('_UNDO'))
					.Form::closeButtonSpace()
					//.Form::closeForm()
					.'</div>', 'content');
			} break;

			default: {
				cout( $this->_get_competences_query('html', $report_data, $other) );
			}

		}
		//cout( $this->_get_competences_query() );
	}

	function _get_competences_query($type='html', $report_data = NULL, $other='') {
		require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
		$_man = new Competences_Manager();

		if ($report_data==NULL) $ref =& $_SESSION['report_tempdata']; else $ref =& $report_data;

		$rc_filters =& $ref['columns_filter']['filters_list'];
		$rc_exclusive = $ref['columns_filter']['exclusive'];

		$final_arr=array();

		//process filter and build query
		$table1 = $GLOBALS['prefix_lms']."_competence";
		$table2 = $GLOBALS['prefix_lms']."_competence_text";
		$table3 = $GLOBALS['prefix_lms']."_competence_user";
		$table4 = $GLOBALS['prefix_fw']."_user";

		if (!$ref['rows_filter']['all_users'])
			$user_query_select=" AND t4.idst IN (".implode(',',$ref['rows_filter']['users']).")";
		else
			$user_query_select="";
		
		$selfrom = "SELECT t1.id_competence as id_competence, t2.text_name as name, ".
			   "t3.id_user as id_user, t4.userid as username, ".
			   "t3.score_init, t3.score_got ".
			   "FROM $table1 as t1 LEFT JOIN $table2 as t2 ON (t1.id_competence=t2.id_competence ".
			   "AND t2.lang_code='".getLanguage()."'), $table3 as t3, $table4 as t4 ".
			   "WHERE t1.id_competence=t3.id_competence AND t3.id_user=t4.idst AND t4.valid=1 ".$user_query_select;

		$signs = array('0'=>'<', '1'=>'<=', '2'=>'=', '3'=>'>=', '4'=>'>');
		$conds = array();
		//foreach ($_POST['rc_filter'] as $key=>$value) {
		foreach($rc_filters as $key=>$value) {
			$f_id  = $key;

			if (isset($value['flag'])) {
				$f_val = $value['flag'];
				$conds['flag'][$f_id]= ($f_val=='yes' ? "=" : '<>');
			} else {
				foreach($value as $key2=>$value2) {
		/*if (isset($value['sign']) && isset($value['value'])) */
					$f_op  = $value2['sign'];
					$f_val = $value2['value'];
					$conds['score'][$f_id][] = "(t3.score_got+t3.score_init)".$signs[ $f_op ].$f_val;
				}
			}
		}

		if (isset($conds['flag'])) {
			foreach ($conds['flag'] as $key=>$value) {
				$t_compdata = $_man->GetCompetence($key);
				$temp_arr = array();
				if ($value=='=') {
					$query = $selfrom." AND t1.id_competence".$value."$key";
					$res=mysql_query($query);
					while ($row=mysql_fetch_array($res)) {
						$temp_arr[]=$row;
						if (!isset($final_arr['users'][ $row['id_user'] ]))
						$final_arr['users'][ $row['id_user'] ]=$row['username'];
					}
					$final_arr['cols'][ $key ] = $t_compdata['name'];//$temp_arr[0]['name'];
				} else {
					//in this case we need a different query construct
					$query = "SELECT DISTINCT t3.* ".
				   "FROM $table3 as t1 LEFT JOIN $table3 as t2 ".
				   "ON (t1.id_user = t2.id_user AND t2.id_competence = $key), $table4 as t3 ".
				   "WHERE t2.id_user IS NULL AND t1.id_user=t3.idst AND t3.valid=1 ";
					$res=mysql_query($query);
					while ($row=mysql_fetch_array($res)) {
						$temp_row=array();
						$temp_row['id_competence']=$t_compdata['id'];
						$temp_row['name']=$t_compdata['name'];
						$temp_row['id_user']=$row['idst'];
						$temp_row['username']=$row['userid'];
						$temp_row['score_init']='';
						$temp_row['score_got']='';

						$temp_arr[]=$temp_row;
						if (!isset($final_arr['users'][ $row['idst'] ]))
						$final_arr['users'][ $row['idst'] ]=$row['userid'];
					}
					$final_arr['cols'][ $key ] = $t_compdata['name'];//$temp_arr[0]['name'];
				}


				foreach ($temp_arr as $k=>$v) { //
					//if (array_key_exists($v['id_comp'],))
					$final_arr['rows'][ $v['id_user'] ][ $v['id_competence'] ] = ($value=='=' ? 'yes' : 'no');//$lang->def('_COMPETENCE_ACQUIRED');
				}
			}
		}

		if (isset($conds['score'])) {
			foreach ($conds['score'] as $key=>$value) {
				if (count($value)>0) {
					$temp_arr = array();
					$query = $selfrom." AND t1.id_competence=$key AND ".implode($value, ' AND ');
					$res=mysql_query($query);
					while ($row=mysql_fetch_array($res)) {
						$temp_arr[]=$row;
						if (!isset($final_arr['users'][ $row['id_user'] ]))
						$final_arr['users'][ $row['id_user'] ]=$row['username'];
					}

					if (count($temp_arr)>0)
					$final_arr['cols'][ $key ] = $temp_arr[0]['name'];
					else
					$final_arr['cols'][ $key ] = '';

					foreach ($temp_arr as $k=>$v) { //
						//if (array_key_exists($v['id_comp'],))
						$final_arr['rows'][ $v['id_user'] ][ $v['id_competence'] ] = $v['score_init'] + $v['score_got'];
					}
				}
			}
		}
		//**************************************************************************

		//check for exclusiveness of results
		if (!isset($final_arr['cols'])) return '';
		$num_cols=count($final_arr['cols']);
		if ($rc_exclusive) {//if (isset($_POST['rc_filter_exclusive'])) {
			if (isset($final_arr['rows']))
			foreach($final_arr['rows'] as $key=>$value) {
				if (count($value)<$num_cols) {
					unset($final_arr['rows'][$key]); //eliminate all partial rows
				}
			}
		}

		//$lang =& DoceboLanguage::createInstance('report', 'framework');
		return $this->_printTable_competences($type, $final_arr);
	}




	//******************************************************************************

	var $delay_columns = array(
		array('_USERID', false),
		array('_LASTNAME', true),
		array('_NAME', true),
		array('_STATUS', true),
		array('_MAIL', true),
		array('_DATE_INSCR', true),
		array('_DATE_FIRST_ACCESS', true),
		array('_DATE_COURSE_COMPLETED', true),
		array('', false)
	);


	function get_delay_filter() {
		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			//go back at the previous step
			jumpTo($back_url);
		}

		//set $_POST data in $_SESSION['report_tempdata']
		$ref =& $_SESSION['report_tempdata']['columns_filter'];
		$selector = new Selector_Course();
		if (isset($_POST['update_tempdata'])) {
			$selector->parseForState($_POST);
			$temp=array(
				'report_type_completed'	=> ($_POST['report_type']=="course_completed" || $_POST['report_type']=="both" ? true : false),//( isset($_POST['report_type_completed']) ? true : false ),
				'report_type_started'	=> ($_POST['report_type']=="course_started" || $_POST['report_type']=="both" ? true : false),//( isset($_POST['report_type_started']) ? true : false ),
				'day_from_subscription' => $_POST['day_from_subscription'],
				'day_until_course_end'	=> $_POST['day_until_course_end'],
				'date_until_course_end' => $GLOBALS['regset']->regionalToDatabase($_POST['date_until_course_end'], 'date'),
				'org_chart_subdivision' => (isset($_POST['org_chart_subdivision']) ? 1 : 0),
				'all_courses'			=> ($_POST['all_courses']==1 ? true : false),
				'selected_courses' 		=> $selector->getSelection(),
				'showed_columns' 		=> (isset($_POST['cols']) ? $_POST['cols'] : array())
			);
			$_SESSION['report_tempdata']['columns_filter'] = $temp; //$ref = $temp;
		} else {
			//first loading of this page -> prepare $_SESSION data structure
			//if (isset($_SESSION['report_update']) /* && is equal to id_report */) break;

			if (!isset($_SESSION['report_tempdata']['columns_filter'])) {
				$_SESSION['report_tempdata']['columns_filter'] = array(//$ref = array(
					'report_type_completed' => false,
					'report_type_started' => false,
					'day_from_subscription' => '',
					'day_until_course_end' => '',
					'date_until_course_end' => '',
					'org_chart_subdivision' => 0,
					'all_users' => false,
					'all_courses' => true,
					'selected_courses' => array(),
					'showed_columns' => array()
				);
			}
		}
		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}

		$lang = $this->lang;

		cout(
			//Form::openForm('user_report_columns_delay', $jump_url).
			Form::getHidden('update_tempdata', 'update_tempdata', 1)
		);

		$dlang =& DoceboLanguage::CreateInstance('report', 'framework');

		$array_report_type = array(
			$dlang->def( '_COURSE_COMPLETED' ) => "course_completed",
			$dlang->def( '_COURSE_STARTED' ) => "course_started",
			$this->lang->def('_FILTER_ALL_CONDS') => "both" );

		//box for rpeort options
		$box = new ReportBox('delay_options_box');
		$box->title = $dlang->def('_REPORT_USER_TITLE_TIMEBELT');
		$box->description = $dlang->def('_REPORT_USER_TITLE_TIMEBELT_DESC');
		$selected_radio = "both";
		if (!$ref['report_type_completed'] || !$ref['report_type_started']) {
			if ($ref['report_type_completed']) $selected_radio = 'course_completed';
			if ($ref['report_type_started']) $selected_radio = 'course_started';
		}
		$box->body =
		Form::getRadioSet('', 'report_type', 'report_type', $array_report_type, $selected_radio)
		// Form::getCheckBox($dlang->def( '_COURSE_COMPLETED' ), 'report_type_completed', 'report_type_completed', 1, (isset($ref['report_type_completed']) ? $ref['report_type_completed'] : false) )
		//.Form::getCheckBox($dlang->def( '_COURSE_STARTED' ), 'report_type_started', 'report_type_started', 1, (isset($ref['report_type_started']) ? $ref['report_type_started'] : false) )
		.Form::getTextfield($dlang->def('_DAY_FROM_SUBSCRIPTION'), 'day_from_subscription', 'day_from_subscription', 20, $ref['day_from_subscription'])
		.Form::getTextfield($dlang->def('_DAY_UNTIL_COURSE_END'), 'day_until_course_end', 'day_until_course_end', 20, $ref['day_until_course_end'])
		.Form::getDatefield($dlang->def('_DATE_UNTIL_COURSE_END'), 'date_until_course_end', 'date_until_course_end', $GLOBALS['regset']->databaseToRegional($ref['date_until_course_end'], 'date') )
		//.Form::getCloseFieldset()
		/*.Form::getCheckbox(	$lang->def('ORG_CHART_SUBDIVISION'), 'org_chart_subdivision_'.$id_report,	'org_chart_subdivision', $ref['org_chart_subdivision'] )*/
		.Form::getBreakRow();

		cout($box->get());

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

		//box for direct course selection
		$selection =& $ref['selected_courses'];
		$selector->parseForState($_POST);
		$selector->resetSelection($selection);
		$temp = count($selection);

		$box = new ReportBox('course_selector');
		$box->title = $lang->def('_REPORT_COURSE_SELECTION');
		$box->description = false;
		$box->body .= '<div class="fc_filter_line filter_corr">';
		$box->body .= '<input id="all_courses" name="all_courses" type="radio" value="1" '.($ref['all_courses'] ? 'checked="checked"' : '').' />';
		$box->body .= '<label for="all_courses">'.$lang->def('_ALL_COURSES').'</label>';
		$box->body .= '<input id="sel_courses" name="all_courses" type="radio" value="0" '.($ref['all_courses'] ? '' : 'checked="checked"').' />';
		$box->body .= '<label for="sel_courses">'.$lang->def('_SEL_COURSES').'</label>';
		$box->body .= '</div>';

		$box->body .= '<div id="selector_container"'.($ref['all_courses'] ? ' style="display:none"' : '').'>';
		//$box->body .= Form::openElementSpace();
		$box->body .= $selector->loadCourseSelector(true);
		//$box->body .= Form::closeElementSpace();
		$box->body .= '</div>';
		$box->footer = $lang->def('_CURRENT_SELECTION').':&nbsp;<span id="csel_foot">'.($ref['all_courses'] ? $lang->def('_ALLCOURSES_FOOTER') : ($temp!='' ? $temp : '0')).'</span>';
		cout($box->get());

		cout(
			'<script type="text/javascript">courses_count='.($temp=='' ? '0' : $temp).';'.
			'courses_all="'.$lang->def('_ALLCOURSES_FOOTER').'";'."\n".
			'YAHOO.util.Event.addListener(window, "load", courses_selector_init);</script>' );


		$box = new ReportBox('columns_selector');
		$box->title = $lang->def('_SELECT_COLUMS');
		$box->description = $lang->def('_SELECT_THE_DATA_COL_NEEDED');
		$box->body = Form::getOpenFieldset($lang->def('_SHOWED_COLUMNS'),$lang->def(''));
		foreach ($this->delay_columns as $key=>$val) {
			if ($val[1]) {
				$box->body .= Form::getCheckBox(
					$dlang->def($val[0]),
					'col_'.$key,
					'cols[]',
					$val[0],
					in_array($val[0], $ref['showed_columns']) ? true : false
				);
			}
		}
		$box->body .= Form::getCloseFieldset();

		cout($box->get());

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		//buttons
		/*cout(
			 Form::openButtonSpace()
			.Form::getBreakRow()
		.Form::getButton('ok_filter', 'import_filter', $lang->def('_SAVE_BACK', 'report'))
		.Form::getButton('show_filter', 'show_filter', $lang->def('_SAVE_SHOW', 'report'))
		.Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO', 'report'))
		.Form::closeButtonSpace()
			.Form::closeForm());*/
	}


	//show function
	function show_report_delay($report_data = NULL, $other = '') {

		//$alluser, , $org_chart_subdivision, $day_from_subscription, $day_until_course_end, $date_until_course_end, $report_type, $course_selected, $user_selected, $mail)
		$jump_url = ''; //show_report

		checkPerm('view');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		if (isset($_POST['send_mail_confirm']))
		$op = 'send_mail_confirm';
		elseif (isset($_POST['send_mail'])) {
			$op = 'send_mail';
		} else {
			$op = 'show_result';
		}

		switch ($op) {

			case 'send_mail_confirm': {
				$subject = importVar('mail_object', false, '['.$lang->def('_SUBJECT').']' );//'[No subject]');
				$body = importVar('mail_body', false, '');
				$acl_man = new DoceboACLManager();
				$user_info = $acl_man->getUser(getLogUserId(), false);
				if ($user_info)
				{
					$sender = $user_info[ACL_INFO_EMAIL];
				}
				$mail_recipients = unserialize(urldecode(get_req('mail_recipients', DOTY_STRING, '')));

				// prepare intestation for email
				$from = "From: ".$sender.$GLOBALS['mail_br'];
				$header  = "MIME-Version: 1.0".$GLOBALS['mail_br']
				."Content-type: text/html; charset=".getUnicode().$GLOBALS['mail_br'];
				$header .= "Return-Path: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "Reply-To: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Sender: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Mailer: PHP/". phpversion().$GLOBALS['mail_br'];

				// send mail
				$arr_recipients = array();
				foreach($mail_recipients as $recipient) {
					$rec_data = $acl_man->getUser($recipient, false);
					//mail($rec_data[ACL_INFO_EMAIL] , stripslashes($subject), stripslashes(nl2br($body)), $from.$header."\r\n");
					$arr_recipients[] = $rec_data[ACL_INFO_EMAIL];
				}
				$mailer = DoceboMailer::getInstance();
				$mailer->SendMail($sender, $arr_recipients, stripslashes($subject), stripslashes(nl2br($body)));

				$result = getResultUi($lang->def('_MAIL_SEND_OK'));

				//$this->show_report($alluser, $jump_url, $org_chart_subdivision, $day_from_subscription, $day_until_course_end, $date_until_course_end, $report_type, $course_selected, $user_selected);
				cout( $this->_get_delay_query('html',NULL,$result) );
			} break;

			case 'send_mail': {
				require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
				$mail_recipients = get_req('mail_recipients', DOTY_MIXED, array());
				cout(
				''//Form::openForm('course_selection', str_replace('&', '&amp;', $jump_url))
					.Form::openElementSpace()
					.Form::getTextfield($lang->def('_MAIL_OBJECT'), 'mail_object', 'mail_object', 255)
					.Form::getTextarea($lang->def('_MAIL_BODY'), 'mail_body', 'mail_body')
					.Form::getHidden('mail_recipients', 'mail_recipients', urlencode(serialize($mail_recipients)))
					.Form::closeElementSpace()
					.Form::openButtonSpace()
					.Form::getButton('send_mail_confirm', 'send_mail_confirm', $lang->def('_SEND_MAIL'))
					.Form::getButton('undo_mail', 'undo_mail', $lang->def('_UNDO'))
					.Form::closeButtonSpace()
					//.Form::closeForm()
					.'</div>', 'content');
			} break;

			default: {
				cout( $this->_get_delay_query('html', $report_data, $other) );
			}

		}
	}


	//query function
	function _get_delay_query($type='html', $report_data = NULL, $other='') {
				
		$output = '';
		if ($report_data==NULL) $report_data =& $_SESSION['report_tempdata'];

		$rdata =& $report_data['rows_filter'];
		$cdata =& $report_data['columns_filter'];

		$acl_man 		= new DoceboACLManager();
		$acl_man->include_suspended = TRUE;
		$course_man 	= new Man_Course();

		$alluser = $rdata['all_users'];
		$jump_url = '';
		$org_chart_subdivision = (isset($cdata['org_chart_subdivision']) ? $cdata['org_chart_subdivision'] : false);
		$day_from_subscription = ($cdata['day_from_subscription'] != "" ? $cdata['day_from_subscription'] : false);
		$day_until_course_end = ($cdata['day_until_course_end'] != "" ? $cdata['day_until_course_end'] : false);
		$date_until_course_end = ($cdata['date_until_course_end'] != "" ? $cdata['date_until_course_end'] : false);
		$report_type_completed = $cdata['report_type_completed'];
		$report_type_started = $cdata['report_type_started'];
		$course_selected = $cdata['selected_courses'];
		$all_courses = $cdata['all_courses'];
		if (!$alluser)
			$user_selected =& $acl_man->getAllUsersFromIdst($rdata['users']);
		else
			$user_selected =& $acl_man->getAllUsersIdst();

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		$lang_u =& DoceboLanguage::CreateInstance('stats', 'lms');

		$status_u = array(
			_CUS_CONFIRMED 		=> $lang_u->def('_USER_STATUS_CONFIRMED', 'stats', 'lms'),
			_CUS_SUBSCRIBED 	=> $lang_u->def('_USER_STATUS_SUBS', 'stats', 'lms'), //..._INSCR ?
			_CUS_BEGIN 			=> $lang_u->def('_USER_STATUS_BEGIN', 'stats', 'lms'),
			_CUS_END 			=> $lang_u->def('_USER_STATUS_END', 'stats', 'lms'),
			_CUS_SUSPEND 		=> $lang_u->def('_SUSPENDED', 'stats', 'lms') );

		//$json = new Services_JSON();
		//$selected_courses_post = urlencode($json->encode($course_selected));
		//$selected_user_post = urlencode($json->encode($user_selected));

		if(empty($user_selected))
		{
			return $lang->def('_NULL_SELECTION');
		}

		if(empty($course_selected) && !$all_courses)
		{
			return $lang->def('_NULL_COURSE_SELECTION');
		}



		if (1==1)//($org_chart_subdivision === 0)
		{
			$date_now = $GLOBALS['regset']->databaseToRegional(date("Y-m-d H:i:s"));
			//$GLOBALS['page']->add( '<b>'.$lang->def('_ANALYSIS_DATE').' :</b> '.$date_now.'<br />', 'content');

			$query_course_user = "
			SELECT cu.idUser, cu.idCourse, cu.edition_id, cu.date_inscr, cu.date_first_access, cu.date_complete, cu.status, cu.level, cu.status
			FROM ".$GLOBALS['prefix_lms']."_courseuser AS cu " .
			" JOIN ".$GLOBALS['prefix_fw']."_user as u ON cu.idUser = u.idst
			WHERE cu.idCourse>0 ".($alluser ? "" : " AND cu.idUser IN ( ".implode(',', $user_selected)." ) ").   " AND u.valid=1 ".
			($all_courses ? '' : " AND cu.idCourse IN (".implode(',', $course_selected).")" )
			." ORDER BY u.lastname, u.firstname, u.userid ";

			$re_course_user = mysql_query($query_course_user);

			$element_to_print = array();
			$courses_codes = array();

			while(list($id_u, $id_c, $id_e, $date_inscr, $fisrt_access, $date_complete, $status, $level) = mysql_fetch_row($re_course_user)) {

				if ($level == '3') { //$report_type === 'course_started' && $level == '3') {

					$user_check = false;
					$now_timestamp = mktime('0', '0', '0', date('m'), date('d'), date('Y'));

					//check the condition on status (course started and/or completed)
					$status_condition = $status != _CUS_END; //&& $status != _CUS_SUSPEND;
					if ($report_type_completed && !$report_type_started) $status_condition = $status_condition && ($status == _CUS_BEGIN);
					if ($report_type_started && !$report_type_completed) $status_condition = $status_condition && ($status != _CUS_BEGIN);

					if ( $day_from_subscription )	{

						if ($status_condition) {
							$user_timestamp = mktime('0', '0', '0', $date_inscr{5}.$date_inscr{6}, ($date_inscr{8}.$date_inscr{9}) + $day_from_subscription, $date_inscr{0}.$date_inscr{1}.$date_inscr{2}.$date_inscr{3});
							if ($user_timestamp < $now_timestamp)	$user_check = true;
						}
					}


					if ( $day_until_course_end ) {

						if ($status_condition)	{

							if ($id_e>0) {

								$query = 	"SELECT date_end"
								." FROM ".$GLOBALS['prefix_lms']."_course_edition"
								." WHERE idCourseEdition = '".$id_e."'";
								list ($date_end) = mysql_fetch_row(mysql_query($query));
								$user_timestamp = mktime('0', '0', '0', $date_end{5}.$date_end{6}, ($date_end{8}.$date_end{9}) - $day_until_course_end, $date_end{0}.$date_end{1}.$date_end{2}.$date_end{3});
								if ($user_timestamp < $now_timestamp)	$user_check = true;

							}	else {

								$query = 	"SELECT date_end"
								." FROM ".$GLOBALS['prefix_lms']."_course"
								." WHERE idCourse = '".$id_c."'";
								list ($date_end) = mysql_fetch_row(mysql_query($query));
								$user_timestamp = mktime('0', '0', '0', $date_end{5}.$date_end{6}, ($date_end{8}.$date_end{9}) - $day_until_course_end, $date_end{0}.$date_end{1}.$date_end{2}.$date_end{3});
								if ($user_timestamp < $now_timestamp) $user_check = true;

							}

						}

					}


					if ($date_until_course_end)	{

						if ($status_condition)	{

							if ($id_e>0) {

								$query = 	"SELECT COUNT(*)"
								." FROM ".$GLOBALS['prefix_lms']."_course_edition"
								." WHERE idCourseEdition = '".$id_e."'"
								." AND date_end < '".$GLOBALS['regset']->regionalToDatabase($date_until_course_end, 'date')."'";
								list ($control) = mysql_fetch_row(mysql_query($query));
								if ($control)	$user_check = true;

							} else {
								$query = 	"SELECT COUNT(*)"
								." FROM ".$GLOBALS['prefix_lms']."_course"
								." WHERE idCourse = '".$id_c."'"
								." AND date_end < '".$GLOBALS['regset']->regionalToDatabase($date_until_course_end, 'date')."'";
								list ($control) = mysql_fetch_row(mysql_query($query));
								if ($control) $user_check = true;

							}

						}

					}


					if (!$date_until_course_end && !$day_from_subscription && !$date_until_course_end)
					if ($status_condition)
					$user_check = true;

					if ($user_check) {
						$course_info = $course_man->getCourseInfo($id_c);
						$user_detail = $acl_man->getUser($id_u, false);
						//$element_to_print[$course_info['name']]['idcourse'] = $id_c;
						//$element_to_print[$course_info['name']]['code'] = $course_info['code'];
						//$element_to_print[$course_info['name']]['data'][] = array(
						$element_to_print[$id_c]['name'] = $course_info['name'];
						$element_to_print[$id_c]['code'] = $course_info['code'];
						$element_to_print[$id_c]['data'][] = array(
																		'idUser' => $id_u,
																		'idCourse' => $id_c,
																		'idCourseEdition' => $id_e,
																		'status' => $status,
																		'userid' => $acl_man->relativeId($user_detail[ACL_INFO_USERID]),
																		'firstname' => $user_detail[ACL_INFO_FIRSTNAME],
																		'lastname' => $user_detail[ACL_INFO_LASTNAME],
																		'mail' => $user_detail[ACL_INFO_EMAIL],
																		'date_inscr' => $date_inscr,
																		'first_access' => $fisrt_access,
																		'date_completed' => $date_complete,
																		'level' => $level);

					}

				}

			}

			$output  = '';
			$output .= $this->_printTable_delay($type, $element_to_print, $cdata['showed_columns']);

			addYahooJs(array('selector' => 'selector-beta-min.js'));
			addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/', '_selectall.js');

			if ($this->use_mail) {
				$output .= Form::openButtonSpace()
				.Form::getHidden('no_show_repdownload', 'no_show_repdownload', 1)
				.Form::openButtonSpace()
				.Form::getButton('send_mail', 'send_mail', $lang->def('_SEND_MAIL'))
				.'<button type="button" class="button" id="select_all" name="select_all" onclick="selectAll();">'.$lang->def('_SELECT_ALL').'</button>'//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
				.'<button type="button" class="button" id="unselect_all" name="unselect_all" onclick="unselectAll()">'.$lang->def('_UNSELECT_ALL').'</button>'//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
				//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
				//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
				.Form::closeButtonSpace();
				//cout(Form::closeForm());
			}
		}

		return $output;
	}




	function _printTable_delay($type, &$element_to_print, $showed_cols=array()) {
		$output = '';
		if(!$type) $type = 'html';
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		if (empty($element_to_print))
		{
			$output .= $lang->def('_NO_USER_FOUND');
		}
		else
		{
			require_once('report_tableprinter.php');
			$buffer = new ReportTablePrinter($type);

			//ksort($element_to_print);

			foreach ($element_to_print as  $id_course=> $info)
			{
				$course_name = $info['name'];

				$header = array(
					$lang->def('_USERID'),
					$lang->def('_LASTNAME'),
					$lang->def('_FIRSTNAME'),
					$lang->def('_STATUS'),
					$lang->def('_EMAIL'),
					$lang->def('_DATE_INSCR'),
					$lang->def('_DATE_FIRST_ACCESS'),
					$lang->def('_DATE_COURSE_COMPLETED')
				);
				if ($this->use_mail) $header[] = '<img src="'.getPathImage().'standard/email.gif"/>';//'';

				$title = $lang->def('_COURSE').': "'.$course_name.'" ('.$info['code'].')';

				$buffer->openTable($title, $title);

				$buffer->openHeader();
				$buffer->addHeader($header);
				$buffer->closeHeader();

				$buffer->openBody();


				$i = 0;
				foreach($info['data'] as $user_info)
				{
						/*$output .=
							'<tr class="row'.( (++$i)%2 ? '' : '_col' ).'">'."\n\t"
								.'<td class="align_center">'.$user_info['userid'].'</td>'."\n\t"
								.'<td class="align_center">'.$user_info['lastname'].'</td>'."\n\t"
								.'<td class="align_center">'.$user_info['firstname'].'</td>'."\n\t"
								.'<td class="align_center">'.$this->status_u[$user_info['status']].'</td>'."\n\t"
								.'<td class="align_center">'.$user_info['mail'].'</td>'."\n\t"
								.'<td class="align_center">'.$GLOBALS['regset']->databaseToRegional($user_info['date_inscr'], 'datetime').'</td>'."\n\t"
								.'<td class="align_center">'.$GLOBALS['regset']->databaseToRegional($user_info['first_access'], 'datetime').'</td>'."\n\t"
								.'<td class="align_center">'.$GLOBALS['regset']->databaseToRegional($user_info['date_completed'], 'datetime').'</td>'."\n\t"
								.'<td class="align_center">'.Form::getCheckbox('', 'mail_'.$user_info['idUser'], 'mail_recipients[]', $user_info['idUser'], isset($_POST['select_all'])).'</td>'."\n\t"
							.'</tr>';*/

					$line = array(
						$user_info['userid'],
						$user_info['lastname'],
						$user_info['firstname'],
						$this->status_u[$user_info['status']],
						$user_info['mail'],
						$GLOBALS['regset']->databaseToRegional($user_info['date_inscr'], 'datetime'),
						$GLOBALS['regset']->databaseToRegional($user_info['first_access'], 'datetime'),
						$GLOBALS['regset']->databaseToRegional($user_info['date_completed'], 'datetime')
					);
					if ($this->use_mail)
					$line[] = '<div class="align_center">'.Form::getInputCheckbox('mail_'.$user_info['idUser'], 'mail_recipients[]', $user_info['idUser'], isset($_POST['select_all']), '').'</div>';

					$buffer->addLine($line);

				}
				$buffer->closeBody();
				$buffer->closeTable();
				$buffer->addBreak();

			}

			$output .= $buffer->get();
		}
		return $output;
	}

	function getHTML($cat = false, $report_data = NULL) {
		$this->use_mail = false;
		return $this->_get_data('html', $cat, $report_data);
	}
	
	function getCSV($cat = false, $report_data = NULL) {
		$this->use_mail = false;
		return $this->_get_data('csv', $cat, $report_data);
	}

	function getXLS($cat = false, $report_data = NULL) {
		$this->use_mail = false; 
		return $this->_get_data('xls', $cat, $report_data);
	}

	//learning objects report functions

	function get_LO_filter() {
		//addCss('style_filterbox');

		$back_url = $this->back_url;
		$jump_url = $this->jump_url;
		$next_url = $this->next_url;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$ref =& $_SESSION['report_tempdata']['columns_filter'];

		addYahooJs(array(
			'yahoo'           => 'yahoo-min.js',
			'yahoo-dom-event' => 'yahoo-dom-event.js',
			'element'         => 'element-beta-min.js',
			'event'           => 'event-min.js'
			));
		addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/','courses_filter.js');

		//back to columns category selection
		if (isset($_POST['undo_filter'])) {
			//go back at the previous step
			jumpTo($back_url);
		}

		//set $_POST data in $_SESSION['report_tempdata']
		$selector = new Selector_Course();
		if (isset($_POST['update_tempdata'])) {
			$selector->parseForState($_POST);
			$temp=array(
				//'org_chart_subdivision' 	=> (isset($_POST['org_chart_subdivision']) ? 1 : 0),
				'all_courses'        => ($_POST['all_courses']==1 ? true : false),
				'selected_courses' 			=> $selector->getSelection(),
				'lo_types' 				=> (isset($_POST['lo_types']) ? $_POST['lo_types'] : array()),
				'lo_milestones' 			=> ( isset($_POST['lo_milestones']) ? $_POST['lo_milestones'] : array() ),
				'showed_columns' 			=> (isset($_POST['cols']) ? $_POST['cols'] : array()),
				'custom_fields'    => array()
			);

			foreach ($ref['custom_fields'] as $val) {
				$temp['custom_fields'][]=array(
					'id'=>$val['id'],
					'label'=>$val['label'],
					'selected'=>(isset($_POST['custom'][ $val['id'] ]) ? true : false)
				);
			}

			$_SESSION['report_tempdata']['columns_filter'] = $temp;
		} else {
			//first loading of this page -> prepare $_SESSION data structure
			//if (isset($_SESSION['report_update']) /* && is equal to id_report */) break;
			//get users' custom fields
			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
			$fman = new FieldList();
			$fields = $fman->getFlatAllFields();
			$custom = array();
			foreach ($fields as $key=>$val) {
				$custom[] = array('id'=>$key, 'label'=>$val, 'selected'=>false);
			}

			if (!isset($_SESSION['report_tempdata']['columns_filter'])) {
				$_SESSION['report_tempdata']['columns_filter'] = array(
					//'org_chart_subdivision' 	=> (isset($_POST['org_chart_subdivision']) ? 1 : 0),
					'all_courses'        => false,
					'selected_courses' 			=> $selector->getSelection(),
					'lo_types' 				=> array(),
					'lo_milestones' 			=> array(),
					'showed_columns' 			=> array(),
					'custom_fields'     => $custom
				);
			}
		}

		//filter setting done, go to next step
		if (isset($_POST['import_filter']) || isset($_POST['show_filter']) || isset($_POST['pre_filter'])) {
			$temp_url = $next_url;
			if (isset($_POST['pre_filter'])) $temp_url.='&show=1&nosave=1';
			if (isset($_POST['show_filter'])) $temp_url.='&show=1';
			jumpTo($temp_url);
		}

		cout( Form::getHidden('update_tempdata', 'update_tempdata', 1) );

		$lang = $this->lang;

		//box for direct course selection
		$selection =& $ref['selected_courses'];
		$selector->parseForState($_POST);
		$selector->resetSelection($selection);
		$temp = count($selection);

		$box = new ReportBox('course_selector');
		$box->title = $lang->def('_REPORT_COURSE_SELECTION');
		$box->description = false;
		$box->body .= '<div class="fc_filter_line filter_corr">';
		$box->body .= '<input id="all_courses" name="all_courses" type="radio" value="1" '.($ref['all_courses'] ? 'checked="checked"' : '').' />';
		$box->body .= '<label for="all_courses">'.$lang->def('_ALL_COURSES').'</label>';
		$box->body .= '<input id="sel_courses" name="all_courses" type="radio" value="0" '.($ref['all_courses'] ? '' : 'checked="checked"').' />';
		$box->body .= '<label for="sel_courses">'.$lang->def('_SEL_COURSES').'</label>';
		$box->body .= '</div>';

		$box->body .= '<div id="selector_container"'.($ref['all_courses'] ? ' style="display:none"' : '').'>';
		//$box->body .= Form::openElementSpace();
		$box->body .= $selector->loadCourseSelector(true);
		//$box->body .= Form::closeElementSpace();
		$box->body .= '<br /></div>';
		$box->footer = $lang->def('_CURRENT_SELECTION').':&nbsp;<span id="csel_foot">'.($ref['all_courses'] ? $lang->def('_ALLCOURSES_FOOTER') : ($temp!='' ? $temp : '0')).'</span>';
		//.'</div>';
		cout($box->get());


		cout(
			'<script type="text/javascript">courses_count='.($temp=='' ? '0' : $temp).';'.
			'courses_all="'.$lang->def('_ALLCOURSES_FOOTER').'";'."\n".
			'YAHOO.util.Event.addListener(window, "load", courses_selector_init);</script>', 'page_head');


		$box = new ReportBox('lo_selection');
		$box->title = $lang->def('_SELECT_LO_OPTIONS');

		//LO columns selection
		$box->body .= Form::getOpenFieldset($lang->def('_RU_LO_TYPES'), 'lotypes_fieldset');
		$res=mysql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_lo_types");
		while ($row=mysql_fetch_assoc($res)) {
			$box->body .= Form::getCheckBox( $row['objectType'], 'lo_type_'.$row['objectType'], 'lo_types['.$row['objectType'].']', $row['objectType'], (in_array($row['objectType'], $ref['lo_types']) ? true : false) );
		}
		$box->body .= Form::getCloseFieldset();

		$box->body .= Form::getOpenFieldset($lang->def('_RU_LO_MILESTONES'), 'lomilestones_fieldset');
		$box->body .= Form::getCheckBox($lang->def('_NONE'), 'lo_milestone_0', 'lo_milestones[]', _MILESTONE_NONE, (in_array(_MILESTONE_NONE, $ref['lo_milestones']) ? true : false) );
		$box->body .= Form::getCheckBox($lang->def('_START'), 'lo_milestone_1', 'lo_milestones[]', _MILESTONE_START, (in_array(_MILESTONE_START, $ref['lo_milestones']) ? true : false) );;
		$box->body .= Form::getCheckBox($lang->def('_END'), 'lo_milestone_2', 'lo_milestones[]', _MILESTONE_END, (in_array(_MILESTONE_END, $ref['lo_milestones']) ? true : false) );;
		$box->body .= Form::getCloseFieldset();

		cout( $box->get() );


		function is_showed($which) {
			if (isset($_SESSION['report_tempdata']['columns_filter'])) {
				return in_array($which, $_SESSION['report_tempdata']['columns_filter']['showed_columns']);
			} else return false;
		};

		//box for columns selection
		$box = new ReportBox('columns_selection');
		$box->title = $lang->def('_SELECT_COLUMS');
		$box->description = $lang->def('_SELECT_THE_DATA_COL_NEEDED');

		//custom fields
		if (count($ref['custom_fields'])>0) {
			$box->body .= Form::getOpenFieldset($lang->def('_USER_CUSTOM_FIELDS'), 'fieldset_course_fields');
			foreach ($ref['custom_fields'] as $key=>$val) {
				$box->body .= Form::getCheckBox($val['label'], 'col_custom_'.$val['id'], 'custom['.$val['id'].']', $val['id'], $val['selected']);
			}
			$box->body .= Form::getCloseFieldset();
		}

		//other fields
		$box->body .= Form::getOpenFieldset($lang->def('_COURSE_FIELDS'), 'fieldset_course_fields');

		foreach($this->LO_columns as $val) { //for ($i=0; $i<count($this->LO_columns); $i++)
			if ($val['select'])
			$box->body .= Form::getCheckBox($val['label'], 'col_sel_'.$val['key'], 'cols[]', $val['key'], is_showed($val['key']));
		}
		$box->body .= Form::getCloseFieldset();

		cout($box->get());

		//cout('</div>'); //close std_block div
	}

	function show_report_LO($report_data = NULL, $other = '') {
		$jump_url = ''; //show_report

		checkPerm('view');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		if (isset($_POST['send_mail_confirm']))
		$op = 'send_mail_confirm';
		elseif (isset($_POST['send_mail'])) {
			$op = 'send_mail';
		} else {
			$op = 'show_result';
		}

		switch ($op) {

			case 'send_mail_confirm': {
				$subject = importVar('mail_object', false, '['.$lang->def('_SUBJECT').']' );//'[No subject]');
				$body = importVar('mail_body', false, '');
				$acl_man = new DoceboACLManager();
				$user_info = $acl_man->getUser(getLogUserId(), false);
				if ($user_info)
				{
					$sender = $user_info[ACL_INFO_EMAIL];
				}
				$mail_recipients = unserialize(urldecode(get_req('mail_recipients', DOTY_STRING, '')));

				// prepare intestation for email
				$from = "From: ".$sender.$GLOBALS['mail_br'];
				$header  = "MIME-Version: 1.0".$GLOBALS['mail_br']
				."Content-type: text/html; charset=".getUnicode().$GLOBALS['mail_br'];
				$header .= "Return-Path: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "Reply-To: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Sender: ".$GLOBALS['framework']['sender_event'].$GLOBALS['mail_br'];
				$header .= "X-Mailer: PHP/". phpversion().$GLOBALS['mail_br'];

				// send mail
				$arr_recipients = array();
				foreach($mail_recipients as $recipient) {
					$rec_data = $acl_man->getUser($recipient, false);
					//mail($rec_data[ACL_INFO_EMAIL] , stripslashes($subject), stripslashes(nl2br($body)), $from.$header."\r\n");
					$arr_recipients[] = $rec_data[ACL_INFO_EMAIL];
				}
				$mailer = DoceboMailer::getInstance();
				$mailer->SendMail($sender, $arr_recipients, stripslashes($subject), stripslashes(nl2br($body)));

				$result = getResultUi($lang->def('_MAIL_SEND_OK'));

				cout( $this->_get_LO_query('html',NULL,$result) );
			} break;

			case 'send_mail': {
				require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
				$mail_recipients = get_req('mail_recipients', DOTY_MIXED, array());
				cout(
				''//Form::openForm('course_selection', str_replace('&', '&amp;', $jump_url))
					.Form::openElementSpace()
					.Form::getTextfield($lang->def('_MAIL_OBJECT'), 'mail_object', 'mail_object', 255)
					.Form::getTextarea($lang->def('_MAIL_BODY'), 'mail_body', 'mail_body')
					.Form::getHidden('mail_recipients', 'mail_recipients', urlencode(serialize($mail_recipients)))
					.Form::closeElementSpace()
					.Form::openButtonSpace()
					.Form::getButton('send_mail_confirm', 'send_mail_confirm', $lang->def('_SEND_MAIL'))
					.Form::getButton('undo_mail', 'undo_mail', $lang->def('_UNDO'))
					.Form::closeButtonSpace()
					//.Form::closeForm()
					.'</div>', 'content');
			} break;

			default: {
				cout( $this->_get_LO_query('html', $report_data, $other) );
			}

		}
	}


	function _convertDate($date) {
		$output = '';
		if ($date != '0000-00-00 00:00:00')
			$output = $GLOBALS['regset']->databaseToRegional($date);
		return $output;
	}

	function _get_LO_query($type='html', $report_data = NULL, $other='') {
		require_once("report_tableprinter.php");

		function is_showed($which, $data) {
			if (isset($data['columns_filter'])) {
				return in_array($which, $data['columns_filter']['showed_columns']);
			} else return false;
		};

		$output = '';

		if ($report_data==NULL) $ref =& $_SESSION['report_tempdata']; else $ref =& $report_data;
		$_rows =& $ref['rows_filter'];
		$_cols =& $ref['columns_filter'];
		$acl_man = new DoceboACLManager();
		$acl_man->include_suspended = TRUE;

		$all_users   = &$_rows['all_users']; //select root & descendants from orgchart instead
		$all_courses = &$_cols['all_courses'];
		$courses     = &$_cols['selected_courses'];
		$types       = &$_cols['lo_types'];
		$milestones  = &$_cols['lo_milestones'];
		$showed      = &$_cols['showed_columns'];
		$customcols  = &$_cols['custom_fields'];
		if ($all_users)
			$users =& $acl_man->getAllUsersIdst();
		else
			$users =& $acl_man->getAllUsersFromIdst($_rows['users']);


		$temptypes=array();
		foreach ($types as $val) { $temptypes[]="'".$val."'"; }

		$tempmilestones=array();
		foreach ($milestones as $val) {
			switch ($val) {
				case _MILESTONE_NONE: { $tempmilestones[]="''"; $tempmilestones[]="'-'"; } break;
				case _MILESTONE_START: { $tempmilestones[]="'start'"; } break;
				case _MILESTONE_END: { $tempmilestones[]="'end'"; } break;
			}
		}

		$colspans=array('user'=>0, 'course'=>0, 'lo'=>0);
		foreach ($this->LO_columns as $val) {
			if ($val['select']) {
				if (in_array($val['key'], $showed)) $colspans[$val['group']]++;
			} else
			if ($val['key']=='_CUSTOM_FIELDS_')
			;//do nothing ...
			else
			$colspans[$val['group']]++;
		}

		//custom user fields
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
		$fman = new FieldList();
		$field_values = array();
		$temp_head2 = array();
		foreach ($customcols as $val) {
			if ($val['selected']) {
				$colspans['user']++;
				$temp_head2[] = $val['label'];
				$field_values[$val['id']] = $fman->fieldValue((int)$val['id'], $users);
			}
		}

		$lang=$this->lang;

		$head1=array();
		$head1[] = array('colspan'=>$colspans['user'],   'value'=>$lang->def('_USER')); //_TH_USER
		$head1[] = array('colspan'=>$colspans['course'], 'value'=>$lang->def('_COURSE')); //_TH_COURSE
		$head1[] = array('colspan'=>$colspans['lo'],     'value'=>$lang->def('_TH_LO'));
		if ($this->use_mail) $head1[]='<img src="'.getPathImage().'standard/email.gif"/>';//'';

		$head2=array();
		foreach ($this->LO_columns as $val) {
			if ($val['select']) {
				if (in_array($val['key'], $showed)) $head2[]=$val['label']; //label
				//if (is_showed($val['key'])) $head2[]=$val['label']; //label
			} else
			if ($val['key']=='_CUSTOM_FIELDS_') {
				foreach ($temp_head2 as $tval) {
					$head2[] = $tval;
				}
			} else
			$head2[]=$val['label']; //label
		}


		if ($this->use_mail) $head2[] = '';//'<img src="'.getPathImage().'standard/email.gif"/>';//''; //header for checkbox

		$buffer = new ReportTablePrinter($type);
		$buffer->openTable('','');

		$buffer->openHeader();
		$buffer->addHeader($head1);
		$buffer->addHeader($head2);
		$buffer->closeHeader();

		//retrieve LOs from courses
		
		$score_arr=array(
			'test'  => array(),
			'scorm' => array()
		);

		//retrieve test and scorm score
		$query = "SELECT t1.idOrg, t2.idUser, t1.idCourse, t2.score, t2.bonus_score, t2.score_status ".
		" FROM ".$GLOBALS['prefix_lms']."_organization AS t1 ".
		" JOIN `".$GLOBALS['prefix_lms']."_testtrack` AS t2 ON ( t1.objectType = 'test' ".
		" AND t1.idOrg = t2.idReference ) ".   " , ".$GLOBALS['prefix_fw']."_user as t3 ".
		//" WHERE ".(!$all_courses ? " t1.idCourse IN (".implode(',', $courses).") " : " 1=1 " ).
		//( count($tempmilestones)>0 ? "AND t1.milestone IN (".implode(',', $tempmilestones).")" : "" );
		"WHERE t3.idst=t2.idUser AND t3.valid=1 ".
		(!$all_courses ? " AND t1.idCourse IN (".implode(',', $courses).") " : "" ).
		(count($tempmilestones)>0 ? " AND t1.milestone IN (".implode(',', $tempmilestones).") " : "" );

		$res = mysql_query($query); //cout('<div>'.$query.'</div>');cout('<div>'.mysql_error().'</div>');
		while ($row=mysql_fetch_assoc($res)) {
			$score_arr['test'][ $row['idOrg'] ][ $row['idUser'] ]=$row['score']+$row['bonus_score'];
		}
		

		$query = "SELECT t1.idOrg, t2.idUser, t1.idCourse, t2.score_raw, t2.score_min, t2.score_max ".
			" FROM ".$GLOBALS['prefix_lms']."_organization AS t1 ".
			" JOIN `".$GLOBALS['prefix_lms']."_scorm_tracking` AS t2 ON ( t1.objectType = 'scormorg' ".
			" AND t1.idOrg = t2.idReference ) ".   " , ".$GLOBALS['prefix_fw']."_user as t3 ".
		//" WHERE ".(!$all_courses ? " t1.idCourse IN (".implode(',', $courses).") " : " 1=1 " ).
		//( count($tempmilestones)>0 ? "AND t1.milestone IN (".implode(',', $tempmilestones).")" : "" );
			"WHERE t3.idst=t2.idUser AND t3.valid=1 ".
		(!$all_courses ? " AND t1.idCourse IN (".implode(',', $courses).") " : "" ).
		(count($tempmilestones)>0 ? " AND t1.milestone IN (".implode(',', $tempmilestones).") " : "" );

		$res = mysql_query($query); //cout('<div>'.$query.'</div>');cout('<div>'.mysql_error().'</div>');
		while ($row=mysql_fetch_assoc($res)) {
			$score_arr['scorm'][ $row['idOrg'] ][ $row['idUser'] ]=$row['score_raw'];
		}

		$buffer->openBody();


		//$LO_status = array('ab-initio'=>, 'passed'=>, 'completed'=>);
		$tlang = DoceboLanguage::createInstance('storage', 'lms');
		$LO_types = array();
		$res = mysql_query("SELECT objectType FROM ".$GLOBALS['prefix_lms']."_lo_types");
		while (list($idtype) = mysql_fetch_row($res)) $LO_types[$idtype] = $tlang->def('_LONAME_'.$idtype);

		//retrieve LO's data
		$query = "SELECT t0.idst as user_st, t0.userId, t0.firstname, t0.lastname, t1.idOrg, t1.objectType, t1.title, ".
			"t1.idResource, t1.milestone, t3.idCourse, t3.code, t3.name, t3.status as course_status, ".
			" t2.firstAttempt, t2.dateAttempt, t2.status ".//```
			" FROM ".$GLOBALS['prefix_fw']."_user as t0, ".
		$GLOBALS['prefix_lms']."_organization as t1, ".
		$GLOBALS['prefix_lms']."_commontrack as t2, ".
		$GLOBALS['prefix_lms']."_course as t3 ".
			" WHERE ".
			"t0.idst=t2.idUser AND t0.valid=1 AND t1.idOrg=t2.idReference AND t1.idCourse=t3.idCourse ".
		( !$all_courses ? " AND t1.idCourse IN (".implode(',', $courses).") " : "" ).
		( count($temptypes)>0 ? " AND t2.objectType IN (".implode(',', $temptypes).") " : "" ).
		( !$all_users ? " AND t2.idUser IN (".implode(',', $users).") " : "" ).
		( count($tempmilestones)>0 ? "AND t1.milestone IN (".implode(',', $tempmilestones).")" : "" );

		$res = mysql_query($query); //cout('<div>'.$query.'</div>');cout('<div>'.mysql_error().'</div>');
		while ($row = mysql_fetch_assoc($res)) {

			$temp=array();
			foreach ($this->LO_columns as $val)
			switch ($val['key']) {
				case 'userid': $temp[]=$acl_man->relativeId($row['userId']); break;
				case 'user_name': { if (in_array($val['key'], $showed)) $temp[]=$row['firstname']." ".$row['lastname']; } break;
				case '_CUSTOM_FIELDS_': {
					foreach ($customcols as $field) {
						if ($field['selected']) {
							if ( isset($field_values[$field['id']][$row['user_st']]) )
								$temp[] = $field_values[ $field['id'] ][ $row['user_st'] ];
							else
								$temp[] = '';
						}
					}
				} break;
				case 'course_code': $temp[]=$row['code']; break;
				case 'course_name': { if (in_array($val['key'], $showed)) $temp[]=$row['name']; } break;
				case 'course_status': { if (in_array($val['key'], $showed)) $temp[]=$this->_convertStatusCourse($row['course_status']); } break;
				case 'lo_type': { if (in_array($val['key'], $showed)) $temp[]=$LO_types[$row['objectType']]; } break;
				case 'lo_name': { if (in_array($val['key'], $showed)) $temp[]=$row['title']; } break;
				case 'lo_milestone': { if (in_array($val['key'], $showed)) $temp[]=$row['milestone']; } break;
				case 'firstAttempt': { if (in_array($val['key'], $showed)) $temp[]=$this->_convertDate($row['firstAttempt']); } break;
				case 'lastAttempt': { if (in_array($val['key'], $showed)) $temp[]=$this->_convertDate($row['dateAttempt']); } break;
				case 'lo_status': { if (in_array($val['key'], $showed)) $temp[]=$tlang->def($row['status']); } break;
				case 'lo_score': {
					if (in_array($val['key'], $showed)) {
						switch ($row['objectType']) {

							case 'test': {
								if (isset($score_arr['test'][ $row['idOrg'] ][ $row['user_st'] ]))
								$score_val=$score_arr['test'][ $row['idOrg'] ][ $row['user_st'] ];
								else
								$score_val='0';
								$temp[] = $score_val;
							} break;

							case 'scormorg' : {
								if (isset($score_arr['scorm'][ $row['idOrg'] ][ $row['user_st'] ]))
								$score_val=$score_arr['scorm'][ $row['idOrg'] ][ $row['user_st'] ];
								else
								$score_val='0';
								$temp[] = $score_val;
							} break;

							default: { $temp[] = ''; } break;
						}
					}
				} break;
				default: { if (in_array($val['key'], $showed)) $temp[]=''; } break;
			} //end switch - end for

			if ($this->use_mail) {
				$temp[]=//'<input type="checkbox" value="'.$row['idst'].'"/>'; //header for checkbox
					'<div class="align_center">'.Form::getInputCheckbox('mail_'.$row['user_st'], 'mail_recipients[]', $row['user_st'], isset($_POST['select_all']), '').'</div>';

			}

			$buffer->addLine($temp);
		}

		$buffer->closeBody();
		$buffer->closeTable();

		$output.=$buffer->get();

		addYahooJs(array('selector' => 'selector-beta-min.js'));
		addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/', '_selectall.js');

		if ($this->use_mail) {
			$output .= Form::openButtonSpace()
			.Form::getHidden('no_show_repdownload', 'no_show_repdownload', 1)
			.Form::openButtonSpace()
			.Form::getButton('send_mail', 'send_mail', $lang->def('_SEND_MAIL'))
			.'<button type="button" class="button" id="select_all" name="select_all" onclick="selectAll();">'.$lang->def('_SELECT_ALL').'</button>'//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
			.'<button type="button" class="button" id="unselect_all" name="unselect_all" onclick="unselectAll()">'.$lang->def('_UNSELECT_ALL').'</button>'//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
			//.Form::getButton('select_all', 'select_all', $lang->def('_SELECT_ALL'))
			//.Form::getButton('unselect_all', 'unselect_all', $lang->def('_UNSELECT_ALL'))
			.Form::closeButtonSpace();
			//cout(Form::closeForm());
		}

		return $output;//'<div>'.$query.'</div>';
	}

}

?>