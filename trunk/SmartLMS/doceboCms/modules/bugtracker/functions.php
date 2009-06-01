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


require_once($GLOBALS['where_framework']."/lib/lib.bugtracker.php");

// -- Url Manager Setup --
$mr_pattern="[P]/[P]/[P]/[O]/[T].html";
$mr_items=array("mn", "pi", "op");
$std_title="bugtracker";
cmsUrlManagerSetup($mr_pattern, $mr_items, $std_title, "mn=bugtracker&pi=".getPI()."&op=main");
// -----------------------

function showBtApplicationsList() {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$vis_item=$GLOBALS["cms"]["visuItem"];

	$bt=new BugTracker();
	$bt->setTableStyle("bugtracker");

	$out->add($bt->listBugTrackerApp($vis_item));

}

function showAppBugs() {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$vis_item=$GLOBALS["cms"]["visuItem"];

	$bt=new BugTracker();
	$bt->setTableStyle("bugtracker");

	$out->add($bt->showAppBugs($vis_item));
}

function getAddEditBugForm() {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$bt=new BugTracker();

	$out->add($bt->getAddEditBugForm());
}

function doSaveBug() {
	$bt=new BugTracker();
	$bt->saveBug();
}

function switchLock() {
	$bt=new BugTracker();
	$bt->switchLock();
}

function showAssigned() {
	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$bt=new BugTracker();
	$bt->setTableStyle("bugtracker");


	$out->add($bt->showAssigned());
}

function selAssignedUsers() {
	$GLOBALS['page']->addStart(
		'<link href="'.getPathTemplate('framework').'style/style.css" rel="stylesheet" type="text/css" />'."\n",
		'page_head');

	$bt=new BugTracker();
	$bt->selAssignedUsers();
}

function showBugDetails() {
	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$bt=new BugTracker();
	$bt->setTableStyle("bugtracker");


	$out->add($bt->showBugDetails());
}

function deletePatch() {
	$GLOBALS['page']->addStart(
		'<link href="'.getPathTemplate('framework').'style/style.css" rel="stylesheet" type="text/css" />'."\n",
		'page_head');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$bt=new BugTracker();

	$out->add($bt->deletePatch());
}

function getPatch() {
	$bt=new BugTracker();
	$bt->getPatch();
}

function setNotify() {
	$bt=new BugTracker();
	$bt->setNotify();
}

function showHideBugSearchForm() {
	$bt=new BugTracker();
	$bt->showHideBugSearchForm();
}

function setBugsOrder() {
	$bt=new BugTracker();
	$bt->setBugsOrder();
}

?>
