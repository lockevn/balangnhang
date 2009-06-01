<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");

$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_manpage.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_organizations.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
 $GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style-admin.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');


function news() {
	//funAdminAccess('OP');
	global $visuItem;

	require_once( $GLOBALS['where_cms'].'/admin/modules/news/news_class.php' );

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	$out->setWorkingZone('content');

	$treeView = createTreeView();

	switch ( news_getOp( $treeView ) ) {
		case "newitem" : {
			addnews( $treeView );
		};break;
		case "modnews" : {
			//print_r($treeView); //??
			modnews( $treeView );
		};break;
		case "delnews" : {
			delnews( $treeView );
		};break;

		case 'newfolder': {
			$out->add('<form method="post" action="index.php?modname=news&amp;op=news" >'."\n");
			news_addfolder( $treeView);
			$out->add('</form>');
		};break;
		case 'renamefolder' : {
			$out->add('<form method="post" action="index.php?modname=news&amp;op=news" >'."\n");
			news_renamefolder($treeView);
			$out->add('</form>');
		};break;
		case 'movefolder' :  {
			$out->add('<form method="post" action="index.php?modname=news&amp;op=news" >'."\n");
			news_move_folder($treeView);
			$out->add('</form>');
		};break;
		case 'news_move_form' :  {
			$out->add('<form method="post" action="index.php?modname=news&amp;op=news" >'."\n");
			news_move_form($treeView);
			$out->add('</form>');
		};break;
		case 'deletefolder' : {
			$out->add('<form method="post" action="index.php?modname=news&amp;op=news" >'."\n");
			news_deletefolder($treeView);
			$out->add('</form>');
		};break;
		case 'display':
		default: {
			$out->add(getTitleArea($lang->def("_NEWS"), "news"));
			$out->add('<form method="post" action="index.php?modname=news&amp;op=news" >'."\n"
				.'<div class="std_block">');

			$listView = $treeView->getListView();

			$user_level =$GLOBALS["current_user"]->getUserLevelId(); //&
			if ($user_level != ADMIN_GROUP_GODADMIN) {
				$treeView->setUseAdminFilter(TRUE);
				$listView->setUseAdminFilter(TRUE);
			}

			$treeView->showbtn=1;
			$out->add($treeView->load());

			$folder_id =$treeView->getSelectedFolderId();
			if (news_checkTreePerm($folder_id, TRUE)) { //&
				$out->add($treeView->loadActions());
				$listView->setInsNew( checkPerm('add', true) );
			}
			$out->add($listView->printOut());

			$out->add('</div>'
				.'</form>');
		};break;
	}

}

