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

class Associate_Question extends Question {

	/**
	 * class constructor
	 *
	 * @param int	the unique database identifer of a question
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function Associate_Question( $id ) {
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
		return 'associate';
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
		.'<td class="access-only valign_top align_center">'
		.'<label for="elem_a_'.$i.'">'.$lang->def('_TEST_QUEST_ELEM').': '.($i + 1).'</label>'
		.'</td>'
		.'<td class="image">'
		//.Form::getTextarea('', 'elem_a_'.$i, 'elem_a['.$i.']', ( isset($_POST['elem_a'][$i]) ? stripslashes($_POST['elem_a'][$i]) : '' ),false,'','form_line_l','floating','textarea',true)


		.loadHtmlEditor('',
							'elem_a_'.$i, 
							'elem_a['.$i.']', 
		( isset($_POST['elem_a'][$i]) ? stripslashes($_POST['elem_a'][$i]) : ''),
		false,
							'', 
		true)

		//.'<textarea class="test_area_answer" id="elem_a_'.$i.'" name="elem_a['.$i.']" cols="19" rows="3">'
		//.( isset($_POST['elem_a'][$i]) ? stripslashes($_POST['elem_a'][$i]) : '' ) //$lang->def('_QUEST_ANSWER')
		//.'</textarea>'
		.'</td>'
		.'<td class="access-only valign_top align_center">'
		.'<label for="elem_b_'.$i.'">'.$lang->def('_TEST_QUEST_ELEM').': '.($i + 1).'</label>'
		.'</td>'
		.'<td class="image">'

		.loadHtmlEditor('',
							'elem_b_'.$i, 
							'elem_b['.$i.']', 
		( isset($_POST['elem_b'][$i]) ? stripslashes($_POST['elem_b'][$i]) : ''),
		false,
							'', 
		true)

		//.Form::getTextarea('', 'elem_b_'.$i, 'elem_b['.$i.']', ( isset($_POST['elem_b'][$i]) ? stripslashes($_POST['elem_b'][$i]) : '' ),false,'','form_line_l','floating','textarea',true)
		//.'<textarea class="test_area_answer" id="elem_b_'.$i.'" name="elem_b['.$i.']" cols="19" rows="3">'
		//.( isset($_POST['elem_b'][$i]) ? stripslashes($_POST['elem_b'][$i]) : '' ) //$lang->def('_QUEST_ANSWER')
		//.'</textarea>'
		.'</td>'
		.'</tr>'."\n", 'content');
	}

	/**
	 * this function write a gui for answer insertion
	 *
	 * @param  int	$i	indicate the line number
	 * @return nothing
	 *
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _lineAssociateAnswer($i, $content_field_b) {
		$lang =& DoceboLanguage::createInstance('test');

		$GLOBALS['page']->add('<tr class="line_answer">'
		.'<td rowspan="2">', 'content');
		if(isset($_POST['elem_a'][$i])) {

			$GLOBALS['page']->add('<label for="associate_b_'.$i.'">'.($i + 1).') '.stripslashes($_POST['elem_a'][$i]).'</label>'
			.'<input type="hidden" name="elem_a['.$i.']" value="'.base64_encode($_POST['elem_a'][$i]).'" />', 'content');
		}
		$GLOBALS['page']->add('</td>'
		.'<td rowspan="2">', 'content');
		if(isset($_POST['elem_b'][$i])) {
			$GLOBALS['page']->add('<input type="hidden" name="elem_b['.$i.']" value="'.base64_encode($_POST['elem_b'][$i]).'" />'
			.'<select id="associate_b_'.$i.'" name="associate_b['.$i.']">'
			.$content_field_b
			.'</select>', 'content');
		}
		$GLOBALS['page']->add('</td>'
		.'<td rowspan="2" class="image">'
		//comment
		.'<label for="comment_'.$i.'">'.$lang->def('_TEST_COMMENT').'</label>'
		.'<textarea class="test_comment" id="comment_'.$i.'" name="comment['.$i.']" cols="14" rows="3">'
		.'</textarea>'
		.'</td>'
		.'<td class="test_ifcorrect">'
		.'<label for="score_correct_'.$i.'">'.$lang->def('_TEST_IFCORRECT').'</label>'
		.'</td>'
		.'<td class="align_right">'
		//score correct
		.'<input type="text" class="test_point" id="score_correct_'.$i.'" name="score_correct['.$i.']" alt="'.$lang->def('_TEST_IFCORRECT').'" size="5" value="0.0" />'
		.'</td>'
		.'</tr>'."\n"
		.'<tr class="line_answer">'
		.'<td class="test_ifcorrect">'
		.'<label for="score_incorrect_'.$i.'">'.$lang->def('_TEST_IFINCORRECT').'</label>'
		.'</td>'
		.'<td class="align_right">'
		//score incorrect
		.'- <input type="text" class="test_point" id="score_incorrect_'.$i.'" name="score_incorrect['.$i.']" alt="'.$lang->def('_TEST_IFINCORRECT').'" size="5" value="0.0" />'
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
		.'<td class="access-only valign_top align_center">'
		.'<input type="hidden" name="elem_a_id['.$i.']" value="'
		.( isset($_POST['elem_a_id'][$i]) ? $_POST['elem_a_id'][$i] : 0).'">'
		.'<label for="elem_a_'.$i.'">'.$lang->def('_TEST_QUEST_ELEM').': '.($i + 1).'</label>'
		.'</td>'
		.'<td class="image">'
		//.Form::getTextarea('', 'elem_a_'.$i, 'elem_a['.$i.']', ( isset($_POST['elem_a'][$i]) ? stripslashes($_POST['elem_a'][$i]) : '' ),false,'','form_line_l','floating','textarea',true)
			
		.loadHtmlEditor('',
							'elem_a_'.$i, 
							'elem_a['.$i.']', 
		( isset($_POST['elem_a'][$i]) ? stripslashes($_POST['elem_a'][$i]) : ''),
		false,
							'', 
		true)
			
		//.'<textarea class="test_area_answer" id="elem_a_'.$i.'" name="elem_a['.$i.']" cols="19" rows="3">'
		//.( isset($_POST['elem_a'][$i]) ? stripslashes($_POST['elem_a'][$i]) : '' )
		//.'</textarea>'
		.'</td>'
		.'<td class="access-only valign_top align_center">'
		.'<input type="hidden" name="elem_b_id['.$i.']" value="'
		.( isset($_POST['elem_b_id'][$i]) ? $_POST['elem_b_id'][$i] : 0).'">'
		.'<label for="elem_b_'.$i.'">'.$lang->def('_TEST_QUEST_ELEM').': '.($i + 1).'</label>'
		.'</td>'
		.'<td class="image">'
		//.Form::getTextarea('', 'elem_b_'.$i, 'elem_b['.$i.']', ( isset($_POST['elem_b'][$i]) ? stripslashes($_POST['elem_b'][$i]) : '' ),false,'','form_line_l','floating','textarea',true)

		/*.loadHtmlEditor('',
		 'elem_b_'.$i,
		 'elem_b['.$i.']',
		 ( isset($_POST['elem_b'][$i]) ? stripslashes($_POST['elem_b'][$i]) : ''),
		 false,
		 '',
		 true)*/
		.'<textarea class="test_area_answer" id="elem_b_'.$i.'" name="elem_b['.$i.']" cols="19" rows="3">'
		.( isset($_POST['elem_b'][$i]) ? stripslashes($_POST['elem_b'][$i]) : '' )
		.'</textarea>'
		.'</td>'
		.'</tr>'."\n", 'content');
	}

	/**
	 * this function write a gui for answer insertion
	 *
	 * @param  int	$i	indicate the line number
	 * @return nothing
	 *
	 * @access private
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function _lineModAssociateAnswer($i, $content_field_b) {
		$lang =& DoceboLanguage::createInstance('test');

		$GLOBALS['page']->add('<tr class="line_answer">'
		.'<td rowspan="2">', 'content');
		if(isset($_POST['elem_a_id'][$i])) {
			$GLOBALS['page']->add('<input type="hidden" id="elem_a_id_'.$i.'" name="elem_a_id['.$i.']" value="'.$_POST['elem_a_id'][$i].'" />', 'content');
		}
		if(isset($_POST['elem_a'][$i])) {

			$GLOBALS['page']->add('<input type="hidden" name="elem_a['.$i.']" value="'.base64_encode($_POST['elem_a'][$i]).'" />'
			.'<label for="associate_b_'.$i.'">'.($i + 1).') '.stripslashes($_POST['elem_a'][$i]).'</label>', 'content');
		}
		$GLOBALS['page']->add('</td>'
		.'<td rowspan="2">', 'content');
		if(isset($_POST['elem_b_id'][$i])) {
			$GLOBALS['page']->add('<input type="hidden" id="elem_b_id_'.$i.'" name="elem_b_id['.$i.']" value="'.$_POST['elem_b_id'][$i].'" />', 'content');
		}
		if(isset($_POST['elem_b'][$i])) {
			$GLOBALS['page']->add('<input type="hidden" name="elem_b['.$i.']" value="'.base64_encode($_POST['elem_b'][$i]).'" />'
			.'<select id="associate_b_'.$i.'" name="associate_b['.$i.']">'
			.$content_field_b
			.'</select>', 'content');
		}
		$GLOBALS['page']->add('</td>'
		.'<td rowspan="2" class="image">'
		//comment
		.'<label for="comment_'.$i.'">'.$lang->def('_TEST_COMMENT').'</label>'
		.'<textarea class="test_comment" id="comment_'.$i.'" name="comment['.$i.']" cols="14" rows="3">'
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
			//insert second group
			$num_group = count($_POST['elem_b']);
			/**
			 * danhut: lưu id của question mới trong bank
			 */
			$id_in_bank_assigned = array();
			/**
			 * danhut: lưu id của question mới trong testquest
			 */
			$id_assigned = array();
			for($j = 0; $j < $num_group; $j++) {

				$content = base64_decode($_POST['elem_b'][$j]);
				/**
				 * danhut: trước tiên luôn đưa question mới vào bank
				 */
				if($content != '') {
					$ins_answer_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_bankquestanswer_associate 
					( idQuest, answer ) VALUES
					( 	'".$_POST['idQuestInBank']."', 
						'".$content."') ";
					if(!mysql_query($ins_answer_query)) {

						errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
					}
					list($id_a) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
					$id_in_bank_assigned[$j] = $id_a;
				}
				/**
				 * danhut: nếu là thêm question cho 1 bài test nào đó thì mới đưa question vào table testquest
				 */
				if($content != '' && $idTest != 0) {
					$ins_answer_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer_associate 
					( idQuest, answer ) VALUES
					( 	'".$_POST['idQuest']."', 
						'".$content."') ";
					if(!mysql_query($ins_answer_query)) {

						errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
					}
					list($id_a) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
					$id_assigned[$j] = $id_a;
				}
			}
			//insert answer of first group
			for($i = 0; $i < $num_answer; $i++) {
				//insert answer
				$elem_asso = $_POST['associate_b'][$i];
				/**
				 * danhut: insert answer for question in bank first
				 */
				$ins_answer_query = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_bankquestanswer 
				( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
				( 	'".$_POST['idQuestInBank']."', 
					'".$id_in_bank_assigned[$elem_asso]."', 
					'".base64_decode($_POST['elem_a'][$i])."', 
					'".$_POST['comment'][$i]."', 
					'".$this->_checkScore($_POST['score_correct'][$i])."', 
					'".$this->_checkScore($_POST['score_incorrect'][$i])."') ";
				if(!mysql_query($ins_answer_query)) {

					errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
				}

				/**
				 * danhut: nếu thêm question cho 1 test nào đó thì mới thêm answer cho question đó trong _testquestanswer
				 */
				if($idTest != 0)
				{
					$ins_answer_query = "
						INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer 
						( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
						( 	'".$_POST['idQuest']."', 
							'".$id_assigned[$elem_asso]."', 
							'".base64_decode($_POST['elem_a'][$i])."', 
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
		} elseif(isset($_POST['do_association'])) {

			//----------------------------------------------------------------------------------------

			/**
			 * danhut: insert new question in bank first
			 *
			 */

			$ins_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_bankquest 
					( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle ) VALUES
					( 	'".$idTest."', 
						'".(int)$_POST['idCategory']."', 
						'".$this->getQuestionType()."', 
						'".$_POST['title_quest']."',
						'".(int)$_POST['difficult']."', 
						'".(int)$_POST['time_assigned']."', 
						'".(int)$this->_getNextSequence($idTest)."', 
						'".$this->_getPageNumber($idTest)."',
						'".( isset($_POST['shuffle']) ? 1 : 0 )."' ) ";
			if(!mysql_query($ins_query)) {

				errorCommunication($lang->def('_TEST_ERR_INS_QUEST')
				.getBackUi('index.php?modname=question&amp;op=create&amp;type_quest='
				.$this->getQuestionType().'&amp;idTest='.$idTest.'&amp;back_test='.$url_encode, $lang->def('_BACK')));
			}
			//find id of auto_increment colum
			list($idQuestInBank) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			if(!$idQuestInBank) {
				errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
			}

			//insert the new question in _testquest if question is created for a specific test
			if($idTest != 0)
			{
				$ins_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_testquest 
					( idTest, idCategory, type_quest, title_quest, difficult, time_assigned, sequence, page, shuffle ) VALUES
					( 	'".$idTest."', 
						'".(int)$_POST['idCategory']."', 
						'".$this->getQuestionType()."', 
						'".$_POST['title_quest']."',
						'".(int)$_POST['difficult']."', 
						'".(int)$_POST['time_assigned']."', 
						'".(int)$this->_getNextSequence($idTest)."', 
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
			//save groups a and b
			$content_a = $content_b = '';
			for($i = 0; $i < $num_answer; $i++) {
				if($_POST['elem_a'][$i] != '') {
					$content_a .= '<option value="'.$i.'">'.stripslashes($_POST['elem_a'][$i]).'</option>';
				}
				if($_POST['elem_b'][$i] != '') {
					$content_b .= '<option value="'.$i.'">'.stripslashes($_POST['elem_b'][$i]).'</option>';
				}
			}

			$GLOBALS['page']->add(
			getTitleArea($lang->def('_TEST_SECTION'), 'test')
			.'<div class="std_block">'
			.getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))
			.'<div class="title_big">'
			.$lang->def('_QUEST_ACRN_'.strtoupper($this->getQuestionType())).' - '
			.$lang->def('_QUEST_'.strtoupper($this->getQuestionType()))
			.'</div><br />'
			.Form::openForm('form_add_quest', 'index.php?modname=question&amp;op=create') , 'content');

			$GLOBALS['page']->add(
			Form::openElementSpace()
			.Form::getHidden('type_quest', 'type_quest', $this->getQuestionType())
			.Form::getHidden('idTest', 'idTest', $idTest)
			.Form::getHidden('back_test', 'back_test', $url_encode)
			.Form::getHidden('num_answer', 'num_answer', $num_answer)
			.Form::getHidden('idQuest', 'idQuest', $idQuest)
			/**
			 * danhut: lưu thêm id của question in bank
			 */
			.Form::getHidden('idQuestInBank', 'idQuestInBank', $idQuestInBank)
			.'<div class="no_float"></div><br />'
				
			.'<table class="test_answer" cellspacing="0" summary="'.$lang->def('_TEST_ANSWER_SUMMARY').'">'."\n"
			.'<caption>'.$lang->def('_TEST_ASSOCIATE').'</caption>'."\n"
			.'<tr>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_A').'</th>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_B').'</th>'
			.'<th>'.$lang->def('_TEST_COMMENT').'</th>'
			.'<th colspan="2">'.$lang->def('_TEST_POINT').'</th>'
			.'</tr>'."\n", 'content');
			for($i = 0; $i < $num_answer; $i++) {
				$this->_lineAssociateAnswer($i, $content_b);
			}
			$GLOBALS['page']->add(
				'</table>'
				.Form::closeElementSpace()

				.Form::openButtonSpace()
				.Form::getButton('add_question', 'add_question', $lang->def('_SAVE'))
				.Form::closeButtonSpace()
				.Form::closeForm()
				.'</div>', 'content');
					
		} else {

			//insert form

			require_once($GLOBALS['where_lms'].'/lib/lib.questcategory.php');
			$categories = Questcategory::getCategory();

			//writing difficult array
			$arr_dufficult = array(5 => '5 - '.$lang->def('_VERY_HARD'), 4 => '4 - '.$lang->def('_HARD'), 3 => '3 - '.$lang->def('_MEDIUM'), 2 => '2 - '.$lang->def('_EASY'), 1 => '1 - '.$lang->def('_VERY_EASY'));



			$GLOBALS['page']->add(
			getTitleArea($lang->def('_TEST_SECTION'), 'test')
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
			.'<th class="access-only">'.$lang->def('_TEST_QUEST_ELEM_NUM').'</th>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_A').'</th>'
			.'<th class="access-only ">'.$lang->def('_TEST_QUEST_ELEM_NUM').'</th>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_B').'</th>'
			.'</tr>'."\n", 'content');
			for($i = 0; $i < $num_answer; $i++) {
				$this->_lineAnswer($i);
			}
			$GLOBALS['page']->add(
				'</table>'
				.Form::getButton( 'more_answer', 'more_answer', $lang->def('_TEST_ADD_ONE_ANSWER'), 'button_nowh' ), 'content');
				if($num_answer > 1) $GLOBALS['page']->add(Form::getButton( 'less_answer', 'less_answer', $lang->def('_TEST_SUB_ONE_ANSWER'), 'button_nowh' ), 'content');
				$GLOBALS['page']->add(
				'' // Form::getButton( 'select_from_libraries', 'select_from_libraries', $lang->def('_TEST_SEL_LIBRARIES'), 'button_nowh' )
				.Form::closeElementSpace()
				.Form::openButtonSpace()
				.Form::getButton('do_association', 'do_association', $lang->def('_TEST_QUEST_SEL_ASSOCIATION'))
				.Form::closeButtonSpace()
				.Form::closeForm()
				.'</div>', 'content');
		}
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

			//save second group-----------------------------------------------

			$correct_answer = array();
			$existent_associate = array();


			$re_answer_asso = mysql_query("
					SELECT idAnswer
					FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
					WHERE idQuest = '".(int)$this->id."'");

			while(list($id_aa) = mysql_fetch_row($re_answer_asso)) $existent_associate[$id_aa] = 1;

			for($j = 0; $j < $num_answer; $j++) {

				$content = base64_decode($_POST['elem_b'][$j]);
				if($content != '') {

					if( isset($_POST['elem_b_id'][$j]) && ($_POST['elem_b_id'][$j] != 0) ) {

						//must update
						$id_old_a = $_POST['elem_b_id'][$j];
						if(isset($existent_associate[$id_old_a])) unset($existent_associate[$id_old_a]);


						$upd_ans_query = "
								UPDATE ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
								SET answer = '".$content."' 
								WHERE idAnswer = '".(int)$id_old_a."'";

						if(!mysql_query($upd_ans_query)) {

							errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
						}
						$id_assigned[$j] = $id_old_a;
					} else {
						//insert new answer

						$ins_answer_query = "
								INSERT INTO ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
								( idQuest, answer ) VALUES 
								( 	'".(int)$this->id."', 
									'".$content."' ) ";

						if(!mysql_query($ins_answer_query)) {

							errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
						}
						$id_assigned[$j] = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
					}
				}
			}

			while(list($id_aa) = each($existent_associate)) {
				//i must delete these answer

				$del_answer_query = "
						DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate
						WHERE idQuest = '".(int)$this->id."' AND idAnswer = '".(int)$id_aa."'";

				if(!mysql_query($del_answer_query)) {

					errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
				}
			}

			//first group-----------------------------------------------------
			//find saved answer

			$re_answer = mysql_query("
					SELECT idAnswer
					FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
					WHERE idQuest = '".(int)$this->id."'");

			while(list($id_a) = mysql_fetch_row($re_answer)) $existent_answer[$id_a] = 1;

			for($i = 0; $i < $num_answer; $i++) {
				//scannig answer
				$content = base64_decode($_POST['elem_a'][$i]);
				$elem_asso = $_POST['associate_b'][$i];

				if($content != '') {

					if( isset($_POST['elem_a_id'][$i]) && ($_POST['elem_a_id'][$i] != 0) ) {
						//must update
						$idAnswer = $_POST['elem_a_id'][$i];
						if(isset($existent_answer[$idAnswer])) unset($existent_answer[$idAnswer]);

						$upd_ans_query = "
								UPDATE ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
								SET is_correct = '".$id_assigned[$elem_asso]."',
									answer = '".$content."',
									comment = '".$_POST['comment'][$i]."',
									score_correct = '".$this->_checkScore($_POST['score_correct'][$i])."', 
									score_incorrect = '".$this->_checkScore($_POST['score_incorrect'][$i])."'
								WHERE idAnswer = '".(int)$idAnswer."'";

						if(!mysql_query($upd_ans_query)) {
							errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
						}
					} else {
						//insert new answer

						$ins_answer_query = "
								INSERT INTO ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
								( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
								( 	'".$this->id."', 
									'".$id_assigned[$elem_asso]."', 
									'".$content."', 
									'".$_POST['comment'][$i]."', 
									'".$this->_checkScore($_POST['score_correct'][$i])."', 
									'".$this->_checkScore($_POST['score_incorrect'][$i])."') ";

						if(!mysql_query($ins_answer_query)) {

							errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
						}
					}
				}
			}
			while(list($idA) = each($existent_answer)) {
				//i must delete these answer


				$del_answer_query = "
						DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer
						WHERE idQuest = '".(int)$this->id."' AND idAnswer = '".(int)$idA."'";

				if(!mysql_query($del_answer_query)) {

					errorCommunication($lang->def('_TEST_ERR_INS_ANSWER').getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK')));
				}
			}
			//back to question list
			jumpTo( ''.$back_test);
		} elseif(isset($_POST['do_association'])) {

			//----------------------------------------------------------------------------------------
			//insert the new question

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

				errorCommunication($lang->def('_TEST_ERR_INS_QUEST')
				.getBackUi('index.php?modname=question&amp;op=edit&amp;type_quest='
				.$this->getQuestionType().'&amp;idQuest='.$this->id.'&amp;back_test='.$url_encode, $lang->def('_BACK')));
			}
			//save groups a and b
			$content_a = $content_b = '';
			for($i = 0; $i < $num_answer; $i++) {
				if($_POST['elem_a'][$i] != '') {
					$content_a .= '<option value="'.$i.'">'.stripslashes($_POST['elem_a'][$i]).'</option>';
				}
				if($_POST['elem_b'][$i] != '') {
					$content_b .= '<option value="'.$i.'">'.stripslashes($_POST['elem_b'][$i]).'</option>';
				}
			}
			//load comment and scores

			$re_answer = mysql_query("
					SELECT comment, score_correct, score_incorrect 
					FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
					WHERE idQuest = '".(int)$this->id."'
					ORDER BY idAnswer");


			$i_load = 0;
			while(list($_POST['comment'][$i_load],
			$_POST['score_correct'][$i_load],
			$_POST['score_incorrect'][$i_load] ) = mysql_fetch_row($re_answer)){
				++$i_load;
			}

			$GLOBALS['page']->add(
			getTitleArea($lang->def('_TEST_SECTION'), 'test')
			.'<div class="std_block">'
			.getBackUi(ereg_replace('&', '&amp;', $back_test), $lang->def('_BACK'))

			.'<div class="title_big">'
			.$lang->def('_QUEST_ACRN_'.strtoupper($this->getQuestionType())).' - '
			.$lang->def('_QUEST_'.strtoupper($this->getQuestionType()))
			.'</div><br />'
			.Form::openForm('form_add_quest', 'index.php?modname=question&amp;op=edit')

			.Form::openElementSpace()
			.Form::getHidden('type_quest', 'type_quest', $this->getQuestionType())
			.Form::getHidden('back_test', 'back_test', $url_encode)
			.Form::getHidden('num_answer', 'num_answer', $num_answer)
			.Form::getHidden('idQuest', 'idQuest', $this->id)
			/**
			 * danhut: thêm hidden field xác định đang edit question trong bank hay trong test
			 */
			.Form::getHidden('isInBank', 'isInBank', $isInBank)
			.'<div class="no_float"></div><br />'
				
			.'<table class="test_answer" cellspacing="0" summary="'.$lang->def('_TEST_ANSWER_SUMMARY').'">'."\n"
			.'<caption>'.$lang->def('_TEST_ASSOCIATE').'</caption>'."\n"
			.'<tr>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_A').'</th>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_B').'</th>'
			.'<th>'.$lang->def('_TEST_COMMENT').'</th>'
			.'<th colspan="2">'.$lang->def('_TEST_POINT').'</th>'
			.'</tr>'."\n", 'content');
			for($i = 0; $i < $num_answer; $i++) {
				$this->_lineModAssociateAnswer($i, $content_b);
			}
			$GLOBALS['page']->add(
				'</table>'
				.Form::closeElementSpace()

				.Form::openButtonSpace()
				.Form::getButton('add_question', 'add_question', $lang->def('_SAVE'))
				.Form::closeButtonSpace()
				.Form::closeForm()
				.'</div>', 'content');
					
		} else {
			//load data
			if(!isset($_POST['elem_a_id'])) {

				list($sel_cat, $quest, $sel_diff, $sel_time, $shuffle ) = mysql_fetch_row(mysql_query("
						SELECT idCategory, title_quest, difficult, time_assigned, shuffle 
						FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."quest 
						WHERE idQuest = '".(int)$this->id."'"));

				$re_answer = mysql_query("
						SELECT idAnswer, answer 
						FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
						WHERE idQuest = '".(int)$this->id."'
						ORDER BY idAnswer");

				$j_load = $i_load = 0;
				while(list($_POST['elem_a_id'][$i_load], $_POST['elem_a'][$i_load]) = mysql_fetch_row($re_answer)){
					++$i_load;
				}

				$re_answer_2 = mysql_query("
						SELECT idAnswer, answer 
						FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
						WHERE idQuest = '".(int)$this->id."'
						ORDER BY idAnswer");

				while(list($_POST['elem_b_id'][$j_load], $_POST['elem_b'][$j_load]) = mysql_fetch_row($re_answer_2)){
					++$j_load;
				}
				$num_answer = ( $i_load > $j_load ? $i_load : $j_load );
			}


			//insert form
			require_once($GLOBALS['where_lms'].'/lib/lib.questcategory.php');
			$categories = Questcategory::getCategory();
			//writing difficult array
			$arr_dufficult = array(5 => '5 - '.$lang->def('_VERY_HARD'), 4 => '4 - '.$lang->def('_HARD'), 3 => '3 - '.$lang->def('_MEDIUM'), 2 => '2 - '.$lang->def('_EASY'), 1 => '1 - '.$lang->def('_VERY_EASY'));


			$GLOBALS['page']->add(
			getTitleArea($lang->def('_TEST_SECTION'), 'test')
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
			/**
			 * danhut: thêm hidden field xác định đang edit question trong bank hay trong test
			 */
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
			.'<th class="access-only">'.$lang->def('_TEST_QUEST_ELEM_NUM').'</th>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_A').'</th>'
			.'<th class="access-only">'.$lang->def('_TEST_QUEST_ELEM_NUM').'</th>'
			.'<th>'.$lang->def('_TEST_QUEST_ELEMENTS_B').'</th>'
			.'</tr>'."\n", 'content');
			for($i = 0; $i < $num_answer; $i++) {
				$this->_lineModAnswer($i);
			}
			$GLOBALS['page']->add(
				'</table>'
				.Form::getButton( 'more_answer', 'more_answer', $lang->def('_TEST_ADD_ONE_ANSWER'), 'button_nowh' ), 'content');
				if($num_answer > 1) $GLOBALS['page']->add(Form::getButton( 'less_answer', 'less_answer', $lang->def('_TEST_SUB_ONE_ANSWER'), 'button_nowh' ), 'content');
				$GLOBALS['page']->add(
				'' // Form::getButton( 'select_from_libraries', 'select_from_libraries', $lang->def('_TEST_SEL_LIBRARIES'), 'button_nowh' )
				.Form::closeElementSpace()

				.Form::openButtonSpace()
				.Form::getButton('do_association', 'do_association', $lang->def('_TEST_QUEST_SEL_ASSOCIATION'))
				.Form::closeButtonSpace()
				.Form::closeForm()
				.'</div>', 'content');
		}
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
			//delete answer
			if(!mysql_query("
				DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
				WHERE idQuest = '".$this->id."'")) return false;
		}
		
		

			//remove answer
			if(!mysql_query("
				DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
				WHERE idQuest = '".$this->id."'")) 
			{
				return false;
			}
			if(!mysql_query("
				DELETE FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
				WHERE idQuest = '".$this->id."'")) 
			{
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
			$tableNamePrefix = '_bank';
		}
		else
		{
			$tableNamePrefix = '_test';
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
			'".(int)$shuffle."') ";
		if(!mysql_query($ins_query)) return false;
		//find id of auto_increment colum
		list($new_id_quest) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$new_id_quest) return false;

		//retriving new answer
		$re_answer = mysql_query("
		SELECT idAnswer, answer  
		FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");
		$new_correct = array();
		while(list($idAnswer, $answer) = mysql_fetch_row($re_answer)) {

			//insert answer
			$ins_answer_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer_associate 
			( idQuest, answer ) VALUES
			( 	'".(int)$new_id_quest."',
				'".mysql_escape_string($answer)."' ) ";
			if(!mysql_query($ins_answer_query)) return false;

			list($new_correct[$idAnswer]) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		}

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
				'".(int)$new_correct[$is_correct]."', 
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

		$re_associate = mysql_query("
		SELECT idAnswer, answer 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer_associate 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");

		$answer_do = array();
		$find_prev = false;
		if($id_track != 0) {

			//recover previous information
			$recover_answer = "
			SELECT idAnswer, more_info 
			FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
			WHERE idQuest = '".(int)$this->id."' AND 
				idTrack = '".(int)$id_track."'";
			$re_answer_do = mysql_query($recover_answer);
			if(mysql_num_rows($re_answer_do)) {

				//find previous answer
				$find_prev = true;
				while(list($id_a, $id_sel) = mysql_fetch_row($re_answer_do)) $answer_do[$id_a] = $id_sel;
			}
		}

		$option_associate = array();
		$option_associate[0]['prefix'] = '<option value="0"';
		$option_associate[0]['suffix'] = '>'.$lang->def('_QUEST_NO_ASSOCIATION').'</option>';
		while(list($id_aa, $answer_associate) = mysql_fetch_row($re_associate)) {
			$option_associate[$id_aa]['prefix'] = '<option value="'.$id_aa.'"';
			$option_associate[$id_aa]['suffix'] = '>'.$answer_associate.'</option>';
		}

		$content = '<div class="play_question">'
		.'<div>'.$lang->def('_QUEST_'.strtoupper($this->getQuestionType())).'</div>'
		.'<div class="title_question">'.$num_quest.') '.$title_quest.'</div>'
		.'<div class="answer_question">';
		while(list($id_answer, $answer) = mysql_fetch_row($re_answer)){

			$content .= '<div class="form_line_l">'
			.'<label  for="quest_'.$id_quest.'_'.$id_answer.'">'.$answer.'</label>'
			.'&nbsp;<select class="test_as_select" id="quest_'.$id_quest.'_'.$id_answer.'" '
			.'name="quest['.$id_quest.']['.$id_answer.']"'
			.( $find_prev && $freeze ? ' disabled="disabled"' : '' ).'>';
			foreach($option_associate as $id_aa => $text ) {

				$content .= $text['prefix']
				.(($find_prev && $answer_do[$id_answer] == $id_aa) ? ' selected="selected"' : '')
				.$text['suffix'];
			}
			$content .= '</select></div>';
		}
		$content .=  '</div>'
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
					'".( $source['quest'][$this->id][$id_answer] == $is_correct ? $score_corr : -$score_incorr )."', 
					'".(int)$source['quest'][$this->id][$id_answer]."' )";
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
	function updateAnswer( $id_track, &$source ) {

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
		WHERE idQuest = '".(int)$this->id."'"));

		if(!$num_correct) $score_assigned = 0;
		else $score_assigned = round($score / $num_correct, 2);

		return round($score_assigned * $num_correct, 2);
	}

	/**
	 * set the maximum score for the question
	 *
	 * @param 	double 	$score	the score assigned to the question
	 * @param 	double 	$try	if true the function return the effective point that will be assigned
	 *
	 * @return 	double	contain the new maximum score for the question, can be different from the param $score
	 *					because can be round
	 *
	 * @access public
	 * @author Fabio Pirovano (fabio@docebo.com)
	 */
	function setMaxScore( $score, $try = false  ) {


		list($num_correct) = mysql_fetch_row(mysql_query("
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'"));

		if(!$num_correct) $score_assigned = 0;
		else $score_assigned = round($score / $num_correct, 2);

		if($try) return round($score_assigned * $num_correct, 2);

		$re_assign = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_testquestanswer
		SET score_correct = '".$score_assigned."'
		WHERE idQuest = '".(int)$this->id."'");
		if(!$re_assign) return 0;
		else return round($score_assigned * $num_correct, 2);
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
		SELECT idAnswer, answer, is_correct, comment  
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer";
		$re_answer = mysql_query($query_answer);

		$re_associate = mysql_query("
		SELECT idAnswer, answer 
		FROM ".$GLOBALS['prefix_lms']."_testquestanswer_associate 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");


		//recover previous information
		$recover_answer = "
		SELECT idAnswer, more_info 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_answer 
		WHERE idQuest = '".(int)$this->id."' AND 
			idTrack = '".(int)$id_track."'";
		$re_answer_do = mysql_query($recover_answer);
		if(mysql_num_rows($re_answer_do)) {

			while(list($id_a, $id_sel) = mysql_fetch_row($re_answer_do)) $answer_do[$id_a] = $id_sel;
		}

		$option_associate = array();
		$option_associate[0] = $lang->def('_QUEST_NO_ASSOCIATION');
		while(list($id_aa, $answer_associate) = mysql_fetch_row($re_associate)) {

			$option_associate[$id_aa] = $answer_associate;
		}

		$quest = '<div class="play_question">'
		.'<div class="title_question">'.$num_quest.') '.$title_quest.'</div>'
		.'<div class="answer_question">';
		while(list($id_answer, $answer, $is_correct, $comm) = mysql_fetch_row($re_answer)){

			$comm_corret 	= '';
			$answer_comment = '';

			$quest .= '<div class="no_float">'
			.'<div class="associate_colum_float">'.$answer.'</div>'
			.'<div class="associate_colum_float">';
			foreach($option_associate as $id_aa => $text ) {

				if(isset($answer_do[$id_answer]) && $answer_do[$id_answer] == $id_aa) {
					if($is_correct == $id_aa) {
						$quest .= $text.'&nbsp;<span class="test_answer_correct">'.$lang->def('_TEST_CORRECT').'</span>';
					} else {
						$quest .= $text.'&nbsp;<span class="test_answer_incorrect">'.$lang->def('_TEST_INCORRECT').'</span>';
						$answer_comment = $comm;
					}
				} elseif($id_aa == $is_correct && $show_solution) {
					$comm_corret = $answer.'&nbsp;<span class="text_bold">'.$lang->def('_TEST_NOT_AS_THECORRECT').' : </span>'.$text;
				}
			}
			if($comm_corret != '') {
				$comment .= '<br />'.$comm_corret.( $answer_comment != '' ? '<br />' : '' ).$answer_comment.'<br />';
			}
			$quest .= '</div></div><div class="no_float"></div>';
		}
		$quest .=  '</div>'
		.'</div>';

		return array(	'quest' 	=> $quest,
						'score'		=> $this->userScore($id_track), 
						'comment'	=> $comment );

	}

	function importFromRaw($raw_quest, $id_test = false) {

		if($id_test === false) $id_test = 0;

		/*luôn insert vào bank*/
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_bankquest 
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
		list($new_id_questbank) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$new_id_questbank) return false;

		if(!is_array($raw_quest->answers)) return $new_id_questbank;

		//insert question vào test
		if($id_test != 0)
		{
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

		}

		//retriving new answer
		reset($raw_quest->extra_info);
		while(list($k ,$raw_answer) = each($raw_quest->extra_info)) {

			/*luon insert answer vao bank*/
			$ins_answer_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_bankquestanswer_associate 
			( idQuest, answer ) VALUES
			( 	'".(int)$new_id_questbank."',
				'".$raw_answer->text."' ) ";
			if(!mysql_query($ins_answer_query)) return false;
			list($new_correct_bank[$k]) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

			if($id_test != 0)
			{
				//insert answer
				$ins_answer_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer_associate 
					( idQuest, answer ) VALUES
					( 	'".(int)$new_id_quest."',
						'".$raw_answer->text."' ) ";
				if(!mysql_query($ins_answer_query)) return false;
				list($new_correct[$k]) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			}
		}

		reset($raw_quest->answers);
		while(list($k, $raw_answer) = each($raw_quest->answers)) {

			//insert answer vao bank
			$ins_answer_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_bankquestanswer 
			( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
			( 	'".(int)$new_id_questbank."', 
				'".(int)$new_correct_bank[$k]."', 
				'".$raw_answer->text."', 
				'".$raw_answer->comment."',
				'".$this->_checkScore($raw_answer->score_correct)."', 
				'".$this->_checkScore($raw_answer->score_penalty)."') ";
			if(!mysql_query($ins_answer_query)) return false;
				
			if($id_test != 0)
			{
				//insert answer
				$ins_answer_query = "
					INSERT INTO ".$GLOBALS['prefix_lms']."_testquestanswer 
					( idQuest, is_correct, answer, comment, score_correct, score_incorrect ) VALUES
					( 	'".(int)$new_id_quest."', 
						'".(int)$new_correct[$k]."', 
						'".$raw_answer->text."', 
						'".$raw_answer->comment."',
						'".$this->_checkScore($raw_answer->score_correct)."', 
						'".$this->_checkScore($raw_answer->score_penalty)."') ";
				if(!mysql_query($ins_answer_query)) return false;
			}
		}

		return $new_id_questbank;
	}


	function exportToRaw($isInBank = false) {

		if($isInBank)
		{
			$tableNamePrefix = "_bank";
		}
		else
		{
			$tableNamePrefix = "_test";
		}
		//retriving question information
		list($idCategory, $type_quest, $title_quest, $difficult, $time_assigned, ) = mysql_fetch_row(mysql_query("
		SELECT idCategory, type_quest, title_quest, difficult, time_assigned 
		FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."quest 
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
		$i = 0;
		$corres = array();
		$re_answer = mysql_query("
		SELECT idAnswer, is_correct, answer, comment, score_correct, score_incorrect 
		FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");
		while(list($idAnswer, $is_correct, $answer, $comment, $score_c, $score_inc) = mysql_fetch_row($re_answer)) {

			$oAnswer = new AnswerRaw();
			$oAnswer->id_answer 		= $idAnswer;
			$oAnswer->is_correct 		= $is_correct;
			$oAnswer->text 				= $answer;
			$oAnswer->comment 			= $comment;
			$oAnswer->score_correct 	= $score_c;
			$oAnswer->score_penalty 	= $score_inc;

			$oQuest->answers[$i] = $oAnswer;
			$corres[$is_correct] = $i;
			$i++;
		}

		//retriving new answer
		$re_answer = mysql_query("
		SELECT idAnswer, answer  
		FROM ".$GLOBALS['prefix_lms'].$tableNamePrefix."questanswer_associate 
		WHERE idQuest = '".(int)$this->id."'
		ORDER BY idAnswer");

		$oQuest->extra_info = array();
		while(list($idAnswer, $answer) = mysql_fetch_row($re_answer)) {

			$oAnswer = new AnswerRaw();
			$oAnswer->text = $answer;

			$oQuest->extra_info[$corres[$idAnswer]] = $oAnswer;
		}

		return $oQuest;
	}
}

?>