<?php

/*************************************************************************/
/* DOCEBO CMS - Content Managment System                                 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* Copyright (c) 2004 by Emanuele Sandri (esandri@tiscali.it)            */
/* Copyright (c) 2004 by Fabio pirovano (gishell@tiscali.it)             */
/*                                                                       */
/*              http://www.spaghettilearning.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_framework'].'/lib/lib.treedb.php');
require_once($GLOBALS['where_framework'].'/lib/lib.treeview.php');

function sel_block_groups($idBlock, $db_group) {
return 0;
	$groups = array();
	echo("\n<div class=\"title\">"._BLOCK_GROUPS."</div>\n");
	echo("<div class=\"groupsperm\">");
	while(list($idCommon, $nameGroup) = each($groups)) {
		if (in_array($idCommon, $db_group)) $chk=" checked=\"checked\""; else $chk="";
		echo("<input type=\"checkbox\" id=\"idGroups[".$idCommon."]\" name=\"idGroups[".$idCommon."]\" value=\"".$idCommon."\" $chk />\n");
		echo("<label for=\"idGroups[".$idCommon."]\">".$nameGroup."</label><br />\n");
	}
	echo '</div>'."\n\n";
}


function sel_block_forums($form, $lang, $block_id) {

	$db_forum=db_block_forums($block_id);
	$res="";

	if (!is_array($db_forum))
		$db_forum=array();

	$qtxt="SELECT idForum, title FROM ".$GLOBALS['prefix_cms']."_forum ORDER BY sequence";
	$q=mysql_query($qtxt);

	$forums=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$forums[$row["idForum"]]=$row["title"];
		}
	}

	$res.=$form->getOpenFieldset($lang->def("_BLOCK_FORUM_LIST"));

	while(list($idForum, $title) = each($forums)) {
		if (in_array($idForum, $db_forum)) $chk=true; else $chk=false;
		/*$res.="<input type=\"checkbox\" id=\"idForums[".$idForum."]\" name=\"idForums[".$idForum."]\" value=\"".$idForum."\" $chk />\n";
		$res.="<label for=\"idForums[".$idForum."]\">".$title."</label><br />\n";*/
		$res.=$form->getCheckbox($title, "idForums_".$idForum."_", "idForums[".$idForum."]", $idForum, $chk);
	}

	$res.=$form->getCloseFieldset();

	return $res;
}

function block_group_list(& $form, & $lang, $block_id) { // 2.1 ready

	require_once($GLOBALS['where_cms'].'/lib/lib.permsel.php');
	include_once($GLOBALS['where_framework']."/lib/lib.acl.php");

	$res="";

	$acl=new DoceboACL();
	$acl_manager=$acl->getACLManager();

	$perm_sel=new GroupPermSel();
	$perm=array();

	// Load the save permissions
	$token=array(
				'view' => array( 	'code' => 'view',
				'name' => '_VIEW',
				'image' => 'standard/view.gif')
			);

	foreach($token as $key=>$val) {
		$roleid="/cms/modules/block/".$block_id."/".$val["code"];
		$rolest=$acl->getRoleST($roleid);


		if ($rolest === FALSE) {
			$grp_to_read=array();
		}
		else {
			// Wich groups are member of this role? [TODO]
			$grp_to_read=$acl_manager->getRoleGMembers($rolest);
		}


		// Let's build the $perm array with the selected permissions [TODO]
		foreach ($grp_to_read as $grp_key=>$grp_val) {

			$perm[$grp_val][$val["code"]]=1;
		}

	}

	$res.=$perm_sel->getPermissionUi("block_form", $perm);
	$res.="<br />\n";

	return $res;
}




function block_css_list(& $form, & $lang, $cur, $ext="", $num=0) { // 2.1 ready

	$res="";

	if ((int)$cur == 0) $cur=1;

	if (!getAccessibilityStatus()) {

		$onchange ="onchange=\"javascript:image$num.src='".getPathImage()."csspreview/css_'+this.value+'.jpg';";
		$onchange.="image$num.alt='css_'+this.value;\"";

		$preview ="<img name=\"image".(int)$num."\" src=\"".getPathImage()."csspreview/css_";
		$preview.=(int)$cur.".jpg\" alt=\"".$lang->def("_PREVIEW")."\" />\n";

	}
	else {
		$onchange="";
		$preview="";
	}

 	$css_arr=array();
 	for ($i=1; $i<=10; $i++) {
  	$css_arr[$i]=$lang->def("_STYLE")." ".$i;
 	}
 	$res.=$form->getLineDropdown('form_line_l', 'floating', $lang->def("_BLOCK_STYLE").":", 'dropdown', "css$ext", "css$ext", $css_arr, $cur, $onchange, $preview, "");

	return $res;
}


