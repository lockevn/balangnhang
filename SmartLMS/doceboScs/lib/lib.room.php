<?php

/************************************************************************/
/* DOCEBO SCS - Syncronous Collaborative System							*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

function getAdminRules() {
	$query_rules_admin = "
	SELECT enable_recording_function, enable_advice_insert, enable_write, enable_chat_recording,
		enable_private_subroom, enable_public_subroom,
		enable_drawboard_watch, enable_drawboard_write,
		enable_audio, enable_webcam, enable_stream_watch, enable_strem_write, enable_remote_desktop
	FROM ".$GLOBALS['prefix_scs']."_rules_admin ";
	$re_rules_admin = mysql_query($query_rules_admin);
	return mysql_fetch_assoc($re_rules_admin);
}

function getRoomRules($id_room) {
	$query_rules_admin = "
	SELECT room_name, room_type, enable_recording_function, enable_advice_insert, enable_write, enable_chat_recording,
		enable_private_subroom, enable_public_subroom,
		enable_drawboard_watch, enable_drawboard_write,
		enable_audio, enable_webcam, enable_stream_watch, enable_strem_write, enable_remote_desktop
	FROM ".$GLOBALS['prefix_scs']."_rules_room
	WHERE id_room = '".$id_room."'";
	$re_rules_admin = mysql_query($query_rules_admin);
	return mysql_fetch_assoc($re_rules_admin);
}

function insertRoom($array_source) {

	$variable = '';
	$values = '';
	while(list($var_name, $var_value) = each($array_source)) {

		$variable 	.= $var_name.', ';
		$values 	.= "'".$var_value."', ";
	}

	$query_insert = "INSERT INTO ".$GLOBALS['prefix_scs']."_rules_room ( ".substr($variable, 0, -2)." ) VALUES ( "
				.substr($values, 0, -2)." )";
	if(!mysql_query($query_insert)) return false;
	list($id_room) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	return $id_room;
}

function updateRoom($id_room, $array_source) {

	$query_update = "UPDATE ".$GLOBALS['prefix_scs']."_rules_room SET ";
	while(list($var_name, $var_value) = each($array_source)) {

		$query_update .= $var_name." = '".$var_value."', ";
	}
	$query_update = substr($query_update, 0, -2);
	$query_update .= " WHERE id_room = '".$id_room."'";

	return mysql_query($query_update);
}

function deleteRoom($id_room, $room_type = false, $id_source = false) {

	if($room_type == false)
		$query_delete = "DELETE FROM ".$GLOBALS['prefix_scs']."_rules_room WHERE id_room = '".$id_room."'";
	else {
		$query_delete = "DELETE FROM ".$GLOBALS['prefix_scs']."_rules_room
		WHERE room_type = '".$room_type."' AND id_source = '".$id_room."'";
	}

	return mysql_query($query_delete);
}


function getRoomList($incl_room_type=FALSE, $excl_room_type=FALSE) {
	
	$res =array();
	if (!is_array($incl_room_type)) { $incl_room_type =array(); }
	if (!is_array($excl_room_type)) { $excl_room_type =array(); }

	$room_qtxt ="SELECT id_room, room_name "
		." FROM ".$GLOBALS["prefix_scs"]."_rules_room "
		." WHERE 1 ";
	if (count($incl_room_type) > 0) {
		$room_qtxt .= " AND room_type IN ( '".implode("'',", $incl_room_type)."' )";
	}
	if (count($excl_room_type) > 0) {
		$room_qtxt .= " AND room_type NOT IN ( '".implode("'',", $excl_room_type)."' )";
	}
	
	$q = mysql_query($room_qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$id =$row["id_room"];
			$res[$id]=$row["room_name"];
		}
	}

	return $res;
}

?>