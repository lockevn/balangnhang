<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
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


function content() {
	//funAdminAccess('OP');
	$visuItem=$GLOBALS["visuItem"];

	require_once( $GLOBALS['where_cms'].'/admin/modules/content/content_class.php' );

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	$out->setWorkingZone('content');

	$treeView = createTreeView();

	switch ( content_getOp( $treeView ) ) {
		case "newitem" : {
			addcontent( $treeView );
		};break;
		case "modcontent" : {
			//print_r($treeView); //??
			modcontent( $treeView );
		};break;
		case "delcontent" : {
			delcontent( $treeView );
		};break;

		case 'newfolder': {
			$out->add('<form method="post" action="index.php?modname=content&amp;op=content" >'."\n");
			content_addfolder( $treeView);
			$out->add('</form>');
		};break;
		case 'renamefolder' : {
			$out->add('<form method="post" action="index.php?modname=content&amp;op=content" >'."\n");
			content_renamefolder($treeView);
			$out->add('</form>');
		};break;
		case 'movefolder' :  {
			$out->add('<form method="post" action="index.php?modname=content&amp;op=content" >'."\n");
			content_move_folder($treeView);
			$out->add('</form>');
		};break;
		case 'content_move_form' :  {
			$out->add('<form method="post" action="index.php?modname=content&amp;op=content" >'."\n");
			content_move_form($treeView);
			$out->add('</form>');
		};break;
		case 'deletefolder' : {
			$out->add('<form method="post" action="index.php?modname=content&amp;op=content" >'."\n");
			content_deletefolder($treeView);
			$out->add('</form>');
		};break;
		case 'display':
		default: {
			//area title
			$out->add(getTitleArea($lang->def("_TEXTOF"), "content"));
			show_default($treeView, $out);
		};break;
	}

}


function show_default(& $treeView, & $out) {

	$out->add('<div class="std_block">');
	$out->add('<form method="post" action="index.php?modname=content&amp;op=content" >'."\n");
	$listView = $treeView->getListView();

	$user_level =$GLOBALS["current_user"]->getUserLevelId(); //&
	if ($user_level != ADMIN_GROUP_GODADMIN) {
		$treeView->setUseAdminFilter(TRUE);
		$listView->setUseAdminFilter(TRUE);
	}

	$treeView->showbtn=1;
	$out->add($treeView->load());

	$folder_id =$treeView->getSelectedFolderId();
	if (content_checkTreePerm($folder_id, TRUE)) { //&
		$out->add($treeView->loadActions());
		$listView->setInsNew( checkPerm('add', true) );
	}
	$out->add($listView->printOut());

	$out->add('</form>'.'</div>');

}