function can_see_block($user_st, $allowed_st) {

	$res=0;
	if ((is_array($user_st)) && (is_array($allowed_st))) {
		foreach ($allowed_st as $key=>$val) {
			if (in_array($val, $user_st)) $res=1;
		}
	}

	return $res;
}



function db_block_groups($idBlock, $type="area") { // old

	$db_group=array();

	switch ($type) {
		case "area": {
			$table=$GLOBALS["prefix_cms"]."_area_block_group";
		} break;
		case "autojoin": {
			$table=$GLOBALS["prefix_cms"]."_group_autojoin";
		} break;
	}

	// Dati gruppi
	$qtxt="SELECT * FROM $table WHERE idBlock='$idBlock';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_group[]=$row["idGroup"];
		}
	}

	return $db_group;
}


function db_block_forums($block_id) {

	$db_forum=array();

	// Dati gruppi
	$qtxt="SELECT * FROM ".$GLOBALS['prefix_cms']."_area_block_forum WHERE idBlock='$block_id';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_forum[]=$row["idForum"];
		}
	}

	return $db_forum;
}



function save_block_groups($block_id, $set_perm_arr=false) { // 2.1 ready

	require_once($GLOBALS['where_cms'].'/lib/lib.permsel.php');
	include_once($GLOBALS['where_framework']."/lib/lib.acl.php");

	$acl=new DoceboACL();
	$acl_manager=$acl->getACLManager();

	$perm_sel=new GroupPermSel();

	if (($set_perm_arr === FALSE) || (!is_array($set_perm_arr))) {
		$set_perm_arr=$_POST;
		$perm_arr=false;
	}
	else {
		$perm_arr=& $set_perm_arr;
	}

	// Save the selected permissions
	if (isset($set_perm_arr["save"])) {
		$perm=$perm_sel->getSelectedPermission($perm_arr);

		$token=$perm_sel->getAllToken();
		$rolest_arr=array();
		$idst_arr=array();

		foreach($token as $key=>$val) {
			if (isset($val["code"])) {
				$roleid="/cms/modules/block/".$block_id."/".$val["code"];
				$rolest=$acl->getRoleST($roleid);

				if ($rolest === FALSE) {
					$rolest=$acl_manager->registerRole($roleid, "");
				}

				$rolest_arr[$val["code"]]=$rolest;
				$idst_arr[]=$rolest;
			}
		}

		foreach($perm as $key=>$val) {

			$group_st=$key;

			if ($group_st > 0) {

				$not_to_rem=array();
				$rc=$acl_manager->getRolesContainer($group_st);
				if (!is_array($rc))
					$rc=array();

				$to_rem=array_intersect($idst_arr, $rc);

				foreach($val as $sp_key=>$sp_val) { // add selected
					if ($sp_val == 1) {
						if (!in_array($rolest_arr[$sp_key], $rc)) {
							$acl_manager->addToRole($rolest_arr[$sp_key], $group_st);
						}
						$not_to_rem[]=$rolest_arr[$sp_key];
					}
				}

				foreach($to_rem as $rm_key=>$rm_val) { // removes deselected
					if (!in_array($rm_val, $not_to_rem)) {
						$acl_manager->removeFromRole($rm_val, $group_st);
					}
				}

			}
		}

		// Refresh Cms user permissions
		include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
		include_once($GLOBALS['where_cms']."/lib/lib.cms_common.php");
		setCmsReloadPerm();
		unsetBlockInfo();
	}

}



