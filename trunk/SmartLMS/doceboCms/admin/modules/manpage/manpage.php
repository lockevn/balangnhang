<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.org                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");

$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_treeview.css" rel="stylesheet" type="text/css" />', 'page_head');
$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_manpage.css" rel="stylesheet" type="text/css" />', 'page_head');
$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style_organizations.css" rel="stylesheet" type="text/css" />', 'page_head');


require_once( dirname(__FILE__) . '/pagelib.php' );
require_once( dirname(__FILE__) . '/adminAreaFunction.php');

function createPerm( $idGroups, $idArea ) {


	if(!is_array($idGroups)) return;
	while( list(,$group) = each($idGroups) ) {
		mysql_query("
		INSERT INTO ".$GLOBALS["prefix_cms"]."_area_perm
		SET idArea = '$idArea',
			idGroup = '$group'");
	}
	return;
}

function updatePerm( $idGroups, $idArea ) {


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_perm WHERE idArea='$idArea' ORDER BY idGroup;";
	$q=mysql_query($qtxt);

	$db_idGroup=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$db_idGroup[]=$row["idGroup"];
		}
	}

	if(!is_array($idGroups)) return;
	foreach ($idGroups as $key=>$val) {

		// se non e' tra quelli gia' presenti nel DB lo aggiunge..
		if (!in_array($val, $db_idGroup)) {
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_area_perm (idArea, idGroup) VALUES ('$idArea', '$val');";
			mysql_query($qtxt);
		}

	}


	// E se nel database ce ne sono alcuni "di troppo", li toglie..
	foreach ($db_idGroup as $key=>$val) {
		if (!in_array($val, $idGroups)) {
			$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_area_perm WHERE idArea='$idArea' AND idGroup='$val';";
			mysql_query($qtxt);
		}
	}

	return;
}

function createSubdivision( $layout_name, $idArea ) {


	require_once($GLOBALS['where_cms'].'/lib/page_models/layout.'.$layout_name.'.php');

	//create subdivision level 0 (main subdivision)
	$main_sub=$layout->getParamMain();
	$content=$layout->getParamContent(); //-debug-// echo("<pre>");
	foreach($main_sub as $a_key=>$a_val) {

		$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_area_subdivision ";
		$qtxt.="SET idArea = '$idArea', idParentSub = '0', sequence = '".$a_val['sequence']."', ";
		$qtxt.="areaWidth = '".$a_val['width']."', areaType = '".$a_val['type']."'"; //-debug-// echo $qtxt."\n\n";

		$q=mysql_query($qtxt);

		if ($a_val["type"] == "content") {
			list($idContent)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

			if (($a_key != "0") && ($a_key != "")  && (is_array($content)) && (isset($content[$a_key]))) {
				// print_r($content[$a_key]);

				foreach($content[$a_key] as $cnt_key=>$cnt_val) {
					$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_area_subdivision ";
					$qtxt.="SET idArea = '$idArea', idParentSub = '$idContent', sequence = '".$cnt_val['sequence']."', ";
					$qtxt.="areaWidth = '".$cnt_val['width']."', areaType = '".$cnt_val['type']."'";  //-debug-// echo $qtxt."\n\n";

					$q=mysql_query($qtxt);
				}
			}
		}

	} //-debug-// echo("</pre>");

	return true;

	/*
	while( list(,$param_sub) = each($main_sub) ) {
		mysql_query("
		INSERT INTO ".$GLOBALS["prefix_cms"]."_area_subdivision
		SET idArea = '$idArea',
			idParentSub = 0,
			sequence = '".$param_sub['sequence']."',
			areaWidth = '".$param_sub['width']."',
			areaType = '".$param_sub['type']."'");
		if($param_sub['type'] == 'content' ) {
			list( $idContent ) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		}
	}

	if( ($idContent !== false) && ($layout->getParamContentNumber() > 0) ) {
		$content_sub = $layout->getParamContent();
		while( list(,$param_cont_sub) = each($content_sub) ) {
			mysql_query("
			INSERT INTO ".$GLOBALS["prefix_cms"]."_area_subdivision
			SET idArea = '$idArea',
				idParentSub = '$idContent',
				sequence = '".$param_cont_sub['sequence']."',
				areaWidth = '".$param_cont_sub['width']."',
				areaType = '".$param_cont_sub['type']."'");
		}
	}
	return true;
	*/
	// die();
}


function update_home() {

	$q=mysql_query("SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE lev='1';");

	if (($q) && (mysql_num_rows($q) > 0)) {
		$i=0;
		while ($row=mysql_fetch_array($q)) {
			$lang_arr[$i]=$row["idArea"];
			$i++;
		}
	}

	if (is_array($lang_arr)) {
		foreach ($lang_arr as $key=>$val) {

			// temp: (todo: impostare langdef=1 al momento della creazione della lingua)
			// $q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_area SET langdef='1' WHERE idArea='$val';");

			// resetto tutte le home
			$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_area SET home='0' WHERE idParent='$val';");

			// imposto la home
			$q=mysql_query("SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE idParent='$val' AND publish='1' ORDER BY path;");
			if (($q) && (mysql_num_rows($q) > 0)) {
				$row=mysql_fetch_array($q);
				$idArea=$row["idArea"];
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_area SET home='1' WHERE idArea='$idArea';");
			}
		}
	}
}


