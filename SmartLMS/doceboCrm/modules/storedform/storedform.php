<?php
/*************************************************************************/
/* DOCEBO CRM - Customer Relationship Management                         */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @version  $Id: $
 */
// ----------------------------------------------------------------------------
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=storedform&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/storedform/lib.storedform.php");


function storedform() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("storedform", "crm");
	$um=& UrlManager::getInstance();


	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_STOREDFORM_TABLE_CAPTION");
	$table_summary=$lang->def("_STOREDFORM_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$sfman=new StoredFormManager();


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_STOREDFORM");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$head=array();
	$head[]=$lang->def("_FIRSTNAME");
	$head[]=$lang->def("_LASTNAME");
	$head[]=$lang->def("_EMAIL");
	$head[]=$lang->def("_DATE");


	$img ="<img src=\"".getPathImage()."standard/import_data.gif\" alt=\"".$lang->def("_IMPORT_CONTACT")."\" ";
	$img.="title=\"".$lang->def("_IMPORT_CONTACT")."\" />";
	$head[]=$img;

	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;

	$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;


	$head_type=array("", "", "", "", "image", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink($um->getUrl());

	$ini=$tab->getSelectedElement();


	$list=$sfman->getStoredFormList($ini, $vis_item, FALSE, TRUE);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["send_id"];

		$data=$sfman->getMappedData($list_arr[$i]["form_id"], $id);

		$firstname=$data["user"]["predefined"]["name"]["value"];
		$lastname=$data["user"]["predefined"]["lastname"]["value"];

		$rowcnt=array();

		$show_details=(isset($_SESSION["show_storedform_details"][$id]) ? TRUE : FALSE);

		$anchor_name ="jump_to_".$id;
		$url_qry="&op=toggledetails&send_id=".$id."#".$anchor_name;
		$short_lbl=substr($firstname, 0 , 20)."...";
		$url=$um->getUrl($url_qry);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_HIDE_DETAILS")." ".$short_lbl."\" ";
			$img.="title=\"".$lang->def("_HIDE_DETAILS")." ".$short_lbl."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_SHOW_DETAILS")." ".$short_lbl."\" ";
			$img.="title=\"".$lang->def("_SHOW_DETAILS")." ".$short_lbl."\" />";
		}
		$rowcnt[]="<a name=\"".$anchor_name."\" href=\"".$url."\">".$img.$firstname."</a>\n";

		$rowcnt[]=$lastname;

		$email=$list_arr[$i]["email"];
		$rowcnt[]="<a href=\"mailto:".$email."\">".$email."</a>";

		$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["send_date"]);


		$url=$um->getUrl("op=import&send_id=".$id);
		$img ="<img src=\"".getPathImage()."standard/import_data.gif\" alt=\"".$lang->def("_IMPORT_CONTACT")."\" ";
		$img.="title=\"".$lang->def("_IMPORT_CONTACT")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

		$url=$um->getUrl("op=edit&send_id=".$id);
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

		$url=$um->getUrl("op=del&send_id=".$id);
		$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
		$img.="title=\"".$lang->def("_DEL")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";


		$tab->addBody($rowcnt);
		if ($show_details) {
			$details=$sfman->getStoredFormDetails($email, $data);
			$tab->addBodyExpanded($details, "line_details");
		}
	}


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function toggleStoredFormDetails() {
	$ok=TRUE;
	$um=& UrlManager::getInstance();

	if ((isset($_GET["send_id"])) && ($_GET["send_id"] > 0))
			$send_id=$_GET["send_id"];
	else
		$ok=FALSE;

	if ($ok) {
		if (isset($_SESSION["show_storedform_details"][$send_id]))
			unset($_SESSION["show_storedform_details"][$send_id]);
		else {
			// Force to have no more than one at the same time
			if (isset($_SESSION["show_storedform_details"])) {
				unset($_SESSION["show_storedform_details"]);
			}
			$_SESSION["show_storedform_details"][$send_id]=1;
		}
	}

	jumpTo($um->getUrl());
}


