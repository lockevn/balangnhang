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

/**
 * @package admin-library
 * @subpackage interaction
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.template.php 995 2007-03-09 14:15:07Z fabio $
 */

function parseTemplateDomain($curr_domain = false) {
	
	$association = array();
	if(!isset($GLOBALS['template_domain'])) return false;
	$domains = $GLOBALS['template_domain'];
	$domains = str_replace("\r", "\n", $domains);
	$domains = str_replace("\n\n", "\n", $domains);
	$rows = explode("\n", $domains);
	foreach($rows as $pair) {
		
		list($domain, $template) = explode(',', $pair);
		$association[$domain] = $template;
	}
	if($curr_domain !== false) {
		
		if(isset($association[$curr_domain])) return $association[$curr_domain];
		return false;
	}
	return $association;
}

/**
 * @return string 	the default template saved in database
 */
function getDefaultTemplate( $platform = false ) {

	require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');

	if($platform === false) {
		if(defined("IN_CORE") && isset($_SESSION['current_action_platform'])) $platform = $_SESSION['current_action_platform'];
		else $platform = $GLOBALS['platform'];
	}

	$plat_templ = parseTemplateDomain($_SERVER['HTTP_HOST']);
	if($plat_templ == false) {
	
		$plat_man =& PlatformManager::createInstance();
		$plat_templ = $plat_man->getTemplateForPlatform($platform);
	}
	
	if(is_dir($GLOBALS['where_lms'].'/templates/'.$plat_templ)) return $plat_templ;
	else return array_pop(getTemplateList());
}

/**
 * this function change the template used only in the session
 *
 * @param string 	a valid template name
 */
function setTemplate($new_template) {

	if(($new_template != '') && is_dir('templates/'.$new_template.'/')) {
		$GLOBALS['defaultTemplate'] = $new_template;
		$_SESSION[$GLOBALS['platform'].'_sesTemplate'] = $new_template;
	}
}

function resetTemplate() {
	unset($_SESSION[$GLOBALS['platform'].'_sesTemplate'] );
	setTemplate(getTemplate());
}

/**
 * @return string the actual template name
 */
function getTemplate() {

	if(isset($GLOBALS[$GLOBALS['platform']]['ignore_template_user_pref']) &&
			$GLOBALS[$GLOBALS['platform']]['ignore_template_user_pref'] == 'on') {

		if(isset($_SESSION[$GLOBALS['platform'].'_sesTemplate'])) return $_SESSION[$GLOBALS['platform'].'_sesTemplate'];
		return getDefaultTemplate();
	} else {

		require_once($GLOBALS['where_framework'].'/setting.php');
		if($GLOBALS['framework']['templ_use_field'] != 0
			&& !$GLOBALS['current_user']->isAnonymous()) {

			if(isset($_SESSION[$GLOBALS['platform'].'_sesTemplate'])) return $_SESSION[$GLOBALS['platform'].'_sesTemplate'];

			if($GLOBALS['current_user']->isAnonymous()) {

				$_SESSION[$GLOBALS['platform'].'_sesTemplate'] = getDefaultTemplate();
				return getDefaultTemplate();
			}

			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

			$field_man = new FieldList();
			$field_man->setFieldEntryTable($GLOBALS['prefix_fw'].'_field_userentry');
			$value_field = $field_man->getUserFieldEntryData(
				getLogUserId(),
				array($GLOBALS['framework']['templ_use_field'])
			);

			$query_template_assigned = "
			SELECT template_code
			FROM ".$GLOBALS['prefix_fw']."_field_template
			WHERE id_common = '".$GLOBALS['framework']['templ_use_field']."'
				AND ref_id = '"
					.( isset($value_field[$GLOBALS['framework']['templ_use_field']])
						? $value_field[$GLOBALS['framework']['templ_use_field']]
						: 0 )."' ";
			$re = mysql_query($query_template_assigned);
			list($template_code) = mysql_fetch_row($re);
			if($template_code == '' || !is_dir($GLOBALS['where_framework'].'/templates/'.$template_code.'/')) {

				$_SESSION[$GLOBALS['platform'].'_sesTemplate'] = getDefaultTemplate();
				return getDefaultTemplate();
			}

			$_SESSION[$GLOBALS['platform'].'_sesTemplate'] = $template_code;
			return $template_code;

		} else {

			$templ_pref = $GLOBALS['current_user']->preference->getTemplate();
			if($templ_pref == false) {
				if(isset($_SESSION[$GLOBALS['platform'].'_sesTemplate'])) {
					return $_SESSION[$GLOBALS['platform'].'_sesTemplate'];
				}
				return getDefaultTemplate();
			}
			if(is_dir('templates/'.$templ_pref)) return $templ_pref;
			else return array_pop(getTemplateList());
		}
	}
}

