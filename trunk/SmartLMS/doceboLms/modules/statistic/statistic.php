<?php
/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function outPageView($link) {
	
	$lang =& DoceboLanguage::createInstance('statistic', 'lms');
	$for 	= importVar('for', false, 'week');
	$times 	= array('day', 'week', 'month', 'year');
	
	$dateend = date("Y-m-d H:i:s");
	$walk = array();
	switch($for) {
		case 'day' : {
			
			$dateinit = date("Y-m-d H:i:s", time() - 24*3600);
			$start_num = substr($dateinit, 11, 2);
			$colums = 24;
			$select = " HOUR(timeof) AS from_time ";
			$group_by = " HOUR(timeof) ";
			
			for($i = 1; $i <= $colums; $i++) {
				
				$walk[] = (($i + $start_num) % $colums);
			}
		};break;
		case 'week' : {
			
			$dateinit = date("Y-m-d H:i:s", time() - 7*24*3600);
			$start_num = date("w", time() - 7*24*3600);
			$colums = 7;
			$select = " DAYOFWEEK(timeof) AS from_time ";
			$group_by = " DAYOFMONTH(timeof) ";
			for($i = 1; $i <= $colums; $i++) {
				
				$walk[] = (($start_num + $i) % $colums) + 1;
				$walk_name[] = $lang->def('_WEEK_DAY_'.(($start_num + $i) % $colums).'_SHORT');
			}
		};break;
		case 'month' : {
			$dateinit = date("Y-m-d H:i:s", time() - 30*24*3600);
			$start_num = substr($dateinit, 8, 2);
			$colums = 31;
			$limit = cal_days_in_month(CAL_GREGORIAN, (substr($dateinit, 5, 2) - 1), substr($dateinit, 0, 4));
			$select = " DAYOFMONTH(timeof) AS from_time ";
			$group_by = " DAYOFMONTH(timeof) ";
			for($i = 1; $i <= $colums; $i++) {
				
				$c = (($start_num + $i) % $limit);
				if($c == 0) $c = $limit;
				$walk[] = $c;
			}
		};break;
		case 'year' : {
			$dateinit = date("Y-m-d H:i:s", time() - 365*24*3600);
			$start_num = substr($dateinit, 5, 2) - 1;
			$colums = 12;
			$select = " MONTH(timeof) AS from_time ";
			$group_by = " MONTH(timeof) ";
			for($i = 1; $i <= $colums; $i++) {
				
				$c = (($start_num + $i) % $colums);
				if($c == 0) $c = $colums;
				$walk[] = $c;
			}
		};break;
	}
	$page_views = array();
	$query_stat = "
	SELECT ".$select.", COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_trackingeneral 
	WHERE idCourse='".$_SESSION['idCourse']."' "
		."AND timeof >= '$dateinit' AND timeof <= '$dateend' 
	GROUP BY ".$group_by."
	ORDER BY timeof";
	$max = 0;
	$re_stat = mysql_query($query_stat);
	while(list($col, $number) = mysql_fetch_row($re_stat)) {
		
		$page_views[$col] = $number;
		if($number > $max) $max = $number;
	}
	$GLOBALS['page']->add(
		'<div class="container_graphic">'."\n"
		.'<table cellspacing="0">'
		.'<tr class="colum_isto">', 'content');
	
	$left_space = 0;
	$out_index = '';
	$width = round(100 / $colums, 1);
	while(list($i, $number) = each($walk)) {
		
		$value = ( isset($page_views[$number]) ? $page_views[$number] : 0 );
		$GLOBALS['page']->add(
			'<td style="width: '.$width.'%;">'
				.( $value != 0 ? $value.'<div class="colored_isto" style="height: '.((150 / $max) * $value).'px;">&nbsp;</div>' : '' )
			.'</td>', 'content');
		$out_index .= '<td>'.( isset($walk_name[$i]) ? $walk_name[$i] : $number ).'</td>';
	}	
	$GLOBALS['page']->add(
		'</tr>'
		.'<tr class="colum_index" scope="col">'.$out_index.'</tr>'
		.'</table>'
		.'</div>'."\n"
		.'<div class="align_center">'
		.'<ul class="adjac_link">', 'content');
	while(list(, $value) = each($times)) {
		
		if($for == $value) {
			$GLOBALS['page']->add('<li><span class="statsistic_for_selected">'.$lang->def('_FOR_'.$value).'</span></li>', 'content');
		} else {
			$GLOBALS['page']->add('<li><a href="'.$link.'&amp;for='.$value.'" '
				.'title="'.$lang->def('_FOR_TITLE_'.$value).'">'.$lang->def('_FOR_'.$value).'</a></li>', 'content');
		}
	}
	$GLOBALS['page']->add('</ul>'
		.'</div>', 'content');
}

