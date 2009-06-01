<?php
/*************************************************************************/
/* DOCEBO SCS - Syncronous Collaborative System                          */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_scs'].'/setting.php');

class RoomPermissions {

	var $prefix=NULL;
	var $dbconn=NULL;
	var $room_id="";
	var $module="";


	function RoomPermissions($room_id, $module, $prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== FALSE ? $prefix : $GLOBALS["prefix_scs"]);
		$this->dbconn=$dbconn;
		$this->platform=$GLOBALS["platform"];
		$this->room_id=(int)$room_id;
		$this->module=$module;
	}


	function _executeQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
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


	function _getPermTable() {
		return $this->prefix."_chatperm";
	}


	function getRoomId() {
		return (int)$this->room_id;
	}


	function setRoomId($room_id) {
		$this->room_id=(int)$room_id;
	}

	function getModule() {
		return $this->module;
	}


	function addPerm($perm, $idst_arr) {
		$res=TRUE;

		if (empty($perm))
			return FALSE;

		foreach($idst_arr as $user_idst) {
			$qtxt ="INSERT INTO ".$this->_getPermTable()." (room_id, module, user_idst, perm) ";
			$qtxt.="VALUES ('".$this->getRoomId()."', '".$this->getModule()."', '".$user_idst."', '".$perm."')";

			$q=$this->_executeQuery($qtxt);
			if (!$q)
				$res=FALSE;
		}

		return $res;
	}


	function removePerm($perm, $idst_arr) {
		$res=TRUE;

		if (empty($perm))
			return FALSE;

		if ((is_array($idst_arr)) && (count($idst_arr) > 0)) {

			$qtxt ="DELETE FROM ".$this->_getPermTable()." WHERE room_id='".$this->getRoomId()."' AND ";
			$qtxt.="module='".$this->getModule()."' AND perm='".$perm."' AND ";
			$qtxt.="user_idst IN (".implode(",", $idst_arr).")";

			$q=$this->_executeQuery($qtxt);
			if (!$q)
				$res=FALSE;
		}

		return $res;
	}


	function getAllPerm() {
		$res=array();

		$fields="user_idst, perm";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getPermTable()." WHERE ";
		$qtxt.="room_id='".$this->getRoomId()."' AND module='".$this->getModule()."'";

		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_assoc($q)) {

				$user_idst=$row["user_idst"];
				$perm=$row["perm"];
				$res[$perm][$user_idst]=$user_idst;

			}
		}

		return $res;
	}


}


?>
