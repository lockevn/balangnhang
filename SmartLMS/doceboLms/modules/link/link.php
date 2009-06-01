<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Fabio Pirovano (gishell@tiscali.it)             */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if(!$GLOBALS['current_user']->isAnonymous()) {

// XXX: modlinkgui
function modlinkgui( $object_link ) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$del_perm = checkPerm('view', false, 'storage');
	
	$back_coded = htmlentities(urlencode($object_link->back_url));
	
	$textQuery = "
	SELECT idLink, title, link_address, sequence 
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idCategory = '".$object_link->getId()."' 
	ORDER BY sequence";
	$result = mysql_query($textQuery);
	$num_link = mysql_num_rows($result);
	
	list($title_cat) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_lms']."_link_cat 
	WHERE idCategory = '".$object_link->getId()."'"));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_LINK'), 'link')
		.'<div class="std_block">'		
		.getBackUi( ereg_replace('&', '&amp;', $object_link->back_url).'&amp;mod_result=0', $lang->def('_BACK') )
		
		.'<span class="title">'.$lang->def('_TITLE').' : </span>'.$title_cat
		.'<div class="mod_container">'
		.'<a href="index.php?modname=link&amp;op=modlinkcat&amp;idCategory='.$object_link->getId()
			.'&amp;back_url='.$back_coded.'" title="'.$lang->def('_MODLINKCATT').'">'
		.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /> '.$lang->def('_MODLINKCAT').'</a>'
		.'</div><br />', 'content');
	$tableCat = new typeOne(0, '', $lang->def('_SUMMARY_LINK'));
	
	$contentH = array($lang->def('_QUESTION'), $lang->def('_URL'), 
		'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" title="'.$lang->def('_MOVE_DOWN').'" />',
		'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" title="'.$lang->def('_MOVE_UP').'" />',
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_MOD').'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_REMOVET').'" />');
	$typeH = array('', 'image', 'image', 'image', 'image', 'image');
	$tableCat->setColsStyle($typeH);
	$tableCat->addHead($contentH);
	
	$i = 1;
	while(list($idLink, $title, $link_address, $seq) = mysql_fetch_row($result)) {
		$rowContent = array($seq.') '.$title, $link_address);
		if($i != $num_link) {
			$rowContent[] = '<a href="index.php?modname=link&amp;op=movedown&amp;idLink='
				.$idLink.'&amp;back_url='.$back_coded.'">'
				.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" title="'.$lang->def('_MOVE_DOWN').'" /></a>';
		}
		else $rowContent[] = '&nbsp;';
		if($i != 1){
			$rowContent[] = '<a href="index.php?modname=link&amp;op=moveup&amp;idLink='
				.$idLink.'&amp;back_url='.$back_coded.'">'
				.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" title="'.$lang->def('_MOVE_UP').'" /></a>';
		}
		else $rowContent[] = '&nbsp;';
		$rowContent[] = '<a href="index.php?modname=link&amp;op=modlink&amp;idLink='
			.$idLink.'&amp;back_url='.$back_coded.'">'
			.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_MOD').'" /></a>';
		$rowContent[] = '<a href="index.php?modname=link&amp;op=dellink&amp;idLink='
				.$idLink.'&amp;back_url='.$back_coded.'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_REMOVET').'" /></a>';
		
		$tableCat->addBody($rowContent);
		++$i;
	}
	
	$tableCat->addActionAdd('<a href="index.php?modname=link&amp;op=newlink&amp;idCategory='
			.$object_link->getId().'&amp;back_url='.$back_coded.'" title="'.$lang->def('_ADDLINKT').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '.$lang->def('_ADDLINK').'</a>');
	
	$GLOBALS['page']->add($tableCat->getTable()
		.'<span class="text_bold text_little">[ '
		.'<a href="index.php?modname=link&amp;op=fixsequence&amp;idCategory='.$object_link->getId()
			.'&amp;back_url='.$back_coded.'">'.$lang->def('_FIXSEQUENCE').'</a>'
		.' ]</span>'
		.'</div>', 'content');
}

