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


$css=getModuleCss($GLOBALS["pb"]);
$GLOBALS["page"]->add("<div class=\"".$css."\">\n", "content");
$GLOBALS["page"]->add(getModuleBlockTitle($GLOBALS["pb"]), "content");

// $opt=loadBlockOption($GLOBALS["pb"]);


$op=importVar("op");
if (empty($op))
	$op="main";



switch ($op) { // Student profile mode

	case "main": {
		profile_searchMain();
	} break;

	case "profile": {
		if (!searchTeacherMode())
			psShowProfile();
		else
			psShowTeacherProfile();
	} break;

}




$GLOBALS["page"]->add("</div>\n", "content");

?>
