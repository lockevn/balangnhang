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

define("_LINKS_PPATH", $GLOBALS["where_files_relative"]."/doceboCms/links/preview/");
require_once($GLOBALS["where_cms"]."/lib/lib.manModules.php");

function check_links_perm($pb, $id=0) {

	// --- Checking block permissions:

	$pb=(int)$pb;

	if ($pb == 0)
		return false;

	if ((int)$GLOBALS["area_id"] == 0)
		return false;

	$b_info=getBlockInfo($pb);

	if (!$b_info["view"])
		return false;

	if (!isset($b_info["name"]))
		return false;


	// --- Checking item permissions:

	if (($b_info["name"] == "links") && ($id > 0)) {

		$opt=loadBlockOption($pb);
		$path=$opt["path"];

		if ($opt["recurse"]) $path_q="t2.path LIKE '$path%'"; else $path_q="t2.path='$path'";

		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_links as t1 ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_links_dir as t2 ";
		$qtxt.="ON (($path_q AND t1.idFolder=t2.id) ";
		if ($path == "/")
			$qtxt.=" OR t1.idFolder='0'";
		$qtxt.=") WHERE t1.idLinks='$id' AND t1.publish='1'";

		$q=mysql_query($qtxt);
		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	// --- All test passed
	return true;
}


function count_links_hit($id) {

	require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

	$id=(int)$id;

	$key=array("links_item", $id, "hit");

	if ((!isBot()) && (!getItemValue($key))) {
		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_links SET click=click+1 WHERE idLinks='".$id."' LIMIT 1";
		$q=mysql_query($qtxt);
		setItemValue($key, true);
	}

}

function show_links($pb, $pid, $pos=0) {

	// ---------------------      ---------------------
	if (!check_links_perm($pb)) die("You can't access!");
	// ---------------------      ---------------------

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$lang=DoceboLanguage::createInstance("links", "cms");

	$opt=loadBlockOption($pb);

	$qtxt="SELECT id FROM ".$GLOBALS["prefix_cms"]."_links_dir WHERE path='".$opt["path"]."';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$blk_root=$row["id"];
	}
	else if ($opt["path"] == "/") $blk_root=0;
	else die();

	$sel_lang=getLanguage();

	$cf_path=$opt["path"];

	$out->add("<div class=\"show_links\">\n");

	$out->add('<div class="links_folder_nav">');
	$folder=(int)importVar("folder");
	folders_nav_bar($folder, $opt["path"], "links");
	$out->add('</div>');


	// --------------------- Finding folder id --------------------------

	if ((isset($_GET["folder"])) && ($_GET["folder"] > 0)) {

		$qtxt="SELECT id, path, lev FROM ".$GLOBALS["prefix_cms"]."_links_dir WHERE id='".(int)$_GET["folder"]."'";

	}
	else {

		$qtxt="SELECT id, lev FROM ".$GLOBALS["prefix_cms"]."_links_dir WHERE path='".$opt["path"]."'";

	}
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		if (isset($row["path"])) {
			$cf_path=$row["path"];

			if (substr($cf_path, 0, strlen($opt["path"])) != $opt["path"])
				$cf_path=$opt["path"]; // Anti "smart guy" code ;)
		}

		$cf_id=$row["id"];
		$lev=$row["lev"]+1;

	}
	else if (($q) && (mysql_num_rows($q) == 0)) {
		$lev=1; // ..assuming that the path == root
		$cf_id=0;
	}

	// --------------------- Categories Box ----------------------------

	if ($opt["recurse"]) {

		$fields="t1.id as folder_id, t2.title as folder_title, COUNT(DISTINCT t3.idLinks) as item_tot";
		$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_links_dir as t1 ";
		$qtxt.="INNER JOIN ".$GLOBALS["prefix_cms"]."_links_titles AS t2 ON (t2.iddir=t1.id) ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_links as t3 ON (t3.idFolder=t1.id) ";
		$qtxt.="WHERE t1.lev='".$lev."' AND t1.path LIKE '".$cf_path."%' ";
		$qtxt.="AND t2.lang='".getLanguage()."' AND t3.publish='1' AND t3.url!='' ";
		$qtxt.="GROUP BY t1.id ";

		drawCategoriesBox($out, $lang, $qtxt, "links");

	}

	// -----------------------------------------------------------------


	if (isset($cf_id))
		$path_q="t1.idFolder='".$cf_id."'";
	else
		$path_q="t1.path='".$cf_path."'";

	$sp=$pos*$opt["number"];

	$t1=$GLOBALS["prefix_cms"]."_links";
	$t2=$GLOBALS["prefix_cms"]."_links_info";

	$sel_q="t1.idLinks, t1.idFolder, t1.url, t1.fpreview, t2.title, t2.sdesc, t2.lang, t1.click, COUNT(t3.key1) as com_cnt";

	$qtxt ="SELECT $sel_q FROM $t1 as t1 ";
	$qtxt.="INNER JOIN ".$t2." AS t2 ON (t2.idl=t1.idLinks) ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_sysforum as t3 ON (t3.key1='links_comment' AND t3.key2=t1.idLinks) ";
	$qtxt.="WHERE ".$path_q." AND t1.publish='1' AND t2.lang='$sel_lang' ";
	$qtxt.="GROUP BY t1.idLinks";

	$ini=(isset($_GET["ini"]) ? (int)$_GET["ini"]-1 : 0)*$opt["number"];

	$q=mysql_query($qtxt);

	if ($q) {

		$tot=mysql_num_rows($q);

		if ($tot > 0) {
			$qtxt.=" LIMIT $ini,".$opt["number"];
			$q=mysql_query($qtxt);
		}
	}


	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$tab=new typeOne($opt["number"]);
	$tab->setTableStyle("links_table");

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php?mn=links&amp;pi=".getPI()."&amp;op=links&amp;folder=".$folder);
	$tab->nav_bar->setSymbol(getCmsNavSymbols());

	$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);

	$head=array($lang->def("_PREVIEW"), $lang->def("_LINK"), $lang->def("_DESCRIPTION"), $lang->def("_CLICK"));
	$head_type = array('preview', '', '', 'clicks');

	if ($use_comments) {
		$head[]=$lang->def("_COMMENTS");
		$head_type[]='comments';
	}

	$tab->setColsStyle($head_type);
	$tab->addHead($head);


	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$rowcnt=array();

			$file=_LINKS_PPATH.$row["fpreview"];
			if ($row["fpreview"] != "") {
				$img="<img src=\"".$file."\" alt=\"".$row["title"]."\" title=\"".$row["sdesc"]."\" />\n";
				$rowcnt[]=$img;
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$showitem_url="index.php?mn=links&amp;pi=".getPI()."&amp;op=showlink&amp;folder=".$row["idFolder"]."&amp;id=".$row["idLinks"];
			//$rowcnt[]="<a class=\"details_link\" href=\"".$showitem_url."\">".$row["title"]."</a>\n";
			$goto_url="index.php?mn=links&amp;pi=".getPI()."&amp;op=go&amp;id=".$row["idLinks"];
			$rowcnt[]=open_ext_link($goto_url, "item_link").$row["title"]."</a>";

			$alt =$lang->def("_READ_DESCRIPTION")." ".$row["title"];
			$img="<img class=\"cat_item_img\" src=\"".getPathImage()."block/description.gif\" alt=\"".$alt."\" title=\"".$alt."\" />\n";
			$link="<a href=\"".$showitem_url."\">".$img."</a>\n";
			$rowcnt[]=$link.$row["sdesc"];

			$img="<img class=\"cat_item_img\" src=\"".getPathImage()."block/clicks.gif\" alt=\" \" title=\" \" />\n";
			$rowcnt[]=$img."( ".$row["click"]." )";

			if ($use_comments) {
				$img_comment="<img class=\"cat_item_img\" src=\"".getPathImage()."block/comments.gif\" alt=\" \" title=\" \" />\n";
				$rowcnt[]="<a href=\"".$showitem_url."\">".$img_comment."</a>( <a href=\"".$showitem_url."\">".$row["com_cnt"]."</a> )\n";;
			}

			$tab->addBody($rowcnt);

		}
		$out->add($tab->getTable().$tab->getNavBar($ini, $tot));
	}



	$out->add("</div>\n"); // show_link
}



