<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if($GLOBALS['current_user']->isLoggedIn()) {

	$GLOBALS['page']->add(
	'<script type="text/javascript" src="'.$GLOBALS['where_framework_relative'].'/lib/lib.menu_over.js"></script>'
	.'<!--[if lt IE 7.]>
		<script type="text/javascript" src="'.$GLOBALS['where_framework_relative'].'/lib/lib.pngfix.js"></script>
	<![endif]-->', 'page_head');

	if(!isset($_SESSION['current_action_platform'])) {
		$_SESSION['current_action_platform'] = 'framework';
	}

	$lang 	=& DoceboLanguage::createInstance('menu_over', 'framework');
	$p_man 	=& PlatformManager::createInstance();

	$platforms 	= $p_man->getPlatformList();

	$b_info = getBrowserInfo();
	$img_ext = '.png';

	$GLOBALS['page']->add('<li><a href="#main_menu">'.$lang->def('_BLIND_MAIN_MENU').'</a></li>', 'blind_navigation');

	$GLOBALS['page']->add('<script type="text/javascript">'
		."
		switchToDrowpdown('opened_menu', 'opened_menu_2', 'menu_close');
		"
		.'</script>', 'page_head');


	$GLOBALS['page']->setWorkingZone('menu_over');
	$GLOBALS['page']->add('<div><ul id="main_menu" class="main_menu_over"><!-- Main menu -->');

	foreach($platforms as $p_code => $p_name) {

		$menu_man =& $p_man->getPlatofmMenuInstance($p_code);

		if(isset($_SESSION['menu_over']['p_sel'])) {

			$pl_is_sel = ($p_code == $_SESSION['menu_over']['p_sel']);
		} else $pl_is_sel = false;


		if($menu_man !== false) {

			$main_voice = $menu_man->getLevelOne();

			if(!empty($main_voice)) {

				$GLOBALS['page']->add(
					'<li class="'.( $pl_is_sel ? 'menu_open_nojs' : 'menu_close' ).'" '.( $pl_is_sel ? 'id="opened_menu"' : '' ).'' .
					'	onmouseover="adminOpenMenu(this, \'menu_open\');" onmouseout="adminCloseMenu(this, \'menu_close\');" '.
					//'	onfocus="adminOpenMenu(this, \'menu_open\');" onblur="adminCloseMenu(this, \'menu_close\');"'.
					'>'

						.'<a class="first_line" href="index.php?op=platform_sel&amp;pl_sel='.$p_code.'" onclick="return false;">'
						.'<img class="platform_icon" src="'.getPathImage('fw').'platform/'.$p_code.$img_ext.'" alt=".:" /><br />'
						.$lang->def('_FIRST_LINE_'.$p_code).'</a>'
						.'<ul class="list_modules">');

				foreach($main_voice as $id_m => $v_main) {

						$url ='index.php?op=over_main_sel&amp;id_sel='.$id_m;

						if ((isset($v_main['open_popup'])) && ($v_main['open_popup'])) {
							$extra ='onclick="window.open(this.href); return false;"';
							$url =$v_main["link"];
						}
						else {
							$extra ='onclick="return false;"';
						}

					if(isset($_SESSION['menu_over']['main_sel']) && isset($_SESSION['menu_over']['p_sel'])) {

						$main_is_sel = ($id_m == $_SESSION['menu_over']['main_sel'] && $p_code == $_SESSION['menu_over']['p_sel']);
					} else $main_is_sel = false;

					if(!isset($v_main['collapse']) || $v_main['collapse'] === false) {

						if(trim($v_main['image']) == 'area_title/') $img = getPathImage('fw').'standard/blank.gif';
						else $img = getPathImage($p_code).$v_main['image'].$img_ext;

						$GLOBALS['page']->add(
							'<li class="'.( $main_is_sel ? 'menu_open_nojs' : 'menu_close' ).'" '.( $pl_is_sel ? 'id="opened_menu_2"' : '' ).' '.
												'	onmouseover="adminOpenMenu(this, \'menu_open\');" onmouseout="adminCloseMenu(this, \'menu_close\');"'.
												//'	onfocus="adminOpenMenu(this, \'menu_open\');" onblur="adminCloseMenu(this, \'menu_close\');"'.
												'>'

								.'<a class="arrow_left" href="'.$url.'" '.$extra.'>'
								.'<img class="menu_icon" src="'.$img.'" alt=".:" />'
								.$v_main['name'].'</a>'
								.'<ul>');
					}
					$under_voice = $menu_man->getLevelTwo($id_m);
					foreach($under_voice as $id_m => $voice) {

						$url =str_replace('&', '&amp;',  $voice['link']);

						if ((!isset($voice['dont_close_over'])) || (!$voice['dont_close_over'])) {
							$url.='&amp;close_over=1';
						}

						if ((isset($voice['open_popup'])) && ($voice['open_popup'])) {
							$extra ='onclick="window.open(this.href); return false;"';
						}
						else {
							$extra ="";
						}

						if(isset($voice['modname']) && isset($voice['op'])) {

							$GLOBALS['page']->add(
								'<li><a '.$extra.' href="'.$url.'">'
								.'<img class="menu_icon" src="'.getPathImage($voice['of_platform']).'area_title/'.$voice['modname'].'_'.$voice['op'].$img_ext.'" alt=".:" />'
								.$voice['name'].'</a></li>');
						} else {

							$GLOBALS['page']->add(
								'<li><a '.$extra.' href="'.$url.'">'
								.'<img class="menu_icon" src="'.getPathImage($voice['of_platform']).'standard/blank.gif" alt=".:" />'
								.$voice['name'].'</a></li>');
						}
					}
					if(!isset($v_main['collapse']) || $v_main['collapse'] === false) {

						$GLOBALS['page']->add('</ul>'
							.'</li>');
					}
				}
				$GLOBALS['page']->add('</ul>'
					.'</li>');
			}
		}
	}

	$GLOBALS['page']->add('
		<!-- Jump to -->
		<li class="'.( $GLOBALS['op'] == 'jumpto_sel' ? 'menu_open_nojs' : 'menu_close' ).'" '.( $pl_is_sel ? 'id="opened_jumpto"' : '' ).' '.
				' 	onmouseover="adminOpenMenu(this, \'menu_open\');" onmouseout="adminCloseMenu(this, \'menu_close\');"'.
				//'	onfocus="adminOpenMenu(this, \'menu_open\');" onblur="adminCloseMenu(this, \'menu_close\');"'.
				'>
			<a class="little_link" href="index.php?op=jumpto_sel&amp;close_over=1"><img class="little_icon" src="'.getPathImage('fw').'platform/jump_to.png" alt=".:" 	/>
			<br />'.def('_JUMP_TO_PLATFORM', 'menu_over', 'framework').'</a>
			<ul class="list_modules">
		', 'menu_over');

	$platforms 	= $p_man->getPlatformList(TRUE);
	unset($platforms['scs']);
	unset($platforms['ecom']);
	foreach($platforms as $p_code => $p_name) {

		$GLOBALS['page']->add(
		'<li>'
			.'<a  href="'.$GLOBALS['where_'.$p_code.'_relative'].'">'
			.'	<img class="menu_icon" src="'.getPathImage('fw').'platform/'.$p_code.'_little'.$img_ext.'" alt=".:" />'
			.$p_name.'</a>'
		.'</li>', 'menu_over');
	}
	$GLOBALS['page']->add('</ul>'
		.'</li>');
	$GLOBALS['page']->add(
		'<li>'
			.'<a class="little_link" href="/doceboLms/index.php?modname=login&op=logout">'
			.'<img class="little_icon" src="'.getPathImage('fw').'platform/logout'.$img_ext.'" alt=".:" rel="logout Core" /><br />'
			.def('_LOGOUT', 'menu_over', 'framework').'</a>'
		.'</li>');
	$GLOBALS['page']->add('</ul><div class="no_float"></div></div>');
}

?>