/**
 * @return string the absolute path of templates folder root
 */
function getAbsoluteBasePathTemplate($platform = false) {

	if($platform === false) {
		if(defined("IN_CORE") && isset($_SESSION['current_action_platform'])) $platform = $_SESSION['current_action_platform'];
		else $platform = $GLOBALS['platform'];
	}
	if($platform == 'fw') $platform = 'framework';
	if(!isset($GLOBALS['where_'.$platform])) $platform = 'framework';
	return $GLOBALS['where_'.$platform]
				.( substr($GLOBALS['where_'.$platform], -1) == '/' ? '' : '/').'templates/';
}

/**
 * @return string the absolute path of templates folder
 */

function getAbsolutePathTemplate($platform = false) {

	return getAbsoluteBasePathTemplate($platform).getTemplate().'/';
}
/**
 * @return string the relative url of templates folder root
 */
function getRelativeBasePathTemplate($platform = false) {


	if($platform === false) {
		if(defined("IN_CORE") && isset($_SESSION['current_action_platform'])) $platform = $_SESSION['current_action_platform'];
		else $platform = $GLOBALS['platform'];
	}
	if($platform == 'fw') $platform = 'framework';
	if(!isset($GLOBALS['where_'.$platform.'_relative'])) $platform = 'framework';
	return $GLOBALS['where_'.$platform.'_relative']
				.( substr($GLOBALS['where_'.$platform.'_relative'], -1) == '/' ? '' : '/').'templates/';
}

/**
 * @return string the relative url of templates folder
 */

function getPathTemplate($platform = false) {

	return getRelativeBasePathTemplate($platform).getTemplate().'/';
}

/**
 * @return array an array with the existent templates
 */
function getTemplateList($set_keys = FALSE, $platform = FALSE) {

	if($platform === FALSE) $path = $GLOBALS['where_framework'].'/templates/';
	else $path = getRelativeBasePathTemplate($platform);
	$templ = dir($path);
	while($elem = $templ->read()) {

		if((is_dir($path.$elem)) && ($elem != ".") && ($elem != "..") && ($elem != ".svn") && $elem{0} != '_' ) {

			if (!$set_keys) $templArray[] = $elem;
			else $templArray[$elem] = $elem;
		}
	}
	closedir($templ->handle);

	if (!$set_keys) sort($templArray);
	else ksort($templArray);

	reset($templArray);
	return $templArray;
}

/**
 * @return string 	the relative address of the images directory
 */
function getPathImage($platform = false) {

	return getPathTemplate($platform).'images/';
}

/**
 * @param string	$text		The title of the area
 * @param string	$image		the name of the gif in tampltes/xxx/images/area_title/
 * @param string	$alt_image	The alt for the image [deprecated, not used]
 * @param bool		$ignore_glob	ignore global value of the title
 *
 * @return string 	the code for a graceful title area
 */
function getTitleArea($text, $image = '', $alt_image = '', $ignore_glob = false) {

	$is_first = true;
	if(!is_array($text))
		$text = array($text);

	$html = '<div class="area_block">'."\n";
	if($image != '') {

		$GLOBALS['page']->add(
			'<link href="'.getPathTemplate().'style/style_heading.php?image='.$image.'" rel="stylesheet" type="text/css" />'."\n"
		, 'page_head');
	}
	foreach($text as $link => $title) {

		if($is_first) {

			$is_first = false;
			// Retrive, if exists, name customized by the user for the module
			if(!$ignore_glob && isset($GLOBALS['module_assigned_name'][$GLOBALS['modname']]) && $GLOBALS['module_assigned_name'][$GLOBALS['modname']] != '') {
				$title = $GLOBALS['module_assigned_name'][$GLOBALS['modname']];
			}
			// Area title
			$html .= '<h1 id="main_area_title" class="main_title_'.$image.'">'.$title.'</h1>'."\n";

			$GLOBALS['page']->add('<li><a href="#main_area_title">'.def('_JUMP_TO', 'standard').' '.$title.'</a></li>', 'blind_navigation');

			if($title) $GLOBALS['page']->replace('<title>', '<title>'.$GLOBALS['title_page'].' &rsaquo; '.$title.'</title>', 'page_head');

			// Init navigation
			if(count($text) > 1) {
				$html .= '<ul class="navigation">';
				if(!is_int($link)) {
					$html .= '<li><a href="'.$link.'">'.def('_START_PAGE', 'standard').' '.strtolower($title).'</a></li>';
				} else $html .= '<li>'.def('_START_PAGE', 'standard').' '.strtolower($title).'</li>';
			}
		} else {

			if(is_int($link)) $html .= '<li> &gt; '.$title.'</li>';
			else $html .= ' <li> &gt; <a href="'.$link.'">'.$title.'</a></li>';
		}
	}
	if(count($text) > 1) $html .= '</ul>'."\n";
	$html .= '</div>'."\n";
	return $html;
}

