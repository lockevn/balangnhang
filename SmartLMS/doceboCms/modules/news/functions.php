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


if (!defined("_TOPIC_FPATH")) define("_TOPIC_FPATH", $GLOBALS["where_files_relative"]."/doceboCms/topic/");


function check_news_perm($pb, $id=0) {

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

	if (($b_info["name"] == "news") && ($id > 0)) {

		$opt=loadBlockOption($pb);
		$path=$opt["path"];

		if ($opt["recurse"])
			$path_q="t2.path LIKE '$path%'";
		else
			$path_q="t2.path='$path'";

		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_news as t1 ";
		$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_news_dir as t2 ";
		$qtxt.="ON ($path_q AND t1.idFolder=t2.id) ";
		$qtxt.="WHERE t1.idNews='".$id."' ";
		if (($path == "/") || ($path == "/root"))
			$qtxt.=" OR t1.idFolder='0'";

		$q=mysql_query($qtxt);
		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	if (($b_info["name"] == "news_sel") && ($id > 0)) {
		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
		$q=mysql_query($qtxt);

		if ((!$q) || (mysql_num_rows($q) == 0)) {
			return false;
		}
	}


	// --- All test passed
	return true;
}


function check_news_perm_old($pb, $id) {
	// Controllo che l'utente possa visualizzare la news..

	return true;

	$can_see=0;
	$valid_item=0;
	$valid_folder=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$pb' AND item_id='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_item=1;
	}


	$opt=loadBlockOption($pb);
	$path=$opt["path"];

	if ($opt["recurse"]) $path_q="t2.path LIKE '$path%'"; else $path_q="t2.path='$path'";

	if ($path != "/") {
		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_news as t1, ".$GLOBALS["prefix_cms"]."_news_dir as t2 ";
		$qtxt.="WHERE $path_q AND t1.idFolder=t2.id AND t1.idNews='$id';";
	}
	else {
		$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_news as t1, ".$GLOBALS["prefix_cms"]."_news_dir as t2 ";
		$qtxt.="WHERE (($path_q AND t1.idFolder=t2.id) OR t1.idFolder='0') AND t1.idNews='$id';";
	}
	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		$valid_folder=1;
	}


	if (($valid_item) || ($valid_folder)) {
		//-TP// $user_grp=getUserGroup();
		//-TP// $allowed_grp=db_block_groups($pb);
		//-TP// $can_see=can_see_block($user_grp, $allowed_grp);
		$can_see=true;
	}

	if (!$can_see) die("You can't access!");
}



function show_news_list($idBlock, $title="", $type="dir", $block="normal") {

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	// ---------------------          ---------------------
	if (!check_news_perm($idBlock)) die("You can't access!");
	// ---------------------          ---------------------

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('news', 'cms');

	$option = loadBlockOption($idBlock);

	if ((isset($option["force_lang"])) && (!empty($option["force_lang"])))
		$sel_lang=$option["force_lang"];
	else
		$sel_lang=getLanguage();

	$show_search=false;
	$searchkey="";
	$tot=0;

	if ($type == "dir") {
		$qtxt=getNewsDirQuery($sel_lang, $option, $show_search, $searchkey, $tot);
		$extra="";
	}
	else if ($type == "sel") {
		$qtxt=getNewsSelQuery($sel_lang, $option, $show_search, $searchkey, $tot);
		$extra="&amp;sel=1";
	}


	if($title != '') $out->add('<div class="titleBlock">'.$title.'</div>');
	$out->add('<div class="body_block">');

	$out->add('<div class="news_list">');


	if ($show_search) {
		$form=new Form();

		$url="index.php?".str_replace("&", "&amp;", $_SERVER['QUERY_STRING']);
		$out->add($form->openForm("search_form", $url));
		$out->add($form->openElementSpace());
		$out->add($form->getTextfield($lang->def("_SEARCH"), "searchkey", "searchkey", 255, htmlentities(strip_tags($searchkey)) ));

		$out->add($form->closeElementSpace());
		$out->add($form->openButtonSpace());
		$out->add($form->getButton('search', 'search', $lang->def("_SEARCH")));
		$out->add($form->closeButtonSpace());
		$out->add($form->closeForm());
	}

	if ($block == "normal") {
		$out->add(show_news_list_normal($out, $lang, $option, $type, $searchkey, $extra, $qtxt, $tot));
	}
	else if ($block == "small") {
		$out->add(show_news_list_small($out, $lang, $option, $type, $qtxt));
	}


	$out->add('</div>');

	$out->add('</div>');

}