// XXX: addlinkcat
function addlinkcat( $object_link ) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_LINK'), 'link')
		.'<div class="std_block">'
		.Form::openForm('faqform', 'index.php?modname=link&amp;op=inslinkcat')
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_link->back_url)))
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $lang->def('_TITLE'))
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $lang->def('_DESCRIPTION'))
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addlinkcat', 'addlinkcat', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: inslinkcat
function inslinkcat() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	$back_url = urldecode($_POST['back_url']);
	
	$query_ins = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_link_cat
	SET title = '".( (trim($_POST['title']) == '') ? $lang->def('_NOTITLE') : $_POST['title'])."',
		description = '".$_POST['description']."',
		author = '".(int)getLogUserId()."'";
	if(!mysql_query($query_ins)) {
		$_SESSION['last_error'] = $lang->def('_ERRINSLINKCAT');
		jumpTo( ''.$back_url.'&create_result=0');
	}
	list($idLink) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	jumpTo( ''.$back_url.'&id_lo='.$idLink.'&create_result=1');
}

// XXX: modlinkcat
function modlinkcat() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$idCategory = importVar('idCategory', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	list($title, $descr) = mysql_fetch_row(mysql_query("
	SELECT title, description
	FROM ".$GLOBALS['prefix_lms']."_link_cat 
	WHERE idCategory = '".$idCategory."'"));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_LINK'), 'link')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=link&amp;op=modlinkgui&amp;idCategory='
			.$idCategory.'&amp;back_url='.$back_coded, $lang->def('_BACK'))
		
		.Form::openForm('faqform', 'index.php?modname=link&amp;op=uplinkcat')
		.Form::openElementSpace()
		.Form::getHidden('idCategory', 'idCategory', $idCategory)
		.Form::getHidden('back_url', 'back_url', $back_coded)
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $descr)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addlinkcat', 'addlinkcat', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: uplinkcat
function uplinkcat() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	$query_ins = "
	UPDATE ".$GLOBALS['prefix_lms']."_link_cat
	SET title = '".( (trim($_POST['title']) == '') ? $lang->def('_NOTITLE') : $_POST['title'])."',
		description = '".$_POST['description']."' 
	WHERE idCategory = '".(int)$_POST['idCategory']."'";
	if(!mysql_query($query_ins)) {
		$GLOBALS['page']->add(getErrorUi($lang->def('_ERRINSLINKCAT')
						.getBackUi('index.php?modname=link&op=modlinkgui&idCategory='
						.(int)$_POST['idCategory'].'&back_url='.$back_coded, $lang->def('_BACK'))), 'content');
		return;
	}
	
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	Track_Object::updateObjectTitle($_POST['idCategory'], 'link', $_POST['title']);
	
	jumpTo( 'index.php?modname=link&op=modlinkgui&idCategory='.(int)$_POST['idCategory'].'&back_url='.$back_coded);
}

