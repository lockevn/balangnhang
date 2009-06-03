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

	require_once($GLOBALS['where_lms'].'/lib/lib.middlearea.php');
	
	$ma = new Man_MiddleArea();

	addCss('style_menu');

	$GLOBALS['page']->add('<!--[if IE]> <link href="'.getPathTemplate('lms').'style/style_menu_ie.css" rel="stylesheet" type="text/css" /> <![endif]-->'."\n", 'page_head');
	$GLOBALS['page']->add('	<script type="text/javascript" src="'.$GLOBALS['where_framework_relative'].'/lib/lib.menu_over.js"></script>'."\n", 'page_head');
	
	$ml_lang 	= DoceboLanguage::createInstance('profile');
	$mo_lang 	= DoceboLanguage::createInstance('menu_over', 'lms');
	$ml_lang 	= DoceboLanguage::createInstance('menu_course');

	$user_level = $GLOBALS['current_user']->getUserLevelId();
	
	require_once($GLOBALS['where_framework'].'/lib/lib.message.php');
	$msg = new Man_Message();
	$unread_num = $msg->getCountUnreaded(getLogUserId(), array(), '', true);

	$query_menu = "
	SELECT mo.idModule, mo.module_name, mo.default_op, mo.default_name, mo.token_associated,
		under.idMain, under.my_name, mo.module_info
	FROM ".$GLOBALS['prefix_lms']."_module AS mo
		JOIN ".$GLOBALS['prefix_lms']."_menucourse_under AS under
	WHERE mo.idModule = under.idModule
		AND under.idCourse = '0'
	ORDER BY under.sequence ";
	$re_menu_voice = mysql_query($query_menu);

	$GLOBALS['page']->add(
		'<div class="over_menu_main">'
		.'<ul class="tray">', 'menu_over');
	
	printCartMenuItem();
	
	$user_opt = 0;
	$user_area = '';
	
	$public_admin_opt = 0;
	$public_admin_area = '';
	
	while(list($id_m, $module_name, $def_op, $default_name, $token, $id_main, $my_name, $m_info) = mysql_fetch_row($re_menu_voice)) {

		if($ma->currentCanAccessObj('mo_'.$id_m) && $module_name != 'course_autoregistration' && $module_name != 'public_forum') {

			if(strstr($m_info, 'type=user')) {
				
				$user_opt++;
				$user_area .= '<li>'
						.'<a class="om_'.$module_name.'" href="index.php?modname='.$module_name.'&amp;op='.$def_op.'&amp;id_module_sel='.$id_m.'&amp;id_main_sel='.$id_main.'&amp;sop=unregistercourse">'
						
						.( $module_name == 'profile' && (isset($GLOBALS['framework']['profile_only_pwd']) && $GLOBALS['framework']['profile_only_pwd'] == 'on')
							? $ml_lang->def('_CHANGEPASSWORD', 'profile')
							: ( $ml_lang->isDef($default_name) ? $ml_lang->def($default_name) : $default_name )
						).'</a>'
						.'</li>';
			}
			elseif($m_info != NULL)
			{
				if (strstr($m_info, 'type=public_admin') && checkPerm($token, true, $module_name,  true) )
				{
					$public_admin_opt++;
					$public_admin_area .= '<li>'
							.'<a href="index.php?modname='.$module_name.'&amp;op='.$def_op.'&amp;id_module_sel='.$id_m.'&amp;id_main_sel='.$id_main.'&amp;sop=unregistercourse">'
							.( $ml_lang->isDef($default_name) ? $ml_lang->def($default_name) : $default_name ).'</a>'
							.'</li>';
				}
			}
			else
			{

				$GLOBALS['page']->add(
						'<li><a class="om_first om_'.$module_name.'" href="index.php?modname='.$module_name.'&amp;op='.$def_op.'&amp;id_module_sel='.$id_m.'&amp;id_main_sel='.$id_main.'&amp;sop=unregistercourse">'
						.( $ml_lang->isDef($default_name) ? $ml_lang->def($default_name) : $default_name ).'</a>'
						.'</li>', 'menu_over');
			}
		}
	}
	//$GLOBALS['page']->add($user_area, 'menu_over');
	
	if($user_area != '') {
		
		if($user_opt <= 1) {
			
			$GLOBALS['page']->add('<li><a class="om_first '.substr($user_area, 14), 'menu_over');
		} else {
		
			$GLOBALS['page']->add(''
				.'<li class="'.( isset($_GET['open_menu']) ? 'menu_open' : 'menu_close' ).'"'
				.'	onmouseover="adminOpenMenu(this, \'menu_open\');" onmouseout="adminCloseMenu(this, \'menu_close\');" '
				//.'	onfocus="adminOpenMenu(this, \'menu_open\');" onblur="adminCloseMenu(this, \'menu_close\');"'
				.'>'
			
			.'<a class="om_first om_myarea" href="index.php?open_menu=1" onclick="return false;">'.$mo_lang->def('_MY_AREA').'</a>'
			.'<ul class="om_under_menu">'
			
			.$user_area
			.'</ul>'
			.'</li>'
			, 'menu_over');
		}
		
	}
	
	if($ma->currentCanAccessObj('course_autoregistration')) {
	
		$GLOBALS['page']->add(
			'<li><a class="om_first om_selfregistr" href="index.php?modname=course_autoregistration&amp;op=course_autoregistration&amp;sop=unregistercourse">'
			.$mo_lang->def('_COURSE_AUTOREGISTRATION')
			.'</a></li>', 'menu_over');
	}
	
	if($ma->currentCanAccessObj('public_forum')) {
	
		$GLOBALS['page']->add(
				'<li><a class="om_first om_forum" href="index.php?modname=public_forum&amp;op=forum&amp;id_public_forum=0&amp;sop=unregistercourse">'
				.$mo_lang->def('_PUBLIC_FORUM')
				.'</a></li>', 'menu_over');
	}
	
	if($ma->currentCanAccessObj('message')) {

		require_once($GLOBALS['where_framework'].'/lib/lib.message.php');
		$msg = new Man_Message();
		$unread_num = $msg->getCountUnreaded(getLogUserId(), array(), '', true);

		$GLOBALS['page']->add(
			'<li><a class="om_first om_message" href="index.php?modname=message&amp;op=message&amp;sop=unregistercourse">'
			.$mo_lang->def('_MY_MESSAGE').' ('.$unread_num.')'
			.'</a></li>', 'menu_over');
	}
	$user_level = $GLOBALS['current_user']->getUserLevelId();
	
	if($user_level == ADMIN_GROUP_PUBLICADMIN)
	{
		if($public_admin_area != '') {
			
			if($public_admin_opt <= 1) {
				
				$GLOBALS['page']->add('<li><a class="om_first"'.substr($public_admin_area, 6), 'menu_over');
			} else {
			
				$GLOBALS['page']->add(
				'<li class="'.( isset($_GET['open_menu']) ? 'menu_open' : 'menu_close' ).'"'
					.'	onmouseover="adminOpenMenu(this, \'menu_open\');" onmouseout="adminCloseMenu(this, \'menu_close\');" '
					//.'	onfocus="adminOpenMenu(this, \'menu_open\');" onblur="adminCloseMenu(this, \'menu_close\');"'
					.'>'
				
				.'<a class="om_first om_admin" href="index.php?open_menu=1" onclick="return false;">'.$mo_lang->def('_PUBLIC_ADMIN_AREA').'</a>'
				.'<ul class="om_under_menu">'.$public_admin_area.'</ul>'
				.'</li>', 'menu_over');
			}
		}
	}
	
	if($user_level == ADMIN_GROUP_GODADMIN || $user_level == ADMIN_GROUP_ADMIN ) {

		$GLOBALS['page']->add(
			'<li><a class="om_first om_admin" href="'.$GLOBALS['where_framework_relative'].'">'
				.$mo_lang->def('_GO_TO_FRAMEWORK')
			.'</a></li>', 'menu_over');
	}
	$GLOBALS['page']->add(
		'<li><a class="om_first om_logout" href="/doceboLms/index.php?modname=login&op=logout">'
			.$mo_lang->def('_LOGOUT')
		.' LMS</a></li>', 'menu_over');

	$GLOBALS['page']->add(
		'</ul>'
		.'</div>', 'menu_over');

}


