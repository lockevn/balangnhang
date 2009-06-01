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

if($GLOBALS["cms"]["use_mod_rewrite"] == 'on')
{
	list($title, $mr_title) = mysql_fetch_row(mysql_query(	"SELECT title, mr_title"
															." FROM ".$GLOBALS["prefix_cms"]."_area"
															." WHERE idArea = '".$GLOBALS["area_id"]."'"));
	
	if ($mr_title != "")
		$page_title = format_mod_rewrite_title($mr_title);
	else
		$page_title = format_mod_rewrite_title($title);
	
	$backurl = 'page/'.$GLOBALS["area_id"].'/'.$page_title.'.html';
}
else
	$backurl = "index.php?special=changearea&amp;newArea=".$GLOBALS["area_id"];

$opt=loadBlockOption($GLOBALS["pb"]);
$cat_id=(int)$opt["category_id"];


$op=importVar("op");
if (empty($op))
	$op="main";
switch ($op) {

	case "main": {
		showFaqList($cat_id);
	} break;

	case "search": {
		setSearch();
	} break;

	case "addfaq": {
		addeditFaq($cat_id, "add");
	} break;

	case "editfaq": {
		addeditFaq($cat_id, "edit");
	} break;

	case "savefaq": {
		if (!isset($_POST["undo"]))
			saveFaq($cat_id);
		else
			showFaqList($cat_id);
	} break;

}


/* $GLOBALS["page"]->add("<div style=\"text-align: right;\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content"); */

$GLOBALS["page"]->add("</div>\n", "content");


?>