function show_news_list_normal(& $out, & $lang, $option, $type, $searchkey, $extra, $qtxt, $tot) {

	$use_comments=((isset($option["use_comments"]) && ($option["use_comments"] == 1)) ? true : false);
	$show_attach_inline =((isset($option["show_attach_inline"]) && ($option["show_attach_inline"] == 1)) ? true : false);

	$q = mysql_query($qtxt); //echo $qtxt;

	$i=0;
	$rss_arr=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_row($q)) {

			if ($type == "dir") {
				list($date, $title, $reflink, $desc, $longdesc, $idNews, $cancomment, $com_cnt, $topic_label, $topic_image, $topic_imgalign, $topic_id, $news_cat, $idFolder) = $row;
			}
			else if ($type == "sel") {
				list($date, $title, $reflink, $desc, $longdesc, $idNews, $cancomment, $com_cnt, $topic_label, $topic_image, $topic_imgalign, $topic_id) = $row;
			}

			if ($GLOBALS["cms"]["use_mod_rewrite"] == "on")
				$news_url="news/".getPI()."/".$idNews."/".format_mod_rewrite_title($title).".html";
			else
				$news_url="index.php?mn=news&amp;pi=".getPI()."&amp;id=$idNews";

			$out->add('<div class="news_box"><div class="news_title">');
			if ($option["show_newsdate"])
				$out->add('<span class="news_date">'.$GLOBALS["regset"]->databaseToRegional($date).'</span>');


			if ($searchkey != "") {
				$title=preg_replace("/".$searchkey."/i", "<span class=\"highlight\">$0</span>", $title);
				$desc=preg_replace("/".$searchkey."/i", "<span class=\"highlight\">$0</span>", $desc);
			}


			$has_longdesc=(bool)(($longdesc != "") && ($longdesc != "<br _moz_editor_bogus_node=\"TRUE\"/>"));

			if (($has_longdesc) && ($reflink == "")) $out->add('<a href="'.$news_url.'">');
			if ($reflink != "") $out->add(open_ext_link($reflink)); //echo("<a href=\"".$reflink."\" target=\"_blank\">");
			$out->add($title);
			if (($has_longdesc) || ($reflink != "")) $out->add('</a>');

			$out->add('</div>'
				.'<div class="news_text">');

			if ((int)$topic_id > 0)
				show_topic_img($topic_label, $topic_image, $topic_imgalign);


			$out->add($desc);

			if ($show_attach_inline) {
				$out->add("\n");
				$out->add(getNewsAttach($idNews, FALSE, "inline_attach_list"));
			}

			$out->add('</div>');
			$out->add('<div class="no_float"></div>');

			$link_text="";

			if ($option["show_newslink"]) {
				if ($has_longdesc) {
					$link_text.='<div class="read_more">';
					$link_text.='<a href="'.$news_url.'">'.$lang->def("_READ_MORE").'</a></div>';
				}
				else if ($reflink != "") {
					$link_text.="<div class=\"more_here\">\n";
					$link_text.=open_ext_link($reflink);
					$link_text.=$lang->def("_MORE_HERE")."</a></div>\n";
				}
			}


			if ( 
			   ($use_comments) && ($option["show_commentslink"])) {
				$link_text.="<div class=\"inline_comments\">\n";
				$link_text.='<a href="'.$news_url.'">'.$lang->def("_COMMENTS").' ('.$com_cnt.')</a>';
				$link_text.="</div>\n";
			}

			if (($option["show_topiclink"]) && ((int)$topic_id > 0)) {
				$link_text.="<div class=\"topic_link\">\n";
				$url="index.php?mn=news&amp;pi=".getPI().$extra."&amp;op=listtopic&amp;id=$idNews&amp;topicid=".$topic_id;
				$link_text.="<a href=\"".$url."\">".$topic_label."</a>";
				$link_text.="</div>\n";
			}

			if ($link_text != "") {
				$out->add("<div class=\"news_inline_links\">\n");
				$out->add($link_text);
				$out->add("</div>\n");
			}

			$out->add('</div>'); // news_box


			// ---------------- Feed Info -------------------------
			$timestamp=$GLOBALS["regset"]->databaseToTimestamp($date);

			$rss_arr[$i]["title"]=$title;
			$rss_arr[$i]["description"]=$desc;
			$rss_arr[$i]["url"]=($reflink != "" ? $reflink : $GLOBALS["cms"]["url"].$news_url);
			$rss_arr[$i]["date"]=date("r", $timestamp);
			$i++;
			// ----------------------------------------------------
		}
	}

	if ($tot > $option["number"]) {

		// Navigation bar:
		require_once($GLOBALS["where_framework"]."/lib/lib.navbar.php");
		$nav=new NavBar("sp", $option["number"], $tot);
		$url="index.php?mn=news&amp;pi=".getPI().$extra."&amp;op=news";
		$nav->setLink($url);
		$nav->setSymbol(getCmsNavSymbols());

		$out->add($nav->getNavBar());

	}

	addNewsFeed($rss_arr);

}


