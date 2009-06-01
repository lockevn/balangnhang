<?php

/*************************************************************************/
/* DOCEBO - Content Management System                                    */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Claudio Erba, Fabio Pirovano                    */
/* & Giovanni Derks - http://www.spaghettilearning.com                   */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");

$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_manpage.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_organizations.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
 $GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style-admin.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');


define("_PPATH_INTERNAL", "/doceboCms/links/preview/");
define("_PPATH", $GLOBALS["where_files_relative"]._PPATH_INTERNAL);


function links() {
	$visuItem=$GLOBALS["visuItem"];

	require_once( $GLOBALS["where_cms"].'/admin/modules/links/links_class.php' );

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

	$out->setWorkingZone('content');


	$treeView = createTreeView();

	switch ( links_getOp( $treeView ) ) {
		case "newitem" : {
			if (isset($_POST["undo"]))
				show_default($treeView, $out);
			else
				addlinks( $treeView );
		};break;
		case "modlinks" : {
			modlinks( $treeView );
		};break;
		case "dellinks" : {
			dellinks( $treeView );
		};break;

		case 'newfolder': {
			$out->add('<form method="post" action="index.php?modname=links&amp;op=links" >'."\n");
			links_addfolder( $treeView);
			$out->add('</form>');
		};break;
		case 'renamefolder' : {
			$out->add('<form method="post" action="index.php?modname=links&amp;op=links" >'."\n");
			links_renamefolder($treeView);
			$out->add('</form>');
		};break;
		case 'movefolder' :  {
			$out->add('<form method="post" action="index.php?modname=links&amp;op=links" >'."\n");
			links_move_folder($treeView);
			$out->add('</form>');
		};break;
		case 'links_move_form' :  {
			$out->add('<form method="post" action="index.php?modname=links&amp;op=links" >'."\n");
			$out->add(links_move_form($treeView));
			$out->add('</form>');
		};break;
		case 'deletefolder' : {
			$out->add('<form method="post" action="index.php?modname=links&amp;op=links" >'."\n");
			links_deletefolder($treeView);
			$out->add('</form>');
		};break;
		case 'display':
		default: {
			//area title
			$out->add(getTitleArea($lang->def("_LINKS"), "links"));
			show_default($treeView, $out);
		};break;
	}

}

function show_default(& $treeView, & $out) {

	$out->add('<div class="std_block">');
	$out->add('<form method="post" action="index.php?modname=links&amp;op=links" >'."\n");
	$listView = $treeView->getListView();
	$treeView->showbtn=1;
	$out->add($treeView->load());
	$out->add($treeView->loadActions());
	$listView->setInsNew( checkPerm('add', true) );
	$out->add($listView->printOut());

	$out->add('</form>'.'</div>');

}


function addlinks( $treeView ) {
	checkPerm('add');


	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$back_ui_url="index.php?modname=links&amp;op=links";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_LINKS");
	$title_arr[]=$lang->def("_ADD_LINKS");
	$out->add(getTitleArea($title_arr, "links"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=links&amp;op=inslinks", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idFolder", "idFolder", (int)$treeView->getSelectedFolderId()));


	$out->add($form->getFilefield($lang->def("_PREVIEW"), "file_preview", "file_preview"));

	$out->add($form->getTextfield($lang->def("_URL"), "url", "url", 255));
	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255));
	$out->add($form->getTextfield($lang->def("_AUTH_EMAIL"), "auth_email", "auth_email", 255));


	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
	$out->add(show_pubexp_table($form, $lang, 0, 0));

	$out->add("<br /><br />\n");

	$out->add($form->getHidden("important", "important", 0));
	$out->add($form->getHidden("cancomment", "cancomment", 1));


	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");


