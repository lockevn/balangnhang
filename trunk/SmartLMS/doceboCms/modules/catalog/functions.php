<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

addCss("style_catalog");
require_once($GLOBALS["where_ecom"]."/lib/lib.catalog.php");


function &cpSetup() {
	$res =new CatalogPublic();
	$res->urlManagerSetup("mn=catalog&pi=".getPI()."&op=main");
	return $res;
}


function getCategoriesMenu($block_id=FALSE) {

	if (($block_id === FALSE) || ($block_id < 1)) {
		$block_id =(int)$GLOBALS["pb"];
	}

	$cp =& cpSetup();
	$opt =loadBlockOption($block_id);

	$from_path ="/root";
	$lev =FALSE;
	$cat_id =FALSE;

	if ((isset($_GET["mn"])) && ($_GET["mn"] == "catalog") &&
	    (isset($_GET["cat_id"])) && ($_GET["cat_id"] > 0)) {
		// load $from_path from the selected category folder
		$cat_info =$cp->catalogManager->getCategoryInfo($_GET["cat_id"]);
		$cat_id =$_GET["cat_id"];

		// check that $from_path is the same or a child of the default one
		if (substr($cat_info["path"], 0, strlen($from_path)) == $from_path) {
			$from_path =$cat_info["path"];
			$lev =$cat_info["lev"];
		}
	}

	$res =$cp->getCategoriesMenuCode($from_path, $lev, $cat_id);

	return $res;
}


function getCatalogCategoryId() {
	$res =0;

	if ((isset($_GET["cat_id"])) && ($_GET["cat_id"] > 0)) {
		$res =$_GET["cat_id"];
	}

	return $res;
}


function showCategoryItems() {
	$res="";

	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$cat_id =getCatalogCategoryId();
	$res =$cp->showCategoryItems($cat_id, TRUE);

	$out->add($res);
}


function showProductDetails() {
	$res="";

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0)) {
		$prd_id =$_GET["prd_id"];
	}
	else {
		return FALSE;
	}

	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$cat_id =getCatalogCategoryId();

	$res =$cp->showProductDetails($prd_id, $cat_id);

	$out->add($res);
}


function showMiniCartSummary() {
	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$res =$cp->showMiniCartSummary();

	$out->add($res);
}


function addToCart() {

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0)) {
		$prd_id =$_GET["prd_id"];
	}
	else {
		return FALSE;
	}

	$cat_id =getCatalogCategoryId();

	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$res =$cp->addToCart($prd_id, $cat_id);

	$out->add($res);
}


function showCartSummary() {
	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$res =$cp->showCartSummary();

	$out->add($res);
}


function catalogNewCompany() {
	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$res =$cp->createNewCompany();

	$out->add($res);
}


function catalogSaveCompany() {
	$cp =& cpSetup();
	$res =$cp->saveCompany();
}


function catalogCompanyBillingInfo() {
	$cp =& cpSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$res =$cp->companyBillingInfo();

	$out->add($res);
}


?>