/**
 * @param string	$message	the error message
 * @param bool		$with_image	add the standard error image or not
 *
 * @return string 	the code for a graceful error user interface
 */
function getErrorUi($message, $with_image = true) {

	return '<p class="error_container">'
		.'<strong>'.$message.'</strong>'
		.'</p>';
}

/**
 * @param string $name the name of the result
 *
 * @return string 	the code for a graceful result confirmer
 **/
function getResultUi( $name ) {
	return "\n".'<p class="result_container">'."\n\t"
		.'<strong>'.$name.'</strong>'."\n"
		.'</p>'."\n";
}

/**
 * @param string $message the information message
 *
 * @return string 	the code for a graceful information user interface
 */
function getInfoUi($message) {

	return '<p class="information_container">'
		.'<strong>'.$message.'</strong>'
		.'</p>';
}

/**
 * @param 	string 	$link 	the link related with the back operation
 * @param 	string 	$name 	the name of the link
 * @param 	string 	$type 	the type of back ('link','button','submit')
 * 							if is selected button as type the link will be ignored
 *
 * @return  string 	the code for a graceful back purpose
 **/
function getBackUi( $link, $name, $type = 'link' ) {

	switch($type) {
		case "button" : {
			return '<div class="back_container_button">'
				.'<input class="button" type="button" value="'.$name.'" /></div>';
		};break;
		case "submit" : {
			return '<div class="back_container_button">'
				.'<input class="button" type="submit" value="'.$name.'" /></div>';
		};break;
		default : {
			return '<div class="back_container">'."\n\t".'<a href="'.$link.'" '
					.( $GLOBALS['framework']['use_accesskey'] == 'on' ? 'accesskey="b">'.$name.' (b)' : '>'.$name ).'</a>'."\n"
					.'</div>'."\n";
		}
	}
}

/**
 * @param 	string	$are_you_sure 		the text to display in the title
 * @param 	string	$central_text 		the text in the central part
 * @param 	string	$command_is_link 	if the undo and confirm command is link or button,
 										if is true, the other
 * @param 	string	$confirm_ref 		if $command_is_link is true, this is the confirm link, else the button name and id
 *										if the name contains "[" "]" they change it in this way "[" => "_", "]" => ""
 * @param 	string	$undo_ref 			if $command_is_link is true, this is the undo link, else the button name and id
 *										if the name contains "[" "]" they change it in this way "[" => "_", "]" => ""
 * @param 	string	$confirm_text 		the text of the confirm action (optional)
 * @param 	string	$undo_text 			the text of the undo action (optional
 *
 * @return string the html code for the requested interface
 */
function getDeleteUi($are_you_sure, $central_text, $command_is_link,
			$confirm_ref, $undo_ref, $confirm_text = false, $undo_text = false) {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$txt = '<div class="boxinfo_title">'.$are_you_sure.'</div>'
		.'<div class="boxinfo_container">'
		.$central_text
		.'</div>'
		.'<div class="del_container">';
	if($command_is_link) {

		$txt .= '<a href="'.$confirm_ref.'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.( $confirm_text == false ? def('_CONFIRM') : $confirm_text ).'" />'
				.'&nbsp;'.( $confirm_text == false ? def('_CONFIRM') : $confirm_text ).'</a>&nbsp;&nbsp;'
				.'<a href="'.$undo_ref.'">'
				.'<img src="'.getPathImage().'standard/undo.gif" alt="'.( $undo_text == false ? def('_UNDO') : $undo_text ).'" />'
				.'&nbsp;'.( $undo_text == false ? def('_UNDO') : $undo_text ).' </a>';
	} else {

		$confirm_ref_id = str_replace(']', '', str_replace('[', '_', $confirm_ref));
		$undo_ref_id	= str_replace(']', '', str_replace('[', '_', $undo_ref));
		$txt .= Form::getButton($confirm_ref_id, $confirm_ref, def('_CONFIRM'), 'transparent_del_button')
			.'&nbsp;'
			.Form::getButton($undo_ref_id, $undo_ref, def('_UNDO'), 'transparent_undo_button');
	}
	$txt .= '</div>';
	return $txt;
}