function show_news_list_small(& $out, & $lang, $option, $type, $qtxt) {

	$q = mysql_query($qtxt); //echo $qtxt;

	$out->add("<div class=\"block_small\">\n");

	$i=0;
	$rss_arr=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			if ($GLOBALS["cms"]["use_mod_rewrite"] == "on")
				$news_url="news/".getPI()."/".$row["idNews"]."/".format_mod_rewrite_title($row["title"]).".html";
			else
				$news_url="index.php?mn=news&amp;pi=".getPI()."&amp;id=".$row["idNews"];

			$out->add('<div class="news_title">');
			if ($option["show_newsdate"])
				$out->add('<span class="news_date">'.$GLOBALS["regset"]->databaseToRegional($row["publish_date"]).'</span>');


			$has_longdesc=(bool)(($row["long_desc"] != "") && ($row["long_desc"] != "<br _moz_editor_bogus_node=\"TRUE\"/>"));

			if (($has_longdesc) && ($row["reflink"] == "")) $out->add('<a href="'.$news_url.'">');
			if ($row["reflink"] != "") $out->add(open_ext_link($row["reflink"]));
			$out->add($row["title"]);
			if (($has_longdesc) || ($row["reflink"] != "")) $out->add('</a>');

			$out->add('</div>'
				.'<div class="news_text">');

//			$out->add($row["short_desc"]);

			$out->add("</div>\n");


			// ---------------- Feed Info -------------------------
			$timestamp=$GLOBALS["regset"]->databaseToTimestamp($row["publish_date"]);

			$rss_arr[$i]["title"]=$row["title"];
			$rss_arr[$i]["description"]=$row["short_desc"];
			$rss_arr[$i]["url"]=($row["reflink"] != "" ? $row["reflink"] : $GLOBALS["cms"]["url"].$news_url);
			$rss_arr[$i]["date"]=date("r", $timestamp);
			$i++;
			// ----------------------------------------------------
		}
	}

	$out->add("</div>\n");

	addNewsFeed($rss_arr);
}


