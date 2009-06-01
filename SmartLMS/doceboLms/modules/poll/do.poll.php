<?php

/************************************************************************/
/* DOCEBO LMS - E-Learning System                                 		*/
/* ============================================                         */
/*                                                                      */
/*                                                                      */
/* http://www.docebolms.org												*/
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

function retriveTrack($id_reference, $id_poll, $id_user) {
	
	if(isset($_POST['id_track']) || isset($_GET['id_track'])) {
		return importVar('id_track', true, 0);
	}
	
	if($id_reference !== FALSE) {
		
		// Load existing info track$id_reference, $id_resource, $id_user
		
		$id_track 	= Track_Poll::getIdTrack($id_reference, $id_poll, $id_user); //fixed by fleo
		//$track_info 	= Track_Poll::getIdTrack($id_reference, $id_poll, $id_user); 
		//$id_track 		= $track_info['id_track'];
		
		if($id_track) {
			return $id_track;
		} else {
			$id_track = Track_Poll::createNewTrack($id_user, $id_poll, $id_reference);
			if($id_track) {
				/*Track_Poll::createTrack(	$id_reference, 
											$id_track, 
											$id_user, 
											date('Y-m-d H:i:s'), 
											'attempted', 
											'poll' );*/
				return $id_track;
			}
		}
	} 
	return false;
}

function intro( $object_poll, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.poll.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.poll.php' );
	
	$lang 			=& DoceboLanguage::createInstance('poll');
	$id_poll 		= $object_poll->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_poll->back_url));
	$id_track 		= retriveTrack($id_reference, $id_poll, getLogUserId());
	
	$poll_man 	= new PollManagement($id_poll);
	$play_man 	= new PlayPollManagement($id_poll, getLogUserId(), $id_track, $poll_man);
	$poll_info 	= $poll_man->getPollAllInfo();
	
	$page_title = array(
		ereg_replace('&', '&amp;', $object_poll->back_url) => $lang->def('_TITLE'), 
		$poll_info['title']
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'poll')
		.'<div class="std_block">'
		.getBackUi( ereg_replace('&', '&amp;', $object_poll->back_url), $lang->def('_BACK'))

		.'<span class="text_bold">'.$lang->def('_TITLE').' : </span>'.$poll_info['title'].'<br /><br />'
		.'<span class="text_bold">'.$lang->def('_DESCRIPTION').' : </span>'.$poll_info['description'].'<br />', 'content');

	
	$GLOBALS['page']->add(
		Form::openForm('poll_intro', 'index.php?modname=poll&amp;op=play')
		.Form::getHidden('id_poll', 'id_poll', $id_poll)
		.Form::getHidden('id_param', 'id_param', $id_param)
		.Form::getHidden('id_track', 'id_track', $id_track)
		.Form::getHidden('back_url', 'back_url', $url_coded)
		.Form::getHidden('next_step', 'next_step', 'play')
		.'<div class="align_right">'
	, 'content');
	// Actions
	$score_status = $play_man->getStatus();
	$quest_number = $poll_man->getTotalQuestionNumber();
	
	if($quest_number == 0) {
		$GLOBALS['page']->add($lang->def('_NO_QUESTION_IN_POLL'), 'content');
	} elseif($id_track !== false && $score_status == 'valid') {
		$GLOBALS['page']->add($lang->def('_POLL_ALREDY_VOTED'), 'content');
	} else {
		
		$GLOBALS['page']->add(Form::getButton('begin', 'begin', $lang->def('_POLL_BEGIN')), 'content');
	}
	$GLOBALS['page']->add(
		'</div>'
		.Form::closeForm()
		.'</div>', 'content');
}

function playPollDispatch( $object_poll, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.poll.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.poll.php' );
	
	$id_poll 		= $object_poll->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_poll->back_url));
	$id_track 		= retriveTrack($id_reference, $id_poll, getLogUserId());
	
	if(isset($_POST['show_result'])) {
		
		// continue a poll completed, show the result
		showResult($object_poll, $id_param);
	}  else {
		
		// play poll
		play($object_poll, $id_param);
	}
}

