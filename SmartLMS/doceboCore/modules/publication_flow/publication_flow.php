<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework']."/lib/lib.pubflow.php");

function publication_flow() {
	checkPerm('view');
	$write_perm=true;
	$mod_perm=true;
	$rem_perm=true;


	require_once($GLOBALS['where_framework']."/lib/lib.typeone.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_pubflow', 'framework');
	$pubflow=new PublicationFlowManager();

	$out->setWorkingZone("content");

	$out->add(getTitleArea($lang->def("_PUBLICATION_FLOW"), "pubflow"));
	$out->add("<div class=\"std_block\">\n");

	$ini=importVar("ini", true, 0);

	$arr=$pubflow->getAllFlow();


	$table=new typeOne($GLOBALS["visuItem"]);
	$out->add($table->OpenTable(""));

	$head=array($lang->def("_TITLE"), $lang->def("_DESCRIPTION"),
		'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def("_ALT_MODSTEPS").'" title="'.$lang->def("_ALT_MODSTEPS").'" />',
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type=array('', '', 'img', 'img', 'img');


	$out->add($table->WriteHeader($head, $head_type));

	$tot=(count($arr) < ($ini+$GLOBALS["visuItem"])) ? count($arr) : $ini+$GLOBALS["visuItem"];
	for($i=$ini; $i<$tot; $i++ ) {
		$rowcnt=array();
		$label=$pubflow->getItemLangText("flow", $arr[$i]["flow_id"], getLanguage(), "label");
		$rowcnt[]=$label;
		$rowcnt[]=$pubflow->getItemLangText("flow", $arr[$i]["flow_id"], getLanguage(), "description");
		if ($mod_perm) {
			$btn ="<a href=\"index.php?modname=publication_flow&amp;op=flowsteps&amp;id=".$arr[$i]["flow_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/modelem.gif\" ";
			$btn.="alt=\"".$lang->def("_ALT_MODSTEPS")."\" title=\"".$lang->def("_ALT_MODSTEPS")." ".$label."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;

			$btn ="<a href=\"index.php?modname=publication_flow&amp;op=editflow&amp;id=".$arr[$i]["flow_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/mod.gif\" ";
			$btn.="alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD")." ".$label."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else {
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
		}

		if (($rem_perm) && (!$arr[$i]["default"])) {
			$btn ="<a href=\"index.php?modname=publication_flow&amp;op=delflow&amp;id=".$arr[$i]["flow_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/rem.gif\" ";
			$btn.="alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")." ".$label."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else
			$rowcnt[]="&nbsp;";

		$out->add($table->writeRow($rowcnt));
	}

	if($write_perm) {
		$out->add($table->WriteAddRow('<a href="index.php?modname=publication_flow&amp;op=addflow">
		 <img src="'.getPathImage().'standard/add.gif" title="'.$lang->def( '_ADD' ).'" alt="'.$lang->def( '_ADD' ).'" /> '.
		 $lang->def( '_ADD' ).'</a>'));
	}

	$out->add($table->CloseTable());

	$out->add($table->WriteNavBar('',
								'index.php?modname=publication_flow&amp;op=pubflow&amp;ini=',
								$ini,
								count($arr)));


	$out->add("</div>\n");

}


function pubflow_editflow($flow_id=0) {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_pubflow', 'framework');
	$form=new Form();

	$out->setWorkingZone("content");

	$out->add(getTitleArea($lang->def("_PUBLICATION_FLOW"), "pubflow"));
	$out->add("<div class=\"std_block\">\n");

	$data=array();

	if ((int)$flow_id == 0) {  // Add
		$out->add($form->openForm("pubflow_form", "index.php?modname=publication_flow&amp;op=insnew"));
		$field_val=FALSE;
		$submit_lbl=$lang->def("_INSERT");
	}
	else {  // Edit
		$out->add($form->openForm("pubflow_form", "index.php?modname=publication_flow&amp;op=updflow"));

		$pubflow=new PublicationFlowManager();

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {
			$field_val["label"][$val]=$pubflow->getItemLangText("flow", $flow_id, $val, "label");
			$field_val["description"][$val]=$pubflow->getItemLangText("flow", $flow_id, $val, "description");
		}

		$submit_lbl=$lang->def("_EDIT");
	}


	$out->add($form->openElementSpace());


	multi_lang_field($form, "label", $lang->def("_TITLE"), $field_val);
	multi_lang_field($form, "description", $lang->def("_DESCRIPTION"), $field_val, "textarea");


	$out->add($form->getHidden("flow_id", "flow_id", $flow_id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $submit_lbl));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	//"<br /><br /><input class=\"button\" type=\"submit\" value=\"".$submit_lbl."\" />\n");
	$out->add($form->closeForm());

	$out->add("</div>\n");

}



function pubflow_delflow($flow_id) {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('admin_pubflow', "framework");

	$form=new Form();
	$pubflow=new PublicationFlowManager();
	$res="";


	$back_url="index.php?modname=publication_flow&amp;op=pubflow";

	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$pubflow->deleteFlow($flow_id);

		jumpTo($back_url);
	}
	else {

		$title=$pubflow->getItemLangText("flow", $flow_id, getLanguage(), "label");

		$form=new Form();

		$url="index.php?modname=publication_flow&amp;op=delflow&amp;id=".$flow_id;
		$res.=$form->openForm("del_form", $url);

		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'undo');

		// ----------------------------------------------------------------------
		$out->add(getTitleArea($lang->def("_PUBLICATION_STEP"), "pubflow"));
		$out->add("<div class=\"std_block\">\n");
		$out->add($res);
		$out->add("</div>\n");
	}
}


function multi_lang_field(& $form, $field_name, $field_lbl, $field_val=FALSE, $type="text") {
	checkPerm('view');
	if ($field_val == FALSE)
		$field_val=array();

	$larr=$GLOBALS['globLangManager']->getAllLangCode();
	foreach ($larr as $key=>$val) {

		$field=$field_name."[".$val."]";

		$current_val=(isset($field_val[$field_name][$val]) ? $field_val[$field_name][$val] : "");

		if ($type == "text")
			$GLOBALS['page']->add($form->getTextfield($field_lbl." (".$val.")", $field, $field, 255, $current_val));
		else if ($type == "textarea")
			$GLOBALS['page']->add($form->getSimpleTextarea($field_lbl." (".$val.")", $field, $field, $current_val));
	}

}



function pubflow_save() {
	checkPerm('view');

	$pubflow=new PublicationFlowManager();
	$pubflow->saveFlow($_POST);

	jumpTo("index.php?modname=publication_flow&op=pubflow");
}



function pubflow_steps($flow_id) {
	checkPerm('view');
	$write_perm=true;
	$mod_perm=true;
	$rem_perm=true;


	require_once($GLOBALS['where_framework']."/lib/lib.typeone.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_pubflow', 'framework');
	$pubflow=new PublicationFlowManager();

	$out->setWorkingZone("content");

	$out->add(getTitleArea($lang->def("_PUBLICATION_STEP"), "pubflow"));
	$out->add("<div class=\"std_block\">\n");

	$out->add( getBackUi( "index.php?modname=publication_flow&amp;op=pubflow", $lang->def( '_BACK' ) ));

	$ini=importVar("ini", true, 0);

	$arr=$pubflow->getAllSteps($flow_id);
	$flow_info=$pubflow->getFlowInfo($flow_id);


	$table=new typeOne($GLOBALS["visuItem"]);
	$out->add($table->OpenTable(""));

	$head=array($lang->def("_TITLE"), $lang->def("_DESCRIPTION"),
		'<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type=array('', '', 'img', 'img', 'img');


	$out->add($table->WriteHeader($head, $head_type));

	$tot=(count($arr) < ($ini+$GLOBALS["visuItem"])) ? count($arr) : $ini+$GLOBALS["visuItem"];
	for($i=$ini; $i<$tot; $i++ ) {
		$rowcnt=array();
		$label=$pubflow->getItemLangText("step", $arr[$i]["step_id"], getLanguage(), "label");
		$rowcnt[]=$label;
		$rowcnt[]=$pubflow->getItemLangText("step", $arr[$i]["step_id"], getLanguage(), "description");
		if ($mod_perm) {
			$btn ="<a href=\"index.php?modname=publication_flow&amp;op=stepperm&amp;id=".$flow_id;
			$btn.="&amp;step=".$arr[$i]["step_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/moduser.gif\" ";
			$btn.="alt=\"".$lang->def("_ALT_MODPERM")."\" title=\"".$lang->def("_ALT_MODPERM")." ".$label."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;


			$btn ="<a href=\"index.php?modname=publication_flow&amp;op=editstep&amp;id=".$flow_id;
			$btn.="&amp;step=".$arr[$i]["step_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/mod.gif\" ";
			$btn.="alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD")." ".$label."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else {
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
		}

		if (($rem_perm) && (!$flow_info["default"])) {
			$btn ="<a href=\"index.php?modname=publication_flow&amp;op=delstep&amp;id=".$flow_id;
			$btn.="&amp;step=".$arr[$i]["step_id"]."\">";
			$btn.="<img src=\"".getPathImage()."standard/rem.gif\" ";
			$btn.="alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")." ".$label."\" />";
			$btn.="</a>\n";
			$rowcnt[]=$btn;
		}
		else {
			$rowcnt[]="&nbsp;";
		}

		$out->add($table->writeRow($rowcnt));
	}

	if (($write_perm) && (!$flow_info["default"])) {
		$out->add($table->WriteAddRow('<a href="index.php?modname=publication_flow&amp;op=addstep&amp;id='.$flow_id.'">
		 <img src="'.getPathImage().'standard/add.gif" title="'.$lang->def( 'ALT_ADD' ).'" alt="'.$lang->def( 'ALT_ADD' ).'" /> '.
		 $lang->def( '_ADD' ).'</a>'));
	}

	$out->add($table->CloseTable());

	$out->add($table->WriteNavBar('',
								'index.php?modname=publication_flow&amp;op=flowsteps&amp;id='.$flow_id.'&amp;ini=',
								$ini,
								count($arr)));


	$out->add("</div>\n");

}


function pubflow_editstep($flow_id, $step_id=0) {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_pubflow', 'framework');
	$form=new Form();

	$out->setWorkingZone("content");

	$out->add(getTitleArea($lang->def("_PUBLICATION_FLOW"), "pubflow"));
	$out->add("<div class=\"std_block\">\n");

	$data=array();

	if ((int)$step_id == 0) {  // Add
		$out->add($form->openForm("pubflow_form", "index.php?modname=publication_flow&amp;op=insstep&amp;id=".$flow_id));
		$submit_lbl=$lang->def("_INSERT");

		$field_val=FALSE;
		$flow_info["default"]=FALSE;
		$is_published=false;
	}
	else {  // Edit
		$out->add($form->openForm("pubflow_form", "index.php?modname=publication_flow&amp;op=updstep&amp;id=".$flow_id));

		$pubflow=new PublicationFlowManager();
		$flow_info=$pubflow->getFlowInfo($flow_id);
  		$step_info=$pubflow->getStepInfo($step_id);

		$is_published=($step_info["is_published"] ? true:false);

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {
			$field_val["label"][$val]=$pubflow->getItemLangText("step", $step_id, $val, "label");
			$field_val["description"][$val]=$pubflow->getItemLangText("step", $step_id, $val, "description");
		}

		$submit_lbl=$lang->def("_EDIT");
	}


	$out->add($form->openElementSpace());

	if (!$flow_info["default"])
		$out->add($form->getCheckbox($lang->def("_IS_PUBLISHED"), "is_published", "is_published", '1', $is_published));
	else
		$out->add($form->getHidden("is_published", "is_published", $is_published));

	multi_lang_field($form, "label", $lang->def("_TITLE"), $field_val);
	multi_lang_field($form, "description", $lang->def("_DESCRIPTION"), $field_val, "textarea");


	$out->add($form->getHidden("flow_id", "flow_id", $flow_id));
	$out->add($form->getHidden("step_id", "step_id", $step_id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $submit_lbl));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	//"<br /><br /><input class=\"button\" type=\"submit\" value=\"".$submit_lbl."\" />\n");
	$out->add($form->closeForm());

	$out->add("</div>\n");

}


function pubflow_savestep() {
	checkPerm('view');

	$pubflow=new PublicationFlowManager();
	$pubflow->saveStep($_POST);

	jumpTo("index.php?modname=publication_flow&op=flowsteps&id=".$_POST["flow_id"]);
}



function pubflow_editStepPerm($flow_id, $step_id) {
	checkPerm('view');

	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	$mdir=new Module_Directory();

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_pubflow', 'framework');
	//$form=new Form();

	$out->setWorkingZone("content");

	//$out->add(getTitleArea($lang->def("_PUBLICATION_FLOW_STEP_PERM"), "pubflow"));
	//$out->add("<div class=\"std_block\">\n");

	$back_url="index.php?modname=publication_flow&amp;op=flowsteps&amp;id=".$flow_id;
	//$out->add( getBackUi($back_url, $lang->def( '_BACK' ) ));


	$pubflow=new PublicationFlowManager();
	$st_id=$pubflow->getStId($step_id);


	if( isset($_POST['okselector']) ) {
		$arr_selection=$mdir->getSelection($_POST);
		$arr_unselected=$mdir->getUnselected();

		foreach($arr_unselected as $idstMember) {
			$mdir->aclManager->removeFromGroup($st_id, $idstMember );
		}

		foreach($arr_selection as $idstMember) {
			$mdir->aclManager->addToGroup($st_id, $idstMember );
		}

		jumpTo(str_replace("&amp;", "&", $back_url));
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo(str_replace("&amp;", "&", $back_url));
	}
	else {

		if( !isset($_GET['stayon']) ) {
			$mdir->resetSelection($mdir->aclManager->getGroupMembers($st_id));
		}

		$url="index.php?modname=publication_flow&amp;op=stepperm&amp;id=".$flow_id."&amp;step=".$step_id."&amp;stayon=1";
		$mdir->loadSelector($url,
			$lang->def( '_PUBLICATION_FLOW_STEP_PERM' ).' '.$groupid, "", TRUE);
	}

	//$out->add("</div>\n");

}



function pubflow_delstep($flow_id, $step_id) {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('admin_pubflow', "framework");

	$form=new Form();
	$pubflow=new PublicationFlowManager();
	$res="";



	$back_url="index.php?modname=publication_flow&amp;op=flowsteps&amp;id=".$flow_id;

	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$step_info=$pubflow->getStepInfo($step_id);
		$pubflow->deleteStep($step_info["flow_id"], $step_id);

		jumpTo($back_url);
	}
	else {

		$title=$pubflow->getItemLangText("step", $step_id, getLanguage(), "label");

		$form=new Form();

		$url="index.php?modname=publication_flow&amp;op=delstep&amp;id=".$flow_id."&amp;step=".$step_id;
		$res.=$form->openForm("del_form", $url);

		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'undo');

		// ----------------------------------------------------------------------
		$out->add(getTitleArea($lang->def("_PUBLICATION_STEP"), "pubflow"));
		$out->add("<div class=\"std_block\">\n");
		$out->add($res);
		$out->add("</div>\n");
	}
}



// ----------------------------------------------------------------------------

function langDispatch( $op ) {
	switch($op) {

		case "pubflow": {
			publication_flow();
		} break;

		case "addflow": {
			pubflow_editflow();
		} break;

		case "insnew": {
			if (isset($_POST["undo"]))
				publication_flow();
			else
				pubflow_save();
		} break;

		case "editflow": {
			pubflow_editflow((int)$_GET["id"]);
		} break;

		case "delflow": {
			pubflow_delflow((int)$_GET["id"]);
		} break;

		case "updflow": {
			if (isset($_POST["undo"]))
				publication_flow();
			else
				pubflow_save();
		} break;

		case "flowsteps": {
			pubflow_steps((int)$_GET["id"]);
		} break;

		case "addstep": {
			pubflow_editstep((int)$_GET["id"]);
		} break;

		case "insstep": {
			if (isset($_POST["undo"]))
				pubflow_steps((int)$_GET["id"]);
			else
				pubflow_savestep();
		} break;

		case "editstep": {
			pubflow_editstep((int)$_GET["id"], (int)$_GET["step"]);
		} break;

		case "delstep": {
			pubflow_delstep((int)$_GET["id"], (int)$_GET["step"]);
		} break;

		case "updstep": {
			if (isset($_POST["undo"]))
				pubflow_steps((int)$_GET["id"]);
			else
				pubflow_savestep();
		} break;


		case "stepperm": {
			if (isset($_POST["undo"]))
				pubflow_steps((int)$_GET["id"]);
			else
				pubflow_editStepPerm((int)$_GET["id"], (int)$_GET["step"]);
		} break;

	}
}


?>
