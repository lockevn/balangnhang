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

require_once($GLOBALS["where_framework"]."/lib/lib.wiki.php");
require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");

// -- Url Manager Setup --
$mr_pattern="[P]/[P]/[P]/[P]/[O]/[P]";
$mr_items=array("mn", "pi", "op", "lang", "page");
$std_title="index";
$lang=0; // this will be loaded later by the CoreWikiPublic object
cmsUrlManagerSetup($mr_pattern, $mr_items, $std_title, "mn=wiki&pi=".getPI()."&op=show&lang=".$lang);
// -----------------------


addCss("style_wiki", "fw");


function &cwpSetup($wiki_id) {
	$res =new CoreWikiPublic($wiki_id);
	$can_edit =$res->checkWikiPerm($wiki_id, "edit", TRUE);
	$res->setInternalPerm("edit", $can_edit);
	return $res;
}


function cwpGlobalSetup($wiki_id) {

	if (!isset($GLOBALS["core_wiki_public"]))
		$GLOBALS["core_wiki_public"]=& cwpSetup($wiki_id);

}


function wikiMain($wiki_id) {
	if ((int)$wiki_id < 1)
		return FALSE;
	
	$pi = get_req('pi', DOTY_MIXED, 0);
	$lang = get_req('lang', DOTY_MIXED, '');
	$page = get_req('page', DOTY_MIXED, '');
	
	$res="";
	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	
	$can_view =$cwp->checkWikiPerm($wiki_id, "view", TRUE);
	$can_mod =$cwp->checkWikiPerm($wiki_id, "edit", TRUE);
	
	if(!$can_view && $can_mod)
		jumpTo('index.php?mn=wiki&pi='.$pi.'&op=edit&lang='.$lang.'&page='.$page);
	
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	
	$title=$cwp->lang->def("_WIKI");
	$res.=$cwp->titleArea($title);
	$res.=$cwp->getHead($can_mod, TRUE, TRUE, $can_view, $can_view);


	if ($can_view) {
		$res.=$cwp->getPageContent();
	}
	else {
		$res.=$cwp->lang->def("_CANT_VIEW_WIKI");
	}


	$res.=$cwp->getFooter(TRUE);
	$out->add($res);
}


function editWikiPage($wiki_id) {
	if ((int)$wiki_id < 1)
		return FALSE;
	
	$res="";
	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	
	$can_view =$cwp->checkWikiPerm($wiki_id, "view", TRUE);
	
	$cwp->checkWikiPerm($wiki_id, "edit");

	$page_code=$cwp->editWikiPage();

	if (!empty($page_code)) {
		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$title=$cwp->lang->def("_WIKI");
		$res.=$cwp->titleArea($title);
		$res.=$cwp->getHead(TRUE, FALSE, FALSE, FALSE, $can_view);

		//$res.=$cwp->getWikiToolbar();

		$res.=$page_code;

		$res.=$cwp->getFooter();
		$out->add($res);
	}
}


function wikiMap($wiki_id) {
	if ((int)$wiki_id < 1)
		return FALSE;
	
	if(isset($_POST['export_pdf']) || isset($_POST['export_all_pdf']))
		wikiPdfExport($wiki_id, isset($_POST['export_all_pdf']));
	
	$pi = get_req('pi', DOTY_MIXED, '');
	$lang = get_req('lang', DOTY_MIXED, '');
	$page = get_req('page', DOTY_MIXED, '');
	
	if(isset($_POST['del_selected']))
		jumpTo('index.php?mn=wiki&pi='.$pi.'&op=map&lang='.$lang.'&page='.$page.'&result='.(int)wikiPageDeleting($wiki_id));
	
	$res="";
	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	
	$can_view =$cwp->checkWikiPerm($wiki_id, "view", TRUE);
	
	$cwp->checkWikiPerm($wiki_id, "edit");

	$title=$cwp->lang->def("_WIKI");
	$res.=$cwp->titleArea($title);
	$res.=$cwp->getHead(TRUE, TRUE, FALSE, FALSE, $can_view);

	$res.=$cwp->wikiMap();

	$res.=$cwp->getFooter();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	
	if(isset($_GET['result']) && $_GET['result'])
		$out->add(getResultUi($cwp->lang->def("_OPERATION_SUCCESSFUL")));
	else
		$out->add(getErrorUi($cwp->lang->def("_OPERATION_FAILURE")));
	
	$out->add(	$res.Form::getButton('export_pdf', 'export_pdf', $cwp->lang->def('_EXPORT_WIKI'))
				.Form::getButton('export_all_pdf', 'export_all_pdf', $cwp->lang->def('_EXPORT_ALL'))
				.Form::getButton('del_selected', 'del_selected', $cwp->lang->def('_DEL')));
}

