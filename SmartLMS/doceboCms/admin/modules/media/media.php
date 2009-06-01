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



define("_FPATH_INTERNAL", "/doceboCms/media/");
define("_FPATH", $GLOBALS["where_files_relative"]._FPATH_INTERNAL);
define("_PPATH_INTERNAL", "/doceboCms/media/preview/");
define("_PPATH", $GLOBALS["where_files_relative"]._PPATH_INTERNAL);

function media() {
	$visuItem=$GLOBALS["visuItem"];

	require_once($GLOBALS["where_cms"].'/admin/modules/media/media_class.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	$out->setWorkingZone('content');

	$treeView = createTreeView();

	switch ( media_getOp( $treeView ) ) {
		case "newitem" : {
			if (isset($_POST["undo"]))
				show_default($treeView, $out);
			else
				addmedia( $treeView );
		};break;
		case "modmedia" : {
			modmedia( $treeView );
		};break;
		case "delmedia" : {
			delmedia( $treeView );
		};break;

		case 'newfolder': {
			$out->add('<form method="post" action="index.php?modname=media&amp;op=media" >'."\n");
			media_addfolder( $treeView);
			$out->add('</form>');
		};break;
		case 'renamefolder' : {
			$out->add('<form method="post" action="index.php?modname=media&amp;op=media" >'."\n");
			media_renamefolder($treeView);
			$out->add('</form>');
		};break;
		case 'movefolder' :  {
			$out->add('<form method="post" action="index.php?modname=media&amp;op=media" >'."\n");
			media_move_folder($treeView);
			$out->add('</form>');
		};break;
		case 'media_move_form' :  {
			$out->add('<form method="post" action="index.php?modname=media&amp;op=media" >'."\n");
			media_move_form($treeView);
			$out->add('</form>');
		};break;
		case 'deletefolder' : {
			$out->add('<form method="post" action="index.php?modname=media&amp;op=media" >'."\n");
			media_deletefolder($treeView);
			$out->add('</form>');
		};break;
		case 'display':
		default: {
			$out->add(getTitleArea($lang->def("_MEDIA"), "media"));
			show_default($treeView, $out);
		};break;
	}

}



function show_default(& $treeView, & $out) {

	$out->add('<div class="std_block">');
	$out->add('<form method="post" action="index.php?modname=media&amp;op=media" >'."\n");
	$listView = $treeView->getListView();

	$user_level =$GLOBALS["current_user"]->getUserLevelId(); //&
	if ($user_level != ADMIN_GROUP_GODADMIN) {
		$treeView->setUseAdminFilter(TRUE);
		$listView->setUseAdminFilter(TRUE);
	}

	$treeView->showbtn=1;
	$out->add($treeView->load());

	$folder_id =$treeView->getSelectedFolderId();
	if (media_checkTreePerm($folder_id, TRUE)) { //&
		$out->add($treeView->loadActions());
		$listView->setInsNew( checkPerm('add', true) );
	}
	$out->add($listView->printOut());

	$out->add('</form>'.'</div>');

}




function addmedia(& $treeView ) {
	checkPerm('add');

	//include_once("core/manDateTime.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$folder_id =(int)$treeView->getSelectedFolderId(); //&
	media_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
	$form=new Form();


	$back_ui_url="index.php?modname=media&amp;op=media";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MEDIA");
	$title_arr[]=$lang->def("_ADD_MEDIA");
	$out->add(getTitleArea($title_arr, "media"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=media&amp;op=insmedia", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idFolder", "idFolder", $folder_id)); //&

	$out->add($form->getOpenFieldset($lang->def("_FILE_UPLOAD_OR_URL")));

	$out->add($form->getFilefield($lang->def("_FILENAME"), "file", "file"));
	$out->add($form->getFilefield($lang->def("_PREVIEW"), "file_preview", "file_preview"));

	$out->add($form->getTextfield($lang->def("_MEDIA_URL"), "media_url", "media_url", 255));

	$out->add($form->getCloseFieldset());


	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255));
	$out->add($form->getTextfield($lang->def("_AUTH_EMAIL"), "auth_email", "auth_email", 255));
	$out->add($form->getTextfield($lang->def("_AUTH_URL"), "auth_url", "auth_url", 255));


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


}

