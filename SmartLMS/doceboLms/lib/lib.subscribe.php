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

class CourseSubscribe_Management {

	var $course_man;

	var $acl;

	var $acl_man;
	var $dbconn=NULL;

	function CourseSubscribe_Management($dbconn=NULL) {

		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

		$this->course_man 	= new Man_Course();
		$this->acl 			=& $GLOBALS['current_user']->getAcl();
		$this->acl_man 		=& $GLOBALS['current_user']->getAclManager();
		$this->dbconn=$dbconn;
	}


	function _query( $query ) {
		if ($this->dbconn === NULL)
			$rs =mysql_query($query);
		else
			$rs =mysql_query($query, $this->dbconn);
		doDebug($query);
		return $rs;
	}


	function _insQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	/**
	 * Subscribe a group of users(N) to a group of courses(N)
	 * @param array 	$arr_user 		the id of the users
	 * @param array 	$arr_course 	the id of the courses
	 * @param mixed 	$levels 		a matrix defined in this way
	 *									array( id_course => array( id_user => lv_number, ... ), ... )
	 *									or else a level_number that is used for all the users
	 *
	 * @return bool true if success, false otherwise
	 */
	function multipleSubscribe($arr_users, $arr_courses, $levels) {

		$re = true;
		while(list(, $id_course) = each($arr_courses)) {

			$re &= $this->subscribeUsers($arr_users , $id_course, ( is_array($levels) ? $levels[$id_course] : $levels ));
		}
		return $re;
	}

	/**
	 * Subscribe a user(1) to a group of courses(N)
	 * @param int 		$id_user 		the id of the users
	 * @param array 	$arr_course 	the id of the courses
	 * @param mixed 	$levels 		a matrix defined in this way
	 *									array( id_course => lv_number, ... )
	 *									or else a level_number that is used for all the users
	 *
	 * @return bool true if success, false otherwise
	 */
	function multipleUserSubscribe($id_user, $arr_courses, $levels) {

		if(empty($arr_courses)) return true;

		while(list(, $id_course) = each($arr_courses)) {

			$re = true;

			$group_levels 	=& $this->course_man->getCourseIdstGroupLevel($id_course);
			$user_level 	= $this->course_man->getLevelsOfUsers($id_course, $id_user);

			$lv = ( is_array($levels) ? $levels[$id_course] : $levels );

			if(!isset($user_level[$id_user])) {

				$this->acl_man->addToGroup($group_levels[$lv], $id_user);

				$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
				( idUser, idCourse, level, waiting, subscribed_by, date_inscr ) VALUES
				( '".$id_user."', '".$id_course."', '".$lv."', '0', '".getLogUserId()."', '".date("Y-m-d H:i:s")."' )";

				$re &= $this->_query($query);
			} elseif($user_level[$id_user] != $lv) {

				$old_lv = $user_level[$id_user];

				$this->acl_man->removeFromGroup($group_levels[$old_lv], $id_user);
				$this->acl_man->addToGroup($group_levels[$lv], $id_user);

				$query = "
				UPDATE ".$GLOBALS['prefix_lms']."_courseuser
				SET level = '".$lv."'
				WHERE idUser = '".$id_user."' AND
						idCourse = '".$id_course."'";
				$re &= $this->_query($query);
			}

		}
		return $re;
	}

