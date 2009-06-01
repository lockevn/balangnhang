<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/



// restituisce l'url della pagina corrente;
// va passato il parametro exclude che e' un array con i nomi
// delle variabili (GET) da non includere nell'url.
function page_url($exclude) {

	if (!is_array($exclude)) $exclude=array($exclude);

	$res=$_SERVER["SCRIPT_NAME"];

	$i=0;
	foreach ($_GET as $key=>$val) {
		if ($i < 1) $sep="?";
		else $sep="&amp;";
		if (!in_array($key, $exclude)) $res.=$sep.$key."=".$val;
		$i++;
	}

	return $res;

}



function loadBlockOption($id) {
	//REQUIRES :$idBlock > 0 and valid
	//EFFECTS  :create an array with the option for the block passed

	// -- Normal ------------------------------------------------------
	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_option ";
	$qtxt.="WHERE idBlock = '".(int)$id."'";
	$q=mysql_query($qtxt);

	$opt=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {
			$opt[$row["name"]]=$row["value"];
		}
	}


	// -- Text --------------------------------------------------------
	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_option_text ";
	$qtxt.="WHERE idBlock = '".(int)$id."'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {
			$opt[$row["name"]]=$row["text"];
		}
	}

	return $opt;
}



function saveTreeState($prefix) {

	//echo("\n\nPOST:\n\n"); print_r($_POST);

	if ((isset($_POST["treeview_state_"])) && ($_POST["treeview_state_"] != "")
		&& (!isset($_SESSION[$prefix."treeview_state_"]))) {

		$_SESSION[$prefix."treeview_state_"]=$_POST["treeview_state_"];
		$_SESSION[$prefix."treeview_selected_"]=$_POST["treeview_selected_"];
		$_SESSION[$prefix."treeview_idplayitem_"]=$_POST["treeview_idplayitem_"];
		unset($_POST["treeview_state_"]);

	}

	//echo("<pre>\n\nSESSION:\n\n"); print_r($_SESSION);  echo("\n\n[SAVE]</pre>\n\n");

}



function loadTreeState($prefix) {

	//echo("<pre>\n\nSESSION:\n\n"); print_r($_SESSION);

	if ((isset($_SESSION[$prefix."treeview_state_"])) && ($_SESSION[$prefix."treeview_state_"] != "")
		&& (!isset($_POST["treeview_state_"]))) {

		$_POST["treeview_state_"]=$_SESSION[$prefix."treeview_state_"];
		$_POST["treeview_selected_"]=$_SESSION[$prefix."treeview_selected_"];
		$_POST["treeview_idplayitem_"]=$_SESSION[$prefix."treeview_idplayitem_"];
		unset($_SESSION[$prefix."treeview_state_"]);

	}

	//echo("\n\nPOST:\n\n"); print_r($_POST); echo("\n\n[LOAD]</pre>\n\n");

}

function resetTreeState($prefix) {
	if ((isset($_SESSION[$prefix."treeview_state_"])) && ($_SESSION[$prefix."treeview_state_"] != "")) {
		unset($_SESSION[$prefix."treeview_state_"]);
	}

	if (isset($_POST["treeview_state_"])) {
		unset($_POST["treeview_state_"]);
	}
}


function sendCmsGenericEvent($members=FALSE, $class, $module, $action, $log, $sub_string, $txt_string, $replace=FALSE) {

	if ($members === FALSE) {
		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$members=$acl_manager->getGroupAllUser($acl_manager->getGroupRegisteredId());
	}
	if ($replace === FALSE) {
		$replace=array('[url]' => $GLOBALS['cms']['url']);
	}
	require_once($GLOBALS['where_framework'] . '/lib/lib.eventmanager.php');
	$msg_composer = new EventMessageComposer($module, 'cms');
	$msg_composer->setSubjectLangText('email', $sub_string.'_EMAIL', false);
	$msg_composer->setBodyLangText('email', $txt_string.'_EMAIL', $replace);

	$msg_composer->setSubjectLangText('sms', $sub_string.'_SMS', false);
	$msg_composer->setBodyLangText('sms', $txt_string.'_SMS', $replace);

	createNewAlert($class,
							 $module,
							 $action,
							 '1',
							 $log,
							 $members,
							 $msg_composer );
}



function isCmsAdmin($req_god=TRUE) {

	if ($req_god)
		$req_level=ADMIN_GROUP_GODADMIN;
	else
		$req_level=ADMIN_GROUP_ADMIN;

	$userlevelid = $GLOBALS['current_user']->getUserLevelId();
	if( $userlevelid != $req_level ) {
		return false;
	}
	else {
		return true;
	}

}


function unsetBlockInfo() {

	if (isset($_SESSION["block_info"]))
		unset($_SESSION["block_info"]);

	if (isset($_SESSION["block_info_time"]))
		unset($_SESSION["block_info_time"]);

}


?>
