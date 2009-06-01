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

require_once($GLOBALS["where_crm"]."/modules/ticket/lib.ticket.php");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=ticket&pi=".getPI()."&op=main");
// -----------------------

addCss("style_crm");


function ticketGetMainPage() {
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$ctm=new CustomerTicketManager();
	$cm=new CompanyManager();


	$company_arr=$cm->getUserCompanies($GLOBALS["current_user"]->getIdSt());
	$ctm->setTicketCompany($company_arr);


	$tot=count($company_arr);

	if ($tot == 1) {
		$company_id=$ctm->getCurrentCompanyId();
		$out->add($ctm->showCompanyTicket($company_id));
	}
	else if ($tot > 1) {
		$out->add($ctm->showCompanySelect());
	}

}


function showCompanyTicket() {
	$res="";

	$ctm=new CustomerTicketManager();

	$company_id=$ctm->getCurrentCompanyId();
	$res.=$ctm->showCompanyTicket($company_id);

	return $res;
}


function addTicket() {

	$res="";

	$ctm=new CustomerTicketManager();

	$res.=$ctm->addTicket();

	return $res;
}


function showTicket() {
	$res="";

	$ctm=new CustomerTicketManager();
	$res.=$ctm->showTicket();

	return $res;
}


function addeditMessage() {
	$res="";

	$ctm=new CustomerTicketManager();
	$res.=$ctm->addeditMessage();

	return $res;
}


function saveTicketMessage() {
	$ctm=new CustomerTicketManager();
	$ctm->saveTicketMessage();
}

function setTicketOrder() {
	$ctm=new CustomerTicketManager();
	$ctm->setTicketOrder();
}

?>
