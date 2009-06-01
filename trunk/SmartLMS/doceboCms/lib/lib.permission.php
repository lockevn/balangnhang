<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function checkPerm($mn, $mode, $return_value = false) {

	switch($mode) {
		default:  $suff = $mode;
	}

	$role = '/'.$GLOBALS['platform'].'/'.$mn.'/'.$suff;

	if($GLOBALS['current_user']->matchUserRole($role)) {

		return true;
	} else {

		if($return_value) return false;
		else die("$role You can't access");
	}
}

?>
