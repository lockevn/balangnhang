<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
require_once($GLOBALS['where_lms'].'/admin/modules/report/report_schedule.php');

function _encode(&$data) { return serialize($data); } //{ return urlencode(serialize($data)); }
function _decode(&$data) { return unserialize($data); } //{ return unserialize(urldecode($data)); }

function unload_filter($temp=false) {
	$_SESSION['report']=array();
	if ($temp) $_SESSION['report_tempdata']=array();
	if (isset($_SESSION['report_update'])) unset($_SESSION['report_update']);

	$_SESSION['report_saved'] = false;
	$_SESSION['report_saved_data'] = array('id' => '', 'name' => '');
}

function load_filter($id, $tempdata=false, $update=false) {
	
	if ($id==false) return;

	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
	
	$row = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$id"));
	$temp = unserialize($row['filter_data']);
	if ($tempdata) $_SESSION['report_tempdata'] = $temp;
	$_SESSION['report'] = $temp;

	$_SESSION['report_saved'] = true;
	$_SESSION['report_saved_data'] = array('id' => $id, 'name' => getReportNameById($id));

	if ($update) $_SESSION['report_update'] = $id;
	else $_SESSION['report_update'] = false;
}

function openreport($idrep=false) {
	$lang =& DoceboLanguage::createInstance('report');

	if ($idrep!=false && $idrep>0)
	$id_report = $idrep;
	else {
		$id_report = $_SESSION['report_tempdata']['id_report'];

		if ($id_report!=false && $idrep>0)
		load_filter($idrep, true, false);
	}
	$query_report = "
	 SELECT class_name, file_name, report_name
	 FROM ".$GLOBALS['prefix_lms']."_report
	 WHERE id_report = '".$id_report."'";
	$re_report = mysql_query($query_report);

	if(mysql_num_rows($re_report) == 0) {
		reportlist();
		return;
	}
	list($class_name, $file_name, $report_name) = mysql_fetch_row($re_report);

	require_once($GLOBALS['where_lms'].'/admin/modules/report/'.$file_name);
	$obj_report = new $class_name( $id_report );

	return $obj_report;
}

function get_update_info() {
	$output = '';
	$lang =& DoceboLanguage::createInstance('report');
	if (isset($_SESSION['report_update'])) {
		$ref =& $_SESSION['report_update'];
		if (is_int($ref) && $ref>0) {
			$output .= $lang->def('_REPORT_MODIFYING').getReportNameById($_SESSION['report_update']);
		}
	}
	return $output;
}

//******************************************************************************

$lang =& DoceboLanguage::createInstance('report');

function close_page() {
	return '</div>'; //close std_block div
}

define('_REP_KEY_NAME',     'name');
define('_REP_KEY_CREATOR',  'creator');
define('_REP_KEY_CREATION', 'creation');
define('_REP_KEY_PUBLIC',   'public');
define('_REP_KEY_OPEN',     'open');
define('_REP_KEY_MOD',      'mod');
define('_REP_KEY_SCHED',    'sched');
define('_REP_KEY_REM',      'rem');

