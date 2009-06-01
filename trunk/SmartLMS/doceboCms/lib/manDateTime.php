<?php

/*************************************************************************/
/* DOCEBO - Content Management System                                    */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


define("_DMY_FORMAT", "d m Y");
define("_MDY_FORMAT", "m d Y");
define("_YMD_FORMAT", "Y m d");


/*************************************************************************/


if (strtolower(getLanguage()) == "italian")
	define(_LANGCODE, "it");
else
	define(_LANGCODE, "en");



function init_calendar() {

	$lang=_LANGCODE;

	echo("<script type=\"text/javascript\" src=\"core/extra/calendar/calendar.js\"></script>\n");
	echo("<script type=\"text/javascript\" src=\"core/extra/calendar/lang/calendar-$lang.js\"></script>\n");
	echo("<script type=\"text/javascript\" src=\"core/extra/calendar/calendar-setup.js\"></script>\n");

}



function make_cal($val="", $ext="") {

	//echo("<input class=\"textfield\" type=\"text\" id=\"date$ext\" name=\"date$ext\" value=\"$val\" readonly=\"readonly\" />\n");
	echo("<input class=\"textfield\" type=\"text\" id=\"date$ext\" name=\"date$ext\" value=\"$val\" />\n");
	echo("<button id=\"trigger$ext\" name=\"trigger$ext\" class=\"calbtn\"></button>\n");

}



function setup_cal($ext="") {

	$dformat=df_str(_DATEFORMAT, _DATESEP, "%");

	echo("<script type=\"text/javascript\">\n");
	echo("Calendar.setup(\n");
	echo("{\n");
	echo("ifFormat    : \"".$dformat."\",\n");
	echo("inputField  : \"date$ext\",\n");
	echo("button      : \"trigger$ext\"\n");
	echo("}\n");
	echo(");\n");
	echo("</script>\n");

}



function time_select($sel="", $ext="") {

	$tarr=explode(":", $sel);

	echo("<select id=\"hour$ext\" name=\"hour$ext\">\n");
	for ($i=0; $i<24; $i++) {
		$val=leading_zero($i, 2);
		if (_TIMEFORMAT == 24) $val_lbl=$val;
		else if (_TIMEFORMAT == 12) {
			if ($i<12) $val_lbl=leading_zero($i+1, 2)." AM";
			else $val_lbl=leading_zero($i-11, 2)." PM";
			$val=leading_zero($val+1, 2);
			if ($val == 24) $val="00";
		}
		if ($val == $tarr[0]) $sel=" selected=\"selected\""; else $sel="";
		echo("<option value=\"$val\"$sel>$val_lbl</option>\n");
	}
	echo("</select>\n");

	echo(":&nbsp;");

	echo("<select id=\"min$ext\" name=\"min$ext\">\n");
	for ($i=0; $i<60; $i++) {
		if ($i == $tarr[1]) $sel=" selected=\"selected\""; else $sel="";
		$val=leading_zero($i, 2);
		echo("<option value=\"$val\"$sel>$val</option>\n");
	}
	echo("</select>\n");

}



function leading_zero( $aNumber, $intPart, $floatPart=NULL, $dec_point=NULL, $thousands_sep=NULL) {        //Note: The $thousands_sep has no real function because it will be "disturbed" by plain leading zeros -> the main goal of the function
  $formattedNumber = $aNumber;
  if (!is_null($floatPart)) {    //without 3rd parameters the "float part" of the float shouldn't be touched
   $formattedNumber = number_format($formattedNumber, $floatPart, $dec_point, $thousands_sep);
   }
  //if ($intPart > floor(log10($formattedNumber)))
  if ((int)$aNumber != 0) {
		$formattedNumber = str_repeat("0",($intPart + -1 - floor(log10($formattedNumber)))).$formattedNumber;
		return $formattedNumber;
	}
	else {
		$res="";
		for ($i=0; $i<$intPart; $i++) {
			$res.="0";
		}
		return $res;
	}
}



function get_timestamp($date, $time) {


	$date=date_convert($date, _DATEFORMAT, _MDY_FORMAT);

	$tarr=explode(":", $time);
	$darr=explode(_DATESEP, $date);

	if ((is_array($tarr)) && (is_array($darr)))
		return (int)mktime($tarr[0], $tarr[1], $tarr[2], $darr[0], $darr[1], $darr[2]);
	else
		return 0;
}



function set_from_timestamp($ts, &$date, &$time, $seconds=0, $tformat="") {

	if ($tformat == "") $tformat=_TIMEFORMAT;

	$dfstr=df_str(_DATEFORMAT, _DATESEP);

	$date=date($dfstr, $ts);

	if     ($tformat == 24) { $h="H"; $a=""; }
	elseif ($tformat == 12) { $h="h"; $a=" A"; }

	if (!$seconds)
		$time=date($h.":i".$a, $ts);
	else
		$time=date($h.":i:s".$a, $ts);
}


function date_convert($date, $from, $to, $fsep="", $tsep="") {

	if ($fsep == "") $fsep=_DATESEP;
	if ($tsep == "") $tsep=_DATESEP;

	$darr=explode($fsep, $date);
	$fa=explode(" ", strtolower($from));
	$ta=explode(" ", strtolower($to));

	foreach ($fa as $key=>$val) { // ;)
		$res[$val]=$darr[$key];
	}

	//print_r($res); //if you want to see the light, uncomment this ;)
	return $res[$ta[0]].$tsep.$res[$ta[1]].$tsep.$res[$ta[2]];
}



function df_str($format, $sep, $pre="") {

	$res="";
	$dfa=explode(" ", $format);
	$count=count($dfa);

	$i=0;
	foreach ($dfa as $key=>$val) {
		$res.=$pre.$val;
		$i++;
		if ($i < $count) $res.=$sep;
	}

	return $res;
}




function conv_datetime($dt, $seconds=0, $offset=0, $what="both") {
	// $dt: "2004-11-05 16:31:28"

	if ((defined(_TIMEOFFSET)) && ($offset == 0)) $offset=_TIMEOFFSET;

	$dtarr=explode(" ", $dt);
	$date=date_convert($dtarr[0], _YMD_FORMAT, _DATEFORMAT, $fsep="-");
	$ts=get_timestamp($date, $dtarr[1])+$offset*3600;

	set_from_timestamp($ts, $date, $time, $seconds);

	switch ($what) {
		case "both": {
			return $date." ".$time;
		} break;
		case "date": {
			return $date;
		} break;
		case "time": {
			return $time;
		} break;
	}
}


?>
