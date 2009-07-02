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

require_once( $GLOBALS['where_lms'].'/modules/question_poll/class.question.php' );

class Title_Question extends Question {
	
	var $id;
	
	/**
	 * function Title_Question( $id )
	 *
	 * @param int $id 	the id of the question
	 * @return nothing
	 */
	function Title_Question( $id ) {
		
		parent::Question( $id );
	}
	
	/**
	 * function getQuestionType()
	 *
	 * Return the type of the question
	 *
	 * @return string the type of the question
	 */
	function getQuestionType() {
		return 'title';
	}
	
	/**
	 * function create()
	 *
	 * @param $back_url	the url where the function retutn at the end of the operation
	 * @return nothing
	 */
	function create($id_poll, $back_poll) {
		$lang =& DoceboLanguage::createInstance('poll');
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$url_encode = htmlentities(urlencode($back_poll));
		
		if(isset($_POST['add_question'])) {
			
			if(!mysql_query("
			INSERT INTO ".$GLOBALS['prefix_lms']."_pollquest 
			( id_poll, type_quest, title_quest, sequence, page ) VALUES 
			( 	'".$id_poll."', 
				'".$this->getQuestionType()."', 
				'".$_POST['title_quest']."',
				'".$this->_getNextSequence($id_poll)."', 
				'".$this->_getPageNumber($id_poll)."' ) ")) {
					
				$GLOBALS ['page']->out( getErrorUi($lang->def('_POLL_ERR_INS_QUEST')
					.getBackUi('index.php?modname=question_poll&amp;op=create&amp;type_quest='
					.$this->getQuestionType().'&amp;id_poll='.$id_poll.'&amp;back_poll='.$url_encode, $lang->def('_BACK'))), 'content');
			}
			jumpTo( ''.$back_poll);
		}
		
		$GLOBALS['page']->add(getTitleArea($lang->def('_POLL_SECTION'), 'poll')
			.'<div class="std_block">'
			.getBackUi(ereg_replace('&', '&amp;', $back_poll), $lang->def('_BACK'))
			.'<div class="title_big">'
			.$lang->def('_QUEST_ACRN_'.strtoupper($this->getQuestionType())).' - '
			.$lang->def('_QUEST_'.strtoupper($this->getQuestionType()))
			.'</div><br />'
			.Form::openForm('form_add_quest', 'index.php?modname=question_poll&amp;op=create')
		
			.Form::openElementSpace()
		
			.Form::getHidden('type_quest', 'type_quest', $this->getQuestionType())
			.Form::getHidden('id_poll', 'id_poll', $id_poll)
			.Form::getHidden('back_poll', 'back_poll', $url_encode)
		
			.Form::getTextarea($lang->def('_POLL_QUEST_TITLE'), 'title_quest', 'title_quest')
			.Form::getTextBox($lang->def('_QUEST_TITLE_NOTE'))
			.Form::closeElementSpace()
		
			.Form::openButtonSpace()
			.Form::getButton('add_question', 'add_question', $lang->def('_INSERT'))
			.Form::closeButtonSpace()
		
			.Form::closeForm()
			.'</div>', 'content');
	}
	
	function edit($back_poll) {
		$lang 			=& DoceboLanguage::createInstance('poll');
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$url_encode = htmlentities(urlencode($back_poll));
		
		if(isset($_POST['add_question'])) {
			if(!mysql_query("
			UPDATE ".$GLOBALS['prefix_lms']."_pollquest 
			SET title_quest = '".$_POST['title_quest']."' 
			WHERE id_quest = '".$this->id."'")) {
				errorCommunication($lang->def('_ERR_INS_QUEST')
					.getBackUi('index.php?modname=question_poll&amp;op=edit&amp;type_quest='
					.$this->getQuestionType().'&amp;id_quest='.$this->id.'&amp;back_poll='.$url_encode, $lang->def('_BACK')));
			}
			jumpTo( ''.$back_poll);
		}
		list($title_quest) = mysql_fetch_row(mysql_query("
		SELECT title_quest 
		FROM ".$GLOBALS['prefix_lms']."_pollquest 
		WHERE id_quest = '".$this->id."'"));
		$GLOBALS['page']->add(getTitleArea($lang->def('_POLL_SECTION'), 'poll' )
			.'<div class="std_block">'
			.getBackUi(ereg_replace('&', '&amp;', $back_poll), $lang->def('_BACK'))
			.'<div class="title_big">'
			.$lang->def('_QUEST_ACRN_'.strtoupper($this->getQuestionType())).' - '
			.$lang->def('_QUEST_'.strtoupper($this->getQuestionType()))
			.'</div><br />'
			.Form::openForm('form_mod_quest', 'index.php?modname=question_poll&amp;op=edit')
		
			.Form::openElementSpace()
		
			.Form::getHidden('type_quest', 'type_quest', $this->getQuestionType())
			.Form::getHidden('id_quest', 'id_quest', $this->id)
			.Form::getHidden('back_poll', 'back_poll', $url_encode)
		
			.Form::getTextarea($lang->def('_POLL_QUEST_TITLE'), 'title_quest', 'title_quest', $title_quest)
			.Form::getTextBox($lang->def('_QUEST_TITLE_NOTE'))
			.Form::closeElementSpace()
		
			.Form::openButtonSpace()
			.Form::getButton('add_question', 'add_question', $lang->def('_SAVE'))
			.Form::closeButtonSpace()
		
			.Form::closeForm()
			.'</div>', 'content');
	}
	
	/**
	 * this function create a copy of a question and return the corresponding id
	 * 
	 * @return int 	return the id of the new question if success else return false
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function copy( $new_id_poll, $back_poll = NULL ) {
		
		return parent::copy($new_id_poll, $back_poll);
	}
	
	/**
	 * display the quest for play, if 
	 * 
	 * @param 	int		$num_quest 			the number of the quest to display in front of the quest title
	 * @param 	bool	$shuffle_answer 	randomize the answer display order
	 * @param 	int		$id_track 			where find the answer, if find -> load
	 * @param 	bool	$freeze 			if true, when load disable the user interaction
	 * 
	 * @return string of html question code
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function play( $num_quest, $shuffle_answer = false, $id_track = 0, $freeze = false ) {
		
		list($title_quest) = mysql_fetch_row(mysql_query("
		SELECT title_quest 
		FROM ".$GLOBALS['prefix_lms']."_pollquest 
		WHERE id_quest = '".$this->id."'"));
		
		return '<div class="quest_title">'.$title_quest.'</div>';
	}
	
}

?>