// XXX: movelink
function movelink($direction) {
	checkPerm('view', false, 'storage');
	
	$back_url = urldecode( $_GET['back_url'] );
	$back_coded = htmlentities(urlencode( $back_url ));
	
	list($idCategory, $seq) = mysql_fetch_row(mysql_query("
	SELECT idCategory, sequence
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idLink = '".(int)$_GET['idLink']."'"));
	
	if($direction == 'up') {
		if($seq == 0) return;
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_link 
		SET sequence = '$seq' 
		WHERE idCategory = '".$idCategory."' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_link 
		SET sequence = sequence - 1 
		WHERE idLink = '".(int)$_GET['idLink']."'");
		
	}
	if($direction == 'down') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_link 
		SET sequence = '$seq' 
		WHERE idCategory = '".$idCategory."' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_link 
		SET sequence = '".($seq + 1)."' 
		WHERE idLink = '".(int)$_GET['idLink']."'");
	}
	jumpTo( 'index.php?modname=link&op=modlinkgui&idCategory='.$idCategory.'&back_url='.$back_coded);
}

// XXX: fixsequence
function fixsequence() {
	checkPerm('view', false, 'storage');
	
	$back_url = urldecode( $_GET['back_url'] );
	$back_coded = htmlentities(urlencode( $back_url ));
	
	$reQuest = mysql_query("
	SELECT idLink
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idCategory = '".(int)$_GET['idCategory']."'
	ORDER BY sequence");
	
	$i = 1;
	while(list($idLink) = mysql_fetch_row($reQuest)) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_link 
		SET sequence = '".($i++)."' 
		WHERE idLink = '$idLink'");
	}
	
	jumpTo( 'index.php?modname=link&op=modlinkgui&idCategory='.$_GET['idCategory'].'&back_url='.$back_coded);
}

// XXX: newlink
function newlink() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$idCategory = importVar('idCategory', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_LINK'), 'link')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=link&op=modlinkgui&idCategory='
			.$idCategory.'&back_url='.$back_coded, $lang->def('_BACK'))
		
		.Form::openForm('faqform', 'index.php?modname=link&amp;op=inslink')
		.Form::openElementSpace()
		.Form::getHidden('idCategory', 'idCategory', $idCategory)
		.Form::getHidden('back_url', 'back_url', $back_coded)
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $lang->def('_TITLE'))
		.Form::getTextfield($lang->def('_LINK_ADDRESS'), 'link_a', 'link_a', 255, 'http://')
		.Form::getSimpleTextarea($lang->def('_KEYWORD'), 'keyword', 'keyword', $lang->def('_KEYWORD'))
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $lang->def('_DESCRIPTION'))
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addlink', 'addlink', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: inslink
function inslink() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	$idCategory = importVar('idCategory', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	list($seq) = mysql_fetch_row(mysql_query("
	SELECT MAX(sequence)
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idCategory = '".$idCategory."'"));
	
	$query_ins = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_link
	SET idCategory = '".$idCategory."',
		title = '".$_POST['title']."',
		link_address = '".$_POST['link_a']."',
		keyword = '".$_POST['keyword']."',
		description = '".$_POST['description']."',
		sequence = '".($seq + 1)."'";
	if(!mysql_query($query_ins)) {
		$GLOBALS['page']->add(getErrorUi($lang->def('_ERRINSLINK')
			.getBackUi('index.php?modname=link&op=modlinkgui&idCategory='
			.$idCategory.'&back_url='.$back_coded, $lang->def('_BACK'))), 'content');
		return;
	}
	jumpTo( 'index.php?modname=link&op=modlinkgui&idCategory='.$idCategory.'&back_url='.$back_coded);
}
//DEBUG: arrivato qui
// XXX: modlink
function modlink() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$back_url = urldecode($_GET['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));
	
	list($idCategory, $title, $link_a, $keyword, $description) = mysql_fetch_row(mysql_query("
	SELECT idCategory, title, link_address, keyword, description 
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idLink = '".(int)$_GET['idLink']."'"));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_LINK'), 'link')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=link&op=modlinkgui&idCategory='
			.$idCategory.'&back_url='.$back_coded, $lang->def('_BACK'))
		
		.Form::openForm('faqform', 'index.php?modname=link&amp;op=uplink')
		.Form::openElementSpace()
		.Form::getHidden('idLink', 'idLink', $_GET['idLink'])
		.Form::getHidden('back_url', 'back_url', $back_coded)
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
		.Form::getTextfield($lang->def('_LINK_ADDRESS'), 'link_a', 'link_a', 255, $link_a)
		.Form::getSimpleTextarea($lang->def('_KEYWORD'), 'keyword', 'keyword', $keyword)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $description)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('modlink', 'modlink', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: uplink
function uplink() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	$back_url = urldecode($_POST['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));	
	
	list($idCategory) = mysql_fetch_row(mysql_query("
	SELECT idCategory 
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idLink = '".(int)$_POST['idLink']."'"));
	
	$query_ins = "
	UPDATE ".$GLOBALS['prefix_lms']."_link
	SET title = '".$_POST['title']."',
		link_address = '".$_POST['link_a']."',
		keyword = '".$_POST['keyword']."',
		description = '".$_POST['description']."'
	WHERE idLink = '".(int)$_POST['idLink']."'";
	if(!mysql_query($query_ins)) {
		$GLOBALS['page']->add(getErrorUi($lang->def('_ERRINSLINK')
			.getBackUi('index.php?modname=link&op=modlinkgui&idCategory='
			.$idCategory.'&back_url='.$back_coded, $lang->def('_BACK'))), 'content');
		return;
	}
	jumpTo( 'index.php?modname=link&op=modlinkgui&idCategory='.$idCategory.'&back_url='.$back_coded);
}

// XXX: dellink
function dellink() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('link');
	
	$back_url = urldecode($_GET['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));	
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_LINK'), 'link'), 'content');
	if( isset($_GET['confirm']) ) {
		list($idCategory, $seq) = mysql_fetch_row(mysql_query("
		SELECT idCategory, sequence
		FROM ".$GLOBALS['prefix_lms']."_link 
		WHERE idLink = '".(int)$_GET['idLink']."'"));
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_link 
		WHERE idLink  = '".(int)$_GET['idLink']."'")) {
			$GLOBALS['page']->add(getErrorUi($lang->def('_ERRREMLINK').getBackUi('index.php?modname=link&op=modlinkgui&idCategory='
				.$idCategory.'&back_url='.$back_coded, $lang->def('_BACK'))), 'content');
			return;
		}
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_link 
		SET sequence = sequence -1
		WHERE sequence > '".$seq."'");
		
		jumpTo( 'index.php?modname=link&op=modlinkgui&idCategory='.$idCategory.'&back_url='.$back_coded);
	}
	else {
		list($idCategory, $title, $link_a, $description) = mysql_fetch_row(mysql_query("
		SELECT idCategory, title, link_address, description 
		FROM ".$GLOBALS['prefix_lms']."_link 
		WHERE idLink = '".(int)$_GET['idLink']."'"));
		
		$GLOBALS['page']->add('<div class="std_block">'
			.getDeleteUi(	$lang->def('_AREYOUSURELINK'),
							'<span>'.$lang->def('_TITLE').' : </span>'.$title.'<br />'
								.'<span>'.$lang->def('_URL').' : </span>'.$link_a.'<br />'
								.'<span>'.$lang->def('_DESCRIPTION').' : </span>'.$description, 
							true, 
							'index.php?modname=link&amp;op=dellink&amp;idLink='.$_GET['idLink'].'&amp;back_url='
								.$back_coded.'&amp;confirm=1', 
							'index.php?modname=link&amp;op=modlinkgui&amp;idCategory='.$idCategory.'&amp;back_url='
								.$back_coded.''
						)
			.'</div>'
			.'</div>', 'content');
	}
}

// XXX: switch
if(isset($GLOBALS['op'])) switch($GLOBALS['op']) {
	case "modlinkgui" : {
		$idCategory = importVar('idCategory', true, 0);
		$back_url = importVar('back_url');
		
		$object_link= createLO( 'link', $idCategory );
		$object_link->edit( $idCategory, urldecode( $back_url ) );
	};break;
	//add category
	case "addlinkcat" : {
		addlinkcat();
	};break;
	case "inslinkcat" : {
		inslinkcat();
	};break;
	//mod category
	case "modlinkcat" : {
		modlinkcat();
	};break;
	case "uplinkcat" : {
		uplinkcat();
	};break;
	//mod
	case "movedown" : {
		movelink('down');
	};break;
	case "moveup" : {
		movelink('up');
	};break;
	case "fixsequence" : {
		fixsequence();
	};break;
	//add link
	case "newlink" : {
		newlink();
	};break;
	case "inslink" : {
		inslink();
	};break;
	//mod link
	case "modlink" : {
		modlink();
	};break;
	case "uplink" : {
		uplink();
	};break;
	//del link
	case "dellink" : {
		dellink();
	};break;
}

}

?>