	/**
	 * Subscribe a group of users(N) to a course(1)
	 * @param array 	$arr_user 	the id of the users
	 * @param int 		$id_course 	the id of the course
	 * @param mixed 	$levels 		a array defined in this way
	 *									array( id_user => lv_number, ... )
	 *									or else a level_number that is used for all the users
	 *
	 * @return bool true if success, false otherwise
	 */
	function subscribeUsers($arr_users, $id_course, $levels) {

		if(empty($arr_users)) return true;

		$re = true;
		$group_levels 	=& $this->course_man->getCourseIdstGroupLevel($id_course);
		$user_level 	= $this->course_man->getLevelsOfUsers($id_course, $arr_users);

		while(list(, $id_user) = each($arr_users)) {

			$lv = ( is_array($levels) ? $levels[$id_user] : $levels );
			if(!isset($user_level[$id_user])) {

				$this->acl_man->addToGroup($group_levels[$lv], $id_user);

				$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
				( idUser, idCourse, level, waiting, subscribed_by, date_inscr ) VALUES
				( '".$id_user."', '".$id_course."', '".$lv."', '0', '".getLogUserId()."', '".date("Y-m-d H:i:s")."' )";

				$re &= $this->_query($query);
			} else {

				$old_lv = $user_level[$id_user];

				$this->acl_man->removeFromGroup($group_levels[$old_lv], $id_user);
				$this->acl_man->addToGroup($group_levels[$lv], $id_user);

				$query = "
				UPDATE ".$GLOBALS['prefix_lms']."_courseuser
				SET level = '".$lv."'
				WHERE idUser = '".$id_user."' AND
						idCourse = '".$id_course."'";
				$re &= $this->_query($query);
			}
		}

		return $re;
	}

	/**
	 * Subscribe a user(1) to a course(1)
	 * @param int 	$id_user 		the id of the user
	 * @param int 	$id_course 		the id of the course
	 * @param int 	$level_number 	the level number of the user
	 *
	 * @return bool true if success, false otherwise
	 */
	function subscribeUser($id_user, $id_course, $level_number) {

		return $this->subscribeUsers(array($id_user), $id_course, $level_number);
	}

	/**
	 * Unsubscribe a group of users to a course
	 * @param array 	$arr_user 		the id of the users
	 * @param array 	$arr_course 	the id of the courses
	 *
	 * @return bool true if success, false otherwise
	 */
	function multipleUnsubscribe($arr_users, $arr_courses) {

		$re = true;
		while(list(, $id_course) = each($arr_courses)) {

			$re &= $this->unsubscribeUsers($arr_users , $id_course);
		}
		return $re;
	}

	/**
	 * Unsubscribe a group of users to a course
	 * @param array 	$arr_user 	the id of the users
	 * @param int 		$id_course 	the id of the course
	 *
	 * @return bool true if success, false otherwise
	 */
	function unsubscribeUsers($arr_users, $id_course) {

		if(empty($arr_users)) return true;

		$group_levels =& $this->course_man->getCourseIdstGroupLevel($id_course);
		$user_level = $this->course_man->getLevelsOfUsers($id_course, $arr_users);

		while(list(, $id_user) = each($arr_users)) {

			if(isset($user_level[$id_user])) {
				$lv = $user_level[$id_user];
				$this->acl_man->removeFromGroup($group_levels[$lv], $id_user);
			}
		}
		$re = $this->_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser IN ( ".implode(',', $arr_users)." ) AND idCourse = '".$id_course."'");

		return $re;
	}

	/**
	 * Unsubscribe a user to a course
	 * @param int 	$id_user 	the id of the user
	 * @param int 	$id_course 	the id of the course
	 *
	 * @return bool true if success, false otherwise
	 */
	function unsubscribeUser($id_user, $id_course) {

		return $this->unsubscribeUsers(array($id_user), $id_course);
	}