function insmedia() {
	checkPerm('add');
	$cms_previewimg_maxsize=$GLOBALS["cms"]["cms_previewimg_maxsize"];
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.multimedia.php");
	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$folder_id =(int)$_POST['idFolder']; //&
	media_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
	$form=new Form();

	$media_url =$_POST["media_url"];

	sl_open_fileoperations();

	if ((isset($_FILES["file"]["name"])) && (!empty($_FILES["file"]["name"]))) {

		$fname=$_FILES["file"]["name"];
		$real_fname=time().rand(10,99)."_".$fname;
		$tmp_fname=$_FILES["file"]["tmp_name"];

		$f1=sl_upload($tmp_fname, _FPATH_INTERNAL.$real_fname);
	}
	else {

		$fname="";
		$real_fname="";

		if (!empty($media_url)) {
			if (isYouTube($media_url)) {
				$fname =$media_url;
			}
			else {
				$fname=basename($media_url);
				$fname=(strpos($fname, "?") !== FALSE ? preg_replace("/(\?.*)/", "", $fname) : $fname);
			}
		}

		$f1=FALSE;
	}

	if ((isset($_FILES["file_preview"]["name"])) && (!empty($_FILES["file_preview"]["name"]))) {

		$fpreview=time().rand(10,99)."_".$_FILES["file_preview"]["name"];
		$tmp_fpreview=$_FILES["file_preview"]["tmp_name"];

		$f2=sl_upload($tmp_fpreview, _PPATH_INTERNAL.$fpreview);
	}
	else {

		$fpreview="";

		$f2=FALSE;
	}
	sl_close_fileoperations();

	$media_type=getMediaType($fname);

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true; //check_period($ts_pub, $ts_exp);

	if ( ( ($f1) || (!empty($media_url)) ) && ( ($f2) || (empty($fpreview)) ) && ($period_ok)) {

		// Try to create the preview if no file was uploaded:
		if ((empty($fpreview)) && ($media_type === "image")) {
			include_once($GLOBALS['where_framework']."/lib/lib.multimedia.php");
			$fpreview=$real_fname;
			$res=createPreview(_FPATH, _PPATH, $fpreview, $cms_previewimg_maxsize, $cms_previewimg_maxsize);
			if (!$res) $fpreview="";
		}

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		if(!mysql_query("
		INSERT INTO ".$GLOBALS["prefix_cms"]."_media
		SET idFolder = '".(int)$_POST['idFolder']."',
			publish_date = NOW(),
			fname = '".$fname."',
			real_fname = '".addslashes($real_fname)."',
			fpreview = '".addslashes($fpreview)."',
			media_url = '".$media_url."',
			author = '".$_POST['author']."',
			auth_email = '".$_POST['auth_email']."',
			auth_url = '".$_POST['auth_url']."',
			important = '".(int)$_POST['important']."',
			cancomment = '".$_POST['cancomment']."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate)) {
			errorCommunication(_INSERR);
			return;
		}
		else {
			list($idM)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[fname]"=>$fname);
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "MediaCreated",
					/* module:     */ "admin_media",
					/* action:     */ "add",
					/* log:        */ "Added media ".$idM,
					/* sub_string: */ "_MEDIA_ADDED_ALERT_SUB",
					/* txt_string: */ "_MEDIA_ADDED_ALERT_TXT",
					/* replace:    */ $replace
				);

			$out->add("<div class=\"std_block\">\n");
			$out->add("<br /><b>\n");
			$out->add(info_lang_table($idM));
			$out->add("<form action=\"index.php?modname=media&op=media\" method=\"POST\">\n");
			$out->add('<br /><input class="button" type="submit" value="'.$lang->def("_CONTINUE").'" />');
			$out->add("</form>\n");
			$out->add("</div>\n");
		}
	}
	else {
		$out->add("<div class=\"std_block\">\n");
		$out->add("<b>"._INSERR."</b><br /><br />");
		$out->add("<a href=\"javascript:history.go(-1);\">&lt;&lt; ".$lang->def("_BACK")."</a>\n");
		$out->add("</div>\n");
		unlink_uploads(array(_FPATH_INTERNAL.$real_fname, _PPATH_INTERNAL.$fpreview));
	}
}


//---------------------------------------------------------------------