function printCartMenuItem() {

	if(isset($_SESSION['idCourse']) && $_SESSION['idCourse'] != 0) return ;

	require_once($GLOBALS["where_ecom"]."/lib/lib.cart.php");
	$cart=& Cart::createInstance();
	$cart_item_count=$cart->getCartItemCount();

	if ($cart_item_count > 0) {
		
		addYahooJs(array(
			'animation' 		=> 'animation-min.js',
			'dragdrop' 			=> 'dragdrop-min.js"',
			'button' 			=> 'button-min.js"',
			'container' 		=> 'container-min.js"'
		), array(
			'container/assets/skins/sam' => 'container.css',
			'button/assets/skins/sam' => 'button.css'
		));
		addJs($GLOBALS['where_lms_relative'].'/modules/coursecatalogue/', 'ajax.coursecatalogue.js');
		
		$GLOBALS["page"]->add(
			'<li id="cart_link" class="'.( isset($_GET['open_menu']) ? 'menu_open' : 'menu_close' ).'">'
			.'<a class="om_first om_cart" href="index.php?modname=coursecatalogue&amp;op=go_cart" '
				.'onMouseOver="show_cart_preview(); return false;">'
			.def("_SHOPPING_CART", "coursecatalogue", "lms")." (".$cart_item_count.")</a>"
			.'</li>'
		, "menu_over");
	}

}

?>