function addnews(& $treeView) {
	checkPerm('add');
 	$visuItem=$GLOBALS["visuItem"];
	include_once($GLOBALS["where_cms"]."/admin/modules/news/functions.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$folder_id =(int)$treeView->getSelectedFolderId(); //&
	news_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$back_ui_url="index.php?modname=news&amp;op=news";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_NEWS");
	$title_arr[]=$lang->def("_ADD_NEWS");
	$out->add(getTitleArea($title_arr, "news"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("news_form", "index.php?modname=news&amp;op=insnews"));

	$out->add($form->openElementSpace());

	$treeView->printState();

	$out->add($form->getTextarea($lang->def("_SHORTDESC"), "sdesc", "sdesc"));
	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "ldesc", "ldesc"));

	$out->add($form->getTextfield($lang->def("_TITLE"), "title", "title", 255));
	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255));

	$out->add($form->getTextfield($lang->def("_REFLINK"), "reflink", "reflink", 255));
	$out->add($form->getTextfield($lang->def("_SOURCE"), "source", "source", 255));
	$out->add($form->getTextfield($lang->def("_LOCATION"), "location", "location", 255));

	$langArray=array();
	$tmp_array=$GLOBALS['globLangManager']->getAllLangCode();
	foreach($tmp_array as $key=>$val) {
		$langArray[$val]=$val;
	}
	$out->add($form->getDropdown($lang->def("_LANGUAGE"), "lang", "language", $langArray, getLanguage()));

	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
	$out->add(show_pubexp_table($form, $lang, 0, 0));

	$out->add($form->getHidden("important", "important", 0));
	$out->add($form->getHidden("cancomment", "cancomment", 1));


	$out->add($form->getOpenFieldset($lang->def("_NEWS_TOPIC")));

	select_main_topic("maintopic", $lang->def("_MAINTOPIC"), false, $form);

	select_topic_img_align("topicimgalign", $lang->def("_TOPIC_IMG_ALIGN"), false, $form);

	select_related_topic("relatedtopic", $lang->def("_RELATEDTOPIC"), false, $form);

	$out->add($form->getCloseFieldset());

	$out->add($form->getHidden("idFolder", "idFolder", $folder_id)); //&

	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_INSERT")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function insnews() {
	checkPerm('add');
	include_once($GLOBALS["where_cms"]."/admin/modules/news/functions.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$folder_id =(int)$_POST['idFolder']; //&
	news_checkTreePerm($folder_id);

	if($_POST['title'] == '') $_POST['title'] = def('_NOTITLE', 'admin_news');

	$arr=get_pubexp_info();
	$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
	$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

	if(!mysql_query("
	INSERT INTO ".$GLOBALS["prefix_cms"]."_news
	SET idFolder = '".(int)$_POST['idFolder']."',
		publish_date = NOW(),
		title = '".$_POST['title']."',
		reflink = '".$_POST['reflink']."',
		source = '".$_POST['source']."',
		location = '".$_POST['location']."',
		author = '".$_POST['author']."',
		short_desc = '".$_POST['sdesc']."',
		long_desc = '".trim($_POST['ldesc'])."',
		language = '".$_POST['language']."',
		pubdate = ".$pubdate.",
		expdate = ".$expdate.",
		important = '".(int)$_POST['important']."',
		cancomment = '".$_POST['cancomment']."',
		ord = '1'")) {
		errorCommunication(_INSERR);
		return;
	}
	else {
		list($idNews)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID() FROM ".$GLOBALS["prefix_cms"]."_news"));

		require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
		$replace=array("[title]"=>$_POST['title']);
		sendCmsGenericEvent(
				/* members:    */ FALSE,
				/* class:      */ "NewsCreated",
				/* module:     */ "admin_news",
				/* action:     */ "add",
				/* log:        */ "Added news ".$idNews,
				/* sub_string: */ "_NEWS_ADDED_ALERT_SUB",
				/* txt_string: */ "_NEWS_ADDED_ALERT_TXT",
				/* replace:    */ $replace
			);

		save_topic_info($idNews, "maintopic", "topicimgalign", "relatedtopic");
	}

	require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
	fix_item_order($GLOBALS["prefix_cms"]."_news", "idNews", (int)$_POST['idFolder']);

	Header('Location:index.php?modname=news&op=news');
}


//---------------------------------------------------------------------

function modnews(& $treeView) {
	checkPerm('mod');
 global $visuItem;

	include_once($GLOBALS["where_cms"]."/admin/modules/news/functions.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');
	if(isset($treeView))	{
		$idNews = (int)$treeView->getNewsSelected();
		$idFolder = (int)$treeView->getSelectedFolderId(); 
	} else	{
		$idNews = importVar('id_news',TRUE,0);
		$idFolder=importVar('id_folder',TRUE,0);
	}	
	news_checkTreePerm($idFolder);

	//load info
	$textQuery = "
	SELECT title, author, reflink, source, location, short_desc, long_desc, language, important, cancomment, pubdate, expdate
	FROM ".$GLOBALS["prefix_cms"]."_news
	WHERE idNews  = '$idNews'";

	list($title, $author, $reflink, $source, $location, $short_desc, $long_desc, $news_lang, $important, $cancomment, $pubdate, $expdate) = mysql_fetch_row(mysql_query($textQuery));

	$back_ui_url="index.php?modname=news&amp;op=news";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_NEWS");
	$title_arr[]=$lang->def("_EDIT_NEWS").": ".$title;
	$out->add(getTitleArea($title_arr, "news"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("news_form", "index.php?modname=news&amp;op=upnews"));

	$out->add($form->openElementSpace());

	if(isset($treeView)) $treeView->printState();

	$out->add($form->getTextarea($lang->def("_SHORTDESC"), "sdesc", "sdesc", $short_desc));
	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "ldesc", "ldesc", $long_desc));

	$out->add($form->getTextfield($lang->def("_TITLE"), "title", "title", 255, $title));
	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255, $author));

	$out->add($form->getTextfield($lang->def("_REFLINK"), "reflink", "reflink", 255, $reflink));
	$out->add($form->getTextfield($lang->def("_SOURCE"), "source", "source", 255, $source));
	$out->add($form->getTextfield($lang->def("_LOCATION"), "location", "location", 255, $location));

	$langArray=array();
	$tmp_array=$GLOBALS['globLangManager']->getAllLangCode();
	foreach($tmp_array as $key=>$val) {
		$langArray[$val]=$val;
	}
	$out->add($form->getDropdown($lang->def("_LANGUAGE"), "lang", "language", $langArray, $news_lang));


	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add($form->getHidden("important", "important", 0));
	$out->add($form->getHidden("cancomment", "cancomment", 1));

	$out->add($form->getOpenFieldset($lang->def("_NEWS_TOPIC")));

	$maintopic_info=get_maintopic_info($idNews);
	$related_topics=get_related_topics($idNews);

	select_main_topic("maintopic", $lang->def("_MAINTOPIC"), $maintopic_info["topic_id"], $form);

	select_topic_img_align("topicimgalign", $lang->def("_TOPIC_IMG_ALIGN"), $maintopic_info["img_align"], $form);

	select_related_topic("relatedtopic", $lang->def("_RELATEDTOPIC"), $related_topics, $form);


	$out->add($form->getCloseFieldset());


	$out->add($form->getHidden("idNews", "idNews", $idNews));
	$out->add($form->getHidden("idFolder", "idFolder", $idFolder)); //&

	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());


	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function upnews() {
	checkPerm('mod');
 	$visuItem=$GLOBALS["visuItem"];
 	//require_once($GLOBALS["where_cms"]."/lib/manDateTime.php");
	include_once($GLOBALS["where_cms"]."/admin/modules/news/functions.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$folder_id =(int)$_POST['idFolder']; //&
	news_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');
	$out->setWorkingZone("content");

	if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true; //check_period($ts_pub, $ts_exp);

	if ($period_ok) {

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		if(!mysql_query("
		UPDATE ".$GLOBALS["prefix_cms"]."_news
		SET title = '".$_POST['title']."',
			reflink = '".$_POST['reflink']."',
			source = '".$_POST['source']."',
			location = '".$_POST['location']."',
			author = '".$_POST['author']."',
			short_desc = '".$_POST['sdesc']."',
			long_desc = '".trim($_POST['ldesc'])."',
			language = '".$_POST['language']."',
			important = '".$_POST['important']."',
			cancomment = '".$_POST['cancomment']."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate."
	  	WHERE idNews = '".$_POST['idNews']."'")) {
			errorCommunication($lang->def("_INSERR"));
			return;
		}
		else {
			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[title]"=>$_POST['title']);
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "NewsModified",
					/* module:     */ "admin_news",
					/* action:     */ "edit",
					/* log:        */ "Edited news ".$_POST['idNews'],
					/* sub_string: */ "_NEWS_EDITED_ALERT_SUB",
					/* txt_string: */ "_NEWS_EDITED_ALERT_TXT",
					/* replace:    */ $replace
				);
		}

		save_topic_info($_POST['idNews'], "maintopic", "topicimgalign", "relatedtopic");

		jumpTo('index.php?modname=news&op=news');
	}
	else {
		$out->add("<div class=\"std_block\">\n");
		$out->add("<b>".$lang->def("_INSERR")."</b><br /><br />");
		$out->add("<a href=\"javascript:history.go(-1);\">&lt;&lt; ".$lang->def("_BACK")."</a>\n");
		$out->add("</div>\n");
	}
}

//----------------------------------------------------------------------------

function delnews( $treeView=FALSE ) {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	if ($treeView !== FALSE) { //&
		$folder_id = (int)$treeView->getSelectedFolderId();
	}
	else if (isset($_POST["folder_id"])) {
		$folder_id=(int)$_POST["folder_id"];
	}
	else {
		$folder_id =0;
	}

	news_checkTreePerm($folder_id); //&

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=news&op=news");
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_news_attach WHERE idNews='".$id."';");
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_news_topic WHERE idNews='".$id."';");

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_news WHERE idNews='".$id."';");

		// ---------- Fixing order:
		require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
		fix_item_order($GLOBALS["prefix_cms"]."_news", "idNews", $folder_id);
		// ------------------------

		jumpTo("index.php?modname=news&op=news");
	}
	else {

		//load info
		$id=(int)$treeView->getNewsSelected();
		list($title) = mysql_fetch_row(mysql_query("
		SELECT title
		FROM ".$GLOBALS["prefix_cms"]."_news
		WHERE idNews  = '".$id."'"));


		$back_ui_url="index.php?modname=news&amp;op=news";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_NEWS");
		$title_arr[]=$lang->def("_DELETE_NEWS").": ".$title;
		$out->add(getTitleArea($title_arr, "news"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("news_form", "index.php?modname=news&amp;op=delnews"));

		$out->add($form->getHidden("id", "id", $id));
		$out->add($form->getHidden("folder_id", "folder_id", $folder_id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

	return 0;

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	if( $_GET['confirm'] == 1 ) {

		if(!mysql_query("
		DELETE FROM ".$GLOBALS["prefix_cms"]."_news
		WHERE idNews  = '".(int)$_GET['idNews']."'")) {
			$out->add(getTitleArea($lang->def("_NEWS"), "news"));
			errorCommunication(_ERRREM);
			return;
		}

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_news_attach WHERE idNews='".(int)$_GET['idNews']."';");
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_news_topic WHERE idNews='".(int)$_GET['idNews']."';");

		// ---------- Fixing order:
		require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
		fix_item_order($GLOBALS["prefix_cms"]."_news", "idNews", (int)$_GET["idFolder"]);
		// ------------------------


		Header('Location:index.php?modname=news&op=news');

	}
	else {

		$idFolder=(int)$treeView->selectedFolder;

		//load info
		list($title, $short_desc) = mysql_fetch_row(mysql_query("
		SELECT title, short_desc
		FROM ".$GLOBALS["prefix_cms"]."_news
		WHERE idNews  = '".(int)$treeView->getNewsSelected()."'"));

		echo '<div class="std_block">';

			echo '<b>'._AREYOUSURENEWS.'</b><br />'
				.'<div class="evidenceBlock">'
				.'<b>'._TITLE.' :</b> &quot;'.$title.'&quot;<br />'
				.'<b>'._SHORTDESC.' :</b> '.$short_desc.'<br /><br />'
				.'[ <a href="index.php?modname=news&amp;op=delnews&amp;idNews='
				.(int)$treeView->getNewsSelected().'&amp;confirm=1&amp;idFolder='.$idFolder.'">'._YES.'</a> | '
				.'<a href="index.php?modname=news&amp;op=news">'._NO.'</a> ]'
				.'</div>';

		echo '</div>';
	}

}


function attachnews($id) {
	checkPerm('mod');
	global $visuItem;

	news_checkTreePerm(FALSE, FALSE, $id); //&

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');


	$sel_lang=get_news_lang($id);

	if (isset($_GET["add"]))
		$add=$_GET["add"];
	else
		$add="";
	$show_add=0;


	switch ($add) {

		case "docs" : {
			$t1=$GLOBALS["prefix_cms"]."_docs";
			$t2=$GLOBALS["prefix_cms"]."_docs_info";
			$qtxt="SELECT idDocs, fname, real_fname, sdesc "
					." FROM $t1"
					." LEFT JOIN $t2 ON ($t2.idd=$t1.idDocs AND $t2.lang='$sel_lang')";

			define("_MA_TABLE", "docs");
			define("_MA_TABLE2", _MA_TABLE."_info");
			define("_MA_ADD", "docs");
			define("_MA_ID", "idDocs");
			define("_MA_SFT", "1"); // Show File type (icon)
			define("_MA_FIELD1", "fname");
			define("_MA_FIELD2", "sdesc");
			define("_MA_TITLE1", $lang->def("_TITLE"));
			define("_MA_TITLE2", $lang->def("_SHORTDESC"));
			define("_SELMOD", "news");
			define("_BACKID", $id);
			define("_MA_QUERY", $qtxt);
			$show_add=1;
		};break;

		case "media" : {
			$t1=$GLOBALS["prefix_cms"]."_media";
			$t2=$GLOBALS["prefix_cms"]."_media_info";
			$qtxt="SELECT idMedia, fname, real_fname, sdesc "
					." FROM $t1"
					." LEFT JOIN $t2 ON ($t2.idm=$t1.idMedia AND $t2.lang='$sel_lang')";

			define("_MA_TABLE", "media");
			define("_MA_TABLE2", _MA_TABLE."_info");
			define("_MA_ADD", "media");
			define("_MA_ID", "idMedia");
			define("_MA_SFT", "1"); // Show File type (icon)
			define("_MA_FIELD1", "fname");
			define("_MA_FIELD2", "sdesc");
			define("_MA_TITLE1", $lang->def("_TITLE"));
			define("_MA_TITLE2", $lang->def("_SHORTDESC"));
			define("_SELMOD", "news");
			define("_BACKID", $id);
			define("_MA_QUERY", $qtxt);
			$show_add=1;
		};break;

		case "links" : {
			$t1=$GLOBALS["prefix_cms"]."_links";
			$t2=$GLOBALS["prefix_cms"]."_links_info";
			$qtxt="SELECT idLinks, title, url, sdesc "
					." FROM $t1"
					." LEFT JOIN $t2 ON ($t2.idl=$t1.idLinks AND $t2.lang='$sel_lang')";

			define("_MA_TABLE", "links");
			define("_MA_TABLE2", _MA_TABLE."_info");
			define("_MA_TAB2NOTNULL", "title");
			define("_MA_ADD", "links");
			define("_MA_ID", "idLinks");
			define("_MA_SFT", "0"); // Show File type (icon)
			define("_MA_FIELD1", "title");
			define("_MA_FIELD2", "url");
			define("_MA_TITLE1", $lang->def("_TITLE"));
			define("_MA_TITLE2", $lang->def("_URL"));
			define("_SELMOD", "news");
			define("_BACKID", $id);
			define("_MA_QUERY", $qtxt);
			$show_add=1;
		};break;

	}


	$back_ui_url="index.php?modname=news&amp;op=news";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_NEWS");
	$title_arr[]=$lang->def("_NEWS_ATTACHMENT");
	$out->add(getTitleArea($title_arr, "news"));
	$out->add("<div class=\"std_block\">\n");

	if ($show_add) {
		include_once($GLOBALS["where_cms"]."/admin/modules/manattach/manattach.php");

		$back_ui_url="index.php?modname=news&amp;op=manattach&id=".$id;
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		manattach();

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	}
	else {
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
		show_attachments($id);
	}
	$out->add("</div>\n");

}



function check_period_old(&$ts_pub, &$ts_exp) {
 require_once($GLOBALS["where_cms"]."/lib/manDateTime.php");

	$period_ok=1;
	if (($_POST["use_pub_time"]) || ($_POST["use_exp_time"])) {
		$date_pub=$_POST["date_pub"];
		$time_pub=$_POST["hour_pub"].":".$_POST["min_pub"];
		$date_exp=$_POST["date_exp"];
		$time_exp=$_POST["hour_exp"].":".$_POST["min_exp"];
		if ($_POST["use_pub_time"])
			$ts_pub=get_timestamp($date_pub, $time_pub);
		if ($_POST["use_exp_time"])
			$ts_exp=get_timestamp($date_exp, $time_exp);

		if (($_POST["use_pub_time"]) && ($_POST["use_exp_time"]) && ($ts_pub>=$ts_exp)) $period_ok=0;
	}

	return $period_ok;
}


function show_attachments($id) {

	checkPerm('mod'); //&
	news_checkTreePerm(FALSE, FALSE, $id); //&

 	$out=& $GLOBALS["page"];
 	$lang=& DoceboLanguage::createInstance("admin_news", "cms");

	if ((isset($_GET["act_op"])) && ($_GET["act_op"] == "delattach")) {
		del_attach();
	}

	if ((isset($_GET["act_op"])) && ($_GET["act_op"] == "attachitem")) {
		$aid=$_GET["add_id"];
		$type=$_GET["type"];
		$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_news_attach (idNews, idAttach, type) VALUES ('$id', '$aid', '$type')";
		mysql_query($qtxt);
	}

	$out->add("<b>".$lang->def("_ATTACH_DOCS")."</b>\n");
	show_attach_table("docs", $id);

	$out->add("<b>".$lang->def("_ATTACH_MEDIA")."</b>\n");
	show_attach_table("media", $id);

	$out->add("<b>".$lang->def("_ATTACH_LINKS")."</b>\n");
	show_attach_table("links", $id);

	$back_ui_url="index.php?modname=news&amp;op=news";
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
}

function show_attach_table($type, $id) {
 global $visuItem;
	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');

	checkPerm('mod'); //&
	news_checkTreePerm(FALSE, FALSE, $id); //&


	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	$sel_lang=get_news_lang($id);
	$table = new typeOne(0);
	$out->add($table->OpenTable(""));

	switch ($type) {

		case "docs" : {
			$t1=$GLOBALS["prefix_cms"]."_docs";
			$t2=$GLOBALS["prefix_cms"]."_docs_info";
			$t3=$GLOBALS["prefix_cms"]."_news_attach";
			$row_id="idDocs";
			$qtxt="SELECT $t1.*, $t2.sdesc, $t3.* "
					." FROM $t1 INNER JOIN $t3 ON ($t3.idAttach=$t1.$row_id)"
					." LEFT JOIN $t2 ON ($t2.idd=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idNews='$id'";

			$head = array($lang->def("_TYPE"), $lang->def("_FILENAME"), $lang->def("_SHORTDESC"),
				'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
			$head_type = array('img', '', '', 'img');

			$row_1="fname";
			$row_2="sdesc";
			$row_3="real_fname";

			$out->add($table->WriteHeader($head, $head_type));
		};break;


		case "media" : {
			$t1=$GLOBALS["prefix_cms"]."_media";
			$t2=$GLOBALS["prefix_cms"]."_media_info";
			$t3=$GLOBALS["prefix_cms"]."_news_attach";
			$row_id="idMedia";
			$qtxt="SELECT $t1.*, $t2.sdesc, $t3.* "
					." FROM $t1 INNER JOIN $t3 ON ($t3.idAttach=$t1.$row_id)"
					." LEFT JOIN $t2 ON ($t2.idm=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idNews='$id'";

			$head = array($lang->def("_TYPE"), $lang->def("_FILENAME"), $lang->def("_SHORTDESC"),
				'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
			$head_type = array('img', '', '', 'img');

			$row_1="fname";
			$row_2="sdesc";
			$row_3="real_fname";

			$out->add($table->WriteHeader($head, $head_type));
		};break;

		case "links" : {
			$t1=$GLOBALS["prefix_cms"]."_links";
			$t2=$GLOBALS["prefix_cms"]."_links_info";
			$t3=$GLOBALS["prefix_cms"]."_news_attach";
			$row_id="idLinks";
			$qtxt="SELECT $t1.*, $t2.title, $t3.* "
					." FROM $t1 INNER JOIN $t3 ON ($t3.idAttach=$t1.$row_id)"
					." LEFT JOIN $t2 ON ($t2.idl=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idNews='$id'";

			$head = array($lang->def("_TITLE"), $lang->def("_URL"),
				'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
			$head_type = array('', '', 'img');

			$row_1="title";
			$row_2="url";

			$out->add($table->WriteHeader($head, $head_type));
		};break;

	}


	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$rowcnt=array();
			if (($type == "docs") || ($type == "media")) {
				$fn=$row[$row_3];
				$expFileName = explode('.', $fn);
				$totPart = count($expFileName) - 1;
				$mime=mimetype($expFileName[$totPart]);
				$img="<img src=\"".getPathImage().mimeDetect($fn)."\" alt=\"$mime\" title=\"$mime\" />\n";

				$rowcnt[]=$img;
			}

			$rowcnt[]=$row[$row_1];
			$rowcnt[]=$row[$row_2];

			//$back="&amp;idref=$idref&amp;op=editvalcatitems";

			$aid="&amp;aid=".$row["id"];
			$rowcnt[]=
				"<a href=\"index.php?modname=news&amp;id=$id&amp;op=delattach$aid\">".
				"<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" /></a>";

			$out->add($table->WriteRow($rowcnt));
		}
	}

	$out->add($table->WriteAddRow('<a href="index.php?modname=news&amp;op=manattach&amp;id='.$id.'&amp;add='.$type.'" title="'.$lang->def("_ADD").'">'
									.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def("_ADD").'" /> '.$lang->def("_ADD").'</a>'));

	$out->add($table->CloseTable());

	$out->add("<br /><br />\n");
}


function get_news_lang($id) {
 global $visuItem;

	$res="";

	$qtxt="SELECT language FROM ".$GLOBALS["prefix_cms"]."_news WHERE idNews='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$res=$row["language"];
	}

	return $res;
}


function del_attach() {
	checkPerm('del');

	//TODO: 	news_checkTreePerm - requires news id.. //&


	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	$url="index.php?modname=news&amp;op=manattach&amp;id=".$_GET["id"];

	if (isset($_POST["canc_del"])) {
		jumpTo($url);
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_news_attach WHERE id='".$id."' LIMIT 1;");

		jumpTo($url);
	}
	else {

		$out->add("<div class=\"std_block\">\n");

		$form=new Form();

		$out->add($form->openForm("news_form", $url."&amp;act_op=delattach"));

		$out->add($form->getHidden("id", "id", (int)$_GET["aid"]));

		$out->add(	getDeleteUi(
					$lang->def('_AREYOUSURE'),
					false,
					false,
					'conf_del',
					'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

}


function news_checkTreePerm($folder_id, $return_val=FALSE, $item_id=FALSE) { //&
	require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

	if (($item_id > 0) && ($folder_id === FALSE)) {
		list($folder_id) = mysql_fetch_row(mysql_query("
		SELECT idFolder
		FROM ".$GLOBALS["prefix_cms"]."_news
		WHERE idNews = '".$item_id."'"));
	}

	$ctp=new CmsTreePermissions("news");
	$res =$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, $return_val);

	if ($return_val)
		return $res;
}




if ((isset($GLOBALS["op"]) && ($GLOBALS["op"] != "")))
	$op=$GLOBALS["op"];
else
	$op="news";


switch($op) {
	case "news" : {
		news();
	};break;

	case "selnewshomepage" : {
		selnewshomepage();
	};break;
	case "newsonhome" : {
		newsonhome();
	};break;

	case "addnews" : {
		addnews();
	};break;
	case "insnews" : {
		if (isset($_POST["undo"]))
			news();
		else
			insnews();
	};break;

	case "modnews" : {
		modnews();
	};break;
	case "upnews" : {
		if (isset($_POST["undo"]))
			news();
		else
			upnews();
	};break;

	case "delnews" : {
		delnews();
	};break;

	case "manattach" : {
		attachnews($_GET["id"]);
	};break;


	case "delattach": {
		del_attach();
	};break;

}

?>