function getNewsDirQuery($sel_lang, $option, & $show_search, & $searchkey, & $tot) {

	$fields ="t1.publish_date, t1.title, t1.reflink, t1.short_desc, ";
	$fields.="t1.long_desc, t1.idNews, t1.cancomment, COUNT(t2.key1) as com_cnt, t4.label, t4.image, t3.img_align, ";
	$fields.="t3.topic_id, t5.title as topic_title, t1.idFolder";

	$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_news as t1 ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_sysforum as t2 ON (t2.key1='news_comment' AND t2.key2=t1.idNews) ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_news_topic as t3 ON (t1.idNews=t3.idNews AND t3.main='1') ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_topic as t4 ON (t4.topic_id=t3.topic_id AND t4.language='$sel_lang') ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_news_titles as t5 ON (t5.iddir=t1.idFolder and t5.lang='$sel_lang') ";


	if (isset($_GET["op"]))
		$op=substr($_GET["op"], 0, 30);
	else
		$op="shownews";

	$queryF = "
	SELECT id
	FROM ".$GLOBALS["prefix_cms"]."_news_dir
	WHERE path LIKE '".$option['path'].( $option['recurse'] ? '%' : '' )."'";
	$reFolder = mysql_query($queryF);

	$idFolderCollection = '';
	while(list($id_f) = mysql_fetch_row($reFolder)) {
		$idFolderCollection .= $id_f.',';
	}
	if($option['path'] == '/' ) $idFolderCollection .= '0,';

	$qtxt.="WHERE t1.publish='1' AND t1.idFolder IN (".substr($idFolderCollection, 0, -1).") AND ";


	if ((isset($option["show_search"])) && ($option["show_search"])) {
		$show_search=true;
	}
	else {
		$show_search=false;
	}

	if ($op == "listtopic") {
		$qtxt.="t3.topic_id='".(int)$_GET["topicid"]."' AND ";
		$show_search=true;
	}


	if ((isset($_POST["searchkey"])) && (trim($_POST["searchkey"]) != "")) {
		$searchkey=trim($_POST["searchkey"]);

		$qtxt.="(t1.title LIKE '%".$searchkey."%' OR ";
		$qtxt.="t1.short_desc LIKE '%".$searchkey."%' OR ";
		$qtxt.="t1.long_desc LIKE '%".$searchkey."%') ";
		$qtxt.=" AND ";

	}
	else
		$searchkey="";


	$qtxt.="t1.language = '$sel_lang' GROUP BY t1.idNews ";
	$qtxt.="ORDER BY t1.ord ASC, t1.publish_date DESC "; //echo $qtxt;

	$q=mysql_query($qtxt);
	if ($q)
		$tot=mysql_num_rows($q);

	$ini=(isset($_GET["sp"]) ? (int)$_GET["sp"]-1 : 0)*$option["number"];

	$qtxt.="LIMIT ".$ini.", ".$option['number'];

	return $qtxt;
}