function manpage() {
	////-TP// funAdminAccess('OP');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

	$tree = new pageDb();
	$treeView = new page_TreeView($tree, FALSE);
	$treeView->parsePositionData($_POST, $_POST, $_POST);
	//area title

	if ((isset($_POST['inspage'])) && (!isset($_POST["undo"]))) {

		if ($_POST["ispage"]) {
			$langdef=0;
			$title=$_POST['title'];
		}
		else {
			$langdef=1;
			$lang_list=$GLOBALS['globLangManager']->getAllLangCode();
			$title=$lang_list[$_POST['title']];
		}

		if ((isset($_POST["uselink"])) && ($_POST["uselink"]))
			$link=$_POST['link'];
		else
			$link="";

		$show_in_menu =(isset($_POST["show_in_menu"]) ? (int)$_POST["show_in_menu"] : 0);
		$show_in_macromenu =(isset($_POST["show_in_macromenu"]) ? (int)$_POST["show_in_macromenu"] : 0);

		$idArea = $tree->addItemById( $treeView->getSelectedFolderId(), $title, $_POST['alias'], $_POST['template'], $_POST['mr_title'], $_POST['browser_title'], $_POST['keyword'], $_POST['sitedesc'], $link, '0', $langdef, $show_in_menu, $show_in_macromenu );
		if($idArea !== false) {
			if ($_POST["ispage"]) { // se e' una pagina e non una lingua (!root)
				//createPerm( $_POST['idGroups'], $idArea );
				save_page_groups($idArea);
				createSubdivision( $_POST['subdivision'], $idArea );
			}
			else { // Imposto i permessi per la lingua.
				//createPerm(array(0), $idArea );
			}
			update_home();

			require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
			$replace=array("[title]"=>$title);
			sendCmsGenericEvent(
					/* members:    */ FALSE,
					/* class:      */ "PageCreated",
					/* module:     */ "admin_manpage",
					/* action:     */ "add",
					/* log:        */ "Added page ".$idArea,
					/* sub_string: */ "_PAGE_ADDED_ALERT_SUB",
					/* txt_string: */ "_PAGE_ADDED_ALERT_TXT",
					/* replace:    */ $replace
				);

		}
		resetTreeState("manpage");
		jumpTo(' index.php?modname=manpage&op=manpage'); // <- It's a hot day.. Refresh!!
	}
	else if((isset($_POST['updpage'])) && (!isset($_POST["undo"]))) {
		$idArea=(int)$_POST["idItem"];
		$arr["idItem"]=$idArea;
		$arr["title"]=$_POST['title'];
		$arr["alias"]=$_POST['alias'];
		$arr["template"]=$_POST['template'];
		$arr["mr_title"]=$_POST['mr_title'];
		$arr["browser_title"]=$_POST['browser_title'];
		$arr["keyword"]=$_POST['keyword'];
		$arr["sitedesc"]=$_POST['sitedesc'];
		if ($_POST["uselink"])
			$arr["link"]=$_POST['link'];
		else
			$arr["link"]="";
		$arr["publish"]=$_POST['publish'];
		$arr["show_in_menu"]=$_POST['show_in_menu'];
		$arr["show_in_macromenu"]=$_POST['show_in_macromenu'];
		//$idArea=$tree->modifyItem($arr);
		$tree->modifyItem($arr);

		require_once($GLOBALS["where_cms"] . "/lib/lib.cms_common.php");
		$replace=array("[title]"=>$_POST['title']);
		sendCmsGenericEvent(
				/* members:    */ FALSE,
				/* class:      */ "PageModified",
				/* module:     */ "admin_manpage",
				/* action:     */ "edit",
				/* log:        */ "Edited page ".$idArea,
				/* sub_string: */ "_PAGE_EDITED_ALERT_SUB",
				/* txt_string: */ "_PAGE_EDITED_ALERT_TXT",
				/* replace:    */ $replace
			);


		// Unset template preference
		require_once($GLOBALS["where_cms"] . "/lib/lib.area.php");
		$key=array("page_template", $idArea, "name");
		setItemValue($key, FALSE, TRUE);

		// Aggiorno i gruppi:
		save_page_groups($_POST['idItem']);
		//updatePerm($_POST['idGroups'], $_POST['idItem']);
		jumpTo(' index.php?modname=manpage&op=manpage');
	}
	else if((isset($_POST['updlang'])) && (!isset($_POST["undo"]))) {
		$browser_title=$_POST['browser_title'];
		$keyword=$_POST['keyword'];
		$sitedesc=$_POST['sitedesc'];

		$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_area SET ";
		$qtxt.="browser_title='".$browser_title."', keyword='".$keyword."', sitedesc='".$sitedesc."' ";
		$qtxt.="WHERE idArea='".(int)$_POST["item_id"]."'";
		$q=mysql_query($qtxt);

		jumpTo(' index.php?modname=manpage&op=manpage');
	}


	switch( $treeView->op ) {
		case 'newfolder' : {
			saveTreeState("manpage");
			newpage($treeView);
		};break;
		case "pagemodblock" : {
			saveTreeState("manpage");
			pagemodblock($treeView);
		};break;

		case "editpage" : {
			saveTreeState("manpage");
			editpage($treeView);
		} break;

		case "editpagelang" : {
			saveTreeState("manpage");
			editpagelang($treeView);
		} break;

		case "pagedel" : {
			//saveTreeState("manpage");
			pagedel($treeView);
		};break;

		default:
			page_display( $treeView );
		break;
	}

}

function page_display( $treeView ) {
	////-TP// funAdminAccess('OP');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

	$out->add(getTitleArea($lang->def("_MANPAGE"), "manpage"));

	$user_level=$GLOBALS["current_user"]->getUserLevelId();
	if ($user_level != ADMIN_GROUP_GODADMIN) {
		$treeView->setUseAdminFilter(TRUE);
	}


	$out->add('<form method="post" action="index.php?modname=manpage&amp;op=manpage">'
		.'<div class="std_block">');
	$out->add($treeView->load());
	$out->add($treeView->loadActions());
	$out->add('</div>'
		.'</form>');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupFormDialogBox(
		'',
		'index.php?modname=manpage&op=delpagenow',
		'input[name*=_pagedel_]',
		$lang->def('_AREYOUSURE'),
		$lang->def('_CONFIRM'),
		$lang->def('_UNDO'),
		'function(o) { return o.title; }',
		'_pagedel_',
		'idArea',
		'conf_del'
	);
}

function getSubdivisionList($path = "") {

	if ($path == "")
		$path=$GLOBALS['where_cms'].'/lib/page_models/';

	$mod_d = dir($path);
	while($ele = $mod_d->read()) {
		if (@filesize($path.$ele) > 0) {
			if( ereg('layout', $ele) ) {
				$explode = explode('.', $ele);
				$mod_array[] = $explode[1];
			}
		}
	}
	closedir($mod_d->handle);
	sort($mod_array);
	reset($mod_array);
	return $mod_array;
}

