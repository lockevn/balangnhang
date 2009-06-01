<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Fabio Pirovano (gishell@tiscali.it)             */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Learning_Link extends Learning_Object {
	
	var $id;
	
	var $idAuthor;
	
	var $title;
	
	var $back_url;
	
	/** 
	 * object constructor
	 **/
	function Learning_Link( $id = NULL ) {
		
		parent::Learning_Object( $id );
		if( $id !== NULL ) {
			list( $this->idAuthor, $this->title ) = mysql_fetch_row(mysql_query("
			SELECT author, title 
			FROM ".$GLOBALS['prefix_lms']."_link_cat
			WHERE idCategory = '".$id."'"));
		}
	}
	
	function getObjectType() {
		return 'link';
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
		
		require_once( $GLOBALS['where_lms'].'/modules/link/link.php' );
		addlinkcat( $this );
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
		
		require_once( $GLOBALS['where_lms'].'/modules/link/link.php' );
		modlinkgui( $this );
	}
	
	/**
	 * function del
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url (not used yet)
	 * @return false if fail, else return the id lo
	 **/
	function del( $id, $back_url = NULL ) {
		checkPerm('view', false, 'storage');
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_link 
		WHERE idCategory = '".$id."'")) {
			return false;
		}
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_link_cat 
		WHERE idCategory = '".$id."'")) {
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
		list($title, $descr, $author) = mysql_fetch_row(mysql_query("
		SELECT title, description, author
		FROM ".$GLOBALS['prefix_lms']."_link_cat 
		WHERE idCategory = '".$id."'"));
		
		//insert new item
		$query_ins = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_link_cat
		SET title = '".mysql_escape_string($title)."',
			description = '".mysql_escape_string($descr)."',
			author = '".$author."'";
		if(!mysql_query($query_ins)) return false;
		list($idCat) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		
		//retriving quest
		$reQuest = mysql_query("
		SELECT title, link_address, keyword, description, sequence 
		FROM ".$GLOBALS['prefix_lms']."_link 
		WHERE idCategory = '".(int)$id."'");
		while(list($title, $link_a, $keyword, $description, $seq) = mysql_fetch_row($reQuest)) {
			
			$query_ins = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_link
			SET idCategory = '".$idCat."',
				title = '".mysql_escape_string($title)."',
				link_address = '".mysql_escape_string($link_a)."',
				keyword = '".mysql_escape_string($keyword)."',
				description = '".mysql_escape_string($description)."',
				sequence = '".$seq."'";
			if(!mysql_query($query_ins)) {
				$this->del( $idCat );
				return false;
			}
		}
		return $idCat;
	}
	
	/**
	 * function play( $id, $id_param, $back_url )
	 * @param int $id contains the resource id
	 * @param int $id_param contains the id needed for params retriving
	 * @param string $back_url contain the back url
	 * @return nothing return
	 **/
	function play( $id, $id_param, $back_url ) {
		
		require_once( $GLOBALS['where_lms'].'/modules/link/do.link.php' );
		
		$this->id = $id;
		$this->back_url = $back_url;
		
		$step = importVar('next_step');
		switch($step) {
			default : {
				play( $this, $id_param );
			};break;
		}
	}
}

?>