return 0;

	//area title
	echo '<form id="linksform" action="index.php?modname=links&amp;op=inslinks" enctype="multipart/form-data" method="POST">'
		.'<div class="std_block">'

		.'<input type="hidden" name="idFolder" value="'.(int)$treeView->getSelectedFolderId().'" />'
		.'<div class="title"><label for="file_preview">'._PREVIEW.'</label></div>'
		.'<input class="textfield" type="file" id="file_preview" name="file_preview" />'

		.'<div class="title"><label for="url">'._URL.'</label></div>'
		.'<input class="textfield" type="text" id="url" name="url" value="http://" />'
		.'<div class="title"><label for="author">'._AUTHOR.'</label></div>'
		.'<input class="textfield" type="text" id="author" name="author" />'
		.'<div class="title"><label for="auth_email">'._AUTH_EMAIL.'</label></div>'
		.'<input class="textfield" type="text" id="auth_email" name="auth_email" /><br /><br />';

	echo("<table>\n");
	echo("<tr><td class=\"hvcenter\">\n");
	echo("<input type=\"checkbox\" id=\"use_pub_time\" name=\"use_pub_time\" value=\"1\"$upt_chk />\n");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"date_pub\">"._DATE_PUB."</label></div>\n");
	make_cal($date_pub, "_pub");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"hour_pub\">"._TIME_PUB."</label></div>\n");
	time_select($time_pub, "_pub");
	echo("</td></tr>\n");
	echo("<tr><td class=\"hvcenter\">\n");
	echo("<input type=\"checkbox\" id=\"use_exp_time\" name=\"use_exp_time\" value=\"1\"$uet_chk />\n");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"date_exp\">"._DATE_EXP."</label></div>\n");
	make_cal($date_exp, "_exp");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"hour_exp\">"._TIME_EXP."</label></div>\n");
	time_select($time_exp, "_exp");
	echo("</td></tr>\n");
	echo("</table>\n");


	echo("<br /><br />\n");

	echo '<label><input type="checkbox" name="important" value="1" />&nbsp;'._IMPOF.'</label><br /><br />'

		.'<input class="button" type="submit" value="'._INSERT.'" />'
		.'</div>'
		.'</form>';

	setup_cal("_pub");
	setup_cal("_exp");
}

function inslinks() {
	checkPerm('add');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');
	$form=new Form();

	if ($_FILES["file_preview"]["name"] != "")
		$fpreview=time().rand(10,99)."_".$_FILES["file_preview"]["name"];
	else
		$fpreview="";
	$tmp_fpreview=$_FILES["file_preview"]["tmp_name"];

	sl_open_fileoperations();
	$f2=sl_upload($tmp_fpreview, _PPATH_INTERNAL.$fpreview);
	sl_close_fileoperations();

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true;

	if (( ($f2) || ($fpreview == "") ) && ($period_ok)) {

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		if(!mysql_query("
		INSERT INTO ".$GLOBALS["prefix_cms"]."_links
		SET idFolder = '".(int)$_POST['idFolder']."',
			publish_date = NOW(),
			url = '".$_POST['url']."',
			fpreview = '".addslashes($fpreview)."',
			author = '".$_POST['author']."',
			auth_email = '".$_POST['auth_email']."',
			important = '".(int)$_POST['important']."',
			cancomment = '".(int)$_POST["cancomment"]."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate)) {
			errorCommunication(_INSERR);
			return;
		}
		else {
			list($id)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));


			$out->add("<div class=\"std_block\">\n");
			$out->add("<br /><b>\n");
			$out->add($lang->def("_FILEINFO")."</b><br />\n");
			$out->add(info_lang_table($id));
			$out->add("<form action=\"index.php?modname=links&op=links\" method=\"POST\">\n");
			$out->add('<br /><input class="button" type="submit" value="'.$lang->def("_CONTINUE").'" />');
			$out->add("</form>\n");
			$out->add("</div>\n");
		}
	}
	else {
		$out->add("<div class=\"std_block\">\n");
		$out->add("<b>".$lang->def("_INSERR")."</b><br /><br />");
		$out->add("<a href=\"javascript:history.go(-1);\">".$lang->def("_BACK")."</a>\n");
		$out->add("</div>\n");
		unlink_uploads(array(_PPATH_INTERNAL.$fpreview));
	}
}


//---------------------------------------------------------------------

