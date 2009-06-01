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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

require_once($GLOBALS['where_cms']."/admin/modules/poll/functions.php");


function poll_show_main($poll_id) {

	$poll=new PollManager();
	$poll_info=$poll->getPollInfo($poll_id);

	if ($poll_id < 1)
		return "";

	$out=& $GLOBALS['page'];
	$out->setWorkingZone("content");

	$out->add("<h4 class=\"poll_title\">".$poll_info["question"]."</h4>\n");

	if ($poll->alreadyVoted($poll_id)) {
		show_poll_results($poll_id);
	}
	else {
		poll_vote_form($poll_id);
	}

}


function poll_vote_form($poll_id) {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('poll', 'cms');
	$form=new Form();
	$poll=new PollManager();

	$form_name="poll_form_".$poll_id;
	$out->add($form->openForm($form_name, "index.php?mn=poll&amp;pi=".getPI()."&amp;op=poll", "std_form poll_form"));

	$out->add($form->openElementSpace());

	$answer_arr=$poll->getAllAnswers($poll_id);

	foreach ($answer_arr as $key=>$val) {
		$radio_name="answer";
		$radio_id="answer_".$val["answer_id"];
		$out->add($form->getRadio($val["answer_txt"], $radio_id, $radio_name, $val["answer_id"]));
	}

	$out->add($form->getHidden("poll_id", "poll_id", $poll_id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('vote', 'vote', $lang->def("_VOTE")));
	$out->add($form->getButton('show_results', 'show_results', $lang->def("_SHOW_RESULTS")));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

}


function save_poll_vote() {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('poll', 'cms');

	if ((!isset($_POST["poll_id"])) || ($_POST["poll_id"] < 0)) {
		$out->add($lang->def("_NO_POLL_SELECTED"));
	}
	else if (!isset($_POST["answer"])) {
		$out->add($lang->def("_MUST_SPEC_ANSWER"));
	}
	else {
		$poll=new PollManager();
		$poll->savePollVote($_POST["poll_id"], $_POST["answer"]);

		show_poll_results($_POST["poll_id"], false);
	}

}


function show_poll_results($poll_id, $compact=true) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance('poll', 'cms');
	$poll=new PollManager();

	$poll_info=$poll->getPollInfo($poll_id);
	$answer_arr=$poll->getPollResult($poll_id);

	if (!$compact) {
		$out->add("<h2>".$lang->def("_POLL_RESULTS").": ".$poll_info["question"]."</h2>\n");
	}

	$out->add("<ul class=\"poll_res_list\">");
	foreach ($answer_arr as $key=>$val) {
		$out->add("<li>");
		$out->add($val["answer_txt"].": ".$val["votes"]." (".$val["percent"]."%)");
		$out->add("</li>\n");
	}
	$out->add("</ul>\n");

}


?>
