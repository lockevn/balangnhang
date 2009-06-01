<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

$reSetting = mysql_query("
SELECT param_name, param_value, value_type
FROM ".$GLOBALS['prefix_fw']."_setting
WHERE param_load = '1'", $GLOBALS['dbConn']);

while(list($var_name, $var_value, $value_type) = mysql_fetch_row($reSetting)) {

	switch( $value_type ) {
		//if is int cast it
		case "int" : {
			$$var_name = (int)$var_value;
			$GLOBALS['framework'][$var_name] = (int)$var_value;
		};break;
		//if is enum switch value to on or off
		case "enum" : {
			if( $var_value == 'on' ) {
				$$var_name = 'on';
				$GLOBALS['framework'][$var_name] = 'on';
			} else {
				$$var_name = 'off';
				$GLOBALS['framework'][$var_name] = 'off';
			}
		};break;
		//else simple assignament
		default : {
			$$var_name = $var_value;
			$GLOBALS['framework'][$var_name] = $var_value;
		}
	}
}
mysql_free_result($reSetting);

$GLOBALS['title_page'] = 'DoceboCore '.$core_version;

?>