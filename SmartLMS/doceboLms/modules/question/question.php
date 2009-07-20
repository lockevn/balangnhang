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

//INFO: quest_create
function quest_create($type_quest, $idTest, $back_test) {

	//INFO: từ lọai câu hỏi $type_quest lấy về code file chứa class và
	//tên class tương ứng để xử lý loại câu hỏi đó
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_lms']."_quest_type 
	WHERE type_quest = '".$type_quest."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);

	require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);

	$quest_obj = eval("return new $type_class( 0 );");
	//INFO: create question mới bằng cách gọi method create của class tương ứng với question type
	$quest_obj->create($idTest, $back_test);
}

// XXX: quest_edit
function quest_edit($type_quest, $idQuest, $back_test, $isInBank=false) {


	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_lms']."_quest_type 
	WHERE type_quest = '".$type_quest."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);

	require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);

	$quest_obj = eval("return new $type_class( $idQuest );");

	$quest_obj->edit($back_test, $isInBank);
}

// XXX: switch
switch($GLOBALS['op']) {
	case "create" : {

		$type_quest = importVar('type_quest');
		$idTest = importVar('idTest', true, 0);
		$back_test = urldecode(importVar('back_test'));

		quest_create($type_quest, $idTest, $back_test);
	};break;
	case "edit" : {

		$type_quest = importVar('type_quest');
		$idQuest = importVar('idQuest', true, 0);
		/**
		 * danhut: kiểm tra xem đang edit trong bank hay test
		 */
		$isInBank = importVar('isInBank', true, 1);
		$back_test = urldecode(importVar('back_test'));
		
		if($isInBank == 0)
		{
			quest_edit($type_quest, $idQuest, $back_test, false);
		}
		else
		{
			quest_edit($type_quest, $idQuest, $back_test, true);
		}
	};break;
	case "quest_download" : {

		$type_quest = importVar('type_quest');
		$id_quest 	= importVar('id_quest', true, 0);
		$id_track 	= importVar('id_track', true, 0);

		$re_quest = mysql_query("
		SELECT type_file, type_class 
		FROM ".$GLOBALS['prefix_lms']."_quest_type 
		WHERE type_quest = '".$type_quest."'");
		if(!mysql_num_rows($re_quest) ) return;
		list($type_file, $type_class) = mysql_fetch_row($re_quest);

		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);

		$quest_obj = eval("return new $type_class( $id_quest );");

		$quest_obj->download($id_track);
	};break;
}

?>