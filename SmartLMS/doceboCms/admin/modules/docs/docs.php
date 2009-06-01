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


define("_FPATH_INTERNAL", "/doceboCms/docs/");
define("_FPATH", $GLOBALS["where_files_relative"]._FPATH_INTERNAL);

function docs() {

	$visuItem=$GLOBALS["visuItem"];

	require_once($GLOBALS["where_cms"].'/admin/modules/docs/docs_class.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

	$out->setWorkingZone('content');

	//area title

	$treeView = createTreeView();

	switch ( docs_getOp( $treeView ) ) {
		case "newitem" : {
			if (isset($_POST["undo"]))
				show_default($treeView, $out);
			else
				adddocs( $treeView );
		};break;
		case "moddocs" : {
			moddocs( $treeView );
		};break;
		case "deldocs" : {
			deldocs( $treeView );
		};break;

		case 'newfolder': {
			$out->add('<form method="post" action="index.php?modname=docs&amp;op=docs" >'."\n");
			docs_addfolder( $treeView);
			$out->add('</form>');
		};break;
		case 'renamefolder' : {
			$out->add('<form method="post" action="index.php?modname=docs&amp;op=docs" >'."\n");
			docs_renamefolder($treeView);
			$out->add('</form>');
		};break;
		case 'movefolder' :  {
			$out->add('<form method="post" action="index.php?modname=docs&amp;op=docs" >'."\n");
			docs_move_folder($treeView);
			$out->add('</form>');
		};break;
		case 'docs_move_form' :  {
			$out->add('<form method="post" action="index.php?modname=docs&amp;op=docs" >'."\n");
			$out->add(docs_move_form($treeView));
			$out->add('</form>');
		};break;
		case 'deletefolder' : {
			$out->add('<form method="post" action="index.php?modname=docs&amp;op=docs" >'."\n");
			docs_deletefolder($treeView);
			$out->add('</form>');
		};break;
		case 'display':
		default: {
			$out->add(getTitleArea($lang->def("_DOCS"), "docs"));
			show_default($treeView, $out);
		};break;
	}

}


function show_default(& $treeView, & $out) {

	$out->add('<div class="std_block">');
	$out->add('<form method="post" action="index.php?modname=docs&amp;op=docs" >'."\n");
	$listView = $treeView->getListView();

	$user_level =$GLOBALS["current_user"]->getUserLevelId(); //&
	if ($user_level != ADMIN_GROUP_GODADMIN) {
		$treeView->setUseAdminFilter(TRUE);
		$listView->setUseAdminFilter(TRUE);
	}

	$treeView->showbtn=1;
	$out->add($treeView->load());

	$folder_id =$treeView->getSelectedFolderId();
	if (docs_checkTreePerm($folder_id, TRUE)) { //&
		$out->add($treeView->loadActions());
		$listView->setInsNew( checkPerm('add', true) );
	}
	$out->add($listView->printOut());

	$out->add('</form>'.'</div>');

}



function adddocs(& $treeView ) {
	checkPerm('add');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$folder_id =(int)$treeView->getSelectedFolderId(); //&
	docs_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');


	$back_ui_url="index.php?modname=docs&amp;op=docs";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_DOCS");
	$title_arr[]=$lang->def("_ADD_DOCS");
	$out->add(getTitleArea($title_arr, "docs"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=docs&amp;op=insdocs", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idFolder", "idFolder", $folder_id)); //&


	$out->add($form->getFilefield($lang->def("_FILENAME"), "file", "file"));

	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255));
	$out->add($form->getTextfield($lang->def("_AUTH_EMAIL"), "auth_email", "auth_email", 255));
	$out->add($form->getTextfield($lang->def("_AUTH_URL"), "auth_url", "auth_url", 255));
	
	$out->add(
		$form->getDateField($lang->def('_DATE'), 'date', 'date', (isset($_GET['date']) ? $_GET['date'] : ''))
		.$form->getLineBox($lang->def('_TIME'), $form->getInputDropdown('', 'time_h', 'time_h', getHours(), false, false).' : '.$form->getInputDropdown('', 'time_m', 'time_m', getMinutes(), false, false))
	);

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

	include_once("core/manDateTime.php");

	init_calendar();

	//area title
	echo '<form id="docsform" action="index.php?modname=docs&amp;op=insdocs" enctype="multipart/form-data" method="POST">'
		.'<div class="std_block">'

		.'<input type="hidden" name="idFolder" value="'.(int)$treeView->getSelectedFolderId().'" />'
		.'<div class="title"><label for="file">'._FILENAME.'</label></div>'
		.'<input class="textfield" type="file" id="file" name="file" />'

		.'<div class="title"><label for="author">'._AUTHOR.'</label></div>'
		.'<input class="textfield" type="text" id="author" name="author" />'
		.'<div class="title"><label for="auth_email">'._AUTH_EMAIL.'</label></div>'
		.'<input class="textfield" type="text" id="auth_email" name="auth_email" />'
		.'<div class="title"><label for="auth_url">'._AUTH_URL.'</label></div>'
		.'<input class="textfield" type="text" id="auth_url" name="auth_url" /><br /><br />';

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
		.'<label><input type="checkbox" name="cancomment" value="1" />&nbsp;'._ALLOW_COMMENTS.'</label><br /><br />'

		.'<input class="button" type="submit" value="'._INSERT.'" />'
		.'</div>'
		.'</form>';

	setup_cal("_pub");
	setup_cal("_exp");
}