function link_details($pb) {

	if (isset($_GET["id"]))
		$id=(int)$_GET["id"];
	else
		return FALSE;

	// ---------------------                ---------------------
	if (!check_links_perm($pb, (int)$id)) die("You can't access!");
	// ---------------------                ---------------------

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("links", "cms");

	$out->add("<div class=\"show_links\">\n");

	$opt=loadBlockOption($pb);
	if (isset($opt["path"]))
		$path=$opt["path"];
	else
		$path="";

	$sel_lang=getLanguage();

	$t1=$GLOBALS["prefix_cms"]."_links";
	$t2=$GLOBALS["prefix_cms"]."_links_info";

	$qtxt ="SELECT * FROM $t1 LEFT JOIN $t2 ON ($t2.idl=idLinks AND $t2.lang='$sel_lang') ";
	$qtxt.="WHERE idLinks='$id' AND publish=1;";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
	}


	if (isset($_GET["folder"]))
		$folder=$_GET["folder"];
	else
		$folder=$row["idFolder"];


	$out->add('<div class="link_details">');
	$out->add('<div class="links_folder_nav">');
	folders_nav_bar($folder, $path, "links");
	$out->add('</div>');

	$out->add('<div class="links_box">');
	$out->add('<div class="links_text">');



	$file=_LINKS_PPATH.$row["fpreview"];
	if ($row["fpreview"] != "") {
		$img="<img class=\"link_img\" src=\"".$file."\" alt=\"".$row["title"]."\" title=\"".$row["sdesc"]."\" />\n";
		$out->add($img);
	}


	$goto_url="index.php?mn=links&amp;pi=".getPI()."&amp;op=go&amp;id=".$row["idLinks"];
	$out->add(open_ext_link($goto_url, "link_title").$row["title"]."</a>");
	$out->add(" (<span class=\"clicks_label\">".$lang->def("_CLICK").": ".$row["click"]."</span>)\n");


	if (isset($row["ldesc"])) {
		$out->add('<div class="links_description">');
		$out->add("<span class=\"description_label\">".$lang->def("_DESCRIPTION").":</span>\n");
		$out->add($row["ldesc"]);
		$out->add('</div>');
	}

	$out->add('<div class="links_url">');

	if (strlen($row["url"]) <= 60)
		$urltxt=$row["url"];
	else
		$urltxt=substr($row["url"], 0, 57)."...";

	$out->add(open_ext_link($goto_url, "link_title").$urltxt."</a>");
	$out->add('</div>');


	$out->add('<div class="links_line">&nbsp;</div>');

	$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);

	if ($use_comments) {
		load_comments($id, $pb);
	}

	$out->add('</div>'); // links_text
	$out->add('<div class="no_float"></div>');

	$out->add('</div>'); // links_box
	$out->add('</div>'); // link_details
	$out->add("</div>"); // show_links

}


