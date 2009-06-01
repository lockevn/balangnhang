<?php

/************************************************************************/
/* DOCEBO CMS - Content Managment System                                */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2005                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function getTitle() {
	return 'Docebo CMS '.$GLOBALS['cms']['cms_version'];
}

require_once($GLOBALS['where_framework'].'/lib/lib.pagewriter.php');
require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
require_once($GLOBALS['where_framework'].'/lib/lib.template.php');
require_once($GLOBALS['where_framework'].'/lib/lib.donotdo.php');
require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');

require_once($GLOBALS['where_cms'].'/lib/lib.area.php');
require_once($GLOBALS["where_cms"]."/lib/lib.template_lang.php");
require_once($GLOBALS["where_cms"]."/lib/lib.permission.php");

?>
