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

class ChoiceMultiple_Question extends Question {

	/**
	 * class constructor
	 *
	 * @param int	the unique database identifer of a question
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function ChoiceMultiple_Question( $id ) {
		parent::Question($id);
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
		return 'choice_multiple';
	}

	/**
	 * this function write a gui line for answer insertion
	 *
	 * @param  int	$i	indicate the line number
	 * @return nothing
	 *
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _lineAnswer($i) {
		$lang =& DoceboLanguage::createInstance('test');
		$GLOBALS['page']->add('<tr class="line_answer">'
		.'<td rowspan="2" class=" valign_top align_center">'
		.'<label for="is_correct_'.$i.'">'.$lang->def('_TEST_CORRECT').'</label><br /><br />'
		.'<input type="checkbox" id="is_correct_'.$i.'" name="is_correct['.$i.']" value="1"'
		.( isset($_POST['is_correct'][$i])  ? ' checked="checked"' : '').' />'
		.'</td>'
		.'<td rowspan="2" class="image">'
		//answer
		.'<label class="access-only" for="answer_'.$i.'">'.$lang->def('_TEST_TEXT_ANSWER').'</label>'
		//.Form::getTextarea('', 'answer_'.$i, 'answer['.$i.']', ( isset($_POST['answer'][$i]) ? stripslashes($_POST['answer'][$i]) : ''),false,'','form_line_l','floating','textarea',true)

		.loadHtmlEditor('',
							'answer_'.$i, 
							'answer['.$i.']', 
		( isset($_POST['answer'][$i]) ? stripslashes($_POST['answer'][$i]) : ''),
		false,
							'', 
		true)
			
		//.'<textarea class="test_area_answer" id="answer_'.$i.'" name="answer['.$i.']" cols="25" rows="3">'
		//.( isset($_POST['answer'][$i]) ? stripslashes($_POST['answer'][$i]) : '')//$lang->def('_QUEST_ANSWER')
		//.'</textarea>'
		.'</td>'
		.'<td rowspan="2" class="image">'
		//comment
		.'<label class="access-only" for="comment_'.$i.'">'.$lang->def('_TEST_COMMENT').'</label>'
		.'<textarea class="test_comment" id="comment_'.$i.'" name="comment['.$i.']" rows="6">'
		.( isset($_POST['comment'][$i]) ? stripslashes($_POST['comment'][$i]) : '')//$lang->def('_QUEST_COMMENT')
		.'</textarea>'
		.'</td>'
		.'<td class="test_ifcorrect">'
		.'<label for="score_correct_'.$i.'">'.$lang->def('_TEST_IFCORRECT').'</label>'
		.'</td>'
		.'<td class="align_right">'
		//score correct
		.'<input type="text" class="test_point" id="score_correct_'.$i.'" name="score_correct['.$i.']" alt="'.$lang->def('_TEST_IFCORRECT').'" size="5" value="'
		.( isset($_POST['score_correct'][$i]) ? $_POST['score_correct'][$i] : '0.0').'" />'
		.'</td>'
		.'</tr>'."\n"
		.'<tr class="line_answer">'
		.'<td class="test_ifcorrect">'
		.'<label for="score_incorrect_'.$i.'">'.$lang->def('_TEST_IFINCORRECT').'</label>'
		.'</td>'
		.'<td class="align_right">'
		//score incorrect
		.'- <input type="text" class="test_point" id="score_incorrect_'.$i.'" name="score_incorrect['.$i.']" alt="'.$lang->def('_TEST_IFINCORRECT').'" size="5" value="'
		.( isset($_POST['score_incorrect'][$i]) ? $_POST['score_incorrect'][$i] : '0.0').'" />'
		.'</td>'
		.'</tr>'."\n", 'content');
	}

	/**
	 * this function write a gui line for answer insertion,projected for modify
	 *
	 * @param  int	$i	indicate the line number
	 * @return nothing
	 *
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _lineModAnswer($i) {
		$lang =& DoceboLanguage::createInstance('test');
		$GLOBALS['page']->add('<tr class="line_answer">'
		.'<td rowspan="2" class=" valign_top align_center">'
		.'<label for="is_correct_'.$i.'">'.$lang->def('_TEST_CORRECT').'</label><br /><br />', 'content');
		if(isset($_POST['answer_id'][$i])) {
			$GLOBALS['page']->add('<input type="hidden" id="answer_id_'.$i.'" name="answer_id['.$i.']" value="'.$_POST['answer_id'][$i].'" />', 'content');
		}
		$GLOBALS['page']->add('<input type="checkbox" id="is_correct_'.$i.'" name="is_correct['.$i.']" value="1"'
		.( isset($_POST['is_correct'][$i])  ? ' checked="checked"' : '').' />'
		.'</td>'
		.'<td rowspan="2" class="image">'
		//answer
		.'<label class="access-only" for="answer_'.$i.'">'.$lang->def('_TEST_TEXT_ANSWER').'</label>'
			
		//.Form::getTextarea('', 'answer_'.$i, 'answer['.$i.']', ( isset($_POST['answer'][$i]) ? stripslashes($_POST['answer'][$i]) : ''),false,'','form_line_l','floating','textarea',true)

		.loadHtmlEditor('',
							'answer_'.$i, 
							'answer['.$i.']', 
		( isset($_POST['answer'][$i]) ? stripslashes($_POST['answer'][$i]) : ''),
		false,
							'', 
		true)
			
		//.'<textarea class="test_area_answer" id="answer_'.$i.'" name="answer['.$i.']" cols="25" rows="3">'
		//.( isset($_POST['answer'][$i]) ? stripslashes($_POST['answer'][$i]) : '')
		//.'</textarea>'
		.'</td>'
		.'<td rowspan="2" class="image">'
		//comment
		.'<label class="access-only" for="comment_'.$i.'">'.$lang->def('_TEST_COMMENT').'</label>'
		.'<textarea class="test_comment" id="comment_'.$i.'" name="comment['.$i.']" rows="6">'
		.( isset($_POST['comment'][$i]) ? stripslashes($_POST['comment'][$i]) : '')
		.'</textarea>'
		.'</td>'
		.'<td class="test_ifcorrect">'
		.'<label for="score_correct_'.$i.'">'.$lang->def('_TEST_IFCORRECT').'</label>'
		.'</td>'
		.'<td class="align_right">'
		//score correct
		.'<input type="text" class="test_point" id="score_correct_'.$i.'" name="score_correct['.$i.']" alt="'.$lang->def('_TEST_IFCORRECT').'" size="5" value="'
		.( isset($_POST['score_correct'][$i]) ? $_POST['score_correct'][$i] : '0.0').'" />'
		.'</td>'
		.'</tr>'."\n"
		.'<tr class="line_answer">'
		.'<td class="test_ifcorrect">'
		.'<label for="score_incorrect_'.$i.'">'.$lang->def('_TEST_IFINCORRECT').'</label>'
		.'</td>'
		.'<td class="align_right">'
		//score incorrect
		.'- <input type="text" class="test_point" id="score_incorrect_'.$i.'" name="score_incorrect['.$i.']" alt="'.$lang->def('_TEST_IFINCORRECT').'" size="5" value="'
		.( isset($_POST['score_incorrect'][$i]) ? $_POST['score_incorrect'][$i] : '0.0').'" />'
		.'</td>'
		.'</tr>'."\n", 'content');
	}

	/**
	 * this function create a new question
	 *
	 * @param  int		$idTest 	indicates the test selected
	 * @param  string	$back_test	indicates the return url
	 * @return nothing
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function create( $idTest, $back_test ) {
		$lang =& DoceboLanguage::createInstance('test');

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$url_encode = htmlentities(urlencode($back_test));

		//manage number of answer
		$num_answer = importVar('num_answer', true, 2);
		if(isset($_POST['more_answer'])) ++$num_answer;
		if(isset($_POST['less_answer']) && ($num_answer > 1) ) --$num_answer;

		if(isset($_POST['add_question'])) {
			//insert the new question

			/*đưa question mới vào Bank*/
			$ins_query = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_bankquest 
				( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle ) VALUES
				( 	'".(int)$idTest."', 
					'".(int)$_POST['idCategory']."', 
					'".$this->getQuestionType()."', 
					'".$_POST['title_quest']."',
					'".(int)$_POST['difficult']."',
					'".(int)$_POST['time_assigned']."', 
					'".$this->_getNextSequence($idTest)."', 
					'".$this->_getPageNumber($idTest)."',
					'".( isset($_POST['shuffle']) ? 1 : 0 )."' ) ";

