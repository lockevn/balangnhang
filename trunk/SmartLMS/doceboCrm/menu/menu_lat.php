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

	$lang = DoceboLanguage::createInstance('publicmenu');

	if (!isset($_SESSION['current_main_menu']))
		$_SESSION['current_main_menu']=1;

	//find information about the current main area
	if(!isset($GLOBALS['current_main_info'])) {
		$query_main = "
		SELECT name, image
		FROM ".$GLOBALS['prefix_crm']."_public_menu
		WHERE ".	"idMenu = '".$_SESSION['current_main_menu']."'";
		$q=mysql_query($query_main);
		if (($q) && (mysql_num_rows($q) > 0)) {
			list($menu_name, $GLOBALS['current_main_info']['image']) = mysql_fetch_row($q);
			$GLOBALS['current_main_info']['name'] = $lang->def($menu_name);
		}
		else {
			return "";
		}
	}

	$GLOBALS['page']->add('<li><a href="#menu_lat">'.$lang->def('_BLIND_MENU_LAT').'</a></li>', 'blind_navigation');

	$GLOBALS['page']->add(
		//menu intestation
		'<div id="menu_lat" class="menu_box">'."\n"
		.'<div class="menu_intest">'
		.( ($GLOBALS['current_main_info']['image'] != '') ?
			'<img src="'.getPathImage("crm").'menu/'.$GLOBALS['current_main_info']['image'].'" alt="'
				.$GLOBALS['current_main_info']['name'].'" />' :
			'' )
		.$GLOBALS['current_main_info']['name']
		.'</div>', 'menu');



	//find information about the element of the menu
	$query_menu = "
	SELECT t1.idUnder, t1.module_name, t1.default_op, t1.default_name, t1.associated_token
	FROM ".$GLOBALS['prefix_crm']."_publicmenu_under AS t1
	WHERE ".( isset($_SESSION['current_main_menu']) ? " t1.idMenu = '".$_SESSION['current_main_menu']."' " : '1' )."
	ORDER BY t1.sequence";
	$re_menu_voice = mysql_query($query_menu);

	$GLOBALS['page']->add('<ul class="menu_box_list">', 'menu');
	while(list($id_module, $module_name, $default_op, $default_name, $token) = mysql_fetch_row($re_menu_voice)) {

		$GLOBALS['module_assigned_name'][$module_name] = $lang->def($default_name);
		if(checkModPerm($token, $module_name, true)) {

			if(isset($_SESSION['sel_module_id']) && $_SESSION['sel_module_id'] == $id_module) {

				$GLOBALS['page']->add( '<li>'
					.'<strong class="voice_selected">'.$GLOBALS['module_assigned_name'][$module_name] .'</strong>', 'menu');

				if(isset($module_cfg)) {
					$extra_voice = $module_cfg->getVoiceMenu();
					if(is_array($extra_voice) && !empty($extra_voice)) {

						$GLOBALS['page']->add('<div class="menu_box_under">'.implode('', $extra_voice).'</div>', 'menu');
					}
				}
				$GLOBALS['page']->add('</li>', 'menu');
			} else {

				$GLOBALS['page']->add('<li>'
					.'<a class="voice" href="index.php?modname='.$module_name.'&amp;op='.$default_op.'">'
					.$GLOBALS['module_assigned_name'][$module_name]
					.'</a>'
					.'</li>', 'menu');
			}
		}
	}


	$GLOBALS['page']->add( '</ul>'."\n"
		.'</div>'
		, 'menu');
}


?>