<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package  DoceboCore
 */

define("DASH_MAX_RSS_NEWS", 5);
define("_DOCEBO_CORP_BLOG_FEED_ID", 3);

function dashboard() {

	if(!checkPerm('view', true)) return;

	require_once($GLOBALS['where_framework'].'/lib/lib.rss.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');

	$lang =& DoceboLanguage::createInstance('dashboard', 'framework');

	$platform_man =& PlatformManager::createInstance();

	$plats = $platform_man->getActivePlatformList(true);
	require_once($GLOBALS['where_framework'].'/class/class.dashboard.php');

	$blocks = array();
	if(file_exists($GLOBALS['where_framework'].'/class/class.dashboard_framework.php')) {

		require_once($GLOBALS['where_framework'].'/class/class.dashboard_framework.php');
		$box_man = new Dashboard_Framework();

		$area_blocks = $box_man->getBoxContent();
		if(is_array($area_blocks)) {

			while(list(, $block_c) = each($area_blocks))
				$main_blocks[] = $block_c;
		}
	}
	while(list($code, $name) = each($plats)) {

		if(file_exists($GLOBALS['where_'.$code].'/class/class.dashboard_'.$code.'.php')) {

			require_once($GLOBALS['where_'.$code].'/class/class.dashboard_'.$code.'.php');
			$class_name = 'Dashboard_'.ucfirst($code);
			$box_man = eval("return new $class_name (); ");

			$area_blocks = $box_man->getBoxContent();
			if(is_array($area_blocks)) {

				while(list(, $block_c) = each($area_blocks))
					$blocks[] = $block_c;
			}
		}
	}
	// print out ----------------------------------------------------------------

	$GLOBALS['page']->add(
		getTitleArea($lang->def('_DASHBOARD'), 'dashboard')
		.'<div class="std_block">' );

	$links = '<h2 class="user_main_title">'.$lang->def('_SUPPORT_SITE').'</h2>'
			.'<ul id="support_site">'
			.'<li>danhut</li>'
			.'<li>lockevn</li>'
			.'</ul>';

	// block on the left ------------------------------------------------------------
	if(!empty($main_blocks)) {

		$GLOBALS['page']->add('<div class="dash_shadow">'
			.'<div class="dash_block'.( count($blocks) <= 1 ? '_small' : '_normal' ).'">'
			.implode('', $main_blocks)
			.( count($blocks) <= 1 ? '' : $links )
			.'</div>'
			.'</div>' );
	}
	// block on the right -----------------------------------------------------------

	$GLOBALS['page']->add('<div class="dash_shadow_right">'
		.'<div class="dash_block'.( count($blocks) <= 1 ? '_small' : '_normal' ).'">'
		.implode('', $blocks)
		.( count($blocks) > 1 ? '' : $links )
		.'</div>'
		.'</div>' );

		
	// print out rss feed -----------------------------------------------------------
	$GLOBALS['page']->add('<div class="no_float"></div>');

	if($GLOBALS['framework']['welcome_use_feed'] == 'on') {

		$GLOBALS['page']->add('<div class="action_line">'
			.'<a href="index.php?modname=dashboard&amp;op=deactivate">'.$lang->def('_DEACTIVATE_FEED').'</a>'
			.'</div>');

		$rss 		= new FeedReader();
		$rss_man 	= new FeedReaderManager();

		$append ='-'.( getLanguage() == 'italian' ? 'italian' : 'english');
		$rss->setAppendToUrl(_DOCEBO_CORP_BLOG_FEED_ID, $append);
		$feed_to_display = $rss_man->getFeedListByZone('dashboard');

		while(list($id_feed, $custom_title) = each($feed_to_display)) {

			$GLOBALS['page']->add('<div class="rss_shadow">'
				.'<div class="rss_block">' );

			$readed_rss = $rss->readFeed( $id_feed );

			// feed header --------------------------------------------------
			if(isset($readed_rss['description'])) $readed_rss['description'] = $rss->cleanEntry($readed_rss['description']);
			elseif(isset($readed_rss['title'])) $readed_rss['description'] = $rss->cleanEntry($readed_rss['title']);
			else $readed_rss['description'] = $lang->def('_UNTITLED_RSS');

			$GLOBALS['page']->add(
				'<h1>'
				.( isset($readed_rss['link'])
					? '<a class="rss_global_link" href="'.$readed_rss['link'].'"'
							.' onclick="window.open(this.href); return false;"'
							.' onkeypress="window.open(this.href); return false;">'
						.'<img src="'.getPathImage('fw').'standard/goto.gif" title="'.$lang->def('_GOTO_TITLE').'" alt="'.$lang->def('_GOTO').'" />'
						.'</a>'
					: '' )
				.( $lang->isDef($readed_rss['description']) ? $lang->def($readed_rss['description']) : $readed_rss['description'] )
				.'</h1>', 'content');

			// feed contents ------------------------------------------------
			for($i = 0; $i < $readed_rss['items_count'] && $i < DASH_MAX_RSS_NEWS; $i++) {

				$current_news =& $readed_rss['items'][$i];
				$GLOBALS['page']->add(
					'<h2 class="news_rss">'
					.'<a href="'.$current_news['link'].'"'
							.' onclick="window.open(this.href); return false;"'
							.' onkeypress="window.open(this.href); return false;">'
						.$rss->cleanEntry($current_news['title'])
					.'</a>'
					.'</h2>'
					.'<div class="'.( $i != ($readed_rss['items_count']-1) && $i != (DASH_MAX_RSS_NEWS-1) ? 'news_rss' : 'news_rss_noborder' ) .'">'
						.$rss->cleanEntry($current_news['description'])
					.'</div>'
				, 'content');
			}
			$GLOBALS['page']->add('</div></div>', 'content');
		}
	} else {
		$GLOBALS['page']->add('<div class="action_line">'
			.'<a href="index.php?modname=dashboard&amp;op=activate">'.$lang->def('_ACTIVATE_FEED').'</a>'
			.'</div>');
	}

	// close main block ------------------------------------------------

	$GLOBALS['page']->add('
			<div class="no_float"></div>
		</div>', 'content');
}

function dashboardDispatch($op) {

	$GLOBALS['page']->add('<link href="'.getPathTemplate('fw').'style/style_dboard.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
	switch($op) {
		case "deactivate" : {
			$query = "
			UPDATE ".$GLOBALS['prefix_fw']."_setting
			SET param_value = 'off'
			WHERE param_name = 'welcome_use_feed'";
			if(mysql_query($query)) $GLOBALS['framework']['welcome_use_feed'] = 'off';

			dashboard();
		};break;
		case "activate" : {
			$query = "
			UPDATE ".$GLOBALS['prefix_fw']."_setting
			SET param_value = 'on'
			WHERE param_name = 'welcome_use_feed'";
			if(mysql_query($query)) $GLOBALS['framework']['welcome_use_feed'] = 'on';

			dashboard();
		};break;
		default : {
			dashboard();
		};break;
	}
}

?>