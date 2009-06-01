<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function categoryDispatch($op, &$treeView) {
	switch($op) {
		case "newfolder" : {
			 $GLOBALS['page']->add($treeView->loadNewFolder(), 'content');
		};break;
		case "renamefolder" : {
			$GLOBALS['page']->add($treeView->loadRenameFolder(), 'content');
		};break;
		case "movefolder" : {
			$GLOBALS['page']->add($treeView->loadMoveFolder(), 'content');
		};break;
		case "deletefolder" : {
			$GLOBALS['page']->add($treeView->loadDeleteFolder(), 'content');
		};break;
	}
}

?>