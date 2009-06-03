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

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_framework'].'/class/class.dashboard.php');

class Dashboard_Cms extends Dashboard {

	function Dashboard_Cms() {

	}

	function getBoxContent() {

		$html = '';
		define("_BBCLONE_DIR", $GLOBALS["where_cms"]."/addons/bbclone/");

		$GLOBALS['cms_stats_here'] = '';

		$lang =& DoceboLanguage::createInstance('dashboard', 'framework');

		//require_once(_BBCLONE_DIR."cms_constants.php");

		$html = array();
//		$stats = getPageViewArray();
//		$html[] = '<h2 class="course_main_title">'.$lang->def('_CMS_PANEL').'</h2>'
//			.'<p class="cms_main">'
//				.$lang->def('_CMS_STAT_LAST_DAY').': <b>'.$stats['last_day'].'</b>;<br />'
//				.$lang->def('_CMS_STAT_LAST_WEEK').': <b>'.$stats['last_week'].'</b>;<br />'
//				.$lang->def('_CMS_STAT_LAST_MONTH').': <b>'.$stats['last_month'].'</b>;<br />'
//				.$lang->def('_CMS_STAT_LAST_YEAR').': <b>'.$stats['last_year'].'</b>;'
//			.'</p><p>'
//				.$lang->def('_CMS_STAT_TOTALVISIT').': <b>'.$stats['totalvisit'].'</b>;<br />'
//				.$lang->def('_CMS_STAT_TOTALUNIQUE').': <b>'.$stats['totalunique'].'</b>;'
//			.'</p>';

		return $html;
	}

}

?>