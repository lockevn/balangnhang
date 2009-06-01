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

class Man_Forum_Cms {
	
	function getCountUnreaded($id_user, $courses, $last_access) {
		
		$unreaded = array();
		if(empty($courses)) return $unreaded;
		$courses_string = implode(',', $courses);
		
		$acl 	=& $GLOBALS['current_user']->getAcl();
		$all_user_idst = $acl->getSTGroupsST(getLogUserId());
		$all_user_idst[] = getLogUserId();
		
		$reLast = mysql_query("
		SELECT idCourse, UNIX_TIMESTAMP(last_access) 
		FROM ".$GLOBALS['prefix_cms']."_forum_timing
		WHERE idUser = '".$id_user."' AND idCourse IN ( ".$courses_string." ) ");
		while(list($id_c, $last) = mysql_fetch_row($reLast)) {
			
			$last_forum_access[$id_c] = $last;
		}
		
		while(list(, $id_course) = each($courses)) {
			// abilitated forum
			if(checkPermForCourse('mod', $id_course, true, 'forum', 'lms')) {
				
				$query_view_forum = "
				SELECT f.idForum 
				FROM ".$GLOBALS['prefix_cms']."_forum AS f 
				WHERE f.idCourse = '".$id_course."'";
				
			} else {
				
				$query_view_forum = "
				SELECT DISTINCT f.idForum  
				FROM ".$GLOBALS['prefix_cms']."_forum AS f 
					LEFT JOIN ".$GLOBALS['prefix_cms']."_forum_access AS fa ON ( f.idForum = fa.idForum )
				WHERE f.idCourse = '".$id_course."' AND ( fa.idMember IS NULL OR fa.idMember IN (".implode($all_user_idst, ',')." )  ) ";
			}
			$re_forum = mysql_query($query_view_forum);
			if(mysql_num_rows($re_forum)) {
			
				$forums = array();
				while(list($id_f) = mysql_fetch_row($re_forum)) {
					$forums[] = $id_f;
				}
				$re_unreaded = mysql_query("
				SELECT COUNT( m.idMessage ) 
				FROM ".$GLOBALS['prefix_cms']."_forumthread AS t JOIN ".$GLOBALS['prefix_cms']."_forummessage AS m 
				WHERE t.idThread = m.idThread AND m.author <> '".getLogUserId()."' AND UNIX_TIMESTAMP(m.posted) >= '"
					.( isset($last_forum_access[$id_course]) ? $last_forum_access[$id_course] : 0 )."'
					AND m.idCourse = '".$id_course."' AND t.idForum IN ( ".implode(',', $forums)." )");
				
				list($unreaded[$id_course]) = mysql_fetch_row($re_unreaded);
			}
		}
		return $unreaded;
	}
	
	function getUserForumPostCms($id_user)
	{
		$query_forum_post="
			SELECT COUNT(*)
			FROM ".$GLOBALS['prefix_cms']."_forummessage
			WHERE author = '".$id_user."'";
		
		$forum_post = mysql_fetch_row(mysql_query($query_forum_post));
		
		return $forum_post[0];
	}

}

?>