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

class Language_TagVar extends TagVar {

	function Language_TagVar() {

		$this->_my_tag_ref = 'language';
	}

	/**
	 * Identify and manage a var substitution found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_var($var) {

		$request = $this->parse_var_string($var);
		if(count($request) == 3) {

			$platform 	= $request[0];
			$module 	= $request[1];
			$key 		= $request[2];
		} elseif(count($request) == 2) {
		
			$platform 	= $GLOBALS['platform'];
			$module 	= $request[0];
			$key 		= $request[1];
		} else {

			return '';
		}
		$lang =& DoceboLanguage::createInstance($module, $platform);
		return $lang->def($key);
	}

	/**
	 * Identify and manage a tag found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_tag($tag, $args) {

		$request = $this->parse_attrib_string($args);

		$platform 	= ( $args['platform'] != '' ? $args['platform'] : $GLOBALS['platform'] );
		$module 	= $args['module'];
		$key 		= $args['key'];
		
		$lang =& DoceboLanguage::createInstance($module, $platform);
		return $lang->def($key);
	}


}

?>