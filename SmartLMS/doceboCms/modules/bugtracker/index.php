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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

require_once($GLOBALS["where_cms"]."/modules/bugtracker/functions.php");

$css=getModuleCss($GLOBALS["pb"]);
$GLOBALS["page"]->add("<div class=\"".$css."\">\n", "content");
$GLOBALS["page"]->add(getModuleBlockTitle($GLOBALS["pb"]), "content");



$out=& $GLOBALS['page'];
$out->setWorkingZone('content');

$out->add("<div class=\"body_block\">\n");

$op=importVar("op");
switch ($op) {

	case "main": {
		showBtApplicationsList();
	} break;

	case "show": {
		showAppBugs();
	} break;

	case "addbug":
	case "editbug": {
		getAddEditBugForm();
	} break;


	case "savebug": {
		if (!isset($_POST["undo"]))
			doSaveBug();
		else
			showAppBugs();
	} break;


	case "switchlock": {
		switchLock();
	} break;


	case "assign": {
		showAssigned();
	} break;

	case "assignnewuser": {
		selAssignedUsers();
	} break;

	case "bugdetails": {
		showBugDetails();
	} break;

	case "delpatch": {
		deletePatch();
	} break;

	case "getpatch": {
		getPatch();
	} break;

	case "setnotify": {
		setNotify();
	} break;

	case "showhidesearchform": {
		showHideBugSearchForm();
	} break;

	case "setorder": {
		setBugsOrder();
	} break;

}

$out->add("</div>\n"); // body_block


$GLOBALS["page"]->add("</div>\n", "content"); // getModuleCss

?>