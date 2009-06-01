<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class GroupScormObjMan {
	
	function GroupScormObjMan() {}
	
	
	/**
	 * returns the users score for a list of scorm obj
	 * @param array		$id_scorms		an array with the id of the scorm obj for which the function must retrive scores
	 * @param array		$id_students	the students of the course 
	 *
	 * @return array 	a matrix with the index [id_scorm] [id_user] and values array( score, max_score )
	 */
	function &getSimpleScormScores($id_scorms, $id_students = false) {
		
		$data = array();
		if(empty($id_scorms)) return $data;
		if(empty($id_students)) $id_students = false;
		$query_scores = "
		SELECT idReference, idUser, score_raw, score_max 
		FROM ".$GLOBALS['prefix_lms']."_scorm_tracking 
		WHERE lesson_status IN ('passed', 'valid', 'completed') AND idReference IN ( ".implode(',', $id_scorms)." ) ";
		if($id_students !== false) $query_scores .= " AND idUser IN ( ".implode(',', $id_students)." )";
		$re_scores = mysql_query($query_scores);
		while($scorm_data = mysql_fetch_assoc($re_scores)) {
			
			$data[$scorm_data['idReference']][$scorm_data['idUser']]['score'] = $scorm_data['score_raw'];
			$data[$scorm_data['idReference']][$scorm_data['idUser']]['max_score'] = $scorm_data['score_max'];
		}
		return $data;
	}
	
}

?>