function get_report_table($url='') {
	checkPerm('view');
	$can_mod = checkPerm('mod'  , true);

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$acl_man =& $GLOBALS['current_user']->getACLManager();
	$level = $GLOBALS['current_user']->getUserLevelId(getLogUserId());

	$admin_cond = '';
	switch ($level) {
		case ADMIN_GROUP_GODADMIN : break;
		case ADMIN_GROUP_ADMIN :;
		case ADMIN_GROUP_PUBLICADMIN :;
		case ADMIN_GROUP_USER :;
		default : $admin_cond .= " WHERE t1.author=".getLogUserId(); break;
	}

	//addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/', 'ajax.report.js');

	
	$lang =& DoceboLanguage::createInstance('report');
	$output = '';

	$is_admin = ( ($level==ADMIN_GROUP_GODADMIN || $level==ADMIN_GROUP_ADMIN) ? true : false);

	if ($is_admin || $can_mod) {//if ($can_mod) {
		cout('<script type="text/javascript">
		var _FAILURE = "error";
		var ajax_path = "'.$GLOBALS['where_lms_relative'].'/ajax.adm_server.php?mn=report&plf=lms";

		function public_report(o, id_rep) {
			o.disabled=true; //no more operations allowed on the checkbox while ajaxing

			var val_el=document.getElementById("enable_value_"+id_rep);
			var value=val_el.value;

			var data = "&op=public_rep&id="+id_rep+"&val="+value;
			var objAjax = YAHOO.util.Connect.asyncRequest("POST", ajax_path+data, {
			success:function(t) {
				var temp=o.src;
				if (value==1)	{ o.src=temp.replace("unpublish.gif", "publish.gif"); val_el.value=0; }
				if (value==0)	{ o.src=temp.replace("publish.gif", "unpublish.gif"); val_el.value=1; }
					o.disabled=false;
				},
			failure:function(t) {
					o.disabled=false;
					alert(_FAILURE); //...
				} });
			}
		</script>', 'page_head');
	}


	//filter by author
	if ($level==ADMIN_GROUP_GODADMIN) {//if ($can_mod) {

	addYahooJs();
	$filter = get_req('author_filther', DOTY_INT, 0);
	$current_user = $acl_man->getUser(getLogUserId(), false);
	$authors = array(
		0 => '('.$lang->def('_ALLCOURSES_FOOTER').')', //recycle text key
		$current_user[ACL_INFO_IDST] => $acl_man->relativeId($current_user[ACL_INFO_USERID])
	);
	$query = "SELECT u.idst, u.userid FROM ".$GLOBALS['prefix_lms']."_report_filter as r JOIN ".$GLOBALS['prefix_fw']."_user as u ON (r.author=u.idst) WHERE u.idst<>".getLogUserId()." ORDER BY u.userid";
	$res = mysql_query($query);
	while ($row = mysql_fetch_assoc($res)) { $authors[$row['idst']] = $acl_man->relativeId($row['userid']); }
	$output .= '<script type="text/javascript"></script>';
	$output .=
    Form::openForm('author_form', 'index.php', false, 'GET').
    Form::getHidden('op', 'op', 'reportlist').
    Form::getHidden('modname', 'modname', 'report').
    Form::getDropDown($lang->def('_AUTHOR'), 'author_filther', 'author_filther', $authors , $filter, '', '', 'onchange="YAHOO.util.Dom.get(\'author_form\').submit();"' ).
		Form::getBreakRow().
    Form::closeForm();

	} else {
		$filter=0;
	}
	//end filter

	$query = "SELECT t1.*, t2.userid FROM ".
		$GLOBALS['prefix_lms']."_report_filter as t1 LEFT JOIN ".$GLOBALS['prefix_fw']."_user as t2 ON t1.author=t2.idst ".$admin_cond;
	if ($filter>0) {
		if ($admin_cond=='')
			$query .= " WHERE t1.author=".$filter;
		else
			$query .= " OR t1.author=".$filter;
	}


	$tb = new TypeOne($GLOBALS['lms']['visu_course']);
	$tb->initNavBar('ini', 'button');
	$col_type = array('','','align_center','image','image','image');//,'image','image');
	$col_content = array(
		$lang->def('_NAME'),
		$lang->def('_TAB_REP_CREATOR'),
		$lang->def('_CREATION_DATE'),
		$lang->def('_TAB_REP_PUBLIC'),
		'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_VIEW').'" title="'.$lang->def('_VIEW').'" />',
		'<img src="'.getPathImage().'standard/wait_alarm.png" alt="'.$lang->def('_REP_TITLE_SCHED').'" title="'.$lang->def('_REP_TITLE_SCHED').'" />'/*,
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_REP_TITLE_MOD').'" title="'.$lang->def('_MOD').'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_REP_TITLE_REM').'" title="'.$lang->def('_DEL').'" />'	*/
	);

	if ($is_admin || $can_mod) {
		$col_type[]='image';
		$col_type[]='image';
		$col_content[]='<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_MOD').'" />';
		$col_content[]='<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_REP_TITLE_REM').'" title="'.$lang->def('_DEL').'" />';
	}

	$tb->setColsStyle($col_type);
	$tb->addHead($col_content);

	if ($res = mysql_query($query)) {
		while ($row = mysql_fetch_assoc($res)) {
			$id = $row['id_filter'];
			$opn_link =
				'<a href="index.php?modname=report&amp;op=show_results&amp;idrep='.$id.'" '. //'.$url.'&amp;action=open&amp;idrep='.$id.'" '.
				' title="'.$lang->def('_VIEW').'">'.
				'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_VIEW').'" />'.
				'</a>';
			$sch_link =
			//'<a href="'.$url.'&amp;action=schedule&amp;idrep='.$id.'" '.
				'<a href="index.php?modname=report&amp;op=schedulelist&amp;idrep='.$id.'" '.
				' title="'.$lang->def('_REP_TITLE_SCHED').'">'.
				'<img src="'.getPathImage().'standard/wait_alarm.png" alt="'.$lang->def('_REP_TITLE_SCHED').'" />'.
				'</a>';
			$mod_link =
				'<a href="'.$url.'&amp;action=modify&amp;idrep='.$id.'" '.
				' title="'.$lang->def('_MOD').'">'.
				'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" />'.
				'</a>';
			$rem_link =
				'<a href="'.$url.'&amp;action=delete&amp;idrep='.$id.'" '.
				' title="'.$lang->def('_DEL').' : '.($row['author'] == 0 ? $lang->def($row['filter_name']) : $row['filter_name']).'">'.
				'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" />';//.
				'</a>';
			$can_public = ($can_mod ? true : ($is_admin && $row['author']==getLogUserId() ? true : false));
			$public = '<image '.($can_public ? 'class="handover"' : '').' src="'.getPathImage('lms').'webpages/'.
			($row['is_public']==1 ? '' : 'un').'publish.gif'.'" '.
			($is_admin || $can_mod ? 'onclick="public_report(this, '.$row['id_filter'].');" ' : '').' />'.
				'<input type="hidden" id="enable_value_'.$row['id_filter'].'" '.
				'value="'.($row['is_public']==1 ? '0' : '1').'" />';
			$tb_content = array(
				_REP_KEY_NAME     => ($row['author'] == 0 ? $lang->def($row['filter_name']) : $row['filter_name']),
				_REP_KEY_CREATOR  => ($row['author'] == 0 ? '<div class="align_center">-</div>' : $acl_man->relativeId($row['userid'])),
				_REP_KEY_CREATION => $GLOBALS['regset']->databaseToRegional($row['creation_date']),
				_REP_KEY_PUBLIC   => $public,//$row['report_name'],
				_REP_KEY_OPEN     => $opn_link,
				_REP_KEY_SCHED    => $sch_link/*,
				_REP_KEY_MOD    => $mod_link,
				_REP_KEY_REM    => $rem_link*/
			);
			if ($is_admin || $can_mod) {
				if ($row['author']==getLogUserId() || $can_mod) {
					$tb_content[_REP_KEY_MOD] = $mod_link;
					$tb_content[_REP_KEY_REM] = $rem_link;
				} else {
					$tb_content[_REP_KEY_MOD] = '&nbsp;';
					$tb_content[_REP_KEY_REM] = '&nbsp;';
				}
			}
			$tb->addBody($tb_content);
		}
	}

	if ($is_admin || $can_mod) {//if ($can_mod) {
		$tb->addActionAdd('
			<a href="index.php?modname=report&amp;op=report_category">'.
		'<img src="'.getPathImage().'standard/add.gif" '.
			'title="'.$lang->def('_NEWREPORT_TITLE').'" /> '.
			$lang->def('_NEWREPORT_TITLE').'</a>');
	}

	$output .= $tb->getTable();

	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delete]');

	return $output;
}