/**
	 * Function that return the hours for the dropdown men�
	 * 
	 * @return array Array with the hours
	 */
	function getHours()
	{
		$re = array();
		
		$re['00'] = '00';
		$re['01'] = '01';
		$re['02'] = '02';
		$re['03'] = '03';
		$re['04'] = '04';
		$re['05'] = '05';
		$re['06'] = '06';
		$re['07'] = '07';
		$re['08'] = '08';
		$re['09'] = '09';
		$re['10'] = '10';
		$re['11'] = '11';
		$re['12'] = '12';
		$re['13'] = '13';
		$re['14'] = '14';
		$re['15'] = '15';
		$re['16'] = '16';
		$re['17'] = '17';
		$re['18'] = '18';
		$re['19'] = '19';
		$re['20'] = '20';
		$re['21'] = '21';
		$re['22'] = '22';
		$re['23'] = '23';
		
		return $re;
	}
	
	/**
	 * Function that return the minutes for the dropdown men�
	 * 
	 * @return array Array with the minutes
	 */
	function getMinutes()
	{
		$re = array();
		
		$re['00'] = '00';
		//$re['05'] = '05';
		//$re['10'] = '10';
		$re['15'] = '15';
		//$re['20'] = '20';
		//$re['25'] = '25';
		$re['30'] = '30';
		//$re['35'] = '35';
		//$re['40'] = '40';
		$re['45'] = '45';
		//$re['50'] = '50';
		//$re['55'] = '55';
		
		return $re;
	}

function insdocs() {
	checkPerm('add');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$folder_id =(int)$_POST['idFolder']; //&
	docs_checkTreePerm($folder_id);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
	$form=new Form();

	$fname=$_FILES["file"]["name"];
	$real_fname=time().rand(10,99)."_".$fname;
	$tmp_fname=$_FILES["file"]["tmp_name"];

	sl_open_fileoperations();
	$f1=sl_upload($tmp_fname, _FPATH_INTERNAL.$real_fname);
	sl_close_fileoperations();

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true; //check_period($ts_pub, $ts_exp);
	
	$date = $_POST['date'];
	
	if($date == '')
		$publish_date = date('Y-m-d H:i:s');
	else
		$publish_date = $GLOBALS['regset']->RegionalToDatabase($date.' '.$_POST['time_h'].':'.$_POST['time_m'].':00', 'datetime');
	
	if (($f1) && ($period_ok)) {

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");

		if(!mysql_query("
		INSERT INTO ".$GLOBALS["prefix_cms"]."_docs
		SET idFolder = '".(int)$_POST['idFolder']."',
			publish_date = '".$publish_date."',
			fname = '".$fname."',
			real_fname = '".addslashes($real_fname)."',
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
			list($idD)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[fname]"=>$fname);
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "DocumentCreated",
					/* module:     */ "admin_docs",
					/* action:     */ "add",
					/* log:        */ "Added document ".$idD,
					/* sub_string: */ "_DOCS_ADDED_ALERT_SUB",
					/* txt_string: */ "_DOCS_ADDED_ALERT_TXT",
					/* replace:    */ $replace
				);

			$out->add("<div class=\"std_block\">\n");
			$out->add("<br /><b>\n");
			$out->add($lang->def("_FILEINFO")."</b><br />\n");
			$out->add(info_lang_table($idD));
			$out->add("<form action=\"index.php?modname=docs&op=docs\" method=\"POST\">\n");
			$out->add('<br /><input class="button" type="submit" value="'.$lang->def("_CONTINUE").'" />');
			$out->add("</form>\n");
			$out->add("</div>\n");

		}

		//jumpTo('index.php?modname=docs&op=docs');
	}
	else {
		$out->add("<div class=\"std_block\">\n");
		$out->add("<b>".$lang->def("_INSERR")."</b><br /><br />");
		$out->add("<a href=\"javascript:history.go(-1);\">&lt;&lt; ".$lang->def("_BACK")."</a>\n");
		$out->add("</div>\n");
		unlink_uploads(array(_FPATH_INTERNAL.$real_fname));
	}
}


