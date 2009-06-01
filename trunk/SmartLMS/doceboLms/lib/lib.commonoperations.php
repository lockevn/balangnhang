<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function lmsLoginOperation() {
	require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.preassessment.php');

	$pa_man = new AssessmentList();
	$user_course_as_assessment = $pa_man->getUserAssessmentSubsription($GLOBALS['current_user']->getArrSt());

	if(is_array($user_course_as_assessment)) {

		$subs_man = new CourseSubscribe_Management();
		$subs_man->multipleUserSubscribe(	getLogUserId(),
											$user_course_as_assessment['course_list'],
											$user_course_as_assessment['level_number']);
	}
}


function lmsLogoutOperation() {

}


?>