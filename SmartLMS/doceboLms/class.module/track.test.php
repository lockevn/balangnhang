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

require_once($GLOBALS['where_lms'].'/class.module/track.object.php');

class Track_Test extends Track_Object {
	
	/** 
	 * object constructor
	 * Table : learning_commontrack
	 * idReference | idUser | idTrack | objectType | date_attempt  | status |
	 **/
	function Track_Test( $idTrack, $idResource = false, $idParams = false, $back_url = NULL ) {
		$this->objectType = 'test';
		parent::Track_Object($idTrack);
		
		$this->idResource = $idResource;
		$this->idParams = $idParams;
		if($back_url === NULL) $this->back_url = array();
		else $this->back_url = $back_url;
	}
	
	/**
	 * function createTrack( $idUser, $idTest, $idReference, $attempt_number = 0  )
	 *
	 * create a new row in the _testtrack table for tracking purpose
	 *
	 * @param int	$idUser			the id of the user that display the object
	 * @param int	$idTest			the id of the test that is displayed
	 * @param int	$idReference	the idReference from the table of the lesson
	 *
	 * @return int	idTrack if the row is created correctly otherwise false 
	 **/
	function createNewTrack( $idUser, $idTest, $idReference, $attempt_number = 0  ) {
		
		
		$query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_testtrack 
		SET idUser = '".(int)$idUser."', 
			idTest = '".(int)$idTest."', 
			idReference = '".(int)$idReference."', 
			date_attempt = '".date("Y-m-d H:i:s")."', 
			date_end_attempt = '".date("Y-m-d H:i:s")."', 
			last_page_seen = '0', 
			number_of_save = '0',
			number_of_attempt = '".$attempt_number."'";
		if(!mysql_query($query)) {
			
			return false;
		}
		
		list($idTrack) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$idTrack) return false;
		else return $idTrack;
	}
	
	function getTrack($id_test, $id_user) {
		
		$query = "
		SELECT idTrack
		FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idUser = '".$id_user."' AND idTest = '".$id_test."'";
		$re = mysql_query($query);
		
		if(!mysql_num_rows($re)) return false;
		list($id_track) = mysql_fetch_row($re);
		return $id_track;
	}
	
	/**
	 * function isTrack( $idUser, $idTest, $idReference )
	 *
	 * control if exists at least one row in _testtrack table for tracking purpose
	 *
	 * @param int	$idUser			the id of the user that display the object
	 * @param int	$idTest			the id of the test that is displayed
	 * @param int	$idReference	the idReference from the table of the lesson
	 *
	 * @return int	true if the row exists otherwise false 
	 **/
	function isTrack( $idUser, $idTest, $idReference ) {
		
		
		$query = "
		SELECT COUNT(*) 
		FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idUser = '".(int)$idUser."' AND 
			idTest = '".(int)$idTest."' AND 
			idReference = '".(int)$idReference."'";
		list($re_track) = mysql_fetch_row(mysql_query($query));
		
		return $re_track;
	}
	
	/**
	 * function getTrackInfo( $idUser, $idTest, $idReference )
	 *
	 * return some information abiout a track
	 *
	 * @param int	$idUser			the id of the user that display the object
	 * @param int	$idTest			the id of the test that is displayed
	 * @param int	$idReference	the idReference from the table of the lesson
	 *
	 * @return array	return false if track doesn't exists, otherwise return an array with some info in this way: 
	 *					array ( 
	 * 						idTrack,
	 *						date_attempt,
	 *						date_end_attempt,
	 *						last_page_seen,
	 *						last_page_saved,
	 *						number_of_save
	 *					)
	 *
	 **/
	function getTrackInfo( $idUser, $idTest, $idReference ) {
		
		
		$query = "
		SELECT idTrack, date_attempt, date_end_attempt, last_page_seen, last_page_saved, number_of_save, number_of_attempt 
		FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idUser = '".(int)$idUser."' AND 
			idTest = '".(int)$idTest."' AND 
			idReference = '".(int)$idReference."'";
		$re_track = mysql_query($query);
		
		if(!mysql_num_rows($re_track)) return array();
		else return mysql_fetch_array($re_track);
	}
	
	
	function getTrackInfoById( $idTrack ) {
		
		
		$query = "
		SELECT idTrack, date_attempt, date_end_attempt, last_page_seen, last_page_saved, score, number_of_save, number_of_attempt 
		FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idTrack = '".(int)$idTrack."'";
		$re_track = mysql_query($query);
		
		if(!mysql_num_rows($re_track)) return array();
		else return mysql_fetch_array($re_track);
	}
	
	/**
	 * function updateTrack( $idTrack, $new_info )
	 *
	 * create a new row in the _testtrack table for tracking purpose
	 *
	 * @param int	$idTrack		the track of the object
	 * @param array	$new_info		an array with the new information
	 *
	 * @return bool	true if success false otherwise 
	 **/
	function updateTrack( $idTrack, $new_info ) {
		
		
		$first = true;
		if(!is_array($new_info)) return true;
		$query = "
		UPDATE ".$GLOBALS['prefix_lms']."_testtrack 
		SET ";
		foreach($new_info as $field_name => $field_value) {
			
			$query .= ( $first ? '' : ', ' ).$field_name." = '".$field_value."'";
			if($first) $first = false;
		}
		$query .= " WHERE idTrack = '".(int)$idTrack."'";
		
		if(isset($_POST['show_review'])) return true;
		
		if(!mysql_query($query)) return false;
		else return true;
	}
	
	/**
	 * @return idTrack if exists or false 
	 **/
	function deleteTrack( $idTrack ) {
		
		$query = "DELETE FROM ".$GLOBALS['prefix_lms']."_commontrack "
				." WHERE idTrack='".(int)$idTrack."'"
				."   AND objectType='test'";
		if(!mysql_query( $query )) return false;
		
		$query = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idTrack='".(int)$idTrack."'";
		
		if(mysql_query( $query )) return true;
		else return false;
	}
	
	/**
	 * print in standard output 
	 **/
	function loadReport( $idUser = false ) {
		
		require_once($GLOBALS['where_lms'].'/modules/test/do.test.php' );
		
		if($idUser) user_report($idUser, $this->idResource, $this->idParams);
	}
	
	/**
	 * @return bool	true if this object use extra colum in user report
	 */
	function otherUserField() {
		return true;
	}
	
	/**
	 * @return array	an array with the header of extra colum
	 */
	function getHeaderUserField() {
		
		return array(
			array('content' => _TEST_POINTDO, 'type' => 'align_right')
		);
	}
	
	/**
	 * @return array	an array with the extra colum
	 */
	function getUserField() {
		
		
		$field = array();
		$re_score = mysql_query("
		SELECT idUser, is_end, type_of_result, result 
		FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idTest = '".$this->idResource."'");
		while(list($id_user, $is_end, $point_type, $point_do) = mysql_fetch_row($re_score)) {
			
			if($is_end) $field[$id_user] = array($point_do.( $point_type ? '%' : '' ));
		}
		return $field;
	}
	
}

?>
