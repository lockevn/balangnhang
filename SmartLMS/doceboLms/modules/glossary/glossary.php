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

// XXX: mod glossary interface
function modglossarygui( $object_glos = NULL ) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('glossary');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$tableGlossary = new typeOne($GLOBALS['lms']['visuItem'], '', $lang->def('_GLOSSARY_SUMMARY'));
	
	$tableGlossary->initNavBar('ini', 'link');
	$ini = $tableGlossary->getSelectedElement();
	
	$back_coded = htmlentities(urlencode($object_glos->back_url));
	
	list($title) = mysql_fetch_row(mysql_query("
	SELECT title
	FROM ".$GLOBALS['prefix_lms']."_glossary 
	WHERE idGlossary = '".$object_glos->getId()."'"));
	
	$reTerm = mysql_query("
	SELECT idTerm, term 
	FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
	WHERE idGlossary = '".$object_glos->getId()."' 
	ORDER BY term 
	LIMIT $ini,".$GLOBALS['lms']['visuItem']);
	
	list($num_of_term) = mysql_fetch_row(mysql_query("
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_glossaryterm
	WHERE idGlossary = '".$object_glos->getId()."'"));
	
	if($title == '') {
		$_SESSION['last_error'] = $lang->def('_FILEUNSPECIFIED');
		jumpTo( ereg_replace('&', '&amp;', $object_glos->back_url).'&amp;create_result=0' );
	}
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_SECTIONNAME_GLOSS'), 'glossary', $lang->def('_IMGSECTION_GLOSS'))
		.'<div class="std_block">'
		.getBackUi( ereg_replace('&', '&amp;', $object_glos->back_url).'&amp;mod_result=0', $lang->def('_BACK') )
		.'<span class="title">'.$lang->def('_GLOSSARY').' : </span>'.$title
		.'<div class="mod_container">'
		.'<a href="index.php?modname=glossary&amp;op=modglossary&amp;idGlossary='.$object_glos->getId()
		.'&amp;back_url='.$back_coded.'" title="'.$lang->def('_MODGLOSSARY').'">'
		.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /> '.$lang->def('_MODGLOSSARY').'</a>'
		.'</div><br />'
	, 'content');
	
	$contentArray = array( 
		$lang->def('_TERM'),
		'<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_MODELEM').'" alt="'.$lang->def('_MOD').'" />',
		'<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_REMELEM').'" alt="'.$lang->def('_DEL').'" />'
	);
	$typeArray = array('', 'image', 'image');
	$GLOBALS['page']->add($tableGlossary->addHead($contentArray, $typeArray));
	while(list($idTerm, $term) = mysql_fetch_row($reTerm)) {
		
		$content = array( 
			$term, 
			
			'<a href="index.php?modname=glossary&amp;op=modterm&amp;idTerm='.$idTerm
			.'&amp;back_url='.$back_coded.'" title="'.$lang->def('_MODTERM').'">'
			.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /></a>', 
			
			'<a href="index.php?modname=glossary&amp;op=delterm&amp;idTerm='.$idTerm
			.'&amp;back_url='.$back_coded.'" title="'.$lang->def('_REMTERM').'">'
			.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" /></a>'
		);
		$tableGlossary->addBody($content);
	}
	$tableGlossary->addActionAdd('<a href="index.php?modname=glossary&amp;op=addterm&amp;idGlossary='.$object_glos->getId()
		.'&amp;back_url='.$back_coded.'" title="'.$lang->def('_ADDTERMT').'">'
		.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '.$lang->def('_ADDTERM').'</a>');
	
	$tableGlossary->setLink('index.php?modname=glossary&amp;op=modglossarygui'.'&amp;idGlossary='.$object_glos->getId()
			.'&amp;back_url='.$back_coded);
							
	$GLOBALS['page']->add(
		$tableGlossary->getTable()
		.$tableGlossary->getNavBar($ini, $num_of_term)
		.'</div>', 'content');
}

// XXX: addglossary (insert information for a new glossary)
function addglossary( $object_glos ) {
	checkPerm('view', false, 'storage');
	
	$lang =& DoceboLanguage::createInstance('glossary');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECTIONNAME_GLOSS'), 'glossary', $lang->def('_IMGSECTION_GLOSS'))
		.'<div class="std_block">'
		
		.getBackUi( ereg_replace('&', '&amp;', $object_glos->back_url).'&amp;create_result=0', $lang->def('_BACK') )
		
		.Form::openForm('glossaryform' ,'index.php?modname=glossary&amp;op=insglossary')
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_glos->back_url)))
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 150, $lang->def('_TITLE'))
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $lang->def('_DESCRIPTION'))
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addglossary', 'addglossary', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: insglossary (save new glossary)
function insglossary() {
	checkPerm('view', false, 'storage');

	if( $_POST['title'] == "" ) $_POST['title'] = def('_NOTITLE', 'glossary');
	
	$queryIns = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_glossary 
	SET title = '".$_POST['title']."',
		description = '".$_POST['description']."',
		author = '".(int)getLogUserId()."'";
	if(!mysql_query($queryIns)) {
		
		$_SESSION['last_error'] = def('_ERR_SAVE', 'glossary');
		jumpTo( urldecode($_POST['back_url']).'&create_result=0' );
	}
	list($id) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	
	jumpTo( urldecode($_POST['back_url']).'&id_lo='.$id.'&create_result=1' );
}

