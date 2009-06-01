<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright(c) 2004													 */
/* Fabio Pirovano (gishell@tiscali.it)									 */
/*                                                                       */
/* http://www.docebolms.org												 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if(!$GLOBALS['current_user']->isAnonymous()) {

function play( $object_glos, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	$lang =& DoceboLanguage::createInstance('glossary');
	
	$letter = importVar('letter', true, '');
	$idGlossary = $object_glos->getId();
	$back_coded = htmlentities(urlencode( $object_glos->back_url ));
	$search = urldecode(importVar('search'));
	
	require_once(dirname(__FILE__).'/../../lib/lib.param.php' );
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php' );
	
	$idReference = getLOParam($id_param, 'idReference');
	// NOTE: Track only if $idReference is present 
	if( $idReference !== FALSE ) {
		require_once( dirname(__FILE__).'/../../class.module/track.glossary.php' );
		list( $exist, $idTrack) = Track_Glossary::getIdTrack($idReference, getLogUserId(), $idGlossary, TRUE );
		if( $exist ) {
			$ti = new Track_Glossary( $idTrack );
			$ti->setDate(date('Y-m-d H:i:s'));
			$ti->status = 'completed';
			$ti->update();
		} else {
			$ti = new Track_Glossary( false );
			$ti->createTrack( $idReference, $idTrack, getLogUserId(), date('Y-m-d H:i:s'), 'completed', 'glossary' );
		}
	}
	
	list($title) = mysql_fetch_row(mysql_query("
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_glossary 
	WHERE idGlossary = '".$idGlossary."'"));
	
	$termsQuery = "
	SELECT idTerm, term
	FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
	WHERE idGlossary = '".$idGlossary."'";
	if($search != '' && !isset($_POST['empty'])) {
		$termsQuery .= " AND ( term LIKE '%".$search."%' OR description LIKE '%".$search."%' ) ";
	}
	if( $letter != 0 ) $termsQuery .= " AND term LIKE '".chr($letter)."%'";
	$termsQuery .= ' ORDER BY term';
	$reTerms = mysql_query($termsQuery);
	
	$page_title = array(
		$lang->def('_GLOSSARY_TITLE')
	);
	
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'glossary')
		.'<div class="std_block">'
		.'<div class="colum_container">'
		.getBackUi( $object_glos->back_url, $lang->def('_BACK') )
		
		.'<span class="title">'.$lang->def('_GLOSSARYTITLE').' :</span> '.$title
		
		.Form::openForm('glossary_play', 'index.php?modname=glossary&amp;op=play')
		
		.Form::getOpenFieldset($lang->def('_FILTER'))
			.Form::getHidden('idGlossary', 'idGlossary', $idGlossary)
			.Form::getHidden('idParams', 'idParams', $id_param)
			.Form::getHidden('back_url', 'back_url', $back_coded)
		
		.Form::getTextfield($lang->def('_SEARCH'), 'search', 'search', 255, 
			( $search != '' && !isset($_POST['empty']) ? $search : '' ))
		, 'content');
	$GLOBALS['page']->add( '[ ', 'content');
	//letter selection
	for($i = 97; $i < 123; $i++) {
		if($letter == $i) $GLOBALS['page']->add( '<span class="text_bold">(', 'content');
		$GLOBALS['page']->add( '<a href="index.php?modname=glossary&amp;op=play&amp;idGlossary='.$idGlossary.'&amp;idParams='.$id_param
				.'&amp;letter='.$i.'&amp;back_url='.$back_coded.'">'.chr($i).'</a>', 'content');
		
		if($letter == $i) $GLOBALS['page']->add( ')</span>', 'content');
		if($i < 122) $GLOBALS['page']->add( '-', 'content');
	}
	$GLOBALS['page']->add( '&nbsp;]&nbsp;[&nbsp;', 'content');
	for($i = 48; $i < 58; $i++) {
		if($letter == $i) $GLOBALS['page']->add( '<span class="text_bold">(');
		$GLOBALS['page']->add( '<a href="index.php?modname=glossary&amp;op=play&amp;idGlossary='.$idGlossary.'&amp;idParams='.$id_param
				.'&amp;letter='.$i.'&amp;back_url='.$back_coded.'">'.chr($i).'</a>', 'content');
		
		if($letter == $i) $GLOBALS['page']->add( ')</span>', 'content');
		if($i < 57) $GLOBALS['page']->add( '-', 'content');
	}
	$GLOBALS['page']->add( ' ] '
		
		.Form::getBreakRow()
		.Form::openButtonSpace()
		.Form::getButton('do_search', 'do_search', $lang->def('_SEARCH'))
		.Form::getButton('empty', 'empty', $lang->def('_UNDOSEL'))
		.Form::closeButtonSpace()
		.Form::getCloseFieldset()
		.Form::closeForm()
		.'</div>'
		.'<div class="colum_container">'
		.'<div class="colum_25">'
		.'<div class="padding_04">'
		.'<div class="boxinfo_title">'.$lang->def('_TERMS').'</div>'
		.'<ul class="unformatted_list">', 'content');
	while(list( $idTerm, $term ) = mysql_fetch_row($reTerms)) {
		$GLOBALS['page']->add('<li><a class="href_block" href="index.php?modname=glossary&amp;op=play&amp;idGlossary='
			.$idGlossary.'&amp;idParams='.$id_param.'&amp;letter='.$letter.'&amp;idTerm='.$idTerm
			.'&amp;search='.urlencode($search)
			.'&amp;back_url='.$back_coded.'">'.$term.'</a></li>', 'content');
	}
	$GLOBALS['page']->add('</ul></div>'
		.'</div>'
		.'<div class="colum_75">', 'content');
	if( isset($_GET['idTerm']) ) {
		list($term, $descr) = mysql_fetch_row(mysql_query("
		SELECT term, description
		FROM ".$GLOBALS['prefix_lms']."_glossaryterm 
		WHERE idTerm = '".(int)$_GET['idTerm']."'"));
		
		$GLOBALS['page']->add('<div class="padding_04">'
			.'<div class="boxinfo_title">'.$term.'</div>'
			.'<div class="boxinfo_container">'
				.( $search == '' ? $descr :
				 eregi_replace($search, '<span class="filter_evidence">'.$search.'</span>', $descr) )
			.'</div>'
			.'</div><br />', 'content');
	}
	$GLOBALS['page']->add('</div>'
		.'</div>'
		.'<div class="no_float"></div>'
		.'</div>', 'content');
}

}

?>