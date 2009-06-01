<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* http://www.docebocms.com                                              */
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

$GLOBALS["page"]->add("<div style=\"text-align: right;\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");


$op=importVar("op");
switch ($op) {

		case "slide" : {
			show_slide($GLOBALS["pb"]);
		};break;

		case "slide_sel" : {
			show_slide($GLOBALS["pb"], "sel");
		};break;

		case "gallery" : {
			if ((isset($_GET["sel"])) && ($_GET["sel"]))
				show_gallery($GLOBALS["pb"], "sel");
			else
				show_gallery($GLOBALS["pb"]);
		};break;

		case "slideshow": {
			if ((isset($_GET["sel"])) && ($_GET["sel"]))
				show_slide($GLOBALS["pb"], "sel");
			else
				show_slide($GLOBALS["pb"]);
		} break;

		case "file" : {
			if ((isset($_GET["sel"])) && ($_GET["sel"]))
				show_file($GLOBALS["pb"], "sel");
			else
				show_file($GLOBALS["pb"]);
		};break;

		case "gallery_setpos": {
			if ((isset($_GET["sel"])) && ($_GET["sel"]))
				goto_selpos("sel");
			else
				goto_selpos();
		};break;

		case "download": {
			downloadMediaItem();
		} break;

}


$GLOBALS["page"]->add("<div style=\"text-align: right;\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");

$GLOBALS["page"]->add("</div>\n", "content");




function show_slide_old($pb, $type="dir") {

	getPageId();
	setPageId($GLOBALS["area_id"], $pb);

	$opt=loadBlockOption($pb);

	$pos=(int)$_GET["pos"];

	if ($type == "dir") {
		$path_q=get_path_q("media", $opt);

		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media ";
		$qtxt.="WHERE $path_q AND publish=1";
	}
	else if ($type == "sel") {

		$t1=$GLOBALS["prefix_cms"]."_area_block_items";
		$qtxt="";
		$qtxt.="SELECT t1.id, t2.idMedia, t2.fname, t2.real_fname, t2.fpreview FROM $t1 as t1, ";
		$qtxt.=$GLOBALS["prefix_cms"]."_media as t2 ";
		$qtxt.="WHERE t1.idBlock='$pb' AND t2.idMedia=t1.item_id AND t1.type='media' ORDER BY t1.id";

	}

	$q=mysql_query($qtxt);

	if ($q) {

		$tot=mysql_num_rows($q);

		if ($tot > 0) {
			$qtxt.=" LIMIT $pos,1;";
			$q=mysql_query($qtxt);  //echo $qtxt;

			$row=mysql_fetch_array($q);
			$id=$row["idMedia"];
		}
	}

	$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);

	load_media($id, $type, $opt["showtitle"], $opt["showdesc"], $opt["path"], $pb, $use_comments);
	write_nav_form($pos, $tot, $type, $pb);

}



function write_nav_form($pos, $tot, $type, $pb) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	if ($type == "dir") $op="slide";
	else if ($type == "sel") $op="slide_sel";

	$url="index.php?mn=media&amp;pb=$pb&amp;op=$op&amp;pos=";

	$out->add("<br /><table width=\"100%\">\n");
	$out->add("<tr><td align=\"left\">\n");

	if ($pos > 0) {
		$out->add("<a href=\"$url".($pos-1)."\">"._PREV."</a>\n");
	}

	$out->add("</td><td align=\"right\">\n");

	if ($pos < $tot-1) {
		$out->add("<a href=\"$url".($pos+1)."\">"._NEXT."</a>\n");
	}

	$out->add("</td></tr>\n");
	$out->add("</table><br /><br />\n");

}


function show_gallery($pb, $type="dir") {

	if (isset($_GET["pos"]))
		$pos=$_GET["pos"];
	else
		$pos=0;

	show_gallery_pag($pb, $type, $pos);

}



?>