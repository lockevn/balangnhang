<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");


function productfieldMain() {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');

	$back_coded 	= htmlentities(urlencode('index.php?modname=productfield&op=field_list'));
	$std_lang 		=& DoceboLanguage::createInstance('standard', 'framework');
	$lang 			=& DoceboLanguage::createInstance('field', 'framework');
	$out 			=& $GLOBALS['page'];
	$filter 		= new Form();

	//find available field type
	$re_field = mysql_query("
	SELECT type_field
	FROM ".$GLOBALS['prefix_fw']."_field_type
	ORDER BY type_field");
	$field_av = array();
	$field_select = array('all_field' => $lang->def('_ALL_FIELD_TYPE'));
	while(list($type_field) = mysql_fetch_row($re_field)) {
		$field_av[] = $type_field;
		$field_select[] = $lang->def('_'.strtoupper($type_field));
	}

	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_FIELD_MANAGER'), 'productfield'));
	$out->add('<div class="std_block">');

	//catch possible operation result
	if(isset($_GET['result'])) {
		if($_GET['result'] == 'success') $out->add(getResultUi($lang->def('_SUCCESS_OPERATION')));
		if($_GET['result'] == 'fail') $out->add(getErrorUi($lang->def('_ERR_FAIL_CREATE')));
	}

	$ord = importVar('ord', false, 'trans');
	$flip = importVar('flip', true, 0);

	//filter------------------------------------------------------------
	$filter_type_field = importVar('filter_type_field', false, 'all_field');
	$filter_name_field = importVar('filter_name_field', false, $lang->def('_SEARCH'));
	$out->add(
		$filter->openForm('field_filter', 'index.php?modname=productfield&amp;op=field_list')
		.$filter->getOpenFieldset($lang->def('_FILTER_AV'))
		.$filter->getHidden('ord', 'ord', $ord)
		.$filter->getHidden('flip', 'flip', $flip)
		.$filter->getDropdown($lang->def('_FIELD_TYPE'), 'filter_type_field', 'filter_type_field',
			$field_select, $filter_type_field)
		.$filter->getTextfield($lang->def('_NAME'), 'filter_name_field', 'filter_name_field',
			'255', $filter_name_field)
		.$filter->openButtonSpace()
		.$filter->getButton('search', 'search', $std_lang->def('_SEARCH'))
		.$filter->closeButtonSpace()
		.$filter->getCloseFieldset()
		.$filter->closeForm()
	);

	//display inserted field--------------------------------------------
	$tb_field = new TypeOne($GLOBALS['visuItem'], $lang->def('_FIELD_INSERTED'), $lang->def('_FIELD_INSERTED'));

	$query_field_display = "
	SELECT id_common, type_field, translation
	FROM ".$GLOBALS['prefix_ecom']."_product_field
	WHERE lang_code = '".getLanguage()."'
		".( isset($_POST['filter_type_field']) && $_POST['filter_type_field'] != 'all_field' ?
			" AND type_field = '".$field_av[$_POST['filter_type_field']]."' " :
			"" )."
		".( isset($_POST['filter_name_field']) && $_POST['filter_name_field'] != $lang->def('_SEARCH') ?
			" AND translation LIKE '%".$filter_name_field."%'" :
			"" )."
	ORDER BY sequence";
	$re_field_display = mysql_query($query_field_display);
	$all_fields = mysql_num_rows($re_field_display);

	$img_up = '<img class="valing_middle" src="'.getPathImage().'standard/up.gif" alt="'.$std_lang->def('_MOVE_UP').'" />';
	$img_down = '<img class="valing_middle" src="'.getPathImage().'standard/down.gif" alt="'.$std_lang->def('_MOVE_DOWN').'" />';

	$content_h 	= array(
		'<a href="index.php?modname=productfield&amp;op=field_list">'.$lang->def('_TRANSLATION').'</a>',
		'<a href="index.php?modname=productfield&amp;op=field_list">'.$lang->def('_FIELD_TYPE').'</a>');
	$type_h 	= array('', 'align_center');

	$mod_perm = checkPerm('mod', true);
	$del_perm = checkPerm('del', true);
	if($mod_perm) {
		$content_h[] = $img_down;
		$content_h[] = $img_up;
		$content_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$std_lang->def('_MOD').'" />';
		$type_h[] = 'image';
		$type_h[] = 'image';
		$type_h[] = 'image';
	}
	if($del_perm) {
		$content_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$std_lang->def('_DEL').'" />';
		$type_h[] = 'image';
	}
	$tb_field->setColsStyle($type_h);
	$tb_field->addHead($content_h);

	$lat_type = 'textfield';
	$i = 1;
	while(list($id_common, $type_field, $translation) = mysql_fetch_row($re_field_display)) {

		$cont = array($translation, $lang->def('_'.strtoupper($type_field)));
		if($mod_perm) {
			if($i != $all_fields) {
			$cont[] = '<a href="index.php?modname=productfield&amp;op=movedown&amp;type_field='
				.$type_field.'&amp;id_common='.$id_common.'&amp;back='.$back_coded.'"'
				.' title="'.$lang->def('_MOVE_DOWN').'">'.$img_down.'</a>';
			} else $cont[] = '';
			if($i != 1) {
			$cont[] = '<a href="index.php?modname=productfield&amp;op=moveup&amp;type_field='
				.$type_field.'&amp;id_common='.$id_common.'&amp;back='.$back_coded.'"'
				.' title="'.$lang->def('_MOVE_UP').'">'.$img_up.'</a>';
			} else $cont[] = '';
			$cont[] = '<a href="index.php?modname=productfield&amp;op=manage&amp;fo=edit&amp;type_field='
				.$type_field.'&amp;id_common='.$id_common.'&amp;back='.$back_coded.'"'
				.' title="'.$lang->def('_MOD_FIELD').'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$std_lang->def('_MOD').'" /></a>';
		}
		if($del_perm) {
			$cont[] = '<a href="index.php?modname=productfield&amp;op=field_del&amp;id_common='.$id_common.'"'
				.' title="'.$lang->def('_DEL_FIELD').'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$std_lang->def('_DEL').'" /></a>';
		}
		$tb_field->addBody($cont);
		$lat_type = $type_field;
		$i++;
	}

	$create_form = new Form();
	$select = '';
	foreach($field_av as $k => $type_field) {
		$select .= '<option value="'.$type_field.'"'
				.( $type_field == $lat_type ? ' selected="selected"' : '' ).'>'
				.$lang->def('_'.strtoupper($type_field)).'</option>';
	}


	//add form----------------------------------------------------------
	if(checkPerm('add', true)) {
		$tb_field->addActionAdd(
				$create_form->openForm('field_add', 'index.php?modname=productfield&amp;op=manage&amp;fo=create')
				.$create_form->getHidden('back', 'back', $back_coded)
				.'<label for="type_field">'
				.'<img class="valing_middle" src="'.getPathImage().'standard/add.gif" alt="'.$std_lang->def('_ADD').'" />'
				.' '.$lang->def('_ADD_NEW_FIELD').'</label> '
				.'<select id="type_field" name="type_field">'
				.$select
				.'</select> '
				.$filter->getButton('new_field', 'new_field', $std_lang->def('_CREATE'), 'button_nowh')
				.$filter->closeForm()
		);
	}
	$out->add($tb_field->getTable());

	$out->add('<a href="index.php?modname=productfield&amp;op=fixsequence&amp;back='.$back_coded.'"'
				.' title="'.$lang->def('_FIX_SEQUENCE_ERROR_TITLE').'">'.$lang->def('_FIX_SEQUENCE_ERROR').'</a>');

	$out->add('</div>');
}



