<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


require_once( dirname(__FILE__).'/lib.event.php' );

/**
 * This is the class for ClassEvents in Docebo
 *
 * @package admin-core
 * @subpackage event
 * @version  $Id:$
 */
class DoceboCompanyNotifier extends DoceboEventConsumer {

	function _getConsumerName() {
		return "DoceboCompanyNotifier";
	}

	function actionEvent( &$event ) {

		parent::actionEvent($event);

		$event_throw = $event->getClassName();
		switch($event_throw) {
			case "UserDel" : {
				$id_user 	= $event->getProperty('userdeleted');

				// remove user from associated
				require_once($GLOBALS['where_framework'].'/lib/lib.company.php');

				$ccm = new CoreCompanyManager();
				$ccm->removeUserFromCompany($id_user);
			};break;
		}
		return true;
	}

}

?>