function statistic() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
	$lang =& DoceboLanguage::createInstance('statistic', 'lms');
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$course_man 	= new Man_Course();
	$course_user 	= $course_man->getIdUserOfLevel($_SESSION['idCourse']);
	$users_list 	=& $acl_man->getUsers($course_user);
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_STATISTIC'), 'statistic')
		.'<div class="std_block">', 'content');
		
	if($GLOBALS['lms']['tracking'] == 'on') {
		
		$GLOBALS['page']->add('<div class="title">'.$lang->def('_PAGE_VIEW').'</div>', 'content');
		outPageView('index.php?modname=statistic&amp;op=statistic');
	}
	$GLOBALS['page']->add(
		'<br />'
		.'<div class="title">'.$lang->def('_USER_TRACK_DETAILS').'</div>', 'content');
	$tb = new TypeOne(0, $lang->def('_USERS_LIST_CAPTION'), $lang->def('_USERS_LIST_SUMMARY'));
	
	$type_h = array('', '', '');
	$cont_h = array(
		$lang->def('_USERNAME'), 
		$lang->def('_LASTNAME'), 
		$lang->def('_FIRSTNAME')
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list(, $user_info) = each($users_list)) {
		
		$cont = array(
			'<a href="index.php?modname=statistic&amp;op=userdetails&amp;id='.$user_info[ACL_INFO_IDST].'" '
				.'title="'.$lang->def('_VIEW_DETAILS').' : '.$acl_man->relativeId($user_info[ACL_INFO_USERID]).'">'
				.$acl_man->relativeId($user_info[ACL_INFO_USERID]).'</a>',
			$user_info[ACL_INFO_LASTNAME], 
			$user_info[ACL_INFO_FIRSTNAME]
		);
		$tb->addBody($cont);
	}
	$GLOBALS['page']->add(
		$tb->getTable()
		.'</div>', 'content');
	
}

