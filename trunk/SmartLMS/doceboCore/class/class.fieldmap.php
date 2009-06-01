<?php
/************************************************************************/
/* DOCEBO CORE - Framework                                              */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2006                                                   */
/* http://www.docebo.org                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * @package admin-core
 * @subpackage field
 */

Class FieldMap {

	var $lang=NULL;

	/**
	 * class constructor
	 */
	function FieldMap() {

	}


	function _query( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _insQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	function _getMainTable() {

	}


	function getPrefix() {
		return "";
	}


	function getPredefinedFieldLabel($field_id) {
		return ucfirst($field_id);
	}


	function getRawPredefinedFields() {
		return array();
	}


	function getPredefinedFields($with_prefix=TRUE) {
		$res=array();

		$pfx=($with_prefix ? $this->getPrefix()."predefined_" : "");
		foreach($this->getRawPredefinedFields() as $code) {
			$res[$pfx.$code]=$this->getPredefinedFieldLabel($code);
		}

		return $res;
	}


	function getCustomFields($with_prefix=TRUE) {
		return array();
	}
	

	/**
	 * @param array $predefined_data
	 * @param array $custom_data
	 * @param mixed $id
	 * @param boolean $dropdown_id if true will take dropdown values as id;
	 *                             else will search the id starting from the value.
	 */
	function saveFields($predefined_data, $custom_data, $id=FALSE) {
		return FALSE;
	}	

}

?>