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


function decodeSessionTime($stime) {
	$output = $stime;
	if (strpos($stime, 'P')!==false) {
		$re1 = preg_match ('/^P((\d*)Y)?((\d*)M)?((\d*)D)?(T((\d*)H)?((\d*)M)?((\d*)(\.(\d{1,2}))?S)?)?$/', $stime, $t1_s );
		if(!isset($t1_s[15]) || $t1_s[15] == '') $t1_s[15] = '00';
		if(!isset($t1_s[13]) || $t1_s[13] == '') $t1_s[13] = '00';
		if(!isset($t1_s[11]) || $t1_s[11] == '') $t1_s[11] = '00';
		if(!isset($t1_s[9]) || $t1_s[9] == '') $t1_s[9] = '0000';
		$output = ($t1_s[9]=='0000' || $t1_s[9] == '' ? '' : $t1_s[9].':')
			.sprintf("%'02s:%'02s.%'02s",  $t1_s[11], $t1_s[13], $t1_s[15]);
	}
	return $output;
}


function getTrackingTable($id_user, $id_org) {

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$tb = new TypeOne($GLOBALS['lms']['visu_course']);
	
	$lang = DoceboLanguage::CreateInstance('organization', 'lms');
	
	$h_type = array('', '', 'image', 'image', '', 'nowrap', 'image', 'image nowrap');
	$h_content = array(
		$lang->def('_NAME'),
		$lang->def('_STATUS'),
		$lang->def('_SCORE'),
		$lang->def('_MAX_SCORE'),
		$lang->def('_LAST_ACCESS'),
		$lang->def('_TIME'),
		$lang->def('_ATTEMPTS'),
		''
	);

	$tb->setColsStyle($h_type);
	$tb->addHead($h_content);

	$qry = "SELECT t3.title, t1.lesson_status, t1.score_raw, t1.score_max, t1.session_time, ".
		" MAX(t2.date_action) as last_access, COUNT(*) as attempts, t1.idscorm_item as item, t1.idscorm_tracking as id_track ".
		" FROM ".$GLOBALS['prefix_lms']."_scorm_tracking as t1, ".
		" ".$GLOBALS['prefix_lms']."_scorm_tracking_history as t2, ".
		" ".$GLOBALS['prefix_lms']."_scorm_items as t3 ".
		" WHERE t1.idscorm_item=t3.idscorm_item AND ".
		" t2.idscorm_tracking=t1.idscorm_tracking AND t3.idscorm_organization=$id_org ".
		" AND t1.idUser=$id_user ".
		" GROUP BY t2.idscorm_tracking".
		" ORDER BY t1.idscorm_item ";

	$res = mysql_query($qry);
	while ($row = mysql_fetch_assoc($res)) {
		
		$line = array();
		
		
		$interactions = '<a href="index.php?modname=organization&op=scorm_interactions&amp;id_user='.$id_user.'&amp;id_org='.$id_org.'&amp;id_track='.$row['id_track'].'">'.$lang->def('_SHOW_INTERACTIONS').'</a>';
		$scorm_history = '<a href="index.php?modname=organization&op=scorm_history&amp;id_user='.$id_user.'&amp;id_org='.$id_org.'&amp;id_obj='.$row['item'].'">'.$lang->def('_SHOW_HISTORY').'</a>';
		
		$line[] = $row['title'];
		$line[] = $row['lesson_status'];
		$line[] = $row['score_raw'];
		$line[] = $row['score_max'];
		$line[] = $GLOBALS['regset']->databaseToRegional($row['last_access']);
		$line[] = decodeSessionTime($row['session_time']);
		$line[] = $row['attempts'];
		//$line[] = ($row['score_raw']!='' ? $interactions : '');
		$line[] = ( $row['attempts'] > 1 ? $scorm_history : '' ) 
			.($row['score_raw']!='' ? '<br />'.$interactions : '');
	
	
		$tb->addBody($line);
	
	}

	//title
	cout( getTitleArea( '' ) );
	cout( '<div class="std_block">' );

	//back button, back to treeview
	if(isset($_GET['back']) && $_GET['back'])
		$back = getBackUi( 'index.php?modname=course&amp;op=mycourses&amp;sop=unregistercourse' , $lang->def('_BACK', 'standard', 'framework') );
	else
		$back = getBackUi( 'index.php?modname=organization' , $lang->def('_BACK') );
	cout( $back );
	cout( $tb->getTable() );
	cout( $back );
	cout( '</div>' );

} //end function


