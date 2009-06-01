<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function view_area() {
	checkPerm('view');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.middlearea.php');
	
	$lang 	=& DoceboLanguage::createInstance('middlearea', 'lms');
	$lc 	=& DoceboLanguage::createInstance('menu_course', 'lms');
	$lo 	=& DoceboLanguage::createInstance('menu_over', 'lms');
	
	
	$query_menu = "
	SELECT mo.idModule, mo.module_name, mo.default_op, mo.default_name, mo.token_associated,
		under.idMain, under.my_name, mo.module_info
	FROM ".$GLOBALS['prefix_lms']."_module AS mo
		JOIN ".$GLOBALS['prefix_lms']."_menucourse_under AS under
	WHERE mo.idModule = under.idModule
		AND under.idCourse = '0'
	ORDER BY under.sequence ";
	$re_menu_voice = mysql_query($query_menu);
	
	
	$base_url = 'index.php?modname=middlearea&amp;op=select_permission&amp;load=1&amp;obj_index=';
	$second_url = 'index.php?modname=middlearea&amp;op=switch_active&amp;obj_index=';
	
	$ma = new Man_MiddleArea();
	
	$disabled_list = $ma->getDisabledList(); 
	
	cout('<link href="'.getPathTemplate('lms').'style/style_middlearea_man.css" rel="stylesheet" type="text/css" />', 'page_head');
	
	
	cout( getTitleArea($lang->def('_MIDDLE_AREA'), 'middlearea')
		.'<div class="std_block">' );
	
	cout( '<div class="menu_over">'
        .'<ul>' ); 
    
    $user_opt = 0;
	$user_area = '';
	while(list($id_m, $module_name, $def_op, $default_name, $token, $id_main, $my_name, $m_info) = mysql_fetch_row($re_menu_voice)) {

		if($module_name != 'course_autoregistration' && $module_name != 'public_forum') {
			
			if($module_name != 'course') {
				
				if(strstr($m_info, 'type=user') || strstr($m_info, 'type=public_admin')) {
					
						
						cout( '<li class="ico'.( isset($disabled_list['mo_'.$id_m]) ? ' disabled' : '' ).'">'
							.'<a href="'.$base_url.'mo_'.$id_m.'">'
							.( $lc->isDef($default_name) ? $lc->def($default_name) : $default_name )
							.'</a>'
							.' <a class="use_area" href="'.$second_url.'mo_'.$id_m.'">'.$lang->def('_ENABLE_AREA').'</a>'
							.'</li>' );
					
				} else {
				
					cout( '<li class="ico'.( isset($disabled_list['mo_'.$id_m]) ? ' disabled' : '' ).'">'
						.'<a href="'.$base_url.'mo_'.$id_m.'">'
						.( $lc->isDef($default_name) ? $lc->def($default_name) : $default_name )
						.'</a>'
						.' <a class="use_area" href="'.$second_url.'mo_'.$id_m.'">'.$lang->def('_ENABLE_AREA').'</a>'
						.'</li>' );
				}
			} else {
				
				cout( '<li>'
					.( $lc->isDef($default_name) ? $lc->def($default_name) : $default_name )
					.'</li>' );
			}
		}
	}
	
	cout( '<li class="ico'.( isset($disabled_list['course_autoregistration']) ? ' disabled' : '' ).'">'
		.'<a href="'.$base_url.'course_autoregistration">'
		.$lo->def('_COURSE_AUTOREGISTRATION')
		.'</a>'
		.' <a class="use_area" href="'.$second_url.'course_autoregistration">'.$lang->def('_ENABLE_AREA').'</a>'
		.'</li>' );
	
	cout( '<li class="ico'.( isset($disabled_list['public_forum']) ? ' disabled' : '' ).'">'
		.'<a href="'.$base_url.'public_forum">'
		.$lo->def('_PUBLIC_FORUM')
		.'</a>'
		.' <a class="use_area" href="'.$second_url.'public_forum">'.$lang->def('_ENABLE_AREA').'</a>'
		.'</li>' );
  
	cout(  '<li class="ico'.( isset($disabled_list['message']) ? ' disabled' : '' ).'">'
		.'<a href="'.$base_url.'message">'
		.$lo->def('_MY_MESSAGE')
		.'</a>'
		.'<a class="use_area" href="'.$second_url.'message">'.$lang->def('_ENABLE_AREA').'</a>'
		.'</li>' );
	
	cout( '<li>'
		.$lo->def('_GO_TO_FRAMEWORK')
		.'</li>' );
	
	cout( '<li>'
		.$lo->def('_LOGOUT')
		.'</li>' );
  
  	cout( '</ul>'
  		.'</div>' ); 
    
    cout('
    
    <div class="macolum_container">
    
        <div class="right_col">
            <div class="box'.( isset($disabled_list['user_details_short']) ? ' disabled' : '' ).'">

				<p class="ico">
				<a href="'.$base_url.'user_details_short">'.$lang->def('_SIMPLE_USER_PROFILE').'</a></p>
				<a class="use_area" href="'.$second_url.'user_details_short">'.$lang->def('_ENABLE_AREA').'</a>
			</div>
            <div class="box'.( isset($disabled_list['user_details_full']) ? ' disabled' : '' ).'">

				<p class="ico">
				<a href="'.$base_url.'user_details_full">'.$lang->def('_COMPLETE_USER_PROFILE').'</a></p>
				<a class="use_area" href="'.$second_url.'user_details_full">'.$lang->def('_ENABLE_AREA').'</a>
			</div>
            <div class="box'.( isset($disabled_list['career']) ? ' disabled' : '' ).'">

				<p class="ico">
				<a href="'.$base_url.'career">'.$lang->def('_CAREER').'</a></p>
				<a class="use_area" href="'.$second_url.'career">'.$lang->def('_ENABLE_AREA').'</a>
			</div>
            <div class="box'.( isset($disabled_list['search_form']) ? ' disabled' : '' ).'">

				<p class="ico">
				<a href="'.$base_url.'search_form">'.$lang->def('_SEARCH_FORM').'</a></p>
				<a class="use_area" href="'.$second_url.'search_form">'.$lang->def('_ENABLE_AREA').'</a>
			</div>
            <div class="box'.( isset($disabled_list['news']) ? ' disabled' : '' ).'">

				<p class="ico">
				<a href="'.$base_url.'news">'.$lang->def('_NEWS').'</a></p>
				<a class="use_area" href="'.$second_url.'news">'.$lang->def('_ENABLE_AREA').'</a>
			</div>
        </div>



        
        <div class="left_col">
            <div class="box'.( isset($disabled_list['lo_tab']) ? ' disabled' : '' ).'">

				<p class="align_right ico">
				<a href="'.$base_url.'lo_tab">'.$lang->def('_LO_PLAN').'</a>
				<a class="use_area" href="'.$second_url.'lo_tab">'.$lang->def('_ENABLE_AREA').'</a>
				</p>
			</div>
            <div class="box">

				<p class="fixed">'.$lang->def('_MY_COURSES').'</p>
			</div>
        </div>

	</div>');
	
	cout('<div class="no_float"></div>');
	
	cout('</div>');
}

function switch_active() {

	require_once($GLOBALS['where_lms'].'/lib/lib.middlearea.php');
	
	$man_ma = new Man_MiddleArea(); 
	
	$obj_index = importVar('obj_index', false, '');
	
	$lang =& DoceboLanguage::createInstance('middlearea', 'lms');
	$selected = $man_ma->getObjIdstList($obj_index);
	$man_ma->setObjIdstList($obj_index, $selected);
	
	$re = $man_ma->changeDisableStatus($obj_index);
	
	jumpTo('index.php?modname=middlearea&amp;op=view_area&amp;result='.($re ? 'ok' : 'err' ));
}

function select_permission() {
	checkPerm('view');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.middlearea.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('middlearea', 'lms');
	
	$obj_index = importVar('obj_index', false, '');
	
	// first step load selector 
	
	$man_ma 	 = new Man_MiddleArea(); 
	$acl_manager = new DoceboACLManager();
	$user_select = new Module_Directory();
	
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = TRUE;
	$user_select->show_orgchart_simple_selector = false;
	//$user_select->multi_choice = TRUE;
	
	// try to load previous saved
	if(isset($_GET['load'])) {
		
		$selected = $man_ma->getObjIdstList($obj_index);
		if(is_array($selected))	$user_select->resetSelection($selected);
	}
	if(isset($_POST['okselector'])) {
	
		$selected = $user_select->getSelection($_POST);
		$re = $man_ma->setObjIdstList($obj_index, $selected);
		jumpTo('index.php?modname=middlearea&amp;op=view_area&amp;result='.($re ? 'ok' : 'err' ));
	}
	
	
	$user_select->setPageTitle(
		getTitleArea($lang->def('_MIDDLE_AREA'), 'middlearea')
	);
	$user_select->addFormInfo(
		Form::getHidden('obj_index', 'obj_index', $obj_index)
	);
	$user_select->loadSelector('index.php?modname=middlearea&amp;op=select_permission', 
			false, 
			$lang->def('_CHOOSE_WHO_CAN_SEE'), 
			true, 
			true );
}

/**
 * Dispatching
 **/
function MiddleAreaDispatch($op) {
	
	if(isset($_POST['cancelselector'])) $op = 'view_area';
	
	switch($op) {
		case "select_permission" : {	
			select_permission();
		};break;
		case "switch_active" : {	
			switch_active();
		};break;
		case "view_area" :
		default : {
			view_area();
		};break;
	}
	
	
}

?>