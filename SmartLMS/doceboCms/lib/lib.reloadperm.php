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


function setCmsReloadPerm($time=FALSE) {

	if (($time === FALSE) || ($time == 0))
		$time=time();

	$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_setting SET param_value='".$time."' ";
	$qtxt.="WHERE param_name='reload_perm_after'";
	$q=mysql_query($qtxt);
}


function checkCmsReloadPerm() {

	// First time:
	if (!isset($_SESSION["perm_last_reload"])) {
		$_SESSION["perm_last_reload"] =0;
	}

	if ($GLOBALS["cms"]["reload_perm_after"] > $_SESSION["perm_last_reload"])
		return true;
	else
		return false;
}


function reloadCmsPerm() {
	require_once($GLOBALS["where_cms"]."/lib/lib.cms_common.php");

	unsetBlockInfo();
	$GLOBALS["current_user"]->loadUserSectionST("/cms/");
	$_SESSION["perm_last_reload"]=$GLOBALS["cms"]["reload_perm_after"];

	$GLOBALS['current_user']->SaveInSession();

}


?>
