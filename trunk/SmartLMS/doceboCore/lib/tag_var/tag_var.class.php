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

class TagVar {

	var $_my_tag_ref = NULL;

	function TagVar() {}

	/**
	 * Parse the attribute string in order to identify the param setted for the element
	 * @param $str 	string 	the list of param to parse i.e. ( name="123" color="456" )
	 *
	 * @return array	return an array, as keys you have the attribute name, as value the attribute value i.e. (array('name' => 123, 'color' => 456))
	 */
	function parse_attrib_string($str) {

		$attrib = array();
		preg_match_all('/\s*([-_a-z]+)=["]([^"]+)["]?/i', stripslashes($str), $regs, PREG_SET_ORDER);

		// convert attributes to an associative array (name => value)
		if(!$regs) return $attrib;
		foreach ($regs as $attr)
			$attrib[strtolower($attr[1])] = $attr[2];
		return $attrib;
	}
	

	/**
	 * Parse the var string in order to identify the param setted for the element
	 * @param $str 	string 	the list of param to parse i.e. ( standard:_LANGUAGE )
	 *
	 * @return array	return an array, as keys you have the attribute name, as value the attribute value i.e. (array('name' => 123, 'color' => 456))
	 */
	function parse_var_string($str) {

		$pieces = array();
		$exploded = explode(':', $str);
		
		foreach ($exploded as $founded)
			$pieces[] = $founded;
		return $pieces;
	}

	/**
	 * Identify and manage a var substitution found in the layout file
	 * @param 	string 	$var the var founded
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_var($var) {

		return '';
	}

	/**
	 * Identify and manage a tag found in the layout file
	 * @param 	array 	$mathces contains the match of the preg_replace_callback
	 *
	 * @return string	the substitution text
	 */
	function parse_docebo_tag($source, $tag, $args) {

		return '';
	}

}

?>