function save_block_groups_old($idBlock, $idGroups, $type="area") {

	switch ($type) {
		case "area": {
			$table=$GLOBALS["prefix_cms"]."_area_block_group";
		} break;
		case "autojoin": {
			$table=$GLOBALS["prefix_cms"]."_group_autojoin";
		} break;
	}

	$qtxt="SELECT * FROM $table WHERE idBlock='$idBlock' ORDER BY idGroup;";
	$q=mysql_query($qtxt);

	$db_idGroup=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$db_idGroup[]=$row["idGroup"];
		}
	}

	if(!is_array($idGroups)) {
		$qtxt="DELETE FROM $table WHERE idBlock='$idBlock';";
		mysql_query($qtxt);
		return;
	}
	foreach ($idGroups as $key=>$val) {

		// se non e' tra quelli gia' presenti nel DB lo aggiunge..
		if (!in_array($val, $db_idGroup)) {
			$qtxt="INSERT INTO $table (idBlock, idGroup) VALUES ('$idBlock', '$val');";
			mysql_query($qtxt);
		}

	}


	// E se nel database ce ne sono alcuni "di troppo", li toglie..
	foreach ($db_idGroup as $key=>$val) {
		if (!in_array($val, $idGroups)) {
			$qtxt="DELETE FROM $table WHERE idBlock='$idBlock' AND idGroup='$val';";
			mysql_query($qtxt);
		}
	}
}


function save_block_forums($block_id) { // 2.1 ready

	$idForums=$_POST["idForums"];

	$qtxt="SELECT * FROM ".$GLOBALS['prefix_cms']."_area_block_forum WHERE idBlock='$block_id' ORDER BY idForum;";
	$q=mysql_query($qtxt);

	$db_idForum=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$db_idForum[]=$row["idForum"];
		}
	}

	if((!is_array($idForums)) || (count($idForums) == 0)) {
		$qtxt="DELETE FROM ".$GLOBALS['prefix_cms']."_area_block_forum WHERE idBlock='$block_id';";
		mysql_query($qtxt);
		return;
	}
	foreach ($idForums as $key=>$val) {

		// se non e' tra quelli gia' presenti nel DB lo aggiunge..
		if (!in_array($val, $db_idForum)) {
			$qtxt="INSERT INTO ".$GLOBALS['prefix_cms']."_area_block_forum (idBlock, idForum) VALUES ('$block_id', '$val');";
			mysql_query($qtxt);
		}

	}


	// E se nel database ce ne sono alcuni "di troppo", li toglie..
	foreach ($db_idForum as $key=>$val) {
		if (!in_array($val, $idForums)) {
			$qtxt="DELETE FROM ".$GLOBALS['prefix_cms']."_area_block_forum WHERE idBlock='$block_id' AND idForum='$val';";
			mysql_query($qtxt);
		}
	}
}


function check_period(&$ts_pub, &$ts_exp) {

	$period_ok=1;
	if (($_POST["use_pub_time"]) || ($_POST["use_exp_time"])) {
		$date_pub=$_POST["date_pub"];
		$time_pub=$_POST["hour_pub"].":".$_POST["min_pub"];
		$date_exp=$_POST["date_exp"];
		$time_exp=$_POST["hour_exp"].":".$_POST["min_exp"];
		if ($_POST["use_pub_time"])
			$ts_pub=get_timestamp($date_pub, $time_pub);
		if ($_POST["use_exp_time"])
			$ts_exp=get_timestamp($date_exp, $time_exp);

		if (($_POST["use_pub_time"]) && ($_POST["use_exp_time"]) && ($ts_pub>=$ts_exp)) $period_ok=0;
	}

	return $period_ok;
}



function save_pubexp_info($block_id) {

	$arr=get_pubexp_info();

	if (($arr["pubdate"] != "0") && ($arr["pubdate"] != ""))
		saveParam($block_id, "pubdate", $arr["pubdate"]);
	if (($arr["expdate"] != "0") && ($arr["expdate"] != ""))
		saveParam($block_id, "expdate", $arr["expdate"]);

	if (($arr["pubdate"] == 0) && ($arr["expdate"] == 0)) {
		$qtxt ="DELETE FROM ".$GLOBALS["prefix_cms"]."_area_option ";
		$qtxt.="WHERE ((name='pubdate' OR name='expdate') AND value='0') ";
		$qtxt.="OR (name='pubdate' AND value > NOW())";
		$q=mysql_query($qtxt);
	}

}



