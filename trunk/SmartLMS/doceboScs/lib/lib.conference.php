<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.5.0
 * 
 * ( editor = Eclipse 3.2.0 [phpeclipse,subclipse,WTP], tabwidth = 4 ) 
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_scs'].'/setting.php');
require_once($GLOBALS['where_scs'].'/lib/lib.dimdim.php');
require_once($GLOBALS['where_scs'].'/lib/lib.intelligere.php');
require_once($GLOBALS['where_scs'].'/lib/lib.teleskill.php');

require_once($GLOBALS['where_framework']."/lib/lib.calendar_core.php");
require_once($GLOBALS['where_framework']."/lib/lib.calevent_core.php");
require_once($GLOBALS['where_framework']."/lib/lib.calevent_lms.php");

class Conference_Manager {
	
	function Conference_Manager() {
		$this->creation_limit_per_user = $GLOBALS['scs']['conference_creation_limit_per_user'];
	}
	
	function _getRoomTable() {
		
		return $GLOBALS['prefix_scs'].'_room';
	}
	
	function _query($query) {

		$re = mysql_query($query);
		return $re;
	}
	
	function canOpenRoom($start_time) {
		return true;
	}
	
	function getRoomMaxParticipants($id_room)
	{
		list($max_participants) = mysql_fetch_row(mysql_query(	"SELECT maxparticipants"
																." FROM ".$this->_getRoomTable().""
																." WHERE id = '".$id_room."'"));
		
		return $max_participants;
	}
	
	function insert_room($idCourse,$idSt,$name,$room_type,$start_timestamp,$end_timestamp,$meetinghours,$maxparticipants) {
		
		//save in calendar the corresponding event
		
		$start_date = date("Y-m-d H:i:s", $start_timestamp);
		$end_date = date("Y-m-d H:i:s", $end_timestamp);
		
		ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$start_date,$parts);
		
					
		$event=new DoceboCalEvent_lms();
		$event->calEventClass="lms";
		$event->start_year=$parts[1];
		$event->start_month=$parts[2];
		$event->start_day=$parts[3];
		
		$event->_year=$event->start_year;
		$event->_month=$event->start_month;
		$event->_day=$event->start_day;
		
