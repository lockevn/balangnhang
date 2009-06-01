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

class ChatBooking {

	var $prefix=NULL;
	var $dbconn=NULL;
	var $platform="";
	var $module="";

	var $room_subscriptions=NULL;
	var $user_subscriptions=NULL;


	function ChatBooking($module, $prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== FALSE ? $prefix : $GLOBALS["prefix_scs"]);
		$this->dbconn=$dbconn;
		$this->platform=$GLOBALS["platform"];
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


	function _getBookingTable() {
		return $this->prefix."_booking";
	}


	function getPlatform() {
		return $this->platform;
	}


	function getModule() {
		return $this->module;
	}


	function bookRoom($user_idst, $room_id) {
		$res=FALSE;

		$qtxt ="SELECT booking_id FROM ".$this->_getBookingTable()." ";
		$qtxt.="WHERE room_id='".(int)$room_id."' AND user_idst='".(int)$user_idst."' LIMIT 0,1";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {

			$row=mysql_fetch_assoc($q);
			$res=$row["booking_id"];

		}
		else if (($q) && (mysql_num_rows($q) == 0)) {

			$qtxt ="INSERT INTO ".$this->_getBookingTable()." (room_id, platform, module, user_idst) ";
			$qtxt.="VALUES ('".(int)$room_id."', '".$this->getPlatform()."', ";
			$qtxt.="'".$this->getModule()."', '".(int)$user_idst."')";

			$booking_id=$this->_executeInsert($qtxt);
			$res=$booking_id;

		}

		return $res;
	}


	function loadRoomSubscriptions($room_id, $where=FALSE) {
		$res=array();

		$fields="booking_id, platform, module, user_idst, approved";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getBookingTable()." ";
		$qtxt.="WHERE room_id='".(int)$room_id."'";

		if (($where !== FALSE) && (!empty($where)))
			$qtxt.=" AND ".$where;

		$q=$this->_executeQuery($qtxt);


		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_assoc($q)) {
				$user_idst=$row["user_idst"];
				$res[$user_idst]=$row;
			}
		}

		return $res;
	}


	function getRoomSubscriptions($room_id, $where=FALSE) {

		$rs=$this->room_subscriptions;

		if ((isset($rs[$room_id])) && (is_array($rs[$room_id]))) {
			return $rs[$room_id];
		}
		else {
			$this->room_subscriptions[$room_id]=$this->loadRoomSubscriptions($room_id, $where);
			return $this->room_subscriptions[$room_id];
		}
	}


	function setApproved($user_idst, $room_id, $val=TRUE) {

		$qtxt ="UPDATE ".$this->_getBookingTable()." SET approved='".(int)$val."' ";
		$qtxt.="WHERE room_id='".(int)$room_id."' AND user_idst='".(int)$user_idst."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}


	function deleteByRoom($room_to_del) {

		if ((!is_array($room_to_del)) || (count($room_to_del) < 1))
			return FALSE;

		$qtxt ="DELETE FROM ".$this->_getBookingTable()." ";
		$qtxt.="WHERE room_id IN '".implode(",", $room_to_del)."' ";
		$qtxt.="AND module='".$this->getModule()."'";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}


	function deleteBooking($user_idst, $room_id) {

		$qtxt ="DELETE FROM ".$this->_getBookingTable()." ";
		$qtxt.="WHERE room_id='".(int)$room_id."' AND user_idst='".(int)$user_idst."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}


}
?>