function wikiPdfExport($wiki_id, $all)
{
	$cwp =& cwpSetup($wiki_id);
	
	$query =	"SELECT title"
				." FROM ".$GLOBALS['prefix_fw']."_wiki"
				." WHERE wiki_id = '".$wiki_id."'";
	
	list($wiki_title) = mysql_fetch_row(mysql_query($query));
	
	$pages_selected = $_POST['page'];
	
	$page_code_array = array();
	
	$query =	"SELECT page_code"
				." FROM ".$GLOBALS['prefix_fw']."_wiki_page";
	
	if($all)
		$query .= " WHERE wiki_id = '".$wiki_id."'";
	else
		$query .= " WHERE page_id IN (".implode(',',$pages_selected).")";
	
	$result = mysql_query($query);
	
	while(list($page_code) = mysql_fetch_row($result))
		$page_code_array[] = $page_code;
	
	$query =	"SELECT p.page_id, p.page_code, i.title, r.content"
				." FROM ".$GLOBALS['prefix_fw']."_wiki_page as p"
				." JOIN ".$GLOBALS['prefix_fw']."_wiki_page_info as i ON p.page_id = i.page_id"
				." JOIN ".$GLOBALS['prefix_fw']."_wiki_revision as r ON p.page_id = r.page_id";
	if($all)
		$query .= " WHERE p.wiki_id = '".$wiki_id."'";
	else
		$query .= " WHERE p.page_id IN (".implode(',',$pages_selected).")";
	
	$query .=	" AND i.version = r.version"
				." AND i.language = '".$cwp->getWikiLanguage($wiki_id)."'"
				." AND r.language = '".$cwp->getWikiLanguage($wiki_id)."'"
				." ORDER BY p.page_path";
	
	$html = '';
	
	$result = mysql_query($query);
	
	while(list($page_id, $page_code, $title, $content) = mysql_fetch_row($result))
	{
		$content = str_replace('{site_base_url}', getSiteBaseUrl(), $content);
		
		$html .=	'<div style="page-break-after:always">'
					.'<a name="'.$page_code.'"><h2>'.htmlentities($title).'</h2></a>'
					.$cwp->parseWikiLinks($content, true, $page_code_array)
					.'</div>';
	}
	
	$html =	'<html><head>'
				.'<style>'
				.'body {font-family: helvetica;'
				.'margin: 30px 30px 30px 30px;}'
				.'</style>'
				.'</hedad><body>'
				.$html
				.'</body></html>';
	
	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	
	sendStrAsFile($html, getCleanTitle($wiki_title, 60).'.html', 'utf-8');
	
	//getPdfPhp5($html, getCleanTitle($wiki_title, 60).'.pdf');
}

