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

/**
 * @package admin-core
 * @subpackage dashboard
 */

class Dashboard_Framework extends Dashboard {

	function Dashboard_Framework() {

	}

	function getBoxContent() {

		$html = array();
		if(!checkPerm('view_org_chart', true, 'directory', 'framework')) return $html;

		require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
		$user_dir = new Module_Directory();
		$user_stats = $user_dir->getUsersStats();

		$lang =& DoceboLanguage::createInstance('dashboard', 'framework');

		if($GLOBALS['framework']['welcome_use_feed'] == 'on') {

			require_once($GLOBALS['where_framework'].'/lib/lib.fsock_wrapper.php');
			$fp = new Fsock();
			$released_version = $fp->send_request('http://www.docebo.org/release.txt');
			
			if(!$fp) {

				$released_version = '<strong class="old_release">'.$lang->def('_UNKNOWN_RELEASE').'</strong>';
				$GLOBALS['framework']['welcome_use_feed'] = 'off';
			} else {			
				
				if($released_version == false) {

					$released_version = '<strong class="ok_release">'.$lang->def('_UNKNOWN_RELEASE').'</strong>';
					$GLOBALS['framework']['welcome_use_feed'] = 'off';
				}
				if($released_version == $GLOBALS['framework']['core_version']) {
					$released_version = '<strong class="ok_release">'.$released_version.'</strong>';
				} else {
					$released_version = '<strong class="old_release">'.$released_version.' ('.$lang->def('_NEW_RELEASE_AVAILABLE').')</strong>';
				}
			}
		}
		$html[] = '<h2 class="user_main_title">'.$lang->def('_USERS_PANEL').'</h2>'
			.'<p class="user_main">'
				.$lang->def('_TOTAL_USER').': <b>'.($user_stats['all'] - 1).'</b>;<br />'
				.$lang->def('_SUSPENDED_USER').': <b>'.$user_stats['suspended'].'</b>;<br />'
				.( checkPerm('approve_waiting_user', true, 'directory', 'framework')
					? $lang->def('_WAITING_USER').': <b>'.$user_stats['waiting'].'</b>;'
					: '' )
			.'</p><p>'
				.$lang->def('_SUPERADMIN_USER').': <b>'.$user_stats['superadmin'].'</b>;<br />'
				.$lang->def('_ADMIN_USER').': <b>'.$user_stats['admin'].'</b>;'
			.'</p><p>'
				.$lang->def('_REG_TODAY').': <b>'.$user_stats['register_today'].'</b>;<br />'
				.$lang->def('_REG_YESTERDAY').': <b>'.$user_stats['register_yesterday'].'</b>;<br />'
				.$lang->def('_REG_LASTSEVENDAYS').': <b>'.$user_stats['register_7d'].'</b>;'
			.'</p><p>'
				.$lang->def('_INACTIVE_USER').': <b>'.$user_stats['inactive_30d'].'</b>;<br />'
				.$lang->def('_ONLINE_USER').': <b>'.$user_stats['now_online'].'</b>;'
			.'</p><p>'
				.$lang->def('_CORE_VERSION').': <b>'.$GLOBALS['framework']['core_version'].'</b>;<br />'
				.( $GLOBALS['framework']['welcome_use_feed'] == 'on' ? $lang->def('_LAST_RELEASED').': '.$released_version.';' : '' )
			.'</p>';
		return $html;
	}

}

?>