//---------------------------------------------------------------------

function moddocs( $treeView, $docs_id=FALSE ) {
	checkPerm('mod');

	//area title
	//addTitleArea('docs');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_cms'].'/lib/manDateTime.php');

	// salva la scheda:
	if ((isset($_POST["save_docs_info"])) && ($_POST["save_docs_info"]))
		save_docs_info();

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$platform = importVar('of_platform',FALSE,'');
	
	if ($platform == 'cms') {
		$idDocs = importVar('idDocs',TRUE,0);
		$idFolder=importVar('id_folder',TRUE,0);
//		docs_checkTreePerm($idFolder);
	} else	if ($docs_id !== FALSE) {
		$idDocs=(int)$docs_id;
		$idFolder = importVar('idFolder');
	} else {
		if(isset($treeView))	{
			$idDocs = (int)$treeView->getDocsSelected();
			$idFolder = (int)$treeView->getSelectedFolderId();
		} 
		docs_checkTreePerm($idFolder);
	}
	

	//load info
	$textQuery = "
	SELECT publish_date, fname, author, auth_email, auth_url, important, cancomment, pubdate, expdate
	FROM ".$GLOBALS["prefix_cms"]."_docs
	WHERE idDocs  = '$idDocs'";

	list($publish_date, $fname, $author, $auth_email, $auth_url, $important, $cancomment, $pubdate, $expdate) = mysql_fetch_row(mysql_query($textQuery));

	if ((int)$pubdate > 0) {
		$upt_chk=" checked=\"checked\"";
		set_from_timestamp($pubdate, $date_pub, $time_pub, 0, 24);
	}
	else {
		$upt_chk="";
		$date_pub="";
		$time_pub="";
	}
	if ((int)$expdate > 0) {
		$uet_chk=" checked=\"checked\"";
		set_from_timestamp($expdate, $date_exp, $time_exp, 0, 24);
	}
	else {
		$uet_chk="";
		$date_exp="";
		$time_exp="";
	}

	$time_h = $publish_date{11}.$publish_date{12};
	$time_m = $publish_date{14}.$publish_date{15};
	
	$date = $publish_date{8}.$publish_date{9}.$publish_date{7}.$publish_date{5}.$publish_date{6}.$publish_date{4}.$publish_date{0}.$publish_date{1}.$publish_date{2}.$publish_date{3};

	$back_ui_url="index.php?modname=docs&amp;op=docs";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_DOCS");
	$title_arr[]=$lang->def("_EDIT_DOCS").": ".$fname;
	$out->add(getTitleArea($title_arr, "docs"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("media_form", "index.php?modname=docs&amp;op=updocs", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());

	$out->add($form->getHidden("idDocs", "idDocs", $idDocs));
	$out->add($form->getHidden("idFolder", "idFolder", $idFolder)); //&


	if ($treeView !== FALSE)
		$treeView->printState();

	$out->add($form->getFilefield($lang->def("_FILENAME"), "file", "file", $fname));


	$out->add($form->getTextfield($lang->def("_AUTHOR"), "author", "author", 255, $author));
	$out->add($form->getTextfield($lang->def("_AUTH_EMAIL"), "auth_email", "auth_email", 255, $auth_email));
	$out->add($form->getTextfield($lang->def("_AUTH_URL"), "auth_url", "auth_url", 255, $auth_url));
	
	$out->add(
		$form->getDateField($lang->def('_DATE'), 'date', 'date', $date)
		.$form->getLineBox($lang->def('_TIME'), $form->getInputDropdown('', 'time_h', 'time_h', getHours(), $time_h, false).' : '.$form->getInputDropdown('', 'time_m', 'time_m', getMinutes(), $time_m, false))
	);

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

	$out->add(info_lang_table($idDocs));

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");


return 0;

	echo '<form method="post" action="index.php?modname=docs&amp;op=updocs" enctype="multipart/form-data">'
		.'<div class="std_block">'
		.'<input type="hidden" name="idDocs" value="'.$idDocs.'" />';

	$treeView->printState();

	init_calendar();

	echo '<div class="title"><label for="file">'._FILENAME.'</label></div>'
		.'<input class="textfield" type="file" id="file" name="file" maxlength="100" />'

		.'<div class="title"><label for="author">'._AUTHOR.'</label></div>'
		.'<input class="textfield" type="text" id="author" name="author" value="'.$author.'" />'
		.'<div class="title"><label for="auth_email">'._AUTH_EMAIL.'</label></div>'
		.'<input class="textfield" type="text" id="auth_email" name="auth_email" value="'.$auth_email.'" />'
		.'<div class="title"><label for="auth_url">'._AUTH_URL.'</label></div>'
		.'<input class="textfield" type="text" id="auth_url" name="auth_url" value="'.$auth_url.'" /><br /><br />';


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
		.'<label><input type="checkbox" name="cancomment" value="1"'
		.( $cancomment ? ' checked="checked"' : '' )
		.' />&nbsp;'._ALLOW_COMMENTS.'</label><br /><br />'


		.'<input class="button" type="submit" value="'._SAVE.'" />';


		echo("<br /><br /><b>\n");
		echo($lang->def("_FILEINFO")."</b><br />\n");

		info_lang_table($idDocs);

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
		$url="index.php?modname=docs&amp;op=docs_editinfo&amp;lang=$val&amp;idDocs=$id";
		$link="<a href=\"$url\">$img</a>";

		$line=array($val, $link);
		$res.=$table->WriteRow($line);
	}

	$res.=$table->CloseTable();

	return $res;
}


function updocs() {
	checkPerm('mod');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$folder_id =(int)$_POST['idFolder']; //&
	docs_checkTreePerm($folder_id);

	//load info
	$idDocs=(int)$_POST["idDocs"];
	list($old_real_fname) = mysql_fetch_row(mysql_query("
		SELECT real_fname
		FROM ".$GLOBALS["prefix_cms"]."_docs
		WHERE idDocs  = '".$idDocs."'"));


	$fname=$_FILES["file"]["name"];
	$real_fname=time().rand(10,99)."_".$fname;
	$tmp_fname=$_FILES["file"]["tmp_name"];

	sl_open_fileoperations();
	$f1=sl_upload($tmp_fname, _FPATH_INTERNAL.$real_fname);
	sl_close_fileoperations();

	$ts_pub=0;
	$ts_exp=0;
	$period_ok=true;

	if (( ($f1) || ($fname == "") ) && ($period_ok)) {

		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_docs SET ";
		if ($fname != "") {
			$qtxt.="fname = '".$fname."', real_fname = '".addslashes($real_fname)."',";
			// Cancello il vecchio file:
			sl_unlink(_FPATH_INTERNAL.$old_real_fname);
		}

		$arr=get_pubexp_info();
		$pubdate=(!empty($arr["pubdate"]) ? "'".$arr["pubdate"]."'" : "NULL");
		$expdate=(!empty($arr["expdate"]) ? "'".$arr["expdate"]."'" : "NULL");
		
		$date = $_POST['date'];
		
		if($date == '')
			$publish_date = date('Y-m-d H:i:s');
		else
			$publish_date = $GLOBALS['regset']->RegionalToDatabase($date.' '.$_POST['time_h'].':'.$_POST['time_m'].':00', 'datetime');
		
		$qtxt.="publish_date = '".$publish_date."', 
			author = '".$_POST['author']."',
			auth_email = '".$_POST['auth_email']."',
			auth_url = '".$_POST['auth_url']."',
			important = '".$_POST['important']."',
			cancomment = '".$_POST['cancomment']."',
			pubdate = ".$pubdate.",
			expdate = ".$expdate."
			WHERE idDocs = '".$_POST['idDocs']."'";

		if(!mysql_query($qtxt)) {
			errorCommunication(_INSERR);
			return;
		}
		else {
			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[fname]"=>(!empty($fname) ? $fname : $_POST["old_fname"]));
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "DocumentModified",
					/* module:     */ "admin_docs",
					/* action:     */ "edit",
					/* log:        */ "Edited document ".$_POST['idDocs'],
					/* sub_string: */ "_DOCS_EDITED_ALERT_SUB",
					/* txt_string: */ "_DOCS_EDITED_ALERT_TXT",
					/* replace:    */ $replace
				);
		}

		jumpTo('index.php?modname=docs&op=docs');
	}
	else {
		echo("<div class=\"std_block\">\n");
		echo("<b>"._INSERR."</b><br /><br />");
		echo("<a href=\"javascript:history.go(-1);\">&lt;&lt; "._BACK."</a>\n");
		echo("</div>\n");
		unlink_uploads(array(_FPATH_INTERNAL.$real_fname));
	}
}

//----------------------------------------------------------------------------

function deldocs( $treeView=FALSE ) {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

	if ($treeView !== FALSE) { //&
		$folder_id = (int)$treeView->getSelectedFolderId();
	}
	else if (isset($_POST["folder_id"])) {
		$folder_id=(int)$_POST["folder_id"];
	}
	else {
		$folder_id =0;
	}

	docs_checkTreePerm($folder_id); //&

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=docs&op=docs");
	}
	else if (isset($_POST["conf_del"])) {

		$id=(int)$_POST["id"];

		$qtxt="SELECT real_fname FROM ".$GLOBALS["prefix_cms"]."_docs WHERE idDocs='$id';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$real_fname=$row["real_fname"];

			@sl_unlink(_FPATH_INTERNAL.$real_fname);
		}

		$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_docs WHERE idDocs='$id';";
		$q=mysql_query($qtxt);

		jumpTo("index.php?modname=docs&op=docs");
	}
	else {

		//load info
		$id=(int)$treeView->getDocsSelected();
		list($fname) = mysql_fetch_row(mysql_query("
		SELECT fname
		FROM ".$GLOBALS["prefix_cms"]."_docs
		WHERE idDocs  = '".$id."'"));

		$back_ui_url="index.php?modname=docs&amp;op=docs";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_DOCS");
		$title_arr[]=$lang->def("_DELETE_DOCS").": ".$fname;
		$out->add(getTitleArea($title_arr, "docs"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("docs_form", "index.php?modname=docs&amp;op=deldocs"));

		$out->add($form->getHidden("id", "id", $id));
		$out->add($form->getHidden("folder_id", "folder_id", $folder_id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_FILENAME').' :</span> '.$fname.'<br />',
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
		$idDocs=(int)$_GET["idDocs"];
		list($real_fname) = mysql_fetch_row(mysql_query("
		SELECT real_fname
		FROM ".$GLOBALS["prefix_cms"]."_docs
		WHERE idDocs  = '".$idDocs."'"));

		@sl_unlink(_FPATH_INTERNAL.$real_fname);

		if(!mysql_query("
		DELETE FROM ".$GLOBALS["prefix_cms"]."_docs
		WHERE idDocs  = '".$idDocs."'")) {
			addTitleArea('docs');
			errorCommunication(_ERRREM);
			return;
		}

		jumpTo('index.php?modname=docs&op=docs');

	}
	else {

		//load info
		$idd=(int)$treeView->getDocsSelected();
		list($fname) = mysql_fetch_row(mysql_query("
		SELECT fname
		FROM ".$GLOBALS["prefix_cms"]."_docs
		WHERE idDocs  = '".$idd."'"));

		echo '<div class="std_block">';

			echo '<b>'._AREYOUSUREDOCS.'</b><br />'
				.'<div class="evidenceBlock">'
				.'<b>'._FILENAME.' :</b> &quot;'.$fname.'&quot;<br /><br />'
				.'[ <a href="index.php?modname=docs&amp;op=deldocs&amp;idDocs='
				.(int)$treeView->getDocsSelected().'&amp;confirm=1">'._YES.'</a> | '
				.'<a href="index.php?modname=docs&amp;op=docs">'._NO.'</a> ]'
				.'</div>';

		echo '</div>';
	}

}


function docs_editinfo() {
	checkPerm('mod');


	require_once( $GLOBALS["where_cms"].'/admin/modules/docs/docs_class.php' );
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$sel_lang=$_GET["lang"];
	$idDocs=$_GET["idDocs"];
	$btn="do_moddocs_".$idDocs;

	docs_checkTreePerm(FALSE, FALSE, $idDocs); //&


	$home_url="index.php?modname=docs&amp;op=docs";
	$back_ui_url="index.php?modname=docs&amp;op=editdocs&amp;id=".$idDocs;
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_DOCS");
	$title_arr[$back_ui_url]=$lang->def("_EDIT_DOCS");
	$title_arr[]=$lang->def("_EDIT_DOCS_INFO");
	$out->add(getTitleArea($title_arr, "docs"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	$out->add($form->openForm("docs_form", "index.php?modname=docs&amp;op=docs"));

	$out->add($form->openElementSpace());


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_docs_info WHERE idd='$idDocs' AND lang='$sel_lang';";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$info=mysql_fetch_array($q);
		$out->add($form->getHidden("new", "new", "0"));
	}
	else {
		$info=array();
		$info["title"]=$lang->def("_TITLE");
		$info["keywords"]="";
		$info["sdesc"]=$lang->def("_SHORTDESC");
		$info["ldesc"]=$lang->def("_DESCRIPTION");
		$out->add($form->getHidden("new", "new", "1"));
	}

	$out->add($form->getHidden($btn, $btn, "1"));
	$out->add($form->getHidden("idDocs", "idDocs", $idDocs));
	$out->add($form->getHidden("lang", "lang", $sel_lang));
	$out->add($form->getHidden("save_docs_info", "save_docs_info", "1"));

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


function save_docs_info() {

	$idDocs=$_POST["idDocs"];
	docs_checkTreePerm(FALSE, FALSE, $idDocs); //&

	$lang=$_POST["lang"];
	$title=$_POST["title"];
	$keywords=$_POST["keywords"];
	$sdesc=$_POST["sdesc"];
	$ldesc=$_POST["ldesc"];
	$new=$_POST["new"];


	$qtxt="";
	if ($new) {
		$qtxt.="INSERT INTO ".$GLOBALS["prefix_cms"]."_docs_info (idd, title, keywords, sdesc, ldesc, lang) ";
		$qtxt.="VALUES ('$idDocs', '$title', '$keywords', ";
		$qtxt.="'$sdesc', '$ldesc', '$lang');";
	}
	else {
		$qtxt.="UPDATE ".$GLOBALS["prefix_cms"]."_docs_info SET title='$title', keywords='$keywords', ";
		$qtxt.="sdesc='$sdesc', ldesc='$ldesc' WHERE idd='$idDocs' AND lang='$lang';";
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
	include_once($GLOBALS["where_framework"]."/lib/lib.upload.php");

	foreach ($ul_files as $key=>$val) {
		sl_unlink($val);
	}

}

function docs_checkTreePerm($folder_id, $return_val=FALSE, $item_id=FALSE) { //&
	require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

	if (($item_id > 0) && ($folder_id === FALSE)) {
		list($folder_id) = mysql_fetch_row(mysql_query("
		SELECT idFolder
		FROM ".$GLOBALS["prefix_cms"]."_docs
		WHERE idDocs = '".$item_id."'"));
	}

	$ctp=new CmsTreePermissions("document");
	$res =$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, $return_val);

	if ($return_val)
		return $res;
}



$op=importVar("op");
switch($op) {
	case "docs" : {
		docs();
	};break;

	case "seldocshomepage" : {
		seldocshomepage();
	};break;
	case "docsonhome" : {
		docsonhome();
	};break;
	case "insdocs" : {
		if (isset($_POST["undo"]))
			docs();
		else
			insdocs();
	};break;

	case "editdocs" : {
		moddocs(FALSE, $_GET["id"]);
	};break;

	case "updocs" : {
		if (isset($_POST["undo"]))
			docs();
		else
			updocs();
	};break;

	case "deldocs" : {
		deldocs();
	};break;

	case "docs_editinfo" : {
		docs_editinfo();
	};break;

}

?>
