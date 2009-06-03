<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

if(!defined("IN_DOCEBO")) die("You can't access this file directly!");

class Layout {
	
	function heading() {
		$html = '';
		return $html;
	}

	function charset() 					{ return 'utf-8'; }

	function lang_code() 				{ return 'it'; }

	function description() 				{ return ''; }

	function keyword() 					{ return ''; }
	
	function menu($menu_code) 			{

		switch($menu_code) {
			case "menu_over" : return $GLOBALS['page']->getContent('menu_over');
			case "menu" : return $GLOBALS['page']->getContent('menu');
		}
	}

	function get_where($base, $rel = true) {

		$rel = ( $rel ? '_relative' : '' );
		switch($base) {
			case "basesite" 	: return $GLOBALS['where_scs'.$rel].'';					break;
			case "addons" 		: return $GLOBALS['where_framework'.$rel].'/addons';	break;
			case "framework" 	: return $GLOBALS['where_framework'.$rel];				break;
			case "files" 		: return $GLOBALS['where_files'.$rel];					break;
			case "siteurl" 		: return $GLOBALS['base_url'];							break;
		}

	}
	
	function parse_docebo_xml($input) {

		// replace tags
		$output = preg_replace_callback(	'/<docebo:([_a-z]+)-([-_a-z]+)\s+([^>]+)>/Ui',
											"parse_docebo_tag",
											$input );

		// replace vars
		$output = preg_replace_callback(	'/{--([_a-z]+):([\w\:\_\s]+)--}/Ui',
											"parse_docebo_var",
											$output );
		// ok all done
		return $output;
	}

	/**
	 * Insipirated from roundcube project
	 * Author: Thomas Bruederli <roundcube@gmail.com>
	 */
	function parse_template($path = '', $exit = TRUE) {		
		if(!isset($GLOBALS['tag_var_instances'])) $GLOBALS['tag_var_instances'] = array();
		
		$browser_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
		$pos = strpos($browser_code, ';');
		if($pos !== false) $browser_code = substr($browser_code, 0, $pos);
	
		$browser = getBrowserInfo();
		if($browser["browser"] !== 'msie') {
			header("Content-Type: text/html; charset=".getUnicode()."");
			$GLOBALS['page']->addStart('<?xml version="1.0" encoding="'.getUnicode().'"?'.'>'."\n", 'page_head' );
		} else {
			header("Content-Type: text/html; charset=".getUnicode()."");
		}

		$templ = '';

		if($fp = @fopen($path, 'r')) {

			$templ = fread($fp, filesize($path));
			fclose($fp);
		} else {
			die('Template file not found : "'.$path.'"');
			return false;
		}

		// parse for specialtags
		$output = Layout::parse_docebo_xml($templ);

		//return trim(parse_with_globals($output)), $skin_path);

		return trim($output);
	}

	/**
	 * Insipirated from roundcube project
	 * Author: Thomas Bruederli <roundcube@gmail.com>
	 */
	function parse_php_template($path = '', $exit = TRUE) {
		
		if(!isset($GLOBALS['tag_var_instances'])) $GLOBALS['tag_var_instances'] = array();
		
		$browser_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
		$pos = strpos($browser_code, ';');
		if($pos !== false) $browser_code = substr($browser_code, 0, $pos);

		$browser = getBrowserInfo();
		if($browser["browser"] !== 'msie') {
			header("Content-Type: text/html; charset=".getUnicode()."");
			$GLOBALS['page']->addStart('<?xml version="1.0" encoding="'.getUnicode().'"?'.'>'."\n", 'page_head' );
		} else {
			header("Content-Type: text/html; charset=".getUnicode()."");
		}
		
		$templ = '';

		// save current cached content
		$old_ob = ob_get_contents();
		ob_clean();
		ob_start();

		// include and parse php of the template file
		if(!file_exists($path)) {
			die('Template file not found : "'.$path.'"');
			return false;
		}
		@include($path);

		//take the output of the included file
		$templ = ob_get_contents();

		// restore buffer
		ob_clean();
		ob_start();
		echo($old_ob);

		// parse for specialtags
		$output = Layout::parse_docebo_xml($templ);

		//return trim(parse_with_globals($output)), $skin_path);

		return trim($output);
	}
}

/**
 * Identify and manage a var substitution found in the layout file
 * @param 	array 	$mathces contains the match of the preg_replace_callback
 * 
 * @return string	the substitution text
 */
function parse_docebo_var($matches) {
	
	$source = trim(strtolower($matches[1]));
	
	if(!isset($GLOBALS['tag_var_instances'][$source])) {
	
		if(file_exists($GLOBALS['where_framework'].'/lib/tag_var/tag_var.'.$source.'.php')) {
			
			require_once($GLOBALS['where_framework'].'/lib/tag_var/tag_var.'.$source.'.php');
			
			$class_name = ucfirst($source).'_TagVar';  
			$GLOBALS['tag_var_instances'][$source] = new $class_name();
		}
	}
	if($GLOBALS['tag_var_instances'][$source] != false) {
		
		return $GLOBALS['tag_var_instances'][$source]->parse_docebo_var($matches[2]);
	}
	return '';
}

/**
 * Identify and manage a tag found in the layout file
 * @param 	array 	$mathces contains the match of the preg_replace_callback
 * 
 * @return string	the substitution text
 */
function parse_docebo_tag($matches) {
	
	$source = trim(strtolower($matches[1]));
	$tag 	= trim(strtolower($matches[2]));
	$args 	= substr($matches[3], 0, -2);

	if(!isset($GLOBALS['tag_var_instances'][$source])) {
	
		if(file_exists($GLOBALS['where_framework'].'/lib/tag_var/tag_var.'.$source.'.php')) {
			
			require_once($GLOBALS['where_framework'].'/lib/tag_var/tag_var.'.$source.'.php');
			
			$class_name = ucfirst($source).'_TagVar';  
			$GLOBALS['tag_var_instances'][$source] = new $class_name();
		}
	}
	if($GLOBALS['tag_var_instances'][$source] != false) {
		
		return $GLOBALS['tag_var_instances'][$source]->parse_docebo_tag($tag, $args);
	}
	return '';
/*
	// execute command
	if($source == 'general')
	switch ($tag) {
	
		case "html_open_tag" : {
		
			return Layout::heading();
		};break;
		case "var" : {

			return ( isset($GLOBALS['substitue'][$args['name']]) ? $GLOBALS['substitue'][$args['name']] : '' );
		};break;
		case "meta" : {

			return '<meta http-equiv="Content-Type" content="text/html; charset='.Layout::charset().'" />';
		};break;

		case "object" : {

			if($args['name'] == 'pagetitle') return Layout::title();
		};break;

		case "style" : {

			return '<link type="text/css" href="'.getPathTemplate().'style/'.$args['file'].'" rel="stylesheet" />';
		};break;

		case "script" : {

			return '<script type="text/javascript" src="'.$args['file'].'"></script>';
		};break;

		case "zone" : {

			return $GLOBALS['page']->getContent($args['name']);
		};break;
		case "login" : {

			return $GLOBALS['login_mask'];
		};break;
		
		
		//include a file
		case 'include': {

			if($fp = @fopen($args['file'], 'r')) {
				$incl = fread($fp, filesize($args['file']));
				fclose($fp);
				return Layout::parse_docebo_xml($incl);
			}
		}
		break;
	}
	return '';*/
}


?>