function modmedia( $treeView, $media_id=FALSE ) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	// salva la scheda:
	if ((isset($_POST["save_media_info"])) && ($_POST["save_media_info"]))
		save_media_info();

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	if ($media_id !== FALSE)
		$idMedia=(int)$media_id;
	else
		$idMedia = (int)$treeView->getMediaSelected();

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	media_checkTreePerm($idFolder);

	//load info
	$textQuery = "
	SELECT fname, real_fname, fpreview, media_url, author, auth_email, auth_url, important, cancomment, pubdate, expdate
	FROM ".$GLOBALS["prefix_cms"]."_media
	WHERE idMedia  = '$idMedia'";

	list($fname, $real_fname, $fpreview, $media_url, $author, $auth_email, $auth_url, $important, $cancomment, $pubdate, $expdate) = mysql_fetch_row(mysql_query($textQuery));

	$back_ui_url="index.php?modname=media&amp;op=media";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MEDIA");
	$title_arr[]=$lang->def("_EDIT_MEDIA").": ".$fname;
	$out->add(getTitleArea($title_arr, "media"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=media&amp;op=upmedia", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idMedia", "idMedia", $idMedia));
	$out->add($form->getHidden("idFolder", "idFolder", $idFolder)); //&

	if ($treeView !== FALSE)
		$treeView->printState();

	$out->add($form->getOpenFieldset($lang->def("_FILE_UPLOAD_OR_URL")));

	$file_name =(!empty($real_fname) ? $fname : FALSE);
	$out->add($form->getExtendedFilefield($lang->def("_FILENAME"), "file", "file", $real_fname, $fname));
	$out->add($form->getExtendedFilefield($lang->def("_PREVIEW"), "file_preview", "file_preview", $fpreview));

	$out->add($form->getTextfield($lang->def("_MEDIA_URL"), "media_url", "media_url", 255, $media_url));

	$out->add($form->getCloseFieldset());


	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255, $author));
	$out->add($form->getTextfield($lang->def("_AUTH_EMAIL"), "auth_email", "auth_email", 255, $auth_email));
	$out->add($form->getTextfield($lang->def("_AUTH_URL"), "auth_url", "auth_url", 255, $auth_url));


	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));


	$out->add("<br /><br />\n");

	$out->add($form->getHidden("important", "important", 0));
	$out->add($form->getHidden("cancomment", "cancomment", 1));
	$out->add($form->getHidden("old_fname", "old_fname", $fname));


	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add("<br /><br /><b>\n");
	$out->add($lang->def("_FILEINFO")."</b><br />\n");

	$out->add(info_lang_table($idMedia));

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");

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
		$url="index.php?modname=media&amp;op=media_editinfo&amp;lang=$val&amp;idMedia=$id";
		$link="<a href=\"$url\">$img</a>";

		$line=array($val, $link);
		$res.=$table->WriteRow($line);
	}

	$res.=$table->CloseTable();

	return $res;
}