//step functions

function reportlist() {
	checkPerm('view');

	require_once(dirname(__FILE__).'/class.report.php'); //reportbox class
	require_once(dirname(__FILE__).'/report_schedule.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');

	if ($action = get_req('action', DOTY_STRING, false)) {
		switch ($action) {
			case 'sched_rem': {
				report_delete_schedulation(get_req('id_sched', DOTY_INT, false));
			} break;
		}
	}

	unload_filter(true);

	$lang =& DoceboLanguage::createInstance('report');

	//addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/','ajax.report.js');

	//$lang->def('_REPORT_LIST_CAPTION')
	cout(getTitleArea($lang->def('_REPORT'), 'report'));
	cout('<div class="std_block">');
	//cout(get_report_steplist($step_index));

	switch (get_req('saverep', DOTY_STRING, false)) {
		case 'true'  : cout( getResultUi($lang->def('_SAVE_REPORT_OK')) ); break;
		case 'false' : cout( getErrorUi($lang->def('_SAVE_REPORT_FAIL')) ); break;
	}

	switch (get_req('modrep', DOTY_STRING, false)) {
		case 'true'  : cout( getResultUi($lang->def('_MOD_REPORT_OK')) ); break;
		case 'false' : cout( getErrorUi($lang->def('_MOD_REPORT_FAIL')) ); break;
	}

	cout(get_report_table('index.php?modname=report&op=report_open_filter'));

	cout( close_page() );//std_block div
}