function field_del() {
	checkPerm('del');
	$back_coded 	= htmlentities(urlencode('index.php?modname=productfield&op=field_list'));
	$std_lang 		=& DoceboLanguage::createInstance('standard', 'framework');
	$lang 			=& DoceboLanguage::createInstance('field', 'framework');
	$out 			=& $GLOBALS['page'];

	$id_common = importVar('id_common', true, 0);

	//find available field type
	$re_field = mysql_query("
	SELECT type_field, translation
	FROM ".$GLOBALS['prefix_fw']."_field
	WHERE id_common = '".(int)$id_common."'
	ORDER BY type_field");
	list($type_field, $translation) = mysql_fetch_row($re_field);

	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_FIELD_MANAGER'), 'productfield'));
	$out->add('<div class="std_block">');

	$out->add('<div class="boxinfo_title">'.$lang->def('_AREYOUSURE').'</div>'
			.'<div class="boxinfo_container">'
			.'<span class="text_bold">'.$lang->def('_FIELD_TYPE').' : </span>'.$lang->def('_'.strtoupper($type_field)).'<br />'
			.'<span class="text_bold">'.$lang->def('_TRANSLATION').' : </span>'.$translation
			.'</div>'
			.'<div class="del_container">'
			.'<a href="index.php?modname=productfield&amp;op=manage&amp;fo=del&amp;type_field='
					.$type_field.'&amp;id_common='.(int)$id_common.'&amp;back='.$back_coded.'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$std_lang->def('_CONFIRM').'" />&nbsp;'
				.$std_lang->def('_CONFIRM').'</a>&nbsp;&nbsp;'
			.'<a href="index.php?modname=productfield&amp;op=field_list&amp;result=undo">'
				.'<img src="'.getPathImage().'standard/undo.gif" alt="'.$std_lang->def('_UNDO').'" />&nbsp;'
				.$std_lang->def('_UNDO').' </a>'
			.'</div>');

	$out->add('</div>');
}

function movefield($direction) {
	checkPerm('mod');
	$out 			=& $GLOBALS['page'];

	$id_common = importVar('id_common', true, 0);

	$re_field = mysql_query("
	SELECT tf.type_file, tf.type_class, f.sequence
	FROM ".$GLOBALS['prefix_fw']."_field_type AS tf JOIN
		".$GLOBALS['prefix_ecom']."_product_field AS f
	WHERE tf.type_field = f.type_field AND
		id_common = '".(int)$id_common."'");
	list($type_file_1, $type_class_1, $sequence) = mysql_fetch_row($re_field);

	if($direction == 'up') {
		$next_seq = $sequence - 1;
	} else {
		$next_seq = $sequence + 1;
	}

	$query_field_2 = "
	SELECT tf.type_file, tf.type_class, f.id_common
	FROM ".$GLOBALS['prefix_fw']."_field_type AS tf JOIN
		".$GLOBALS['prefix_ecom']."_product_field AS f
	WHERE tf.type_field = f.type_field AND
		f.sequence = '".(int)$next_seq."'";

	$re_field_2 = mysql_query($query_field_2);
	list($type_file_2, $type_class_2, $id_common_2) = mysql_fetch_row($re_field_2);

	$back = urldecode(importVar('back'));
	if($type_file_2 == '') {

		fixsequence(false);
		$re_field_2 = mysql_query($query_field_2);
		list($type_file_2, $type_class_2, $id_common_2) = mysql_fetch_row($re_field_2);

		if($type_file_2 == '') jumpTo($back);
	}

	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file_1);
	$first_instance = eval("return new $type_class_1( $id_common );");
	$first_instance->movetoposition($next_seq);

	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file_2);
	$second_instance = eval("return new $type_class_2( $id_common_2 );");
	$second_instance->movetoposition($sequence);

	jumpTo($back);
}

