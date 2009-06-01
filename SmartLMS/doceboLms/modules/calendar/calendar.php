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

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function drawCalendar() {
	checkPerm('view');
	
	$size = importVar('size', false, 'max');
	
	$width = "90%";
	if($size == "min") $width = "200px";
	
	addCss('calendar_'.$size);
	
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
	addCss('style_yui_docebo');
		
	addJs($GLOBALS['where_lms_relative'].'/modules/calendar/', 'calendar.js');
	addJs($GLOBALS['where_lms_relative'].'/modules/calendar/', 'calendar_helper.js');
	
	//permissions = permissions granted to the logged user according to his/her level and role 
	//	2 => can create/delete/modify all events
	//	1 => can create/delete/modify only own events
	//	0 => can view only 
	
	$permissions = 0;
	if(checkPerm('mod', true)) $permissions = 2;
	elseif(checkPerm('personal', true)) $permissions = 1;
	
	//mode="edit" => events can be added and edited according to given permissions
	//mode="view" => events can only be viewed regardless the permissions
	
	$GLOBALS['page']->add('<script type="text/javascript">'
		.'	setup_cal(	null, '
		.'\'lms\', '
		.'\'lms\', '
		.'\'edit\', '
		.'\''.$permissions.'\', '
		.'\''.$GLOBALS["current_user"]->getIdSt().'\' '
		.');'
    	 .'</script>'
    , 'page_head');
	
	$GLOBALS['page']->add("\n"
		.getTitleArea(def('_CALENDAR', 'calendar'), 'calendar')
		.'<div class="std_block">'
		.'<div id="displayCalendar" style="clear: both; width:'.$width.'"></div>'
		.'<div class="no_float"></div>'
		.'</div>'
	, 'content');
}


?>
