<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Fabio Pirovano (gishell@tiscali.it)             */
/* http://www.docebolms.org						 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous())  die('You can\'t access');

// XXX: save status in session
function savePollStatus($save_this) {
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php' );
	$save = new Session_Save();
	$save_name = $save->getName('poll');
	
	$save->save($save_name, $save_this);
	return $save_name;
}

function &loadPollStatus($save_name) {
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php' );
	$save = new Session_Save();
	
	return $save->load($save_name);
}

// XXX: addpoll
function addpoll( $object_poll ) {
	checkPerm('view', false, 'storage');
	
	$lang =& DoceboLanguage::createInstance('poll');
	if( !is_a($object_poll, 'Learning_Poll') ) {
		$_SESSION['last_error'] = $lang->def('_POLL_INCORRECTOBJECT');
		jumpTo( ''.$object_poll->back_url.'&amp;create_result=0');
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php' );
	$url_encode = htmlentities(urlencode($object_poll->back_url));
	
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_POLL_SECTION'), 'poll')
		.'<div class="std_block">'
		.getBackUi(ereg_replace('&', '&amp;', $object_poll->back_url).'&amp;create_result=0', $lang->def('_BACK'))
		.Form::getFormHeader($lang->def('_POLL_ADD_FORM'))
		.Form::OpenForm('addpoll_form', 'index.php?modname=poll&amp;op=inspoll')
		
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_poll->back_url)))

		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', '255')
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'textof', 'textof')
		
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('button_ins', 'button_ins', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: inspoll
function inspoll() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	if( trim($_POST['title']) == '' ) $_POST['title'] = $lang->def('_NOTITLE');
	
	$ins_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_poll 
	( author, title, description )
		VALUES 
	( '".(int)getLogUserId()."', '".$_POST['title']."', '".$_POST['textof']."' )";
	
	if( !mysql_query($ins_query) ) {
		
		$_SESSION['last_error'] = $lang->def('_POLL_ERR_INSERT');
		jumpTo( ''.urldecode($_POST['back_url']).'&create_result=0' );
	}
	
	list($id_poll) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	if($id_poll > 0) jumpTo( ''.urldecode($_POST['back_url']).'&id_lo='.$id_poll.'&create_result=1' );
	else jumpTo( ''.urldecode($_POST['back_url']).'&create_result=0' );
}

// XXX: modpoll
function modpoll() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$id_poll = importVar('id_poll', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$url_encode = htmlentities(urlencode($back_url));
	
	list($poll_title, $textof) = mysql_fetch_row( mysql_query("
	SELECT title, description
	FROM ".$GLOBALS['prefix_lms']."_poll
	WHERE id_poll = '".$id_poll."'") );
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_POLL_SECTION'), 'poll')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=poll&amp;op=modpollgui&amp;id_poll='.$id_poll.'&amp;back_url='.$url_encode, $lang->def('_BACK'))
		.Form::getFormHeader($lang->def('_POLL_MOD_FORM'))
		.Form::OpenForm('addpoll_form', 'index.php?modname=poll&amp;op=uppoll')
		
		.Form::openElementSpace()
		.Form::getHidden('id_poll', 'id_poll', $id_poll)
		.Form::getHidden('back_url', 'back_url', $url_encode)
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', '255', $poll_title)
		.Form::getTextarea( $lang->def('_DESCRIPTION'), 'textof', 'textof', $textof)

		.Form::closeElementSpace()
	
		.Form::openButtonSpace()
		.Form::getButton('button_ins', 'button_ins', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
	
		.Form::closeForm()
		.'</div>', 'content');
}


// XXX: uppoll
function uppoll() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	if( trim($_POST['title']) == '' ) $_POST['title'] = $lang->def('_NOTITLE');
	
	$id_poll = importVar('id_poll', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$url_encode = htmlentities(urlencode($back_url));
	
	$mod_query = "
	UPDATE ".$GLOBALS['prefix_lms']."_poll
	SET title = '".$_POST['title']."', 
		description = '".$_POST['textof']."' 
	WHERE id_poll = '".$id_poll."'";
	
	if( !mysql_query($mod_query) ) {
		
		errorCommunication($lang->def('_ERR_UPD_POLL')
			.getBackUi('index.php?modname=poll&amp;op=modpoll&amp;id_poll='.$id_poll.'&amp;back_url='.$url_encode));
		return;
	}
	
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	Track_Object::updateObjectTitle($id_poll, 'poll', $_POST['title']);
	
	jumpTo( 'index.php?modname=poll&op=modpollgui&id_poll='.$id_poll.'&back_url='.$url_encode );
}

// XXX: modpollgui
function modpollgui( $object_poll ) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	
	if( !is_a($object_poll, 'Learning_Poll') ) {
		$_SESSION['last_error'] = $lang->def('_POLL_INCORRECTOBJECT');
		jumpTo( ''.$object_poll->back_url.'&amp;create_result=0');
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$url_encode = htmlentities(urlencode($object_poll->back_url));
	
	list($poll_title) = mysql_fetch_row( mysql_query("
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_poll 
	WHERE id_poll = '".$object_poll->getId()."'") );
	$re_quest = mysql_query("
	SELECT id_quest, type_quest, title_quest, sequence, page 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_poll = '".$object_poll->getId()."'
	ORDER BY sequence");
	
	$num_quest = mysql_num_rows($re_quest);
	list($num_page) = mysql_fetch_row(mysql_query("
	SELECT MAX(page) 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_poll = '".$object_poll->getId()."'"));
	$num_page = (int)$num_page;
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_POLL_SECTION'), 'poll')
		.'<div class="std_block">'
		.getBackUi(ereg_replace('&','&amp;', $object_poll->back_url), $lang->def('_BACK')), 'content');
	if(isset($_GET['mod_operation'])) {
		if($_GET['mod_operation']) $GLOBALS['page']->add(getResultUi($lang->def('_QUEST_MOD_OK')), 'content');
		else $GLOBALS['page']->add(getResultUi($lang->def('_QUEST_ERR_MODIFY')), 'content');
	}
	//other areas
	$GLOBALS['page']->add('<span class="text_bold">'.$lang->def('_TITLE').' : </span>'.$poll_title.'<br />'
		.'<div class="mod_container">'
		.'<a href="index.php?modname=poll&amp;op=modpoll&amp;id_poll='
		.$object_poll->getId().'&amp;back_url='.$url_encode.'" title="'.$lang->def('_REG_MODPOLL_TITLE').'">'
		.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" />&nbsp;'.$lang->def('_MOD').'</a>'
		.'</div><br />', 'content');
	
	$caption = str_replace('[tot_page]' , $num_page, str_replace('[tot_element]' ,$num_quest , $lang->def('_POLL_CAPTION')));
	
	$tab = new TypeOne( 0, $caption, $lang->def('_POLL_SUMMARY'));
 
	$tab->setColsStyle(array('image', 'image', '', 'image', 'image', 'image', 'image', 'image'));
	$tab->addHead(
		array($lang->def('_QUEST'), $lang->def('_TYPE'), $lang->def('_TITLE'), $lang->def('_POLL_QUEST_ORDER'), 
		'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" longdesc="'.$lang->def('_DOWN').'" />', 
		'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" longdesc="'.$lang->def('_UP').'" />', 
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" longdesc="'.$lang->def('_MOD').'" />', 
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" longdesc="'.$lang->def('_POLL_REMPOLL').'" />' ));
	$i = 0;
	$quest_num = 1;
	$title_num = 1;
	$last_type = '';
	$uri_back = '&amp;back_url='.$url_encode;
	while(list($id_quest, $type, $title, $sequence, $page) = mysql_fetch_row($re_quest)) {
		
		$last_type = $type;
		$content = array(
		( (($type != 'break_page') && ($type != 'title')) ? '<span class="text_bold">'.($quest_num++).'</span>' : '' ),
		$lang->def('_QUEST_ACRN_'.strtoupper($type)),
		$title,
		$sequence, 
		( ($i != ($num_quest - 1)) ? 
			'<a href="index.php?modname=poll&amp;op=movedown&amp;id_quest='.$id_quest.$uri_back.'" title="'.$lang->def('_DOWN').'">'
				.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').' : '.$lang->def('_POLL_ROWS').' '.($i + 1).'" longdesc="'.$lang->def('_DOWN').'" /></a>' : '' ),
		( ($i != 0) ? 
			'<a href="index.php?modname=poll&amp;op=moveup&amp;id_quest='.$id_quest.$uri_back.'" title="'.$lang->def('_UP').'">'
				.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').' : '.$lang->def('_POLL_ROWS').' '.($i + 1).'" longdesc="'.$lang->def('_UP').'" /></a>' : '' ),
				
		( $type != 'break_page' ? '<a href="index.php?modname=poll&amp;op=modquest&amp;id_quest='.$id_quest.$uri_back.'" title="'.$lang->def('_MOD').'">'
			.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$lang->def('_POLL_ROWS').' '.($i + 1).'" longdesc="'.$lang->def('_MOD').'" /></a>' : '' ),
		'<a href="index.php?modname=poll&amp;op=delquest&amp;id_quest='.$id_quest.$uri_back.'" title="'.$lang->def('_POLL_REMPOLL').'">'
			.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$lang->def('_POLL_ROWS').' '.($i + 1).'" longdesc="'.$lang->def('_POLL_REMPOLL').'" /></a>',
		);
		
		$tab->addBody($content);
		++$i;
	}
	
	//------------------------------------------------------------------
	if($num_quest > 1) {
		$move_quest = '<form class="align_right" method="post" action="index.php?modname=poll&amp;op=movequest">'
			.'<div>'
			.'<input type="hidden" name="back_url" value="'.$url_encode.'" />'
			.'<input type="hidden" name="id_poll" value="'.$object_poll->getId().'" />';
		$move_quest .= '<label class="text_bold" for="source_quest">'.$lang->def('_POLL_MOVE_QUEST').'</label>&nbsp;'
			.'<select id="source_quest" name="source_quest">';
		for( $opt = 1; $opt <= $i; $opt++ ) {
			$move_quest .= '<option value="'.$opt.'"'
				.( $opt == 1 ? ' selected="selected"' : '' ).'>'.$lang->def('_POLL_MOVEQUEST').' '.$opt.'</option>';
		}
		$move_quest .= '</select>';
		$move_quest .= '<label class="text_bold" for="dest_quest"> '.$lang->def('_POLL_LABEL_AFTER_QUEST').'</label>&nbsp;'
			.'<select id="dest_quest" name="dest_quest">'
			.'<option value="1" selected="selected">'.$lang->def('_POLL_FIRST_QUEST').'</option>';
		for( $opt = 1; $opt < $i; $opt++ ) {
			$move_quest .= '<option value="'.($opt + 1).'">'.$lang->def('_POLL_AFTER_QUEST').' '.$opt.'</option>';
		}
		$move_quest .= '<option value="'.($i + 1).'">'.$lang->def('_POLL_LAST_QUEST').'</option>';
		$move_quest .= '</select>';
		$move_quest .= '&nbsp;<input class="button_nowh" type="submit" name="move_quest" value="'.$lang->def('_ALT_MOVE').'" />'
			.'</div>'
			.'</form>';
		$tab->addActionAdd( $move_quest );
	}
	//------------------------------------------------------------------
	$re_type = mysql_query("
	SELECT type_quest 
	FROM ".$GLOBALS['prefix_lms']."_quest_type_poll 
	ORDER BY sequence");
	$add_quest = '<form method="post" action="index.php?modname=poll&amp;op=addquest">'
		.'<div>'
		.'<input type="hidden" name="back_url" value="'.$url_encode.'" />'
		.'<input type="hidden" name="id_poll" value="'.$object_poll->getId().'" />';
	$add_quest .= '<label class="text_bold" for="add_poll_quest">'.$lang->def('_POLL_ADDQUEST').'</label>&nbsp;'
		.'<select id="add_poll_quest" name="add_poll_quest">';
	while(list($type_quest) = mysql_fetch_row($re_type)) {
		$add_quest .= '<option value="'.$type_quest.'"'
		.( $last_type == $type_quest ? ' selected="selected"' : '' ).'>'
		.$lang->def('_QUEST_ACRN_'.strtoupper($type_quest)).' - '.$lang->def('_QUEST_'.strtoupper($type_quest)).'</option>';
	}
	$add_quest .= '</select>';
	$add_quest .= '&nbsp;<input class="button_nowh" type="submit" name="add_quest" value="'.$lang->def('_ADD').'" />'
		.'</div>'
		.'</form>';
	//------------------------------------------------------------------
	$tab->addActionAdd( $add_quest );
	$GLOBALS['page']->add(
		$tab->getTable()
	, 'content');	
	
	$GLOBALS['page']->add(
		' <a href="index.php?modname=poll&amp;op=fixsequence&amp;id_poll='.$object_poll->getId().$uri_back.'">'
		.$lang->def('_SEQUENCE_ERROR', 'test').'</a>'
	, 'content');
	
	$GLOBALS['page']->add(
		getBackUi(ereg_replace('&','&amp;', $object_poll->back_url), $lang->def('_BACK'))
		.'</div>'
	, 'content');
	//fixPageSequence($object_poll->getId());
}

// XXX: movequestion
function movequestion($direction) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	
	$id_quest = importVar('id_quest', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	list($seq, $id_poll) = mysql_fetch_row(mysql_query("
	SELECT sequence, id_poll 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_quest = '$id_quest'"));
	
	if($direction == 'up') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = '$seq' 
		WHERE id_poll = '$id_poll' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest  
		SET sequence = sequence - 1 
		WHERE id_poll = '$id_poll' AND id_quest = '$id_quest'");
		
	}
	if($direction == 'down') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = '$seq' 
		WHERE id_poll = '$id_poll' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = '".($seq + 1)."' 
		WHERE id_poll = '$id_poll' AND id_quest = '$id_quest'");
	}
	fixPageSequence($id_poll);
	jumpTo( 'index.php?modname=poll&op=modpollgui&id_poll='.$id_poll.'&back_url='.$back_coded);
}

// XXX: movequestion from to
function movequest() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	
	$id_poll = importVar('id_poll', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	$source_seq = importVar('source_quest', true, 0);
	$dest_seq = importVar('dest_quest', true, 0);
	
	list($id_quest) = mysql_fetch_row(mysql_query("
	SELECT id_quest 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_poll = '$id_poll' AND sequence = '$source_seq'"));
	
	if($source_seq < $dest_seq) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = sequence - 1 
		WHERE id_poll = '$id_poll' AND sequence > '".($source_seq)."'  AND sequence < '".($dest_seq)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = '".($dest_seq - 1)."' 
		WHERE id_quest = '$id_quest'");
	} else {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = sequence + 1 
		WHERE id_poll = '$id_poll' AND sequence >= '".($dest_seq)."'  AND sequence < '".($source_seq)."'");
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = '$dest_seq' 
		WHERE id_quest = '$id_quest'");
	}
	fixPageSequence($id_poll);
	jumpTo( 'index.php?modname=poll&op=modpollgui&id_poll='.$id_poll.'&back_url='.$back_coded);
}

// XXX: fixPageSequence
function fixPageSequence($id_poll) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	list($tot_quest) = mysql_fetch_row(mysql_query("
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_poll = '".$id_poll."'"));
	
	$re_break_page = mysql_query("
	SELECT sequence 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_poll = '".$id_poll."' AND type_quest = 'break_page'
	ORDER BY sequence");
	
	$page_num = 1;
	//first page 
	$ini_seq = 0;
	while(list($break_sequence) = mysql_fetch_row($re_break_page)) {
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest
		SET page = '".(int)$page_num."'
		WHERE id_poll = '".(int)$id_poll."' AND 
			sequence > '".(int)$ini_seq."' AND sequence <= '".(int)$break_sequence."'");
		$ini_seq = $break_sequence;
		++$page_num;
	}
	mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_pollquest
	SET page = '".(int)$page_num."'
	WHERE id_poll = '".(int)$id_poll."' AND 
		sequence > '".(int)$ini_seq."' AND sequence <= '".(int)$tot_quest."'");
}

// XXX: fixSequence
function fixPollSequence($id_poll) {
checkPerm('view', false, 'storage');
	
	$id_poll = importVar('id_poll', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	$re_quest = mysql_query("
	SELECT id_quest, sequence 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_poll = '$id_poll' 
	ORDER BY page, sequence");
	$seq = 1;
	while(list($id_quest) = mysql_fetch_row($re_quest)) {
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = '$seq' 
		WHERE id_quest = '$id_quest'");
		$seq++;
	}
	jumpTo( 'index.php?modname=poll&op=modpollgui&id_poll='.$id_poll.'&back_url='.$back_coded);
}

function &istanceQuest( $type_of_quest, $id ) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	
	$re_quest = mysql_query("
	SELECT type_file, type_class 
	FROM ".$GLOBALS['prefix_lms']."_quest_type_poll _poll 
	WHERE type_quest = '".$type_of_quest."'");
	if( !mysql_num_rows($re_quest) ) return;
	list($type_file, $type_class) = mysql_fetch_row($re_quest);
	
	require_once( $GLOBALS['where_lms'].'/modules/question_poll/'.$type_file);
	$quest_obj = eval("return new $type_class ( $id );");
	
	return $quest_obj;
}

// XXX: addquest 
function addquest() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	$id_poll = importVar('id_poll', true, 0);
	
	if(isset($_POST['add_poll_quest'])) {
		//first enter
		$type_quest = importVar('add_poll_quest');
		$var_to_safe = array(
			'id_quest' => 0,
			'type_quest' => $type_quest,
			'id_poll' => $id_poll,
			'back_url' => urldecode(importVar('back_url'))
		);
		$var_save = savePollStatus($var_to_safe);
	}
	else {
		//other enter
		$var_save = importVar('poll_saved');
		$var_loaded = loadPollStatus($var_save);
		
		$id_poll = $var_loaded['id_poll'];
		$type_quest = $var_loaded['type_quest'];
	}
	
	require_once($GLOBALS['where_lms'].'/modules/question_poll/question_poll.php');
	
	quest_create($type_quest, $id_poll, 'index.php?modname=poll&op=modpollgui&poll_saved='.$var_save);
}

// XXX: modquest 
function modquest() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('poll');
	
	
	$id_quest = importVar('id_quest', true, 0);
	
	list($id_poll, $type_quest) = mysql_fetch_row(mysql_query("
	SELECT id_poll, type_quest 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_quest = '".$id_quest."'"));
	
	if(!isset($_POST['back_url'])) {
		//first enter
		$var_to_safe = array(
			'id_quest' => $id_quest,
			'type_quest' => $type_quest,
			'id_poll' => $id_poll,
			'back_url' => urldecode(importVar('back_url'))
		);
		$var_save = savePollStatus($var_to_safe);
	}
	else {
		//other enter
		$var_save = importVar('poll_saved');
		$var_loaded = loadPollStatus($var_save);
		
		$id_quest = $var_loaded['id_quest'];
		$type_quest = $var_loaded['type_quest'];
	}
	
	require_once($GLOBALS['where_lms'].'/modules/question_poll/question_poll.php');
	
	quest_edit($type_quest, $id_quest, 'index.php?modname=poll&op=modpollgui&poll_saved='.$var_save);
}

// XXX: deletequest
function delquest() {
	checkPerm('view', false, 'storage');
	
	$lang =& DoceboLanguage::createInstance('poll');
	
	$id_quest = importVar('id_quest', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$url_coded = htmlentities(urlencode($back_url));
	
	list($id_poll, $title_quest, $type_quest, $seq) = mysql_fetch_row(mysql_query("
	SELECT id_poll, title_quest, type_quest, sequence 
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_quest = '".$id_quest."'"));
	
	if(isset($_GET['confirm'])) {
		
		$quest_obj = istanceQuest($type_quest, $id_quest);
		if(!$quest_obj->del()) {

			errorCommunication($lang->def('_POLL_ERR_QUESTREM').'index.php?modname=poll&amp;op=delquest&amp;id_poll='.$id_poll.'&amp;back_url='
				.$url_coded, $lang->def("_BACK") );
			return;
		}
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
		SET sequence = sequence -1 
		WHERE sequence > '$seq'");
		fixPageSequence($id_poll);
		jumpTo( 'index.php?modname=poll&op=modpollgui&id_poll='.$id_poll.'&back_url='.$url_coded);
	}
	else {
		$GLOBALS['page']->add(
			'<div class="std_block">'
			.getDeleteUi(	$lang->def('_POLL_AREYOUSURE'), 
							'<span class="text_bold">'.$lang->def('_TYPE').' : </span>'
							.$lang->def('_QUEST_ACRN_'.strtoupper($type_quest)).' - '.$lang->def('_QUEST_'.strtoupper($type_quest)).'<br />'
							.'<span class="text_bold">'.$lang->def('_TITLE').' : </span>'.$title_quest, 
							
							true,
							'index.php?modname=poll&amp;op=delquest&amp;id_quest='.$id_quest.'&amp;back_url='.$url_coded.'&amp;confirm=1', 
							'index.php?modname=poll&amp;op=modpollgui&amp;id_poll='.$id_poll.'&amp;back_url='.$url_coded
						)
			.'</div>', 'content');
	}
}

// XXX: switch
switch($GLOBALS['op']) {
	case "inspoll" : {
		inspoll();
	};break;

	case "modpoll" : {
		modpoll();
	};break;
	case "uppoll" : {
		uppoll();
	};break;

	case "modpollgui" : {
		if( isset($_GET['poll_saved']) || isset($_POST['poll_saved']) ) {
			//other enter
			$var_save = importVar('poll_saved');
			$var_loaded = loadPollStatus($var_save);
			
			$id_poll = $var_loaded['id_poll'];
			$back_url = urlencode($var_loaded['back_url']);
			fixPageSequence($id_poll);
		}
		else {
			$id_poll = importVar('id_poll', true, 0);
			$back_url = importVar('back_url');
		}
		$object_poll = createLO('poll', $id_poll);
		$object_poll->edit($id_poll, urldecode($back_url));
	};break;
	
	case "fixsequence" : {
		fixPollSequence();
	};break;
	
	case "movequest" : {
		movequest();
	};break;
	
	case "movedown" : {
		movequestion('down');
	};break;
	case "moveup" : {
		movequestion('up');
	};break;
	
	case "addquest" : {
		addquest();
	};break;
	case "modquest" : {
		modquest();
	};break;
	case "delquest" : {
		delquest();
	};break;
	
}




?>