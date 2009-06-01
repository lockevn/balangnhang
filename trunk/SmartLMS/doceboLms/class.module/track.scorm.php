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

require_once( $GLOBALS['where_lms'].'/class.module/track.object.php');

//if( version_compare(phpversion(), "5.0.0") == -1 ) {
	define('_track_scorm_basepath',$GLOBALS['where_lms'].'/modules/scorm/');
//} else {
//	define('_track_scorm_basepath',$GLOBALS['where_lms'].'/modules/scorm5/');
//}
class Track_ScormOrg extends Track_Object {
	
	var $idTrack;
	var $idReference;
	var $idUser;
	var $dateAttempt;
	var $status;
	var $objectType;
	
	/** 
	 * object constructor
	 * Table : learning_commontrack
	 * idReference | idUser | idTrack | objectType | date_attempt  | status |
	 **/
	function Track_ScormOrg( $idTrack, $idResource = false, $idParams = false, $back_url = NULL ) {
		$this->objectType = 'scormorg';
		parent::Track_Object($idTrack);
		
		$this->idResource = $idResource;
		$this->idParams = $idParams;
		if($back_url === NULL) $this->back_url = array();
		else $this->back_url = $back_url;
	}
	
	/**
	 * print in standard output 
	 **/
	function loadReport( $idUser = FALSE ) {
		
		require_once( _track_scorm_basepath.'scorm_stats.php' );
		require_once( $GLOBALS['where_lms'].'/lib/lib.param.php' );
		if($idUser !== false) {
			$this->idReference = getLOParam($this->idParams, 'idReference');
			return scrom_userstat( $this->idResource, $idUser, $this->idReference  );
		}
	}
	
	/**
	 * print in standard output the details of a track 
	 **/
	function loadReportDetail( $idUser, $idItemDetail ) {
		require_once( _track_scorm_basepath.'scorm_stats.php' );
		if($idUser !== false) {
			return scrom_userstat_detail( $this->idResource, $idUser, $idItemDetail );
		}
	}
	
}

?>