function play($object_poll, $id_param) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.poll.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.poll.php' );
	
	$lang 			=& DoceboLanguage::createInstance('poll');
	$id_poll 		= $object_poll->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_poll->back_url));
	$id_track 		= retriveTrack($id_reference, $id_poll, getLogUserId());
	
	$poll_man 	= new PollManagement($id_poll);
	$play_man 	= new PlayPollManagement($id_poll, getLogUserId(), $id_track, $poll_man);
	$poll_info 		= $poll_man->getPollAllInfo();
	$track_info 	= $play_man->getTrackAllInfo();
	
	//number of poll pages-------------------------------------------
	$tot_page = $poll_man->getTotalPageNumber();
	
	// find the page to display 
	$previous_page = importVar('previous_page', false, false);
	if($previous_page === false) {
		
		$page_to_display = 1;
	} else {
		$page_to_display = $previous_page;
		if(isset($_POST['next_page'])) ++$page_to_display;
		if(isset($_POST['prev_page']) && $page_to_display > 1) --$page_to_display;
	}
	if(isset($_POST['page_to_save']) && ($id_reference !== false)) {
		$play_man->storePage($_POST['page_to_save'], true);
	}
	
	// save page track info
	$quest_sequence_number = $poll_man->getInitQuestSequenceNumberForPage($page_to_display);
	$query_question			= $play_man->getQuestionsForPage($page_to_display);
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE').' : '.$poll_info['title'], 'poll')
		.'<div class="std_block">'
		
		.Form::openForm('poll_play', 'index.php?modname=poll&amp;op=play', 'std_form', 'post', 'multipart/form-data')
		// Standard info
		.Form::getHidden('next_step', 'next_step', 'play')
		.Form::getHidden('id_poll', 'id_poll', $id_poll)
		.Form::getHidden('id_param', 'id_param', $id_param)
		.Form::getHidden('back_url', 'back_url', $url_coded)
		.Form::getHidden('id_track', 'id_track', $id_track), 'content');
	
	
	if($tot_page > 1) {
		$GLOBALS['page']->add(
			'<div class="align_center">'.$lang->def('_POLL_PAGES').' : '.$page_to_display.' / '.$tot_page.'</div><br />'
		, 'content');
	}
	
	// Page info
	$GLOBALS['page']->add(
		Form::getHidden('page_to_save', 'page_to_save', $page_to_display)
		.Form::getHidden('previous_page', 'previous_page', $page_to_display), 'content');
	
	// Get question from database
	$re_question = mysql_query($query_question);
	
	// Page display
	$GLOBALS['page']->add('<div class="test_answer_space">', 'content');
	
	while(list($idQuest, $type_quest, $type_file, $type_class) = mysql_fetch_row($re_question)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question_poll/'.$type_file);
		$quest_obj = eval("return new $type_class( $idQuest );");
		
		$GLOBALS['page']->add($quest_obj->play( 	$quest_sequence_number, 
								false, 
								$id_track,
								false ), 'content');
		
		if(($type_quest != 'break_page') && ($type_quest != 'title')) {
			++$quest_sequence_number;
		}
	}
	$GLOBALS['page']->add('</div>'
		.'<div class="test_button_space">', 'content');
	
	if($page_to_display != 1) {
		//back to the next page
		$GLOBALS['page']->add(Form::getButton('prev_page', 'prev_page', $lang->def('_POLL_PREV_PAGE'), 'test_button'), 'content');
	}
	if($page_to_display != $tot_page) {
		//button to the next page
		$GLOBALS['page']->add(Form::getButton('next_page', 'next_page', $lang->def('_POLL_NEXT_PAGE'), 'test_button'), 'content');
	} else {
		//button to the result page
		$GLOBALS['page']->add(Form::getButton('show_result', 'show_result', $lang->def('_POLL_END_PAGE'), 'test_button'), 'content');
	}
	$GLOBALS['page']->add('</div>'
		.Form::closeForm()
		.'</div>', 'content');
}

