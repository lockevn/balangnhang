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

require_once( dirname( __FILE__ ).'/learning.object.php' );

class Learning_Item extends Learning_Object {
	
	var $id;
	
	var $idAuthor;
	
	var $title;
	
	var $back_url;
	
	/**
	 * function learning_Object()
	 * class constructor
	 **/
	function Learning_Item( $id = NULL ) {
		
		parent::Learning_Object( $id );
		if( $id !== NULL ) {
			list( $this->idAuthor, $this->title ) = mysql_fetch_row(mysql_query("
			SELECT author, title 
			FROM ".$GLOBALS['prefix_lms']."_materials_lesson 
			WHERE idLesson = '".$id."'"));
		}
	}
	
	function getObjectType() {
		return 'item';
	}
	
	/**
	 * function create( $back_url )
	 * @param string $back_url contains the back url
	 * @return nothing
	 * attach the id of the created object at the end of back_url with the name, in attach the result in create_result
	 *
	 * static
	 **/
	function create( $back_url ) {
		
		$this->back_url = $back_url;
		
		unset($_SESSION['last_error']);
		
		require_once( $GLOBALS['where_lms'].'/modules/item/item.php' );
		additem( $this );
	}
	
	/**
	 * function edit
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url
	 * @return nothing
	 * attach in back_url id_lo that is passed $id and attach edit_result with the result of operation in boolean format 
	 **/
	function edit( $id, $back_url ) {
		
		$this->id = $id;
		$this->back_url = $back_url;
		
		unset($_SESSION['last_error']);
		
		require_once( $GLOBALS['where_lms'].'/modules/item/item.php' );
		moditem( $this );
	}
	
	/**
	 * function del
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url (not used yet)
	 * @return false if fail, else return the id lo
	 **/
	function del( $id, $back_url = NULL ) {
		checkPerm('view', false, 'storage');
		
		unset($_SESSION['last_error']);
		
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
		
		$path_to_file = '/doceboLms/'.$GLOBALS['lms']['pathlesson'];
		
		list($old_file) = mysql_fetch_row(mysql_query("
		SELECT path 
		FROM ".$GLOBALS['prefix_lms']."_materials_lesson 
		WHERE idLesson = '".$id."'"));
		
		$size = getFileSize($GLOBALS['where_files_relative'].$path_to_file.$old_file);
		if($old_file != '') {
			
			sl_open_fileoperations();
			if(!sl_unlink( $path_to_file.$old_file )) {
				sl_close_fileoperations();
				$_SESSION['last_error'] = def('_ERRDELFILE', 'item');
				return false;
			}
			sl_close_fileoperations();
			$GLOBALS['course_descriptor']->subFileToUsedSpace(false, $size);
		}
		$delete_query = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_materials_lesson 
		WHERE idLesson = '".$id."'";
		
		if(!mysql_query( $delete_query )) {
			
			$_SESSION['last_error'] = def('_ERRDEL', 'item');
			return false;
		}
		return $id;

	}
	
	/**
	 * function copy( $id, $back_url )
	 * @param int $id contains the resource id
	 * @param string $back_url contain the back url (not used yet)
	 * @return int $id if success FALSE if fail
	 **/
	function copy( $id, $back_url = NULL ) {
		
		require_once( $GLOBALS['where_framework'].'/lib/lib.upload.php' );
		
		//find source info
		list($title, $descr, $file) = mysql_fetch_row(mysql_query("
		SELECT title, description, path 
		FROM ".$GLOBALS['prefix_lms']."_materials_lesson 
		WHERE idLesson = '".(int)$id."'"));
		
		//create the copy filename 
		$path_to_file = '/doceboLms/'.$GLOBALS['lms']['pathlesson'];
		$savefile = $_SESSION['idCourse'].'_'.mt_rand(0, 100).'_'.time().'_'
			.implode('_', array_slice(explode('_', $file), 3));
		
		//copy fisic file
		sl_open_fileoperations();
		if(!sl_copy( $path_to_file.$file, $path_to_file.$savefile )) {
			sl_close_fileoperations();
			return false;
		}
		
		//insert new item
		$insertQuery = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_materials_lesson 
		SET author = '".getLogUserId()."',
			title = '".mysql_escape_string($title)."',
			description = '".mysql_escape_string($descr)."',
			path = '".mysql_escape_string($savefile)."'";
			
		
		if(!mysql_query($insertQuery)) {
			sl_unlink( $path_to_file.$savefile );
			sl_close_fileoperations();
			return false;
		}
		sl_close_fileoperations();
		
		list($idLesson) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		return $idLesson;
	}
	
	/**
	 * function play( $id, $id_param, $back_url )
	 * @param int $id contains the resource id
	 * @param int $id_param contains the id needed for params retriving
	 * @param string $back_url contain the back url
	 * @return nothing return
	 **/
	function play( $id, $id_param, $back_url ) {
		
		require_once( $GLOBALS['where_lms'].'/modules/item/do.item.php' );
		
		$this->id = $id;
		$this->back_url = $back_url;
		play( $id, $id_param, $back_url );
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
}

?>
