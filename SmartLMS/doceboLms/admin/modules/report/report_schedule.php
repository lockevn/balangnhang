<?php
/************************************************************************/
/* DOCEBO LMS - Learning managment system        */
/* ============================================       */
/*                  */
/* Copyright (c) 2008             */
/* http://www.docebo.com            */
/*                  */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.  */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function get_action_status($idrep) {
	$lang =& DoceboLanguage::createInstance('report', 'framework');
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
	$output = '';
	if (isset($_SESSION['schedule_update']))
		$output .= $lang->def('_INFO_MODIFYING_SCHEDULATION').getScheduleNameById($_SESSION['schedule_update']).'<br />';
	else
		$output .= $lang->def('_INFO_NEW_SCHEDULATION_CREATION');
	$output .= '<br />'.$lang->def('_INFO_WHICH_IS_BEING_SCHEDULATED').getReportNameById($idrep);
	
	return $output;
}

//users selector
function schedule_recipients($idrep) {
    checkPerm('mod');

		$lang =& DoceboLanguage::createInstance('report', 'framework');
		
		if (get_req('schedule_undo', DOTY_MIXED, false)) {
			//$back_op = get_req()
			$back_url  = 'index.php?modname=report&op=schedulelist&idrep='.$idrep;
			jumpTo($back_url);
		}
		
		$back_url = 'index.php?modname=report&op=report_schedule&idrep='.$idrep;
		$jump_url = 'index.php?modname=report&op=report_schedule&idrep='.$idrep;
		$end_url = 'index.php?modname=report&op=schedulelist&idrep='.$idrep;
	
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.directory.php');
		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
		//require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		
		$aclManager = new DoceboACLManager();
		$user_select = new Module_Directory();
		
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		
		if(!isset($_POST['is_updating'])) {

			//save filter, if needed
			require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
			//save schedulation data in session
			if (!isset($_SESSION['schedule_tempdata'])) {
				$_SESSION['schedule_tempdata'] = array();
			}
			
			switch ($_POST['cron_radio']) {
				case 'day'   : $sched_info = ''; break;
				case 'week'  : $sched_info = $_POST['cron_weekly']; break;
				case 'month' : $sched_info = $_POST['cron_monthly']; break;
				default : $sched_info = ''; break;
			}
			
			$sched_time = '';//$_POST['cron_hours'].':'.$_POST['cron_minutes'].':00';
						
			$ref =& $_SESSION['schedule_tempdata'];
			
			$ref['name']        = $_POST['sched_name'];
			$ref['period']      = $_POST['cron_radio'];
			$ref['period_info'] = $sched_info;
			$ref['time']        = $sched_time;

			$user_select->resetSelection($ref['recipients']);
		}
		$info_status = getInfoUi(get_action_status($idrep));
		
		$save_schedule_failed = false;
				
		if(isset($_POST['cancelselector'])) {
			
			//jumpTo($back_url);
			jumpTo('index.php?modname=report&op=schedulelist&idrep='.$idrep);
			
		} elseif(isset($_POST['okselector'])) {
			$ref =& $_SESSION['schedule_tempdata'];

			$entity_selected 	= $user_select->getSelection($_POST);
			$_temp =& $aclManager->getAllUsersFromIdst($entity_selected);
		
			//$_temp = $ref['recipients'];
			$_name = $ref['name'];
			$_time = $ref['time'];
			$_period = $ref['period'].','.$ref['period_info'];			
			  
			//get current saved report ID from session (check if report is saved, otherwise -> error)
			
			if (isset($_SESSION['schedule_update'])) {
				$sched = report_update_schedulation($_SESSION['schedule_update'], $_name, $_period, $_time, $_temp);
			} else {
				$id_report = $idrep;//$_SESSION['report_saved_data']['id'];
				$sched = report_save_schedulation($id_report, $_name, $_period, $_time, $_temp);
			}
			
			if ($sched!==false) {
				//unset($_SESSION['schedule_tempdata']);
				jumpTo($end_url);
			} else {
				$save_schedule_failed = true;
			}
		}
		
		$error_info = '';
		if ($save_schedule_failed) {
			//$page_title .= ''; //...
			//addforminfo
			$error_info = getErrorUi($lang->def('_SAVE_SCHEDULE_FAIL'));
		}
		
		//draw selector
		$user_select->show_user_selector = TRUE;
		$user_select->show_group_selector = TRUE;
		$user_select->show_orgchart_selector = TRUE;
		$user_select->show_orgchart_simple_selector = false;
		
		$page_title = getTitleArea($lang->def('_REPORT_SCHEDULE_TITLE')); //add beck url

		$user_select->setPageTitle($page_title);
		$user_select->addFormInfo(
			getBackUi($back_url, $lang->def('_BACK'), 'content').
			Form::getHidden('next_step', 'next_step', 'sched_setrecipients').
			Form::getHidden('is_updating', 'is_updating', 1));
		$user_select->loadSelector(str_replace('&', '&amp;', $jump_url), 
				false, 
				$error_info.$info_status.$lang->def('_CHOOSE_USER_FOR_SCHEDULATION'), 
				true, 
				true );
}


