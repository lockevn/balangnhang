<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------



function drawCalendar($cal_id) {
	if ($cal_id < 1)
		return FALSE;

	$role_id ="/cms/calendar/".$cal_id."/admin";
	$can_admin =$GLOBALS["current_user"]->matchUserRole($role_id);
	$can_edit =FALSE;
	if (!$can_admin) {
		$role_id ="/cms/calendar/".$cal_id."/edit";
		$can_edit =$GLOBALS["current_user"]->matchUserRole($role_id);
	}

	$size =(isset($_GET["size"]) ? $_GET["size"]:"max");

	addCss('windows');
	addCss('calendar_'.$size);
	//addAjaxJs();
	//addScriptaculousJs();
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

	$GLOBALS['page']->add("\n".'<script type="text/javascript" src="'.$GLOBALS['where_cms_relative'].'/modules/calendar/calendar.js"></script>'."\n"
, 'page_head');

	$GLOBALS['page']->add("\n".'<script type="text/javascript" src="'.$GLOBALS['where_cms_relative'].'/modules/calendar/calendar_helper.js"></script>'."\n"
, 'page_head');


	//permissions = permissions granted to the user
	/*
	$permissions=2; //can create/delete/modify all events
	$permissions=1; //can create/delete/modify only own events
	$permissions=0; //can view only
	*/

	//mode="edit" => events can be added and edited according to given permissions
	//mode="view" => events can only be viewed regardless the permissions

	if ($can_admin) {
		$mode ="edit";
		$permissions =2;
	}
	else if ($can_edit) {
		$mode ="edit";
		$permissions =1;
	}
	else {
		$mode ="view";
		$permissions =0;
	}


	$GLOBALS['page']->add('<script type="text/javascript">'
     //.'setup_cal(\''.$GLOBALS['where_cms_relative'].'/modules/calendar/ajax.calendar.php\',\'cms\',\'cms\',\''.$mode.'\',\''.$permissions.'\',\''.$GLOBALS["current_user"]->getIdSt().'\', \''.$cal_id.'\');'
     .' setup_cal(\'NULL\',\'cms\',\'cms\',\''.$mode.'\',\''.$permissions.'\',\''.$GLOBALS["current_user"]->getIdSt().'\', \''.$cal_id.'\'); '
     .'</script>', 'page_head');


	$width="98%";
	if ($size=="min") $width="200px";
	$GLOBALS['page']->add("\n".'<div id="displayCalendar" style="float: left; clear: both; width:'.$width.'"></div>','content');
}



// ----------------------------------------------------------------------------

function calendarDispatch($op, $cal_id) {
	$cal_id =(int)$cal_id;
	switch ($op) {

		case "calendar": {
			drawCalendar($cal_id);
		} break;

	}
}


?>
