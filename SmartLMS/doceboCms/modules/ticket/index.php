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

$css=getModuleCss($GLOBALS["pb"]);
$GLOBALS["page"]->add("<div class=\"".$css."\">\n", "content");
$GLOBALS["page"]->add(getModuleBlockTitle($GLOBALS["pb"]), "content");


$out=& $GLOBALS['page'];
$out->setWorkingZone('content');

$out->add("<div class=\"body_block\">\n");


$res="";
$op=importVar("op");
switch ($op) {

	case "main": {
		ticketGetMainPage();
	} break;

	case "ticket": {
		$res.=showCompanyTicket();
	} break;

	case "addticket": {
		$res.=addTicket();
	} break;

	case "createticket": {
		$ctm=new CustomerTicketManager();
		$ctm->createTicket();
	} break;

	case "showticket": {
		$res.=showTicket();
	} break;

	case "addticketreply":
	case "editticketmsg": {
		$res.=addeditMessage();
	} break;

	case "saveticketmsg": {
		saveTicketMessage();
	} break;

	case "ticketsetorder": {
		setTicketOrder();
	} break;

}

$out->add($res);


$out->add("</div>\n"); // body_block
$GLOBALS["page"]->add("</div>\n", "content"); // getModuleCss


?>