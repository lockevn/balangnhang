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

class ReservationRoomPermissions {

	function ReservationRoomPermissions()
	{
	}
	
	function _getReservationPermTable()
	{
		return 'learning_reservation_perm';
	}
	
	function addReservationPerm($perm, $event_id, $idst_arr) {
		$res=TRUE;
		
		if (empty($perm))
			return FALSE;
		
		foreach($idst_arr as $user_idst)
		{
			$qtxt ="INSERT INTO ".$this->_getReservationPermTable()." (event_id, user_idst, perm) ";
			$qtxt.="VALUES ('".$event_id."', '".$user_idst."', '".$perm."')";
			
			$q=mysql_query($qtxt);
			if (!$q)
				$res=FALSE;
		}
		
		return $res;
	}
	
	function removeReservationPerm($perm, $event_id, $idst_arr) {
		$res=TRUE;

		if (empty($perm))
			return FALSE;

		if ((is_array($idst_arr)) && (count($idst_arr) > 0)) {

			$qtxt ="DELETE FROM ".$this->_getReservationPermTable()." WHERE event_id='".$event_id."' AND ";
			$qtxt.="perm='".$perm."' AND ";
			$qtxt.="user_idst IN (".implode(",", $idst_arr).")";

			$q=mysql_query($qtxt);
			if (!$q)
				$res=FALSE;
		}

		return $res;
	}
	
	function getAllReservationPerm($event_id) {
		$res=array();

		$fields="user_idst, perm";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getReservationPermTable()." WHERE ";
		$qtxt.="event_id='".$event_id."'";

		$q=mysql_query($qtxt);

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