// XXX: modglossary (modify the infomation of a glossary) 
function modglossary() {
	checkPerm('view', false, 'storage');
	
	$lang =& DoceboLanguage::createInstance('glossary');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$idGlossary = importVar('idGlossary', true, 0);
	$back_url = urldecode(importVar('back_url'));
	$back_coded = htmlentities(urlencode($back_url));
	
	list($title, $description) = mysql_fetch_row(mysql_query("
	SELECT title, description
	FROM ".$GLOBALS['prefix_lms']."_glossary 
	WHERE idGlossary = '".$idGlossary."'"));
	
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECTIONNAME_GLOSS'), 'glossary', $lang->def('_IMGSECTION_GLOSS'))
		.'<div class="std_block">'
		
		.getBackUi( ereg_replace('&', '&amp;', $back_url).'&amp;create_result=0', $lang->def('_BACK') )
		
		.Form::openForm('glossaryform' ,'index.php?modname=glossary&amp;op=upglossary')
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', $back_coded)
		.Form::getHidden('idGlossary', 'idGlossary', $idGlossary)
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 150, $title)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $description)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('modglossary', 'modglossary', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: upglossary (update new glossary info)
function upglossary() {
	checkPerm('view', false, 'storage');
	
	$back_url = urldecode($_POST['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));
	
	if ($_POST['title'] == "") $_POST['title'] = def('_NOTITLE', 'glossary');
	
	if(!mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_glossary 
	SET title='".$_POST['title']."',
	description='".$_POST['description']."'
	WHERE idGlossary='".(int)$_POST['idGlossary']."'")) {
		
		//error while inserting
		$GLOBALS['page']->add(getErrorUi(
						def('_ERR_SAVE', 'glossary')
						.getBackUi('index.php?modname=glossary&amp;op=modglossarygui&amp;idGlossary='
							.(int)$_POST['idGlossary']
							.'&amp;back_url='.$back_coded, def('_BACK'))), 'content');
		return;
	}
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	Track_Object::updateObjectTitle($_POST['idGlossary'], 'glossary', $_POST['title']);
	
	jumpTo( 'index.php?modname=glossary&op=modglossarygui&idGlossary='.(int)$_POST['idGlossary']
		.'&back_url='.$back_coded );
}

// XXX: addterm
function addterm() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('glossary');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$back_url = urldecode($_GET['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECTIONNAME_GLOSS'), 'glossary', $lang->def('_IMGSECTION_GLOSS'))
		.'<div class="std_block">'
		.getBackUi('index.php?modname=glossary&amp;op=modglossarygui&amp;idGlossary='.(int)$_GET['idGlossary']
			.'&amp;back_url='.$back_coded, $lang->def('_BACK'))
		
		.Form::openForm('glossaryform' ,'index.php?modname=glossary&amp;op=insterm')
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', $back_coded)
		.Form::getHidden('idGlossary', 'idGlossary', $_GET['idGlossary'])
		.Form::getTextfield($lang->def('_TERM'), 'term', 'term', 255, $lang->def('_TERM'))
		.Form::getTextarea($lang->def('_TERMDESCR'), 'description', 'description', $lang->def('_TERMDESCR'))
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('modglossary', 'modglossary', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: insterm
function insterm() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('glossary');
	
	
	$back_url = urldecode($_POST['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));
	
	if( $_POST['term'] == "" ) $_POST['term'] = $lang->def('_NOTITLE');
	
	$queryIns = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_glossaryterm 
	SET idGlossary = '".(int)$_POST['idGlossary']."',
		term = '".$_POST['term']."',
		description = '".$_POST['description']."'";

	if(!mysql_query($queryIns)) {
		
		$GLOBALS['page']->add(getErrorUi($lang->def('_ERR_SAVE')
			.getBackUi('index.php?modname=glossary&op=modglossarygui&idGlossary='.(int)$_POST['idGlossary']
			.'&amp;back_url='.$back_coded, $lang->def('_BACK'))), 'content');
		return;
	}
		
	jumpTo( 'index.php?modname=glossary&op=modglossarygui&idGlossary='.(int)$_POST['idGlossary']
		.'&back_url='.$back_coded );
}

// XXX: modterm
function modterm() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('glossary');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$back_url = urldecode($_GET['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));
	
	list($idGlossary, $term, $description) = mysql_fetch_row(mysql_query("
	SELECT idGlossary, term, description
	FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
	WHERE idTerm = '".(int)$_GET['idTerm']."'"));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECTIONNAME_GLOSS'), 'glossary', $lang->def('_IMGSECTION_GLOSS'))
		.'<div class="std_block">'
		.getBackUi('index.php?modname=glossary&amp;op=modglossarygui&amp;idGlossary='.(int)$idGlossary
		.'&amp;back_url='.$back_coded, $lang->def('_BACK'))
		
		.Form::openForm('glossaryform' ,'index.php?modname=glossary&amp;op=upterm')
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', $back_coded)
		.Form::getHidden('idTerm', 'idTerm', $_GET['idTerm'])
		.Form::getTextfield($lang->def('_TERM'), 'term', 'term', 255, $term)
		.Form::getTextarea($lang->def('_TERMDESCR'), 'description', 'description', $description)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('modterm', 'modterm', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX: upterm
function upterm() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('glossary');
	
	$back_url = urldecode($_POST['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));
	
	if ($_POST['term'] == "") $_POST['term'] = $lang->def('_NOTITLE');
	list($idGlossary) = mysql_fetch_row(mysql_query("
	SELECT idGlossary
	FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
	WHERE idTerm = '".(int)$_POST['idTerm']."'"));
	
	if(!mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_glossaryterm 
	SET term = '".$_POST['term']."',
		description = '".$_POST['description']."'
		WHERE idTerm='".$_POST['idTerm']."' AND idGlossary = '$idGlossary'")) {
		
		$GLOBALS['page']->add(getErrorUi(
				$lang->def('_ERR_SAVE')
				.getBackUi('index.php?modname=glossary&op=modglossarygui&idGlossary='.$idGlossary
					.'&amp;back_url='.$back_coded, $lang->def('_BACK'))) 
		, 'content');
		return;
	}
	
	jumpTo( 'index.php?modname=glossary&op=modglossarygui&idGlossary='.$idGlossary
		.'&back_url='.$back_coded);
}

// XXX: delterm
function delterm() {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('glossary');
	
	$back_url = urldecode($_GET['back_url']);
	$back_coded = htmlentities(urlencode( $back_url ));	
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_SECTIONNAME_GLOSS'), 'glossary', $lang->def('_IMGSECTION_GLOSS')), 'content');
	
	if( isset($_GET['confirm']) ) {
		
		list($idGlossary) = mysql_fetch_row(mysql_query("
		SELECT idGlossary
		FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
		WHERE idTerm = '".(int)$_GET['idTerm']."'"));
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
		WHERE idTerm='".(int)$_GET['idTerm']."'")) {
			
			$GLOBALS['page']->add(getErrorUi(
				$lang->def('_ERRREMTERM')
				.getBackUi('index.php?modname=glossary&op=modglossarygui&idGlossary='.$idGlossary.'&amp;back_url='.$back_coded, 
					$lang->def('_BACK'))
			, 'content'));
			return;
		}
		jumpTo( 'index.php?modname=glossary&op=modglossarygui&idGlossary='.$idGlossary
			.'&back_url='.$back_coded);
	}
	else {
		list($idGlossary, $term, $descr) = mysql_fetch_row(mysql_query("
		SELECT idGlossary, term, description
		FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
		WHERE idTerm = '".(int)$_GET['idTerm']."'"));
		
		$GLOBALS['page']->add(
			'<div class="std_block">'
			.getDeleteUi(	$lang->def('_AREYOUSURETERM'), 
							'<span class="text_bold">'.$lang->def('_TERM').' : </span>'.$term.'<br />'
								.''.$descr.'', 
							true, 
							'index.php?modname=glossary&amp;op=delterm&amp;idTerm='.$_GET['idTerm']
								.'&amp;back_url='.$back_coded.'&amp;confirm=1', 
							'index.php?modname=glossary&amp;op=modglossarygui&amp;idGlossary='
								.$idGlossary.'&amp;back_url='.$back_coded
							)
			.'</div>'
			.'</div>', 'content');
	}

}

// XXX: switch
if( isset($GLOBALS ['op']) ) switch($GLOBALS ['op']) {
	case "modglossarygui" : {
		$idGlossary = importVar('idGlossary', true, 0);
		$back_url = importVar('back_url');
		
		$object_glos = createLO( 'glossary', $idGlossary );
		$object_glos->edit( $idGlossary, urldecode( $back_url ) );
	};break;
	//add a glossary
	case "addglossary" : {
		addglossary();
	};break;
	case "insglossary" : {
		insglossary();
	};break;
	// modify a glossary
	case "modglossary" : {
		modglossary();
	};break;
	case "upglossary" : {
		upglossary();
	};break;
	
	// add a term
	case "addterm" : {
		addterm();
	};break;
	case "insterm" : {
		insterm();
	};break;
	// modify a term
	case "modterm" : {
		modterm();
	};break;
	case "upterm" : {
		upterm();
	};break;
	// delete a term
	case "delterm" : {
		delterm();
	};break;
	
	case "play" : {
		require_once( dirname(__FILE__).'/do.glossary.php' );
		
		$idGlossary = importVar('idGlossary', true, 0);
		$idParams = importVar('idParams', true, 0);
		$back_url = importVar('back_url');
		
		$object_glos = createLO( 'glossary', $idGlossary );
		$object_glos->play( $idGlossary, $idParams, urldecode( $back_url ) );
	};break;
}

}

?>