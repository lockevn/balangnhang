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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

define("_PATH_MESSAGE", getPLSetting("lms", 'pathmessage'));
define("_MESSAGE_VISU_ITEM", $GLOBALS['cms']['visuItem']);
define("_MESSAGE_PL_URL", $GLOBALS['cms']['url']);

addCss("style", "framework", false, true);
addCss("style_message", "framework");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=message&pi=".getPI()."&op=message", FALSE, "message");
// -----------------------

?>