	/**
	 * Unsubscribe a user from all the courses
	 * @param int 	$id_user 	the id of the user
	 *
	 * @return bool true if success, false otherwise
	 */
	function unsubscribeUserFromAllCourses($id_user) {

		$re = $this->_query("
		SELECT idCourse
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser = '".$id_user."'");

		$res = true;
		while(list($id_course) = mysql_fetch_row($re)) {

			$res &= $this->unsubscribeUsers(array($id_user), $id_course);
		}
		return $res;
	}

	/**
	 * Suspend a user from a course
	 * @param int 	$id_user 	the id of the user
	 *
	 * @return bool true if success, false otherwise
	 */
	function suspendUser($id_user, $id_course) {

		require_once( $GLOBALS['where_lms'] . '/lib/lib.course.php' );

		$re = $this->_query("
		UPDATE ".$GLOBALS['prefix_lms']."_courseuser
		SET status = '"._CUS_SUSPEND."'
		WHERE idUser = '".$id_user."' AND idCourse = '".$id_course."'");

		return $re;
	}

	// if there is edition ----------------------------------------------------------

	/**
	 * Subscribe a group of users(N) to a course edition(1)
	 * @param array 	$arr_user 	the id of the users
	 * @param int 		$id_course 	the id of the course
	 * @param mixed 	$levels 		a array defined in this way
	 *									array( id_user => lv_number, ... )
	 *									or else a level_number that is used for all the users
	 *
	 * @return bool true if success, false otherwise
	 */
	function subscribeEditionUsers($arr_users, $id_edition, $levels, $id_course = false) {

		if(empty($arr_users)) return true;
		if($id_course == false) {

			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

			$man = new Man_Course();
			$info = $man->getEditionInfo($id_edition);
			$id_course = $info['idCourse'];
		}

		$re = true;
		$acl_man =& $GLOBALS['current_user']->getAclManager();
		$group_levels 	=& $this->course_man->getCourseIdstGroupLevel($id_course);
		$user_level 	= $this->course_man->getLevelsOfUsers($id_course, $arr_users);

		$edition_group 	= $acl_man->getGroupST('/lms/course_edition/'.$id_edition.'/subscribed');
		if($edition_group === FALSE) {
			$edition_group = $acl_man->registerGroup('/lms/course_edition/'.$id_edition.'/subscribed', 'all the user of a course edition', true, "course");
		}
		while(list(, $id_user) = each($arr_users)) {

			$lv = ( is_array($levels) ? $levels[$id_user] : $levels );
			if(!isset($user_level[$id_user])) {

				$this->acl_man->addToGroup($group_levels[$lv], $id_user);
				$this->acl_man->addToGroup($edition_group, $id_user);

				$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
				( idUser, idCourse, edition_id, level, waiting, subscribed_by, date_inscr ) VALUES
				( '".$id_user."', '".$id_course."', '".$id_edition."', '".$lv."', '0', '".getLogUserId()."', '".date("Y-m-d H:i:s")."' )";

				$re &= $this->_query($query);
			} else {

				$old_lv = $user_level[$id_user];

				$this->acl_man->removeFromGroup($group_levels[$old_lv], $id_user);
				$this->acl_man->addToGroup($group_levels[$lv], $id_user);

				$query = "
				UPDATE ".$GLOBALS['prefix_lms']."_courseuser
				SET level = '".$lv."'
				WHERE idUser = '".$id_user."'
					AND idCourse = '".$id_course."'
					AND edition_id = '".$id_edition."'";
				$re &= $this->_query($query);
			}
		}

		return $re;
	}


	// special subscribe for connector ----------------------------------------------

	/**
	 * Subscribe a user(1) to a course(1), connection control
	 * @param int 		$id_user 	the id of the users
	 * @param int 		$id_course 	the id of the course
	 * @param int 		$level 		the level
	 *
	 * @return bool true if success, false otherwise
	 */
	function subscribeUserWithConnection($id_user, $id_course, $level, $connection, $date = false) {

		$query_courseuser = "
		SELECT idUser, level, imported_from_connection
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idCourse = '".$id_course."' AND idUser = '".$id_user."'";
		$re_courseuser = $this->_query($query_courseuser);

		$re = true;
		$group_levels 	=& $this->course_man->getCourseIdstGroupLevel($id_course);

		if(!mysql_num_rows($re_courseuser)) {

			$this->acl_man->addToGroup($group_levels[$level], $id_user);

			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
			( idUser, idCourse, level, waiting, subscribed_by, date_inscr, imported_from_connection ) VALUES
			( '".$id_user."', '".$id_course."', '".$level."', '0', '".getLogUserId()."', '".($date ? $date : date("Y-m-d H:i:s"))."', '".$connection."' )";

			$re &= $this->_query($query);
		} else {

			list($id_user, $old_lv, $import_from) = mysql_fetch_row($re_courseuser);
			if($import_from !== $connection) return 'jump';

			if($old_lv != $level) {

				$this->acl_man->removeFromGroup($group_levels[$old_lv], $id_user);
				$this->acl_man->addToGroup($group_levels[$level], $id_user);
			}
			$query = "
			UPDATE ".$GLOBALS['prefix_lms']."_courseuser
			SET level = '".$level."'
			WHERE idUser = '".$id_user."' AND
					idCourse = '".$id_course."'";
			$re &= $this->_query($query);
		}

		return $re;
	}

	/**
	 * Suspend a user from a course
	 * @param int 	$id_user 	the id of the user
	 *
	 * @return bool true if success, false otherwise
	 */
	function suspendUserWithConnection($id_user, $id_course, $connection) {

		require_once( $GLOBALS['where_lms'] . '/lib/lib.course.php' );

		$re = $this->_query("
		UPDATE ".$GLOBALS['prefix_lms']."_courseuser
		SET status = '"._CUS_SUSPEND."'
		WHERE idUser = '".$id_user."'
			AND idCourse = '".$id_course."'
			AND imported_from_connection = '".$connection."'");
		return $re;
	}


	/**
	 * Unsubscribe a user from a course
	 * @param array 	$arr_user 	the id of the users
	 * @param int 		$id_course 	the id of the course
	 *
	 * @return bool true if success, false otherwise
	 */
	function unsubscribeUserWithConnection($id_user, $id_course, $connection) {

		$group_levels =& $this->course_man->getCourseIdstGroupLevel($id_course);


		$query_courseuser = "
		SELECT idUser, level, imported_from_connection
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idCourse = '".$id_course."' AND idUser = '".$id_user."'";
		$re_courseuser = $this->_query($query_courseuser);

		list($id_user, $level, $import_from) = mysql_fetch_row($re_courseuser);
		if($import_from == $connection) return 'jump';

		$this->acl_man->removeFromGroup($group_levels[$level], $id_user);

		$re = $this->_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser = '".$id_user."' AND idCourse = '".$id_course."'");

		return $re;
	}
	
	
	/**
	 * Unsubscribe a group of users to a course
	 * @param array 	$arr_user 	the id of the users
	 * @param int 		$id_course 	the id of the course
	 *
	 * @return bool true if success, false otherwise
	 */
	function unsubscribeUsersEd($arr_users, $id_edition, $id_course = false) {

		if(empty($arr_users)) return true;
		if($id_course == false) {

			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

			$man = new Man_Course();
			$info = $man->getEditionInfo($id_edition);
			$id_course = $info['idCourse'];
		}
		$group_levels =& $this->course_man->getCourseIdstGroupLevel($id_course);
		$user_level = $this->course_man->getLevelsOfUsers($id_course, $arr_users);
		
		$re = $this->_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser IN ( ".implode(',', $arr_users)." ) AND idCourse = '".$id_course."' AND editon_id = '".$id_edition."'");

		$survivor = array();
		$query = "
		SELECT idUser 
		FROM ".$GLOBALS['prefix_lms']."_courseuser
		WHERE idUser IN ( ".implode(',', $arr_users)." ) AND idCourse = '".$id_course."'";
		$re_query = mysql_query($query);
		while(list($idu) = mysql_fetch_row($re_query)) {
			$survivor[$idu] = $idu;
		}

		while(list(, $id_user) = each($arr_users)) {

			if(isset($user_level[$id_user]) && !isset($survivor[$id_user])) {
				$lv = $user_level[$id_user];
				$this->acl_man->removeFromGroup($group_levels[$lv], $id_user);
			}
		}

		return $re;
	}

	/**
	 * Unsubscribe a user to a course
	 * @param int 	$id_user 	the id of the user
	 * @param int 	$id_course 	the id of the course
	 *
	 * @return bool true if success, false otherwise
	 */
	function unsubscribeUserFromEd($id_user, $id_edition, $id_course = false) {

		return $this->unsubscribeUsersEd(array($id_user), $id_edition, $id_course);
	}
}

?>
