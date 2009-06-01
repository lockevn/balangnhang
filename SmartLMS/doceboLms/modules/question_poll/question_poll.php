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

// XXX: quest_create
function quest_create($type_quest, $id_poll, $back_poll) {
	
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_lms']."_quest_type_poll 
	WHERE type_quest = '".$type_quest."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once($GLOBALS['where_lms'].'/modules/question_poll/'.$type_file);
	
	$quest_obj = eval("return new $type_class( 0 );");
	$quest_obj->create($id_poll, $back_poll);
}

// XXX: quest_edit
function quest_edit($type_quest, $id_quest, $back_poll) {
	
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_lms']."_quest_type_poll 
	WHERE type_quest = '".$type_quest."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once($GLOBALS['where_lms'].'/modules/question_poll/'.$type_file);
	
	$quest_obj = eval("return new $type_class( $id_quest );");
	
	$quest_obj->edit($back_poll);
}

// XXX: switch
switch($GLOBALS['op']) {
	case "create" : {
		
		$type_quest = importVar('type_quest');
		$id_poll = importVar('id_poll', true, 0);
		$back_poll = urldecode(importVar('back_poll'));
		
		quest_create($type_quest, $id_poll, $back_poll);
	};break;
	case "edit" : {
		
		$type_quest = importVar('type_quest');
		$id_quest = importVar('id_quest', true, 0);
		$back_poll = urldecode(importVar('back_poll'));
		
		quest_edit($type_quest, $id_quest, $back_poll);
	};break;
}

?>