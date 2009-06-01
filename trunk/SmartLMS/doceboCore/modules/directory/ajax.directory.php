<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package course management
 * @subpackage course catalogue
 * @category ajax server
 * @author Giovanni Derks
 * @version $Id:$
 *
 */

if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

require_once($GLOBALS['where_framework'].'/lib/lib.permission.php');

$op = get_req('op', DOTY_ALPHANUM, '');
switch($op) {
	case "getuserprofile" : {
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		
		require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');
		
		$id_user = importVar('id_user', true, 0);
		
		$profile = new UserProfile( $id_user );
		$profile->init('profile', 'lms', 'modname=directory&op=org_manageuser&id_user='.$id_user, 'ap');
		
		$user_level = $GLOBALS["current_user"]->getUserLevelId();
		
		if($user_level == ADMIN_GROUP_GODADMIN)
		{
			$profile->enableGodMode();
			$profile->disableModViewerPolicy();
		}
		else
		{
			if(checkPerm('edituser_org_chart', true, 'directory', 'framework'))
			{
				$profile->enableGodMode();
				$profile->disableModViewerPolicy();
			}
		}
		
		$value = array("content" 	=> $profile->getUserInfo()
		 		
			 	// teacher profile, if the user is a teacher
			 	.$profile->getUserTeacherProfile()
		 		
				.$profile->getUserLmsStat()  .'<br />'.$profile->getUserCompetencesList(),
				"id_user" => $id_user
		);
  
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
		
		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;
}

?>