function report_category() {
	checkPerm('mod');

	require_once(dirname(__FILE__).'/class.report.php'); //reportbox class
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
	//require_once('report_categories.php');
	load_categories();

	$lang =& DoceboLanguage::createInstance('report');

	$step_index = 0;
	//cout( get_page_title($step_index) );

	$page_title = getTitleArea(array(
			'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
			$lang->def('_REPORT_SEL_CATEGORY')
		), 'report'/*, $lang->def('_ALT_REPORT')*/);
	cout($page_title.'<div class="std_block">');

	$error = get_req('err', DOTY_STRING, false);
	switch ($error) {
		case 'noname': {
			cout( getErrorUi($lang->def('_REPORT_NONAME')) );
		} break;
	}

	/*$info = get_status_info();
	if($info) cout( getInfoUi($info) );*/

	$box = new ReportBox('report_create');

	$box->title = $lang->def('_REPORT_CREATE');
	$box->description = $lang->def('_REPORT_CREATE_DESCRIPTION');
	//$GLOBALS['page']->add('<div class="report_box"><h2>'.$lang->def('_REPORT_CHOOSE').'</h2>', 'content');

	$box->body =
	Form::openForm('repcat_form', 'index.php?modname=report&op=report_rows_filter').
	Form::getHidden('set_category', 'set_category', 1);
	//Form::openElementSpace();

	$box->body .= Form::getTextField(
		$lang->def('_SAVE_REPORT_NAME'), //$label_name,
		'report_name',
		'report_name',
		'200');

	$temp = array();
	foreach ($GLOBALS['report_categories'] as $key=>$value) {
		$temp[$key] = $lang->def($value );
	}
	$box->body .=
	Form::getDropDown(
		$lang->def('_SELECT_REPORT_CATEGORY'), '', 'id_report', $temp);

	$box->body .=
	//Form::closeElementSpace().
	Form::openButtonSpace().
	Form::getButton( '', 'cat_forward', $lang->def('_FORWARD'), false).
	Form::getButton( '', 'cat_undo', $lang->def('_UNDO'), false).
	Form::closeButtonSpace().
	Form::closeForm();

	cout($box->get());

	/*$lang->def('_REPORT_SCHEDMAN');$lang->def('_REPORT_SCHEDMAN_DESC');*/

	cout( close_page() );
}

function report_rows_filter() {
	checkPerm('mod');

	if (get_req('cat_undo', DOTY_MIXED, false)) jumpTo('index.php?modname=report&op=reportlist');

	$lang =& DoceboLanguage::createInstance('report');
	$ref =& $_SESSION['report_tempdata'];

	if (get_req('set_category', DOTY_INT, 0)==1) {
		if (get_req('report_name', DOTY_STRING, '')=='') jumpTo('index.php?modname=report&op=report_category&err=noname');
		$ref['id_report'] = get_req('id_report', DOTY_ALPHANUM, false);
		$ref['report_name'] = get_req('report_name', DOTY_STRING, false);
	}

	$obj_report = openreport();
	$obj_report->back_url = 'index.php?modname=report&op=report_category';
	$obj_report->jump_url = 'index.php?modname=report&op=report_rows_filter';
	$obj_report->next_url = 'index.php?modname=report&op=report_sel_columns';

	$page_title = getTitleArea(array(
			'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
	  'index.php?modname=report&amp;op=report_category' => $lang->def('_REPORT_SEL_CATEGORY'),//$obj_report->report_name,
			$lang->def('_REPORT_SEL_ROWS')
		), 'report'/*, $lang->def('_ALT_REPORT')*/);

	if ($obj_report->usestandardtitle_rows) {
		cout($page_title.'<div class="std_block">');//.getBackUi($obj_report->back_url, $lang->def('_BACK'), 'content'));
	} else {
		//this is used just to pass std title string to object functions, who may use it
		$obj_report->page_title = $page_title;
	}

	$obj_report->get_rows_filter();

	if ($obj_report->usestandardtitle_rows) {
		cout('</div>'); //close title area
	}
}

function report_sel_columns() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('report');
	$obj_report = openreport();
	$temp = $obj_report->get_columns_categories();
	$box = new ReportBox('choose_col');

	cout(getTitleArea(array(
			'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
			'index.php?modname=report&amp;op=report_category' => $lang->def('_REPORT_SEL_CATEGORY'),//$obj_report->report_name,
			'index.php?modname=report&amp;op=report_rows_filter' => $lang->def('_REPORT_SEL_ROWS'),
				$lang->def('_REPORT_SEL_COLUMNS')
			))
		.'<div class="std_block">');

	$box->title_css = 'choose_details';
	$box->title = $lang->def('_REPORT_CHOOSE_COLUMNS');
	$box->description = $lang->def('_REPORT_LIST_CAPTION');
	$box->body = Form::openForm('choose_category_form','index.php?modname=report&op=report_columns_filter&of_platform=lms');

	$i = 1;
	foreach ($temp as $key=>$value) {

		$box->body .= Form::getRadio( $i.') '.$value, 'sel_columns_'.$key, 'columns_filter', $key, ($i==1)/*,  ($key==0 ? true : false) */);
		$i++;
	}
	$box->body.=
	Form::openButtonSpace().
	Form::getButton( '', '', $lang->def('_CONFIRM'), false).
	Form::closeButtonSpace().
	Form::closeForm();

	cout($box->get().'</div>');
}

