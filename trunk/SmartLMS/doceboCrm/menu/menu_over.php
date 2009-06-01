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

if(!$GLOBALS['current_user']->isAnonymous()) {

	$lang = DoceboLanguage::createInstance('menu_over');

	$GLOBALS['page']->add('<li><a href="#main_menu">'.$lang->def('_BLIND_MAINMENU_OPTION').'</a></li>', 'blind_navigation');

	$GLOBALS['page']->add('<div id="main_menu" class="info_strip">', 'menu_over');

	$GLOBALS['page']->add(
		'<a class="logout_voice" href="index.php?modname=login&amp;op=logout" '
		.( $GLOBALS['framework']['use_accesskey'] == 'on' ?
			'accesskey="l">'.$lang->def('_LOGOUT').' <em class="shortcut">[L]</em>' :
			'>'.$lang->def('_LOGOUT') )
		.'</a>'

		.'<div class="no_float"></div>'
		.'</div>'."\n", 'menu_over');
}

?>