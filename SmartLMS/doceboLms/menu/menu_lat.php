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

if(!$GLOBALS['current_user']->isAnonymous()) {
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
	
	addCss('style_menu');
	addJs($GLOBALS['where_lms_relative'].'/lib/', 'lib.menu_lat.js');
	
	$lang 		= DoceboLanguage::createInstance('course');
	$mo_lang 	= DoceboLanguage::createInstance('menu_over');
	$ml_lang 	= DoceboLanguage::createInstance('menu_course');
	
	$id_main_sel 	= importVar('id_main_sel', true, 0);
	$id_module_sel 	= importVar('id_module_sel', true, 0);
	
	if(isset($_GET['id_main_sel'])) 	$_SESSION['current_main_menu'] = $id_main_sel;
	if(isset($_GET['id_module_sel'])) 	$_SESSION['sel_module_id'] = $id_module_sel;
	
	$GLOBALS['page']->add('<li><a href="#menu_lat">'.$mo_lang->def('_BLIND_MENU_LAT').'</a></li>', 'blind_navigation');
	
	// retrive all the module ----------------------------------------
	$query_menu = "
	SELECT mo.idModule, mo.module_name, mo.default_op, mo.default_name, mo.token_associated, 
		under.idMain, under.my_name 
	FROM ".$GLOBALS['prefix_lms']."_module AS mo 
		JOIN ".$GLOBALS['prefix_lms']."_menucourse_under AS under
	WHERE mo.idModule = under.idModule 
		AND  under.idCourse = '".$_SESSION ['idCourse']."' 
	ORDER BY under.idMain, under.sequence ";
	$re_menu_voice = mysql_query($query_menu);
	
	$counter = 0;
	$menu_module = array();
	while(list($id_m, $module_name, $def_op, $default_name, $token, $id_main, $my_name) = mysql_fetch_row($re_menu_voice)) {
		
		// checkmodule module
		if(checkPerm($token, true, $module_name)) {
			
			$GLOBALS['module_assigned_name'][$module_name] = ( $my_name != '' ? $my_name : $ml_lang->def($default_name) );
			
			if(!isset($menu_module[$id_main])) $menu_module[$id_main] = '';
			$menu_module[$id_main] .= '<li'.(isset($_SESSION['sel_module_id']) && $id_m == $_SESSION['sel_module_id'] ? ' class="selected"' : '' ).'>'
						.'<a href="index.php?modname='.$module_name.'&amp;op='.$def_op.'&amp;id_module_sel='.$id_m.'&amp;id_main_sel='.$id_main.'">'
						.$GLOBALS['module_assigned_name'][$module_name].'</a>'
						.'</li>';
			$counter++;
		} // end if checkPerm
		
	} // end while
	
	// recover main menu --------------------------------------------------------------------------------
	$id_list = array();
	$query = "
	SELECT idMain, name
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE  idCourse = '".$_SESSION ['idCourse']."' 
	ORDER BY sequence ";
	$re_main = mysql_query($query);
	
	$GLOBALS['page']->add(
		'<div id="menu_lat" class="menu_box">'."\n"
		.'<ul class="lat_menu_main">'
	, 'menu');
	while(list($id_main, $name) = mysql_fetch_row($re_main)) {
		
		if(isset($menu_module[$id_main])) {
			
			$GLOBALS['page']->add(
					'<li '.( $_SESSION['current_main_menu'] == $id_main ? ' class="lat_menu_open"' : '' ).'>'
					.'<a href="index.php?id_module_sel=0&amp;id_main_sel='.$id_main.'" 
							onclick=" expand_menu(\'menu_lat_'.$id_main.'\', \'lat_menu_close\', \'lat_menu_open\'); return false;">'
					.( $mo_lang->isDef($name) ? $mo_lang->def($name) : $name ).'</a>'
					
					.'<ul id="menu_lat_'.$id_main.'" style="'.( $_SESSION['current_main_menu'] == $id_main ? 'display: block;' : 'display: none;' ).'">'
						.$menu_module[$id_main]
					.'</ul>'
					
					.'</li>', 'menu');
			$id_list[] = '"menu_lat_'.$id_main.'"';
		}
		
	}
	// close menu ---------------------------------------------------------------------
	$GLOBALS['page']->add(
		'</ul>'
		.'</div>', 'menu');
	if(!empty($id_list)) {
		
		$GLOBALS['page']->add('<script type="text/javascript">'			."\n"
			.'	var list = new Array('.implode(',', $id_list).');'		."\n"
			.'	setMenuList(list);'										."\n"
			.'</script>', 'footer');
	}
	
	// panel with user/course info -----------------------------------------------------
	
	$info_panel = '';
	if(isset($_SESSION['idCourse'])) {
		
		$GLOBALS['page']->add('<li><a href="#your_info">'.$mo_lang->def('_BLIND_YOUR_INFO').'</a></li>', 'blind_navigation');
	
		$userid 		= $GLOBALS['current_user']->getUserId();
		$user_name 		= $GLOBALS['current_user']->getUserName();
		$course_name 	= $GLOBALS['course_descriptor']->getValue('name');
		$sponsor_link 	= $GLOBALS['course_descriptor']->getValue('linkSponsor');
		$sponsor_img 	= $GLOBALS['course_descriptor']->getValue('imgSponsor');
		$course_img 	= $GLOBALS['course_descriptor']->getValue('img_course');
		
		$info_panel .= '<div class="course_descr_box">'."\n";

		if($course_img != '') {
			
			$path = $GLOBALS['where_files_relative'].'/doceboLms/'.$GLOBALS['lms']['pathcourse'];
			$info_panel .= '<p class="align_center">'
				.'<img class="course_logo" src="'.$path.$course_img.'" alt="'.$lang->def('_COURSE_LOGO').' : '.$course_name.'" />'
				.'</p>'."\n";
		}
		
		// welcome user_name
		$info_panel .= '<div id="your_info" class="course_user_name">'
			.'<span>'.$lang->def('_WELCOME').' : </span><b>'.$user_name.'</b>'
			.'</div>';
		
		// welcome in_course
		$info_panel .= '<div class="course_user_name">'
				.'<span>'.$lang->def('_IN_COURSE').' : </span><b>'.$course_name.'</b>'
				.'</div>'."\n";
		
		
		$user_stats = array('head'=>array(),'body'=>array());
		if(!isset($_SESSION['is_ghost']) || $_SESSION['is_ghost'] !== true) {
			
			if($GLOBALS['course_descriptor']->getValue('show_time') == 1) {
				
				$tot_time_sec 		= TrackUser::getUserPreviousSessionCourseTime(getLogUserId(), $_SESSION['idCourse']);
				$partial_time_sec 	= TrackUser::getUserCurrentSessionCourseTime($_SESSION['idCourse']);
				$tot_time_sec  		+= $partial_time_sec;
				
				$hours 		= (int)($partial_time_sec / 3600);
				$minutes 	= (int)(($partial_time_sec % 3600) / 60);
				$seconds 	= (int)($partial_time_sec % 60);
				if($minutes < 10) $minutes = '0'.$minutes;
				if($seconds < 10) $seconds = '0'.$seconds;
				$partial_time = ( $hours != 0 ? $hours.'h ' : '' ).$minutes.'m ';//.$seconds.'s ';
				
				$hours 		= (int)($tot_time_sec/3600);
				$minutes 	= (int)(($tot_time_sec%3600)/60);
				$seconds 	= (int)($tot_time_sec%60);
				if($minutes < 10) $minutes = '0'.$minutes;
				if($seconds < 10) $seconds = '0'.$seconds;
				$tot_time = ( $hours != 0 ? $hours.'h ' : '' ).$minutes.'m ';//.$seconds.'s ';
				
				addJs($GLOBALS['where_lms_relative'].'/lib/','lib.track_user.js');
				$GLOBALS['page']->add(
					'<script type="text/javascript">'
					.'	userCounterStart('.(int)$partial_time_sec.', '.(int)$tot_time_sec.');'
					.'</script>'."\n"
				, 'page_head');
				
				
				$user_stats['head'][0] = $lang->def('_PARTIAL_TIME');
				$user_stats['body'][0] = '<span id="partial_time">'.$partial_time.'</span>';
					
				$user_stats['head'][1] = $lang->def('_TOT_TIME');
				$user_stats['body'][1] = '<span id="total_time">'.$tot_time.'</span>';
				
			}
			
		}
		
		// who is online ---------------------------------------------------------

		if($GLOBALS['course_descriptor']->getValue('show_who_online') == _SHOW_INSTMSG) {
			
			addCss('instmsg');
			addYahooJs(
				array(
					'yahoo'      => 'yahoo-min.js',
					'event'      => 'event-min.js',
					'connection' => 'connection-min.js'
				)
			);
			addJs($GLOBALS['where_lms_relative'].'/modules/instmsg/','instmsg.js');

			$GLOBALS['page']->add(
				'<script type="text/javascript">'
				." setup_instmsg( '".$GLOBALS['current_user']->getIdSt()."', "
				."'".$userid."', "
				."'".getPathImage('fw')."' ); "
				.'</script>'."\n", 'page_head');
			
			$user_stats['head'][2] = $lang->def('_WHOIS_ONLINE');
			$user_stats['body'][2] = '<b id="user_online_n">'
				.'<a id="open_users_list" href="javascript:void(0)">'
					.TrackUser::getWhoIsOnline($_SESSION['idCourse'])
				.'</a></b>';
			
		} elseif($GLOBALS['course_descriptor']->getValue('show_who_online') == _SHOW_COUNT) {
			
			$user_stats['head'][2] = $lang->def('_WHOIS_ONLINE');
			$user_stats['body'][2] = '<b id="user_online_n">'
					.TrackUser::getWhoIsOnline($_SESSION['idCourse'])
				.'</b>';
		}
		// print first pannel
		if(!empty($user_stats['head'])) {
			
			$info_panel .= '<table id="user_stats">'
			.'<thead><tr>'
			.( isset($user_stats['head'][0]) ? '<th scope="col">'.$user_stats['head'][0].'</th>' : '' )
			.( isset($user_stats['head'][1]) ? '<th scope="col">'.$user_stats['head'][1].'</th>' : '' )
			.( isset($user_stats['head'][2]) ? '<th scope="col">'.$user_stats['head'][2].'</th>' : '' )
			.'</tr></thead><tbody><tr>'
			.( isset($user_stats['body'][0]) ? '<td>'.$user_stats['body'][0].'</td>' : '' )
			.( isset($user_stats['body'][1]) ? '<td>'.$user_stats['body'][1].'</td>' : '' )
			.( isset($user_stats['body'][2]) ? '<td>'.$user_stats['body'][2].'</td>' : '' )
			.'</tr></tbody>'
			.'</table>';
		}
		
		// print progress bar -------------------------------------------------
		if($GLOBALS['course_descriptor']->getValue('show_progress') == 1) {
			
			require_once( $GLOBALS['where_lms'].'/lib/lib.stats.php' );
			$total = getNumCourseItems( $_SESSION['idCourse'], 
										FALSE, 
										getLogUserId(), 
										FALSE );
			$tot_complete = getStatStatusCount(	getLogUserId(), 
												$_SESSION['idCourse'],
												array( 'completed', 'passed' ) );
			$tot_failed = getStatStatusCount(	getLogUserId(), 
												$_SESSION['idCourse'],
												array( 'failed' ) );
			
			
			$info_panel .= '<table id="course_stats">'
			.'<thead><tr>'
				.'<th scope="col">'.$lang->def('_PROGRESS_ALL').'</th>'
				.'<th scope="col">'.$lang->def('_PROGRESS_COMPLETE').'</th>'
				.'<th scope="col">'.$lang->def('_PROGRESS_FAILED').'</th>'
			.'</tr></thead><tbody><tr>'
				.'<td>'.$total.'</td>'
				.'<td>'.$tot_complete.'</td>'
				.'<td>'.$tot_failed.'</td>'
			.'</tr></tbody>'
			.'</table>';
			
			$info_panel .= '<p class="course_progress">'
				.'<span>'.$lang->def('_PROGRESS_INTO_THE_COURSE').'</span>'
				.'</p>'
				.'<div class="no_float"></div>'
				.renderProgress($tot_complete, $tot_failed, $total, false)."\n";
		}
		
		$info_panel .= '</div>'."\n";
		
		// Sponsor  ---------------------------------------------------
		if($sponsor_img != '') {
			
			$path = $GLOBALS['lms']['url'].$GLOBALS['where_files_relative'].'/doceboLms/'.$GLOBALS['lms']['pathcourse'];
			$link_arg = '<img src="'.$path.$sponsor_img.'" alt="'.$lang->def('_SPONSORED_BY').'" />';
		} else $link_arg = $lang->def('_SPONSORED_BY');
		
		if($sponsor_link != '' && trim($sponsor_link) != 'http://') {
			
			$GLOBALS['page']->add(
				'<a href="'.$sponsor_link.'" title="'.$lang->def('_SPONSORED_BY').'">'.$link_arg.'</a>'
			, 'menu');
		} elseif($sponsor_img != '') {
			
			$GLOBALS['page']->add($link_arg, 'menu');
		}
		
		
	} // end if course
	if ($counter == 1) {
		$GLOBALS['page']->clean('menu', false);
		$GLOBALS['page']->clean('content', false);
		$GLOBALS['page']->addStart('');
		$GLOBALS['page']->addEnd('');
	}
	
	$GLOBALS['page']->add($info_panel, 'menu');
	
	if(isset($GLOBALS['use_tag']) && $GLOBALS['use_tag'] == 'on') {
	
		addYahooJs(array('tabview'=>'tabview-min.js')
			, array('tabview/assets/skins/sam/' => 'tabview.css'));
			
		require_once($GLOBALS['where_framework'].'/lib/lib.tags.php');
		$tags = new Tags('*');
		
		$GLOBALS['page']->add(''
			
			.'<div id="tag_cloud" class="yui-navset"></div>'
			."<script type=\"text/javascript\">
			(function() {
				var cloud_tab = new YAHOO.widget.TabView();
		
				cloud_tab.addTab( new YAHOO.widget.Tab({
					label: '".def('_POPULAR', 'tags', 'framework')."',
					dataSrc: '".$GLOBALS['where_framework_relative']."/ajax.adm_server.php?plf=framework&file=tags&op=get_platform_cloud',
					cacheData: true, 
					active: true 
				}));
				cloud_tab.addTab( new YAHOO.widget.Tab({
					label: '".def('_COURSE', 'tags', 'framework')."',
					dataSrc: '".$GLOBALS['where_framework_relative']."/ajax.adm_server.php?plf=framework&file=tags&op=get_course_cloud',
					cacheData: true
				}));
				cloud_tab.addTab( new YAHOO.widget.Tab({
					label: '".def('_YOURS', 'tags', 'framework')."',
					dataSrc: '".$GLOBALS['where_framework_relative']."/ajax.adm_server.php?plf=framework&file=tags&op=get_user_cloud',
					cacheData: true
				}));
		
				cloud_tab.appendTo('tag_cloud');
			})();
			</script>"
		, 'menu');
	}
}

?>
