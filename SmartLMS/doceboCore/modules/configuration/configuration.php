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
 * @package Configuration
 * @author 	Pirovano Fabio (fabio@docebo.com)
 * @version $Id: configuration.php 473 2006-07-21 13:13:27Z giovanni $
 **/

define("SMS_GROUP", 3);

function config() {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.tab.php');

	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('configuration', 'framework');
	$form 		= new Form();

	$dot 		= '<img src="'.getPathImage().'config/dot.gif" alt="&gt;" />';
	$dot_sel 	= '<img src="'.getPathImage().'config/dot_sel.gif" alt="+" />';

	$group_sel 	= importVar('group_sel', false, -1);
	$isa 		= importVar('isa', false, -1);

	if(isset($_POST['tabelem_lms_status'])) 		$active_tab = 'lms';
	elseif(isset($_POST['tabelem_cms_status'])) 	$active_tab = 'cms';
	elseif(isset($_POST['tabelem_kms_status'])) 	$active_tab = 'kms';
	elseif(isset($_POST['tabelem_scs_status'])) 	$active_tab = 'scs';
	elseif(isset($_POST['tabelem_crm_status'])) 	$active_tab = 'crm';
	elseif(isset($_POST['tabelem_ecom_status'])) $active_tab = 'ecom';
	elseif(isset($_POST['tabelem_global_status'])) 	$active_tab = 'global';
	else $active_tab = importVar('tab', false, 'global');

	//instance class-------------------------------------------

	$title_area = array(
		$lang->def('_CONFIGURATION'),
		$lang->def('_CONF_'.strtoupper($active_tab)) );

	require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');

	$plat_man =& PlatformManager::createInstance();
	$conf =& $plat_man->getPlatofmConfigInstance(($active_tab == 'global' ? 'framework' : $active_tab ));

	if($active_tab == 'global') {
		require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
		$user_manager = new UserManager();
	}
	$groups = $conf->getRegroupUnit();
	if(isset($groups[$group_sel])) {
		$title_area[] = $groups[$group_sel];
	} elseif($group_sel == 'server') {
		$title_area[] = $lang->def('_RG_FW_SERVER');
	}

	//prefetching tab-------------------------------------------
	$tabs = new TabView('conf', 'index.php?modname=configuration&amp;op=config');

	$global_tab = new TabElemDefault('global', $lang->def('_CONF_GLOBAL'), getPathImage().'main_zone/framework.gif');
	$tabs->addTab($global_tab);
	$active_platform = $plat_man->getActivePlatformList(true);

	while(list($code) = each($active_platform)) {

		$new_tab = new TabElemDefault($code, $lang->def('_CONF_'.strtoupper($code)), getPathImage().'main_zone/'.$code.'.gif');
		$tabs->addTab($new_tab);
	}
	$tabs->setActiveTab($active_tab);

	//tab and selection
	$out->setWorkingZone('content');
	$out->add(
		getTitleArea($title_area, 'configuration')
		.'<div class="std_block">'
		.$tabs->printTabView_Begin());

	//save page if require
	if(isset($_POST['save_config'])) {
		$save_ok =FALSE;
		if($isa == 'um') {

			if($user_manager->saveElement($group_sel)) {
				$save_ok =TRUE;
				$out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));
			} else {
				$out->add(getErrorUi($lang->def('_ERROR_IN_SAVE')));
			}
		} else {

			if($conf->saveElement($group_sel)) {
				$save_ok =TRUE;
				$out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));
			} else {
				$out->add(getErrorUi($lang->def('_ERROR_IN_SAVE')));
			}
		}

		if ($save_ok && isset($_POST['option'])) {
			require_once($GLOBALS['where_framework'] . "/lib/lib.eventmanager.php");
			$field_saved =implode(" - ", array_keys($_POST['option']));
			$event =& DoceboEventManager::newEvent('SettingUpdate', 'configuration', 'edit', '1', 'Config for '.$group_sel.' updated');
			$event->setProperty('field_saved', $field_saved);
			DoceboEventManager::dispatch($event);
		}
	}

	$out->add('<ul class="configuration_regroup">');
	if($active_tab == 'global') {
		$out->add('<li'.( 'server' == $group_sel ? ' class="active"' : '' ).'>'
				.'<a href="index.php?modname=configuration&amp;op=config&amp;tab='.$active_tab.'&amp;group_sel=server">'
				.( 'server' == $group_sel ? $dot_sel : $dot ).'&nbsp;'.$lang->def('_RG_FW_SERVER').'</a></li>');

		$groups_um = $user_manager->getRegroupUnit();

		while(list($id, $name) = each($groups_um)) {

			$out->add('<li'.( $id == $group_sel ? ' class="active"' : '' ).'>'
					.'<a href="index.php?modname=configuration&amp;op=config&amp;tab='.$active_tab.'&amp;group_sel='.$id.'&amp;isa=um">'
					.( (string)$id == $group_sel ? $dot_sel : $dot ).'&nbsp;'.$name.'</a></li>');
		}
	}
	//find groups----------------------------------------------------
	while(list($id, $name) = each($groups)) {

		$out->add('<li'.( (string)$id == $group_sel ? ' class="active"' : '' ).'>'
				.'<a href="index.php?modname=configuration&amp;op=config&amp;tab='.$active_tab.'&amp;group_sel='.$id.'">'
				.( (string)$id == $group_sel ? $dot_sel : $dot ).'&nbsp;'.$name.'</a></li>');
	}
	$out->add('</ul>'
			.'<div class="config_page">');
	if($group_sel == 'server') {
		$out->add(
			server_info()
		);
	} elseif($isa == 'um') {

		$out->add(
			Form::openForm('conf_option', 'index.php?modname=configuration&amp;op=config')
			.Form::openElementSpace()

			.Form::getHidden('group_sel', 'group_sel', $group_sel)
			.Form::getHidden('isa', 'isa', 'um')
			.Form::getHidden('tab', 'tab', $active_tab)

			.$user_manager->getPageWithElement($group_sel)
			.Form::closeElementSpace()
			.Form::openButtonSpace()
			.Form::getButton('save_config', 'save_config', $lang->def('_SAVE'))
			.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
			.Form::closeButtonSpace()
			.Form::CloseForm()
		);
	} elseif($group_sel >= 0) {

		if (($group_sel == SMS_GROUP) && ($active_tab == "global")) {
			show_sms_panel($out, $lang);
		}

		$out->add(
			Form::openForm('conf_option', 'index.php?modname=configuration&amp;op=config')
			.Form::openElementSpace()

			.Form::getHidden('group_sel', 'group_sel', $group_sel)
			.Form::getHidden('tab', 'tab', $active_tab)

			.$conf->getPageWithElement($group_sel)
			.Form::closeElementSpace()
			.Form::openButtonSpace()
			.Form::getButton('save_config', 'save_config', $lang->def('_SAVE'))
			.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
			.Form::closeButtonSpace()
			.Form::CloseForm()
		);
	}
	$out->add('</div>'
		.$tabs->printTabView_End()
		.'</div>');
}