function getHistoryTable($id_user, $id_obj) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$tb = new TypeOne($GLOBALS['lms']['visu_course']);
	
	$id_org = get_req('id_org', DOTY_INT, 0);
	
	$lang = DoceboLanguage::CreateInstance('organization', 'lms');
	
	$h_type = array('', '', '', '', '');
	$h_content = array(
		$lang->def('_ATTEMPT'),
		$lang->def('_STATUS'),
		$lang->def('_SCORE'),
		$lang->def('_DATE'),
		$lang->def('_TIME')
	);
	
	$tb->setColsStyle($h_type);
	$tb->addHead($h_content);
	
	$qry = "SELECT t1.* FROM ".
		$GLOBALS['prefix_lms']."_scorm_tracking_history as t1 JOIN ".
		$GLOBALS['prefix_lms']."_scorm_tracking as t2 ON (t1.idscorm_tracking=t2.idscorm_tracking) ".
		" WHERE t2.idscorm_item=$id_obj AND t2.idUser=$id_user ".
		" ORDER BY t1.date_action ASC ";
	$res = mysql_query($qry); $i=1;
	while ($row = mysql_fetch_assoc($res)) {
		
		$line = array();
		
		$line[] = $lang->def('_ATTEMPT').' '.$i;
		$line[] = $row['lesson_status'];
		$line[] = $row['score_raw'];
		$line[] = $GLOBALS['regset']->databaseToRegional($row['date_action']);
		$line[] = decodeSessionTime($row['session_time']);
				
		$tb->addBody($line);
		$i++;
	}
	
	//title
	cout( getTitleArea( '' ) );
	cout( '<div class="std_block">' );

	//back button, back to treeview
	$back = getBackUi( 'index.php?modname=organization&amp;op=scorm_track&amp;id_user='.$id_user.'&amp;id_org='.$id_org , $lang->def('_BACK') );
	
	//back button, back to treeview
	cout( $back );
	cout( $tb->getTable() );
	cout( $back );
	cout( '</div>' );
}



function getInteractionsTable($id_user, $idtrack) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.domxml.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$tb = new TypeOne($GLOBALS['lms']['visu_course']);
	
	$lang = DoceboLanguage::CreateInstance('organization', 'lms');
	
	$id_org = get_req('id_org', DOTY_INT, 0);
	
	$h_type = array('', '', '');
	$h_content = array(
		$lang->def('_DESCRIPTION'),
		$lang->def('_TYPE'),
		$lang->def('_RESULT')
	);

	$tb->setColsStyle($h_type);
	$tb->addHead($h_content);

	$qry = "SELECT xmldata FROM ".$GLOBALS['prefix_lms']."_scorm_tracking WHERE idscorm_tracking=$idtrack AND idUser=$id_user";
	$res = mysql_query($qry);
	$row = mysql_fetch_array($res);
	
	
	$doc = new DoceboDOMDocument();
	$doc->loadXML($row['xmldata']);

	$context = new DoceboDOMXPath( $doc );
	$root = $doc->documentElement;
	
	$temp = $context->query('//interactions');
	
	$lines = array();
	for ($i=0; $i<$temp->length; $i++) {
		$arr = array();
		$node =& $temp->item($i);
		
		//interaction index
		//$arr['index'] = $node->getAttribute('index');

		//get description
		$elem = $context->query('description/text()', $node);
		$elemNode =& $elem->item(0);
		if($elemNode && isset($elemNode->textContent)) {
			$arr['description'] = $elemNode->textContent;
			
			//get type
			$elem = $context->query('type/text()', $node);
			$elemNode =& $elem->item(0);
			$arr['type'] = $elemNode->textContent;
		
			//get result
			$elem = $context->query('result/text()', $node);
			$elemNode =& $elem->item(0);
			$arr['result'] = $elemNode->textContent;
			
			//get id
			$elem = $context->query('id/text()', $node);
			$elemNode =& $elem->item(0);
			$id = $elemNode->textContent;
			
			if($arr['result'] == '1') $arr['result'] = 'true';
			else $arr['result'] = 'false';
			
			$lines[$id] = array( $arr['description'], $arr['type'], $arr['result'] );
		}
	
	}
	
	foreach ($lines as $key=>$line) {
		$tb->addBody($line);
	}
	
	//title
	cout( getTitleArea( $lang->def('_SCORM_INTERACTIONS_TABLE') ) );
	cout( '<div class="std_block">' );

	//back button, back to treeview
	$back = getBackUi( 
		'index.php?modname=organization&amp;op=scorm_track&amp;id_user='.$id_user.'&amp;id_org='.$id_org, 
		$lang->def('_BACK_TO_TRACK') );//'index.php?modname=organization&amp;op=history&amp;id_user='.$id_user.'&amp;id_org='.$org , $lang->def('_BACK_TO_TRACK') );
	
	//back button, back to treeview
	cout( $back );
	cout( $tb->getTable() );
	cout( $back );
	cout( '</div>' );
}

?>