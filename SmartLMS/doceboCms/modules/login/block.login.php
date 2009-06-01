<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

function login_showMain($idBlock, $title, $block_op ) {
	//REQUIRES : true
	//EFFECTS  :display the login box
	if (isset($GLOBALS["loginIncorrect"]))
		$loginIncorrect=$GLOBALS["loginIncorrect"];
	else
		$loginIncorrect="";


	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('login', 'cms');

	$out->add('<div class="body_block">');

	if ($GLOBALS['current_user']->isLoggedIn()) {

		require_once($GLOBALS["where_cms"]."/modules/profile/class.cms_user_profile.php");

		$out->add('<div class="login_frame">'
			.'<div class="loginBox2">'."\n"
			.$lang->def("_WELCOME"));

		$res ="";
		$res.=' <b>';
		$url ="index.php?mn=profile&amp;pi=".getPI()."&amp;op=profile&amp;id_user=".getLogUserId();
		$res.='<a href="'.$url.'">';
		$res.=$_SESSION['public_area_username'].'</a></b><br /><br />'."\n";

		$res.='<ul class="login_buttons">';

		$level=$GLOBALS["current_user"]->getUserLevelId();
		$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
		$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);
		if (($is_admin) || ($is_god_admin)) {
			$title=def('_GOTO_ADMIN', 'standard');
			$res.='<li class="btn_goadmin"><a href="'.$GLOBALS["where_framework_relative"].'/">'.$title.'</a></li>';
		}

		$res.='<li class="btn_logout"><a href="index.php?action=logout">'.$lang->def("_LOG_LOGOUT").'</a></li>';
		$res.='</ul>'."\n";
		$out->add($res);

		$out->add('</div>'."\n"
			.'</div>');
	} else {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');

		getPageId();
		setPageId($GLOBALS["area_id"], $idBlock);

		$user_manager = new UserManager();
		$user_manager->setRegisterTo('link', 'index.php?mn=login&amp;pi='.getPI().'&amp;op=register');
		$user_manager->setLostPwdTo('link', 'index.php?mn=login&amp;pi='.getPI().'&amp;op=lostpwd');
		$extra = false;
		if(isset($GLOBALS['logout'])) {
			$extra = array( 'style' => 'logout_action', 'content' => $lang->def('_UNLOGGED') );
		}
		if(isset($GLOBALS['access_fail'])) {
			$extra = array( 'style' => 'noaccess', 'content' => $lang->def('_NOACCESS') );
		}
		$user_manager->setLoginStyle(FALSE);
		$user_manager->hideLoginLanguageSelection();
		$out->add(Form::openForm('login_confirm', 'index.php?action=login')
			.Form::getHidden("from_area", "from_area", getIdArea())
			.Form::getHidden("from_block", "from_block", $idBlock)
			.$user_manager->getLoginMask('index.php?action=login', $extra)
			.Form::closeForm());

	}
	$out->add("</div>\n"); // body_block
}

?>