function newpage( $treeView ) {
	checkPerm('add');

	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
	$form=new Form();


	$parent_id =$treeView->getSelectedFolderId();
	manpage_checkPagePerm($parent_id);

	$folder=$treeView->tdb->getFolderById($parent_id);
	$level=$folder->level;

	$page=(bool)($level > 0);


	$out->setWorkingZone('content');


	$back_ui_url="index.php?modname=manpage&amp;op=manpage";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MANPAGE");
	if ($page)
		$title_arr[]=$lang->def("_ADDPAGE");
	else
		$title_arr[]=$lang->def("_ADDLANGUAGE");
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

 	$url ="index.php?modname=manpage&amp;op=manpage";
 	$out->add($form->openForm("page_form", $url));

	$out->add($treeView->printState());



 	$out->add($form->openElementSpace());

 	$out->add($form->getHidden("inspage", "inspage", "1"));


	if ($page) {
		$out->add($form->getTextfield($lang->def("_TITLE").":", "title", "title", 255, $lang->def("_TITLE")));

		if ($level == 1) { // if it's a macroarea..
			$out->add($form->getTextfield($lang->def("_ALIAS").":", "alias", "alias", 255, $lang->def("_ALIAS")));
		}
		else {
			$out->add($form->getHidden("alias", "alias", ""));
		}

 		$out->add($form->getHidden("ispage", "ispage", "1"));
	}
	else {
		$lang_list=$GLOBALS['globLangManager']->getAllLangCode();
		$out->add($form->getDropdown($lang->def("_LANGUAGE").":", "title", "title", $lang_list, ""));
		$out->add($form->getHidden("ispage", "ispage", "0"));
	}


	$default_template=getDefaultTemplate();
	if (!getAccessibilityStatus()) {

		$onchange ="onchange=\"javascript:template_preview.src='".$GLOBALS["where_cms_relative"]."/templates/'+this.value+'/template.jpg';";
		$onchange.="template_preview.alt='template '+this.value;\"";

		$preview ="<img name=\"template_preview\" src=\"".$GLOBALS["where_cms_relative"]."/templates/".$default_template."/";
		$preview.="template.jpg\" alt=\"".$lang->def("_PREVIEW")."\" />\n";
	}
	else {
		$onchange="";
		$preview="";
	}

	$templ = getTemplateList(true, "cms");
	$out->add($form->getLineDropdown('form_line_l', 'floating',$lang->def("_TEMPLATE").":", "dropdown", "template", "template", $templ, $default_template, $onchange, $preview, ""));

	if ($page) {

		if ($GLOBALS["cms"]["use_mod_rewrite"] == "on") {
			$out->add($form->getTextfield($lang->def("_MR_TITLE").":", "mr_title", "mr_title", 255, ""));
		}
		else {
			$out->add($form->getHidden("mr_title", "mr_title", ""));
		}
		$out->add($form->getTextfield($lang->def("_BROWSER_TITLE").":", "browser_title", "browser_title", 255, ""));
		$out->add($form->getSimpleTextarea($lang->def("_KEYWORD").":", "keyword", "keyword", ""));
		$out->add($form->getSimpleTextarea($lang->def("_DESCRIPTION").":", "sitedesc", "sitedesc", ""));


		$out->add($form->getCheckbox($lang->def("_USELINK").":", "uselink", "uselink", "1", false));
		$out->add($form->getTextfield($lang->def("_LINK").":", "link", "link", 255, "http://"));

		$out->add($form->getCheckbox($lang->def("_SHOW_IN_MENU"), "show_in_menu", "show_in_menu", "1", true));
		if ($level == 1) { // if it's a macroarea..
			$out->add($form->getCheckbox($lang->def("_SHOW_IN_MACROMENU"), "show_in_macromenu", "show_in_macromenu", "1", true));
		}
		else {
			$out->add($form->getHidden("show_in_macromenu", "show_in_macromenu", "0"));
		}


		$out->add($form->closeElementSpace());


		$out->add($form->openButtonSpace());
		$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
		$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
		$out->add($form->closeButtonSpace());

		$out->add($form->openElementSpace());

		$out->add(page_group_list($form, $lang, 0, true));

		$out->add('<div class="title">'.$lang->def("_SUBDIVISION").'</div>');
		$sub = getSubdivisionList();
		$i = 1;
		while( list(,$sub_name) = each($sub) ) {
			$out->add('<label for="lay'.$i.'">'
				.'&nbsp;<img src="'.getPathImage().'page_models/layout'.$sub_name.'.gif" alt="'.$sub_name.'" />'
				.'&nbsp;</label>'
				.'<input type="radio" id="lay'.$i.'" value="'.$sub_name.'" name="subdivision"'
				.( ($i == 1) ? ' checked="checked"' : '').' />&nbsp;'."\n");
			if( ($i++ % 4) == 0 ) $out->add('<br /><br />');
		}
	}


  $out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");

return 0;
	echo '<div class="title"><label for="templ">'._TEMPLATE.'</label></div>'
		.'<select class="dropSelect" id="templ" name="template">';
	$template = giveTemplateList();
	while(list( ,$valueT)= each($template)) {
		echo '<option value="'.$valueT.'">'.$valueT.'</option>';
	}

	echo '</select><br /><br />';

	if ($page) {
		echo '<div class="title"><label for="keyword">'._KEYWORD.'</label></div>'
			.'<textarea id="keyword" name="keyword" cols="60" rows="3"></textarea>'
			.'<br /><br />'
			. '<div class="title"><label for="link">'._LINK.'</label></div>'
			.'<input type="checkbox" id="uselink" name="uselink" value="1" />'
			._USELINK.":<br />\n"
			.'<input class="textfield" type="text" id="link" name="link" maxlength="255" value="http://" />'
			.'<br /><br />'
			.'<input class="button" type="submit" name="insert" value="'._INSERT.'" />'
			.'<br /><br />';

		$groups = listGroup();
		echo '<div class="title">'._GROUPS.'</div>'
			.'<div class="groupsperm">';
		while(list($idCommon, $nameGroup) = each($groups)) {
			echo '<input id="group'.$idCommon.'" type="checkbox" name="idGroups[]" value="'.$idCommon.'" />'
				.'&nbsp;<label for="group'.$idCommon.'">'.$nameGroup.'</label><br />';
		}
		echo '</div>'."\n\n";

	}

	if ($page) {
		echo '<div class="title">'._SUBDIVISION.'</div>';
		$sub = getSubdivisionList();
		$i = 1;
		while( list(,$sub_name) = each($sub) ) {
			echo '<label for="lay'.$i.'">'
				.'&nbsp;<img src="'.getPathImage().'page_models/layout'.$sub_name.'.gif" alt="'.$sub_name.'" />'
				.'&nbsp;</label>'
				.'<input type="radio" id="lay'.$i.'" value="'.$sub_name.'" name="subdivision"'
				.( ($i == 1) ? ' checked="checked"' : '').' />&nbsp;'."\n";
			if( ($i++ % 4) == 0 ) echo '<br /><br />';
		}
	}
		/*

		.'<img src="'.getPathImage().'"manpage/2.gif alt="'._TWO.'" />'
		.'<input type="radio" value="2" name="subdivision" />'
		.'<img src="'.getPathImage().'"manpage/3.gif alt="'._THREE.'" />'
		.'<input type="radio" value="3" name="subdivision" />'
		.'<img src="'.getPathImage().'"manpage/4.gif alt="'._FOUR.'" />'
		.'<input type="radio" value="4" name="subdivision" />';
	*/
	echo '<br />'
		.'<input class="button" type="submit" name="insert" value="'._INSERT.'" />'
		.'</div>'
		.'</form>';
}


