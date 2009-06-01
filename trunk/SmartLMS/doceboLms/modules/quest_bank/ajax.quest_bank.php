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
 * @subpackage course catalogue
 * @category ajax server
 * @version $Id:$
 *
 */

if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

$op = get_req('op', DOTY_ALPHANUM, '');
switch($op) {
	case "getselected" : {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
		$qbm = new QuestBankMan();
		
		$quest_category 	= get_req('quest_category', DOTY_INT);
		$quest_difficult 	= get_req('quest_difficult', DOTY_INT); 
		$quest_type 		= get_req('quest_type', DOTY_ALPHANUM);
		
		$re_quest = $qbm->resQuestList($quest_category, $quest_difficult, $quest_type);
		
		$value = array();
		while(list($id_q) = $qbm->fetch($re_quest)) {
			
			$value[] = (int)$id_q;
		}
		
		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;
	case "delquest" : {
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
		
		$id_quest = get_req('id_quest', DOTY_INT);
		$row_quest = get_req('row_quest', DOTY_ALPHANUM);
		
		require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
		$qman = new QuestBankMan();
		$result = $qman->delQuest($id_quest);
		
		$value = array("result"=>$result, "id_quest"=>$id_quest, "row_quest"=>$row_quest, "error"=>$qman->last_error);
		
		$json = new Services_JSON();
		$output = $json->encode($value);
		docebo_cout($output);
	};break;
	default : {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.quest_bank.php');
		$qbm = new QuestBankMan();
		
		$quest_category 	= get_req('quest_category', DOTY_INT);
		$quest_difficult 	= get_req('quest_difficult', DOTY_INT); 
		$quest_type 		= get_req('quest_type', DOTY_ALPHANUM); 
		$startIndex 		= get_req('startIndex', DOTY_INT, 0);
		$results 			= get_req('results', DOTY_INT, 30);
		
		$totalRecords = $qbm->totalQuestList($quest_category, $quest_difficult, $quest_type);
		$re_quest = $qbm->resQuestList($quest_category, $quest_difficult, $quest_type, $startIndex, $results);
		
		$value = array(
			"totalRecords" => (int)$totalRecords,
			"recordsReturned" => (int)$qbm->num_rows($re_quest),
			"startIndex" => (int)$startIndex,
			"records" => array(),
			"qc" => $quest_category,
			"qd" => $quest_difficult, 
			"qt" => $quest_type, 
			"si" => $startIndex,
			"re" => $results
		);

		while(list($id_q, $id_c, $type, $title, $difficult) = $qbm->fetch($re_quest)) {
			
			$value['records'][] = array(
				"id_quest" => $id_q,
				"category_quest" => $id_c,
				"type_quest" => $type,
				"title_quest" => $title,
				"difficult" => $difficult
			);
		}
		
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
		docebo_cout($output);
	};break;
}

?>