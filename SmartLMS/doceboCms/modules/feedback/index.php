<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
check_feedback_perm($GLOBALS["pb"]);
// ---------------------------------------------------------------------------


$css=getModuleCss($GLOBALS["pb"]);
$GLOBALS["page"]->add("<div class=\"".$css."\">\n", "content");
$GLOBALS["page"]->add(getModuleBlockTitle($GLOBALS["pb"]), "content");


if($GLOBALS["cms"]["use_mod_rewrite"] == 'on')
{
	list($title, $mr_title) = mysql_fetch_row(mysql_query(	"SELECT title, mr_title"
															." FROM ".$GLOBALS["prefix_cms"]."_area"
															." WHERE idArea = '".$GLOBALS["area_id"]."'"));
	
	if ($mr_title != "")
		$page_title = format_mod_rewrite_title($mr_title);
	else
		$page_title = format_mod_rewrite_title($title);
	
	$backurl = 'page/'.$GLOBALS["area_id"].'/'.$page_title.'.html';
}
else
	$backurl = "index.php?special=changearea&amp;newArea=".$GLOBALS["area_id"];

$GLOBALS["page"]->add("<div style=\"text-align: right\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");


$op=importVar("op");
switch ($op) {

		default : {
			$opt=loadBlockOption($GLOBALS["pb"]);
			show_feedback_mask($pb, $opt["form_id"]);
		};break;

		case "sendform" : {
			send_message($GLOBALS["pb"]);
		};break;

}


$GLOBALS["page"]->add("<div style=\"text-align: right\">\n", "content");
$GLOBALS["page"]->add("<a class=\"back_link\" href=\"$backurl\">".def("_BACK")."</a></div>\n", "content");
$GLOBALS["page"]->add("</div>\n", "content");



function send_message($pb) {

	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");

	$fields=new FieldList();

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('feedback', 'cms');


	$form_id=(int)$_POST["form_id"];
	$fromemail=str_replace(array("\r", "\r"), "", $_POST["fromemail"]);

	$field_info=getFormFieldInfo($form_id);


	$mandatory_only=array();
	foreach($field_info["mandatory"] as $key=>$val) {
		if ($val)
			$mandatory_only[]=$key;
	}

	$all_mandatory=$fields->isFilledSpecFields($mandatory_only);

	if (($all_mandatory) && (!empty($_POST["fromemail"]))) {

		$form_info=getFormInfo($form_id);

		if ($form_info !== FALSE) {
			$filled_val=$fields->getFilledSpecVal($field_info["fields"], false, true);
			
			$msg="\r\n";

			$arr_fields=array();
			foreach($filled_val as $field_id=>$val) {

				$msg.=$val["description"].": ".$val["value"]."\n\n";
				$arr_fields[$field_id]=$val["value"];

			}


			// -- Fields database storage ---------------------------------------------

			if ($form_info["storeinfo"])
				storeFormInfo($fromemail, $form_info, $arr_fields);
			
			// ------------------------------------------------------------------------


			$email_list=explode("\n", $form_info["email"]);
			$sub=$form_info["title"];

			$sub=stripslashes(translateChr($sub, getTranslateTable(), true));
			
			$msg = str_replace('\r\n', "\r\n", $msg);
			
			$msg = str_replace("\r", "\n", $msg);
			$msg = str_replace("\n\n", "\n", $msg);
			$msg = str_replace("\n", "<br>", $msg);
			
			$msg=stripslashes(translateChr($msg, getTranslateTable(), true));
			
			$msg.="<br>User Agent: ".$_SERVER['HTTP_USER_AGENT']."<br>IP: ".$_SERVER['REMOTE_ADDR'];
			
			$msg = kses($msg, array('br' => array()));
			
			$error = false;
			
			require_once($GLOBALS['where_framework'].'/lib/lib.mailer.php');
			
			$mailer = DoceboMailer::getInstance();
			
			foreach((array)$email_list as $key=>$val)
				if ($val != "")
					if (!$mailer->SendMail($fromemail, $val, $sub, $msg, false, array(MAIL_REPLYTO => $fromemail, MAIL_SENDER_ACLNAME => false)))
						$error = true;
			if($error)
				$out->add(getErrorUi($lang->def("_FEEDBACK_FIELDREQ")));
			else
				$out->add(getResultUi($lang->def("_FEEDBACK_SUCCESS")));
		}
	}
	else
		$out->add(getErrorUi($lang->def("_FEEDBACK_FIELDREQ")));
}

?>