function getNewsSelQuery($sel_lang, $option, & $show_search, & $searchkey, & $tot) {

	$fields ="t1.publish_date, t1.title, t1.reflink, t1.short_desc, ";
	$fields.="t1.long_desc, t1.idNews, t1.cancomment, COUNT(t3.key1) as com_cnt, t5.label, t5.image, t4.img_align, ";
	$fields.="t4.topic_id";

	$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_news as t1 ";
	$qtxt.="INNER JOIN ".$GLOBALS["prefix_cms"]."_area_block_items as t2 ON (t2.item_id=t1.idNews) ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_sysforum as t3 ON (t3.key1='news_comment' AND t3.key2=t1.idNews) ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_news_topic as t4 ON (t1.idNews=t4.idNews AND t4.main='1') ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_topic as t5 ON (t5.topic_id=t4.topic_id AND t5.language='$sel_lang') ";


	if (isset($_GET["op"]))
		$op=substr($_GET["op"], 0, 30);
	else
		$op="shownews";


	$qtxt.="WHERE t2.type='news' AND t2.idBlock='".$GLOBALS["pb"]."' AND t1.publish='1' AND ";


	if ((isset($option["show_search"])) && ($option["show_search"])) {
		$show_search=true;
	}
	else {
		$show_search=false;
	}

	if ($op == "listtopic") {
		$qtxt.="t4.topic_id='".(int)$_GET["topicid"]."' AND ";
		$show_search=true;
	}


	if ((isset($_POST["searchkey"])) && (trim($_POST["searchkey"]) != "")) {
		$searchkey=trim($_POST["searchkey"]);

		$qtxt.="(t1.title LIKE '%".$searchkey."%' OR ";
		$qtxt.="t1.short_desc LIKE '%".$searchkey."%' OR ";
		$qtxt.="t1.long_desc LIKE '%".$searchkey."%') ";
		$qtxt.=" AND ";

	}
	else
		$searchkey="";


	$qtxt.="t1.language = '$sel_lang' GROUP BY t2.item_id ";
	$qtxt.="ORDER BY t1.ord ASC, t1.publish_date DESC "; //echo $qtxt;

	$q=mysql_query($qtxt);
	if ($q)
		$tot=mysql_num_rows($q);

	$ini=(isset($_GET["sp"]) ? (int)$_GET["sp"]-1 : 0)*$option["number"];

	$qtxt.="LIMIT ".$ini.", ".$option['number'];

	return $qtxt;
}



