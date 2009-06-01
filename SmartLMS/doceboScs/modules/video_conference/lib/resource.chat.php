<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

if(!defined("IN_DOCEBO")) die("You can't access this file directly!");

require_once(dirname(__FILE__).'/resource.main.php');

define("CHATVR_ID", 			0);
define("CHATVR_ID_USER", 		1);
define("CHATVR_DISPLAYNAME", 	2);
define("CHATVR_ROOM_ID", 		3);
define("CHATVR_SENT_DATE", 		4);
define("CHATVR_TEXTOF", 		5);
define("CHATVR_TYPEOF", 		6);

class Chat_VR extends VideoResource {

	var $_room_id;
	
	var $_chat_msg_table;
	
	var $_cm_field;
	
	var $_emot = false;
	
	function Chat_VR($room_id) {
		
		require_once($GLOBALS['where_scs'].'/lib/lib.emoticons.php');
		
		$this->_cm_field = array(
			CHATVR_ID 			=> 'id_msg', 
			CHATVR_ID_USER 		=> 'id_user', 
			CHATVR_DISPLAYNAME 	=> 'display_name', 
			CHATVR_ROOM_ID 		=> 'id_room', 
			CHATVR_SENT_DATE 	=> 'sent_date', 
			CHATVR_TEXTOF 		=> 'textof',
			CHATVR_TYPEOF 		=> 'typeofmsg'
		);
		ksort($this->_cm_field);
		reset($this->_cm_field);
		
		$this->_room_id = $room_id;
		$this->_chat_msg_table = $GLOBALS['prefix_scs'].'_vc_chat';
		
		$this->_emot = new HtmlChatEmoticons();
	}
	
	
	function performAction($action_idref, &$data) {
		
		if(!isset($_SESSION['vc_chat']['last_message_request_time'])) {
			$_SESSION['vc_chat']['last_message_request_time'] = date("Y-m-d H:i:s");
		}
		if(strcmp($_SESSION['vc_user']['login_date'], $_SESSION['vc_chat']['last_message_request_time']) > 0) {
			
			$_SESSION['vc_chat']['last_message_request_time'] = $_SESSION['vc_user']['login_date'];
		}
		
		$result = false;
		switch($action_idref) {
			
			case "post_message" : {
				// clean data recived
				return $this->appendChatRoomMessage(	$_SESSION['id_user'], 
														$_SESSION['display_name'], 
														date("Y-m-d H:i:s"), 
														$data->textof );
			};break;
			case "get_message_list" : {
				
				$msg = $this->getChatRoomMessage($_SESSION['vc_chat']['last_message_request_time']);
				if(!empty($msg)) {
					$row = end($msg);
					reset($msg);
					$_SESSION['vc_chat']['last_message_request_time'] = $row[CHATVR_SENT_DATE];
				}
				return $msg;
			};break;
		}
		return $result;
	}
	
	/**
	 * return all the message for the room specified in the constructor from the $refer_date
	 * 
	 */
	function getChatRoomMessage($refer_date = false) {
		
		$db =& DbConn::getInstance();
		$json = new Services_JSON();
		
		$msgs = array();
		
		$query = " SELECT ".implode(', ', $this->_cm_field)." "
			." FROM ".$this->_chat_msg_table." "
			." WHERE ".$this->_cm_field[CHATVR_ROOM_ID]." = %i ";
		if($refer_date !== false) {
			
			$query .= " AND ".$this->_cm_field[CHATVR_SENT_DATE]." > %date " 
				." AND ".$this->_cm_field[CHATVR_SENT_DATE]." < %date ";
		}
		$data = array($this->_room_id, $refer_date, date("Y-m-d H:i:s") );
		
		$re = $db->query($query, $data);
		
		if(!$db->num_rows($re)) return $msgs;
		while($row = $db->fetch_row($re)) {
			
			if($row[CHATVR_TYPEOF] == 'system') {
				$row[CHATVR_TEXTOF] = $json->encode(unserialize(urldecode($row[CHATVR_TEXTOF])));
			}
			$msgs[$row[CHATVR_ID]] = $row;
		}
		return $msgs;
		
	}
	
	/**
	 * insert a new message into the chat
	 */
	function appendChatRoomMessage($id_user, $display_name, $sent_date, $textof) {
		
		$query = "INSERT INTO ".$this->_chat_msg_table." ( "
			." ".$this->_cm_field[CHATVR_ID].", "
			." ".$this->_cm_field[CHATVR_ID_USER].", "
			." ".$this->_cm_field[CHATVR_DISPLAYNAME].", "
			." ".$this->_cm_field[CHATVR_ROOM_ID].", "
			." ".$this->_cm_field[CHATVR_SENT_DATE].", "
			." ".$this->_cm_field[CHATVR_TEXTOF].", "
			." ".$this->_cm_field[CHATVR_TYPEOF]." "
			." ) VALUES ( "
			." %autoinc, "
			." %i, "
			." %s, "
			." %i, "
			." %date, "
			." %html, "
			." %s  )";
		
		$data = array(NULL, $id_user, ( $display_name === NULL ? 'NULL' : $display_name ), 
			$this->_room_id, $sent_date, $this->_emot->drawEmoticon($textof), '');
		
		return $this->_query($query, $data);
	}
	
	function sendSytemMessage($type_of_msg, &$data) {
		
		switch($type_of_msg) {
			
			// user login logout -------------------------------
			case "login_user" : {
				
				$msg = array('login', $data[0], 'Date il benvenuto a : <b>'.$data[1].'</b>');
				return $this->appendSystemMsg($data[0], $data[1], $msg);
			};break;
			case "logout_user" : {
				
				$msg = array('logout', $data[0], '<b>'.$data[1].'</b> è andato a fare altro');
				return $this->appendSystemMsg($data[0], $data[1], $msg);
			};break;
			
			// webcam broadcast --------------------------------
			case "broadcast_webcam" : {
				
				$msg = array('broadcast_webcam', $data[0], $data[1]);
				return $this->appendSystemMsg($data[0], $data[1], $msg);
			};break;
			case "unbroadcast_webcam" : {
				
				$msg = array('unbroadcast_webcam', $data[0]);
				return $this->appendSystemMsg($data[0], '', $msg);
			};break;
			
			// rise hand ---------------------------------------
			case "rise_hand" : {
				
				$msg = array('rise_hand', $data[0]);
				return $this->appendSystemMsg($data[0], '', $msg);
			};break;
			case "lower_hand" : {
				
				$msg = array('lower_hand', $data[0]);
				return $this->appendSystemMsg($data[0], '', $msg);
			};break;
		}
	}
	
	function appendSystemMsg($id_user, $display_name, $msg) {
		
		$json = new Services_JSON();
		
		$query = "INSERT INTO ".$this->_chat_msg_table." ( "
			." ".$this->_cm_field[CHATVR_ID].", "
			." ".$this->_cm_field[CHATVR_ID_USER].", "
			." ".$this->_cm_field[CHATVR_DISPLAYNAME].", "
			." ".$this->_cm_field[CHATVR_ROOM_ID].", "
			." ".$this->_cm_field[CHATVR_SENT_DATE].", "
			." ".$this->_cm_field[CHATVR_TEXTOF].", "
			." ".$this->_cm_field[CHATVR_TYPEOF]." "
			." ) VALUES ( %autoinc, %i, %s, %i, %date, %text, %s ) ";
			
		$data = array(NULL,  
			$id_user, 
			$display_name,
			$this->_room_id, 
			date("Y-m-d H:i:s"), 
			urlencode(serialize($msg)), 
			'system'
		);
		return $this->_query($query, $data);
	}
	
}

?>