function addcontent( $treeView ) {
	checkPerm('add');
 	$visuItem=$GLOBALS["visuItem"];
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$folder_id =(int)$treeView->getSelectedFolderId(); //&
	content_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');


	$back_ui_url="index.php?modname=content&amp;op=content";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
	$title_arr[]=$lang->def("_ADD_CONTENT");
	$out->add(getTitleArea($title_arr, "content"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("content_form", "index.php?modname=content&amp;op=inscontent"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idFolder", "idFolder", $folder_id)); //&

	$treeView->printState();

	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "ldesc", "ldesc"));

	$out->add($form->getTextfield($lang->def("_TITLE"), "title", "title", 255, $lang->def("_TITLE")));



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


	$out->add($form->getHidden("idFolder", "idFolder", (int)$treeView->getSelectedFolderId()));

	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_INSERT")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function inscontent() {
	checkPerm('add');
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$folder_id =(int)$_POST['idFolder']; //&
	content_checkTreePerm($folder_id);

	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	if($_POST['title'] == '') $_POST['title'] = $lang->def("_NOTITLE");

	$arr=get_pubexp_info();
	$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
	$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

	if(!mysql_query("
	INSERT INTO ".$GLOBALS["prefix_cms"]."_content
	SET idFolder = '".(int)$_POST['idFolder']."',
		publish_date = NOW(),
		title = '".$_POST['title']."',
		long_desc = '".$_POST['ldesc']."',
		language = '".$_POST['language']."',
		pubdate = ".$pubdate.",
		expdate = ".$expdate.",
		important = '".(int)$_POST['important']."',
		cancomment = '".$_POST['cancomment']."',
		ord = '1'")) {
		errorCommunication($lang->def("_INSERR"));
		return;
	}
	else {
		list($idC)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

		require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
		$replace=array("[title]"=>$_POST['title']);
		sendCmsGenericEvent(
				/* members:    */ FALSE,
				/* class:      */ "ContentCreated",
				/* module:     */ "admin_content",
				/* action:     */ "add",
				/* log:        */ "Added content ".$idC,
				/* sub_string: */ "_CONTENT_ADDED_ALERT_SUB",
				/* txt_string: */ "_CONTENT_ADDED_ALERT_TXT",
				/* replace:    */ $replace
			);
	}

	require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
	fix_item_order($GLOBALS["prefix_cms"]."_content", "idContent", (int)$_POST['idFolder']);


	Header('Location:index.php?modname=content&op=content');
}


//---------------------------------------------------------------------

function modcontent( $treeView ) {
	checkPerm('mod');

	//require_once($GLOBALS["where_cms"]."/lib/manDateTime.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	if(isset($treeView))
	{
		$idContent = (int)$treeView->getContentSelected();
		$idFolder = (int)$treeView->getSelectedFolderId();
	}
	else
	{
		$idContent=importVar('id_content',TRUE,0);
		$idFolder=importVar('id_folder',TRUE,0);
		content_checkTreePerm($idFolder);
	}

	content_checkTreePerm($idFolder);

	//load info
	$textQuery = "
	SELECT type, key1, title, long_desc, language, important, cancomment, pubdate, expdate
	FROM ".$GLOBALS["prefix_cms"]."_content
	WHERE idContent  = '$idContent'";

	list($type, $key1, $title, $long_desc, $content_lang, $important, $cancomment, $pubdate, $expdate) = mysql_fetch_row(mysql_query($textQuery));


	$back_ui_url="index.php?modname=content&amp;op=content";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
	$title_arr[]=$lang->def("_EDIT_CONTENT").": ".$title;
	$out->add(getTitleArea($title_arr, "content"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("content_form", "index.php?modname=content&amp;op=upcontent"));

	$out->add($form->openElementSpace());

	if(isset($treeView)) $treeView->printState();


	$out->add($form->getHidden("type", "type", $type));
	$out->add($form->getHidden("key1", "key1", $key1));

	if ($type == "block_text") {
		$long_desc=loadTextOf($key1);
	}


	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "ldesc", "ldesc", $long_desc));

	$out->add($form->getTextfield($lang->def("_TITLE"), "title", "title", 255, $title));


	if ($type == "normal") {

		$langArray=array();
		$tmp_array=$GLOBALS['globLangManager']->getAllLangCode();
		foreach($tmp_array as $key=>$val) {
			$langArray[$val]=$val;
		}
		$out->add($form->getDropdown($lang->def("_LANGUAGE"), "language", "language", $langArray, $content_lang));

	}
	else {
		$out->add($form->getHidden("language", "language", $content_lang));
	}

	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add($form->getHidden("important", "important", 0));
	$out->add($form->getHidden("cancomment", "cancomment", 1));

	$out->add($form->getHidden("idContent", "idContent", $idContent));
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

function upcontent() {
	checkPerm('mod');

	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");


	$folder_id =(int)$_POST['idFolder']; //&
	content_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');


	if($_POST['title'] == '')
		$_POST['title'] = $lang->def("_NOTITLE");

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true;

	$long_desc="";
	$type=$_POST["type"];

	switch ($type) {
		case "normal": {
			$long_desc=$_POST['ldesc'];
		} break;
		case "block_text": {
			saveTextof((int)$_POST["key1"], $_POST['ldesc']);
		} break;
	}

	if ($period_ok) {

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		if(!mysql_query("
		UPDATE ".$GLOBALS["prefix_cms"]."_content
		SET title = '".$_POST['title']."',
			long_desc = '".$long_desc."',
			language = '".$_POST['language']."',
			important = '".$_POST['important']."',
			cancomment = '".$_POST['cancomment']."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate."
	  	WHERE idContent = '".$_POST['idContent']."'")) {
			$out->add(getErrorUi(($lang->def("_INSERR"))));
			return;
		}
		else {
			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[title]"=>$_POST['title']);
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "ContentModified",
					/* module:     */ "admin_content",
					/* action:     */ "edit",
					/* log:        */ "Edited content ".$_POST['idContent'],
					/* sub_string: */ "_CONTENT_EDITED_ALERT_SUB",
					/* txt_string: */ "_CONTENT_EDITED_ALERT_TXT",
					/* replace:    */ $replace
				);
		}

		jumpTo('index.php?modname=content&op=content');
	}
	else {
		$out->add("<div class=\"std_block\">\n");
		$out->add("<b>".$lang->def("_INSERR")."</b><br /><br />");
		$out->add("<a href=\"javascript:history.go(-1);\">&lt;&lt; ".$lang->def("_BACK")."</a>\n");
		$out->add("</div>\n");
	}
}

//----------------------------------------------------------------------------

function delcontent( $treeView=FALSE ) {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	if ($treeView !== FALSE) { //&
		$folder_id = (int)$treeView->getSelectedFolderId();
	}
	else if (isset($_POST["folder_id"])) {
		$folder_id=(int)$_POST["folder_id"];
	}
	else {
		$folder_id =0;
	}

	content_checkTreePerm($folder_id); //&

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=content&op=content");
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];
		$folder_id=(int)$_POST["folder_id"];

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_content_attach WHERE idContent='".$id."';");

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_content WHERE idContent='".$id."';");

		if ($_POST["type"] == "block_text") {
			$idBlock=(int)$_POST["key1"];

			require_once($GLOBALS["where_cms"]."/lib/lib.area.php");
			require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
			$blk_lang=get_area_lang(get_block_idArea($idBlock));

			$res="";
			$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_text WHERE idBlock='".$idBlock."' AND language='$blk_lang';";
			$q=mysql_query($qtxt);
		}

		// ---------- Fixing order:
		require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
		fix_item_order($GLOBALS["prefix_cms"]."_content", "idContent", $folder_id);
		// ------------------------

		jumpTo("index.php?modname=content&op=content");
	}
	else {

		//load info
		$id=(int)$treeView->getContentSelected();
		$folder_id=(int)$treeView->getSelectedFolderId();
		list($title, $type, $key1) = mysql_fetch_row(mysql_query("
		SELECT title, type, key1
		FROM ".$GLOBALS["prefix_cms"]."_content
		WHERE idContent  = '".$id."'"));

		$back_ui_url="index.php?modname=content&amp;op=content";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
		$title_arr[]=$lang->def("_DELETE_CONTENT").": ".$title;
		$out->add(getTitleArea($title_arr, "content"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("content_form", "index.php?modname=content&amp;op=delcontent"));

		$out->add($form->getHidden("id", "id", $id));
		$out->add($form->getHidden("folder_id", "folder_id", $folder_id));
		$out->add($form->getHidden("type", "type", $type));
		$out->add($form->getHidden("key1", "key1", $key1));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}
}


function attachcontent($id) {
	checkPerm('mod');
 global $visuItem;

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

 	$out->add(getTitleArea($lang->def("_TEXTOF"), "content"));

	$sel_lang=get_content_lang($id);

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
			define("_SELMOD", "content");
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
			define("_SELMOD", "content");
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
			define("_SELMOD", "content");
			define("_BACKID", $id);
			define("_MA_QUERY", $qtxt);
			$show_add=1;
		};break;

	}

	$out->add("<div class=\"std_block\">\n");
	if ($show_add) {
		include_once($GLOBALS["where_cms"]."/admin/modules/manattach/manattach.php");
		manattach();

		$out->add("<form action=\"index.php?modname=content&amp;op=manattach&id=".$id."\" method=\"POST\">\n");
		$out->add("<input class=\"button\" type=\"submit\" value=\"".$lang->def("_BACK")."\" />\n");
		$out->add("</form>\n");
	}
	else {
		show_attachments($id);
	}
	$out->add("</div>\n");

}

function show_attachments($id) {

 	$out=& $GLOBALS["page"];
 	$lang=& DoceboLanguage::createInstance("admin_content", "cms");

	if ((isset($_GET["act_op"])) && ($_GET["act_op"] == "delattach")) {
		del_attach();
	}

	if ((isset($_GET["act_op"])) && ($_GET["act_op"] == "attachitem")) {
		$aid=$_GET["add_id"];
		$type=$_GET["type"];
		$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_content_attach (idContent, idAttach, type) VALUES ('$id', '$aid', '$type')";
		mysql_query($qtxt);
	}

	$out->add("<b>".$lang->def("_ATTACH_DOCS")."</b>\n");
	show_attach_table("docs", $id);

	$out->add("<b>".$lang->def("_ATTACH_MEDIA")."</b>\n");
	show_attach_table("media", $id);

	$out->add("<b>".$lang->def("_ATTACH_LINKS")."</b>\n");
	show_attach_table("links", $id);

	$out->add("<form action=\"index.php?modname=content&amp;op=content\" method=\"POST\">\n");
	$out->add("<input class=\"button\" type=\"submit\" value=\"".$lang->def("_BACK")."\" />\n");
	$out->add("</form>\n");

}

function show_attach_table($type, $id) {
 global $visuItem;
	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	$sel_lang=get_content_lang($id);
	$table = new typeOne(0);
	$out->add($table->OpenTable(""));

	switch ($type) {

		case "docs" : {
			$t1=$GLOBALS["prefix_cms"]."_docs";
			$t2=$GLOBALS["prefix_cms"]."_docs_info";
			$t3=$GLOBALS["prefix_cms"]."_content_attach";
			$row_id="idDocs";
			$qtxt="SELECT $t1.*, $t2.sdesc, $t3.* "
					." FROM $t1 INNER JOIN $t3 ON ($t3.idAttach=$t1.$row_id)"
					." LEFT JOIN $t2 ON ($t2.idd=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idContent='$id'";

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
			$t3=$GLOBALS["prefix_cms"]."_content_attach";
			$row_id="idMedia";
			$qtxt="SELECT $t1.*, $t2.sdesc, $t3.* "
					." FROM $t1 INNER JOIN $t3 ON ($t3.idAttach=$t1.$row_id)"
					." LEFT JOIN $t2 ON ($t2.idm=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type' AND $t3.idContent='$id'";

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
			$t3=$GLOBALS["prefix_cms"]."_content_attach";
			$row_id="idLinks";
			$qtxt="SELECT $t1.*, $t2.title, $t3.* "
					." FROM $t1 INNER JOIN $t3 ON ($t3.idAttach=$t1.$row_id)"
					." LEFT JOIN $t2 ON ($t2.idl=$t1.$row_id AND $t2.lang='$sel_lang')"
					." WHERE $t3.type='$type'";

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
				"<a href=\"index.php?modname=content&amp;id=$id&amp;op=delattach$aid\">".
				"<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" /></a>";

			$out->add($table->WriteRow($rowcnt));
		}
	}

	$out->add($table->WriteAddRow('<a href="index.php?modname=content&amp;op=manattach&amp;id='.$id.'&amp;add='.$type.'" title="'.$lang->def("_ADD").'">'
									.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def("_ADD").'" /> '.$lang->def("_ADD").'</a>'));

	$out->add($table->CloseTable());

	$out->add("<br /><br />\n");
}


function get_content_lang($id) {
 global $visuItem;

	$res="";

	$qtxt="SELECT language FROM ".$GLOBALS["prefix_cms"]."_content WHERE idContent='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$res=$row["language"];
	}

	return $res;
}


