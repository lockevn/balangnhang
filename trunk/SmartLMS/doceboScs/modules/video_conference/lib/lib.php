<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

if(!defined("IN_DOCEBO")) die("You can't access this file directly!");

require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');
require_once($GLOBALS['where_scs'].'/lib/lib.utils.php');
require_once($GLOBALS['where_scs'].'/lib/lib.check.php');

require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
require_once($GLOBALS['where_scs'].'/lib/lib.emoticons.php');

require_once($GLOBALS['where_scs'].'/lib/resource.main.php');
require_once($GLOBALS['where_scs'].'/lib/resource.chat.php');
require_once($GLOBALS['where_scs'].'/lib/resource.user.php');
require_once($GLOBALS['where_scs'].'/lib/resource.room.php');

require_once($GLOBALS['where_scs'].'/lib/lib.htmlpurifier.php');

require_once($GLOBALS['where_scs'].'/lib/lib.docebodb.php');

require_once($GLOBALS['where_scs'].'/lib/lib.template.php');

?>