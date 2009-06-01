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

addCss("style_simpleprj");
require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
require_once($GLOBALS["where_cms"]."/modules/simpleprj/define.simpleprj.php");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=simpleprj&pi=".getPI()."&op=main");
// -----------------------


$GLOBALS["page"]->add("<link href=\"".getPathTemplate()."style/style_faq.css\" rel=\"stylesheet\" type=\"text/css\" />"."\n", "page_head");



function showSimplePrjList() {

	require_once($GLOBALS["where_cms"]."/admin/modules/block_simpleprj/util.simpleprj.php");
	require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");

	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang =& DoceboLanguage::createInstance('simpleprj', "cms");
	$um =& UrlManager::getInstance();

	$title=$lang->def("_SIMPLEPRJ");
	$res.=getCmsTitleArea($title);
	$res.="<div class=\"std_block\">\n";

	$block_id =$GLOBALS["pb"];
	$prj_in_block =loadBlockSimplePrj($block_id);

	$acl_manager =& $GLOBALS["current_user"]->getAclManager();
	$user_idst =$GLOBALS["current_user"]->getIdSt();
	$roles =$acl_manager->getUserRoleFromPath($user_idst, "/cms/modules/simpleprj", "view");


	if ($roles !== FALSE) {
		$to_show =array_intersect($prj_in_block, $roles["role_info"]);
	}
	else {
		$to_show =array();
	}

	if (count($to_show) > 0) {

		$spm =new SimplePrjManager();

		$where ="project_id IN (".implode(",", $to_show).")";
		$data_info=$spm->getSimplePrjList(FALSE, FALSE, $where);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id =$data_arr[$i]["project_id"];

			$url =$um->getUrl("op=showprj&prjid=".$id);
			$res.="<div class=\"project_box\">";
			$res.="<p><a href=\"".$url."\">".$data_arr[$i]["title"]."</a></p>";
			$res.=$data_arr[$i]["description"];
			$res.="</div>";

		}
	}

	$res.="</div>\n";
	$out->add($res);
}


