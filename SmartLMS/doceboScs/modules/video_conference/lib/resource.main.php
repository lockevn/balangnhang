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

class VideoResource {
	
	var $_db = false;
	
	function VideoResource() {
	
		$this->_db =& DbConn::getInstance();
	}
	
	function _query($query, $values= false) {
		
		if($this->_db === false) $this->_db =& DbConn::getInstance();
		$re = $this->_db->query($query, $values);
		
		if(function_exists('debug')) debug($query);
		return $re;
	}
	
	function _parsed_query($query, $values= false) {
		
		if($this->_db === false) $this->_db =& DbConn::getInstance();
		return $this->_db->get_parsed_query($query, $values);
	}
	
	function performAction($action_idref, &$data) {
		
		return true;
	}
	
}

?>