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

$GLOBALS["page"]->add("<div style=\"text-align: right\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");

$op=importVar("op");
if (empty($op))
	$op="shownews";
switch ($op) {
	case "shownews": {
		show_news($GLOBALS["pb"]);
	} break;

	case "news": {
		if ((isset($_GET["sel"])) && ($_GET["sel"]))
			show_news_list($GLOBALS["pb"], "", "sel");
		else
			show_news_list($GLOBALS["pb"]);
	} break;

	/* case "listcat": {
		show_news_list($GLOBALS["pb"]);
	} break; */

	case "listtopic": {
		if ((isset($_GET["sel"])) && ($_GET["sel"]))
			show_news_list($GLOBALS["pb"], "", "sel");
		else
			show_news_list($GLOBALS["pb"]);
	} break;

	case "download": {
		downloadNewsAttachment();
	} break;

}


$GLOBALS["page"]->add("<div style=\"text-align: right\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");
$GLOBALS["page"]->add("</div>\n", "content");




function show_news($pb) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	// ---------------------                                   ---------------------
	if (!check_news_perm($GLOBALS["pb"], (int)$_GET["id"])) die("You can't access!");
	// ---------------------                                   ---------------------

	$opt=loadBlockOption($pb);

	$news_id=(int)$_GET["id"];
	$qtxt="SELECT publish_date , title, long_desc, idFolder FROM ".$GLOBALS["prefix_cms"]."_news WHERE idNews='".(int)$news_id."';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$data=$GLOBALS["regset"]->databaseToRegional($row["publish_date"]); //conv_datetime($row["publish_date"], 0, _TIMEOFFSET);

		$out->add('<div class="news_read">');
		$level_id = $GLOBALS['current_user']->getUserLevelId();
		$folder_id=$row['idFolder'];
		switch($level_id)	{
			case ADMIN_GROUP_ADMIN:
				require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");
				$ctp=new CmsTreePermissions("news");
 				$consent_edit=$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, TRUE);
				if (!$consent_edit) {
					$title=$row['title'];
					break;
				} // else drop down case
			case ADMIN_GROUP_GODADMIN:
				$qr="SELECT param_value AS base_url FROM ".$GLOBALS["prefix_fw"]."_setting WHERE param_name like 'url'";
				$q=mysql_query($qr);
				if (($q) && (mysql_num_rows($q)==1)) {
					list($base_url)=mysql_fetch_array($q);
				} else  // guessing base URL for doceboCore
					$base_url='http://'.$_SERVER['HTTP_HOST'].'/doceboCore/';
				$title='<a href="'.$base_url
					.'index.php?modname=news&op=modnews&of_platform=cms&id_news='
	 				.$news_id.'&id_folder='.$folder_id.'" target=blank>'.$row['title'].'</a>';
	 			break;
			default:
				$title=$row['title'];
				break;
			}
		$out->add('<div class="news_title">'
			.''.$title);

		if ($opt["show_newsdate"])
			$out->add('<span class="news_date">'.$data.'</span>');

		$out->add('</div>');

		require_once($GLOBALS["where_cms"]."/admin/modules/news/functions.php");

		$news_topic=get_maintopic_info((int)$_GET["id"]);

		if (isset($news_topic["label"])) {
			show_topic_img($news_topic["label"], $news_topic["image"], $news_topic["img_align"]);
		}

		$out->add('<div class="news_box">');
		$out->add('<div class="news_text">'.fixAnchors($row["long_desc"]));

		$out->add('<div class="news_line"></div>');

		$attach_txt=getNewsAttach((int)$_GET["id"]);
		if (!empty($attach_txt)) {
			$out->add($attach_txt);
			$out->add('<div class="news_line"></div>');
		}

		$use_comments=((isset($opt["use_comments"]) && ($opt["use_comments"] == 1)) ? true : false);

		if ($use_comments) {
			load_news_comments((int)$_GET["id"], $pb);
		}

		$out->add('</div>'); // news_text
		$out->add('<div class="no_float"></div>');

		$out->add('</div>'); // news_box
		$out->add('</div>'); // news_read

	}

}



?>