function get_pubexp_info() {

	$res=array();

	$pubdate="0";
	$pubdate_ts="0";
	$expdate="0";
	$expdate_ts="0";

	if ((isset($_POST["use_pubdate"])) && ($_POST["use_pubdate"])) {
		$pubdate=$GLOBALS["regset"]->regionalToDatabase($_POST["pubdate"]);
		$pubdate_ts=(int)$GLOBALS["regset"]->ddate->getTimeStamp();

		if ($pubdate_ts <= time())
			$pubdate="0";
	}

	if ((isset($_POST["use_expdate"])) && ($_POST["use_expdate"])) {
		$expdate=$GLOBALS["regset"]->regionalToDatabase($_POST["expdate"]);
		$expdate_ts=(int)$GLOBALS["regset"]->ddate->getTimeStamp();

		if ($expdate_ts <= $pubdate_ts)
			$expdate="0";
	}

	$res["pubdate"]=$pubdate;
	$res["expdate"]=$expdate;

	return $res;
}




function show_pubexp_table(& $form, & $lang, $pubdate, $expdate) {

	$res="";

	if (($pubdate == "0") || ($pubdate == "")) {
		$pubdate=$GLOBALS["regset"]->databaseToRegional(date("Y-m-d H:i:s"));
		$use_pubdate=false;
	}
	else {
		$pubdate=$GLOBALS["regset"]->databaseToRegional($pubdate);
		$use_pubdate=true;
	}

	if (($expdate == "0") || ($expdate == "")) {
		$expdate=$GLOBALS["regset"]->databaseToRegional(date("Y-m-d H:i:s"));
		$use_expdate=false;
	}
	else {
		$expdate=$GLOBALS["regset"]->databaseToRegional($expdate);
		$use_expdate=true;
	}


	$res.=$form->getCheckbox($lang->def("_USE_PUBDATE").":", "use_pubdate", "use_pubdate", "1", $use_pubdate);
	$res.=$form->getDatefield($lang->def("_PUBDATE"), "pubdate","pubdate", $pubdate, false, true);
	$res.=$form->getCheckbox($lang->def("_USE_EXPDATE").":", "use_expdate", "use_expdate", "1", $use_expdate);
	$res.=$form->getDatefield($lang->def("_EXPDATE"), "expdate","expdate", $expdate, false, true);

	return $res;
}


function get_block_idArea($idBlock) {

	$qtxt="
	SELECT t1.idSubdivision, t1.idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision AS t1
		JOIN ".$GLOBALS["prefix_cms"]."_area_block AS t2
	WHERE t2.idBlock = '".$idBlock."'
		AND t1.idSubdivision = t2.idSubdivision
	LIMIT 1";

	if(!$q = mysql_query($qtxt)) return false;
	if(!$row = mysql_fetch_array($q)) return false;

	$idArea = (int)$row["idArea"];
	return $idArea;
}