function fixsequence($jump = true) {
	checkPerm('mod');

	$re_field = mysql_query("
	SELECT DISTINCT tf.type_file, tf.type_class, f.id_common
	FROM ".$GLOBALS['prefix_fw']."_field_type AS tf JOIN
		".$GLOBALS['prefix_ecom']."_product_field AS f
	WHERE tf.type_field = f.type_field
	ORDER BY f.sequence");

	$new_sequence = 1;
	while(list($type_file, $type_class, $id_common) = mysql_fetch_row($re_field)) {

		require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);
		$first_instance = eval("return new $type_class( $id_common );");
		$first_instance->movetoposition($new_sequence++);
	}

	$back = urldecode(importVar('back'));
	if($jump) jumpTo($back);
}


function manageFields() {
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

			deleteField($type_field, $id_common, $back);
		};break;
		case "special" : {
			$back = urldecode(importVar('back'));
			$id_common = importVar('id_common', true, 0);
			$type_field = importVar('type_field');

			field_specialop($type_field, $id_common, $back);
		};break;
	}
}



// XXX: field create
function field_create($type_field, $back) {
	checkPerm('add');
	$re_quest = mysql_query("
	SELECT type_file, type_class
	FROM ".$GLOBALS['prefix_fw']."_field_type
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);

	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);

	$quest_obj = new $type_class( 0 );
	$quest_obj->setMainTable($GLOBALS['prefix_ecom']."_product_field");
	$quest_obj->setShowOnPlatformDefaultArr(array("ecom"));
	$quest_obj->setUseMultiLang(TRUE);
	$quest_obj->setCanSelectPlatform(FALSE);
	$quest_obj->setUrl('index.php?modname=productfield&amp;op=manage&amp;fo=create');
	$quest_obj->create($back);
}

// XXX: field edit
function field_edit($type_field, $id_common, $back) {
	checkPerm('mod');
	$re_quest = mysql_query("
	SELECT type_file, type_class
	FROM ".$GLOBALS['prefix_fw']."_field_type
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);

	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);

	$quest_obj = new $type_class( $id_common );
	$quest_obj->setMainTable($GLOBALS['prefix_ecom']."_product_field");
	$quest_obj->setShowOnPlatformDefaultArr(array("ecom"));
	$quest_obj->setUseMultiLang(TRUE);
	$quest_obj->setCanSelectPlatform(FALSE);
	$quest_obj->setUrl('index.php?modname=productfield&amp;op=manage&amp;fo=edit');
	$quest_obj->edit($back);
}

// XXX: field del
function deleteField($type_field, $id_common, $back) {
	checkPerm('del');

	$re_quest = mysql_query("
	SELECT type_file, type_class
	FROM ".$GLOBALS['prefix_fw']."_field_type
	WHERE type_field = '".$type_field."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);

	require_once($GLOBALS['where_framework'].'/modules/field/'.$type_file);

	$quest_obj = new $type_class( $id_common );
	$quest_obj->setMainTable($GLOBALS['prefix_ecom']."_product_field");
	$quest_obj->setUrl('index.php?modname=productfield&amp;op=manage&amp;fo=del');
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
	$quest_obj->setMainTable($GLOBALS['prefix_ecom']."_product_field");
	$quest_obj->setUrl('index.php?modname=productfield&amp;op=manage&amp;fo=special');
	$quest_obj->specialop($back);
}



//---------------------------------------------------------------------------//

function productfieldDispatch($op) {
	switch($op) {
		case "main":
		case "field_list": {
			productfieldMain();
		} break;
		case "field_del" : {
			field_del();
		};break;
		case "moveup" : {
			movefield('up');
		};break;
		case "movedown" : {
			movefield('down');
		};break;
		case "fixsequence" : {
			fixsequence();
		};break;

		case "manage" : {
			manageFields();
		} break;
	}
}

?>
