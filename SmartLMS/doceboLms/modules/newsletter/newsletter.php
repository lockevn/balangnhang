<?php
/*************************************************************************/
/* DOCEBO LMS - Learning Managment System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access!');


if(!defined('IN_LMS')) define("IN_LMS", TRUE);

require_once($GLOBALS["where_framework"]."/modules/newsletter/newsletter.php");



?>