function modlinks( $treeView, $links_id=FALSE ) {
	checkPerm('mod');


	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	// salva la scheda:
	if ((isset($_POST["save_links_info"])) && ($_POST["save_links_info"]))
		save_links_info();

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	if ($links_id !== FALSE)
		$idLinks=(int)$links_id;
	else
		$idLinks = (int)$treeView->getLinksSelected();

	//load info
	$textQuery = "
	SELECT fpreview, author, auth_email, url, important, cancomment, pubdate, expdate
	FROM ".$GLOBALS["prefix_cms"]."_links
	WHERE idLinks  = '$idLinks'";

	list($fpreview, $author, $auth_email, $url, $important, $cancomment, $pubdate, $expdate) = mysql_fetch_row(mysql_query($textQuery));

	$back_ui_url="index.php?modname=links&amp;op=links";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_LINKS");
	$title_arr[]=$lang->def("_EDIT_LINKS").": ".substr($url, 0, 40);
	$out->add(getTitleArea($title_arr, "links"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=links&amp;op=uplinks", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idLinks", "idLinks", $idLinks));


	if ($treeView !== FALSE)
		$treeView->printState();

	$out->add($form->getFilefield($lang->def("_PREVIEW"), "file_preview", "file_preview", $fpreview));

	$out->add($form->getTextfield($lang->def("_URL"), "url", "url", 255, $url));
	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255, $author));
	$out->add($form->getTextfield($lang->def("_AUTH_EMAIL"), "auth_email", "auth_email", 255, $auth_email));


	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add("<br /><br />\n");

	$out->add($form->getHidden("important", "important", 0));
	$out->add($form->getHidden("cancomment", "cancomment", 1));


	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add("<br /><br /><b>\n");
	$out->add($lang->def("_FILEINFO")."</b><br />\n");

	$out->add(info_lang_table($idLinks));

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");


return 0;

	echo '<form method="post" action="index.php?modname=links&amp;op=uplinks" enctype="multipart/form-data">'
		.'<div class="std_block">'
		.'<input type="hidden" name="idLinks" value="'.$idLinks.'" />';

	$treeView->printState();

	init_calendar();

	echo '<div class="title"><label for="file_preview">'._PREVIEW.'</label></div>'
		.'<input class="textfield" type="file" id="file_preview" name="file_preview" />'

		.'<div class="title"><label for="url">'._URL.'</label></div>'
		.'<input class="textfield" type="text" id="url" name="url" value="'.$url.'" />'
		.'<div class="title"><label for="author">'._AUTHOR.'</label></div>'
		.'<input class="textfield" type="text" id="author" name="author" value="'.$author.'" />'
		.'<div class="title"><label for="auth_email">'._AUTH_EMAIL.'</label></div>'
		.'<input class="textfield" type="text" id="auth_email" name="auth_email" value="'.$auth_email.'" /><br /><br />';


	echo("<table>\n");
	echo("<tr><td class=\"hvcenter\">\n");
	echo("<input type=\"checkbox\" id=\"use_pub_time\" name=\"use_pub_time\" value=\"1\"$upt_chk />\n");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"date_pub\">"._DATE_PUB."</label></div>\n");
	make_cal($date_pub, "_pub");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"hour_pub\">"._TIME_PUB."</label></div>\n");
	time_select($time_pub, "_pub");
	echo("</td></tr>\n");
	echo("<tr><td class=\"hvcenter\">\n");
	echo("<input type=\"checkbox\" id=\"use_exp_time\" name=\"use_exp_time\" value=\"1\"$uet_chk />\n");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"date_exp\">"._DATE_EXP."</label></div>\n");
	make_cal($date_exp, "_exp");
	echo("</td><td class=\"spaceright\">\n");
	echo("<div class=\"title\"><label for=\"hour_exp\">"._TIME_EXP."</label></div>\n");
	time_select($time_exp, "_exp");
	echo("</td></tr>\n");
	echo("</table>\n");


	echo("<br /><br />\n");

	echo '<label><input type="checkbox" name="important" value="1"'
		.( $important ? ' checked="checked"' : '' )
		.' />&nbsp;'._IMPOF.'</label><br /><br />'


		.'<input class="button" type="submit" value="'._SAVE.'" />';


		echo("<br /><br /><b>\n");
		echo($lang->def("_FILEINFO")."</b><br />\n");

		info_lang_table($idLinks);

		echo '</div>'
		.'</form>';

	setup_cal("_pub");
	setup_cal("_exp");

}


function info_lang_table($id) {
	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');

	$res="";

	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	$table = new typeOne(0);
	$res.=$table->OpenTable("");

	$head = array($lang->def("_LANGUAGE"),
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />');
	$head_type = array('', 'img');


	$res.=$table->WriteHeader($head, $head_type);

	$larr=$GLOBALS['globLangManager']->getAllLangCode();
	foreach ($larr as $key=>$val) {

		$img="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD").": $val\" />";
		$url="index.php?modname=links&amp;op=links_editinfo&amp;lang=$val&amp;idLinks=$id";
		$link="<a href=\"$url\">$img</a>";

		$line=array($val, $link);
		$res.=$table->WriteRow($line);
	}

	$res.=$table->CloseTable();

	return $res;
}