function showResult( $object_poll, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.poll.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.poll.php' );
	
	$lang 			=& DoceboLanguage::createInstance('poll');
	$id_poll 		= $object_poll->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_poll->back_url));
	$id_track 		= retriveTrack($id_reference, $id_poll, getLogUserId());
	
	Track_Poll::createTrack(	$id_reference, 
								$id_track, 
								getLogUserId(), 
								date('Y-m-d H:i:s'), 
								'completed', 
								'poll' );
	
	$poll_man 		= new PollManagement($id_poll);
	$play_man 		= new PlayPollManagement($id_poll, getLogUserId(), $id_track, $poll_man);
	$poll_info 		= $poll_man->getPollAllInfo();
	$track_info 	= $play_man->getTrackAllInfo();
	
	$previous_page = importVar('previous_page', false, false);
	
	if($id_reference !== false && $id_track != false) {
		
		if(isset($_POST['page_to_save'])) $play_man->storePage($_POST['page_to_save'], true);
		
		$now = date('Y-m-d H:i:s');
		$poll_track = new Track_Poll($id_track);
		$poll_track->setDate($now);
		$poll_track->status = 'completed';
		$poll_track->update();
		
		$poll_track->updateTrack($id_track, array('status' => 'valid'));
	}
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE').' : '.$poll_info['title'], 'poll')
		.'<div class="std_block">'
		.$lang->def('_POLL_COMPLETED')
		.'<br />'
		.Form::openForm('poll_show', ereg_replace('&', '&amp;', $object_poll->back_url))
		.'<div class="align_right">'
		.Form::getButton('end_poll', 'end_poll', $lang->def('_POLL_END_BACKTOLESSON'))
		.'</div>'
		.Form::closeForm(), 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function writePollReport( $id_poll, $id_param, $back_url ) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.poll.php' );
	
	$lang 			=& DoceboLanguage::createInstance('poll');
	
	$poll_man 		= new PollManagement($id_poll);
	$report_man 	= new ReportPollManagement();
	
	$poll_info 		= $poll_man->getPollAllInfo();
	$valid_track 	= $report_man->getAllTrackId($id_poll, 'valid');
	$tot_tracks 	= $report_man->getHowMuchStat($id_poll, 'valid');
	
	// save page track info
	$quest_sequence_number = $poll_man->getInitQuestSequenceNumberForPage(1);
	$query_question			= $report_man->getQuestions($id_poll);
	
	$GLOBALS['page']->add(
		'<div class="std_block">'
		.'<div class="test_answer_space">'
	, 'content');
	
	// Get question from database
	$re_question = mysql_query($query_question);
	
	if (isset($_POST['export'])) {
		$export = true;
		$filename = 'stats_'.str_replace(' ', '_', $poll_info['title']).'_'.date("Y\_m\_d").'.csv';
		$filetext = '';
	} else {
		$export = false;
	}
	
	while(list($idQuest, $type_quest, $type_file, $type_class) = mysql_fetch_row($re_question)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question_poll/'.$type_file);
		$quest_obj = eval("return new $type_class( $idQuest );");
		
		if ($export) {
			$filetext.=$quest_obj->export_CSV( $quest_sequence_number, $tot_tracks, $valid_track );
			$filetext .= "\r\n";
		} else {
			$GLOBALS['page']->add($quest_obj->playReport( $quest_sequence_number, $tot_tracks, $valid_track ), 'content');
		}
		
		if(($type_quest != 'break_page') && ($type_quest != 'title')) {
			++$quest_sequence_number;
		}
	}
	
	if ($export) {
		require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
		sendStrAsFile($filetext, $filename);		
	}
	
	$treeview_value = str_replace('treeview_selected_'.$_SESSION['idCourse'], '', array_search($poll_info['title'], $_POST));
	
	$GLOBALS['page']->add(
		Form::openForm('tree_export_form', 'index.php?modname=stats&amp;op=statcourse')
		.Form::getHidden('seq_0.'.$treeview_value, 'treeview_selected_'.$_SESSION['idCourse'].$treeview_value, $poll_info['title'])
		.Form::getHidden('treeview_selected_'.$_SESSION['idCourse'], 'treeview_selected_'.$_SESSION['idCourse'], $treeview_value)
		.Form::getHidden('treeview_state_'.$_SESSION['idCourse'], 'treeview_state_'.$_SESSION['idCourse'], $_POST['treeview_state_'.$_SESSION['idCourse']])
		.Form::openButtonSpace()
		.Form::getButton('export', 'export', $lang->def('_EXPORT_CSV'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	);
	
	$GLOBALS['page']->add('</div>'
		.'</div>', 'content');
}

?>