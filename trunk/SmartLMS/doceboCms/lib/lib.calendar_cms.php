<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_cms']."/lib/lib.calevent_cms.php");

class DoceboCal_cms extends DoceboCal_core{

	function getEvents($year=0,$month=0,$day=0,$start_date="",$end_date="", $cal_id) {

		$where="";

		if (!$month and !$year and empty($start_date) and empty($end_date)) {
			$today=getdate();
			$month=$today['mon'];
			$year=$today['year'];
		}

		if ($day and empty($start_date) and empty($end_date)) {
			if ($where) $where.=" AND ";
			$where.="_day='".$day."'";

		}

		if ($month and empty($start_date) and empty($end_date)) {
			if ($where) $where.=" AND ";
			$where.="_month='".$month."'";

		}

		if ($year and empty($start_date) and empty($end_date)) {
			if ($where) $where.=" AND ";
			$where.="_year='".$year."'";

		}

		if (!empty($start_date) and !empty($end_date)) {
			if ($where) $where.=" AND ";
			$where.="start_date>='".$start_date."' AND start_date<='".$end_date."'";
		}


		$query="SELECT a.*,b.calendar_id FROM ".$GLOBALS['prefix_fw']."_calendar AS a,".$GLOBALS['prefix_cms']."_calendar_item AS b WHERE a.id=b.event_id AND (a.private<>'on' OR (a.private='on' AND a._owner='".$GLOBALS['current_user']->getIdSt()."')) AND b.calendar_id='".$cal_id."' AND ".$where." ORDER BY start_date";

		$result=mysql_query($query);
		
		$calevents = array();
		$i=0;
		while ($row=mysql_fetch_array($result)) {

			/* you should call the constructor of the proper type of event class*/
			$calevents[$i]=new DoceboCalEvent_cms();
			$calevents[$i]->calEventClass="cms";

			/* the following should be set according to the type of event class*/
			$calevents[$i]->id=$row["id"];
			ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$row["start_date"],$parts);
			$calevents[$i]->start_year=$parts[1];
			$calevents[$i]->start_month=$parts[2];
			$calevents[$i]->start_day=$parts[3];
			$calevents[$i]->start_hour=$parts[4];
			$calevents[$i]->start_min=$parts[5];
			$calevents[$i]->start_sec=$parts[6];

			ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$row["end_date"],$parts);
			$calevents[$i]->end_year=$parts[1];
			$calevents[$i]->end_month=$parts[2];
			$calevents[$i]->end_day=$parts[3];
			$calevents[$i]->end_hour=$parts[4];
			$calevents[$i]->end_min=$parts[5];
			$calevents[$i]->end_sec=$parts[6];

			$calevents[$i]->title=htmlentities($row["title"]);
			$calevents[$i]->description=htmlentities($row["description"]);
			$calevents[$i]->category=$row["category"];
			$calevents[$i]->type=$row["type"];
			$calevents[$i]->private=$row["private"];
			$calevents[$i]->visibility_rules=$row["visibility_rules"];

			$calevents[$i]->_year=$row["_year"];
			$calevents[$i]->_month=$row["_month"];
			$calevents[$i]->_day=$row["_day"];
			$calevents[$i]->_owner=$row["_owner"];

			$calevents[$i]->cal_id=$row["calendar_id"]; // Set cal_id before call getPerm() !
			$calevents[$i]->editable=$calevents[$i]->getPerm();
			/*----------------------------------------------------------*/

			$i++;
		}
		return $calevents;
	}
}

?>