function config_line($param_name, $param_value) {

	return '<div class="no_float"><div class="label_effect">'
		.$param_name.'</div>'
		.$param_value
		.'</div>';
}

function server_info() {

	$lang =& DoceboLanguage::createInstance('configuration', 'framework');

	$php_conf = ini_get_all();

	$intest = '<div>'
			.'<div class="label_effect">';

	$html = '<div class="conf_line_title">'.$lang->def('_SERVERINFO').'</div>'
		.config_line($lang->def('_SERVER_ADDR'), $_SERVER['SERVER_ADDR'] )
		.config_line($lang->def('_SERVER_PORT'), $_SERVER['SERVER_PORT'] )
		.config_line($lang->def('_SERVER_NAME'), $_SERVER['SERVER_NAME'] )
		.config_line($lang->def('_SERVER_ADMIN'), $_SERVER['SERVER_ADMIN'] )
		.config_line($lang->def('_SERVER_SOFTWARE'), $_SERVER['SERVER_SOFTWARE'] )
		.'<br />'

		.'<div class="conf_line_title">'.$lang->def('_SERVER_MYSQL').'</div>'
		.config_line($lang->def('_MYSQL_VERS'), mysql_get_server_info())
		.'<br />'

		.'<div class="conf_line_title">'.$lang->def('_PHPINFO').'</div>'
		.config_line($lang->def('_PHPVERSION'), phpversion())
		.config_line($lang->def('_SAFEMODE'), ( $php_conf['safe_mode']['local_value']
			? $lang->def('_ON')
			: $lang->def('_OFF') ))
		.config_line($lang->def('_REGISTER_GLOBAL'), ( $php_conf['register_globals']['local_value']
			? $lang->def('_ON')
			: $lang->def('_OFF') ))
		.config_line($lang->def('_MAGIC_QUOTES_GPC'), ( $php_conf['magic_quotes_gpc']['local_value']
			? $lang->def('_ON')
			: $lang->def('_OFF') ))
		.config_line($lang->def('_UPLOAD_MAX_FILESIZE'), $php_conf['upload_max_filesize']['local_value'])
		.config_line($lang->def('_POST_MAX_SIZE'), $php_conf['post_max_size']['local_value'])
		.config_line($lang->def('_MAX_EXECUTION_TIME'), $php_conf['max_execution_time']['local_value'].'s' )
		.config_line($lang->def('_LDAP'), ( extension_loaded('ldap')
			? $lang->def('_ON')
			: '<span class="font_red">'.$lang->def('_OFF').' '.$lang->def('_USEFULL_ONLY_IF').'</span>') );

	if(version_compare(phpversion(), "5.0.0") == -1) {

		$html .= config_line($lang->def('_DOMXML'), ( extension_loaded('domxml')
				? $lang->def('_ON')
				: '<span class="font_red">'.$lang->def('_OFF').' ('.$lang->def('_NOTSCORM').')</span>' ));
	}
	if (version_compare(phpversion(), "5.2.0", ">"))
	{
		$html .= config_line($lang->def('_ALLOW_URL_INCLUDE'), ( $php_conf['allow_url_include']['local_value']
			? '<span class="font_red">'.$lang->def('_ON').'</span>'
			: $lang->def('_OFF') ));
	}
	if($GLOBALS['uploadType'] == 'ftp') {

		if(function_exists("ftp_connect")) {

			require_once( $GLOBALS['where_framework'].'/lib/lib.upload.php' );
			$re_con = sl_open_fileoperations();
			$html .= config_line($lang->def('_UPLOADFTP'), ( $re_con
				? $lang->def('_FTPOK')
				: '<span class="font_red">'.$lang->def('_FTPERR').'</span>') );
			if($re_con) sl_close_fileoperations();
		} else {

			$html .= config_line($lang->def('_UPLOADFTP'), '<span class="font_red">'.$lang->def('_FTPERR').'</span>' );
		}
	}
	$html .= '<div class="no_float"></div><br />';
	return $html;
}


