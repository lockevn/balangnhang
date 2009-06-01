<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package course management
 * @subpackage pre-assessment
 * @category ajax server
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.5.0
 * 
 * ( editor = Eclipse 3.2.0[phpeclipse,subclipse,WTP], tabwidth = 4 ) 
 */
if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

$op = get_req('op', DOTY_ALPHANUM, '');
switch($op) {
	case "getLang" : {
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( 'preassessment', 'framework');
		
 		$lang_obj='{
			"_DEL_TITLE":"'.$lang->def('_DEL_TITLE').'",
			"_DEL_CONFIRM":"'.$lang->def('_DEL').'",
			"_YES":"'.$lang->def('_CONFIRM').'",
			"_NO":"'.$lang->def('_UNDO').'",
			"_DEL_TITLE_RULE":"'.$lang->def('_DEL_TITLE_RULE').'",
			"_DEL_CONFIRM_RULE":"'.$lang->def('_DEL_CONFIRM_RULE').'",
			"_NEW_RULE":"'.$lang->def('_ADD_RULE').'", 
			"_CONFIRM":"'.$lang->def('_CONFIRM').'", 
			"_UNDO":"'.$lang->def('_UNDO').'"
		}';
  
  		docebo_cout($lang_obj);
	}; break;
	case "modruleform" : {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.preassessment.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( 'preassessment', 'framework');
		
		$rule_man = new AssessmentRule();
		
		$use_default 	= importVar('usedef', false, 1);
		$id_rule 		= importVar('id_rule', true, 0 );
		
		if($id_rule != 0) {
			
			// load old data ------------------------------------
			$rule = $rule_man->getRule($id_rule);
			
			$rule_type = $rule[RULE_TYPE];
			$setting = $rule_man->parseRuleSetting($rule[RULE_TYPE], $rule[RULE_SETTING]);
			$score_type_one = ( isset($setting[0]) ? $setting[0] : '' );
			$score_type_two = ( isset($setting[1]) ? $setting[1] : '' );
		} else {
			
			$rule_type = ( $use_default  ? RULE_DEFAULT : RULE_GREATER );
			$score_type_one = '';
			$score_type_two = '';
		}
		
		$arr_question = array();
		$arr_question[RULE_GREATER] = strip_tags(str_replace('[score]', $lang->def('_SCORE'), $lang->def('_RULE_GREATER')));
		$arr_question[RULE_LESSER]  = strip_tags(str_replace('[score]', $lang->def('_SCORE'), $lang->def('_RULE_LESSER'))); 
		$arr_question[RULE_BETWEEN] = strip_tags(str_replace(array('[score_1]', '[score_2]'), array($lang->def('_SCORE'), $lang->def('_SCORE_2')), $lang->def('_RULE_BETWEEN')));
		if($use_default || $id_rule != 0) $arr_question[RULE_DEFAULT] = $lang->def('_RULE_DEFAULT');
		
		docebo_cout( Form::getHidden('id_assessment', 'id_assessment', importVar('id_assessment', true, 0)) );
		docebo_cout( Form::getHidden('id_rule', 'id_rule', $id_rule) );
		
		docebo_cout(
			'<b><label for="rule_type">'.$lang->def('_RULE_TEXT').'</label>:&nbsp;</b>'
			.Form::getInputDropdown('dropdown_nowh', 'rule_type', 
						'rule_type', 
						$arr_question, 
						$rule_type, 
						' onChange="rule_type_change(\'rule_type\', \'score_type_one\', \'score_type_two\');"')
			.'<br />'.'<br />'
		);
		
		docebo_cout( Form::getTextfield(	$lang->def('_SCORE').':&nbsp;', 
					'score_type_one', 
					'score_type_one', 
					255,
					$score_type_one ) 
		);
		
		docebo_cout( Form::getTextfield(	$lang->def('_SCORE_2').':&nbsp;', 
					'score_type_two', 
					'score_type_two', 
					255,
					$score_type_two ) 
		);
		
		docebo_cout( Form::getBreakRow() );
		
		docebo_cout('<script type="text/javascript">'
			.'rule_type_change(\'rule_type\', \'score_type_one\', \'score_type_two\');'
		.'</script>');
	};break;
}

?>