function uplinks() {
	checkPerm('mod');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	//load info
	$idLinks=(int)$_POST["idLinks"];
	list($old_fpreview) = mysql_fetch_row(mysql_query("
		SELECT fpreview
		FROM ".$GLOBALS["prefix_cms"]."_links
		WHERE idLinks  = '".$idLinks."'"));


	if ($_FILES["file_preview"]["name"] != "")
		$fpreview=time().rand(10,99)."_".$_FILES["file_preview"]["name"];
	else
		$fpreview="";
	$tmp_fpreview=$_FILES["file_preview"]["tmp_name"];

	sl_open_fileoperations();
	$f2=sl_upload($tmp_fpreview, _PPATH_INTERNAL.$fpreview);
	sl_close_fileoperations();

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true;

	if (( ($f2) || ($fpreview == "") ) && ($period_ok)) {

		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_links SET ";
		if ($fpreview != "") {
			$qtxt.="fpreview = '".addslashes($fpreview)."',";
			// Cancello il vecchio file:
			sl_unlink(_PPATH_INTERNAL.$old_fpreview);
		}

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		$qtxt.="author = '".$_POST['author']."',
			auth_email = '".$_POST['auth_email']."',
			url = '".$_POST['url']."',
			important = '".$_POST['important']."',
			cancomment = '".(int)$_POST["cancomment"]."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate."
			WHERE idLinks = '".$_POST['idLinks']."'";

		if(!mysql_query($qtxt)) {
			errorCommunication(_INSERR);
			return;
		}

		jumpTo('index.php?modname=links&op=links');
	}
	else {
		echo("<div class=\"std_block\">\n");
		echo("<b>"._INSERR."</b><br /><br />");
		echo("<a href=\"javascript:history.go(-1);\">&lt;&lt; "._BACK."</a>\n");
		echo("</div>\n");
		unlink_uploads(array(_PPATH_INTERNAL.$fpreview));
	}
}

//----------------------------------------------------------------------------

function dellinks( $treeView=NULL ) {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=links&op=links");
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];

		$qtxt="SELECT fpreview FROM ".$GLOBALS["prefix_cms"]."_links WHERE idLinks='$id';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$fpreview=$row["fpreview"];

			if ($fpreview != "")
				@sl_unlink(_PPATH_INTERNAL.$fpreview);
		}

		$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_links WHERE idLinks='$id';";
		$q=mysql_query($qtxt);

		jumpTo("index.php?modname=links&op=links");
	}
	else {

		//load info
		$id=(int)$treeView->getLinksSelected();
		list($title) = mysql_fetch_row(mysql_query("
		SELECT t2.title
		FROM ".$GLOBALS["prefix_cms"]."_links as t1 LEFT JOIN ".
		$GLOBALS["prefix_cms"]."_links_info as t2 ON (t2.idl=t1.idLinks AND t2.lang='".getLanguage()."')
		WHERE t1.idLinks  = '".$id."'"));


		$back_ui_url="index.php?modname=links&amp;op=links";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_LINKS");
		$title_arr[]=$lang->def("_DELETE_LINKS").": ".$title;
		$out->add(getTitleArea($title_arr, "links"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("links_form", "index.php?modname=links&amp;op=dellinks"));

		$out->add($form->getHidden("id", "id", $id));

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

	include_once("core/upload.php");

	if( $_GET['confirm'] == 1 ) {

		//load info
		$idLinks=(int)$_GET["idLinks"];
		list($fpreview) = mysql_fetch_row(mysql_query("
		SELECT fpreview
		FROM ".$GLOBALS["prefix_cms"]."_links
		WHERE idLinks  = '".$idLinks."'"));

		if ($fpreview != "") @sl_unlink(_PPATH_INTERNAL.$fpreview);

		if(!mysql_query("
		DELETE FROM ".$GLOBALS["prefix_cms"]."_links
		WHERE idLinks  = '".$idLinks."'")) {
			addTitleArea('links');
			errorCommunication(_ERRREM);
			return;
		}

		jumpTo('index.php?modname=links&op=links');

	}
	else {

		//load info
		$idl=(int)$treeView->getLinksSelected();
		list($url) = mysql_fetch_row(mysql_query("
		SELECT url
		FROM ".$GLOBALS["prefix_cms"]."_links
		WHERE idLinks  = '".$idl."'"));

		echo '<div class="std_block">';

			echo '<b>'._AREYOUSURELINKS.'</b><br />'
				.'<div class="evidenceBlock">'
				.'<b>'._TITLE.' :</b> &quot;'.$url.'&quot;<br /><br />'
				.'[ <a href="index.php?modname=links&amp;op=dellinks&amp;idLinks='
				.(int)$treeView->getLinksSelected().'&amp;confirm=1">'._YES.'</a> | '
				.'<a href="index.php?modname=links&amp;op=links">'._NO.'</a> ]'
				.'</div>';

		echo '</div>';
	}

}


function links_editinfo() {
	checkPerm('mod');

	require_once( $GLOBALS["where_cms"].'/admin/modules/links/links_class.php' );
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$sel_lang=$_GET["lang"];
	$idLinks=$_GET["idLinks"];
	$btn="do_modlinks_".$idLinks;


	$home_url="index.php?modname=links&amp;op=links";
	$back_ui_url="index.php?modname=links&amp;op=editlinks&amp;id=".$idLinks;
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_LINKS");
	$title_arr[$back_ui_url]=$lang->def("_EDIT_LINKS");
	$title_arr[]=$lang->def("_EDIT_LINKS_INFO");
	$out->add(getTitleArea($title_arr, "links"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	$out->add($form->openForm("links_form", "index.php?modname=links&amp;op=links"));

	$out->add($form->openElementSpace());


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_links_info WHERE idl='$idLinks' AND lang='$sel_lang';";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$info=mysql_fetch_array($q);
		$out->add($form->getHidden("new", "new", "0"));
	}
	else {
		$info=array();
		$out->add($form->getHidden("new", "new", "1"));
	}

	$out->add($form->getHidden($btn, $btn, "1"));
	$out->add($form->getHidden("idLinks", "idLinks", $idLinks));
	$out->add($form->getHidden("lang", "lang", $sel_lang));
	$out->add($form->getHidden("save_link_info", "save_links_info", "1"));

	$out->add($form->getTextfield($lang->def("_TITLE"), "title", "title", 255, $info["title"]));
	$out->add($form->getTextfield($lang->def("_KEYWORDS"), "keywords", "keywords", 255, $info["keywords"]));
	$out->add($form->getTextfield($lang->def("_SHORTDESC"), "sdesc", "sdesc", 255, $info["sdesc"]));
	$out->add($form->getTextarea($lang->def("_DESCRIPTION"), "ldesc", "ldesc", $info["ldesc"]));

	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}


function save_links_info() {

	$idLinks=$_POST["idLinks"];
	$sel_lang=$_POST["lang"];
	$title=$_POST["title"];
	$keywords=$_POST["keywords"];
	$sdesc=$_POST["sdesc"];
	$ldesc=$_POST["ldesc"];
	$new=$_POST["new"];


	$qtxt="";
	if ($new) {
		$qtxt.="INSERT INTO ".$GLOBALS["prefix_cms"]."_links_info (idl, title, keywords, sdesc, ldesc, lang) ";
		$qtxt.="VALUES ('$idLinks', '$title', '".$keywords."', ";
		$qtxt.="'$sdesc', '".$ldesc."', '$sel_lang');";
	}
	else {
		$qtxt.="UPDATE ".$GLOBALS["prefix_cms"]."_links_info SET title='$title', keywords='".$keywords."', ";
		$qtxt.="sdesc='$sdesc', ldesc='".$ldesc."' WHERE idl='$idLinks' AND lang='$sel_lang';";
	}

	$q=mysql_query($qtxt);
}


function check_period_old(&$ts_pub, &$ts_exp) {
	include_once("core/manDateTime.php");

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


function unlink_uploads($ul_files) {
	include_once("core/upload.php");

	foreach ($ul_files as $key=>$val) {
		sl_unlink($val);
	}

}



$op=importVar("op");

switch($op) {
	case "links" : {
		links();
	};break;

	case "sellinkshomepage" : {
		sellinkshomepage();
	};break;
	case "linksonhome" : {
		linksonhome();
	};break;

	case "inslinks" : {
		if (isset($_POST["undo"]))
			links();
		else
		inslinks();
	};break;

	case "uplinks" : {
		if (isset($_POST["undo"]))
			links();
		else
			uplinks();
	};break;

	case "editlinks" : {
		modlinks(FALSE, $_GET["id"]);
	};break;

	case "dellinks" : {
		dellinks();
	};break;

	case "links_editinfo" : {
		links_editinfo();
	};break;

}

?>
