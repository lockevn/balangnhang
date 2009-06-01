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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

require_once($GLOBALS["where_cms"]."/modules/profile/class.cms_user_profile.php");


function profile() {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('profile', 'framework');

	$profile = new CmsUserProfile( getLogUserId() );
	$profile->init('profile', 'cms', 'mn=profile&pi='.getPI().'&op=profile&id_user='.getLogUserId(), 'ap');
	$profile->enableEditMode();

	$res =$profile->getTitleArea();
	$res.=$profile->getHead();
	$res.=$profile->performAction();
	$res.=$profile->getFooter();

	$out->add($res);
}


function profileDispatch($op) {

	if ($GLOBALS['current_user']->isAnonymous()) {
		$op="anonymous";
	}

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

//	$out->add("<div class=\"profile_form_back\">\n");
//	$out->add("<div class=\"profile_form_box\">\n");

	if(isset($_POST['undo'])) $op = 'profile';
	switch($op) {
		case "profile" : {
			profile();
		};break;
	}

//	$out->add("</div>\n"); // profile_form_box
//	$out->add("</div>\n"); // profile_form_back
}


?>