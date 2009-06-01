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

/**
 * Display a progress bar with 
 *	- $totComplete elementi completi (green see css)
 *  - $totFailed elementi falliti (yellow see css)
 *	- $total elementi in tutto (white see css)
 *
 * @param int $totComplete number of completed elements
 * @param int $totFailed number of failed elements
 * @param int $total total number of elements
 * @param bool	$show_title	 show the title of the progress bar
 **/
function renderProgress($tot_complete, $tot_failed, $total, $show_title = false) {
	
	if($total == 0) return '';
	$perc_complete 	= round(($tot_complete / $total) * 100, 2);
	$perc_failed 	= round(($tot_failed / $total) * 100, 2);
	
	$title = str_replace('[total]', $total, def('_PROGRESS_TITLE', 'course'));
	$title = str_replace('[complete]', $tot_complete, $title);
	$title = str_replace('[failed]', $tot_failed, $title);
	
	$html = '';
	if($show_title === true) $html .= '<span class="progress_title">'.$title.'</span><br />';
	if($perc_complete >= 100) {
		
		$html .= "\n".'<div class="box_progress_complete" title="'.$title.'">'
			.'<div class="no_float">'
			.'</div></div>'."\n";
	} elseif($perc_failed + $perc_complete >= 100) {
		
		$html .= "\n".'<div class="box_progress_failed" title="'.$title.'">';
		if($perc_complete != 0) $html .= '<div class="bar_complete" style="width: '.$perc_complete.'%;"></div>';
		$html .= '<div class="no_float">'
			.'</div></div>'."\n";
	} else {
		
		$html .= "\n".'<div class="box_progress_bar" title="'.$title.'">';
		if($perc_complete != 0) $html .= '<div class="bar_complete" style="width: '.$perc_complete.'%;"></div>';
		if($perc_failed != 0) $html .= '<div class="bar_failed" style="width: '.$perc_failed.'%;"></div>';
		$html .= '<div class="no_float">'
			.'</div></div>'."\n";
	}
	
	return $html;
}

/**
 * Return total number of items in a course
 * @param int $idCourse id of course
 * @param bool $countHidden count hidden elements
 * @param int $idUser id of user to filter accessibility
 * @param bool $countNotAccessible count not accessible elements to user 
 *		this parameter require $idUser to be a valid user
 * @return int number of items in course
 **/
function getNumCourseItems( $idCourse, $countHidden = TRUE, $idUser = FALSE, $countNotAccessible = TRUE ) {
	
	$query = "SELECT count(idOrg) FROM ".$GLOBALS['prefix_lms']."_organization";
	if( !$countNotAccessible ) {
		$query .= " LEFT JOIN ".$GLOBALS['prefix_lms']."_organization_access"
				 ." ON ( ".$GLOBALS['prefix_lms']."_organization.idOrg = ".$GLOBALS['prefix_lms']."_organization_access.idOrgAccess )";	
	}
	$query .= " WHERE (idCourse = '".(int)$idCourse."')"
			. "   AND (idResource <> 0)";
	if( !$countHidden ){ 
		$query .= " AND (visible = '1')";
	}
	
	if( !$countNotAccessible ) {
		$query .= " AND ( (".$GLOBALS['prefix_lms']."_organization_access.kind = 'user'"
				 ." 	AND ".$GLOBALS['prefix_lms']."_organization_access.value = '".(int)$idUser."')"
				 ."	    OR ".$GLOBALS['prefix_lms']."_organization_access.idOrgAccess IS NULL"
				 .")";	
	}
	
	$rs = mysql_query( $query );
	echo "\n\n<!-- $query -->";
	if( $rs === FALSE ) {
		return FALSE;
	} else {
		list($count) = mysql_fetch_row( $rs );
		mysql_free_result( $rs );
		return $count;
	}
}

/**
 * Return total items for a user in a given course whit a specified state
 * @param int $stat_idUser id of user
 * @param int $stat_idCourse id of the course
 * @param mixed $arrStatus array of status to search
 * @return int number of items in requested status
 **/
