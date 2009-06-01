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
 * @version $Id: wire_transfer.php
 * 
 **/

	/* get info about whe wire stransfer settings
	* @return array 	an array with wire transfer payment info
	*/

function getWireTransferInfo () {
	
	$query_setting = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_ecom']."_payaccount_setting
	WHERE account_name = 'wire_transfer'" ;
	
	$wire_transfer_setting = mysql_fetch_assoc(mysql_query($query_setting));
	return $wire_transfer_setting;
}


?>