function getPdfPhp5($html, $name, $orientation = 'P', $img = false)
{
	if($orientation == 'P' || $orientation == 'p')
		$orientation = 'portrait';
	else
		$orientation = 'landspace';
	
	require_once($GLOBALS['where_framework'].'/addons/dompdf/dompdf_config.inc.php');
	
	if(get_magic_quotes_gpc())
		$html = stripslashes($html);
	
	$dompdf = new DOMPDF();
	
	$old_limit = ini_set("memory_limit", "80M");
	
	if ($img != '')
		$html =	'<html><head>'
				.'<style>'
				.'body {background-image: url('.$img.') no-repeat;'
				.'margin: 30px 30px 30px 30px;'
				.'font-family: helvetica;}'
				.'</style>'
				.'</head><body>'
				.$html
				.'</body></html>';
	else
		$html =	'<html><head>'
				.'<style>'
				.'body {font-family: helvetica;'
				.'margin: 30px 30px 30px 30px;}'
				.'</style>'
				.'</hedad><body>'
				.$html
				.'</body></html>';
	
	$dompdf->load_html($html);
	$dompdf->set_paper('a4', $orientation);
	$dompdf->render();
	
	$dompdf->stream($name);
	
	 exit(0);
}

function wikiPageDeleting($wiki_id)
{
	if ((int)$wiki_id < 1)
		return FALSE;
	
	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	
	$pages_selected = $_POST['page'];
	
	return $cwp->deleteWikiPage($wiki_id, $pages_selected);
}

function wikiPageHistory($wiki_id) {
	if ((int)$wiki_id < 1)
		return FALSE;
	
	$res="";
	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	
	$can_view =$cwp->checkWikiPerm($wiki_id, "view", TRUE);
	
	$cwp->checkWikiPerm($wiki_id, "edit");

	$title=$cwp->lang->def("_WIKI");
	$res.=$cwp->titleArea($title);
	$res.=$cwp->getHead(TRUE, TRUE, FALSE, FALSE, $can_view);

	$res.=$cwp->wikiPageHistory();

	$res.=$cwp->getFooter();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$out->add($res);
}


function wikiSearch($wiki_id) {
	if ((int)$wiki_id < 1)
		return FALSE;
	
	$res="";
	$cwp=& cwpSetup($wiki_id);
	
	$can_view =$cwp->checkWikiPerm($wiki_id, "view", TRUE);
	
	$cwp->checkWikiPerm($wiki_id, "view");
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$cwp->lang->def("_WIKI");
	$res.=$cwp->titleArea($title);
	$res.=$cwp->getHead(TRUE, FALSE, FALSE, TRUE, $can_view);


	$res.=$cwp->getFoundPages();


	$res.=$cwp->getFooter(FALSE);
	$out->add($res);
}


function addeditFaq($wiki_id, $todo) {
	$res="";
	
	$um=& UrlManager::getInstance();

	if (isset($_GET["mr_str"]))
		$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);


	if ((isset($_GET["faqid"])) && ($_GET["faqid"] > 0))
		$faq_id=(int)$_GET["faqid"];
	else if ($todo == "add")
		$faq_id=0;
	else
		return FALSE;


	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	
	$can_view =$cwp->checkWikiPerm($wiki_id, "view", TRUE);

	if ($todo == "edit") {
		$info=$cwp->faqManager->getFaqInfo($faq_id);
		$title_label=$cwp->lang->def("_EDIT_FAQ").": ".$info["title"];
	}
	else if ($todo == "add") {
		$title_label=$cwp->lang->def("_ADD_FAQ");
	}

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$back_url=$um->getUrl();
	$info=$cwp->faqManager->getCalendarInfo($wiki_id);
	$title[$back_url]=$info["title"];
	$title[]=$title_label;
	$res.=$cwp->titleArea($title);
	$res.=$cwp->getHead(TRUE, TRUE, FALSE, FALSE, $can_view);
	$res.=$cwp->backUi($back_url);

	$res.=$cwp->addeditFaq($wiki_id, $faq_id, $todo);

	$res.=$cwp->backUi($back_url);
	$res.=$cwp->getFooter();
	$out->add($res);
}


function saveFaq($wiki_id) {

	cwpGlobalSetup($wiki_id);
	$cwp=& $GLOBALS["core_wiki_public"];
	$cwp->saveFaq($wiki_id);

}


?>
