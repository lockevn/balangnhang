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

class Man_Forum {
	
	function getCountUnreaded($id_user, $courses, $last_access) {
		/*
		//$time_start = getmicrotime();
		$unreaded = array();
		if(empty($courses)) return $unreaded;
		
		$reLast = mysql_query("
		SELECT idCourse, new_forum_post
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser = '".$id_user."' AND idCourse IN ( ".implode(',', $courses)." ) ");
		while(list($id_c, $new_post) = mysql_fetch_row($reLast)) {
			
			list($unreaded[$id_c]) = $new_post;
		}
		return $unreaded;*/
		
			
		//$time_start = getmicrotime();
		$unreaded = array();
		if(empty($courses)) return $unreaded;
		
		$reLast = mysql_query("
		SELECT idCourse, new_forum_post
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser = '".$id_user."' AND idCourse IN ( ".implode(',', $courses)." ) ");
		while(list($id_c, $new_post) = mysql_fetch_row($reLast)) {
			
			list($unreaded[$id_c]) = $new_post;
		}
		return $unreaded;
	}
	
	function getUserForumPostLms($id_user) {
		
		$query_forum_post="
			SELECT COUNT(*)
			FROM ".$GLOBALS['prefix_lms']."_forummessage
			WHERE author = '".$id_user."'";
		
		$forum_post = mysql_fetch_row(mysql_query($query_forum_post));
		
		return $forum_post[0];
	}

}

?>