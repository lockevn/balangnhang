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




function showSubmitNews() {

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/topic/functions.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/news/functions.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('submitnews', 'cms');

	$form=new Form();

	$out->add($form->openForm("news_form", "index.php?mn=submitnews&amp;op=insnews&amp;pi=".getPI()));

	$out->add($form->openElementSpace());


	$out->add($form->getTextarea($lang->def("_SHORTDESC"), "sdesc", "sdesc", ""));
	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "ldesc", "ldesc", ""));

	$out->add($form->getTextfield($lang->def("_TITLE"), "title", "title", 255, ""));
	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255, ""));


	$out->add($form->getTextfield($lang->def("_REFLINK"), "reflink", "reflink", 255, "http://"));
	$out->add($form->getTextfield($lang->def("_SOURCE"), "source", "source", 255, ""));
	$out->add($form->getTextfield($lang->def("_LOCATION"), "location", "location", 255, ""));

	$langArray=array();
	$tmp_array=$GLOBALS['globLangManager']->getAllLangCode();
	foreach($tmp_array as $key=>$val) {
		$langArray[$val]=$val;
	}
	$out->add($form->getDropdown($lang->def("_LANGUAGE"), "lang", "language", $langArray, getLanguage()));


	$out->add(ts_select_main_topic("maintopic", $lang->def("_MAINTOPIC"), false, $form));

/*	echo($form->getOpenFieldset(_NEWS_TOPIC));

	echo(ts_select_main_topic("maintopic", _MAINTOPIC, false, $form));

	echo(ts_select_topic_img_align("topicimgalign", _TOPIC_IMG_ALIGN, false, $form));

	echo(ts_select_related_topic("relatedtopic", _RELATEDTOPIC, false, $form));


	echo($form->getCloseFieldset()); */


	$out->add($form->getHidden("idFolder", "idFolder", 0));

	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SEND")));
	//echo($form->getButton('undo', 'undo', _UNDO));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

}



// more or less a duplicate of the one you can find in
// /admin/modules/news/functions.php
function ts_select_main_topic($name, $title, $sel, & $form) {

	$res="";
	$sel_lang=getLanguage();

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE language='$sel_lang' ORDER BY label";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {

		$arr=array();
		while($row=mysql_fetch_array($q)) {
			$arr[$row["topic_id"]]=$row["label"];
		}

		$res.=$form->getDropdown($title, $name, $name, $arr, $sel);
	}

	return $res;
}




function saveNews() {

	if($_POST['title'] == '')
		$_POST['title'] = _NOTITLE;

	$reflink=($_POST['reflink'] == "http://" ? "" : $_POST['reflink']);

	$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_news ";
	$qtxt.="SET idFolder = '".(int)$_POST['idFolder']."', publish_date = NOW(), ";
	$qtxt.="title = '".$_POST['title']."',reflink = '".$reflink."', source = '".$_POST['source']."', ";
	$qtxt.="location = '".$_POST['location']."', author = '".$_POST['author']."', short_desc = '".$_POST['sdesc']."', ";
	$qtxt.="long_desc = '".$_POST['ldesc']."', language = '".$_POST['language']."', ";
	$qtxt.="usercontrib='1', ord = '1'";

	if(!mysql_query($qtxt)) {
		return;
	}

	list($idNews)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID() FROM ".$GLOBALS["prefix_cms"]."_news"));

	if ($idNews > 0) { // Save main topic info

		$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_news_topic (idNews, topic_id, main, img_align) ";
		$qtxt.="VALUES ('".$idNews."', '".(int)$_POST['maintopic']."', '1', 'noimage')";

		$q=mysql_query($qtxt);
	}

	require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
	fix_item_order($GLOBALS["prefix_cms"]."_news", "idNews", (int)$_POST['idFolder']);

	jumpTo('index.php?mn=submitnews&op=complete&pi='.getPI());
}


?>