function report_columns_filter() {
	checkPerm('mod');

	$ref =& $_SESSION['report_tempdata']['columns_filter_category'];
	if (isset($_POST['columns_filter']))
	$ref = $_POST['columns_filter'];

	$lang =& DoceboLanguage::createInstance('report');

	$obj_report = openreport();
	$obj_report->back_url = 'index.php?modname=report&op=report_sel_columns';
	$obj_report->jump_url = 'index.php?modname=report&op=report_columns_filter';
	$obj_report->next_url = 'index.php?modname=report&op=report_save';

	//page title
	$page_title = getTitleArea(array(
			'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
			'index.php?modname=report&amp;op=report_category' => $lang->def('_REPORT_SEL_CATEGORY'),
			'index.php?modname=report&amp;op=report_rows_filter' => $lang->def('_REPORT_SEL_ROWS'),
			'index.php?modname=report&amp;op=report_sel_columns' => $lang->def('_REPORT_SEL_COLUMNS'),
			$lang->def('_REPORT_COLUMNS')
		))
	.'<div class="std_block">';
	//.  	getBackUi($obj_report->back_url, $lang->def('_BACK'), 'content');

	if($obj_report->useStandardTitle_Columns()) {
		cout($page_title);
		cout(Form::openForm('report_columns_form', $obj_report->jump_url));
	} else {
		//this is used just to pass std title string to object functions, who may use it
		$obj_report->page_title = $page_title;
	}

	$output = $obj_report->get_columns_filter($_SESSION['report_tempdata']['columns_filter_category']);
	cout($output);

	if ($obj_report->useStandardTitle_Columns()) {
		cout(
			Form::openButtonSpace()
			.Form::getBreakRow()
			.Form::getButton('pre_filter', 'pre_filter', $lang->def('_SHOW_NOSAVE', 'report'))
			.Form::getButton('ok_filter', 'import_filter', $lang->def('_SAVE_BACK', 'report'))
			.Form::getButton('show_filter', 'show_filter', $lang->def('_SAVE_SHOW', 'report'))
			.Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO', 'report'))
			.Form::closeButtonSpace()
			.Form::closeForm());
		cout('</div>'); //close std_block div
	}
}

function report_save_filter() {
	checkPerm('mod');

	$ref =& $_SESSION['report_tempdata'];
	$report_id = $ref['id_report'];
	$report_name = $ref['report_name'];
	$nosave = get_req('nosave', DOTY_INT, 0);
	$show = get_req('show', DOTY_INT, 0);
	$idrep = get_req('modid', DOTY_INT, false);

	if ($nosave>0) {
		jumpTo('index.php?modname=report&op=show_results&nosave=1'.($idrep ? '&modid='.$idrep : ''));
	}

	if (isset($_SESSION['report_update'])  || $idrep) {
		$save_ok = report_update($idrep, $report_name, $ref);
		if ($show) {
			jumpTo('index.php?modname=report&op=show_results&idrep='.$idrep);
		} else {
			jumpTo('index.php?modname=report&op=reportlist&modrep='.($save_ok ? 'true' : 'false'));
		}
	} else {
		$save_ok = report_save($report_id, $report_name, $ref);
		if ($show) {
			jumpTo('index.php?modname=report&op=show_results&idrep='.$save_ok);
		} else {
			jumpTo('index.php?modname=report&op=reportlist&saverep='.($save_ok ? 'true' : 'false'));
		}
	}
}

function setup_report_js() {

	addYahooJs(array(
		'animation' 		=> 'animation-min.js',
		'dragdrop' 			=> 'dragdrop-min.js',
		'button' 			=> 'button-min.js',
		'container' 		=> 'container-min.js',
		'my_window' 		=> 'windows.js'
	), array(
		'container/assets/skins/sam' => 'container.css',
		'button/assets/skins/sam' => 'button.css'
	));
	addJs($GLOBALS['where_lms_relative'].'/admin/modules/report/', 'ajax.report.js');
}