function upmedia() {
	checkPerm('mod');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.multimedia.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");
	//include_once("core/manDateTime.php");

	$folder_id =(int)$_POST['idFolder']; //&
	media_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	$update_fname =FALSE;
	$update_real_fname =FALSE;
	$update_fpreview =FALSE;
	$cms_previewimg_maxsize=$GLOBALS["cms"]["cms_previewimg_maxsize"];

	//load info
	$idMedia=(int)$_POST["idMedia"];

	$media_url =$_POST["media_url"];
//print_r($_POST); die();
	sl_open_fileoperations();

	if ((isset($_POST["file_to_del"]["file"])) && (!empty($_POST["file_to_del"]["file"]))) {
		sl_unlink(_FPATH_INTERNAL.$_POST["file_to_del"]["file"]);
		$update_fname =TRUE;
		$update_real_fname =TRUE;
		if (isset($_POST["old_file"])) {
			unset($_POST["old_file"]);
		}
	}

	if ((isset($_POST["file_to_del"]["file_preview"])) && (!empty($_POST["file_to_del"]["file_preview"]))) {
		sl_unlink(_PPATH_INTERNAL.$_POST["file_to_del"]["file_preview"]);
		$update_fpreview =TRUE;
		if (isset($_POST["old_file_preview"])) {
			unset($_POST["old_file_preview"]);
		}
	}

	if ((isset($_FILES["file"]["name"])) && (!empty($_FILES["file"]["name"]))) {

		$fname=$_FILES["file"]["name"];
		$real_fname=time().rand(10,99)."_".$fname;
		$tmp_fname=$_FILES["file"]["tmp_name"];

		$f1=sl_upload($tmp_fname, _FPATH_INTERNAL.$real_fname);
		if ($f1) {
			$update_fname =TRUE;
			$update_real_fname =TRUE;
		}
	}
	else {

		$fname="";
		$real_fname="";

		if (!empty($media_url)) {
			if (isYouTube($media_url)) {
				$fname =$media_url;
			}
			else {
				$fname=basename($media_url);
				$fname=(strpos($fname, "?") !== FALSE ? preg_replace("/(\?.*)/", "", $fname) : $fname);
			}
		}

		$f1=FALSE;
	}

	if ((isset($_FILES["file_preview"]["name"])) && (!empty($_FILES["file_preview"]["name"]))) {

		$fpreview=time().rand(10,99)."_".$_FILES["file_preview"]["name"];
		$tmp_fpreview=$_FILES["file_preview"]["tmp_name"];

		$f2=sl_upload($tmp_fpreview, _PPATH_INTERNAL.$fpreview);
		if ($f2) {
			$update_fpreview =TRUE;
		}
	}
	else {

		$fpreview="";

		$f2=FALSE;
	}
	sl_close_fileoperations();

	$media_type=getMediaType($fname);

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true;

	if ( ( ($f1) || ($real_fname == "") || (!empty($media_url)) ) && ( ($f2) || (empty($fpreview)) ) && ($period_ok)) {

		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_media SET ";


		if ((empty($fpreview)) && ($media_type === "image")) {
			$fpreview=$real_fname;
			$res=createPreview(_FPATH, _PPATH, $fpreview, $cms_previewimg_maxsize, $cms_previewimg_maxsize);
			if (!$res) $fpreview="";
			else {
				$f2 =TRUE;
				$update_fpreview =TRUE;
			}
		}

		if (($f1) && (isset($_POST["old_file"])) && (!empty($_POST["old_file"]))) {
			sl_unlink(_FPATH_INTERNAL.$_POST["old_file"]);
		}

		if (($f2) && (isset($_POST["old_file_preview"])) && (!empty($_POST["old_file_preview"]))) {
			sl_unlink(_PPATH_INTERNAL.$_POST["old_file_preview"]);
		}

		$qtxt.=($update_fname ? "fname = '".$fname."', " : "");
		$qtxt.=($update_real_fname ? "real_fname = '".addslashes($real_fname)."', " : "");
		$qtxt.=($update_fpreview ? "fpreview = '".addslashes($fpreview)."', " : "");

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		$qtxt.="media_url = '".$_POST['media_url']."',
			author = '".$_POST['author']."',
			auth_email = '".$_POST['auth_email']."',
			auth_url = '".$_POST['auth_url']."',
			important = '".$_POST['important']."',
			cancomment = '".$_POST['cancomment']."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate."
			WHERE idMedia = '".$_POST['idMedia']."'";

		if(!mysql_query($qtxt)) {
			errorCommunication($lang->def("_INSERR"));
			return;
		}
		else {
			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[fname]"=>(!empty($fname) ? $fname : $_POST["old_fname"]));
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "MediaModified",
					/* module:     */ "admin_media",
					/* action:     */ "edit",
					/* log:        */ "Edited media ".$_POST['idMedia'],
					/* sub_string: */ "_MEDIA_EDITED_ALERT_SUB",
					/* txt_string: */ "_MEDIA_EDITED_ALERT_TXT",
					/* replace:    */ $replace
				);
		}

		header('Location:index.php?modname=media&op=media');
	}
	else {
		$out->add("<div class=\"std_block\">\n");
		$out->add("<b>".$lang->def("_INSERR")."</b><br /><br />");
		$out->add("<a href=\"javascript:history.go(-1);\">".$lang->def("_BACK")."</a>\n");
		$out->add("</div>\n");
		unlink_uploads(array(_FPATH_INTERNAL.$real_fname, _PPATH_INTERNAL.$fpreview));
	}
}

//----------------------------------------------------------------------------