function show_sms_panel(& $out, & $lang) {

	if ((int)$GLOBALS["sms_credit"] == 0) {
		$credit_left="0";
		$note="(".$lang->def("_SMS_CREDIT_UPDATE").")";
	}
	else {
		$credit_left=number_format($GLOBALS["sms_credit"]/1000, 2, ",", "")." &euro;";
		$note="";
	}

	$out->add("<div class=\"conf_sms_panel\">\n");

	$title=$lang->def("_SMSMARKET_LOGO");
	$url="http://www.smsmarket.it/";
	$out->add("<div style=\"float: right;\">");
	$out->add("<a href=\"".$url."\" onclick=\"window.open('".$url."'); return false;\">");
	$out->add("<img src=\"".getPathImage()."/config/smsmarket.gif\" alt=\"".$title."\" title=\"".$title."\" /></a>\n");
	$out->add("</div>\n");

	$out->add("<div><span class=\"text_bold\">");
	$out->add($lang->def("_SMS_CREDIT").": ".$credit_left."</span> ".$note."</div>\n");

	$url="http://www.smsmarket.it/acquista_sms.php";
	$out->add("<div><a href=\"".$url."\" onclick=\"window.open('".$url."'); return false;\">");
	$out->add($lang->def("_SMS_BUY_RECHARGE")."</a></div>\n");

	$out->add("<div class=\"no_float\"> </div></div>");

}



// XXX: switch
function configurationDispatch($op) {
switch($op) {
	case "config" : {
		config();
	};break;
}
}

?>