function showSimplePrj() {
	require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
	require_once($GLOBALS["where_cms"]."/modules/simpleprj/shared.simpleprj.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.ajax_comment.php");

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0)) {
		$project_id =$_GET["prjid"];
	}
	else {
		return FALSE;
	}

	$can_view =checkSimplePrjPerm($project_id, "view");
	$can_upload =checkSimplePrjPerm($project_id, "upload");
	$can_comment =checkSimplePrjPerm($project_id, "comment");
	$can_task =$can_upload;

	if (!$can_view)	die("You can't access!");

	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang =& DoceboLanguage::createInstance('simpleprj', "cms");
	$um =& UrlManager::getInstance();

	$spm =new SimplePrjManager();
	$prj_info =$spm->getSimplePrjInfo($project_id);

	$title =array();
	$url =$um->getUrl();
	$title[$url]=$lang->def("_SIMPLEPRJ");
	$title[]=$prj_info["title"];
	$res.=getCmsTitleArea($title);
	$res.="<div class=\"std_block\">\n";


	$spm =new SimplePrjManager();

	$ax_comm = new AjaxComment('simpleprj_doc', 'cms');
	$comment_count_arr = $ax_comm->getResourceCommentCount();
	addAjaxJs();
	addScriptaculousJs();
  //addYahooJs();

	addJs($GLOBALS["where_cms_relative"]."/modules/simpleprj/", "ajax.simpleprj.js");

	$mod_path =$GLOBALS['where_cms_relative']."/modules/simpleprj/";
	$GLOBALS['page']->add('<script type="text/javascript">'
		.' setup_simpleprj(\''.$mod_path.'ajax.simpleprj.php\'); '
		.'</script>', 'page_head');


	$res.='<div class="simpleprj_container">';
	$res.='<div class="simpleprj_right_box">';


	$data_info=$spm->getSimplePrjTaskList($project_id, FALSE, FALSE);
	$data_arr=$data_info["data_arr"];
	$db_tot=$data_info["data_tot"];
	$tot=count($data_arr);


	$toggle_img ="triangle_down";
	$toggle_title =$lang->def("_HIDE_BOX");
	$action_code ='onclick="toggleBox(\'task_container\', \'toggle_1\', \'task_container\', \''.getPathImage().'\'); return false;"';
	$toggle ='<a href="#" class="toggle" '.$action_code.'>';
	$toggle.='<img id="toggle_1" src="'.getPathImage().'simpleprj/'.$toggle_img.'.gif" ';
	$toggle.='alt="'.$toggle_title.'" title="'.$toggle_title.'" /></a>';
	$res.='<div class="task_box_title">';
	$res.=$toggle.$lang->def("_TASK_LIST")."</div>";
	$res.='<div class="task_container" id="task_container">';
	for($i=0; $i<$tot; $i++ ) {

		$res.=drawTaskLine($data_arr[$i], $project_id, $can_task, $mod_path);
	}

	if ($can_task) {

		$res.='<div id="add_task_container"></div>';
		$action_code ='onclick="showAddTask(\''.$project_id.'\'); Element.toggle(this); return false;" ';
		$res.='<a href="#" id="add_task_link" class="add_task" '.$action_code.'>'.$lang->def("_ADD_TASK")."</a>\n";
	}

	$res.="</div>"; // task_container


	$toggle_img ="triangle_left";
	$toggle_title =$lang->def("_SHOW_BOX");
	$action_code ='onclick="toggleBox(\'users_container\', \'toggle_2\', \'users_container\', \''.getPathImage().'\'); return false;"';
	$toggle ='<a href="#" class="toggle" '.$action_code.'>';
	$toggle.='<img id="toggle_2" src="'.getPathImage().'simpleprj/'.$toggle_img.'.gif" ';
	$toggle.='alt="'.$toggle_title.'" title="'.$toggle_title.'" /></a>';
	$res.='<div class="users_box_title">';
	$res.=$toggle.$lang->def("_USER_LIST")."</div>";
	$res.='<div style="display: none;" id="users_container" class="users_container">';
	$acl =& $GLOBALS["current_user"]->getAcl();
	$acl_manager =& $GLOBALS["current_user"]->getAclManager();
	$role_idst =$acl->getRoleST("/cms/modules/simpleprj/".$project_id."/view");
	/*$role_members =$acl_manager->getRoleMembers($role_idst);
	$all_members =$acl_manager->getGroupMembers($role_members);*/
  $all_members = $acl_manager->getAllRoleMembers($role_idst);
	$user_info =$acl_manager->getUsers($all_members); 
	/*$role_members =$acl_manager->getRoleMembers($role_idst);
	$all_members =$acl_manager->getGroupMembers($role_members);*/
	$all_members = $acl_manager->getAllRoleMembers($role_idst);
	$user_info =$acl_manager->getUsers($all_members);  

	if ((is_array($user_info)) && (count($user_info) > 0)) {
		$res.='<ul class="prj_user_list">';
		foreach($user_info as $user_idst=>$user) {
			$res.='<li>'.$acl_manager->relativeId($user[ACL_INFO_USERID]).'</li>';
		}
		$res.='</ul>';
	}
	$res.="</div>"; // users_container

	$res.="</div>\n"; // simpleprj_right_box


	$res.='<div class="simpleprj_left_box">';

	$data_info=$spm->getSimplePrjDocList($project_id, FALSE, FALSE);
	$data_arr=$data_info["data_arr"];
	$db_tot=$data_info["data_tot"];

	$tot=count($data_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id =$data_arr[$i]["file_id"];

		$fname =preg_replace("/\d*_\d*_\d*_/", "", $data_arr[$i]["fname"]);

		$res.='<div class="prj_doc_box">';
		$img ='<img src="'.getPathImage().mimeDetect($fname).'" alt="'.$fname.'" title="'.$fname.'" />';
		$res.='<span class="fileicon">'.$img."</span>\n";
		$res.='<p class="doctitle">'.$data_arr[$i]["title"].'</p>'."\n";
		$res.='<p class="prj_doc_description">'.$data_arr[$i]["description"].'</p>'."\n";
		$res.='<ul class="prj_doc_actions">'."\n";

		$url =$um->getUrl("op=downloaddoc&prjid=".$project_id."&doc_id=".$id);
		$res.='<li class="download_doc"><a href="'.$url.'">';
		$res.='<span>'.$lang->def("_DOWNLOAD").'</span></a></li>';

		if ($can_upload) {

			$url =$um->getUrl("op=editdoc&prjid=".$project_id."&doc_id=".$id);
			$res.='<li class="edit_doc"><a href="'.$url.'">';
			$res.='<span>'.$lang->def("_MOD").'</span></a></li>';
			$url =$um->getUrl("op=deldoc&prjid=".$project_id."&doc_id=".$id);
			$res.='<li class="delete_doc"><a href="'.$url.'">';
			$res.='<span>'.$lang->def('_DEL').'</span></a></li>';
		}
		$res.="</ul>\n";
		$res.="</div>\n"; // prj_doc_box
	}


	if ($can_upload) {
		$res.='<ul class="simpleprj_actions">';
		$url =$um->getUrl("op=attach&prjid=".$project_id);
		$res.="<li><a class=\"add_btn\" href=\"".$url."\">".$lang->def("_ATTACH_DOCUMENT")."</a></li>";
		$res.="</ul>\n";
	}


	if ($can_comment) {
		$res.='<div class="comments_title">'.$lang->def("_COMMENTS")."</div>\n";
		$ax_comm = new AjaxComment('simpleprj_prj', 'cms');
		$ax_rend = new AjaxCommentRender('simpleprj', 'cms');

		$ax_comm->canReply($can_comment);

		$content = '';
		$comments = $ax_comm->getCommentByResourceKey($project_id);
		$ax_rend->setCommentToDisplay($comments);
		$content.='<div id="comment_div_'.$project_id.'">';
		while(!$ax_rend->isEnd()) {

			$content .= $ax_rend->nextComment();
		}
		$content.="</div>\n";
		$content.= $ax_rend->getAddCommentMask($project_id);
		$res.=$content;
	}

	$res.="</div>\n"; // simpleprj_left_box
	$res.="</div>\n"; // simpleprj_container


	$res.="</div>\n";
	$out->add($res);
}


