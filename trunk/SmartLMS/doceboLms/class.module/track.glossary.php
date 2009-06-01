<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_lms'].'/class.module/track.object.php');

class Track_Glossary extends Track_Object {
	
	function Track_Glossary( $idTrack ) {
		$this->objectType = 'glossary';
		parent::Track_Object($idTrack);
	}

	function getIdTrack( $idReference, $idUser, $idResource, $createOnFail = FALSE ) {
		
		$query = "SELECT idTrack FROM ".$GLOBALS['prefix_lms']."_materials_track"
				." WHERE idReference='".(int)$idReference."'"
				."   AND idUser='".(int)$idUser."'";
		$rs = mysql_query( $query )
			or errorCommunication( 'getIdTrack' );
		if( mysql_num_rows( $rs )  > 0 ) {
			list( $idTrack ) = mysql_fetch_row( $rs );
			return array( TRUE, $idTrack );
		} else if( $createOnFail ) {
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_materials_track"
					."( idResource, idReference, idUser ) VALUES ("
					."'".(int)$idResource."','".(int)$idReference."','".(int)$idUser."')";
			mysql_query( $query )
				or errorCommunication( 'getIdTrack' );
			$idTrack = mysql_insert_id();
			return array( FALSE, $idTrack );
		}
		return FALSE;
	}
	
	/**
	 * print in standard output 
	 **/
	function loadReport() {
		
	}
}

?>
