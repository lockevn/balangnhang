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

require_once($GLOBALS['where_framework'].'/lib/tag_var/tag_var.class.php');

class General_TagVar extends TagVar {

	function General_TagVar() {

		$this->_my_tag_ref = 'general';
	}

	/**
	 * Identify and manage a var substitution found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_var($var) {
		
		// execute command
		$request = $this->parse_var_string($var);
		switch($request[0]) {
			case "path_template" : { return getPathTemplate(); };break;
			case "lang_code" : { 
				
				$browser_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
				$pos = strpos($browser_code, ';');
				if($pos !== false) $browser_code = substr($browser_code, 0, $pos);
				
				return $browser_code;
			};break;
			case "rand" : {
				$min = (int)$request[1];
				$max = (int)$request[2];
				
				return mt_rand($min, $max);
			};break;
		}
		return '';
	}

	/**
	 * Identify and manage a tag found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_tag($tag, $args) {

		$args = $this->parse_attrib_string($args);
		// execute command
		switch ($tag) {
			case "var" : {

				return ( isset($GLOBALS['substitue'][$args['name']]) ? $GLOBALS['substitue'][$args['name']] : '' );
			};break;
			case "meta" : {
			
			    $plat = ( isset($args['platform']) ? $args['platform'] : false );
				return '<meta http-equiv="Content-Type" content="text/html; charset='.Layout::charset().'" />'
						.'<meta name="Copyright" content="Docebo srl" />'
						.'<link rel="Copyright" href="http://www.docebo.com/" title="Copyright Notice" />'
						.'<link href="'.getPathTemplate($plat).'favicon.gif" rel="shortcut icon" />';
			};break;
			case "title" : {

				return $GLOBALS['title_page'];
			};break;
			case "style" : {

				return '<link type="text/css" href="'.getPathTemplate($args['from']).'style/'.$args['file'].'" rel="stylesheet" />';
			};break;

			case "script" : {

				return '<script type="text/javascript" src="'.$args['file'].'"></script>';
			};break;
			case "zone" : {
				
				return $GLOBALS['page']->getContent($args['name']);
			};break;
			case "page_head" : {

				return $GLOBALS['page']->getContent('page_head');
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
		return '';
	}


}

?>