//time and period of schedulation
function schedule_set($idrep) {

    checkPerm('mod');

		$lang =& DoceboLanguage::createInstance('report', 'framework');

		//initialize session data for schedulation, if not updating
		if (!isset($_SESSION['schedule_tempdata'])) {
			$_SESSION['schedule_tempdata'] = array(
				'name' => '',
				'period' => 'day',
				'period_info' => '',
				'time' => '', //eliminate this
				'recipients' => array()
			);
		}
		
		$ref =& $_SESSION['schedule_tempdata'];

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
		$jump_url = 'index.php?modname=report&op=report_schedule&idrep='.$idrep;
		$back_url = 'index.php?modname=report&op=schedulelist&idrep='.$idrep;
		
		$body = Form::openForm('report_schedule_time', $jump_url);		
		
		$body .= //'nome filtro:<input type="text" name="filter_name" value="" />';
			Form::getTextfield( 
				$lang->def('_SAVE_SCHED_NAME'), //$label_name, 
				'sched_name',
				'sched_name',
				'200', $ref['name']).
			Form::getHidden('next_step','next_step','sched_setrecipients');;
		
					
		//create selections for crontab specification		
		$month_days = array();
		for ($i=1; $i<=31; $i++) {
			$month_days[$i] = $i; //TO DO : format with 2 digits filling with 0
		}
		
		$year_months = array();
		for ($i=1; $i<=12; $i++) {
			$year_months[$i] = $i; //TO DO : format with 2 digits filling with 0
		}
		
		$lang_days =& DoceboLanguage::createInstance('calendar', 'lms');
		$week_days = array(
			'0' => $lang_days->def('_MONDAY'),
			'1' => $lang_days->def('_TUESDAY'),
			'2' => $lang_days->def('_WEDNESDAY'),
			'3' => $lang_days->def('_THURSDAY'),
			'4' => $lang_days->def('_FRIDAY'),
			'5' => $lang_days->def('_SATURDAY'),
			'6' => $lang_days->def('_SUNDAY')
		);
		
		
		$body .=
			Form::getOpenFieldset($lang->def('_REPORT_SCHED_SETPERIOD')).
		
			/*'<div class="form_line_l">'.
			Form::getInputRadio('cron_radio_1', 'cron_radio', 'day', ($ref['period']=='day' ? true : false), '').
			'<label class="label_effect" for="cron_radio_1">'.$lang->def('_REPORT_DAILY').'</label>'.
			'</div>'.		
			
			'<div class="form_line_l">'.
			Form::getInputRadio('cron_radio_2', 'cron_radio', 'week', ($ref['period']=='week' ? true : false), '').
			'<label class="label_effect" for="cron_radio_2">'.$lang->def('_REPORT_WEEKLY').'</label>'.
			Form::getInputDropdown('', 'cron_weekly', 'cron_weekly', $week_days, ($ref['period']=='week' ? $ref['period_info'] : ''), '').
			'</div>'.
		
			'<div class="form_line_l">'.
			Form::getInputRadio('cron_radio_3', 'cron_radio', 'month', ($ref['period']=='month' ? true : false), '').
			'<label class="label_effect" for="cron_radio_3">'.$lang->def('_REPORT_MONTHLY').'</label>'.
			Form::getInputDropdown('', 'cron_monthly', 'cron_monthly', $month_days, ($ref['period']=='month' ? $ref['period_info'] : ''), '').
			'</div>'.*/
		
		
			Form::getRadio($lang->def('_REPORT_DAILY'), 'cron_radio_1', 'cron_radio', 'day', ($ref['period']=='day' ? true : false)).
			/*Form::getRadio(
				$lang->def('_REPORT_WEEKLY'), 
				'cron_radio_2',
				'cron_radio',
				'week', ($ref['period']=='week' ? true : false)
			).*/

			'<div class="form_line_l">'.
			Form::getInputRadio( 'cron_radio_2',
				'cron_radio',
				'week',
				($ref['period']=='week' ? true : false),
				'' ).
			' <label class="label_normal" for="cron_radio_2">'.$lang->def('_REPORT_WEEKLY').'</label> '.
			Form::getInputDropdown('', 'cron_weekly', 'cron_weekly', $week_days, ($ref['period']=='week' ? $ref['period_info'] : ''), '').
			'</div>'.

			'<div class="form_line_l">'.
			Form::getInputRadio( 'cron_radio_3',
				'cron_radio',
				'month',
				($ref['period']=='month' ? true : false),
				'' ).
			' <label class="label_normal" for="cron_radio_3">'.$lang->def('_REPORT_MONTHLY').'</label> '.
			Form::getInputDropdown('', 'cron_monthly', 'cron_monthly', $month_days, ($ref['period']=='month' ? $ref['period_info'] : ''), '').
			'</div>'.


			/*
			Form::getRadio(
				$lang->def('_REPORT_MONTHLY').Form::getInputDropdown('', 'cron_monthly', 'cron_monthly', $month_days, ($ref['period']=='month' ? $ref['period_info'] : ''), ''), 
				'cron_radio_3', 'cron_radio', 'month', ($ref['period']=='month' ? true : false)).
		*/
		
			Form::getHidden('idrep', 'idrep', $idrep).
		
			Form::getCloseFieldset();
			
		$body .=
			Form::openButtonSpace().
			
			Form::getButton('', 'schedule_confirm', $lang->def('_FORWARD')).
			Form::getButton('', 'schedule_undo', $lang->def('_UNDO')).
			
			form::closeButtonSpace();
	
		$body .= Form::closeForm();
	
		//output content
		cout(getTitleArea($lang->def('_REPORT_SCHEDULE_TITLE')));
		cout('<div class="std_block">'.getInfoUi(get_action_status($idrep)));
	
		cout($body);
		
		cout('</div>'); //close std_block div
	
}




