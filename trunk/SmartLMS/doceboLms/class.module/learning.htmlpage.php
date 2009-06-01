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

class Learning_Htmlpage extends Learning_Object {
	
	var $id;
	
	var $idAuthor;
	
	var $title;
	
	var $back_url;
	
	/**
	 * function learning_Object()
	 * class constructor
	 **/
	function Learning_Htmlpage( $id = NULL ) {
		
		parent::Learning_Object( $id );
		if( $id !== NULL ) {
			list( $this->idAuthor, $this->title ) = mysql_fetch_row(mysql_query("
			SELECT author, title 
			FROM ".$GLOBALS['prefix_lms']."_htmlpage 
			WHERE idPage = '".$id."'"));
		}
	}
	
	function getObjectType() {
		return 'htmlpage';
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
		
		require_once( $GLOBALS['where_lms'].'/modules/htmlpage/htmlpage.php' );
		addpage( $this );
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
		
		require_once( $GLOBALS['where_lms'].'/modules/htmlpage/htmlpage.php' );
		modpage( $this );
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
		
		$delete_query = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_htmlpage 
		WHERE idPage = '".$id."'";
		if(!mysql_query( $delete_query )) {
			$_SESSION['last_error'] = def('_ERRDEL', 'htmlpage');
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
		
		//find source info
		list($title, $textof, $author) = mysql_fetch_row(mysql_query("
		SELECT title, textof, author 
		FROM ".$GLOBALS['prefix_lms']."_htmlpage 
		WHERE idPage = '".(int)$id."'"));
		
		//insert new item
		$insertQuery = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_htmlpage 
		SET title = '".mysql_escape_string($title)."',
			textof = '".mysql_escape_string($textof)."',
			author = '".$author."'";
		
		if(!mysql_query($insertQuery)) {
			return false;
		}
		list($idPage) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		return $idPage;
	}
	
	/**
	 * function play( $id, $id_param, $back_url )
	 * @param int $id contains the resource id
	 * @param int $id_param contains the id needed for params retriving
	 * @param string $back_url contain the back url
	 * @return nothing return
	 **/
	function play( $id, $id_param, $back_url ) {
		if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
		
		$this->id = $id;
		$this->back_url = $back_url;
		
		list($title, $textof) = mysql_fetch_row(mysql_query("
		SELECT title, textof 
		FROM ".$GLOBALS['prefix_lms']."_htmlpage 
		WHERE idPage = '".(int)$id."'"));
		
		require_once( $GLOBALS['where_lms'].'/lib/lib.param.php' );
		$idReference = getLOParam($id_param, 'idReference');
		// NOTE: Track only if $idReference is present 
		if( $idReference !== FALSE ) {
			require_once( $GLOBALS['where_lms'].'/class.module/track.htmlpage.php' );
			list( $exist, $idTrack) = Track_Htmlpage::getIdTrack($idReference, getLogUserId(), $this->id, TRUE );
			if( $exist ) {
				$ti = new Track_Htmlpage( $idTrack );
				$ti->setDate(date('Y-m-d H:i:s'));
				$ti->status = 'completed';
				$ti->update();
			} else {
				
				$ti = new Track_Htmlpage( false );
				$ti->createTrack( $idReference, $idTrack, getLogUserId(), date('Y-m-d H:i:s'), 'completed', 'htmlpage' );
			}
		}
		
		$GLOBALS['page']->add('<div id="top" class="std_block">'
			.getBackUi( ereg_replace( '&', '&amp;', $this->back_url ), def('_BACK'))
			.'<div class="title">'.$title.'</div>'
			.'<div class="textof">'.$textof.'</div>'
			.'<br /><br />'
			.'<a href="#top" title="'.def('_BACKTOTOP', 'htmlpage', 'lms').'">'
				.'<img src="'.getPathImage().'standard/upcheck.gif" alt="'.def('_BACKTOTOP', 'htmlpage', 'lms').'" />'
				.def('_BACKTOTOP', 'htmlpage', 'lms').'</a>'
			.getBackUi( ereg_replace( '&', '&amp;', $this->back_url ), def('_BACK'))
			.'</div>', 'content');
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
