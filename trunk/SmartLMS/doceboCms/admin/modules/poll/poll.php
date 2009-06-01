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

require_once($GLOBALS['where_cms']."/admin/modules/poll/functions.php");


function poll_questions() {
	$write_perm=checkPerm('add', true);
	$mod_perm=checkPerm('mod', true);
	$rem_perm=checkPerm('del', true);

	require_once($GLOBALS['where_framework']."/lib/lib.typeone.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_poll', 'cms');
	$poll=new PollManager();

	$out->setWorkingZone("content");

	$out->add(getTitleArea($lang->def("_POLL_LIST"), "poll"));
	$out->add("<div class=\"std_block\">\n");

	$ini=importVar("ini", true, 0);

	$arr=$poll->getAllpoll();

	$table=new typeOne($GLOBALS["visuItem"]);
	$out->add($table->OpenTable(""));

	$head=array($lang->def("_QUESTION"),
		'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def("_ALT_MODANSWERS").'" title="'.$lang->def("_ALT_MODANSWERS").'" />',
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type=array('', 'img', 'img', 'img');

	$out->add($table->WriteHeader($head, $head_type));

	$tot=(count($arr) < ($ini+$GLOBALS["visuItem"])) ? count($arr) : $ini+$GLOBALS["visuItem"];
	for($i=$ini; $i<$tot; $i++ ) {
		$rowcnt=array();
		$rowcnt[]=$arr[$i]["question"];
		if ($mod_perm) {
			$btn ="<a href=\"index.php?modname=poll&amp;op=pollanswers&amp;id=".$arr[$i]["poll_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/modelem.gif\" ";
			$btn.="alt=\"".$lang->def("_ALT_MODANSWERS")."\" title=\"".$lang->def("_ALT_MODANSWERS")."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;

			$btn ="<a href=\"index.php?modname=poll&amp;op=editpoll&amp;id=".$arr[$i]["poll_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/mod.gif\" ";
			$btn.="alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD")."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else {
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
		}

		if ($rem_perm) {
			$btn ="<a href=\"index.php?modname=poll&amp;op=delpoll&amp;id=".$arr[$i]["poll_id"]."&amp;conf_del=1\" ".
						"title=\"".$lang->def('_DEL')." : '".$arr[$i]["question"]."'\">";
			$btn.="<img src=\"".getPathImage()."standard/rem.gif\" ";
			$btn.="alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else
			$rowcnt[]="&nbsp;";

		$out->add($table->writeRow($rowcnt));
	}

	if($write_perm) {
		$out->add($table->WriteAddRow('<a href="index.php?modname=poll&amp;op=addpoll">
		 <img src="'.getPathImage().'standard/add.gif" title="'.$lang->def( '_ADD' ).'" alt="'.$lang->def( '_ADD' ).'" /> '.
		 $lang->def( '_ADD' ).'</a>'));
	}

	if ($rem_perm) {
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delpoll]');
	}

	$out->add($table->CloseTable());

	$out->add($table->WriteNavBar('',
								'index.php?modname=poll&amp;op=poll&amp;ini=',
								$ini,
								count($arr)));

	$out->add("</div>\n");
}


function poll_editpoll($poll_id=0) {
	checkPerm('add');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_poll', 'cms');
	$form=new Form();

	$out->setWorkingZone("content");

	$back_ui_url="index.php?modname=poll&amp;op=poll";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_POLLS");

	$data=array();
	$form_code="";

	if ((int)$poll_id <= 0) {  // Add
		$form_code.=$form->openForm("poll_form", "index.php?modname=poll&amp;op=insnew");
		$submit_lbl=$lang->def("_INSERT");
		$question="";

		$title_arr[]=$lang->def("_ADD_POLL");
	}
	else {  // Edit
		$form_code.=$form->openForm("poll_form", "index.php?modname=poll&amp;op=updpoll");

		$poll=new PollManager();
		$arr=$poll->getPollInfo($poll_id);

		$question=$arr["question"];

		$title_arr[]=$lang->def("_EDIT_POLL").": ".$question;

		$submit_lbl=$lang->def("_MOD");
	}

	$out->add(getTitleArea($title_arr, "poll"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form_code.$form->openElementSpace());

	$out->add($form->getTextfield($lang->def("_QUESTION"), "question", "question", 255, $question));

	$out->add($form->getHidden("poll_id", "poll_id", $poll_id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $submit_lbl));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}



function poll_save() {
	checkPerm('mod');

	$poll=new PollManager();
	$poll->savePoll($_POST);

	jumpTo("index.php?modname=poll&op=poll");
}


function del_poll() {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_poll', 'cms');

	if (isset($_POST["canc_del"])) {
		header("location: index.php?modname=poll&op=poll");
	}
	else if ( get_req("conf_del", DOTY_INT, false)) {
		$id=get_req("id", DOTY_INT, false);

		$poll=new PollManager();
		$poll->deletePoll($id);

		header("location: index.php?modname=poll&op=poll");
	}
	else {

		$id=(int)importVar("id");

		$poll=new PollManager();
		$arr=$poll->getPollInfo($id);

		$question=$arr["question"];

		$back_ui_url="index.php?modname=poll&amp;op=poll";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_POLLS");
		$title_arr[]=$lang->def("_DELETE_POLL").": ".$question;
		$out->add(getTitleArea($title_arr, "poll"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("poll_form", "index.php?modname=poll&amp;op=delpoll&amp;id=$id"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_QUESTION').' :</span> '.$question.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}
}


function poll_answers($poll_id) {

	$write_perm=checkPerm('add', true);
	$mod_perm=checkPerm('mod', true);
	$rem_perm=checkPerm('del', true);

	require_once($GLOBALS['where_framework']."/lib/lib.typeone.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_poll', 'cms');
	$poll=new PollManager();

	$out->setWorkingZone("content");

	$arr=$poll->getPollInfo($poll_id);
	$question=$arr["question"];
	$back_ui_url="index.php?modname=poll&amp;op=poll";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_POLLS");
	$title_arr[]=$lang->def("_POLL_ANSWERS").": ".$question;
	$out->add(getTitleArea($title_arr, "poll"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$ini=importVar("ini", true, 0);

	$arr=$poll->getAllAnswers($poll_id);

	$table=new typeOne($GLOBALS["visuItem"]);
	$out->add($table->OpenTable(""));

	$head=array($lang->def("_ANSWER"),
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type=array('', /*'',*/ 'img', 'img');

	$out->add($table->WriteHeader($head, $head_type));

	$tot=(count($arr) < ($ini+$GLOBALS["visuItem"])) ? count($arr) : $ini+$GLOBALS["visuItem"];
	for($i=$ini; $i<$tot; $i++ ) {
		$rowcnt=array();
		$answer=$arr[$i]["answer_txt"];
		$rowcnt[]=$answer;
		if ($mod_perm) {
			$btn ="<a href=\"index.php?modname=poll&amp;op=editanswer&amp;id=".$poll_id;
			$btn.="&amp;answer=".$arr[$i]["answer_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/mod.gif\" ";
			$btn.="alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD")." ".$answer."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else {
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
		}

		if ($rem_perm) {
			$btn ="<a href=\"index.php?modname=poll&amp;op=delanswer&amp;id=".$poll_id;
			$btn.="&amp;answer=".$arr[$i]["answer_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/rem.gif\" ";
			$btn.="alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")." ".$answer."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else
			$rowcnt[]="&nbsp;";

		$out->add($table->writeRow($rowcnt));
	}

	if($write_perm) {
		$out->add($table->WriteAddRow('<a href="index.php?modname=poll&amp;op=addanswer&amp;id='.$poll_id.'">
		 <img src="'.getPathImage().'standard/add.gif" title="'.$lang->def( '_ADD' ).'" alt="'.$lang->def( '_ADD' ).'" /> '.
		 $lang->def( '_ADD' ).'</a>'));
	}

	$out->add($table->CloseTable());

	$out->add($table->WriteNavBar('',
								'index.php?modname=poll&amp;op=pollanswers&amp;id='.$poll_id.'&amp;ini=',
								$ini,
								count($arr)));

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}


function poll_editanswer($poll_id, $answer_id=0) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_poll', 'cms');
	$form=new Form();

	$out->setWorkingZone("content");

	$poll=new PollManager();
	$arr=$poll->getPollInfo($poll_id);
	$question=$arr["question"];
	$home_url="index.php?modname=poll&amp;op=poll";
	$back_ui_url="index.php?modname=poll&amp;op=pollanswers&amp;id=".$poll_id;
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_POLLS");
	$title_arr[$back_ui_url]=$lang->def("_POLL_ANSWERS").": ".$question;

	$data=array();
	$form_code="";

	if ((int)$answer_id == 0) {  // Add
		$form_code.=$form->openForm("poll_form", "index.php?modname=poll&amp;op=insanswer&amp;id=".$poll_id);
		$submit_lbl=$lang->def("_INSERT");

		$answer_txt=$lang->def("_ANSWER");

		$is_published=false;
		$title_arr[]=$lang->def("_ADD_POLL_ANSWER");
	}
	else {  // Edit
		$form_code.=$form->openForm("poll_form", "index.php?modname=poll&amp;op=updanswer&amp;id=".$poll_id);

		$poll=new PollManager();
	  	$answer_info=$poll->getAnswerInfo($answer_id);

		$answer_txt=$answer_info["answer_txt"];

		$submit_lbl=$lang->def("_MOD");
		$title_arr[]=$lang->def("_EDIT_POLL_ANSWER").": ".$answer_txt;
	}

	$out->add(getTitleArea($title_arr, "poll"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form_code.$form->openElementSpace());


	$out->add($form->getTextfield($lang->def("_ANSWER"), "answer_txt", "answer_txt", 255, $answer_txt));


	$out->add($form->getHidden("poll_id", "poll_id", $poll_id));
	$out->add($form->getHidden("answer_id", "answer_id", $answer_id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $submit_lbl));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());


	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}


function poll_saveanswer() {
	checkPerm('mod');

	$poll=new PollManager();
	$poll->saveAnswer($_POST);

	jumpTo("index.php?modname=poll&op=pollanswers&id=".$_POST["poll_id"]);
}


function poll_delanswer($poll_id, $answer_id) {
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_poll', 'cms');

	$back_url="index.php?modname=poll&op=pollanswers&id=".$poll_id;

	if (isset($_POST["canc_del"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$poll=new PollManager();
		$poll->deleteAnswer($answer_id);

		jumpTo($back_url);
	}
	else {

		$id=(int)importVar("id");

		$poll=new PollManager();
		$arr=$poll->getAnswerInfo($answer_id);

		$answer=$arr["answer_txt"];

		$arr=$poll->getPollInfo($poll_id);
		$question=$arr["question"];
		$home_url="index.php?modname=poll&amp;op=poll";
		$back_ui_url="index.php?modname=poll&amp;op=pollanswers&amp;id=".$poll_id;
		$title_arr=array();
		$title_arr[$home_url]=$lang->def("_POLLS");
		$title_arr[$back_ui_url]=$lang->def("_POLL_ANSWERS").": ".$question;
		$title_arr[]=$lang->def("_DELETE_ANSWER").": ".$answer;
		$out->add(getTitleArea($title_arr, "poll"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$url="index.php?modname=poll&amp;op=delanswer&amp;id=".$poll_id."&amp;answer=".$answer_id;
		$out->add($form->openForm("poll_form", $url));


		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_ANSWER').' :</span> '.$answer.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}
}




$op=importVar("op");

switch($op) {
	case "poll": {
		poll_questions();
	} break;

	case "addpoll": {
		poll_editpoll();
	} break;

	case "insnew": {
		if (isset($_POST["undo"]))
			poll_questions();
		else
			poll_save();
	} break;

	case "editpoll": {
		poll_editpoll((int)$_GET["id"]);
	} break;

	case "updpoll": {
		if (isset($_POST["undo"]))
			poll_questions();
		else
			poll_save();
	} break;

	case "delpoll": {
		del_poll();
	} break;

	case "pollanswers": {
		poll_answers((int)$_GET["id"]);
	} break;

	case "addanswer": {
		poll_editanswer((int)$_GET["id"]);
	} break;

	case "insanswer": {
		if (isset($_POST["undo"]))
			poll_answers((int)$_GET["id"]);
		else
			poll_saveanswer();
	} break;

	case "editanswer": {
		poll_editanswer((int)$_GET["id"], (int)$_GET["answer"]);
	} break;

	case "updanswer": {
		if (isset($_POST["undo"]))
			poll_questions((int)$_GET["id"]);
		else
			poll_saveanswer();
	} break;

	case "delanswer": {
		poll_delanswer((int)$_GET["id"], (int)$_GET["answer"]);
	} break;
}


?>
