<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package  DoceboCore
 * @version  $Id: field.php 977 2007-02-23 10:40:19Z fabio $
 * @category Field
 * @author   Fabio Pirovano <fabio@docebo.com>
 */
 
// XXX: field create
function field_create($type_field, $back) {
	checkPerm('add', false, 'field_manager');
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_fw']."_field_type 
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
	
	$quest_obj = new $type_class( 0 );
	$quest_obj->setUrl('index.php?modname=field&amp;op=manage&amp;fo=create');
	$quest_obj->create($back);
}

// XXX: field edit
function field_edit($type_field, $id_common, $back) {
	checkPerm('mod', false, 'field_manager');
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_fw']."_field_type 
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
	
	$quest_obj = new $type_class( $id_common );
	$quest_obj->setUrl('index.php?modname=field&amp;op=manage&amp;fo=edit');
	$quest_obj->edit($back);
}

// XXX: field del
function field_del($type_field, $id_common, $back) {
	checkPerm('del', false, 'field_manager');
	
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_fw']."_field_type 
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
	
	$quest_obj = new $type_class( $id_common );
	$quest_obj->setUrl('index.php?modname=field&amp;op=manage&amp;fo=del');
	$quest_obj->del($back);
}

function field_specialop($type_field, $id_common, $back) {
	
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_fw']."_field_type 
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
	
	$quest_obj = new $type_class( $id_common );
	$quest_obj->setUrl('index.php?modname=field&amp;op=manage&amp;fo=special');
	$quest_obj->specialop($back);
}

// XXX: switch
$fo = importVar('fo');
switch($fo) {
	case "create" : {
		
		$back = urldecode(importVar('back'));
		$type_field = importVar('type_field');
		
		field_create($type_field, $back);
	};break;
	case "edit" : {
		
		$back = urldecode(importVar('back'));
		$id_common = importVar('id_common', true, 0);
		$type_field = importVar('type_field');
		
		field_edit($type_field, $id_common, $back);
	};break;
	case "del" : {
		
		$back = urldecode(importVar('back'));
		$id_common = importVar('id_common', true, 0);
		$type_field = importVar('type_field');
		
		field_del($type_field, $id_common, $back);
	};break;
	case "special" : {
		
		$back = urldecode(importVar('back'));
		$id_common = importVar('id_common', true, 0);
		$type_field = importVar('type_field');
		
		field_specialop($type_field, $id_common, $back);
	};break;
}

?>