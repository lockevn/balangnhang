<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @version  $Id: $
 */
// ----------------------------------------------------------------------------


if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS["where_framework"]."/lib/resources/lib.resource_model.php");


Class ResourceCourse_edition extends ResourceModel {


	function ResourceCourse_edition($prefix=FALSE, $dbconn=NULL) {
		$this->setResourceCode("course_edition");
		parent::ResourceModel($prefix, $dbconn);
	}


	function checkAvailability($resource_id, $start_date=FALSE, $end_date=FALSE) {
		/* $res=FALSE;

		$found=$this->getResourceEntries((int)$resource_id, $start_date, $end_date);

		if (count($found) < $this->getAllowedSimultaneously())
			$res=TRUE; */

		$res=TRUE;

		return $res;
	}


}





?>