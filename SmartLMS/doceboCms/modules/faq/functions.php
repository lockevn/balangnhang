<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

require_once($GLOBALS["where_framework"]."/lib/lib.faq.php");

// -- Url Manager Setup --
$mr_pattern="[P]/[P]/[P]/[O]/[T].html";
$mr_items=array("mn", "pi", "op");
$std_title="index";
cmsUrlManagerSetup($mr_pattern, $mr_items, $std_title, "mn=faq&pi=".getPI()."&op=main");
// -----------------------


$GLOBALS["page"]->add("<link href=\"".getPathTemplate()."style/style_faq.css\" rel=\"stylesheet\" type=\"text/css\" />"."\n", "page_head");


function cfpSetup() {

	if (!isset($GLOBALS["core_faq_public"]))
		$GLOBALS["core_faq_public"]=new CoreFaqPublic();

}


function showFaqList($cat_id) {
	if ((int)$cat_id < 1)
		return FALSE;

	$res="";
	cfpSetup();
	$cfp=& $GLOBALS["core_faq_public"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$cfp->lang->def("_FAQ");
	$res.=$cfp->titleArea($title);
	$res.=$cfp->getHead();

	$res.=$cfp->showFaqList($cat_id);

	$res.=$cfp->getFooter();
	$out->add($res);
}


function setSearch() {
	cfpSetup();
	$cfp=& $GLOBALS["core_faq_public"];
	$cfp->setSearch();
}


function addeditFaq($cat_id, $todo) {
	$res="";

	$um =& UrlManager::getInstance();

	if (isset($_GET["mr_str"]))
		$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);


	if ((isset($_GET["faqid"])) && ($_GET["faqid"] > 0))
		$faq_id=(int)$_GET["faqid"];
	else if ($todo == "add")
		$faq_id=0;
	else
		return FALSE;


	cfpSetup();
	$cfp=& $GLOBALS["core_faq_public"];

	if ($todo == "edit") {
		$info=$cfp->faqManager->getFaqInfo($faq_id);
		$title_label=$cfp->lang->def("_EDIT_FAQ").": ".$info["title"];
	}
	else if ($todo == "add") {
		$title_label=$cfp->lang->def("_ADD_FAQ");
	}

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$back_url=$um->getUrl();
	$info=$cfp->faqManager->getCategoryInfo($cat_id);
	$title[$back_url]=$info["title"];
	$title[]=$title_label;
	$res.=$cfp->titleArea($title);
	$res.=$cfp->getHead();
	$res.=$cfp->backUi($back_url);

	$res.=$cfp->addeditFaq($cat_id, $faq_id, $todo);

	$res.=$cfp->backUi($back_url);
	$res.=$cfp->getFooter();
	$out->add($res);
}


function saveFaq($cat_id) {

	cfpSetup();
	$cfp=& $GLOBALS["core_faq_public"];
	$cfp->saveFaq($cat_id);

}


?>