function userdetails() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$idst_user 	= importVar('id', true, 0);
	$ord		= importVar('ord');
	$inv		= importVar('inv', true, 0);
	$link = 'index.php?modname=statistic&amp;op=userdetails&amp;id='.$idst_user.'';
	
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'link');
	$nav_bar->setLink($link.'&amp;ord='.$ord.'&amp;inv='.$inv);
	if(!isset($_GET['p_ini'])) $ini = $nav_bar->getSelectedElement();
	else $ini = $_GET['p_ini'];
	
	$lang =& DoceboLanguage::createInstance('statistic', 'lms');
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$user_info		=& $acl_man->getUser($idst_user, false);
	
	$page_title = array(
		'index.php?modname=statistic&amp;op=statistic' => $lang->def('_STATISTIC'),
		( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
			? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
			: $acl_man->relativeId($user_info[ACL_INFO_USERID]) )
	);
	
	// Find modulename -> name int his course
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	$course_man = new Man_Course();
	$mods_names =& $course_man->getModulesName($_SESSION['idCourse']);
	
	// find total time in the course
	$query_time = "
	SELECT SUM((UNIX_TIMESTAMP(lastTime) - UNIX_TIMESTAMP(enterTime)))
	FROM ".$GLOBALS['prefix_lms']."_tracksession 
	WHERE idCourse = '".(int)$_SESSION['idCourse']."' AND idUser = '".$idst_user."'";
	list($tot_time) = mysql_fetch_row(mysql_query($query_time));
	
	$query_track = "
	SELECT idEnter, enterTime, lastTime, (UNIX_TIMESTAMP(lastTime) - UNIX_TIMESTAMP(enterTime)) AS howm, 
		numOp, lastFunction, lastOp, session_id 
	FROM ".$GLOBALS['prefix_lms']."_tracksession 
	WHERE idCourse = '".(int)$_SESSION['idCourse']."' AND idUser = '".$idst_user."'
	ORDER BY ";
	
	$img_down = '<img src="'.getPathImage().'standard/ord_asc.gif" alt="'.$lang->def("_ORD_ASC_ALT").'" '
		.'title="'.$lang->def("_ORD_ASC_TITLE").'" />';
	$img_up = '<img src="'.getPathImage().'standard/ord_desc.gif" alt="'.$lang->def("_ORD_DESC_ALT").'" '
		.'title="'.$lang->def("_ORD_DESC_TITLE").'" />';
	$image_hm = $image_nop = $image_sst = '';
	switch($ord) {
		case "hm" : {
			$query_track .= " howm ".( $inv ? '  ' : ' DESC ' );
			$order_for = $lang->def('_HOW_MUCH_TIME');
			$image_hm 	= ( $inv ? $img_down : $img_up );
		};break;
		case "nop" : {
			$query_track .= " numOp ".( $inv ? '  ' : 'DESC' );
			$order_for = $lang->def('_NUMBER_OF_OP');
			$image_nop 	= ( $inv ? $img_down : $img_up );
		};break;
		default : {
			$query_track .= " enterTime ".( $inv ? ' DESC ' : '' );
			$order_for = $lang->def('_SESSION_STARTED');
			$image_sst 	= ( $inv ? $img_down : $img_up );
		};break;
	}
	$query_track .= " LIMIT ".$ini.", ".$GLOBALS['lms']['visuItem'];
	$re_tracks = mysql_query($query_track);
	
	$query_tot_track = "
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_tracksession 
	WHERE idCourse = '".(int)$_SESSION['idCourse']."' AND idUser = '".$idst_user."'";
	list($tot_elem) = mysql_fetch_row(mysql_query($query_tot_track));
	$nav_bar->setElementTotal($tot_elem);
	
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'statistic')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=statistic&amp;op=statistic', $lang->def('_BACK')), 'content');
		
	$tb = new TypeOne(0, $lang->def('_USERS_LIST_DETAILS_CAPTION'), $lang->def('_USERS_LIST_DETAILS_SUMMARY'));
	
	$type_h = array('', '', 'align_center', 'align_center', '');
	$cont_h = array(
		'<a href="'.$link.'&amp;ord=sst&amp;inv='.( $ord == 'sst' && $inv ? '0' : '1' ).'" title="'.$lang->def('_ORD_FOR_SST').'">'
			.$image_sst.' '.$lang->def('_SESSION_STARTED').'</a>', 
		$lang->def('_LAST_ACTION_AT'), 
		'<a href="'.$link.'&amp;ord=hm&amp;inv='.( $ord == 'hm' && $inv ? '0' : '1' ).'" title="'.$lang->def('_ORD_FOR_HM').'">'
			.$image_hm.' '.$lang->def('_HOW_MUCH_TIME').'</a>', 
		'<a href="'.$link.'&amp;ord=nop&amp;inv='.( $ord == 'nop' && $inv ? '0' : '1' ).'" title="'.$lang->def('_ORD_FOR_NOP').'">'
			.$image_nop.' '.$lang->def('_NUMBER_OF_OP').'</a>',  
		$lang->def('_LAST_OP')
	);
	if($GLOBALS['lms']['tracking'] == 'on') {
		$cont_h[] = '<img src="'.getPathImage().'standard/viewmag.gif" title="'.$lang->def('_VIEW_SESSION_DETAILS_TITLE').'" '
			.'alt="'.$lang->def('_VIEW_SESSION_DETAILS_ALT').'" />';
		$type_h[] = 'image';
	};
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	$type_h[2] = 'align_right';
	$tb->setColsStyle($type_h);
	$total_sec = 0; 
	while(list($id_enter, $session_start_at, $last_action_at, $how, $num_op, $last_module, $last_op, $session_id) = mysql_fetch_row($re_tracks)) {
		
		$hours = (int)($how/3600);
		$minutes = (int)(($how%3600)/60);
		$seconds = (int)($how%60);
		if($minutes < 10) $minutes = '0'.$minutes;
		if($seconds < 10) $seconds = '0'.$seconds;
		
		$readable = $hours.'h '.$minutes.'m '.$seconds.'s ';
		$start = $GLOBALS['regset']->databaseToRegional($session_start_at, false, true);
		$cont = array(
			$start,
			$GLOBALS['regset']->databaseToRegional($last_action_at, false, true),
			$readable, 
			$num_op,
			'<span class="text_bold">'.( isset($mods_names[$last_module]) ? $mods_names[$last_module] : $last_module ).'</span> ['.$last_op.']' 
		);
		if($GLOBALS['lms']['tracking'] == 'on') {
			$cont[] = '<a href="index.php?modname=statistic&amp;op=sessiondetails&amp;id='.$idst_user.'&amp;id_enter='.$id_enter.'&amp;p_ini='.$ini
					.'&amp;sid='.$session_id.'" '
				.'title="'.$lang->def('_VIEW_SESSION_DETAILS_TITLE').' : '.$start.'">'
				.'<img src="'.getPathImage().'standard/viewmag.gif" alt="'.$lang->def('_VIEW_SESSION_DETAILS_ALT').' : '.$start.'" /></a>';
		};
		$tb->addBody($cont);
		
	}
	
	$hours = (int)($tot_time/3600);
	$minutes = (int)(($tot_time%3600)/60);
	$seconds = (int)($tot_time%60);
	if($minutes < 10) $minutes = '0'.$minutes;
	if($seconds < 10) $seconds = '0'.$seconds;
	
	$GLOBALS['page']->add(
		'<div>'
		.'<span class="text_bold">'.$lang->def('_USER_TOTAL_TIME').' : </span>'.$hours.'h '.$minutes.'m '.$seconds.'s '
		.'<br />'
		.'<span class="text_bold">'.$lang->def('_RESULT_ORDER_FOR').' : </span>'.$order_for
		.'</div>'
		
		.$tb->getTable()
		.$nav_bar->getNavBar($ini)
		.getBackUi('index.php?modname=statistic&amp;op=statistic', $lang->def('_BACK'))
		.'</div>', 'content');
}