		$event->start_hour=$parts[4];
		$event->start_min=$parts[5];
		$event->start_sec=$parts[6];
		
		ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$end_date,$parts);
		
		$event->end_year=$parts[1];
		$event->end_month=$parts[2];
		$event->end_day=$parts[3];
		
		$event->end_hour=$parts[4];
		$event->end_min=$parts[5];
		$event->end_sec=$parts[6];
		
		$event->title=$name;
		$event->description=$name;
	
		$event->_owner=$idSt;
		if (!$event->_owner) $event->_owner==$GLOBALS["current_user"]->getIdSt();
		
		$event->category="b";
		$event->private="";
		$event->idCourse=$idCourse;
		
		$idCal=$event->store();
		
		//save in database the roomid for user login
		$insert_room = "
		INSERT INTO ".$this->_getRoomTable()."
		( idCal,idCourse,idSt,name, room_type, starttime,endtime,meetinghours,maxparticipants) VALUES (
			'".$idCal."',
			'".$idCourse."',
			'".$idSt."',
			'".$name."', 
			'".$room_type."',
			'".$start_timestamp."',
			'".$end_timestamp."',
			'".$meetinghours."',
			'".$maxparticipants."'
		)";
		
		$id_room="";
		$ok=true;
		if(!mysql_query($insert_room)) {$ok=false;}
		if ($ok) $idConference=mysql_insert_id();

		if ($ok) {
			switch($room_type) {
				case "dimdim":
					$acl_manager =& $GLOBALS['current_user']->getAclManager();
					$dimdim = new DimDim_Manager();
					$display_name = $GLOBALS['current_user']->getUserName();
					$u_info = $acl_manager->getUser(getLogUserId(), false);
					$user_email=$u_info[ACL_INFO_EMAIL];
					$confkey = $dimdim->generateConfKey();
					$audiovideosettings=1;	
					$maxmikes=(int)$GLOBALS["scs"]["dimdim_max_mikes"];	
					$dimdim->insert_room($idConference,$user_email,$display_name,$confkey,$audiovideosettings,$maxmikes);
					break;
				
				case "teleskill":
					$start_date = date("Y-m-d H:i:s", $start_timestamp);
					$end_date = date("Y-m-d H:i:s", $end_timestamp);
					$teleskill = new Teleskill_Management();
					$re_creation_room=$teleskill->openRoom($idConference,$name, $start_date,$end_date, FALSE, FALSE,$maxparticipants);
					break;
			}
		}
		
		return $idConference;
	}
	
	function roomInfo($room_id) {

		$room_open = "
		SELECT id,idCal,idCourse,idSt,name,room_type,starttime,endtime,meetinghours,maxparticipants
		FROM ".$this->_getRoomTable()."
		WHERE id = '".$room_id."'";
		$re_room = $this->_query($room_open);

		return $this->nextRow($re_room);
	}
	
	function roomActive($idCourse, $at_date = false) {

		$room_open = "
		SELECT id,idCourse,idSt,name,room_type,starttime,endtime,meetinghours,maxparticipants
		FROM ".$this->_getRoomTable()."
		WHERE idCourse = '".$idCourse."'";
		
		if ($at_date !== false) {
			$room_open .= " AND endtime >= '".$at_date."'";
		}
		
		$room_open .= " ORDER BY starttime";
		
		$re_room = $this->_query($room_open);
			
		return $re_room;
	}
	

	function totalRoom($re_room) {
		
		return mysql_num_rows($re_room);
	}
	
	function nextRow($re_room) {

		return mysql_fetch_array($re_room);
	}
	
	function deleteRoom($room_id) {
		$conference = $this->roomInfo($room_id);
		
		$room_del = "
		DELETE FROM ".$this->_getRoomTable()."
		WHERE id = '".$room_id."'";
		$re_room = $this->_query($room_del);
		
		$event=new DoceboCalEvent_lms();
		$event->id=$conference["idCal"];
		$event->del();
			
		switch ($conference["room_type"]) {
			case "dimdim":
				$dimdim=new DimDim_Manager();
				$dimdim->deleteRoom($room_id);
				break;
			
			case "teleskill":
				$teleskill = new Teleskill_Management();
				$teleskill->deleteRemoteRoom($room_id);
				break;
		}
		return $re_room;
	}
	
	function getUrl($idConference,$room_type) {
		$conference = $this->roomInfo($idConference);
		
		switch($room_type) {
			case "dimdim":
				$dimdim=new DimDim_Manager();
				$url=$dimdim->getUrl($idConference,$room_type);
				break;
				
			case "teleskill":
				$teleskill = new Teleskill_Management();
				$url=$teleskill->getUrl($idConference,$room_type);
				break;
		}
	
		return $url;
	}
	
	function can_create_user_limit($idSt,$idCourse,$start_timestamp) {
		$ok=true;
		
		if ($this->creation_limit_per_user) {
			$query="SELECT * FROM  ".$this->_getRoomTable().
			" WHERE idSt='$idSt' AND idCourse='$idCourse' AND starttime<='$start_timestamp'";
			$re_room=$this->_query($query);
			$p=mysql_error();
			$n_room=$this->totalRoom($re_room);
			
			if ($n_room >= $this->creation_limit_per_user) {
				$ok=false;
			}
		};
		
		return $ok;
	}
	
	function can_create_room_limit($idSt,$idCourse,$room_type,$start_timestamp,$end_timestamp) {
		$ok=true;
		
		$room_limit=$GLOBALS['scs'][$room_type.'_max_room'];
		
		$query="SELECT * FROM  ".$this->_getRoomTable().
		" WHERE room_type='$room_type' AND idCourse='$idCourse' AND starttime<='$end_timestamp' AND endtime>='$start_timestamp'";
		$re_room=$this->_query($query);
		$n_room=$this->totalRoom($re_room);
		if ($n_room >= $room_limit) {
			die('test '.$room_limit);
			$ok=false;
		}
		
		return $ok;
	}
}

?>