function simplePrjAddEditDoc() {
	require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0)) {
		$project_id =$_GET["prjid"];
	}
	else {
		return FALSE;
	}

	if ((isset($_GET["doc_id"]))) {
		$doc_id =(int)$_GET["doc_id"];
	}
	else {
		$doc_id =0;
	}

	$can_upload =checkSimplePrjPerm($project_id, "upload");
	if (!$can_upload)	die("You can't access!");

	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang =& DoceboLanguage::createInstance('simpleprj', "cms");
	$um =& UrlManager::getInstance();

	$spm =new SimplePrjManager();
	$prj_info =$spm->getSimplePrjInfo($project_id);

	$title =array();
	$back_url =$um->getUrl();
	$title[$back_url]=$lang->def("_SIMPLEPRJ");
	$url =$um->getUrl("op=showprj&prjid=".$project_id);
	$title[$url]=$prj_info["title"];
	if ($doc_id < 1)
		$title[]=$lang->def("_ATTACH_DOCUMENT");
	else {
		$doc_info =$spm->getSimplePrjDocInfo($doc_id, $project_id);
		$title[]=$lang->def("_MOD").": ".$doc_info["title"];
	}
	$res.=getCmsTitleArea($title);
	$res.="<div class=\"std_block\">\n";


	if ($doc_id < 1) {

		$title ="";
		$description ="";
		$old_document ="";
	}
	else {

		$title =$doc_info["title"];
		$description =$doc_info["description"];
		$old_document =$doc_info["fname"];
	}


	$form =new Form();
	$url =$um->getUrl("op=savedoc&prjid=".$project_id);
	$res.=$form->openForm("main_form", $url, "", "", "multipart/form-data");
	$res.=$form->openElementSpace();

	$res.=$form->getHidden("doc_id", "doc_id", $doc_id);
	$res.=$form->getFilefield($lang->def("_DOCUMENT"), "document", "document");
	$res.=$form->getHidden("old_document", "old_document", $old_document);
	$res.=$form->getTextfield($lang->def("_TITLE"), "title", "title", 255, $title);
	$res.=$form->getTextarea($lang->def("_DESCRIPTION"), "description", "description", $description);


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();


	$res.="</div>\n";
	$out->add($res);
}