function saveTextof($idBlock, $textof, $save_content=FALSE) {

	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");

	$lang = get_area_lang(get_block_idArea($idBlock));

	$qtxt = "
	SELECT *
	FROM ".$GLOBALS["prefix_cms"]."_text
	WHERE idBlock = '".$idBlock."'
		AND language = '".$lang."'";

	$q = mysql_query($qtxt);
	if($q && (mysql_num_rows($q) > 0)) {

		$save_res = mysql_query("
		UPDATE  ".$GLOBALS["prefix_cms"]."_text
		SET textof = '".$textof."'
		WHERE idBlock='$idBlock'
			AND language='$lang';");
	} else {

		$save_res = mysql_query("
		INSERT INTO  ".$GLOBALS["prefix_cms"]."_text
			( idBlock, language, textof )
		VALUES
			( '".$idBlock."', '".$lang."', '".$textof."' ) ");

		if($save_content) {

			// Add it as a content too:
			$title = strip_tags(substr($textof, 0 ,60));
			$qtxt = "
			INSERT INTO  ".$GLOBALS["prefix_cms"]."_content
				( idFolder, publish_date, type, key1, language, title, publish )
			VALUES
				( '0', '".date("Y-m-d H:i:s")."', 'block_text', '$idBlock', '$lang', '".$title." ...', '1' )";
			$save_res = mysql_query($qtxt);
		}

		// ---------- Fixing order
		require_once($GLOBALS["where_cms"]."/lib/admin_common.php");
		fix_item_order($GLOBALS["prefix_cms"]."_content", "idContent", 0);
		// ------------------------
	}

}


function loadTextof($idBlock) {
	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");

	$lang=get_area_lang(get_block_idArea($idBlock));

	$res="";
	$qtxt="SELECT textof FROM ".$GLOBALS["prefix_cms"]."_text WHERE idBlock='$idBlock' AND language='$lang';";

	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$res=$row["textof"];
	}

	return $res;
}



function getBlockTitleField(& $form, & $lang, $block_id) {

	$q=mysql_query("SELECT title FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE idBlock='$block_id';");
	list($title)=mysql_fetch_row($q);
 	$res=$form->getTextfield($lang->def("_BLOCK_TITLE").":", "title", "title", 255, $title);

	return $res;
}


function saveBlockTitle($block_id) {

	$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_area_block SET title='".$_POST["title"]."' WHERE idBlock='$block_id';");

	return $q;
}



function getGMonitoringField(& $form, & $lang, $opt) {

	if (isset($opt["gmonitoring"]))
		$gmonitoring=$opt["gmonitoring"];
	else
		$gmonitoring="";

 	$res=$form->getSimpleTextarea($lang->def("_G_MONITORING").":", "gmonitoring", "gmonitoring", $gmonitoring);

	return $res;
}


function saveGMonitoring($block_id) {
	saveParam($block_id, "gmonitoring", $_POST["gmonitoring"], "text");
}


function getBlindNavDescField(& $form, & $lang, $opt) {

	if (getAccessibilityStatus()) {

		if (isset($opt["blindnavdesc"]))
			$blindnavdesc=$opt["blindnavdesc"];
		else
			$blindnavdesc="";

		$res=$form->getTextfield($lang->def("_BLINDNAV_DESC").":", "blindnavdesc", "blindnavdesc", 255, $blindnavdesc);

		return $res;
	}
	else
		return "";
}


function saveBlindNavDesc($block_id) {
	if (getAccessibilityStatus()) {
		saveParam($block_id, "blindnavdesc", $_POST["blindnavdesc"]);
	}
}




/**
 * save parameters for a block
 * @param $idBlock id of target block
 * @param $name param name
 * @param $value param value
 **/
function saveParam( $idBlock, $name, $value, $type="normal" ) {

	switch ($type) {
		case "normal": {
			$table=$GLOBALS["prefix_cms"]."_area_option";
			$field_name="value";
		} break;

		case "text": {
			$table=$GLOBALS["prefix_cms"]."_area_option_text";
			$field_name="text";
		} break;
	}

	$q=mysql_query("SELECT * FROM ".$table." WHERE idBlock='$idBlock' AND name='$name';");

	if (($q) && (mysql_num_rows($q) > 0)) { // Update
		$re = mysql_query("
		UPDATE ".$table."
		SET
			".$field_name." = '".$value."'
		WHERE
			idBlock='$idBlock' AND name='$name'");
	}
	else { // Insert
		$re = mysql_query("
		INSERT INTO ".$table."
		SET idBlock = '$idBlock',
			name = '".$name."',
			".$field_name." = '".$value."'");
	}
	if(!$re) return false;
	else return true;
}


function saveBlockPath($block_id, $folder_id, $table, $idfield="id") {

	if ((int)$folder_id == 0)
		$path="/";
	else {
		$q=mysql_query("SELECT path FROM ".$GLOBALS["prefix_cms"].$table." WHERE ".$idfield."='".$folder_id."'");
		$row=mysql_fetch_array($q);

		$path=$row["path"];
	}

	saveParam($block_id, "path", $path);
}


function getParentBlockArray($parent_type) {
	$res =array();

	$fields ="idBlock, block_name, title";
	$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE ";
	$qtxt.="block_name='".$parent_type."' LIMIT 0,1";

	$q =mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {
			$id =(int)$row["idBlock"];
			$name =(!empty($row["title"]) ? $row["title"] : $row["block_name"]." (".$id.")");
			$res[$id]=$name;
		}
	}

	return $res;
}


?>