			if(!mysql_query($ins_query)) {

				errorCommunication($lang->def('_TEST_ERR_INS_QUEST')
				.getBackUi('index.php?modname=question&amp;op=create&amp;type_quest='
				.$this->getQuestionType().'&amp;idTest='.$idTest.'&amp;back_test='.$url_encode, $lang->def('_BACK')));
			}
			//find id of auto_increment colum
			list($idBankQuest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			if(!$idBankQuest) {
				errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
			}

			/*nếu thêm question vào test, đưa vào _testquest*/
			if($idTest != 0)
			{
				$ins_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
					( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle ) VALUES
					( 	'".(int)$idTest."', 
						'".(int)$_POST['idCategory']."', 
						'".$this->getQuestionType()."', 
						'".$_POST['title_quest']."',
						'".(int)$_POST['difficult']."',
						'".(int)$_POST['time_assigned']."', 
						'".$this->_getNextSequence($idTest)."', 
						'".$this->_getPageNumber($idTest)."',
						'".( isset($_POST['shuffle']) ? 1 : 0 )."' ) ";
				if(!mysql_query($ins_query)) {

					errorCommunication($lang->def('_TEST_ERR_INS_QUEST')
					.getBackUi('index.php?modname=question&amp;op=create&amp;type_quest='
					.$this->getQuestionType().'&amp;idTest='.$idTest.'&amp;back_test='.$url_encode, $lang->def('_BACK')));
				}
				//find id of auto_increment colum
				list($idQuest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
				if(!$idQuest) {
					errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
				}
			}

			/*insert answer vào bank*/
			for($i = 0; $i < $num_answer; $i++) {
				//insert answer
				$ins_answer_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_bankquestanswer 
					( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
					( 	'".$idBankQuest."', 
						'".( isset($_POST['is_correct'][$i]) ? 1 : 0 )."', 
						'".$_POST['answer'][$i]."', 
						'".$_POST['comment'][$i]."', 
						'".$this->_checkScore($_POST['score_correct'][$i])."', 
						'".$this->_checkScore($_POST['score_incorrect'][$i])."') ";
				if(!mysql_query($ins_answer_query)) {
					errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
				}
			}

			//insert answer vào test
			if($idTest != 0)
			{
				for($i = 0; $i < $num_answer; $i++) {
					//insert answer
					$ins_answer_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer 
					( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
					( 	'".$idQuest."', 
						'".( isset($_POST['is_correct'][$i]) ? 1 : 0 )."', 
						'".$_POST['answer'][$i]."', 
						'".$_POST['comment'][$i]."', 
						'".$this->_checkScore($_POST['score_correct'][$i])."', 
						'".$this->_checkScore($_POST['score_incorrect'][$i])."') ";
					if(!mysql_query($ins_answer_query)) {
						errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
					}
				}
			}
			//back to question list
			jumpTo( ''.$back_test);
		}

		//insert form
		require_once($GLOBALS['where_lms'].'/lib/lib.questcategory.php');
		$categories = Questcategory::getCategory();
		//writing difficult array
		$arr_dufficult = array(5 => '5 - '.$lang->def('_VERY_HARD'), 4 => '4 - '.$lang->def('_HARD'), 3 => '3 - '.$lang->def('_MEDIUM'), 2 => '2 - '.$lang->def('_EASY'), 1 => '1 - '.$lang->def('_VERY_EASY'));

		$GLOBALS['page']->add(getTitleArea($lang->def('_TEST_SECTION'), 'test')
		.'<div class="std_block">'
		.getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))
		.'<div class="title_big">'
		.$lang->def('_QUEST_ACRN_'.strtoupper($this->getQuestionType())).' - '
		.$lang->def('_QUEST_'.strtoupper($this->getQuestionType()))
		.'</div><br />'
		.Form::openForm('form_add_quest', 'index.php?modname=question&amp;op=create')

		.Form::openElementSpace()
		.Form::getHidden('type_quest', 'type_quest', $this->getQuestionType())
		.Form::getHidden('idTest', 'idTest', $idTest)
		.Form::getHidden('back_test', 'back_test', $url_encode)
		.Form::getHidden('num_answer', 'num_answer', $num_answer)

		.Form::getTextarea($lang->def('_TEST_QUEST_TITLE'), 'title_quest', 'title_quest',
		( isset($_POST['title_quest']) ? stripslashes($_POST['title_quest']) : '' ) ), 'content');
		if (count($categories) > 1)
		$GLOBALS['page']->add(Form::getDropdown( $lang->def('_TEST_QUEST_CATEGORY'), 'idCategory', 'idCategory', $categories,
		( isset($_POST['idCategory']) ? $_POST['idCategory'] : '' )), 'content');
		$GLOBALS['page']->add(Form::getDropdown( $lang->def('_TEST_DIFFICULT'), 'difficult', 'difficult', $arr_dufficult,
		( isset($_POST['difficult']) ? $_POST['difficult'] : 3 ))
		.Form::getCheckbox($lang->def('_TEST_QUEST_SHUFFLE'), 'shuffle', 'shuffle', '1', ( isset($_POST['shuffle']) ? 1 : 0 ) )
		.Form::getTextfield( $lang->def('_TEST_QUEST_TIMEASS'), 'time_assigned', 'time_assigned', 5,
		( isset($_POST['time_assigned']) ? $_POST['time_assigned'] : '00000' ), $lang->def('_TEST_QUEST_TIMEASS'),
		$lang->def('_TIME_SECOND') )
		.'<div class="no_float"></div><br />'
		.'<table class="test_answer" cellspacing="0" summary="'.$lang->def('_TEST_ANSWER_SUMMARY').'">'."\n"
		.'<caption>'.$lang->def('_TEST_ANSWER').'</caption>'."\n"
		.'<tr>'
		.'<th class="image">'.$lang->def('_TEST_CORRECT').'</th>'
		.'<th>'.$lang->def('_TEST_TEXT_ANSWER').'</th>'
		.'<th>'.$lang->def('_TEST_COMMENT').'</th>'
		.'<th colspan="2">'.$lang->def('_TEST_POINT').'</th>'
		.'</tr>'."\n", 'content');
		for($i = 0; $i < $num_answer; $i++) {
			$this->_lineAnswer($i);
		}
		$GLOBALS['page']->add('</table>'
		.Form::getButton( 'more_answer', 'more_answer', $lang->def('_TEST_ADD_ONE_ANSWER'), 'button_nowh' ), 'content');
		if($num_answer > 1) 	$GLOBALS['page']->add(Form::getButton( 'less_answer', 'less_answer', $lang->def('_TEST_SUB_ONE_ANSWER'), 'button_nowh' ), 'content');
		$GLOBALS['page']->add(
			'' // Form::getButton( 'select_from_libraries', 'select_from_libraries', $lang->def('_TEST_SEL_LIBRARIES'), 'button_nowh' )
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('add_question', 'add_question', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
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
	function edit( $back_test, $isInBank=false ) {
		$lang =& DoceboLanguage::createInstance('test');

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$url_encode = htmlentities(urlencode($back_test));

		//manage number of answer
		$num_answer = importVar('num_answer', true, 2);
		if(isset($_POST['more_answer'])) ++$num_answer;
		if(isset($_POST['less_answer']) && ($num_answer > 1) ) --$num_answer;

		if($isInBank)
		{
			$tableNamePrefix = "_bank";
		}
		else
		{
			$tableNamePrefix = "_test";
		}

		if(isset($_POST['add_question'])) {

			//update question
			$ins_query = "
					UPDATE ".$GLOBALS['prefix_lms'].$tableNamePrefix."quest
					SET idCategory = '".(int)$_POST['idCategory']."', 
						type_quest = '".$this->getQuestionType()."', 
						title_quest = '".$_POST['title_quest']."', 
						difficult = '".(int)$_POST['difficult']."', 
						time_assigned = '".(int)$_POST['time_assigned']."',
						shuffle = '".(isset($_POST['shuffle']) ? 1 : 0)."'
					WHERE idQuest = '".(int)$this->id."'";

			if(!mysql_query($ins_query)) {

				$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_ERR_INS_QUEST')
				.getBackUi('index.php?modname=question&amp;op=edit&amp;type_quest='
				.$this->getQuestionType().'&amp;idQuest='.$this->id.'&amp;back_test='.$url_encode, $lang->def('_BACK'))), 'content');
			}
			//update answer
			if( !isset($_POST['is_correct']) ) $_POST['is_correct'] = -1;

			//find saved answer

			$re_answer = mysql_query("
					SELECT idAnswer 
					FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
					WHERE idQuest = '".(int)$this->id."'");

			while(list($id_a) = mysql_fetch_row($re_answer)) $existent_answer[$id_a] = 1;

			for($i = 0; $i < $num_answer; $i++) {
				//scannig answer
				if( isset($_POST['answer_id'][$i]) ) {
					//must update
					$idAnswer = $_POST['answer_id'][$i];
					if(isset($existent_answer[$idAnswer])) unset($existent_answer[$idAnswer]);

					$upd_ans_query = "
							UPDATE ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
							SET is_correct = '".( isset($_POST['is_correct'][$i]) ? 1 : 0 )."',
								answer = '".$_POST['answer'][$i]."',
								comment = '".$_POST['comment'][$i]."',
								score_correct = '".$this->_checkScore($_POST['score_correct'][$i])."', 
								score_incorrect = '".$this->_checkScore($_POST['score_incorrect'][$i])."'
							WHERE idAnswer = '".(int)$idAnswer."'";

					if(!mysql_query($upd_ans_query)) {
						$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))), 'content');
					}
				}
				else {
					//insert new answer

					$ins_answer_query = "
							INSERT INTO ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
							( idQuest, is_correct, 
								answer, comment,
								score_correct, score_incorrect ) VALUES
							( '".$this->id."', '".( isset($_POST['is_correct'][$i]) ? 1 : 0 )."', 
								'".$_POST['answer'][$i]."', '".$_POST['comment'][$i]."', 
								'".$this->_checkScore($_POST['score_correct'][$i])."', 
								'".$this->_checkScore($_POST['score_incorrect'][$i])."') ";

					if(!mysql_query($ins_answer_query)) {

						$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))), 'content');
					}
				}
			}
			while(list($idA) = each($existent_answer)) {
				//i must delete these answer

				$del_answer_query = "
						DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer
						WHERE idQuest = '".(int)$this->id."' AND idAnswer = '".(int)$idA."'";

				if(!mysql_query($del_answer_query)) {

					$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))), 'content');
				}
			}
			//back to question list
			jumpTo( ''.$back_test);
		}

		require_once($GLOBALS['where_lms'].'/lib/lib.questcategory.php');
		$categories = Questcategory::getCategory();
		//writing difficult array
		$arr_dufficult = array(5 => '5 - '.$lang->def('_VERY_HARD'), 4 => '4 - '.$lang->def('_HARD'), 3 => '3 - '.$lang->def('_MEDIUM'), 2 => '2 - '.$lang->def('_EASY'), 1 => '1 - '.$lang->def('_VERY_EASY'));

		//load data
		if(!isset($_POST['answer_id'])) {
			list($sel_cat, $quest, $sel_diff, $sel_time, $shuffle) = mysql_fetch_row(mysql_query("
			SELECT idCategory, title_quest, difficult, time_assigned, shuffle 
			FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."quest 
			WHERE idQuest = '".(int)$this->id."'"));

			$re_answer = mysql_query("
			SELECT idAnswer, is_correct, answer, comment, score_correct, score_incorrect 
			FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
			WHERE idQuest = '".(int)$this->id."'
			ORDER BY idAnswer");

			$i_load = 0;
			while(list(
			$_POST['answer_id'][$i_load],
			$is_correct,
			$_POST['answer'][$i_load],
			$_POST['comment'][$i_load],
			$_POST['score_correct'][$i_load],
			$_POST['score_incorrect'][$i_load] ) = mysql_fetch_row($re_answer)){
				if($is_correct) $_POST['is_correct'][$i_load] = 1;
				++$i_load;
			}
			$num_answer = $i_load;
		}
		$GLOBALS['page']->add(getTitleArea($lang->def('_TEST_SECTION'), 'test')
		.'<div class="std_block">'
		.getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))
		.'<div class="title_big">'
		.$lang->def('_QUEST_ACRN_'.strtoupper($this->getQuestionType())).' - '
		.$lang->def('_QUEST_'.strtoupper($this->getQuestionType()))
		.'</div><br />'
		.Form::openForm('form_add_quest', 'index.php?modname=question&amp;op=edit')

		.Form::openElementSpace()
		.Form::getHidden('type_quest', 'type_quest', $this->getQuestionType())
		.Form::getHidden('idQuest', 'idQuest', $this->id)
		.Form::getHidden('back_test', 'back_test', $url_encode)
		.Form::getHidden('num_answer', 'num_answer', $num_answer)
		/*danhut: thêm trường đánh dấu lưu vào bank hay test*/
		.Form::getHidden('isInBank', 'isInBank', $isInBank)

		.Form::getTextarea($lang->def('_TEST_QUEST_TITLE'), 'title_quest', 'title_quest',
		( isset($_POST['title_quest']) ? stripslashes($_POST['title_quest']) : $quest ) ), 'content');
		if (count($categories) > 1)
		$GLOBALS['page']->add(Form::getDropdown( $lang->def('_TEST_QUEST_CATEGORY'), 'idCategory', 'idCategory', $categories,
		( isset($_POST['idCategory']) ? $_POST['idCategory'] : $sel_cat )), 'content');
		$GLOBALS['page']->add(Form::getDropdown( $lang->def('_TEST_DIFFICULT'), 'difficult', 'difficult', $arr_dufficult,
		( isset($_POST['difficult']) ? $_POST['difficult'] : $sel_diff ))
		.Form::getCheckbox($lang->def('_TEST_QUEST_SHUFFLE'), 'shuffle', 'shuffle', '1', $shuffle)
		.Form::getTextfield( $lang->def('_TEST_QUEST_TIMEASS'), 'time_assigned', 'time_assigned', 5,
		( isset($_POST['time_assigned']) ? $_POST['time_assigned'] : $sel_time ), $lang->def('_TEST_QUEST_TIMEASS'),
		$lang->def('_TIME_SECOND') )
		.'<div class="no_float"></div><br />'
		
		.'<table class="test_answer" cellspacing="0" summary="'.$lang->def('_TEST_ANSWER_SUMMARY').'">'."\n"
		.'<caption>'.$lang->def('_TEST_ANSWER').'</caption>'."\n"
		.'<tr>'
		.'<th class="image">'.$lang->def('_TEST_CORRECT').'</th>'
		.'<th>'.$lang->def('_TEST_TEXT_ANSWER').'</th>'
		.'<th>'.$lang->def('_TEST_COMMENT').'</th>'
		.'<th colspan="2">'.$lang->def('_TEST_POINT').'</th>'
		.'</tr>'."\n", 'content');
		for($i = 0; $i < $num_answer; $i++) {
			$this->_lineModAnswer($i);
		}
		$GLOBALS['page']->add('</table>'
		.Form::getButton( 'more_answer', 'more_answer', $lang->def('_TEST_ADD_ONE_ANSWER'), 'button_nowh' ), 'content');
		if($num_answer > 1) 	$GLOBALS['page']->add(Form::getButton( 'less_answer', 'less_answer', $lang->def('_TEST_SUB_ONE_ANSWER'), 'button_nowh' ), 'content');
		$GLOBALS['page']->add('' // Form::getButton( 'select_from_libraries', 'select_from_libraries', $lang->def('_TEST_SEL_LIBRARIES'), 'button_nowh' )
		.Form::closeElementSpace()

		.Form::openButtonSpace()
		.Form::getButton('add_question', 'add_question', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
	}

	/**
	 * this function delete the question with the idQuest saved in the variable $this->id
	 *
	 * @return bool	if the operation success return true else return false
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function del($isInBank=false) {

		if($isInBank)
		{
			$tableNamePrefix = "_bank";
		}
		else
		{
			$tableNamePrefix = "_test";
			//xóa những câu trả lời đã trả lời
			if(!$isInBank)
			{
				if(!mysql_query("
				DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
				WHERE idQuest = '".$this->id."'"))
				{
					return false;
				}
			}
		}



		//remove answer
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
		WHERE idQuest = '".$this->id."'")) {
		return false;
		}
		//remove question
		return mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."quest 
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
	function copy( $new_id_test, $back_test = NULL, $isInBank = false ) {


		if($isInBank)
		{
			$tableNamePrefix = "_bank";
		}
		else
		{
			$tableNamePrefix = "_test";
		}
		//retriving question
		list($sel_cat, $quest, $sel_diff, $time_ass, $sequence, $page, $shuffle) = mysql_fetch_row(mysql_query("
		SELECT idCategory, title_quest, difficult, time_assigned, sequence, page, shuffle 
		FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."quest 
		WHERE idQuest = '".(int)$this->id."'"));
		//insert question
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
		( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle ) VALUES 
		( 	'".(int)$new_id_test."', 
			'".(int)$sel_cat."', 
			'".$this->getQuestionType()."', 
			'".mysql_escape_string($quest)."',
			'".(int)$sel_diff."', 
			'".$time_ass."',
			'".(int)$sequence."',
			'".(int)$page."', 
			'".(int)$shuffle."' ) ";
		if(!mysql_query($ins_query)) return false;
		//find id of auto_increment colum
		list($new_id_quest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$new_id_quest) return false;

		//retriving new answer
		$re_answer = mysql_query("
		SELECT idAnswer, is_correct, answer, comment, score_correct, score_incorrect 
		FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");
		while(list($idAnswer, $is_correct, $answer, $comment, $score_c, $score_inc) = mysql_fetch_row($re_answer)) {

			//insert answer
			$ins_answer_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer 
			( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
			( 	'".(int)$new_id_quest."', 
				'".(int)$is_correct."', 
				'".mysql_escape_string($answer)."', 
				'".mysql_escape_string($comment)."',
				'".$this->_checkScore($score_c)."', 
				'".$this->_checkScore($score_inc)."') ";
			if(!mysql_query($ins_answer_query)) return false;
		}
		return $new_id_quest;
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
		$lang =& DoceboLanguage::createInstance('test');

		list($id_quest, $title_quest, $shuffle) = mysql_fetch_row(mysql_query("
		SELECT idQuest, title_quest, shuffle 
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idQuest = '".$this->id."'"));

		$query_answer = "
		SELECT idAnswer, answer 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'";
		if($shuffle_answer || $shuffle) $query_answer .= " ORDER BY RAND()";
		else $query_answer .= " ORDER BY idAnswer";
		$re_answer = mysql_query($query_answer);

		$find_prev = false;
		$id_answer_do = 0;
		if($id_track != 0) {

			//recover previous information
			$recover_answer = "
			SELECT idAnswer 
			FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
			WHERE idQuest = '".(int)$this->id."' AND 
				idTrack = '".(int)$id_track."'";
			$re_answer_do = mysql_query($recover_answer);
			if(mysql_num_rows($re_answer_do)) {

				//find previous answer
				$find_prev = true;
				while(list($id_a) = mysql_fetch_row($re_answer_do)) $answer_do[$id_a] = 1;
			}
		}

		$content = '<div class="play_question">'
		.'<div>'.$lang->def('_QUEST_'.strtoupper($this->getQuestionType())).'</div>'
		.'<div class="title_question">'.$num_quest.') '.$title_quest.'</div>'
		.'<div class="answer_question">';
		while(list($id_answer, $answer) = mysql_fetch_row($re_answer)){

			$content .= '<input type="checkbox" id="quest_'.$id_quest.'_'.$id_answer.'" '
			.'name="quest['.$id_quest.']['.$id_answer.']" value="1"'
			.( ($find_prev && isset($answer_do[$id_answer])) ? ' checked="checked"' : '' )
			.( $find_prev && $freeze ? ' disabled="disabled"' : '' ).' /> '
			.'<label class="text_answer" for="quest_'.$id_quest.'_'.$id_answer.'">'.$answer.'</label><br />';
		}
		$content .= '</div>'
		.'</div>';
		return $content;
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


		$result = true;

		if($this->userDoAnswer($id_track)) {
			if(!$can_overwrite) return true;
			if(!$this->deleteAnswer($id_track)) return false;
		}

		$re_answer = mysql_query("
		SELECT idAnswer, is_correct, score_correct, score_incorrect 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'");
		while(list($id_answer, $is_correct, $score_corr, $score_incorr) = mysql_fetch_row($re_answer)) {

			if(isset($source['quest'][$this->id][$id_answer])) {

				//answer checked by the user
				$track_query = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_testtrack_answer ( idTrack, idQuest, idAnswer, score_assigned, more_info ) 
				VALUES (
					'".(int)$id_track."', 
					'".(int)$this->id."', 
					'".(int)$id_answer."', 
					'".( $is_correct ? $score_corr : -$score_incorr )."', 
					'' )";
				$result &= mysql_query($track_query);
			} elseif($is_correct && ($score_incorr != 0)) {

				//answer correct with penality but not checked by the user
				$track_query = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_testtrack_answer ( idTrack, idQuest, idAnswer, score_assigned, more_info ) 
				VALUES (
					'".(int)$id_track."', 
					'".(int)$this->id."', 
					'".(int)$id_answer."', 
					'".-$score_incorr."', 
					'' )";
				$result &= mysql_query($track_query);
			} elseif(!$is_correct && ($score_corr != 0)) {
				//answer correct with penality but not checked by the user
				$track_query = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_testtrack_answer ( idTrack, idQuest, idAnswer, score_assigned, more_info ) 
				VALUES (
					'".(int)$id_track."', 
					'".(int)$this->id."', 
					'".(int)$id_answer."', 
					'".$score_corr."', 
					'' )";
				$result &= mysql_query($track_query);
			}
		}
		return $result;
	}

	/**
	 * save the answer to the question in an proper format overwriting the old entry
	 *
	 * @param  int		$id_track	the relative id_track
	 * @param  array	$source		source of the answer send by the user
	 *
	 * @return bool	true if success false otherwise
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _updateAnswer( $id_track, &$source ) {


		if(!$this->deleteAnswer($id_track)) return false;
		else return $this->storeAnswer($id_track, $source, false);
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


		return mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
		WHERE idTrack = '".(int)$id_track."' AND 
			idQuest = '".$this->id."'");
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


		return 'auto';
	}

	/**
	 * display the question with the result of a user
	 *
	 * @param  	int		$id_track		the test relative to this question
	 * @param  	int		$num_quest		the quest sequqnce number
	 *
	 * @return array	return an array with xhtml code in this way
	 * 					string	'quest' 	=> the quest,
	 *					double	'score'		=> score obtained from this question,
	 *					string	'comment'	=> relative comment to the quest )
	 *
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function displayUserResult( $id_track, $num_quest, $show_solution ) {
		$lang =& DoceboLanguage::createInstance('test');

		$quest = '';
		$comment = '';
		$com_is_correct = '';

		list($id_quest, $title_quest) = mysql_fetch_row(mysql_query("
		SELECT idQuest, title_quest 
		FROM ".$GLOBALS['prefix_lms']."_testquest 
		WHERE idQuest = '".$this->id."'"));

		$query_answer = "
		SELECT idAnswer, is_correct, answer, comment 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer";
		$re_answer = mysql_query($query_answer);

		$recover_answer = "
		SELECT idAnswer 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
		WHERE idQuest = '".(int)$this->id."' AND 
			idTrack = '".(int)$id_track."'";
		$recover_answer = "
			SELECT idAnswer 
			FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
			WHERE idQuest = '".(int)$this->id."' AND 
				idTrack = '".(int)$id_track."'";
		$re_answer_do = mysql_query($recover_answer);
		while(list($id_a) = mysql_fetch_row($re_answer_do)) {
			$answer_do[$id_a] = 1;
		}
		$quest =
			'<div class="play_question">'
			.'<div class="title_question">'.$num_quest.') '.$title_quest.'</div>'
			.'<div class="answer_question">';
			while(list($id_answer, $is_correct, $answer, $comm) = mysql_fetch_row($re_answer)){

				//if(isset($answer_do[$id_answer]) && ( ($is_correct && $answer_do[$id_answer] > 0) || (!$is_correct && $answer_do[$id_answer] <= 0) ) ) {
				if(isset($answer_do[$id_answer] )) {
					$quest .= '<img src="'.getPathImage().'test/check.gif" title="'.$lang->def('_TEST_ANSWER_CHECK').'" '
					.'alt="'.$lang->def('_TEST_ANSWER_CHECK').'" />&nbsp;'
					.$answer.'&nbsp;';
					if($is_correct) {
						$quest .= '<span class="test_answer_correct">'.$lang->def('_TEST_CORRECT').'</span>';
					} else {
						$quest .= '<span class="test_answer_incorrect">'.$lang->def('_TEST_INCORRECT').'</span>';
						$comment .= '<br />'.$answer.' <span class="text_bold">'.$lang->def('_TEST_NOT_MC_THECORRECT').' : </span>'
						.$comm.'<br />';
					}
					$quest .= '<br />';
				}
				else{

					if($is_correct && $show_solution) {
						$com_is_correct .= '<span class="text_bold">'.$lang->def('_TEST_NOT_THECORRECT').' : </span>'.$answer.'<br />';
					}
					$quest .= '<img src="'.getPathImage().'test/notcheck.gif" title="'.$lang->def('_TEST_ANSWER_NOTCHECK').'" '
					.'alt="'.$lang->def('_TEST_ANSWER_NOTCHECK').'" />&nbsp;'
					.$answer.'<br />';
				}
			}
			$quest .= '</div>'
			.'</div>';

			return array(	'quest' 	=> $quest,
						'score'		=> $this->userScore($id_track), 
						'comment'	=> $com_is_correct.$comment );
	}


}

?>