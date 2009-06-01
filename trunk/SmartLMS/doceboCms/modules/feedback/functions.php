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
// ---------------------------------------------------------------------------

require_once($GLOBALS["where_cms"]."/lib/lib.manModules.php");
require_once($GLOBALS["where_cms"]."/admin/modules/form/man_form.php");

function check_feedback_perm($pb, $feedback=0) {
	// Controllo che l'utente possa visualizzare il blocco..

	return true;

	$user_grp=getUserGroup();
	$allowed_grp=db_block_groups($pb);
	$can_see=can_see_block($user_grp, $allowed_grp);

	if (!$feedback)
		if (!$can_see) die("You can't access!");
	else
		return $can_see;
}


function show_feedback_mask($pb, $form_id) {

	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	$form=new Form();
	$fields=new FieldList();

	getPageId();
	setPageId($GLOBALS["area_id"], $pb);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('feedback', 'cms');

	$out->add("<div class=\"cms_form_box\">\n");

	$out->add($form->openForm("feedback_form", "index.php?mn=feedback&amp;op=sendform&amp;pi=".getPI()));

	$out->add($form->openElementSpace());

	$out->add($form->getTextfield($lang->def("_FROMEMAIL")." *", "fromemail", "fromemail", 255));

	$form_info=getFormInfo($form_id);


	$field_info=getFormFieldInfo($form_id);
	$field_array=$field_info["fields"];
	$mandatory=$field_info["mandatory"];


	$out->add($fields->playSpecFields($field_array, $mandatory));


	$out->add($form->getHidden("form_id", "form_id", $form_id));

	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('send', 'send', $lang->def("_SEND")));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add("</div>\n"); // cms_form_box

	//getFeedbackSendId("giovanni@docebo.com");
}



function getFormInfo($form_id) {
	$res=FALSE;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='".$form_id."';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$res=mysql_fetch_assoc($q);
	}

	return $res;
}



function getFormFieldInfo($form_id) {

	$res=array();

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='".$form_id."' ORDER BY ord";
	$q=mysql_query($qtxt);

	$field_array=array();
	$mandatory=false;
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$field_array[]=$row["idField"];
			$mandatory[$row["idField"]]=(bool)$row["comp"];
		}
	}

	$res["fields"]=$field_array;
	$res["mandatory"]=$mandatory;

	return $res;
}


function getFeedbackSendId($token=FALSE, $force_int=FALSE) {
	$res="";

		$res.=mt_rand(1000, 9999);

	if (($token !== FALSE) && (!empty($token))) {

		if ($force_int) {
			$token=preg_replace("/\\D/", "", md5($token));
		}
		else {

			if (strpos($token, "@") !== FALSE) {
				$token=preg_replace("/(@.*?\$)/", "", $token);
			}

			$token=str_shuffle($token);
		}

		$res.=$token;
	}
	else {
		$res.=mt_rand(1000, 9999);
	}

	$res.=time();

	return $res;
}


function getExtraFormFields($form_type) {
	$lang=& DoceboLanguage::createInstance('feedback', 'cms');
	$res=array();

	//TODO: if necessary implement some code that uses the
	//compulsory attribute. (not yet used / fully implemented)

	switch($form_type) {
		case "normal": {
			// nothing to do
		} break;
		case "crm_contact": {
			$res["firstname"]=array("label"=>$lang->def("_FIRSTNAME"),
			                        "compulsory"=>FALSE,
			                        "type"=>"text_field");
			$res["lastname"]=array("label"=>$lang->def("_LASTNAME"),
			                        "compulsory"=>FALSE,
			                        "type"=>"text_field");
			$res["description"]=array("label"=>$lang->def("_DESCRIPTION"),
			                        "compulsory"=>FALSE,
			                        "type"=>"simple_text_area");
		} break;
	}

	return $res;
}


function storeFormInfo($email, $form_info, $arr_fields) {
	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");

	$tab_info=$GLOBALS["prefix_cms"]."_form_sendinfo";
	$tab_storage=$GLOBALS["prefix_cms"]."_form_storage";

	if ($GLOBALS["current_user"]->isLoggedIn()) {
		$user_id=$GLOBALS["current_user"]->getIdSt();
	}
	else {
		$user_id="0";
	}
	$form_id=(int)$form_info["idForm"];
	$form_type=$form_info["form_type"];

	$common_field="form_id, form_type, send_date, email, user_id";
	$common_val="'".$form_id."', '".$form_type."', NOW(), '".$email."', '".$user_id."'";


	$qtxt ="INSERT INTO ".$tab_info." (".$common_field.") ";
	$qtxt.="VALUES (".$common_val.")";
	$q=mysql_query($qtxt);

	if ($q) {
		$send_id=mysql_insert_id();
	}
	else {
		$send_id=0;
	}

	if ($send_id > 0) {
		$fl=new FieldList();
		$fl->setFieldEntryTable($tab_storage);
		$fl->storeDirectFieldsForUser($send_id, $arr_fields, FALSE);
	}

}


?>
