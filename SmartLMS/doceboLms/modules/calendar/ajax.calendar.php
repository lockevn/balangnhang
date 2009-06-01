<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

//load calendar core classes - extend'em for other type of events
require_once($GLOBALS['where_framework']."/lib/lib.calendar_core.php");
require_once($GLOBALS['where_framework']."/lib/lib.calevent_core.php");

$op = get_req('op', DOTY_ALPHANUM, '');
$calClass = get_req('calClass', DOTY_MIXED, '');

switch ($op) {
	case "get": {
		$month=get_req('month', DOTY_INT);
		$year=get_req('year', DOTY_INT);

		if (!$month and !$year) {
			$today=getdate();
			$year=$today['year'];
 			$month=$today['mon'];
			$day=$today['mday'];
		};

		$m0=$month-2;
		$y0=$year;
		if ($m0<1) {
			$y0--;
			$m0=10;
		};

		$start_date="$y0-$m0-01 00:00:00";
		$end_date="$year-$month-31 23:59:59";

		if ($calClass<>"core")
		require_once($GLOBALS['where_framework']."/lib/lib.calendar_".$calClass.".php");

		$class="DoceboCal_".$calClass;
		$cal = new $class();
		
		if ($calClass=="lms_classroom") {
			$classroom=get_req('classroom');
			$eventlist=$cal->getEvents(0,0,0,$start_date,$end_date,$classroom);
		} else {
			$eventlist=$cal->getEvents(0,0,0,$start_date,$end_date);
		};
		
		$json=new Services_JSON();
		$calEvents=$json->encode($eventlist);
		docebo_cout($calEvents);
	};break;

	case "set": {
		
		$index=get_req("index");
		$calEventClass=get_req("calEventClass");

		if ($calEventClass<>"core")
		require_once($GLOBALS['where_framework']."/lib/lib.calevent_".$calEventClass.".php");

		$class="DoceboCalEvent_".$calEventClass;
		$event=new $class();

		$event->assignVar();
		if($event->store()) {

			$id=$event->id;
	
			docebo_cout("{\"index\":\"$index\",\"id\":\"$id\"}");
		} else {
		
			$result = array();
			$result['index'] = $index;
			$result['error'] = 1;
			$result['errormsg'] = def('_NOT_FREE', 'classevent', 'lms');
			$json = new Services_JSON();
			$result_coded = $json->encode($result);
		
			docebo_cout($result_coded);
		}
		
	};break;

	case "del": {
		$id=get_req("id", DOTY_INT);
		$calEventClass=get_req("calEventClass");

		if ($calEventClass<>"core")
		require_once($GLOBALS['where_framework']."/lib/lib.calevent_".$calEventClass.".php");

		$class="DoceboCalEvent_".$calEventClass;
		$event=new $class();

		//$event->id=$id;
		//$event->_owner=$event->getOwner();

		$event->assignVar();
		
		$event->del();

		docebo_cout("{\"result\":\"1\"}");
	};break;

	case "getForm": {
		$calEventClass=get_req("calEventClass");

		if ($calEventClass<>"core")
		require_once($GLOBALS['where_framework']."/lib/lib.calevent_".$calEventClass.".php");

		$class="DoceboCalEvent_".$calEventClass;
		$event=new $class();

		$form=$event->getForm();

		docebo_cout($form);
	};break;


	case "getLang": {
		$lang =& DoceboLanguage::createInstance( 'calendar', 'lms');

		$lang_obj='{
		"_DN":["'.$lang->def('_SUNDAY').'","'.$lang->def('_MONDAY').'","'.$lang->def('_TUESDAY').'","'.$lang->def('_WEDNESDAY').'","'.$lang->def('_THURSDAY').'","'.$lang->def('_FRIDAY').'","'.$lang->def('_SATURDAY').'","'.$lang->def('_SUNDAY').'"],
		"_SDN":["'.$lang->def('_SUN').'","'.$lang->def('_MON').'","'.$lang->def('_TUE').'","'.$lang->def('_WED').'","'.$lang->def('_THU').'","'.$lang->def('_FRI').'","'.$lang->def('_SAT').'","'.$lang->def('_SUN').'"],
		"_MN":["'.$lang->def('_JANUARY').'","'.$lang->def('_FEBRUARY').'","'.$lang->def('_MARCH').'","'.$lang->def('_APRIL').'","'.$lang->def('_MAY').'","'.$lang->def('_JUNE').'","'.$lang->def('_JULY').'","'.$lang->def('_AUGUST').'","'.$lang->def('_SEPTEMBER').'","'.$lang->def('_OCTOBER').'","'.$lang->def('_NOVEMBER').'","'.$lang->def('_DECEMBER').'"],
		"_SMN":["'.$lang->def('_JAN').'","'.$lang->def('_FEB').'","'.$lang->def('_MAR').'","'.$lang->def('_APR').'","'.$lang->def('_MAY').'","'.$lang->def('_JUN').'","'.$lang->def('_JUL').'","'.$lang->def('_AUG').'","'.$lang->def('_SEP').'","'.$lang->def('_OCT').'","'.$lang->def('_NOV').'","'.$lang->def('_DEC').'"],
		"_PREV_YEAR":"'.$lang->def('_PREV_YEAR').'",
		"_PREV_MONTH":"'.$lang->def('_PREV_MONTH').'",
		"_GO_TODAY":"'.$lang->def('_GO_TODAY').'",
		"_NEXT_MONTH":"'.$lang->def('_NEXT_MONTH').'",
		"_NEXT_YEAR":"'.$lang->def('_NEXT_YEAR').'",
		"_CAL_TITLE":"'.$lang->def('_CAL_TITLE').'",
		"_PART_TODAY":"'.$lang->def('_PART_TODAY').'",
		"_DAY_FIRST":"'.$lang->def('_DAY_FIRST').'",
		"_WEEKEND":"'.$lang->def('_WEEKEND').'",
		"_TODAY":"'.$lang->def('_TODAY').'",
		"_DEF_DATE_FORMAT":"'.$lang->def('_DEF_DATE_FORMAT').'",
		"_TT_DATE_FORMAT":"'.$lang->def('_TT_DATE_FORMAT').'",
		"_WK":"'.$lang->def('_WK').'",
		"_TIME":"'.$lang->def('_TIME').'",
		"_CLOSE":"'.$lang->def('_CLOSE').'",
		"_START":"'.$lang->def('_START').'",
		"_END":"'.$lang->def('_END').'",
		"_SUBJECT":"'.$lang->def('_TITLE').'",
		"_DESCR":"'.$lang->def('_DESCRIPTION').'",
		"_SAVE":"'.$lang->def('_SAVE').'",
		"_DEL":"'.$lang->def('_DEL').'",
		"_NOTITLE":"'.$lang->def('_NOTITLE').'",
		"_NEW_EVENT":"'.$lang->def('_NEW_EVENT').'",
		"_EDT_EVENT":"'.$lang->def('_EDT_EVENT').'",
		"_ERR_START":"'.$lang->def('_ERR_START').'",
		"_ERR_END":"'.$lang->def('_ERR_END').'",
		"_ERR_DATES":"'.$lang->def('_ERR_DATES').'",
		"_PLS_WAIT":"'.$lang->def('_PLS_WAIT').'",
		"_DEL_EVENT":"'.$lang->def('_DEL').'",
		"_DEL_CONFIRM":"'.$lang->def('_DEL_CONFIRM').'",
		"_CATEGORY":"'.$lang->def('_CATEGORY').'",
		"_EVENT":"'.$lang->def('_EVENT').'",
		"_YES":"'.$lang->def('_YES').'",
		"_NO":"'.$lang->def('_NO').'",

		"_PRIVATE":"'.$lang->def('_PRIVATE').'",
		"_GENERIC":"'.$lang->def('_GENERIC').'",
		"_VIDEOCONFERENCE":"'.$lang->def('_VIDEOCONFERENCE').'",
		"_MEETING":"'.$lang->def('_MEETING').'",
		"_CHAT":"'.$lang->def('_CHAT').'",
		"_PUBLISHING":"'.$lang->def('_PUBLISHING').'",
		"_ASSESSMENT":"'.$lang->def('_ASSESSMENT').'"
		}';

		docebo_cout($lang_obj);
	};break;
}

?>