function load_news_comments($id) {

	require_once($GLOBALS["where_framework"]."/lib/lib.sysforum.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=DoceboLanguage::createInstance("sysforum", "cms");

	$anonymous_comment=$GLOBALS["cms"]["anonymous_comment"];

	$force_comment=0;
	$can_comment=0;
	$can_post=(bool)(($GLOBALS['current_user']->isLoggedIn()) || ($anonymous_comment == "on"));
	$is_admin=isCmsAdmin();
	//print_r($_SESSION);

	$opt=loadBlockOption($GLOBALS["pb"]);
	if (isset($opt["ov_comments"]))
		$force_comment=(int)$opt["ov_comments"];
	else
		$force_comment=0;

	$can_comment=true;

	if (($can_comment) || ($force_comment)) {

		$out->add("<div class=\"commentsHead\">".$lang->def("_COMMENTS")."</div>\n");

		$sf=new sys_forum("cms", "news_comment", $id);
		$sf->setPrefix("cms");
		$sf->can_write=$can_post;
		$sf->can_moderate=$is_admin;
		//$sf->can_upload=$perm["can_upload"];

		$sf->url="index.php?mn=news&amp;pi=".getPI()."&amp;id=".$id; //$sf->get_page_url();
		$out->add($sf->show());
		//$out->add("<br />\n");

	}

}



function show_topic_img($label, $image, $img_align) {

	$show_topic_img=0;

	switch($img_align) {
		case "left": {
			$align="left";
			$show_topic_img=1;
		} break;
		case "right": {
			$align="right";
			$show_topic_img=1;
		} break;
		case "noimg": {
			$show_topic_img=0;
		} break;
	}

	if ($show_topic_img) {
		$GLOBALS["page"]->add("<img style=\"float: ".$align.";\" src=\""._TOPIC_FPATH.$image."\" ", "content");
		$GLOBALS["page"]->add("alt=\"".$label."\" title=\"".$label."\" />\n", "content");
	}

}


function getNewsAttach($news_id, $show_title=TRUE, $ul_class=FALSE) {
	$res="";

	$ul_class =($ul_class !== FALSE ? $ul_class : "attach_list");

	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	$lang=& DoceboLanguage::createInstance('news', 'cms');

	$attach_list=getAttachList($GLOBALS["prefix_cms"]."_news_attach", "idNews", $news_id);


	$has_docs=(((isset($attach_list["docs"])) && (is_array($attach_list["docs"])) &&
	            (count($attach_list["docs"]) > 0)) ? TRUE : FALSE);

	$has_media=(((isset($attach_list["media"])) && (is_array($attach_list["media"])) &&
	             (count($attach_list["media"]) > 0)) ? TRUE : FALSE);

	$has_links=(((isset($attach_list["links"])) && (is_array($attach_list["links"])) &&
	             (count($attach_list["links"]) > 0)) ? TRUE : FALSE);


	if (($has_docs) || ($has_media)) {
		$res.=($show_title ? "<b>".$lang->def("_NEWS_ATTACH_LIST")."</b>\n" : "");
		$res.="<ul class=\"".$ul_class."\">";

		if ($has_docs) {
			foreach($attach_list["docs"] as $attach_id=>$attach) {
				$title=(empty($attach["title"]) ? $attach["fname"] : $attach["title"]);
				$img ="<img src=\"".getPathImage().mimeDetect($attach["fname"])."\" ";
				$img.="alt=\"".$attach["fname"]."\" title=\"".$attach["fname"]."\" />\n";
				$url ="index.php?mn=news&amp;op=download&amp;pi=".getPI()."&amp;type=docs";
				$url.="&amp;news_id=".$news_id."&amp;attach_id=".$attach_id;
				$res.="<li>".$img." <a href=\"".$url."\">".$title."</a></li>\n";
			}
		}

		if ($has_media) {
			foreach($attach_list["media"] as $attach_id=>$attach) {
				$title=(empty($attach["title"]) ? $attach["fname"] : $attach["title"]);
				$img ="<img src=\"".getPathImage().mimeDetect($attach["fname"])."\" ";
				$img.="alt=\"".$attach["fname"]."\" title=\"".$attach["fname"]."\" />\n";
				$url ="index.php?mn=news&amp;op=download&amp;pi=".getPI()."&amp;type=media";
				$url.="&amp;news_id=".$news_id."&amp;attach_id=".$attach_id;
				$res.="<li>".$img." <a href=\"".$url."\">".$title."</a></li>\n";
			}
		}

		$res.="</ul>";
	}

	if ($has_links) {
		$res.="<b>".$lang->def("_NEWS_LINKS_LIST")."</b>\n";
		$res.="<ul class=\"attach_list\">";

		if ($has_links) {
			foreach($attach_list["links"] as $attach_id=>$attach) {
				$title=(empty($attach["title"]) ? substr($attach["url"], 0, 60) : $attach["title"]);
				$link=open_ext_link($attach["url"]).$title."</a>";
				$img ="<img src=\"".getPathImage()."block/links22.gif\" ";
				$img.="alt=\"".$title."\" title=\"".$title."\" />\n";
				$res.="<li>".$img." ".$link."</li>\n";
			}
		}

		$res.="</ul>";
	}

	return $res;
}


function downloadNewsAttachment() {

	$news_id=(isset($_GET["news_id"]) ? (int)$_GET["news_id"] : 0);
	$attach_id=(isset($_GET["attach_id"]) ? (int)$_GET["attach_id"] : 0);
	$type=(isset($_GET["type"]) ? $_GET["type"] : "");

	if (($news_id == 0) || ($attach_id == 0) || (empty($type)))
		return "";

	downloadModuleAttachment($GLOBALS["prefix_cms"]."_news_attach", "idNews", $news_id, $attach_id, $type);
}


function addNewsFeed($rss_arr) {
	require_once($GLOBALS["where_framework"]."/lib/lib.rss.php");

	//TODO:
	//make sure that the anonymous user can view the block (pb)

	$pb=$GLOBALS["pb"];
	if ($pb <= 0)
		return FALSE;

	$b_info=getBlockInfo($pb);
	$title=$b_info["title"];

	$fg=new FeedGenerator("block_news", $pb, "cms");

	$res=$fg->generateFeed($title, $rss_arr, false, true);
	return $res;
}

?>