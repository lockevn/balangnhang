<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright(c) 2004													 */
/* Fabio Pirovano (gishell@tiscali.it)									 */
/*                                                                       */
/* http://www.spaghettilearning.com										 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Learning_Object {
	
	var $id;
	
	var $idAuthor;
	
	var $title;
	
	/**
	 * function learning_Object()
	 * class constructor
	 **/
	function Learning_Object( $id = NULL ) {
		$this->id = $id;
		
		$this->idAuthor = '';
		$this->title = '';
	}
	
	/**
	 * function getId()
	 * @return int resource id
	 **/
	
	function getId() {
		return $this->id;
	}
	
	/**
	 * function getIdAuthor()
	 * @return int resource author id
	 **/
	
	function getIdAuthor() {
		return $this->idAuthor;
	}
	
	/**
	 * function getTitle()
	 * @return string title
	 **/
	
	function getTitle() {
		return $this->title;
	}
	
	/**
	 * function getObjectType()
	 * @return string Learning_Object type
	 **/
	
	function getObjectType() {
		return 'object';
	}
	
	/**
	 * function create( $back_url )
	 * @param string $back_url contains the back url
	 * @return bool TRUE if success FALSE if fail
	 * attach the id of the created object at the end of back_url with the name id_lo
	 *
	 * static
	 **/
	 
	function create( $back_url ) {
	
	}
	
	/**
	 * function edit
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url
	 * @return bool TRUE if success FALSE if fail
	 * attach in back_url id_lo that is passed $id and attach edit_result with the result of operation in boolean format 
	 **/
	function edit( $id, $back_url ) {
	
	}
	
	/**
	 * function del
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url (not used yet)
	 * @return int $id if success FALSE if fail
	 * 
	 **/
	function del( $id, $back_url = NULL ) {
	
	}
	
	/**
	 * function copy( $id, $back_url )
	 * @param int $id contains the resource id
	 * @param string $back_url contain the back url (not used yet)
	 * @return int $id if success FALSE if fail
	 **/
	function copy( $id, $back_url = NULL ) {
	
	}
	
	/**
	 * function getParamInfo()
	 * return array of require params for play
	 * @return an example of associative array returned is:
	 *	[0] => (
	 *		['label'] => _DEFINITION,
	 *		['param_name'] => parameter name;
	 *	),
	 *	[1] = >(
	 *		['label'] => _DEFINITION,
	 *		['param_name'] => parameter name;
	 * ) ...
	 **/
	
	function getParamInfo() {
		return FALSE;
	}
	
	/**
	 * function play( $id, $id_param, $back_url )
	 * @param int $id contains the resource id
	 * @param int $id_param contains the id needed for params retriving
	 * @param string $back_url contain the back url
	 * @return nothing return
	 **/
	function play( $id, $id_param, $back_url ) {
	
	}
	
	/**
	 * function import( $source, $back_url ) NOT IMPLEMENTED YET
	 * @param string $source contains the filename 
	 * @return bool TRUE if success FALSE if fail
	 * if operation success attach the new id at the back url with the name id_lo 
	 **/
	function import( $source, $back_url ) {
	
	}
	
	/**
	 * function export( $id, $format, $back_url ) NOT IMPLEMENTED YET
	 * @param string $id contain resource id
	 * @param string $format contain output format
	 * @param string $back_url contain the back url 
	 * @return bool TRUE if success FALSE if fail
	 **/
	function export( $id, $format, $back_url ) {
		
	}
	
	/** 
	 * function getMultipleResource( $idMultiResource )
	 * @param int $idMultiResource identifier of the multi resource
	 * @return array an array with the ids of all resources
	 **/
	function getMultipleResource( $idMultiResource ) {
		return array();
	}
	
	/**
	 * function canBeMilestone() 
	 * @return TRUE if this object can be a milestone
	 *			FALSE otherwise
	 **/
	function canBeMilestone() {
		return FALSE;
	}
}

?>
