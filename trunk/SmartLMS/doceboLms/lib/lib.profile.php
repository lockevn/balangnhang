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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function getprofile($id_user) {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

	$acl_man 	=& $GLOBALS['current_user']->getAClManager();
	$lang 		=& DoceboLanguage::createInstance('profile', 'framework');

	$user_info = $acl_man->getUser($id_user, false);

	$txt = '<div>';

	$txt .= '<div class="boxinfo_title">'.$lang->def('_USERPARAM').'</div>'
		.Form::getLineBox($lang->def('_USERNAME'), $acl_man->relativeId($user_info[ACL_INFO_USERID]) )
		.Form::getLineBox($lang->def('_LASTNAME'), $user_info[ACL_INFO_LASTNAME] )
		.Form::getLineBox($lang->def('_NAME'), $user_info[ACL_INFO_FIRSTNAME] )
		.Form::getLineBox($lang->def('_EMAIL'), $user_info[ACL_INFO_EMAIL] )
		.Form::getBreakRow()
		.'<div class="boxinfo_title">'.$lang->def('_USERFORUMPARAM').'</div>'
		.'<table class="profile_images">'
		.'<tr><td>';
	// NOTE: photo
	$path = $GLOBALS['lms']['url'].$GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
	if($user_info[ACL_INFO_PHOTO] != "") {

		$img_size = getimagesize($path.$user_info[ACL_INFO_PHOTO]);
		$txt .= '<img class="profile_image'
			.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' )
			.'" src="'.$path.$user_info[ACL_INFO_PHOTO].'" alt="'.$lang->def('_PHOTO').'" /><br />';
	} else {
		$txt .= '<div class="text_italic">'.$lang->def('_NOPHOTO').'</div>';
	}
	$txt .= '</td>'
		.'<td>';
	// NOTE: avatar
	if($user_info[ACL_INFO_AVATAR] != "") {

		$img_size = getimagesize($path.$user_info[ACL_INFO_AVATAR]);
		$txt .= '<img class="profile_image'
			.( $img_size[0] > 150 || $img_size[1] > 150 ? ' image_limit' : '' )
			.'" src="'.$path.$user_info[ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" /><br />';
	} else {

		$txt .= '<div class="text_italic">'.$lang->def('_NOAVATAR').'</div>';
	}
	// NOTE: signature
	$txt .= '</td></tr></table>'
		.'<div class="title">'.$lang->def('_SIGNATURE').'</div>'
		.'<div class="profile_signature">'.$user_info[ACL_INFO_SIGNATURE].'</div><br />'."\n";

	$txt .='</div>';
	return $txt;
}

?>