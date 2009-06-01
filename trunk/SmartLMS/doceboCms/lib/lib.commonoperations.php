<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function cmsLoginOperation() {
	require_once($GLOBALS["where_cms"]."/lib/lib.cms_common.php");

	unsetBlockInfo();
}


function cmsLogoutOperation() {
	require_once($GLOBALS["where_cms"]."/lib/lib.cms_common.php");

	unsetBlockInfo();

}


?>
