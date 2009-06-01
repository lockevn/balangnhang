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

/**
 * Abstarct class for question (refer to Factory design pattners)
 *
 * @package 	Test Question
 * @category 	Question
 * @version 	$Id: class.question.php 662 2006-09-22 15:22:38Z fabio $
 * @author  	Fabio Pirovano (fabio@docebo.com)
 * @abstract
 */

class Question {
	
	/**
	 * @var int	$id 	contains the question identifier 
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 **/
	
	var $id;
	
	/**
	 * class constructor
	 * 
	 * @param int	$id	the unique database identifer of a question 
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function Question( $id ) {
		$this->id = $id;
		return;
	}
	
	/**
	 * this function is useful for question recognize
	 * 
	 * @return string	return the identifier of the quetsion 
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function getQuestionType() {
		return 'question';
	}
	
	/**
	 * this function return the sequence value for a new question
	 * 
	 * @param  int	$idTest	indicates the test selected 
	 *
	 * @return int	is the first empty position in question sequencing for the test $idTest
	 * 
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _getNextSequence( $idTest ) {
		
		
		//select max sequence number
		list($seq) = mysql_fetch_row(mysql_query("
		SELECT MAX(sequence)
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idTest = '".$idTest."'"));
		return ($seq + 1);
	}
	
	/**
	 * this function correct the error in the sequence of the question's answer
	 * 
	 * @return nothing
	 * 
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	 
	 function _fixAnswerSequence() {
		
		
		$re_answer = mysql_query("
		SELECT idAnswer 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY sequence, idAnswer");
		
		$seq = 0;
		while(list($id_answer ) = mysql_fetch_row($re_answer)){
			mysql_query("
			UPDATE ".$GLOBALS['prefix_lms']."_testquestanswer
			SET sequence = '".(int)$seq."' 
			WHERE idAnswer = '".(int)$id_answer."'");
			++$seq;
		}
	}
	
	/**
	 * this function return the page of the question
	 * 
	 * @param  int	$idTest	indicates the test selected 
	 * @return int	is the correct number of page for the question
	 * 
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _getPageNumber( $idTest ) {
		
		
		list($seq, $page) = mysql_fetch_row(mysql_query("
		SELECT MAX(sequence), MAX(page)
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idTest = '".$idTest."'"));
		if(!$page) return 1;
		
		list($type_quest) = mysql_fetch_row(mysql_query("
		SELECT type_quest 
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE sequence = '".$seq."' AND idTest = '".$idTest."'"));
		if($type_quest == 'break_page') return ($page + 1);
		else return $page;
	}
	
	/**
	 * this function return the score in a good format for a query
	 * 
	 * @param  double	$score	the score to format 
	 * @return double	the score formatted
	 * 
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _checkScore( $score ) {
		$score = ereg_replace(',', '.', $score);
		if( $score{0} == '.') $score = '0'.$score;
		return $score;
	}
	
	/**
	 * this function create a new question
	 * 
	 * @param  int		$idTest		indicates the test selected
	 * @param  string	$back_test	indicates the return url
	 * @return nothing
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function create( $idTest, $back_test ) {
		
	}
	
	/**
	 * this function modify a question
	 * 
	 * @param  string	$back_test	indicates the return url
	 * @return nothing
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function edit( $back_test ) {
		
	}
	
	/**
	 * this function delete the question with the idQuest saved in the variable $this->id
	 * 
	 * @return bool	if the operation success return true else return false 
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function del() {
		
		return true;
	}
	
	/**
	 * this function create a copy of a question and return the corresponding id
	 * usually a son of this class don't need to redefine this function
	 * 
	 * @return int 	return the id of the new question if success else return false
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function copy( $new_id_test, $back_test = NULL ) {
		
		
		//retriving question information
		list($idCategory, $type_quest, $title_quest, $difficult, $time_assigned, $sequence, $page, $shuffle) = mysql_fetch_row(mysql_query("
		SELECT idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idQuest = '".(int)$this->id."'")); 
		
		//insert the question copy
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
		( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle ) VALUES 
		( 	'".(int)$new_id_test."', 
			'".(int)$idCategory."', 
			'".$this->getQuestionType()."', 
			'".mysql_escape_string($title_quest)."',
			'".(int)$difficult."', 
			'".$time_assigned."',
			'".(int)$sequence."',
			'".(int)$page."', 
			'".(int)$shuffle."' ) ";
		if(!mysql_query($ins_query)) return false;
		
		//find the id of the inserted question
		list($new_id_quest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$new_id_quest) return false;
		
		//retriving new answer
		$re_answer = mysql_query("
		SELECT idAnswer, sequence, is_correct, answer, comment, score_correct, score_incorrect 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");
		
		$map_answer[0] = 0;
		while(list($idAnswer, $seq, $is_correct, $answer, $comment, $score_c, $score_inc) = mysql_fetch_row($re_answer)) {
			
			//insert answer
			$ins_answer_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer 
			( idQuest, sequence, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
			( 	'".(int)$new_id_quest."', 
				'".(int)$seq."', 
				'".(int)$is_correct."', 
				'".mysql_escape_string($answer)."', 
				'".mysql_escape_string($comment)."',
				'".$this->_checkScore($score_c)."', 
				'".$this->_checkScore($score_inc)."') ";
			if(!mysql_query($ins_answer_query)) return false;
			
			list($map_answer[$idAnswer]) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		}
		
		//retriving extra information for this question 
		$re_extra = mysql_query("
		SELECT idAnswer, extra_info 
		FROM ".$GLOBALS['prefix_lms']."_testquest_extra 
		WHERE idQuest = '".(int)$this->id."'");
		
		// save all the extra info, if there are
		while(list($id_answer, $title_info) = mysql_fetch_row($re_extra)) {
			if(!mysql_query("
			INSERT INTO ".$GLOBALS['prefix_lms']."_testquest_extra 
			( idQuest, idAnswer, extra_info ) VALUES 
			( 	'".(int)$new_id_quest."', 
				'".(int)$map_answer[$id_answer]."', 
				'".mysql_escape_string($title_info)."' )")) return false;
		}
		
		return $new_id_quest;
	}
	
	function import( $format, $back_test = NULL ) {
		
	}
	
	function export( $id, $format, $back_test = NULL ) {
		
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
		
		return '';
	}
	
	/**
	 * return true if the user as done this question
	 * 
	 * @param  int	$id_track	the relative id_track
	 * 
	 * @return bool	true if success false otherwise
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function userDoAnswer( $id_track ) {
		
		
		$recover_answer = "
		SELECT idAnswer 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
		WHERE idQuest = '".(int)$this->id."' AND 
			idTrack = '".(int)$id_track."'";
		$re_answer_do = mysql_query($recover_answer);
		
		if(mysql_num_rows($re_answer_do)) return true;
		else return false;
	}
	
	/**
	 * save the answer to the question in an proper format
	 * 
	 * @param  int		$id_track		the relative id_track
	 * @param  array	$source			source of the answer send by the user
	 * @param  bool		$can_overwrite	if the answer for this question exists and this is true, the old answer 
	 *									is updated, else the old answer will be leaved
	 * 
	 * @return bool	true if success false otherwise
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function storeAnswer( $id_track, &$source, $can_overwrite = false ) {
		
		return true;
	}
	
	/**
	 * save the answer to the question in an proper format overwriting the old entry
	 * 
	 * @param  int		$id_track	the relative id_track
	 * @param  array	$source		source of the answer send by the user
	 * 
	 * @return bool	true if success false otherwise
	 * 
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function updateAnswer( $id_track, &$source ) {
		
		return true;
	}
	
	/**
	 * delete the old answer
	 * 
	 * @param  int		$id_track	the relative id_track
	 * 
	 * @return bool	true if success false otherwise
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function deleteAnswer( $id_track ) {
		
		return true;
	}
	
	/**
	 * force a score to a question
	 */
	function setUserScore($id_track, $id_quest, $new_score) {
		
		switch($this->getScoreSetType()) {
			case "manual" : {
				
				$query_update = "
				UPDATE ".$GLOBALS['prefix_lms']."_testtrack_answer 
				SET score_assigned = '".$new_score."', 
					manual_assigned = '1'
				WHERE idTrack = '".$id_track."' AND idQuest = '".$id_quest."'";
				
				return mysql_query($query_update);
			};break;
			case "auto" : {
				
				$sel_answer = "
				SELECT idAnswer
				FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
				WHERE idTrack = '".$id_track."' AND idQuest = '".$id_quest."'";
				list($id_answer) = mysql_fetch_row(mysql_query($sel_answer));
				
				$query_update = "
				UPDATE ".$GLOBALS['prefix_lms']."_testtrack_answer 
				SET score_assigned = '0'
				WHERE idTrack = '".$id_track."' AND idQuest = '".$id_quest."'";
				$re = mysql_query($query_update);
				
				$query_update = "
				UPDATE ".$GLOBALS['prefix_lms']."_testtrack_answer 
				SET score_assigned = '".$new_score."'
				WHERE idTrack = '".$id_track."' AND idQuest = '".$id_quest."' AND idAnswer = '".$id_answer."'";
				$re &= mysql_query($query_update);
				
				return $re;
			};break;
		}
	}
	