function simplePrjSaveDoc() {
	require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0)) {
		$project_id =$_GET["prjid"];
	}
	else {
		return FALSE;
	}

	$can_upload =checkSimplePrjPerm($project_id, "upload");
	if (!$can_upload)	die("You can't access!");

	$um =& UrlManager::getInstance();


	if (!isset($_POST["undo"])) {
		$spm =new SimplePrjManager();
		$spm->saveDocument($project_id, $_POST);
	}


	$url =$um->getUrl("op=showprj&prjid=".$project_id);
	jumpTo($url);
}


function simplePrjDownloadDoc() {
	require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.download.php");

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0)) {
		$project_id =$_GET["prjid"];
	}
	else {
		return FALSE;
	}

	if ((isset($_GET["doc_id"])) && ($_GET["doc_id"] > 0)) {
		$doc_id =$_GET["doc_id"];
	}
	else {
		return FALSE;
	}

	$can_view =checkSimplePrjPerm($project_id, "view");
	if (!$can_view)	die("You can't access!");

	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang =& DoceboLanguage::createInstance('simpleprj', "cms");
	$um =& UrlManager::getInstance();

	$spm =new SimplePrjManager();
	$doc_info =$spm->getSimplePrjDocInfo($doc_id, $project_id);

	$fname =$doc_info["fname"];

	if (!empty($fname)) {
		$ext =end(explode(".", $fname));
		sendFile(_SP_FPATH_INTERNAL, $fname, $ext);
		return FALSE;
	}

	$um =& UrlManager::getInstance();
	jumpTo($um->getUrl("op=showprj&prjid=".$project_id));
}


function simplePrjDeleteDoc() {
	require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0)) {
		$project_id =$_GET["prjid"];
	}
	else {
		return FALSE;
	}

	if ((isset($_GET["doc_id"])) && ($_GET["doc_id"] > 0)) {
		$doc_id =$_GET["doc_id"];
	}
	else {
		return FALSE;
	}

	$can_upload =checkSimplePrjPerm($project_id, "upload");
	if (!$can_upload)	die("You can't access!");

	$res="";

	$um =& UrlManager::getInstance();
	$back_url =$um->getUrl("op=showprj&prjid=".$project_id);

	$spm =new SimplePrjManager();


	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$spm->deleteDocument($doc_id, $project_id);

		jumpTo($back_url);
	}
	else {
		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");
		$lang =& DoceboLanguage::createInstance('simpleprj', "cms");

		$info=$spm->getSimplePrjDocInfo($doc_id, $project_id);
		$title=$info["title"];
		$fname =preg_replace("/\d*_\d*_\d*_/", "", $info["fname"]);

		$form=new Form();

		$url=$um->getUrl("op=deldoc&prjid=".$project_id."&doc_id=".$doc_id);
		$res.=$form->openForm("delete_form", $url);

		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').':</span> '.$title." (".$fname.")".'<br />',
			false,
			'conf_del',
			'undo');

		$res.=$form->closeForm();
		$out->add($res);
	}
}


function checkSimplePrjPerm($project_id, $perm_name) {

	$role ="/cms/modules/simpleprj/".$project_id."/".$perm_name;
	$res =$GLOBALS["current_user"]->matchUserRole($role);

	return $res;
}



?>