function modify_schedulation() {
  checkPerm('mod');
	//preload schedulation data in session
	require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
	
	if ($id_sched = get_req('id_sched', DOTY_INT, false)) {
		$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_report_schedule=$id_sched";
		$row = mysql_fetch_assoc( mysql_query($qry) );
		
		$recipients = array();
		$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient WHERE id_report_schedule=$id_sched";
		$recs = mysql_query($qry);
		while ($trow = mysql_fetch_assoc($recs)) {
			$recipients[] = $trow['id_user'];
		}
		
		$period = explode(',', $row['period']);
		$_SESSION['schedule_update'] = $id_sched; //integer value, <>0 and <>false
		$_SESSION['schedule_tempdata'] = array(
			'name' => $row['name'],
			'period' => $period[0],
			'period_info' => $period[1],
			'time' => '', //eliminate this
			'recipients' => $recipients
		);
		
		$rid = $row['id_report_filter'];
		$_SESSION['report_saved'] = true;
		$_SESSION['report_saved_data'] = array('id'=>$rid, 'name'=>getReportNameById($rid));
	
		schedule_report(get_req('idrep', DOTY_INT, false));
	} else {
		jumpTo('index.php?modname=report&op=schedulelist'); //if error jump to start page
	}
}



function schedule_report() {
	$idrep = get_req('idrep', DOTY_INT, false);
	$step = get_req('next_step', DOTY_STRING, '');
	
	if ($step=='sched_setrecipients') {
		schedule_recipients($idrep);
	} else {
		schedule_set($idrep);
	}
}





