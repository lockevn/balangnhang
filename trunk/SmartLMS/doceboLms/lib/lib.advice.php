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

class Man_Advice {
	
	function getCountUnreaded($id_user, $courses, &$last_access) {
		
		if(empty($courses)) return array();
		
		$unreaded = array();
		$query_unreaded = "
		SELECT idCourse, UNIX_TIMESTAMP(posted) 
		FROM ".$GLOBALS['prefix_lms']."_advice 
		WHERE author <> '".$id_user."' AND idCourse IN ( ".implode(',', $courses)." ) ";
		$re_advice = mysql_query($query_unreaded);
		if(!mysql_num_rows($re_advice)) return array();
		
		while(list($id_c, $posted) = mysql_fetch_row($re_advice)) {
			
			if(!isset($last_access[$id_c])) {
				
				if(isset($unreaded[$id_c])) $unreaded[$id_c]++;
				else $unreaded[$id_c] = 1;
			} elseif($posted > $last_access[$id_c]) {
				
				if(isset($unreaded[$id_c])) $unreaded[$id_c]++;
				else $unreaded[$id_c] = 1;
			}
		}
		return $unreaded;
	}
	
}

?>