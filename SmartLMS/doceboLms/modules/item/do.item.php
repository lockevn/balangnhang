<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2002 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework'].'/lib/lib.download.php');

function play( $idResource, $idParams, $back_url ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	list($file) = mysql_fetch_row(mysql_query("SELECT path"
											. " FROM ".$GLOBALS['prefix_lms']."_materials_lesson"
											. " WHERE idLesson = '".$idResource."'"));
											
	//recognize mime type
	$expFileName = explode('.', $file);
	$totPart = count($expFileName) - 1;

	require_once( $GLOBALS['where_lms'].'/lib/lib.param.php' );
	$idReference = getLOParam($idParams, 'idReference');
	// NOTE: Track only if $idReference is present 
	if( $idReference !== FALSE ) {
		require_once( $GLOBALS['where_lms'].'/class.module/track.item.php' );
		list( $exist, $idTrack) = Track_Item::getIdTrack($idReference, getLogUserId(), $idResource, TRUE );
		if( $exist ) {
			$ti = new Track_Item( $idTrack );
			$ti->setDate(date('Y-m-d H:i:s'));
			$ti->status = 'completed';
			$ti->update();
		} else {
			$ti = new Track_Item( false );
			$ti->createTrack( $idReference, $idTrack, getLogUserId(), date('Y-m-d H:i:s'), 'completed', 'item' );
		}
	}

	if($_SESSION['direct_play'] == 1) {

		if (isset($_SESSION['idCourse'])) {

			TrackUser::closeSessionCourseTrack();

			unset($_SESSION['idCourse']);
			unset($_SESSION['idEdition']);
		}
		if(isset($_SESSION['test_assessment'])) unset($_SESSION['test_assessment']);
		if(isset($_SESSION['cp_assessment_effect'])) unset($_SESSION['cp_assessment_effect']);
		$_SESSION['current_main_menu'] = '1';
		$_SESSION['sel_module_id'] = '1';
		$_SESSION['is_ghost'] = false;

	}
	
	//send file
	sendFile('/doceboLms/'.$GLOBALS['lms']['pathlesson'], $file, $expFileName[$totPart]);
}
		
?>