function importContact() {
	$res="";

	if ((isset($_GET["send_id"])) && ($_GET["send_id"] > 0))
			$send_id=$_GET["send_id"];
	else
		return FALSE;

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");


	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("storedform", "crm");
	$um=& UrlManager::getInstance();
	$form=new Form();

	$sfman=new StoredFormManager();

	$form_info=$sfman->getStoredFormInfo($send_id);

	$data=$sfman->getMappedData($form_info["form_id"], $send_id);
	// print_r(	$data);

	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_STOREDFORM");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	// $url=$um->getUrl("op=do_import&send_id=".$send_id);
	$url =$um->getUrl("op=conf_company&send_id=".$send_id);
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();



	$res.=$form->getTextfield($lang->def("_EMAIL"), "email", "email", 255, $form_info["email"]);


	$userid=$data["user"]["predefined"]["userid"]["value"];
	$userid_label=$data["user"]["predefined"]["userid"]["description"];
	$res.=$form->getTextfield($userid_label, "userid", "userid", 255, $userid);
	$firstname=$data["user"]["predefined"]["name"]["value"];
	$firstname_label=$data["user"]["predefined"]["name"]["description"];
	$res.=$form->getTextfield($firstname_label, "firstname", "firstname", 255, $firstname);
	$lastname=$data["user"]["predefined"]["lastname"]["value"];
	$lastname_label=$data["user"]["predefined"]["lastname"]["description"];
	$res.=$form->getTextfield($lastname_label, "lastname", "lastname", 255, $lastname);


	foreach ($data["user"]["custom"] as $field_id=>$field_data) {
		$val=$field_data["value"];
		$label=$field_data["description"];
		$res.=$form->getTextfield($label, $field_id, $field_id, 255, $val);
	}


	$field_id="company";
	$val=$data["company"]["predefined"][$field_id]["value"];
	$label=$data["company"]["predefined"][$field_id]["description"];
	$res.=$form->getTextfield($label, $field_id, $field_id, 255, $val);

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	$cm=new CompanyManager();

	// ------------------------------------------------------- //
	$dropdown="";
	$dropdown.='<select class="dropdown" ';
	$dropdown.='id="ctype" name="ctype"  >';

	$dropdown_items=$cm->getCompanyTypeList();
	if (isset($data["company"]["predefined"]["ctype"]["value"]))
		$sel_value=$data["company"]["predefined"]["ctype"]["value"];
	else
		$sel_value=FALSE;

	foreach($dropdown_items["list"] as $key=>$val) {

		if (($sel_value !== FALSE) && (strtolower($sel_value) == strtolower($val)))
			$sel=' selected="selected"';
		else
			$sel="";

		$dropdown.='<option value="'.$key.'"'.$sel.'>'.$val.'</option>';
	}

	$dropdown.='</select>';
	$label=$data["company"]["predefined"]["ctype"]["description"];
	$res.=$form->getLineBox("<b>".$label."</b>", $dropdown);

	// ------------------------------------------------------- //

	$dropdown="";
	$dropdown.='<select class="dropdown" ';
	$dropdown.='id="cstatus" name="cstatus"  >';

	$dropdown_items=$cm->getCompanyStatusList();
	if (isset($data["company"]["predefined"]["cstatus"]["value"]))
		$sel_value=$data["company"]["predefined"]["cstatus"]["value"];
	else
		$sel_value=FALSE;

	foreach($dropdown_items["list"] as $key=>$val) {

		if (($sel_value !== FALSE) && (strtolower($sel_value) == strtolower($val)))
			$sel=' selected="selected"';
		else
			$sel="";

		$dropdown.='<option value="'.$key.'"'.$sel.'>'.$val.'</option>';
	}

	$dropdown.='</select>';
	$label=$data["company"]["predefined"]["cstatus"]["description"];
	$res.=$form->getLineBox("<b>".$label."</b>", $dropdown);
	// ------------------------------------------------------- //

	$field_id="address";
	$val=$data["company"]["predefined"][$field_id]["value"];
	$label=$data["company"]["predefined"][$field_id]["description"];
	$res.=$form->getTextfield($label, $field_id, $field_id, 255, $val);
	$field_id="tel";
	$val=$data["company"]["predefined"][$field_id]["value"];
	$label=$data["company"]["predefined"][$field_id]["description"];
	$res.=$form->getTextfield($label, $field_id, $field_id, 255, $val);
	$field_id="vat_number";
	$val=$data["company"]["predefined"][$field_id]["value"];
	$label=$data["company"]["predefined"][$field_id]["description"];
	$res.=$form->getTextfield($label, $field_id, $field_id, 255, $val);

	foreach ($data["company"]["custom"] as $field_id=>$field_data) {
		$val=$field_data["value"];
		$label=$field_data["description"];
		$res.=$form->getTextfield($label, $field_id, $field_id, 255, $val);
	}


	if (isset($data["chistory"]["predefined"]["description"])) {
		$field_id="description";
		$val=$data["chistory"]["predefined"][$field_id]["value"];
		$label=$data["chistory"]["predefined"][$field_id]["description"];
		$res.=$form->getSimpleTextarea($label, "chistory_desc", "chistory_desc", $val);
	}


	$res.=$form->closeElementSpace();

	$res.=$form->openButtonSpace();
	$res.=$form->getButton('import', 'import', $lang->def("_GO_ON"));
	$res.=$form->getButton('undo', 'undo', $lang->def("_UNDO"));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();


	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function doImportContact() {

	if ((isset($_GET["send_id"])) && ($_GET["send_id"] > 0))
			$send_id=$_GET["send_id"];
	else
		return FALSE;

	$import_data =unserialize(urldecode($_POST["import_data"]));
	$data =$_POST+$import_data;

	// -- save user --------------------------------------------------------

	require_once($GLOBALS["where_framework"]."/class/class.fieldmap_user.php");

	$fmu=new FieldMapUser();

	$predefined_data =array();
	$predefined_data["userid"]=$data["userid"];
	$predefined_data["firstname"]=$data["firstname"];
	$predefined_data["lastname"]=$data["lastname"];
	$predefined_data["pass"]=md5($data["userid"].rand(1000,9999));
	$predefined_data["email"]=$data["email"];

	$custom_fields_arr=$fmu->getCustomFields(FALSE);
	$custom_data=array();

	foreach($custom_fields_arr as $field_id=>$label) {
		if (isset($data[$field_id])) {
			$custom_data[$field_id]=$data[$field_id];
		}
	}

	$idst=$fmu->saveFields($predefined_data, $custom_data, 0, FALSE);
	unset($fmu);


	// -- save company -----------------------------------------------------

	require_once($GLOBALS["where_framework"]."/class/class.fieldmap_company.php");

	$fmc=new FieldMapCompany();

	switch ($_POST["company_to_use"]) {

		case "new": {

			$predefined_data =array();
			$predefined_data["id"]=0;
			$predefined_data["name"]=$data["company"];
			$predefined_data["ctype_id"]=$data["ctype"];
			$predefined_data["cstatus_id"]=$data["cstatus"];
			$predefined_data["address"]=$data["address"];
			$predefined_data["tel"]=$data["tel"];
			$predefined_data["email"]=$data["email"];
			$predefined_data["vat_number"]=$data["vat_number"];

			$custom_fields_arr=$fmc->getCustomFields(FALSE);
			$custom_data=array();

			foreach($custom_fields_arr as $field_id=>$label) {
				if (isset($data[$field_id])) {
					$custom_data[$field_id]=$data[$field_id];
				}
			}
		// i think the last parameter should be false for custom data and true for ctype and cstatus..
			$company_id=$fmc->saveFields($predefined_data, $custom_data, 0, FALSE);
			unset($fmc);
		} break;

		case "existing": {
				$company_id=(int)$_POST["existing_company_id"];
		} break;

	}


	// -- add user to company ----------------------------------------------

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	$cm=new CompanyManager();

	if ($company_id > 0) {
		$cm->updateCompanyUsers($company_id, array($idst));
	}


	// -- add contact to contact history -----------------------------------

	if ($company_id > 0) {

		require_once($GLOBALS["where_framework"]."/class/class.fieldmap_chistory.php");

		$fmch=new FieldMapChistory();

		$predefined_data =array(); // not sure about this.
		$predefined_data["title"]=substr($data["chistory_desc"], 0, 40)."...";
		$predefined_data["description"]=$data["chistory_desc"];
		$predefined_data["reason"]=0;
		$predefined_data["type"]="form";

		$predefined_data["company_id"]=$company_id;

		$chistory_id=$fmch->saveFields($predefined_data);
		unset($fmch);
	}

	// -- delete contact ---------------------------------------------------

	// ---------------------------------------------------------------------

	$um=& UrlManager::getInstance();
	jumpTo($um->getUrl());
}



function deleteContact() {
	$res="";

	if ((isset($_GET["send_id"])) && ($_GET["send_id"] > 0))
			$send_id=$_GET["send_id"];
	else
		return FALSE;

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("storedform", "crm");
	$um=& UrlManager::getInstance();


	$sfman=new StoredFormManager();
	$back_url=$um->getUrl();

	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$sfman->deleteStoredForm($send_id);

		jumpTo($back_url);
	}
	else {

		$form_info=$sfman->getStoredFormInfo($send_id);
		$email=$form_info["email"];

		$back_ui_url=$back_url;

		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_STOREDFORM");
		$title_arr[]=$lang->def("_DEL");
		$res.=getCmsTitleArea($title_arr, "company");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

		$form=new Form();

		$url=$um->getUrl("op=del&send_id=".$send_id);

		$res.=$form->openForm("main_form", $url);


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_RECEIVED_FROM').' :</span> '.$email.' ?<br />',
			false,
			'conf_del',
			'undo');

		$res.=$form->closeForm();
		$res.="</div>\n";
		$out->add($res);
	}
}


