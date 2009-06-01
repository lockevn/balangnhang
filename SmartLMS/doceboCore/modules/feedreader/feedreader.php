<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package  DoceboCore
 * @version  $Id: feedreader.php 446 2006-06-17 07:23:27Z fabio $
 */

require_once($GLOBALS['where_framework']."/lib/lib.rss.php");

function feedreaderMain() {
	checkPerm('view');
	
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('feedmanager', "framework");

	$frManager=new FeedReaderManager();

	$res="";

	$vis_item=$GLOBALS["framework"]["visuItem"];

	$tab=new typeOne($vis_item);

	$url="index.php?modname=feedreader&amp;";


	$img ="<img src=\"".getPathImage()."emoticons/background.gif\" alt=\"".$lang->def("_IMAGE")."\" ";
	$img.="title=\"".$lang->def("_IMAGE")."\" />";

	$head=array($img, $lang->def("_TITLE"), $lang->def("_URL"), $lang->def("_REFRESH_TIME"));
	$img ="<img src=\"".getPathImage()."standard/down.gif\" alt=\"".$lang->def("_MOVE_DOWN")."\" ";
	$img.="title=\"".$lang->def("_MOVE_DOWN")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage()."standard/up.gif\" alt=\"".$lang->def("_MOVE_UP")."\" ";
	$img.="title=\"".$lang->def("_MOVE_UP")."\" />";
	$head[]=$img;

	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array('image', '', '', '', 'image', 'image', 'image', 'image');


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php?modname=feedreader&amp;op=feedreader");

	$ini=$tab->getSelectedElement();


	$data_info=$frManager->getAllFeeds($ini, $vis_item);
	$feed_arr=$data_info["feed_arr"];
	$db_tot=$data_info["feed_tot"];

	$tot=count($feed_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$feed_arr[$i]["feed_id"];

		$rowcnt=array();
		if (!empty($feed_arr[$i]["image"])) {
			$img_name=end(explode("/", $feed_arr[$i]["image"]));
			$img ="<img class=\"feed_image\" src=\"".$feed_arr[$i]["image"]."\" ";
			$img.="alt=\"".$img_name."\" title=\"".$img_name."\" />";
			$rowcnt[]=$img;
		}
		else {
			$rowcnt[]="&nbsp;";
		}
		$rowcnt[]=$feed_arr[$i]["title"];
		$rowcnt[]=substr(htmlspecialchars($feed_arr[$i]["url"]), 0, 80);

		$frAdmin=new FeedReaderAdmin();
		$refresh_time_arr=$frAdmin->getRefreshTimeArr($lang);
		$rowcnt[]=$refresh_time_arr[$feed_arr[$i]["refresh_time"]];

		if ($ini+$i < $db_tot-1) {
			$img ="<img src=\"".getPathImage()."standard/down.gif\" alt=\"".$lang->def("_MOVE_DOWN")."\" ";
			$img.="title=\"".$lang->def("_MOVE_DOWN")."\" />";
			$rowcnt[]="<a href=\"".$url."op=movedown&amp;id=".$id."\">".$img."</a>";
		}
		else
			$rowcnt[]="&nbsp;";

		if ($ini+$i > 0) {
			$img ="<img src=\"".getPathImage()."standard/up.gif\" alt=\"".$lang->def("_MOVE_UP")."\" ";
			$img.="title=\"".$lang->def("_MOVE_UP")."\" />";
			$rowcnt[]="<a href=\"".$url."op=moveup&amp;id=".$id."\">".$img."</a>";
		}
		else
			$rowcnt[]="&nbsp;";


		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."op=edit&amp;id=".$id."\">".$img."</a>";
		$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
		$img.="title=\"".$lang->def("_DEL")."\" />";
		$rowcnt[]="<a href=\"".$url."op=del&amp;id=".$id."\">".$img."</a>";

		$tab->addBody($rowcnt);

	}

	$url="index.php?modname=feedreader&amp;op=add";
	$img ="<img src=\"".getPathImage()."standard/add.gif\" alt=\"".$lang->def('_ADD')."\" />";
	$tab->addActionAdd("<a href=\"".$url."\">".$img.$lang->def('_ADD')."</a>\n");

	$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	// ----------------------------------------------------------------------
	$out->add(getTitleArea($lang->def("_FEEDREADER"), "feedreader"));
	$out->add("<div class=\"std_block\">\n");
	$out->add($res);
	$out->add("</div>\n");
}

