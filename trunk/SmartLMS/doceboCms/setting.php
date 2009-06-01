<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


/*Loading platform's standard preferences*********************************/

$reSetting = mysql_query("
SELECT param_name, param_value, value_type
FROM ".$GLOBALS["prefix_cms"]."_setting
WHERE param_load = '1'");

while(list( $var_name, $var_value, $value_type ) = mysql_fetch_row( $reSetting ) ) {

	switch( $value_type ) {
		//if is int cast it
		case "int" : {
			$GLOBALS["cms"][$var_name]=(int)$var_value;
		};break;
		//if is enum switch value to on or off
		case "enum" : {
			if( $var_value == 'on' ) $GLOBALS["cms"][$var_name]='on';
			else $GLOBALS["cms"][$var_name]='off';
		};break;
		//else simple assignament
		default : {
			$GLOBALS["cms"][$var_name]=$var_value;
		}
	}

	//echo("$"."GLOBALS[cms][".$var_name."] = ".$var_value."<br />\n");
}
mysql_free_result($reSetting);

//require_once($elearningPosition."setting.php");



//-------------------------------------------------------------------------[OLD]
/*Platform's user preferences*********************************************/

/*list(
 $urlCms
,$ttlCmsSession

,$defaultCmsTemplate
,$defaultCmsLanguage
,$over_menu

,$visuUserAtTime
,$visuItem

) = mysql_fetch_row(mysql_query("
SELECT
	urlsite,
	ttlSession,

	default_template,
	default_language,
	over_menu

	visu_user_at_time,
	visu_item
FROM ".$prefixCms."_config"));*/
//-------------------------------------------------------------------------[/OLD]


?>