	/**
	 * get the method used to obtain result automatic or manual
	 * 
	 * @return string 	contain one of these value :
	 *					'none' if the question doesn't return any score (such as title or break_page)
	 *					'manual' if the score is set by a user, 
	 *					'auto' if the system automatical assign a result
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function getScoreSetType() {
		
		return 'none';
	}
	
	/**
	 * get the maximum score for the question
	 * 
	 * @return double	the maximum score for a correct answer
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function getMaxScore() {
		
		
		$max_score = 0;
		$re_answer = mysql_query("
		SELECT score_correct
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'");
		while(list($score_correct) = mysql_fetch_row($re_answer)) {
			$max_score = round($max_score + $score_correct, 2);
		}
		return $max_score;
	}
	
	/**
	 * set the maximum score for the question
	 * 
	 * @param 	double 	$score	the score that you want to set
	 * 
	 * @return 	double	return the effective point that will be assigned to the question
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function getRealMaxScore( $score ) {
		
		
		list($num_correct) = mysql_fetch_row(mysql_query("
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."' AND is_correct = '1'"));
		
		if(!$num_correct) $score_assigned = 0;
		else $score_assigned = round($score / $num_correct, 2);
		
		return round($score_assigned * $num_correct, 2);
	}
	
	/**
	 * set the maximum score for the question
	 * 
	 * @param 	double 	$score	the score assigned to the question
	 * 
	 * @return 	double	contain the new maximum score for the question, can be different from the param $score 
	 *					because can be round
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function setMaxScore( $score ) {
		
		
		list($num_correct) = mysql_fetch_row(mysql_query("
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."' AND is_correct = '1'"));
		
		if(!$num_correct) $score_assigned = 0;
		else $score_assigned = round($score / $num_correct, 2);
		
		$re_assign = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_testquestanswer 
		SET score_correct = '0' 
		WHERE idQuest = '".(int)$this->id."' AND is_correct = '0'");
		
		$re_assign = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_testquestanswer 
		SET score_correct = '".$score_assigned."' 
		WHERE idQuest = '".(int)$this->id."' AND is_correct = '1'");
		if(!$re_assign) return 0;
		else return round($score_assigned * $num_correct, 2);
	}
	
	/**
	 * return the user score for this question
	 * 
	 * @param  int		$id_track	the test relative to this question
	 * 
	 * @return double	return the score for the user or 0 if there isn't a track for the question
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function userScore( $id_track ) {
		
		
		$score = 0;
		$re_answer = mysql_query("
		SELECT score_assigned 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_answer
		WHERE idQuest = '".(int)$this->id."' AND idTrack = '".(int)$id_track."'");
		if(!mysql_num_rows($re_answer)) return $score;
		while(list($score_assigned) = mysql_fetch_row($re_answer)) {
			$score = round($score + $score_assigned, 2);
		}
		return $score;
	}
	
	/**
	 * display the question with the result of a user
	 * 
	 * @param  	int		$id_track		the test relative to this question
	 * @param  	int		$num_quest		the quest sequence number
	 * @param  	int		$num_quest		the quest sequence number
	 * 
	 * @return array	return an array with xhtml code in this way
	 * 					string	'quest' 	=> the quest, 
	 *					double	'score'		=> score obtained from this question, 
	 *					string	'comment'	=> relative comment to the quest 
	 * 					bool	'manual_assigned'	=> if the score is alredy assigned manually, this is true 
	 * 
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function displayUserResult( $id_track, $num_quest, $show_solution ) {
		
		
		return array(	'quest' 	=> $num_quest.' : displayUserResult() not defined ! '.$this->getQuestionType().'<br />', 
						'score'		=> 0, 
						'comment'	=> '',
						'manual_assigned' => 0 );
	}
	
	function importFromRaw($raw_quest, $id_test = false) {
		
		if($id_test === false) $id_test = 0;
		
		//insert question
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
		( idQuest, idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page ) VALUES 
		( 	NULL,
			'".(int)$id_test."', 
			'".(int)$raw_quest->id_category."', 
			'".$this->getQuestionType()."', 
			'".$raw_quest->quest_text."',
			'".(int)$raw_quest->difficult."', 
			'".$raw_quest->time_assigned."',
			'1',
			'1' ) ";
		if(!mysql_query($ins_query)) return false;
		
		//find id of auto_increment colum
		list($new_id_quest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$new_id_quest) return false;
		
		if(!is_array($raw_quest->answers)) return $new_id_quest;
		
		reset($raw_quest->answers);
		while(list(,$raw_answer) = each($raw_quest->answers)) {
			
			//insert answer
			$ins_answer_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer 
			( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
			( 	'".(int)$new_id_quest."', 
				'".(int)$raw_answer->is_correct."', 
				'".$raw_answer->text."', 
				'".$raw_answer->comment."',
				'".$this->_checkScore($raw_answer->score_correct)."', 
				'".$this->_checkScore($raw_answer->score_penalty)."') ";
			if(!mysql_query($ins_answer_query)) return false;
		}
		return $new_id_quest;
	}
	
	function getCategoryName($id_cat) {
		
		$name = '';
		$qtxt = "SELECT name " 
			."FROM ".$this->_table_category." "
			."WHERE idCategory = '".$id_cat."' ";
		$re = mysql_query($qtxt);
		list($name) = mysql_fetch_row($re);
		
		return $name;
	}
		
	function exportToRaw($id_test = false) {
		
		//retriving question information
		list($idCategory, $type_quest, $title_quest, $difficult, $time_assigned, ) = mysql_fetch_row(mysql_query("
		SELECT idCategory, type_quest, title_quest, difficult, time_assigned 
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idQuest = '".(int)$this->id."'")); 
		
		//insert the question copy
		$oQuest = new QuestionRaw();
		$oQuest->id 	= $this->id;
		$oQuest->qtype 	= $this->getQuestionType();
			
		$oQuest->id_category 	= $this->getCategoryName($idCategory);
		$oQuest->quest_text 	= $title_quest;
		$oQuest->difficult 		= $difficult;
		$oQuest->time_assigned 	= $time_assigned;
		
		$oQuest->answers 		= array();
		$oQuest->extra_info 	= array();
		
		//retriving new answer
		$re_answer = mysql_query("
		SELECT idAnswer, is_correct, answer, comment, score_correct, score_incorrect 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");
		while(list($idAnswer, $is_correct, $answer, $comment, $score_c, $score_inc) = mysql_fetch_row($re_answer)) {
			
			$oAnswer = new AnswerRaw();
			
			$oAnswer->is_correct 		= $is_correct;
			$oAnswer->text 				= $answer;
			$oAnswer->comment 			= $comment;
			$oAnswer->score_correct 	= $score_c;
			$oAnswer->score_penalty 	= $score_inc;
			
			$oQuest->answers[] = $oAnswer;
		}
		
		//retriving extra information for this question 
		$re_extra = mysql_query("
		SELECT idAnswer, extra_info 
		FROM ".$GLOBALS['prefix_lms']."_testquest_extra 
		WHERE idQuest = '".(int)$this->id."'");
		
		// save all the extra info, if there are
		while(list($id_answer, $title_info) = mysql_fetch_row($re_extra)) {
			
			$oAnswer = new AnswerRaw();
			$oAnswer->text = $title_info;
			
			$oQuest->extra_info[] = $oAnswer;
		}
		
		return $oQuest;
	}
	
}


class QuestionRaw {
	
	var $qtype = NULL;
	
	var $id_category 	= 0;
	var $prompt 		= false;
	var $quest_text 	= false;
	var $difficult 		= 3;
	var $time_assigned 	= 0;
	var $answers 		= array();
	var $extra_info 	= array();
	
	function QuestionRaw() {}
	
	function setCategoryFromName($category_name) {
		
		$cat_list = array();
		$qtxt = "SELECT idCategory,  " 
			."FROM ".$GLOBALS['prefix_lms']."_quest_category "
			."WHERE name = '".$category_name."' AND ( author = 0 OR author = ".(int)getLogUserId()." ) ";
		$re = mysql_query($qtxt);
		if(!$re || !mysql_num_rows($re)) $this->id_category = 0;
		else list($this->id_category) = mysql_fetch_row($re);
		
	}
	
}

class AnswerRaw {
	
	var $is_correct 	= 0;
	var $text 			= false;
	var $comment 		= false;
	var $score_correct 	= 0;
	var $score_penalty 	= 0;
	
	function AnswerRaw() {}
	
}

?>