define('_SCHED_KEY_NAME',     'name');
define('_SCHED_KEY_CREATOR',  'creator');
define('_SCHED_KEY_CREATION', 'creation');
define('_SCHED_KEY_REPORT',   'report');
define('_SCHED_KEY_PERIOD',   'period');
define('_SCHED_KEY_NUMUSER',  'numuser');
define('_SCHED_KEY_ENABLED',  'enabled');
define('_SCHED_KEY_MOD',      '_modify');
define('_SCHED_KEY_REM',      '_remove');


function get_period_text($period) {
	$output = '';
	
	$lang =& DoceboLanguage::createInstance('report', 'framework');
	$texts = array(
		'day'   => $lang->def('_REPORT_DAILY'),
		'week'  => $lang->def('_SCHED_TEXT_WEEK'),
		'month' => $lang->def('_REPORT_MONTHLY')
	);
	
	$lang_days =& DoceboLanguage::createInstance('calendar', 'lms');
	$week_days = array(
		'0' => $lang_days->def('_MONDAY'),
		'1' => $lang_days->def('_TUESDAY'),
		'2' => $lang_days->def('_WEDNESDAY'),
		'3' => $lang_days->def('_THURSDAY'),
		'4' => $lang_days->def('_FRIDAY'),
		'5' => $lang_days->def('_SATURDAY'),
		'6' => $lang_days->def('_SUNDAY')
	);
	
	$parts = explode(',', $period);
	
	$output .= $texts[ $parts[0] ];

	if ($parts[0]=='week') $output .= ' '.strtolower($week_days[ $parts[1] ]);
	if ($parts[0]=='month') $output .= ' '.$parts[1];
	
	return $output;
}