function confirmCompany() {
	$res="";

	if ((isset($_GET["send_id"])) && ($_GET["send_id"] > 0))
			$send_id=$_GET["send_id"];
	else
		return FALSE;

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	$cm=new CompanyManager();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("storedform", "crm");
	$um=& UrlManager::getInstance();
	$form=new Form();

	$sfman=new StoredFormManager();

/*	$form_info=$sfman->getStoredFormInfo($send_id);

	$data=$sfman->getMappedData($form_info["form_id"], $send_id); */

	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_STOREDFORM");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$url =$um->getUrl("op=do_import&send_id=".$send_id);
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();

	$company_name =$_POST["company"];
	$res.=$form->getRadio($lang->def("_USE_NEW_COMPANY")." (".$company_name.")", "new_company", "company_to_use", "new", TRUE);
	$res.=$form->getRadio($lang->def("_USE_EXISTING_COMPANY"), "existing_company", "company_to_use", "existing");

	$company_arr=$sfman->getCompanyArray(FALSE);
	$res.=$form->getDropdown($lang->def("_COMPANY"), "existing_company_id", "existing_company_id", $company_arr);

	$import_data =urlencode(serialize($_POST));
	$res.=$form->getHidden("import_data", "import_data", $import_data);

	$res.=$form->closeElementSpace();

	$res.=$form->openButtonSpace();
	$res.=$form->getButton('import', 'import', $lang->def("_IMPORT_CONTACT"));
	$res.=$form->getButton('undo', 'undo', $lang->def("_UNDO"));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();


	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}



// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		storedform();
	} break;

	case "toggledetails": {
		toggleStoredFormDetails();
	} break;

	case "edit": {

	} break;

	case "del": {
		deleteContact();
	} break;

	case "import": {
		importContact();
	} break;

	case "conf_company": {
		confirmCompany();
	} break;

	case "do_import": {
		if (!isset($_POST["undo"])) {
			doImportContact();
		}
		else {
			storedform();
		}
	} break;

}

?>