function delmedia( $treeView=FALSE ) {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	if ($treeView !== FALSE) { //&
		$folder_id = (int)$treeView->getSelectedFolderId();
	}
	else if (isset($_POST["folder_id"])) {
		$folder_id=(int)$_POST["folder_id"];
	}
	else {
		$folder_id =0;
	}

	media_checkTreePerm($folder_id); //&

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=media&op=media");
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];

		$qtxt="SELECT real_fname, fpreview FROM ".$GLOBALS["prefix_cms"]."_media WHERE idMedia='$id';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$real_fname=$row["real_fname"];
			$fpreview=$row["fpreview"];

			if (!empty($real_fname))
				@sl_unlink(_FPATH_INTERNAL.$real_fname);
			if (!empty($fpreview))
				@sl_unlink(_PPATH_INTERNAL.$fpreview);
		}

		$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_media WHERE idMedia='".(int)$id."'";
		$q=mysql_query($qtxt);

		jumpTo("index.php?modname=media&op=media");
	}
	else {

		//load info
		$id=(int)$treeView->getMediaSelected();
		list($fname) = mysql_fetch_row(mysql_query("
		SELECT fname
		FROM ".$GLOBALS["prefix_cms"]."_media
		WHERE idMedia  = '".$id."'"));

		$back_ui_url="index.php?modname=media&amp;op=media";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_MEDIA");
		$title_arr[]=$lang->def("_DELETE_MEDIA").": ".$fname;
		$out->add(getTitleArea($title_arr, "media"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("media_form", "index.php?modname=media&amp;op=delmedia"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_FILENAME').' :</span> '.$fname.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

}


function media_editinfo() {
	checkPerm('mod');
	require_once($GLOBALS["where_cms"].'/admin/modules/media/media_class.php' );
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
	$form=new Form();

	$sel_lang=$_GET["lang"];
	$idMedia=$_GET["idMedia"];
	$btn=Media_TreeView::_getOpModifyId().$idMedia;

	media_checkTreePerm(FALSE, FALSE, $idMedia); //&

	$home_url="index.php?modname=media&amp;op=media";
	$back_ui_url="index.php?modname=media&amp;op=editmedia&amp;id=".$idMedia;
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_MEDIA");
	$title_arr[$back_ui_url]=$lang->def("_EDIT_MEDIA");
	$title_arr[]=$lang->def("_EDIT_MEDIA_INFO");
	$out->add(getTitleArea($title_arr, "media"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=media&amp;op=media"));

	$out->add($form->openElementSpace());


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media_info WHERE idm='$idMedia' AND lang='$sel_lang';";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$info=mysql_fetch_array($q);
		$out->add($form->getHidden("new", "new", "0"));
	}
	else {
		$info=array();
		$info["title"]=$lang->def("_TITLE");
		$info["keywords"]=$lang->def("_KEYWORDS");
		$info["sdesc"]=$lang->def("_SHORTDESC");
		$info["ldesc"]="";
		$out->add($form->getHidden("new", "new", "1"));
	}

	$out->add($form->getHidden($btn, $btn, "1"));
	$out->add($form->getHidden("idMedia", "idMedia", $idMedia));
	$out->add($form->getHidden("lang", "lang", $sel_lang));
	$out->add($form->getHidden("save_media_info", "save_media_info", "1"));


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


function save_media_info() {

	$idMedia=$_POST["idMedia"];
	media_checkTreePerm(FALSE, FALSE, $idMedia); //&

	$lang=$_POST["lang"];
	$title=$_POST["title"];
	$keywords=$_POST["keywords"];
	$sdesc=$_POST["sdesc"];
	$ldesc=$_POST["ldesc"];
	$new=$_POST["new"];


	$qtxt="";
	if ($new) {
		$qtxt.="INSERT INTO ".$GLOBALS["prefix_cms"]."_media_info (idm, title, keywords, sdesc, ldesc, lang) ";
		$qtxt.="VALUES ('$idMedia', '$title', '$keywords', ";
		$qtxt.="'$sdesc', '$ldesc', '$lang');";
	}
	else {
		$qtxt.="UPDATE ".$GLOBALS["prefix_cms"]."_media_info SET title='$title', keywords='$keywords', ";
		$qtxt.="sdesc='$sdesc', ldesc='$ldesc' WHERE idm='$idMedia' AND lang='$lang';";
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
 include_once($GLOBALS['where_framework']."/lib/lib.upload.php");

	foreach ($ul_files as $key=>$val) {
		sl_unlink($val);
	}

}


function media_checkTreePerm($folder_id, $return_val=FALSE, $item_id=FALSE) { //&
	require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

	if (($item_id > 0) && ($folder_id === FALSE)) {
		list($folder_id) = mysql_fetch_row(mysql_query("
		SELECT idFolder
		FROM ".$GLOBALS["prefix_cms"]."_media
		WHERE idMedia = '".$item_id."'"));
	}

	$ctp=new CmsTreePermissions("media");
	$res =$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, $return_val);

	if ($return_val)
		return $res;
}



if ((isset($GLOBALS["op"]) && ($GLOBALS["op"] != "")))
	$op=$GLOBALS["op"];
else
	$op="media";

switch($op) {
	case "media" : {
		media();
	};break;

	case "selmediahomepage" : {
		selmediahomepage();
	};break;
	case "mediaonhome" : {
		mediaonhome();
	};break;

	case "insmedia" : {
		if (isset($_POST["undo"]))
			media();
		else
			insmedia();
	};break;

	case "upmedia" : {
		if (isset($_POST["undo"]))
			media();
		else
			upmedia();
	};break;

	case "editmedia" : {
		modmedia(FALSE, $_GET["id"]);
	};break;

	case "delmedia" : {
		delmedia();
	};break;

	case "media_editinfo" : {
		if (isset($_POST["undo"]))
			modmedia();
		else
			media_editinfo();
	};break;

}

?>