function open_link_url($pb) {

	if (isset($_GET["id"]))
		$id=(int)$_GET["id"];
	else
		return FALSE;

	// ---------------------                ---------------------
	if (!check_links_perm($pb, (int)$id)) die("You can't access!");
	// ---------------------                ---------------------


	$qtxt ="SELECT url FROM ".$GLOBALS["prefix_cms"]."_links ";
	$qtxt.="WHERE idLinks='".$id."' AND publish='1'";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		$url=$row["url"];

		count_links_hit($id);

		$url =str_replace("{site_base_url}", getSiteBaseUrl(), $url);
		header("location: ".$url);
	}
	die();
}



function load_comments($id, $pb) {

	require_once($GLOBALS["where_framework"]."/lib/lib.sysforum.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("sysforum", "cms");

	$anonymous_comment=$GLOBALS["cms"]["anonymous_comment"];

	$force_comment=0;
	$can_comment=0;
	$can_post=(bool)(($GLOBALS['current_user']->isLoggedIn()) || ($GLOBALS["cms"]["anonymous_comment"] == "on"));
	$is_admin=isCmsAdmin();

	$opt=loadBlockOption($GLOBALS["pb"]);
	if (isset($opt["ov_comments"]))
		$force_comment=(int)$opt["ov_comments"];
	else
		$force_comment=0;

	$can_comment=true;

	if (($can_comment) || ($force_comment)) {

		$out->add("<div class=\"commentsHead\">".$lang->def("_COMMENTS")."</div>\n");

		$sf=new sys_forum("cms", "links_comment", $id);
		$sf->setPrefix("cms");
		$sf->can_write=$can_post;
		$sf->can_moderate=$is_admin;

		$folder=(int)importVar("folder");
		$sf->url="index.php?mn=links&amp;pi=".getPI()."&amp;op=showlink&amp;folder=".$folder."&amp;id=".$id;
		$out->add($sf->show());
	}
}


?>