function editpage($treeView) {
	checkPerm('mod');

	//include('admin/modules/group/groupUtils.php');
	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');


	$id=$treeView->idSelected;
	manpage_checkPagePerm($id);

	// Dati pagina
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='$id';";
	$q=mysql_query($qtxt);
	$row=mysql_fetch_array($q);
	extract($row);
	$db_tmpl=$template;

	// Dati gruppi
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_perm WHERE idArea='$id';";
	$q=mysql_query($qtxt);
	$db_group=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_group[]=$row["idGroup"];
		}
	}


	$back_ui_url="index.php?modname=manpage&amp;op=manpage";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MANPAGE");
	$title_arr[]=$lang->def("_EDITPAGE").": ".$title;
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	$out->add($treeView->printState());

	$folder=$treeView->tdb->getFolderById($treeView->getSelectedFolderId());
	$path=$folder->path;

	$page=(bool)(substr_count($path, "/") > 0);

 	$url ="index.php?modname=manpage&amp;op=manpage";
 	$out->add($form->openForm("page_form", $url));

 	$out->add($form->openElementSpace());

 	$out->add($form->getHidden("ispage", "ispage", "1"));
 	$out->add($form->getHidden("updpage", "updpage", "1"));
 	$out->add($form->getHidden("idItem", "idItem", $id));


	$out->add($form->getTextfield($lang->def("_TITLE").":", "title", "title", 255, $title));

	if ($lev == 2) { // if it's a macroarea..
		$out->add($form->getTextfield($lang->def("_ALIAS").":", "alias", "alias", 255, $alias));
	}
	else {
		$out->add($form->getHidden("alias", "alias", ""));
	}

	if (!getAccessibilityStatus()) {

		$onchange ="onchange=\"javascript:template_preview.src='".$GLOBALS["where_cms_relative"]."/templates/'+this.value+'/template.jpg';";
		$onchange.="template_preview.alt='template '+this.value;\"";

		$preview ="<img name=\"template_preview\" src=\"".$GLOBALS["where_cms_relative"]."/templates/".$db_tmpl."/";
		$preview.="template.jpg\" alt=\"".$lang->def("_PREVIEW")."\" />\n";

	}
	else {
		$onchange="";
		$preview="";
	}

	$templ = getTemplateList(true, "cms");
	$out->add($form->getLineDropdown('form_line_l', 'floating',$lang->def("_TEMPLATE").":", "dropdown", "template", "template", $templ, $db_tmpl, $onchange, $preview, ""));

	if (($GLOBALS["cms"]["use_mod_rewrite"] == "on") && (!$home)) {
		$out->add($form->getTextfield($lang->def("_MR_TITLE").":", "mr_title", "mr_title", 255, $mr_title));
	}
	else {
		$out->add($form->getHidden("mr_title", "mr_title", $mr_title));
	}
	$out->add($form->getTextfield($lang->def("_BROWSER_TITLE").":", "browser_title", "browser_title", 255, $browser_title));
	$out->add($form->getSimpleTextarea($lang->def("_KEYWORD").":", "keyword", "keyword", $keyword));
	$out->add($form->getSimpleTextarea($lang->def("_DESCRIPTION").":", "sitedesc", "sitedesc", $sitedesc));

	$sel=($link != "" ? true : false);
	if ($link == "") $link="http://";

	$out->add($form->getCheckbox($lang->def("_USELINK").":", "uselink", "uselink", "1", $sel));
	$out->add($form->getTextfield($lang->def("_LINK").":", "link", "link", 255, $link));

	$out->add($form->getCheckbox($lang->def("_SHOW_IN_MENU"), "show_in_menu", "show_in_menu", "1", $show_in_menu));
	if ($lev == 2) { // if it's a macroarea..
		$out->add($form->getCheckbox($lang->def("_SHOW_IN_MACROMENU"), "show_in_macromenu", "show_in_macromenu", "1", $show_in_macromenu));
	}
	else {
		$out->add($form->getHidden("show_in_macromenu", "show_in_macromenu", "0"));
	}

	$out->add(page_group_list($form, $lang, $id));

  $out->add($form->closeElementSpace());


	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

 	$url ="index.php?modname=manpage&amp;op=manpage&amp;op=changelayout";
 	$out->add($form->openForm("page_form", $url));
 	//$out->add($form->openElementSpace());
 	$out->add($form->getHidden("idArea", "idArea", $id));
	//$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('changelayout', 'changelayout', $lang->def("_CHANGE_LAYOUT")));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add("</div>\n");
	
}


