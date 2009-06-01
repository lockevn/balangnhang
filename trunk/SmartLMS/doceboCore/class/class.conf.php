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
 * @subpackage configuration
 * @author 	Pirovano Fabio (fabio@docebo.com)
 * @version $Id: class.conf.php 113 2006-03-08 18:08:42Z ema $
 **/
 
class Element {
	
	var $id;
	var $name;
	var $value;
	
	/**
	 * class constructor
	 */
	function Element($id, $name, $value) {
		
		$this->id 		= $id;
		$this->name 	= $name;
		$this->value 	= $value;
		return ;
	}
	
	/**
	 * @return 	string 	the identificative of the type
	 *
	 * @access 	public
	 */
	function getType() {
		
		return 'element';
	}
	
	/**
	 * @return 	string 	use this for display a element
	 *
	 * @access 	public
	 */
	function show() {
		
		return '';
	}
	
	/**
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function save($new_value) {
		
		return true;
	}
}

class Config {
	
	var $table;
	
	/**
	 * class constructor
	 */
	function Config($table) {
		
		$this->table = $table;
		
		return;
	}
	
	/**
	 * @param 	bool	$with_invisible		also return group that contains only invisibile element
	 *
	 * @return 	array 	this array contains association trought id and name of the regroup units
	 *
	 * @access 	public
	 */
	function getRegroupUnit($with_invisible = false) {
		
		return array();
	}
	
	/**
	 * @return 	string 	contains the displayable information for a selected group
	 *
	 * @access 	public
	 */
	function getPageWithElement($regroup) {
		
		return '';
	}
	
	/**
	 * @return 	bool 	true if the operation was successfull false otherwise
	 *
	 * @access 	public
	 */
	function saveElement($regroup) {
		
		return true;
	}
}

?>