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

// ----------------------------------------------------------------------------

$modname=importVar("modname");
if (empty($modname))
	$modname="company";

$GLOBALS["page"]->add("<div class=\"crm_box\">\n", "content");

$opt=loadBlockOption($GLOBALS["pb"]);
$mode =((isset($opt["mode"])) && ($opt["mode"] == "horizontal") ? "horizontal" : "vertical");
if ($mode == "horizontal") {
	showCrmMenu($mode);
}


if (isset($_GET["op"])) {
	$op =$_GET["op"];

	switch ($op) {
		case "delcmpasignlog": {
			$id =(int)$_GET["company_id"];
			deleteCrmLog("company", $id);
		} break;
		case "deltasklog": {
			$id =(int)$_GET["task_id"];
			deleteCrmLog("task", $id);
		} break;
	}
}


require_once($GLOBALS["where_crm"]."/modules/".$modname."/".$modname.".php");
$GLOBALS["page"]->add("</div>\n", "content");

// ----------------------------------------------------------------------------

$GLOBALS["page"]->add("</div>\n", "content");


?>