function report_show_results($idrep = false) {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.download.php' );

	//import yui pop-up stuff
	setup_report_js();

	$lang			=& DoceboLanguage::createInstance('report');
	$start_url		= 'index.php?modname=report&op=reportlist';
	$download		= get_req('dl', DOTY_STRING, false);
	$no_download	= get_req('no_show_repdownload', DOTY_INT, 0);
	$nosave			= get_req('nosave', DOTY_INT, 0);

	if($idrep == false) {
		//die( print_r($_SESSION['report_tempdata'], true ) );
		if (!isset($_SESSION['report_tempdata'])) $ref =& $_SESSION['report']; else $ref =& $_SESSION['report_tempdata'];
		$id_report = $ref['id_report'];
		$res = mysql_query("SELECT class_name, file_name FROM ".$GLOBALS['prefix_lms']."_report WHERE id_report=".$id_report." AND enabled=1");
		$author = 0;
		$filter_name = $ref['report_name'];
		//['columns_filter_category'] 
		if ($res && (mysql_num_rows($res)>0)) {
			list($class_name, $file_name) = mysql_fetch_row($res);
			require_once($GLOBALS['where_lms'].'/admin/modules/report/'.$file_name);
		} else {
			reportlist();
		}
	
	} else {
		/// find main class report filename and report info
		$query_report = "
		SELECT r.class_name, r.file_name, r.report_name, f.filter_name, f.filter_data, f.author
		FROM ".$GLOBALS['prefix_lms']."_report AS r
			JOIN ".$GLOBALS['prefix_lms']."_report_filter AS f
			ON ( r.id_report = f.id_report )
		WHERE f.id_filter = '".$idrep."'";
		$re_report = mysql_query($query_report);

		if(mysql_num_rows($re_report) == 0) {
			reportlist();
			return;
		}
		
		// create the report object
		list($class_name, $file_name, $report_name, $filter_name, $filter_data, $author) = mysql_fetch_row($re_report);
		require_once($GLOBALS['where_lms'].'/admin/modules/report/'.$file_name);
	}
	
	$obj_report = new $class_name( $idrep );
	$obj_report->back_url = $start_url;
	$obj_report->jump_url = 'index.php?modname=report&op=show_results&idrep='.$idrep;

	if($author == 0) $filter_name = $lang->def($filter_name);

	$data = _decode( $filter_data ) ;

	if($download != false) {
		switch ($download) {
			case 'htm': { sendStrAsFile($obj_report->getHTML($data['columns_filter_category'], $data), 'report.html'); };break;
			case 'csv': { sendStrAsFile($obj_report->getCSV($data['columns_filter_category'], $data), 'report.csv'); };break;
			case 'xls': { sendStrAsFile($obj_report->getXLS($data['columns_filter_category'], $data), 'report.xls'); };break;
		}
	}

	cout(getTitleArea(array($start_url => $lang->def('_REPORT'), $filter_name), 'report')
		.'<div class="std_block">'
		.getBackUi($start_url, $lang->def('_BACK_TO_LIST'), 'content'));

	if ($nosave > 0) {
		$mod_id = get_req('modid', DOTY_INT, false);
		cout(getBackUi('index.php?modname=report&op=report_columns_filter'.($mod_id ? '&modid='.$mod_id : ''), $lang->def('_BACK')).
			getBackUi('index.php?modname=report&op=report_save'.($mod_id ? '&modid='.$mod_id : ''), $lang->def('_SAVE_AND_BACK_TO_LIST')));
	}
	if($no_download <= 0) {

		cout('<p class="export_list">'.
			'<a class="export_htm" href="'.$obj_report->jump_url.'&amp;dl=htm">'.$lang->def('_EXPORT_HTML').'</a>&nbsp;'.
			'<a class="export_csv" href="'.$obj_report->jump_url.'&amp;dl=csv">'.$lang->def('_EXPORT_CSV').'</a>&nbsp;'.
			'<a class="export_xls" href="'.$obj_report->jump_url.'&amp;dl=xls">'.$lang->def('_EXPORT_XLS').'</a>'.
			'</p>'.
			//'<div class="nofloat"></div>'.
			'<br/>');
	}

	// css -----------------------------------------------------------
	cout('<link href="'.getPathTemplate('lms').'style/report/style_report_user.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
	// $_SESSION['report_tempdata']['columns_filter_category']

	cout(Form::openForm('user_report_columns_courses', $obj_report->jump_url));
	cout($obj_report->show_results($data['columns_filter_category'], $data));
	cout(Form::closeForm().'</div>');

}

function report_open_filter() {
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');

	$url='index.php?modname=report&op=reportlist';
	$filter_id = get_req('idrep', DOTY_INT, false);
	$action = get_req('action', DOTY_STRING, '');
	if (!$filter_id) { jumpTo($url); return false; }

	switch ($action) {
		case 'schedule': {
			load_filter($filter_id,true);
			jumpTo('index.php?modname=report&op=report_schedule');
		} break;

		case 'open': {
			load_filter($filter_id, true);
			jumpTo('index.php?modname=report&op=show_results');
		} break;

		case 'modify': {
			load_filter($filter_id,true,true); //will load it after the jumpTo
			jumpTo('index.php?modname=report&op=modify_name&modid='.$filter_id);
		} break;

		case 'delete': {
			//delete filter from list and DB, than reload page
			//if (mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$filter_id")) {
			if (report_delete_filter($filter_id)) {
				$success = '&fdel=1&idrep='.$filter_id;
			} else {
				$success = '&fdel=0&idrep='.$filter_id;
			}
			jumpTo($url.$success);
		} break;

		default: jumpTo($url);
	}

}

function schedulelist() {
	require_once('report_schedule.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');

	if ($action = get_req('action', DOTY_STRING, false)) {
		switch ($action) {
			case 'sched_rem': {
				report_delete_schedulation(get_req('id_sched', DOTY_INT, false));
			} break;
		}
	}

	if (isset($_SESSION['schedule_tempdata'])) {
		unset($_SESSION['schedule_tempdata']);
	}
	if (isset($_SESSION['schedule_update'])) {
		unset($_SESSION['schedule_update']);
	}

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('report');

	setup_report_js();

	$idrep = get_req('idrep', DOTY_INT, false);
	cout(getTitleArea(array(
	  'index.php?modname=report&amp;op=reportlist' => $lang->def('_SCHEDULE'),
				$lang->def('_SCHEDULATIONS_FOR_REPORT').'"<b>'.getReportNameById($idrep).'</b>"' ) ) );

	cout('<div class="std_block">');
	cout(get_schedulations_table($idrep));

	cout( close_page() ); //std_block div
}

//******************************************************************************

function report_modify_name() {
	checkPerm('mod');

	require_once(dirname(__FILE__).'/class.report.php'); //reportbox class
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
	//require_once('report_categories.php');
	load_categories();

	$lang =& DoceboLanguage::createInstance('report');

	$idrep = get_req('modid', DOTY_INT, false);
	//if (!idrep) jumpTo(initial page ... )

	$page_title = getTitleArea(array(
			'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
			$lang->def('_REPORT_MOD_NAME')
		), 'report'/*, $lang->def('_ALT_REPORT')*/);
	cout($page_title.'<div class="std_block">');

	$info = get_update_info();
	if($info) cout( getInfoUi($info) );

	$box = new ReportBox('report_modify_name');

	$box->title = $lang->def('_REPORT_MOD_NAME');
	$box->description = $lang->def('_REPORT_MODNAME_DESC');

	$box->body =
	Form::openForm('repcat_form', 'index.php?modname=report&op=modify_rows&modid='.$idrep).
	Form::getHidden('mod_name', 'mod_name', 1);

	$box->body .= Form::getTextField(
		$lang->def('_MOD_REPORT_NAME'), //$label_name,
		'report_name',
		'report_name',
		'200', getReportNameById($idrep));

	$box->body .=
	//Form::closeElementSpace().
	Form::openButtonSpace().
	Form::getButton( '', '', $lang->def('_FORWARD'), false).
	Form::closeButtonSpace().
	Form::closeForm();

	cout($box->get());

	/*$lang->def('_REPORT_SCHEDMAN');$lang->def('_REPORT_SCHEDMAN_DESC');*/

	cout( close_page() );
}

function report_modify_rows() {
	checkPerm('mod');

	$lang =& DoceboLanguage::createInstance('report');
	$ref =& $_SESSION['report_tempdata'];

	$idrep = get_req('modid', DOTY_INT, false);

	if (get_req('mod_name', DOTY_INT, 0)==1) {
		$ref['report_name'] = get_req('report_name', DOTY_STRING, false);
	}

	$obj_report = openreport();
	$obj_report->back_url = 'index.php?modname=report&op=modify_name&modid='.$idrep;
	$obj_report->jump_url = 'index.php?modname=report&op=modify_rows&modid='.$idrep;
	$obj_report->next_url = 'index.php?modname=report&op=modify_cols&modid='.$idrep;

	$page_title = getTitleArea(array(
			'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
			'index.php?modname=report&op=modify_name&modid='.$idrep => $lang->def('_REPORT_MOD_NAME'),
			$lang->def('_REPORT_MOD_ROWS')
		), 'report'/*, $lang->def('_ALT_REPORT')*/);

	/*$info = get_update_info();
	if($info) getInfoUi($info) );*/

	if ($obj_report->usestandardtitle_rows) {
		cout($page_title.'<div class="std_block">');//.getBackUi($obj_report->back_url, $lang->def('_BACK'), 'content'));
		$info = get_update_info();
		if($info) cout( getInfoUi($info) );
		//cout(Form::openForm('user_report_rows_courses_mod', $obj_report->jump_url));
	} else {
		//this is just used to pass std title string to object functions, who may use it
		$obj_report->page_title = $page_title;
	}

	$obj_report->get_rows_filter();

	if ($obj_report->usestandardtitle_rows) {
		//cout(Form::closeForm());
		cout('</div>'); //close title area
	}
}

function report_modify_columns() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$ref =& $_SESSION['report_tempdata']['columns_filter_category'];
	if (isset($_POST['columns_filter']))
	$ref = $_POST['columns_filter'];

	$idrep = get_req('modid', DOTY_INT, false);
	$lang =& DoceboLanguage::createInstance('report');

	$obj_report = openreport();
	$obj_report->back_url = 'index.php?modname=report&op=modify_rows&modid='.$idrep;
	$obj_report->jump_url = 'index.php?modname=report&op=modify_cols&modid='.$idrep;
	$obj_report->next_url = 'index.php?modname=report&op=report_save&modid='.$idrep;

	//page title
	$page_title = getTitleArea(array(
		  'index.php?modname=report&amp;op=reportlist' => $lang->def('_REPORT'),
		  'index.php?modname=report&op=modify_name&modid='.$idrep => $lang->def('_REPORT_MOD_NAME'),
	  'index.php?modname=report&op=modify_rows&modid='.$idrep => $lang->def('_REPORT_MOD_ROWS'),
			$lang->def('_REPORT_MOD_COLUMNS')
		))
	.'<div class="std_block">';

	/*$info = get_update_info();
	if($info) cout( getInfoUi($info) );*/

	if($obj_report->useStandardTitle_Columns()) {
		cout($page_title);
		$info = get_update_info();
		if($info) cout( getInfoUi($info) );
		cout(Form::openForm('user_report_columns_courses_mod', $obj_report->jump_url));
	} else {
		//this is used just to pass std title string to object functions, who may use it
		$obj_report->page_title = $page_title;
	}

	$output = $obj_report->get_columns_filter($_SESSION['report_tempdata']['columns_filter_category']);
	cout($output);

	if ($obj_report->useStandardTitle_Columns()) {
		cout(Form::openButtonSpace());
		cout(
			Form::getBreakRow()
			.Form::getButton('pre_filter', 'pre_filter', $lang->def('_SHOW_NOSAVE', 'report'))
			.Form::getButton('ok_filter', 'import_filter', $lang->def('_SAVE_BACK', 'report'))
			.Form::getButton('show_filter', 'show_filter', $lang->def('_SAVE_SHOW', 'report'))
			.Form::getButton('undo_filter', 'undo_filter', $lang->def('_UNDO', 'report'))
		);
		cout(Form::closeButtonSpace());
		cout(Form::closeForm());
		cout('</div>'); //close std_block div
	}
}

// switch
function reportDispatch($op) {

	
	if(isset($_POST['save_showed'])) $op = 'report_schedule';

	switch($op) {
		case "reportlist" : {
			reportlist();
		};break;

		case "report_category" : {
			report_category();
		};break;

		case "report_rows_filter" : {
			report_rows_filter();
		};break;

		case "report_sel_columns" : {
			report_sel_columns();
		};break;

		case "report_columns_filter" : {
			report_columns_filter();
		};break;

		case "report_save" : {
			if (get_req('nosave', DOTY_INT, 0)>0) {
				report_show_results(false);
			}
			report_save_filter();
		} break;

		case "show_results": {
			report_show_results(get_req('idrep', DOTY_INT, false));
		} break;

		case "modify_name": {
			report_modify_name();
		} break;

		case "modify_rows": {
			report_modify_rows();
		} break;

		case "modify_cols": {
			report_modify_columns();
		} break;

		case "sched_mod": {
			require_once('report_schedule.php');
			modify_schedulation();
		} break;

		case "report_open_filter": {
			report_open_filter();
		} break;

		case "report_schedule": {
			require_once('report_schedule.php');
			schedule_report();
		} break;

		case "schedulelist": {
			schedulelist();
		} break;
	} // end switch

}

?>