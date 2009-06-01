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

class BreakPage_Question extends Question {
	
	var $id;
	
	/**
	 * function BreakPage_Question( $id )
	 *
	 * @param int $id 	the id of the question
	 * @return nothing
	 */
	function BreakPage_Question( $id ) {
		
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
		return 'break_page';
	}
	
	/**
	 * function create()
	 *
	 * @param $back_url	the url where the function retutn at the end of the operation
	 * @return nothing
	 */
	function create($id_poll, $back_poll) {
		
		if(!mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_pollquest 
		( id_poll, type_quest, title_quest, sequence, page ) VALUES 
		( 	'".$id_poll."', 
			'".$this->getQuestionType()."', 
			'<span class=\"text_bold\">".def('_QUEST_BREAK_PAGE')."</span>',
			'".$this->_getNextSequence($id_poll)."', 
			'".$this->_getPageNumber($id_poll)."' ) ")) {
			errorCommunication(def('_POLL_ERR_INS_QUEST')
				.getBackUi(ereg_replace('&', '&amp;', $back_poll), def('_BACK')));
		}
		jumpTo( ''.$back_poll);
		
	}
	
	function edit($back_poll) {
		
		
		jumpTo( ''.$back_poll);
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
		
		list($sel_cat, $quest, $sequence, $page) = mysql_fetch_row(mysql_query("
		SELECT id_category, title_quest, sequence, page 
		FROM ".$GLOBALS['prefix_lms']."_pollquest 
		WHERE id_quest = '".(int)$this->id."'")); 
		
		//insert question
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_pollquest 
		( id_poll, id_category, type_quest, title_quest, sequence, page ) VALUES 
		( 	'".(int)$new_id_poll."', 
			'".(int)$sel_cat."', 
			'".$this->getQuestionType()."', 
			'".mysql_escape_string($quest)."',
			'".(int)$sequence."',
			'".(int)$page."' ) ";
		if(!mysql_query($ins_query)) return false;
		//find id of auto_increment colum
		list($new_id_quest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$new_id_quest) return false;
		return $new_id_quest;
	}
}

?>