function editpagelang($treeView) {
	checkPerm('mod');

	require_once($GLOBALS["where_cms"]."/lib/lib.area.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$id=$treeView->idSelected;
	manpage_checkPagePerm($id);

	// Dati pagina
	$qtxt="SELECT title, browser_title, keyword, sitedesc FROM ".
		$GLOBALS["prefix_cms"]."_area WHERE idArea='$id'";
	$q=mysql_query($qtxt);
	$row=mysql_fetch_array($q);
	extract($row);

	$back_ui_url="index.php?modname=manpage&amp;op=manpage";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MANPAGE");
	$title_arr[]=$lang->def("_EDIT_LANG").": ".$title;
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

 	$url ="index.php?modname=manpage&amp;op=manpage";
 	$out->add($form->openForm("page_form", $url));

 	$out->add($form->openElementSpace());

	$out->add($treeView->printState());

 	$out->add($form->getHidden("updlang", "updlang", "1"));
 	$out->add($form->getHidden("item_id", "item_id", $id));
	
	dd($form->getTextfield($lang->def("_BROWSER_TITLE").":", "browser_title", "browser_title", 255, $browser_title));
	$out->add($form->getSimpleTextarea($lang->def("_KEYWORD").":", "keyword", "keyword", $keyword));
	$out->add($form->getSimpleTextarea($lang->def("_DESCRIPTION").":", "sitedesc", "sitedesc", $sitedesc));


  $out->add($form->closeElementSpace());


	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function pagemodblock($treeView) {
	checkPerm('mod');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

	if($treeView !== false) $idArea = $treeView->getSelectedId();
	elseif(isset($_POST['idArea'])) $idArea = $_POST['idArea'];
	elseif(isset($_GET['idArea'])) $idArea = $_GET['idArea'];

	manpage_checkPagePerm($idArea);

	$back_ui_url="index.php?modname=manpage&amp;op=manpage";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MANPAGE");
	$title_arr[]=$lang->def("_ACTUALPAGE").": ".getPageInfo("title", $idArea);
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add('</div>'); // std_block

	$out->add('<form method="post" action="index.php?modname=manpage&amp;op=pagemodblock">');
	//$out->add('<b>'.$lang->def("_ACTUALPAGE").'</b>'
	//$out->add('<br /><a href="index.php?modname=manpage&amp;op=manpage">'.$lang->def("_BACK").'</a>');
	$out->add('<div class="admin_principalbox">');
	loadAdminArea( $idArea );
	$out->add('</div>'); // admin_principalbox

	$out->add('</form>');
	$out->add('<div class="no_float"></div>');

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
}

function pagedel($tree) {
	checkPerm('del');


	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	//require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
 	$form=new Form();

	if($tree !== false) $idArea = $tree->getSelectedId();
	elseif(isset($_POST['idArea'])) $idArea = $_POST['idArea'];
	elseif(isset($_GET['idArea'])) $idArea = $_GET['idArea'];

	manpage_checkPagePerm($idArea);

	$title=getPageInfo("title", $idArea);



	if (isset($_POST["conf_del"])) {

		// remove roles:
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();
		$acl_manager->deleteRoleFromPath("/cms/page/".$idArea."/");

		delete_all_page_block($idArea);
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_subdivision WHERE idArea='$idArea';");
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_perm WHERE idArea='$idArea';");
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='$idArea';");

		jumpTo("index.php?modname=manpage&amp;op=manpage");
	}
	else if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=manpage&amp;op=manpage");
	}
	else {

		$back_ui_url="index.php?modname=manpage&amp;op=manpage";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_MANPAGE");
		$title_arr[]=$lang->def("_DELETEPAGE").": ".$title;
		$out->add(getTitleArea($title_arr, "manpage"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$url ="index.php?modname=manpage&amp;op=delpagenow";
		$out->add($form->openForm("page_form", $url));

		$out->add($form->getHidden("idArea", "idArea", $idArea));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");

	}

}

function getPageInfo($fields, $idArea) {

	$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_area ";
	$qtxt.="WHERE idArea = '".(int)$idArea."'";

	$q=mysql_query($qtxt);

	$res=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {
			$res=$row;
		}
	}

	if (count($res) == 1)
		$res=current($res);

	return $res;
}

// XXX: addblock

function addblock() {
	checkPerm('add');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '".$_GET['sub_id']."'"));

	manpage_checkPagePerm($idArea);

 	$out->setWorkingZone('content');

	$home_url="index.php?modname=manpage&amp;op=manpage";
	$back_ui_url="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea;
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_MANPAGE");
	$title_arr[$back_ui_url]=$lang->def("_ACTUALPAGE").": ".getPageInfo("title", $idArea);
	$title_arr[]=$lang->def("_SELABLOCKTOADD");
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$reBlock = mysql_query("
	SELECT name, folder, label
	FROM ".$GLOBALS["prefix_cms"]."_blocktype
	WHERE 1 ORDER BY name");

	$tab=new typeOne();

	$head_type=array();
	$tab->setColsStyle($head_type);

	while( list($name_block, $folder, $block_label) = mysql_fetch_row($reBlock) ) {
		$blk_desc=$lang->def($block_label."_DESC");
		$block_label=$lang->def($block_label);
		$link ='<a class="selmodule" href="index.php?modname=manpage&amp;op=addselectedblock&amp;name_block='.$name_block.'&amp;idArea='.$idArea;
		if ($folder != "")
			$link.="&amp;folder=".$folder;
		$link.='&amp;sub_id='.$_GET['sub_id'].'">'.$block_label.'</a><br />';
		$tab->addBody(array($link, $blk_desc));
	}
	$out->add($tab->getTable());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add('</div>');
}

function addselectedblock() {
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	if(isset($_POST['sub_id'])) $idS = $_POST['sub_id'];
	elseif(isset($_GET['sub_id'])) $idS = $_GET['sub_id'];
	else return;

	if(isset($_POST['name_block'])) $name_block = $_POST['name_block'];
	elseif(isset($_GET['name_block'])) $name_block = $_GET['name_block'];
	else return;

	if(isset($_POST['folder'])) $folder = $_POST['folder'];
	elseif(isset($_GET['folder'])) $folder = $_GET['folder'];

	if ($folder == "")
		$folder=$name_block;

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '$idS'"));


	$fn=$GLOBALS["where_cms"]."/admin/modules/block_".$folder."/util.".$name_block.".php";

	if (file_exists($fn)) {
		require_once($fn);
		$block_id=block_BlockAdd($idS, $name_block);
		$function=$name_block."BlockAdd"; // <- used for save custom/default options
		if ($block_id) {
			if (function_exists($function)) {
				eval($function."(".$block_id.", ".$idS.");");
			}
			
			$query =	"UPDATE ".$GLOBALS["prefix_cms"]."_area"
						." SET last_modify = '".date('Y-m-d H:i:s')."'"
						." WHERE idArea = '".$_GET['idArea']."'";
			
			mysql_query($query);
			
			jumpTo("index.php?modname=manpage&op=blockcreated&block_id=".$block_id."&sub_id=".$idS);
		}
	}

}

function block_BlockAdd($sub_id, $b_name) {

	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	//find sequence
	list($seq) =  mysql_fetch_row(mysql_query("
	SELECT MAX(sequence) + 1
	FROM ".$GLOBALS["prefix_cms"]."_area_block
	WHERE idSubdivision = '$sub_id'"));
	//insert block
	$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_area_block
		SET idSubdivision = '$sub_id',
		block_name = '$b_name',
		sequence = '$seq'";
	$q=mysql_query($qtxt);
	if(!$q) return false;
	list($block_id) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));


	// Default block permissions:
	$acl=$GLOBALS["current_user"]->getAcl();
	$anon_st=$acl->getUserST("/Anonymous");
	$reg_st=$acl->getGroupST("/oc_0");
	$set_perm_arr[$anon_st]["view"]=1;
	$set_perm_arr[$reg_st]["view"]=1;
	$set_perm_arr["save"]=true;

	save_block_groups($block_id, $set_perm_arr);

	return $block_id;
}

function blockCreated() {

	if (isset($_GET["block_id"]))
		$block_id=(int)$_GET["block_id"];

	if (isset($_GET["sub_id"]))
		$sub_id=(int)$_GET["sub_id"];

	if (($block_id == 0) || ($sub_id == 0))
		jumpTo("index.php?modname=manpage&op=manpage");

	$url ="index.php?modname=manpage&amp;op=modblock&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=".$sub_id;
	$url.="&amp;msg=new";
	jumpTo($url);

// OLD CODE:
return 0;

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

 	$form=new Form();

 	$out->setWorkingZone('content');
 	$out->add("<div class=\"std_block\">\n");

 	$out->add($form->openForm("block_form", "index.php?modname=manpage&amp;op=modblock&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=".$sub_id));

	$out->add($lang->def("_BLOCK_CREATED")."<br /><br />\n");

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('continue', 'continue', $lang->def("_CONTINUE")));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());
	$out->add("</div>\n");

}

function addselectedblock_old() {


	if(isset($_POST['sub_id'])) $idS = $_POST['sub_id'];
	elseif(isset($_GET['sub_id'])) $idS = $_GET['sub_id'];
	else return;

	if(isset($_POST['name_block'])) $name_block = $_POST['name_block'];
	elseif(isset($_GET['name_block'])) $name_block = $_GET['name_block'];
	else return;

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '$idS'"));

	$back_url = array(
		'address' => 'index.php?modname=manpage&amp;op=addselectedblock',
		'backurl' => 'index.php?modname=manpage&amp;op=pagemodblock&amp;idArea='.$idArea,
		'param' => array( 'idSubdivision' => $idS, 'name_block' => $name_block )
	);
	$block = createBlockIstance( $name_block, 0, $idS, $back_url);
	$block->addBlock();
}

// XXX: modblock
function modblock() {

	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
 	$form=new Form();


	if(isset($_POST['block_id'])) $block_id = $_POST['block_id'];
	elseif(isset($_GET['block_id'])) $block_id = $_GET['block_id'];
	else return;

	if(isset($_POST['sub_id'])) $sub_id = $_POST['sub_id'];
	elseif(isset($_GET['sub_id'])) $sub_id = $_GET['sub_id'];
	else return;

	$message ="";
	if (isset($_GET["msg"])) {
		switch ($_GET["msg"]) {
			case "new": {
				$message =getResultUi($lang->def("_BLOCK_CREATED"));
			} break;
		}
	}

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '$sub_id'"));

	manpage_checkPagePerm($idArea);

	list($name_block, $folder) = mysql_fetch_row(mysql_query("
	SELECT t1.block_name, t2.folder
	FROM ".$GLOBALS["prefix_cms"]."_area_block as t1, ".$GLOBALS["prefix_cms"]."_blocktype as t2
	WHERE t1.block_name=t2.name AND t1.idBlock = '$block_id'"));

	if ($folder == "")
		$folder=$name_block;

	$fn=$GLOBALS["where_cms"]."/admin/modules/block_".$folder."/util.".$name_block.".php";

	$blk_op=importVar("blk_op");

	if ((file_exists($fn)) && ($blk_op == "")) {
		require_once($fn);
		block_BlockEdit_openForm($out, $lang, $form, $sub_id, $block_id, $idArea, $message);
		$function=$name_block."BlockEdit";
		if (function_exists($function)) {
			eval($function."(\$out, \$lang, \$form, \$block_id, \$sub_id);");
		}
		block_BlockEdit_closeForm($out, $lang, $form, $block_id, $idArea);
	}
	else if ((file_exists($fn)) && ($blk_op != "")) {
		require_once($fn);
		$out->add("<div class=\"std_block\">\n");
		$function=$name_block."BlockOption";
		if (function_exists($function)) {
			eval($function."(\$out, \$lang, \$form, \$block_id, \$sub_id, \$blk_op);");
		}
		$out->add("</div>\n");
	}

}

function block_BlockEdit_openForm(& $out, & $lang, & $form, $sub_id, $block_id, $idArea, $message="") {

 	$out->setWorkingZone('content');
	$home_url="index.php?modname=manpage&amp;op=manpage";
	$back_ui_url="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea;
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_MANPAGE");
	$title_arr[$back_ui_url]=$lang->def("_ACTUALPAGE").": ".getPageInfo("title", $idArea);
	$title_arr[]=$lang->def("_EDIT_BLOCK");
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add($message);
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

 	$url ="index.php?modname=manpage&amp;op=saveblock&amp;write=1&amp;block_id=".$block_id;
 	$url.="&amp;sub_id=".$sub_id."&amp;idArea=".$idArea;
 	$out->add($form->openForm("block_form", $url));

 	$out->add($form->openElementSpace());

}

function block_BlockEdit_closeForm(& $out, & $lang, & $form, & $block_id, $idArea) {

  $out->add($form->closeElementSpace());


	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());

	$out->add($form->openElementSpace());
	$out->add(block_group_list($form, $lang, $block_id));
 	$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save_2', 'save', $lang->def("_SAVE")));
	$out->add($form->getButton('undo_2', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());

	$out->add($form->closeForm());

	$back_ui_url="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea;
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");

}

function saveblock() {
	checkPerm('mod');

	require_once($GLOBALS["where_cms"]."/admin/modules/manpage/manBlocks.php");

	if(isset($_POST['block_id'])) $block_id = $_POST['block_id'];
	elseif(isset($_GET['block_id'])) $block_id = $_GET['block_id'];
	else return;

	if(isset($_POST['sub_id'])) $sub_id = $_POST['sub_id'];
	elseif(isset($_GET['sub_id'])) $sub_id = $_GET['sub_id'];
	else return;

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '$sub_id'"));

	manpage_checkPagePerm($idArea);

	list($name_block, $folder) = mysql_fetch_row(mysql_query("
	SELECT t1.block_name, t2.folder
	FROM ".$GLOBALS["prefix_cms"]."_area_block as t1, ".$GLOBALS["prefix_cms"]."_blocktype as t2
	WHERE t1.block_name=t2.name AND t1.idBlock = '$block_id'"));

	if ($folder == "")
		$folder=$name_block;

	$fn=$GLOBALS["where_cms"]."/admin/modules/block_".$folder."/util.".$name_block.".php";

	if (file_exists($fn)) {

		// Saving block groups
		save_block_groups($block_id);

		require_once($fn);
		$function=$name_block."BlockSave";
		if (function_exists($function)) {
			eval($function."(\$block_id, \$sub_id);");
		}
		jumpTo("index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea);
	}

}

function delblock() {
	checkPerm('del');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
 	$form=new Form();


	if(isset($_POST['block_id'])) $block_id = $_POST['block_id'];
	elseif(isset($_GET['block_id'])) $block_id = $_GET['block_id'];
	else return;

	if(isset($_POST['sub_id'])) $sub_id = $_POST['sub_id'];
	elseif(isset($_GET['sub_id'])) $sub_id = $_GET['sub_id'];
	else return;

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '$sub_id'"));

	manpage_checkPagePerm($idArea);

	list($title, $block_name, $folder, $block_label) = mysql_fetch_row(mysql_query("
	SELECT t1.title, t1.block_name, t2.folder, t2.label
	FROM ".$GLOBALS["prefix_cms"]."_area_block as t1, ".
	$GLOBALS["prefix_cms"]."_blocktype as t2
	WHERE t1.idBlock = '$block_id' AND t2.name=t1.block_name"));

	if ($folder == "")
		$folder=$block_name;

	if (isset($_POST["conf_del"])) {

		$fn=$GLOBALS["where_cms"]."/admin/modules/block_".$folder."/util.".$block_name.".php";

		if (file_exists($fn)) {
			require_once($fn);
			block_BlockDel($block_id, $sub_id); // Default values
			$function=$block_name."BlockDel";   // Custom values (optional)
			if (function_exists($function)) {
				eval($function."(\$block_id, \$sub_id);");
			}
			jumpTo("index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea);
		}

	}
	else if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea);
	}
	else {

		$home_url="index.php?modname=manpage&amp;op=manpage";
		$back_ui_url="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea=".$idArea;
		$title_arr=array();
		$title_arr[$home_url]=$lang->def("_MANPAGE");
		$title_arr[$back_ui_url]=$lang->def("_ACTUALPAGE").": ".getPageInfo("title", $idArea);
		$title_arr[]=$lang->def("_DELETE_BLOCK").": ".$title;
		$out->add(getTitleArea($title_arr, "manpage"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$url ="index.php?modname=manpage&amp;op=delblock";
		$url.="&amp;sub_id=".$sub_id."&amp;block_id=".$block_id;
		$out->add($form->openForm("block_form", $url));


		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLEBLOCK').' :</span> '.$title.'<br />'.
			'<span class="text_bold">'.$lang->def('_TYPEBLOCK').' :</span> '.$lang->def($block_label).'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");

	}

}

function block_BlockDel($block_id, $sub_id) {

	mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE idBlock='$block_id';");
	mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_group WHERE idBlock='$block_id';");
	mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_option WHERE idBlock='$block_id';");
	mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_option_text WHERE idBlock='$block_id';");

}

// XXX: delblock
function delblock_old() {

	list($idArea) = mysql_fetch_row(mysql_query("
	SELECT idArea
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idSubdivision = '".(int)$_GET['sub_id']."'"));

	list($block_name, $title) = mysql_fetch_row(mysql_query("
	SELECT block_name, title
	FROM ".$GLOBALS["prefix_cms"]."_area_block
	WHERE idBlock = '".(int)$_GET['block_id']."'"));

	if(isset($_GET['confirm'])) {

		$block = createBlockIstance( $block_name, $_GET['block_id'], (int)$_GET['sub_id'], array('','', array()) );
		if($block->deleteBlock())
			jumpTo('index.php?modname=manpage&op=pagemodblock&idArea='.$idArea);
		else {
			errorCommunication(_ERREMBLOCK);
		}
	}
	else {
		echo '<div class="std_block">'
			.'<div class="title">'._AREYOUSURE.'</div>'
			.$block_name.' : '.$title.'<br />'
			.'[ <a href="index.php?modname=manpage&amp;op=delblock&amp;sub_id='.$_GET['sub_id'].'&amp;block_id='
			.$_GET['block_id'].'&amp;confirm=1">'._YES.'</a>'
			.' | <a href="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea='.$idArea.'">'._NO.'</a> ]'
			.'</div>';
	}
}

function move_block($dir, $block_id, $idSub) {
	require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

	$table=$GLOBALS["prefix_cms"]."_area_block";
	$where="idSubdivision='".$idSub."'";

	utilMoveItem($dir, $table, "idBlock", $block_id, "sequence", $where);
}

function delete_all_page_block($idArea) {

	checkPerm('del');
	manpage_checkPagePerm($idArea);

	$qtxt="SELECT idSubdivision FROM ".$GLOBALS["prefix_cms"]."_area_subdivision WHERE idArea='$idArea'";
	$q_sub=mysql_query($qtxt);

	if (($q_sub) && (mysql_num_rows($q_sub) > 0)) {
		while($row_sub=mysql_fetch_array($q_sub)) {

			$idSub=$row_sub["idSubdivision"];

			$qtxt="SELECT block_id FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE idSubdivision='$idSub'";
			$q_blk=mysql_query($qtxt);

			if (($q_blk) && (mysql_num_rows($q_blk) > 0)) {
				while($row_blk=mysql_fetch_array($q_blk)) {

					$block_id=$row_blk["block_id"];

					// remove roles:
					$acl_manager=& $GLOBALS["current_user"]->getAclManager();
					$acl_manager->deleteRoleFromPath("/cms/modules/block/".$block_id."/");

					mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE idBlock='$block_id';");
					mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_forum WHERE idBlock='$block_id';");
					mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_group WHERE idBlock='$block_id';");
					mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_block_items WHERE idBlock='$block_id';");
					mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_option WHERE idBlock='$block_id';");
					mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_option_text WHERE idBlock='$block_id'");

				}
			}

		}
	}

}

function change_layout() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');
	$form=new Form();

	$idArea=(int)$_POST["idArea"];

	manpage_checkPagePerm($idArea);

	if (isset($_POST["subdivision"]))
		$subdivision=$_POST["subdivision"];
	else
		$subdivision="";

	if (isset($_POST["conf_change"])) {
		$conf=1;
	}
	else if (isset($_POST["canc_change"])) {
		jumpTo("index.php?modname=manpage&amp;op=manpage");
	}
	else {
		$conf=0;
	}

	if ($idArea == 0) die("You can't access!");

	$home_url="index.php?modname=manpage&amp;op=manpage";
	$back_ui_url="index.php?modname=manpage&amp;op=manpage";
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_MANPAGE");
	$title_arr[]=$lang->def("_EDITPAGE").": ".getPageInfo("title", $idArea);
	$title_arr[]=$lang->def("_CHANGE_LAYOUT");
	$out->add(getTitleArea($title_arr, "manpage"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	if (($subdivision != "") && ($conf)) { // -------------| ok, Let's do it.. |-------------

		delete_all_page_block($idArea);
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_area_subdivision WHERE idArea='$idArea';");
		createSubdivision($subdivision, $idArea);

		$url ="index.php?modname=manpage&amp;op=manpage";
		$out->add($form->openForm("page_form", $url));
		$out->add($form->openElementSpace());

		$out->add($lang->def("_OPERATION_SUCCESSFUL"));

		$tree_action="_pagemodblock_".$idArea;
		$out->add($form->getHidden($tree_action, $tree_action, 1));

		$out->add($form->closeElementSpace());
		$out->add($form->openButtonSpace());
		$out->add($form->getButton('changelayout', 'changelayout', $lang->def("_PAGEMODBLOCK")));
		$out->add($form->closeButtonSpace());
		$out->add($form->closeForm());

	}
	else if (($subdivision != "") && (!$conf)) { // -------------| Ask "are you sure" ? |-------------

		$url ="index.php?modname=manpage&amp;op=changelayout";
		$out->add($form->openForm("page_form", $url));

		$out->add($form->getHidden("idArea", "idArea", $idArea));
		$out->add($form->getHidden("subdivision", "subdivision", $subdivision));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			$lang->def('_SURE_CHANGE_LAYOUT'),
			false,
			'conf_change',
			'canc_change'));

		$out->add($form->closeForm());

	}
	else { // -------------| Ask to select a new layout |-------------


		$url ="index.php?modname=manpage&amp;op=changelayout";
		$out->add($form->openForm("page_form", $url));
		$out->add($form->openElementSpace());
		$out->add($form->getHidden("idArea", "idArea", $idArea));

		$out->add('<div class="title">'.$lang->def("_SUBDIVISION").'</div>');
		$sub = getSubdivisionList();
		$i = 1;
		while( list(,$sub_name) = each($sub) ) {
			$out->add('<label for="lay'.$i.'">'
				.'&nbsp;<img src="'.getPathImage().'page_models/layout'.$sub_name.'.gif" alt="'.$sub_name.'" />'
				.'&nbsp;</label>'
				.'<input type="radio" id="lay'.$i.'" value="'.$sub_name.'" name="subdivision"'
				.( ($i == 1) ? ' checked="checked"' : '').' />&nbsp;'."\n");
			if( ($i++ % 4) == 0 ) $out->add('<br /><br />');
		}

		$out->add($form->closeElementSpace());
		$out->add($form->openButtonSpace());
		$out->add($form->getButton('changelayout', 'changelayout', $lang->def("_CONTINUE")));
		$out->add($form->closeButtonSpace());
		$out->add($form->closeForm());

	}

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>");
}

function page_group_list(& $form, & $lang, $idArea, $sel_default=FALSE) { // 2.1 ready

	require_once($GLOBALS['where_cms'].'/lib/lib.permsel.php');
	include_once($GLOBALS['where_framework']."/lib/lib.acl.php");

	$res="";

	$acl=new DoceboACL();
	$acl_manager=$acl->getACLManager();

	$perm_sel=new GroupPermSel();

	// Load the save permissions
	$token=array(
				'view' => array( 	'code' => 'view',
				'name' => '_VIEW',
				'image' => 'standard/view.gif')
			);

	$perm=array();
	foreach($token as $key=>$val) {
		$roleid="/cms/page/".$idArea."/".$val["code"];
		$rolest=$acl->getRoleST($roleid);


		if ($rolest === FALSE) {
			$grp_to_read=array();
		}
		else {
			// Wich groups are member of this role?
			$grp_to_read=$acl_manager->getRoleGMembers($rolest);
		}


		// Let's build the $perm array with the selected permissions
		foreach ($grp_to_read as $grp_key=>$grp_val) {

			$perm[$grp_val][$val["code"]]=1;
		}

		if ($sel_default) {
			$perm[$acl_manager->getAnonymousId()][$val["code"]]=1;
			$perm[$acl_manager->getGroupRegisteredId()][$val["code"]]=1;
		}

	}

	$res.=$perm_sel->getPermissionUi("page_form", $perm);
	$res.="<br />\n";

	return $res;
}

function save_page_groups($idArea) { // 2.1 ready

	require_once($GLOBALS['where_cms'].'/lib/lib.permsel.php');
	include_once($GLOBALS['where_framework']."/lib/lib.acl.php");

	$acl=new DoceboACL();
	$acl_manager=$acl->getACLManager();

	$perm_sel=new GroupPermSel();

	// Save the selected permissions
	if (isset($_POST["save"])) {
		$perm=$perm_sel->getSelectedPermission();

		$token=$perm_sel->getAllToken();
		$rolest_arr=array();
		$idst_arr=array();

		foreach($token as $key=>$val) {
			if (isset($val["code"])) {
				$roleid="/cms/page/".$idArea."/".$val["code"];
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
		setCmsReloadPerm();
	}
}

function manpage_checkPagePerm($area_id) {
	require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

	$ctp=new CmsTreePermissions("page");
	$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$area_id);
}

$act_op="";
if (isset($_POST["act_op"])) $act_op=$_POST["act_op"];
if (isset($_GET["act_op"])) $act_op=$_GET["act_op"];

switch ($act_op) {
	case "moveblkdown": {
		move_block("down", (int)$_GET["block_id"], (int)$_GET["sub_id"]);
	} break;
	case "moveblkup": {
		move_block("up", (int)$_GET["block_id"], (int)$_GET["sub_id"]);
	} break;
}


if ((isset($GLOBALS["op"]) && ($GLOBALS["op"] != "")))
	$op=$GLOBALS["op"];
else
	$op="manpage";

// XXX: switch
switch($op) {
	case "manpage" : {
		loadTreeState("manpage");
		manpage();
	};break;

	case "addpage" : {
		addpage();
	};break;
	case "inspage" : {
		inspage();
	};break;

	case "delpagenow": {
		pagedel(false);
	} break;

	case "pagemodblock" : {
		saveTreeState("manpage");
		pagemodblock( false );
	};break;

	case "addblock" : {
		addblock();
	};break;
	case "addselectedblock" : {
		addselectedblock();
	};break;
	case "blockcreated": {
		blockCreated();
	} break;

	case "modblock" : {
		modblock();
	} break;

	case "saveblock" : {
		if ((!isset($_POST["undo"])) && (isset($_POST["save"])))
			saveblock();
		else if ((!isset($_POST["undo"])) && (!isset($_POST["save"])))
			modblock();
		else
			pagemodblock(false);
	} break;

	case "delblock": {
		delblock();
	} break;


	case "changelayout": {
		change_layout();
	} break;
}
?>
