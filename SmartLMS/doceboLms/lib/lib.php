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

require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');

require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
require_once($GLOBALS['where_framework'].'/lib/lib.template.php');
require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');

require_once($GLOBALS['where_lms'].'/lib/lib.permission.php');
require_once($GLOBALS['where_lms'].'/lib/lib.module.php');

require_once($GLOBALS['where_framework'].'/lib/lib.donotdo.php');
require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');
require_once($GLOBALS['where_lms'].'/lib/lib.utils.php');


?>