function addeditFeed() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS["where_framework"]."/lib/lib.platform.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('feedmanager', "framework");

	$form=new Form();
	$res="";

	$feed_id=(int)importVar("id");

	$url="index.php?modname=feedreader";

	if ($feed_id == 0) {
		$res.=$form->openForm("feedreader_form", $url."&amp;op=insnew");
		$submit_lbl=$lang->def("_INSERT");

		$title="";
		$url="";
		$refresh_time=240;
		$show_on_platform=array();
	}
	else if ($feed_id > 0) {
		$res.=$form->openForm("feedreader_form", $url."&amp;op=save");
		$submit_lbl=$lang->def("_MOD");

		$frManager=new FeedReaderManager();
		$feed_info=$frManager->getFeedInfo($feed_id);

		$title=$feed_info["title"];
		$url=$feed_info["url"];
		$refresh_time=$feed_info["refresh_time"];
		$show_on_platform=explode(",", $feed_info["show_on_platform"]);
	}

	$res.=$form->openElementSpace();

	$res.=$form->getTextfield($lang->def("_TITLE"), "title", "title", 255, $title);
	$res.=$form->getTextfield($lang->def("_URL"), "url", "url", 255, $url);
	$res.=$form->getHidden("old_url", "old_url", $url);

	$frAdmin=new FeedReaderAdmin();
	$refresh_time_arr=$frAdmin->getRefreshTimeArr($lang);

	$res.=$form->getDropdown($lang->def("_REFRESH_TIME"), "refresh_time", "refresh_time", $refresh_time_arr , $refresh_time);

	$res.=$form->getOpenFieldset($lang->def("_SHOW_ON_PLATFORM"));

	$pManager=new PlatformManager();
	$platforms=$pManager->getPlatformList();

	foreach ($platforms as $platform_code=>$platform_title) {
		$name="show_on_platform[".$platform_code."]";
		$id="show_on_platform_".$platform_code;
		$checked=(in_array($platform_code, $show_on_platform) ? true : false);
		$res.=$form->getCheckbox($platform_title, $id, $name, $platform_code, $checked);
	}

	$res.=$form->getCloseFieldset();

	$res.=$form->getHidden("feed_id", "feed_id", $feed_id);

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	// ----------------------------------------------------------------------
	$out->add(getTitleArea($lang->def("_FEEDREADER"), "feedreader"));
	$out->add("<div class=\"std_block\">\n");
	$out->add($res);
	$out->add("</div>\n");
}

function saveFeed() {
	checkPerm('view');

	$fr_manager=new FeedReaderManager();

	$fr_manager->saveFeed($_POST);
	jumpTo("index.php?modname=feedreader&op=feedreader");
}

function delFeed() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('feedmanager', "framework");

	$form=new Form();
	$frManager=new FeedReaderManager();
	$res="";

	$feed_id=(int)importVar("id");


	$back_url="index.php?modname=feedreader&op=feedreader";

	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$frManager->deleteFeed((int)$_POST["feed_id"]);

		jumpTo($back_url);
	}
	else {

		$feed_info=$frManager->getFeedInfo($feed_id);

		$title=$feed_info["title"];

		$form=new Form();

		$url="index.php?modname=feedreader&amp;op=del&amp;id=".$feed_id;
		$res.=$form->openForm("feedreader_form", $url);

		$res.=$form->getHidden("feed_id", "feed_id", $feed_id);

		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'undo');

		// ----------------------------------------------------------------------
		$out->add(getTitleArea($lang->def("_FEEDREADER"), "feedreader"));
		$out->add("<div class=\"std_block\">\n");
		$out->add($res);
		$out->add("</div>\n");
	}
}

// ----------------------------------------------------------------------------

function feedreaderDispatch( $op ) {

	if (isset($_POST["undo"]))
		$op="feedreader";

	switch($op) {
		case "feedreader": {
			feedreaderMain();
		} break;

		case "add": {
			addeditFeed();
		} break;

		case "insnew": {
			saveFeed();
		} break;

		case "edit": {
			addeditFeed();
		} break;

		case "save": {
			saveFeed();
		} break;

		case "del": {
			delFeed();
		} break;

	}
}

?>