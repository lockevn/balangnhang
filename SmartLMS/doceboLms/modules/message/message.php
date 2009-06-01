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

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');

$um =& UrlManager::getInstance("message");
$um->setStdQuery("modname=message&op=message");


addCss("style_message", "framework");
if(!defined('IN_LMS')) define("IN_LMS", TRUE);

define("_PATH_MESSAGE", '/doceboLms/'.$GLOBALS['lms']['pathmessage']);
define("_MESSAGE_VISU_ITEM", $GLOBALS['lms']['visuItem']);
define("_MESSAGE_PL_URL", $GLOBALS['lms']['url']);

require_once($GLOBALS['where_framework'].'/lib/lib.message.php');


?>