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

require_once( dirname(__FILE__).'/class.question.php' );

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
	function create($idTest, $back_test) {
		
		
		if(!mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
		( idTest, type_quest, title_quest, sequence, page, difficult ) VALUES 
		( 	'".$idTest."', 
			'".$this->getQuestionType()."', 
			'<span class=\"text_bold\">".def('_QUEST_BREAK_PAGE')."</span>',
			'".$this->_getNextSequence($idTest)."', 
			'".$this->_getPageNumber($idTest)."',
			'0') ")) {
			errorCommunication(def('_TEST_ERR_INS_QUEST')
				.getBackUi(ereg_replace('&', '&amp;', $back_test), def('_BACK')));
		}
		jumpTo( ''.$back_test);
		
	}
	
	function edit($back_test) {
		
		
		jumpTo( ''.$back_test);
	}
	
	function del() {
		
		return mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idQuest = '".$this->id."'");
	}
	
	/**
	 * this function create a copy of a question and return the corresponding id
	 * 
	 * @return int 	return the id of the new question if success else return false
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function copy( $new_id_test, $back_test = NULL ) {
		
		
		//retriving question
		list($sel_cat, $quest, $sel_diff, $time_ass, $sequence, $page) = mysql_fetch_row(mysql_query("
		SELECT idCategory, title_quest, difficult, time_assigned, sequence, page 
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idQuest = '".(int)$this->id."'"));
		
		//insert question
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
		( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page ) VALUES 
		( 	'".(int)$new_id_test."', 
			'".(int)$sel_cat."', 
			'".$this->getQuestionType()."', 
			'".mysql_escape_string($quest)."',
			'".(int)$sel_diff."', 
			'".$time_ass."',
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