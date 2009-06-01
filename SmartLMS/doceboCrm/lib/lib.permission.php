<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/


function checkModPerm($mode, $mod_name, $return_value = false) {

	$role="/crm/module/".$mod_name."/".$mode;

	$res=$GLOBALS['current_user']->matchUserRole($role);

	if ($res) {
		return TRUE;
	}
	else if ((!$res) && ($return_value))
		return FALSE;
	else if ((!$res) && (!$return_value))
		die("You can't access");

}


function isCrmUser($user_idst=FALSE) {
	require_once($GLOBALS["where_crm"]."/admin/modules/crmuser/lib.crmuser.php");
	$crmum =new CrmUserManager();
	return $crmum->isCrmUser($user_idst);
}


function isCrmTaskUser($user_idst=FALSE) {
	require_once($GLOBALS["where_crm"]."/admin/modules/crmtaskuser/lib.crmtaskuser.php");
	$ctum =new CrmTaskUserManager();
	return $ctum->isCrmTaskUser($user_idst);
}


function isCrmMarketingUser($user_idst=FALSE) {
	require_once($GLOBALS["where_crm"]."/admin/modules/crmmarketinguser/lib.crmmarketinguser.php");
	$cmum =new CrmMarketingUserManager();
	return $cmum->isCrmMarketingUser($user_idst);
}


?>
