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

// TODO: All this function will be moved into an object stored in lib.permsel
// and will replace some functions already available on that file

function getGroupsSelector($form_name, $caption, $image="", $res_id="group",
                           $sel_arr=FALSE, $incl_all=FALSE, $incl_anonymous=FALSE,
                           $incl_registered=FALSE, $check_buttons=TRUE) {

	$lang =& DoceboLanguage::createInstance("profile_search", "cms");

	$res="";
	$groups=array();

	if ($sel_arr === FALSE) {
		$sel_arr=array();
	}


	if ($incl_all) {
		$groups["all"]=$lang->def("_ALL_GROUPS");
	}


	$acl_manager=$GLOBALS["current_user"]->getAclManager();

	$data=new GroupDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
	$q=$data->getRows(0, 100000000000);
	if ($q) {
		while($row=mysql_fetch_array($q)) {
			$groups[$row["idst"]]=$acl_manager->relativeId($row["groupid"]);
		}
	}

	$tab_info=array();
	$tab_info["title"]=$lang->def("_GROUPS");
	$tab_info["caption"]=$caption;
	$tab_info["summary"]="x";
	$tab_info["image"]=$image;

	$res.=getSelectorTable($form_name, $res_id, $sel_arr, $groups, $tab_info);

	return $res;
}


function getLevelsSelector($form_name, $caption, $image="", $res_id="level",
                           $sel_arr=FALSE, $incl_all=FALSE, $check_buttons=TRUE) {

	$res="";

	$pl_man=& PlatformManager::CreateInstance();

	if (!$pl_man->isLoaded("lms")) {
		return "";
	}

	$lang =& DoceboLanguage::createInstance("profile_search", "cms");

	if ($sel_arr === FALSE) {
		$sel_arr=array();
	}


	require_once($GLOBALS["where_lms"]."/lib/lib.levels.php");

	$cl=new CourseLevel();
	$level_list=$cl->getLevels();


	if ($incl_all) {
		$level_list=array_merge(array("all"=>$lang->def("_ALL_LEVELS")), $level_list);
	}


	$tab_info=array();
	$tab_info["title"]=$lang->def("_LEVELS");
	$tab_info["caption"]=$caption;
	$tab_info["summary"]="x";
	$tab_info["image"]=$image;

	$res.=getSelectorTable($form_name, $res_id, $sel_arr, $level_list, $tab_info);

	return $res;
}


function getSelectorTable($form_name, $res_id, $sel_arr, $items, $tab_info, $check_buttons=TRUE) {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$lang =& DoceboLanguage::createInstance("profile_search", "cms");

	$res="";

	loadJsLibraries();

	$c_head=array( $tab_info["title"], "");
	$t_head=array($tab_info["image"], "image");

	$tb=new TypeOne(0, $tab_info["caption"], $tab_info["summary"]);

	$tb->setColsStyle($t_head);
	$tb->addHead($c_head);


	foreach($items as $key=>$val) {

		$rowcnt=array();

		$rowcnt[]=$val;

		$checkbox ="<input class=\"check\" type=\"checkbox\" id=\"".$res_id."_".$key."\" ";
		$checkbox.="name=\"".$res_id."[".$key."]\" value=\"1\" ";
		$checkbox.=(in_array($key, $sel_arr) ? "checked=\"checked\" " : "" )."/>";

		$rowcnt[]=$checkbox;

		$tb->addBody($rowcnt);
	}

	$c_select_all=array();
	$c_select_all[]="&nbsp;";
	$c_select_all[]='<img class="handover"'
			.' onclick="checkall(\''.$form_name.'\', \''.$res_id.'\', true); return false;"'
			.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />'
		.'<img class="handover"'
			.' onclick="checkall(\''.$form_name.'\', \''.$res_id.'\', false); return false;"'
			.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';

	$tb->addBody($c_select_all);

	$res.=$tb->getTable();

	return $res;
}


function saveBlockFilter($block_id, $block_type, $id_type, $id_val_arr) {

	$qtxt ="DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_filter ";
	$qtxt.="WHERE block_id='".$block_id."' AND block_type='".$block_type."' ";
	$qtxt.="AND id_type='".$id_type."'";

	$q=mysql_query($qtxt);


	$i=0;
	$tot=count($id_val_arr);

	if ($tot > 0) {

		$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_area_block_filter ";
		$qtxt.="(block_id, block_type, id_type, id_val) VALUES ";

		foreach($id_val_arr as $val) {

			$qtxt.="('".$block_id."', '".$block_type."', '".$id_type."', '".(int)$val."')";

			if ($i < $tot-1) {
				$qtxt.=", \n";
			}

			$i++;
		}

		$q=mysql_query($qtxt);
	}
}


function loadBlockFilter($block_id, $block_type, $id_type=FALSE) {
	$res=array();

	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block_filter ";
	$qtxt.="WHERE block_id='".$block_id."' AND block_type='".$block_type."'";

	if ($id_type !== FALSE)
		$qtxt.=" AND id_type='".$id_type."'";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {

			$res[$row["id_type"]][]=$row["id_val"];

		}
	}

	return $res;
}


function getCustomFieldsList($form_name, $sel_items=FALSE) {
	$res ="";
	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$fl=new FieldList();
	$lang =& DoceboLanguage::createInstance("profile_search", "cms");

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_groups=array($acl_manager->getGroupRegisteredId());

	$field_list=$fl->getFieldsFromIdst($user_groups);

	if (($sel_items === FALSE) || (empty($sel_items))) {
		$sel_arr =array();
	}
	else {
		$sel_arr =explode(",", $sel_items);
	}

	loadJsLibraries();

	$c_head=array( $tab_info["title"], "");
	$t_head=array($tab_info["image"], "image");

	$tb=new TypeOne(0, $tab_info["caption"], $tab_info["summary"]);

	$tb->setColsStyle($t_head);
	$tb->addHead($c_head);

	$res_id ="custom_field";

	foreach($field_list as $field_id=>$field_info) {
		$id =$res_id."_".$field_id;
		$name =$res_id."[".$field_id."]";

		$rowcnt=array();

		$rowcnt[]=$field_info[FIELD_INFO_TRANSLATION];

		//$checkbox =Form::getCheckbox("", $id, $name, $field_id);

		$checkbox ="<input class=\"check\" type=\"checkbox\" id=\"".$res_id."_".$field_id."\" ";
		$checkbox.="name=\"".$res_id."[".$field_id."]\" value=\"".$field_id."\" ";
		$checkbox.=(in_array($field_id, $sel_arr) ? "checked=\"checked\" " : "" )."/>";

		$rowcnt[]=$checkbox;

		$tb->addBody($rowcnt);
	}

	$c_select_all=array();
	$c_select_all[]="&nbsp;";
	$c_select_all[]='<img class="handover"'
			.' onclick="checkall(\''.$form_name.'\', \''.$res_id.'\', true); return false;"'
			.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />'
		.'<img class="handover"'
			.' onclick="checkall(\''.$form_name.'\', \''.$res_id.'\', false); return false;"'
			.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';

	$tb->addBody($c_select_all);

	$res.=$tb->getTable();

	return $res;
}


function getAvatarSizeDropdownArr(& $lang) {
	$res =array();

	$res['micro']=$lang->def("_SIZE_MICRO");
	$res['small']=$lang->def("_SIZE_SMALL");
	$res['medium']=$lang->def("_SIZE_MEDIUM");
	$res['large']=$lang->def("_SIZE_LARGE");

	return $res;
}

?>