function del_attach() {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	$url="index.php?modname=content&amp;op=manattach&amp;id=".$_GET["id"];

	if (isset($_POST["canc_del"])) {
		jumpTo($url);
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_content_attach WHERE id='".$id."' LIMIT 1;");

		jumpTo($url);
	}
	else {

		$out->add("<div class=\"std_block\">\n");

		$form=new Form();

		$out->add($form->openForm("content_form", $url."&amp;act_op=delattach"));

		$out->add($form->getHidden("id", "id", (int)$_GET["aid"]));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			/*'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />'*/ '',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

}


function content_checkTreePerm($folder_id, $return_val=FALSE, $item_id=FALSE) { //&
	require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

	if (($item_id > 0) && ($folder_id === FALSE)) {
		list($folder_id) = mysql_fetch_row(mysql_query("
		SELECT idFolder
		FROM ".$GLOBALS["prefix_cms"]."_content
		WHERE idContent = '".$item_id."'"));
	}

	$ctp=new CmsTreePermissions("content");
	$res =$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, $return_val);

	if ($return_val)
		return $res;
}


if ((isset($GLOBALS["op"]) && ($GLOBALS["op"] != "")))
	$op=$GLOBALS["op"];
else
	$op="content";

switch($op) {
	case "content" : {
		content();
	};break;

	case "selcontenthomepage" : {
		selcontenthomepage();
	};break;
	case "contentonhome" : {
		contentonhome();
	};break;

	case "addcontent" : {
		addcontent();
	};break;
	case "inscontent" : {
		if (isset($_POST["undo"]))
			content();
		else
			inscontent();
	};break;

	case "modcontent" : {
		modcontent();
	};break;
	case "upcontent" : {
		if (isset($_POST["undo"]))
			content();
		else
			upcontent();
	};break;

	case "delcontent" : {
		delcontent();
	};break;

	case "manattach" : {
		attachcontent($_GET["id"]);
	};break;


	case "delattach": {
		del_attach();
	};break;

}

?>