<?php 

/************************************************************************/
/* DOCEBO Ecom - E-commerce System										*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package Payment
 * @author 	Demarinis Claudio (claudiodema@docebo.com)
 * @version $Id: check.php
 * 
 **/

	/* get info about whe wire stransfer settings
	* @return array an array with check payment info
	*/

function getCheckInfo () {
	
	$query_setting = "
	SELECT param_name,param_value
	FROM ".$GLOBALS['prefix_ecom']."_payaccount_setting
	WHERE account_name = 'check'" ;
	$re_query_setting=mysql_query($query_setting);
	while(list($param_name,$param_value)=mysql_fetch_row($re_query_setting)){
		$check_setting[$param_name]=$param_value;
	}
	return $check_setting;
}


?>