<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2008                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function questbank(&$url) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
	
	$lang =& DoceboLanguage::createInstance('test', 'lms');
	// now add the yui for the table
	
	$qb_select 	= new QuestBank_Selector();
	$qb_select->selected_quest = 'selected_quest';
	$qb_select->item_per_page = 25;
	
	$qb_man 	= new QuestBankMan();
	
	$form = new Form();
	
	
	cout($qb_select->get_header(), 'page_head');
	addCss('style_yui_docebo');
	
	cout('<script type="text/javascript">'
		.$qb_select->get_setup_js()
		.'</script>');
	
	cout( getTitleArea('quest_bank')
		.'<div class="std_block yui-skin-docebo yui-skin-sam">');
		
	// -- search filter --------------------------------------------------
	
	$export_f = $qb_man->supported_format();
	
	cout($form->openForm('search_form', $url->getUrl(), false, 'POST') 
	
		.'<input type="hidden" id="selected_quest" name="selected_quest" value="">'
		
		.'<div class="align_right">
	
			<input type="submit" id="export_quest" name="export_quest" value="'.$lang->def('_EXPORT').'">
			<select id="export_quest_select" name="export_quest_select">');
		foreach($export_f as $id_exp => $def) {
			cout('<option value="'.$id_exp.'">'.$def.'</option>');
		}
		cout('</select>
			<input type="submit" id="import_quest" name="import_quest" value="'.$lang->def('_IMPORT').'">
		</div>');
	
	cout($qb_select->get_filter());
	
	cout($form->closeForm());
	
	// -------------------------------------------------------------------
	
	cout($qb_select->get_selector());
	
	$re_type = mysql_query("
	SELECT type_quest 
	FROM ".$GLOBALS['prefix_lms']."_quest_type
	WHERE type_quest <> 'break_page'
	ORDER BY sequence");
	
	cout('
	<div class="align_left">'
		.$form->openForm('add_quest_form', $url->getUrl('op=addquest'), 'GET').'
		<input type="submit" id="add_quest" name="add_quest" value="'.$lang->def('_ADD').'">
		<select id="add_test_quest" name="add_test_quest">');         
	while(list($type_quest) = mysql_fetch_row($re_type)) {
		
		cout('<option value="'.$type_quest.'">'
			.$lang->def('_QUEST_ACRN_'.strtoupper($type_quest)).' - '.$lang->def('_QUEST_'.strtoupper($type_quest))
			.'</option>');
	}
	cout('</select>'
		.$form->closeForm().'
	</div>');
	
	cout('</div>');
}

// XXX: addquest 
function addquest(&$url) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('test');
	
	$type_quest = importVar('add_test_quest', false, 'choice');
	
	require_once($GLOBALS['where_lms'].'/modules/question/question.php');
	
	quest_create($type_quest, 0, $url->getUrl());
}

function modquest(&$url) {
	$lang =& DoceboLanguage::createInstance('test');
	
	$id_quest = importVar('id_quest', true, 0);
	
	list($type_quest) = mysql_fetch_row(mysql_query("
	SELECT type_quest 
	FROM ".$GLOBALS['prefix_lms']."_bankquest 
	WHERE idQuest = '".$id_quest."'"));
	
	require_once($GLOBALS['where_lms'].'/modules/question/question.php');
	
	quest_edit($type_quest, $id_quest, $url->getUrl(), true);
}

function importquest(&$url) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('test');
	$form = new Form();

	require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
	$qb_man = new QuestBankMan();
	$supported_format = array_flip($qb_man->supported_format());
	
	$title = array($url->getUrl() => $lang->def('_QUEST_BANK'), $lang->def('_IMPORT'));
	cout(
		getTitleArea($title, 'quest_bank')
		.'<div class="std_block">'

		.$form->openForm('import_form', $url->getUrl('op=doimportquest'), false, false, 'multipart/form-data')
		
		.$form->openElementSpace()
		.$form->getFilefield($lang->def('_FILE'), 'import_file', 'import_file')
		.$form->getRadioSet($lang->def('_FILE_FORMAT'), 'file_format', 'file_format', $supported_format, 0)
		.$form->getTextfield($lang->def('_FILE_ENCODE'), 'file_encode', 'file_encode', 255, 'utf-8')
		.$form->closeElementSpace()
		
		.$form->openButtonSpace()
		.$form->getButton('undo','undo',$lang->def('_UNDO'))
		.$form->getButton('quest_search','quest_search',$lang->def('_IMPORT') )
		.$form->closeButtonSpace()
		.$form->closeForm()
		
		.'</div>');
}

function doimportquest(&$url) {

	require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
	
	$lang_t =& DoceboLanguage::createInstance('test');
	
	$qb_man = new QuestBankMan();
	
	$file_format = get_req('file_format', DOTY_INT, 0);
	$file_encode = get_req('file_encode', DOTY_ALPHANUM, 'utf-8');
	$file_readed = file($_FILES['import_file']['tmp_name']);
	
	addCss('style_yui_docebo');
	
	$title = array($url->getUrl() => $lang_t->def('_QUEST_BANK'), $lang_t->def('_IMPORT'));
	cout( getTitleArea($title, 'quest_bank')
		.'<div class="std_block">'
		.getBackUi($url->getUrl(), $lang_t->def('_BACK')) );
	
	$import_result = $qb_man->import_quest($file_readed, $file_format);
	
	cout('<table clasS="type-one" id="import_result">'
		.'<caption>'.$lang_t->def('_IMPORT').'</caption>');
	cout('<thead>');
	cout('<tr class="type-one-header">'
		.'<th>'.$lang_t->def('_QUEST_TYPE').'</th>'
		.'<th>'.$lang_t->def('_SUCCESS').'</th>'
		.'<th>'.$lang_t->def('_FAIL').'</th>'
		.'</tr>' );
	cout('</thead>');
	cout('<tbody>');
	foreach($import_result as $type_quest => $i_result) {
		
		cout('<tr>'
			.'<td>'.$lang_t->def('_QUEST_'.strtoupper($type_quest)).'</td>'
			.'<td>'.( isset($i_result['success']) ? $i_result['success'] : '' ).'</td>'
			.'<td>'.( isset($i_result['fail']) ? $i_result['fail'] : '' ).'</td>'
			.'</tr>' );
	}
	cout('</tbody>');
	cout('</table>');
	
	cout('</div>');
}

function exportquest(&$url) {

	require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
	
	$lang =& DoceboLanguage::createInstance('test');
	
	$qb_man = new QuestBankMan();
	
	$file_format = get_req('export_quest_select', DOTY_INT, 0);
	$quest_category 	= get_req('quest_category', DOTY_INT);
	$quest_difficult 	= get_req('quest_difficult', DOTY_INT);
	$quest_type 		= get_req('quest_type', DOTY_ALPHANUM); 
	
	$quest_selection 	= get_req('selected_quest', DOTY_NUMLIST, '');
	
	$quest_selection = array_filter(preg_split('/,/', $quest_selection, -1, PREG_SPLIT_NO_EMPTY));
	
	if($file_format == -1)
	{
		$new_test_step = get_req('new_test_step', DOTY_INT);
		
		if($new_test_step == 2)
		{
			if( trim($_POST['title']) == '' ) $_POST['title'] = $lang->def('_NOTITLE');
			
			//Insert the test
			
			$ins_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_test 
			( author, title, description )
				VALUES 
			( '".(int)getLogUserId()."', '".$_POST['title']."', '".$_POST['textof']."' )";
			
			if( !mysql_query($ins_query) )
			{
				$_SESSION['last_error'] = $lang->def('_TEST_ERR_INSERT');
			}
			
			list($id_test) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			
			//Insert the question for the test
			
			$reQuest = mysql_query("
			SELECT q.idQuest, q.type_quest, t.type_file, t.type_class 
			FROM ".$GLOBALS['prefix_lms']."_bankquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
			WHERE q.idQuest IN (".implode(',', $quest_selection).") AND q.type_quest = t.type_quest");
			
			while( list($idQuest, $type_quest, $type_file, $type_class) = mysql_fetch_row($reQuest) )
			{
				require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
				$quest_obj = new $type_class( $idQuest );
				$new_id = $quest_obj->copy($id_test, NULL, true);
			}
			
			//Adding the item to the tree
			
			require_once($GLOBALS['where_lms'].'/modules/organization/orglib.php');
			
			$odb= new OrgDirDb($_SESSION['idCourse']);
			
			$odb->addItem(0, $_POST['title'], 'test', $id_test, '0', '0', getLogUserId(), '1.0', '_MEDIUM', '', '', '', '', date('Y-m-d H:i:s'));
			
			questbank($url);
		}
		else
		{
			if(is_array($quest_selection) && !empty($quest_selection))
			{
			require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
			
			cout(	getTitleArea('quest_bank')
					.'<div class="std_block yui-skin-docebo yui-skin-sam">');
			
			$form = new Form();
			
			cout(	$form->openForm('search_form', $url->getUrl(), false, 'POST')
					.$form->getHidden('new_test_step', 'new_test_step', '2')
					.$form->getHidden('export_quest', 'export_quest', $lang->def('_EXPORT'))
					.$form->getHidden('export_quest_select', 'export_quest_select', $file_format)
					.$form->getHidden('quest_category', 'quest_category', $quest_category)
					.$form->getHidden('quest_difficult', 'quest_difficult', $quest_difficult)
					.$form->getHidden('quest_type', 'quest_type', $quest_type)
					.$form->getHidden('selected_quest', 'selected_quest', $_POST['selected_quest'])
					.$form->openElementSpace()
					.$form->getTextfield($lang->def('_TITLE'), 'title', 'title', '255')
					.$form->getTextarea($lang->def('_DESCRIPTION'), 'textof', 'textof')
					.$form->closeElementSpace()
					.$form->openButtonSpace()
					.$form->getButton('button_ins', 'button_ins', $lang->def('_TEST_INSERT'))
					.$form->closeButtonSpace()
					.$form->closeForm());
			}
			else
			{
				$_SESSION['last_error'] = $lang->def('_SELECTION_EMPTY');
				questbank($url);
			}
		}
	}
	else
	{
		$quests = $qb_man->getQuestFromId($quest_selection);
		$quest_export = $qb_man->export_quest($quests, $file_format, true);
		
		require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
		sendStrAsFile( $quest_export, 'export_'.date("Y-m-d").'.txt' );
	}
}

/**
 * del question from question bank
 *
 */
function delquest() {
	checkPerm('view', false, 'storage');
	
	$lang =& DoceboLanguage::createInstance('test');
	
	$idQuest = importVar('idQuest', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$url_coded = htmlentities(urlencode($back_url));
	
	list($idTest, $title_quest, $type_quest, $seq) = mysql_fetch_row(mysql_query("
	SELECT idTest, title_quest, type_quest, sequence 
	FROM ".$GLOBALS['prefix_lms']."_testquest 
	WHERE idQuest = '".$idQuest."'"));
	
	if(isset($_GET['confirm'])) {
		
		$quest_obj = istanceQuest($type_quest, $idQuest);
		if(!$quest_obj->del()) {

			errorCommunication($lang->def('_TEST_ERR_QUESTREM').'index.php?modname=test&amp;op=delquest&amp;idTest='.$idTest.'&amp;back_url='
				.$url_coded, $lang->def("_BACK") );
			return;
		}
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_testquest 
		SET sequence = sequence -1 
		WHERE sequence > '$seq'");
		fixPageSequence($idTest);
		jumpTo( 'index.php?modname=test&op=modtestgui&idTest='.$idTest.'&back_url='.$url_coded);
	}
	else {
		$GLOBALS['page']->add(
			'<div class="std_block">'
			.getDeleteUi(	$lang->def('_TEST_AREYOUSURE'), 
							'<span class="text_bold">'.$lang->def('_TYPE').' : </span>'
							.$lang->def('_QUEST_ACRN_'.strtoupper($type_quest)).' - '.$lang->def('_QUEST_'.strtoupper($type_quest)).'<br />'
							.'<span class="text_bold">'.$lang->def('_TEST_QUEST_TITLE').' : </span>'.$title_quest, 
							
							true,
							'index.php?modname=test&amp;op=delquest&amp;idQuest='.$idQuest.'&amp;back_url='.$url_coded.'&amp;confirm=1', 
							'index.php?modname=test&amp;op=modtestgui&amp;idTest='.$idTest.'&amp;back_url='.$url_coded
						)
			.'</div>', 'content');			
	}
}

function questbankDispatch($op) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url =& UrlManager::getInstance();
	$url->setStdQuery('modname=quest_bank&op=main');
	
	if(isset($_POST['undo'])) $op = 'main';
	if(isset($_POST['import_quest'])) $op = 'importquest';
	if(isset($_POST['export_quest'])) $op = 'exportquest';
	
	switch($op) {
		case "addquest" : {
			addquest($url);
		};break;
		case "modquest" : {
			modquest($url);
		};break;
		
		case "importquest" : {
			importquest($url);
		};break;
		case "doimportquest" : {
			doimportquest($url);
		};break;
		
		case "exportquest" : {
			exportquest($url);
		};break;
		
		case "main" : 
		default: {
			questbank($url);
		}
	}
}

?>