/**
 * @param 	string	$are_you_sure 		the text to display in the title
 * @param 	string	$central_text 		the text in the central part
 * @param 	string	$command_is_link 	if the undo and confirm command is link or button,
 										if is true, the other
 * @param 	string	$confirm_ref 		if $command_is_link is true, this is the confirm link, else the button name and id
 *										if the name contains "[" "]" they change it in this way "[" => "_", "]" => ""
 * @param 	string	$undo_ref 			if $command_is_link is true, this is the undo link, else the button name and id
 *										if the name contains "[" "]" they change it in this way "[" => "_", "]" => ""
 * @param 	string	$confirm_text 		the text of the confirm action (optional)
 * @param 	string	$undo_text 			the text of the undo action (optional
 *
 * @return string the html code for the requested interface
 */
function getModifyUi($are_you_sure, $central_text, $command_is_link,
			$confirm_ref, $undo_ref, $confirm_text = false, $undo_text = false) {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$txt = '<div class="boxinfo_title">'.$are_you_sure.'</div>'
		.'<div class="boxinfo_container">'
		.$central_text
		.'</div>'
		.'<div class="del_container">';
	if($command_is_link) {

		$txt .= '<a href="'.$confirm_ref.'">'
				.'<img src="'.getPathImage().'standard/check.gif" alt="'.( $confirm_text == false ? def('_CONFIRM') : $confirm_text ).'" />'
				.'&nbsp;'.( $confirm_text == false ? def('_CONFIRM') : $confirm_text ).'</a>&nbsp;&nbsp;'
				.'<a href="'.$undo_ref.'">'
				.'<img src="'.getPathImage().'standard/undo.gif" alt="'.( $undo_text == false ? def('_UNDO') : $undo_text ).'" />'
				.'&nbsp;'.( $undo_text == false ? def('_UNDO') : $undo_text ).' </a>';
	} else {

		$confirm_ref_id = str_replace(']', '', str_replace('[', '_', $confirm_ref));
		$undo_ref_id	= str_replace(']', '', str_replace('[', '_', $undo_ref));
		$txt .= Form::getButton($confirm_ref_id, $confirm_ref, def('_CONFIRM'), 'transparent_del_button')
			.'&nbsp;'
			.Form::getButton($undo_ref_id, $undo_ref, def('_UNDO'), 'transparent_undo_button');
	}
	$txt .= '</div>';
	return $txt;
}

/**
 * @param string	$entry	the text that you want to add to the legenda
 *
 * @return string 	the text added
 */
function addLegendaEntry($entry) {

	if(!isset($GLOBALS['_legenda'])) $GLOBALS['_legenda'] = array();
	return $GLOBALS['_legenda'][] = $entry;
}

/**
 * Destroy the entry in the legenda
 */
function emptyLegenda() {

	if(!isset($GLOBALS['_legenda'])) $GLOBALS['_legenda'] = array();
}

/**
 * @return string 	the legenda, if it has at least one entry
 */
function getLegenda() {

	$text = '';
	if(!isset($GLOBALS['_legenda'])) $GLOBALS['_legenda'] = array();
	if(is_array($GLOBALS['_legenda']) && count($GLOBALS['_legenda'])) {
		$text = '<div id="legend" class="layout_legenda">
				<div class="title">Legenda</div>'."\n";
		foreach($GLOBALS['_legenda'] as $key => $value) {
			$text .= '<div class="legenda_line">'."\n"
				."\t".$value."\n"
				.'</div>'."\n";
		}
		$text .= '</div>';
	}
	return $text;
}


function setAccessibilityStatus($new_status) {

	if(isset($GLOBALS['framework']['accessibility']) && $GLOBALS['framework']['accessibility'] != 'off') {

		$_SESSION['high_accessibility'] = $new_status;
	} else {
		$_SESSION['high_accessibility'] = false;
	}
}

function getAccessibilityStatus() {

	if(isset($GLOBALS['framework']['accessibility']) && $GLOBALS['framework']['accessibility'] == 'off')
		return false;

	if(isset($_SESSION['high_accessibility']))
		return ($_SESSION['high_accessibility'] == 1);

	else return true;
}


?>