//create box for operations on schedulations
function get_schedulations_table($idrep=false) {

  checkPerm('view');
  $can_mod = checkPerm('mod', true);

	require_once($GLOBALS['where_framework'].'/lib/lib.table.php');
	//$query = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule as t1 WHERE 1=1 ";
	
	$acl_man =& $GLOBALS['current_user']->getACLManager();
	$level = $GLOBALS['current_user']->getUserLevelId(getLogUserId());
	
	$admin_cond = '';
	switch ($level) {
		case ADMIN_GROUP_GODADMIN :;
		case ADMIN_GROUP_ADMIN : break;
		case ADMIN_GROUP_PUBLICADMIN :;
		case ADMIN_GROUP_USER :; 
		default : $admin_cond .= " AND t1.id_creator=".getLogUserId(); break;
	}
	
	$query = "SELECT t1.*, t3.userid as report_owner, t2.filter_name as report_name, ".
		"COUNT(t4.id_user) as num_users FROM ".
		$GLOBALS['prefix_lms']."_report_schedule as t1, ".
		$GLOBALS['prefix_lms']."_report_filter as t2, ".$GLOBALS['prefix_fw']."_user as t3, ".
		$GLOBALS['prefix_lms']."_report_schedule_recipient as t4 ".
		"WHERE t1.id_report_filter=t2.id_filter AND t3.idst=t1.id_creator ".
		"AND t4.id_report_schedule=t1.id_report_schedule ".$admin_cond." ".
		($idrep ? "AND t1.id_report_filter=$idrep " : '').
		"GROUP BY t1.id_report_schedule";
	
	$lang =& DoceboLanguage::createInstance('report', 'framework');
	$output = '';	
	
	$tb = new Table($GLOBALS['lms']['visu_course']);
	$tb->initNavBar('ini', 'button');
	$col_type = array('align_center','align_center','align_center','align_center','align_center','align_center');//,'image','image');
	$col_content = array(
		$lang->def('_NAME'),
		$lang->def('_TAB_REP_CREATOR'),
		$lang->def('_CREATION_DATE'),
		$lang->def('_TAB_SCHED_PERIOD'),
		$lang->def('_TAB_SCHED_NUMUSER'),
		$lang->def('_ACTIVE')/*,
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_ALT_SCHED_MOD', 'standard').'" title="'.$lang->def('_SCHED_TITLE_MOD').'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_ALT_SCHED_DEL', 'standard').'" title="'.$lang->def('_SCHED_TITLE_REM').'" />'*/
	);
	
	if ($can_mod) {
    $col_type[] = 'image';
    $col_type[] = 'image';
    $col_content[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD', 'standard').'" title="'.$lang->def('_SCHED_TITLE_MOD').'" />';
    $col_content[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL', 'standard').'" title="'.$lang->def('_SCHED_TITLE_REM').'" />';
  }
	
	$tb->setColsStyle($col_type);
	$tb->addHead($col_content);
	
	if ($res = mysql_query($query)) {
		while ($row = mysql_fetch_assoc($res)) {
			$id = $row['id_report_schedule'];
			$mod_link =
				'<a href="index.php?modname=report&amp;op=sched_mod&amp;id_sched='.$id.'&amp;idrep='.$idrep.'" '.
				' title="'.$lang->def('_SCHED_TITLE_MOD').'">'.
				'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" />'.
				'</a>';
			$rem_link =
				'<a href="index.php?modname=report&amp;op=schedulelist&amp;idrep='.$idrep.'&amp;action=sched_rem&amp;id_sched='.$id.'" '.
				' title="'.$lang->def('_SCHED_TITLE_REM').'">'.
				'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" />'.
				'</a>';
			$enabled = 
				/*'<input type="checkbox" value="'.$id.'" '.
				($row['enabled']==1 ? 'checked="checked "' : '').
				'onchange="enable_schedulation(this);" />';*/
				'<image class="handover" src="'.getPathImage('lms').'webpages/'.
				($row['enabled']==1 ? '' : 'un').'publish.gif'.'" '.
				'onclick="enable_schedulation(this, '.$row['id_report_schedule'].');" />'.
				'<input type="hidden" id="enable_value_'.$row['id_report_schedule'].'" '.
				'value="'.($row['enabled']==1 ? '0' : '1').'" />';
			$num_users = '<a href="#" title="'.$lang->def('_NUMUSERS_TITLE').'" '.
				'class="" onclick="show_recipients('.$row['id_report_schedule'].');">'.
				$row['num_users'].'</a>';
			$tb_content = array(
				_SCHED_KEY_NAME     => $row['name'],
				_SCHED_KEY_CREATOR  => $acl_man->relativeId($row['report_owner']),
				_SCHED_KEY_CREATION => $GLOBALS['regset']->databaseToRegional($row['creation_date']),
				_SCHED_KEY_PERIOD   => get_period_text($row['period']),
				_SCHED_KEY_NUMUSER  => $num_users,
				_SCHED_KEY_ENABLED  => $enabled/*,
				_SCHED_KEY_MOD      => $mod_link,
				_SCHED_KEY_REM      => $rem_link*/
			);
			
			if ($can_mod) {
        $tb_content[_SCHED_KEY_MOD] = $mod_link;
        $tb_content[_SCHED_KEY_REM] = $rem_link;
      }
			
			$tb->addBody($tb_content);		
		}	
	}	
	
	$tb->addActionAdd('
		<a href="index.php?modname=report&amp;op=report_schedule&amp;idrep='.$idrep.'">'.
  	'<img src="'.getPathImage().'standard/add.gif" '.
		'title="'.$lang->def('_NEWSCHEDULE_TITLE').'" /> '.
  	$lang->def('_NEWSCHEDULE_TITLE').'</a>');
	
	$output .= $tb->getTable();
	
	return $output;
}
?>