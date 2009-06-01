<?php
/*************************************************************************/
/* DOCEBO ECOM                                                           */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS["where_ecom"]."/admin/modules/reservation/lib.reservation.php");


function raSetup() {
	if (!isset($GLOBALS["reservation_admin"]))
		$GLOBALS["reservation_admin"]=new ReservationAdmin();

	$ra=& $GLOBALS["reservation_admin"];
	$ra->urlManagerSetup("modname=reservation&op=main");
}


function reservationMain() {
	checkPerm("view");

	$res="";
	raSetup();
	$ra=& $GLOBALS["reservation_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/company", "buyer");
	$role_info=$roles["role_info"];

	if (count($role_info) == 1) {
		$res=companyReservation(current($role_info));
		$out->add($res);
	}
	else {

		$title=$ra->lang->def("_RESERVATION_APPROVAL");
		$res.=$ra->titleArea($title);
		$res.=$ra->getHead();

		$vis_item=20; //$GLOBALS["ecom"]["visuItem"];
		$res.=$ra->getBuyerCompaniesTable($vis_item, $roles["role_info"]);

		$res.=$ra->getFooter();
		$out->add($res);
	}
}


function companyReservation($company_id=FALSE) {
	checkPerm("view");

	if ($company_id === FALSE) {
		if ((isset($_GET["company_id"])) && ($_GET["company_id"] > 0)) {
			$company_id=(int)$_GET["company_id"];
		}
		else {
			return FALSE;
		}
	}

	$res="";
	raSetup();
	$ra=& $GLOBALS["reservation_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$ra->lang->def("_RESERVATION_APPROVAL");
	$res.=$ra->titleArea($title);
	$res.=$ra->getHead();

	$res.=$ra->getReservationListTable($company_id);

	$res.=$ra->getFooter();
	$out->add($res);
}


function updateReservation() {
	checkPerm("view");

	raSetup();
	$ra=& $GLOBALS["reservation_admin"];

	$ra->updateReservation($_POST);
}


function buyReservation() {
	checkPerm("view");

	if ((isset($_GET["company_id"])) && ($_GET["company_id"] > 0)) {
		$company_id=(int)$_GET["company_id"];
	}
	else {
		return FALSE;
	}


	$res="";
	raSetup();
	$ra=& $GLOBALS["reservation_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$ra->lang->def("_RESERVATION_APPROVAL");
	$res.=$ra->titleArea($title);
	$res.=$ra->getHead();

	$res.=$ra->buyReservation();

	$res.=$ra->getFooter();
	$out->add($res);

	//print_r($_SESSION["reservations_to_buy"]);
}



//---------------------------------------------------------------------------//

function reservationDispatch($op) {
	switch($op) {
		case "main" : {
			reservationMain();
		} break;
		case "reservations": {
			companyReservation();
		} break;
		case "updatersv": {
			updateReservation();
		} break;
		case "buy": {
			buyReservation();
		} break;
	}
}

?>