function sessiondetails() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$idst_user 	= importVar('id', true, 0);
	$id_enter 	= importVar('id_enter', true, 0);
	$p_ini 	= importVar('p_ini');
	$link = 'index.php?modname=statistic&amp;op=sessiondetails&amp;id='.$idst_user.'&amp;id_enter='.$id_enter;
	
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'link');
	$nav_bar->setLink($link.'&amp;p_ini='.$p_ini);
	$ini = $nav_bar->getSelectedElement();
	
	$lang =& DoceboLanguage::createInstance('statistic', 'lms');
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$user_info		=& $acl_man->getUser($idst_user, false);
	
	$query_track = "
	SELECT g.function, g.type, g.timeof, UNIX_TIMESTAMP(g.timeof) AS unix_time 
	FROM ".$GLOBALS['prefix_lms']."_trackingeneral AS g
	WHERE g.idCourse = '".(int)$_SESSION['idCourse']."' AND g.idUser = '".$idst_user."' AND " .
			" ( g.idEnter = '".$id_enter."' OR (  g.idEnter = 0 AND g.session_id = '".importVar('sid')."' ) ) "
	." ORDER BY g.timeof 
	LIMIT ".$ini.", ".$GLOBALS['lms']['visuItem'];
	$re_tracks = mysql_query($query_track);
	
	$query_tot_track = "
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_trackingeneral 
	WHERE idCourse = '".(int)$_SESSION['idCourse']."' AND idUser = '".$idst_user."' AND idEnter = '".$id_enter."'";
	list($tot_elem) = mysql_fetch_row(mysql_query($query_tot_track));
	$nav_bar->setElementTotal($tot_elem);
	
	// Find modulename -> name int his course
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	$course_man = new Man_Course();
	$mods_names =& $course_man->getModulesName($_SESSION['idCourse']);
	
	$page_title = array(
		'index.php?modname=statistic&amp;op=statistic' => $lang->def('_STATISTIC'),
		'index.php?modname=statistic&amp;op=userdetails&amp;id='.$idst_user.'&amp;p_ini='.$p_ini => ( 
			$user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
				? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
				: $acl_man->relativeId($user_info[ACL_INFO_USERID]) ),
		$lang->def('_VIEW_SESSION_DETAILS_TITLE')
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'statistic')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=statistic&amp;op=userdetails&amp;id='.$idst_user.'&amp;p_ini='.$p_ini, $lang->def('_BACK')), 'content');
		
	$tb = new TypeOne(0, $lang->def('_SESSION_DETAILS_CAPTION'), $lang->def('_SESSION_DETAILS_SUMMARY'));
	
	$type_h = array('', '', '');
	$cont_h = array(
		$lang->def('_TYPE_OF_OPERATION'), 
		$lang->def('_DATE'), 
		$lang->def('_TIME_IN'), 
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	$type_h[2] = 'align_right';
	$tb->setColsStyle($type_h);
	$total_sec = 0;
	$read_previous = false;
	while($read = mysql_fetch_assoc($re_tracks)) {
		
		if($read_previous !== false) {
			
			$time_in = $read['unix_time'] - $read_previous['unix_time']; 
			$hours = (int)($time_in/3600);
			$minutes = (int)(($time_in%3600)/60);
			$seconds = (int)($time_in%60);
			if($minutes < 10) $minutes = '0'.$minutes;
			if($seconds < 10) $seconds = '0'.$seconds;
			
			$readable = $hours.'h '.$minutes.'m '.$seconds.'s ';
			$cont = array(
				$GLOBALS['regset']->databaseToRegional($read_previous['timeof'], false, true),
				'<span class="text_bold">'.( isset($mods_names[$read_previous['function']]) ? $mods_names[$read_previous['function']] : $read_previous['function'] )
					.'</span> ['.$read_previous['type'].']', 
				$readable
			);
			$tb->addBody($cont);
		}
		$read_previous = $read;
	}
	$query_last_track = "
	SELECT g.function, g.type, g.timeof, UNIX_TIMESTAMP(g.timeof) AS unix_time 
	FROM ".$GLOBALS['prefix_lms']."_trackingeneral AS g
	WHERE g.idCourse = '".(int)$_SESSION['idCourse']."' AND g.idUser = '".$idst_user."' AND g.idEnter = '".$id_enter."' 
	LIMIT ".($ini + $GLOBALS['lms']['visuItem']).", 1";
	$re_track = mysql_query($query_last_track);
	if(mysql_num_rows($re_track) > 0) {
		
		$read =  mysql_fetch_assoc($re_track);
		$time_in = $read['unix_time'] - $read_previous['unix_time']; 
		$hours = (int)($time_in/3600);
		$minutes = (int)(($time_in%3600)/60);
		$seconds = (int)($time_in%60);
		if($minutes < 10) $minutes = '0'.$minutes;
		if($seconds < 10) $seconds = '0'.$seconds;
		
		$readable = $hours.'h '.$minutes.'m '.$seconds.'s ';
	} else $readable = '';
	
	$cont = array(
		$GLOBALS['regset']->databaseToRegional($read_previous['timeof']),
		'<span class="text_bold">'.( isset($mods_names[$read_previous['function']]) ? $mods_names[$read_previous['function']] : $read_previous['function'] )
			.'</span> ['.$read_previous['type'].']', 
		$readable
	);
	$tb->addBody($cont);
	$GLOBALS['page']->add(
		$tb->getTable()
		.$nav_bar->getNavBar()
		.getBackUi('index.php?modname=statistic&amp;op=userdetails&amp;id='.$idst_user.'&amp;p_ini='.$p_ini, $lang->def('_BACK'))
		.'</div>', 'content');
}

function statisticDispatch($op) {
	
	switch($op) {
		case "statistic" : {
			statistic();
		};break;
		case "userdetails" : {
			userdetails();
		};break;
		case "sessiondetails" : {
			sessiondetails();
		};break;
	}
}


?>