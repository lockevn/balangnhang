<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

require_once( dirname(__FILE__).'/lib.event.php' );

/**
 * This is the class for ClassEvents in Docebo
 * 
 * @package admin-core
 * @subpackage event
 * @version  $Id: lib.coursenotifier.php 113 2006-03-08 18:08:42Z ema $
 */
class DoceboCourseNotifier extends DoceboEventConsumer {

	function _getConsumerName() {
		return "DoceboUserNotifier";
	}

	function actionEvent( &$event ) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
		
		parent::actionEvent($event);
		
		$acl_man =& $GLOBALS['current_user']->getACLManager();
		
		// recover event information
		$id_user 	= $event->getProperty('userdeleted');
		
		
		$man_subs = new CourseSubscribe_Management();
		$man_subs->unsubscribeUserFromAllCourses($id_user);
		return true;
		
	}
	
}

?>