function getStatStatusCount($stat_idUser, $stat_idCourse, $arrStauts) {
	
	$query = "SELECT count(ct.idreference)"
		." FROM ".$GLOBALS['prefix_lms']."_commontrack ct, ".$GLOBALS['prefix_lms']."_organization org"
		." WHERE (ct.idReference = org.idOrg)"
		."   AND (ct.idUser = '".(int)$stat_idUser."')"
		."   AND (idCourse = '".(int)$stat_idCourse."')"
		."   AND (status IN ('".implode("','",$arrStauts)."'))";
	if( ($rsItems = mysql_query( $query )) === FALSE ) {
		echo $query;
		errorCommunication( "Error on query to get user count based on status" );
		return;
	}
	list($tot) = mysql_fetch_row( $rsItems );
	mysql_free_result( $rsItems );
	return $tot;
}

/**
 * Save notification of user status in a course
 * @param int $idUser id of the user
 * @param int $idCourse id of the course
 * @param int $status new status
 **/
function saveTrackStatusChange( $idUser, $idCourse, $status ) {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	list($prev_status) = mysql_fetch_row(mysql_query("
	SELECT status
 	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".(int)$idUser."' AND idCourse = '".(int)$idCourse."'"));
	
	$extra = '';
	if($prev_status != $status) {
		switch($status) {
			case _CUS_SUBSCRIBED : {
				//approved subscriptin for example
				$extra = ", date_inscr = NOW()";
			};break;
			case _CUS_BEGIN : {
				//first access
				$extra = ", date_first_access = NOW()";
			};break;
			case _CUS_END : {
				//end course
				$extra = ", date_complete = NOW()";
			};break;
		}
	}
	
	if(!mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_courseuser 
	SET status = '".(int)$status."' ".$extra."
	WHERE idUser = '".(int)$idUser."' AND idCourse = '".(int)$idCourse."'")) return false;
	
	$re = mysql_query("
	SELECT when_do 
	FROM ".$GLOBALS['prefix_lms']."_statuschangelog 
	WHERE status_user = '".(int)$status."' AND 
		idUser = '".(int)$idUser."' AND 
		idCourse = '".(int)$idCourse."'");
	
	if( mysql_num_rows($re) ) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_statuschangelog 
		SET when_do = NOW()
		WHERE status_user = '".(int)$status."' AND 
			idUser = '".(int)$idUser."' AND 
			idCourse = '".(int)$idCourse."'");
		
	} else {
		mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_statuschangelog 
		SET status_user = '".(int)$status."', 
			idUser = '".(int)$idUser."', 
			idCourse = '".(int)$idCourse."',
			when_do = NOW()");
	}
	
	if($prev_status != $status && $status == _CUS_END) {
		// send alert
		
		if(!mysql_num_rows($re)) {
			
			//add course's competences scores to user
			require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
			$competences_man = new Competences_Manager();
			$competences_man->AssignCourseCompetencesToUser($idCourse, $idUser/*getLogUserId()*/);
		}
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
		
		
		$teachers = Man_Course::getIdUserOfLevel($_SESSION['idCourse'], '6');
		$cd = new DoceboCourse($idCourse);
		$acl_man =& $GLOBALS['current_user']->getAclManager();
		
		$array_subst = array(
			'[user]' => $acl_man->getUserName($idUser),
			'[course]' => $cd->getValue('name')
		);
		
		$msg_composer = new EventMessageComposer('subscribe', 'lms');

		$msg_composer->setSubjectLangText('email', '_USER_END_COURSE_SBJ', false);
		$msg_composer->setBodyLangText('email', '_USER_END_COURSE_TEXT', $array_subst);

		$msg_composer->setSubjectLangText('sms', '_USER_END_COURSE_SBJ_SMS', false);
		$msg_composer->setBodyLangText('sms', '_USER_END_COURSE_TEXT_SMS', $array_subst);

		// send message to the user subscribed
		createNewAlert(	'UserCourseEnded', 
						'status', 
						'modify', 
						'1', 
						'User end course',
						$teachers, 
						$msg_composer );
		
		//add course's competences scores to user
		require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
		$competences_man = new Competences_Manager();
		$competences_man->AssignCourseCompetencesToUser($idCourse, $idUser/*getLogUserId()*/);
		
	}
	return true;
}


?>
