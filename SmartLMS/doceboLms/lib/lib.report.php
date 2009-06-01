<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system									*/
/* ============================================							*/
/*																			*/
/* Copyright (c) 2005														*/
/* http://www.docebo.com													*/
/*																			*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


function load_categories() {
  $res = mysql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_report WHERE enabled=1");
  $GLOBALS['report_categories'] = array();
  while ($row = mysql_fetch_assoc($res)) {
    $GLOBALS['report_categories'][ $row['id_report'] ] = $row['report_name'];
  }
}


function report_save($report_id, $filter_name, &$filter_data) {
	$data = serialize($filter_data); //put serialized data in DB
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_filter ".
		"(id_report, author, creation_date, filter_data, filter_name) VALUES ".
		"($report_id, ".getLogUserId().", NOW(), '$data', '$filter_name')";
		
	if (!mysql_query($query)) {
		return false;
	} else {
		$row = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		return $row[0];
	}
}


function report_update($report_id, $filter_name, &$filter_data) {
	$data = serialize($filter_data); //put serialized data in DB
	$query = "UPDATE ".$GLOBALS['prefix_lms']."_report_filter SET ".
		//"id_report=$report_id, author=".getLogUserId().", ".
		"creation_date=NOW(), filter_data='$data', filter_name='$filter_name' ".
		"WHERE id_filter=$report_id";
		
	return mysql_query($query);
}


function report_save_schedulation($id_rep, $name, $period, $time, &$recipients) {
	//TO DO : try to use transation for this
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_schedule ".
		"(id_report_filter, id_creator, name, period, time, creation_date) VALUES ".
		"($id_rep, ".getLogUserId().",'".trim($name)."', '$period', '$time', NOW())";
	
	if (!mysql_query($query)) {
		return false;
	} else {
		$row = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		$id_sched = $row[0];
	}
	
	$temp = array();
	foreach ($recipients as $value) {
		$temp[] = '('.$id_sched.', '.$value.')';
	}
	
	//TO DO : handle void recipients case
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_schedule_recipient ".
		"(id_report_schedule, id_user) VALUES ".implode(',', $temp);
		
	if (!mysql_query($query))
		return false;
	else
		return $id_sched;
}


function getReportNameById($id) {
	$qry = "SELECT filter_name, author FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$id";
	$row = mysql_fetch_row( mysql_query($qry) );
	
	if($row[1])
		return $row[0];
	else
	{
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		return $lang->def($row[0]);
	}
}

function getScheduleNameById($id) {
	$qry = "SELECT name FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_report_schedule=$id";
	$row = mysql_fetch_row( mysql_query($qry) );	
	return $row[0];
}

function report_delete_filter($id_filter) {
	$qry = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$id_filter"; 
	$output = mysql_query($qry);
	if ($output) {
		//delete schedulations connected to this filter
		$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_filter=$id_filter";
		$res = mysql_query($qry);
		while ($row = mysql_fetch_assoc($res)) {
			$output = report_delete_scheduletion($row['id_report_schedule']);
		}
	}
	return $output;
}

function report_delete_schedulation($id_sched) {
	//delete row from report_schedule table and recipients row
	$output = false;
	$qry = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_report_schedule=$id_sched";
	if ($output = mysql_query($qry)) {
		$qry2 = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipients WHERE id_report_schedule=$id_sched";
		$output = mysql_query($qry2);		
	}
	return $output;
}


function report_update_schedulation($id_sched, $name, $period, $time, &$recipients) {
	$output = true;
	$qry = "UPDATE ".$GLOBALS['prefix_lms']."_report_schedule ".
		"SET name='$name', period='$period' ".
		"WHERE id_report_schedule=$id_sched";

	if ($output = mysql_query($qry)) {
		$qry2 = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient WHERE id_report_schedule=$id_sched";
		if ($output = mysql_query($qry2)) {
			//delete old recipients and replace with new ones
			$temp = array();
			foreach ($recipients as $value) {
				$temp[] = '('.$id_sched.', '.$value.')';
			}		
			$qry3 = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_schedule_recipient ".
				"(id_report_schedule, id_user) VALUES ".implode(',', $temp);
			$output &= mysql_query($qry3);
			echo $qry3.'<br/>';
		} else echo($qry2); //return false;
	} else echo($qry); //return false;

	return $output;
}


function get_schedule_recipients($id_sched, $names=false) {
	$acl_man =& $GLOBALS['current_user']->getACLManager();
	$qry = "SELECT t2.userid, t2.firstname, t2.lastname ".
		"FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient as t1, ".
		$GLOBALS['prefix_fw']."_user as t2 WHERE t2.idst=t1.id_user AND ".
		"t1.id_report_schedule=$id_sched ORDER BY userid";
	if ($res = mysql_query($qry)) {
		$output = array();
		while ($row = mysql_fetch_assoc($res)) {
			if ($names) {
				$row['userid'] = $acl_man->relativeId($row['userid']);
				$temp = $row;
			} else {
				$temp = $acl_man->relativeId($row['userid']);
			}
			$output[] = $temp;
		}
		return $output;
	} else return false;
}


?>