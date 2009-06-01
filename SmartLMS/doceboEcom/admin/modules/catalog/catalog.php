<?php
/*************************************************************************/
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");
// TODO: check perm!
// TODO: avoid a non-empty floder to be deleted!

addCss("style_treeview");
require_once($GLOBALS["where_ecom"]."/lib/lib.catalog.php");


function &caSetup() {
	$res =new CatalogAdmin();
	$res->urlManagerSetup("modname=catalog&op=main");
	return $res;
}


function catalogMain() {
	checkPerm("view");

	$res="";

	$ca =& caSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$ca->lang->def("_CATALOG");
	$res.=$ca->titleArea($title);
	$res.=$ca->getHead();

	$vis_item =20; //$GLOBALS["ecom"]["visuItem"];
	$res.=$ca->getCatalogMain($vis_item);

	$res.=$ca->getFooter();
	$out->add($res);
}



function addeditProduct($id=0) {
	$res="";

	$ca =&caSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		//checkPerm("mod");
		$info =$ca->catalogManager->getProductInfo($id);
		$title_label =$ca->lang->def("_EDIT_PRODUCT").": ".$info["title"];
	}
	else {
		//checkPerm("add");
		$title_label=$ca->lang->def("_ADD_PRODUCT");
	}

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url] =$ca->lang->def("_CATALOG");
	$title[]=$title_label;
	$res.=$ca->titleArea($title);
	$res.=$ca->getHead();
	$res.=$ca->backUi();

	$res.=$ca->addeditProduct($id);

	$res.=$ca->backUi();
	$res.=$ca->getFooter();
	$out->add($res);
}


function saveProduct() {
	//checkPerm("mod");
	$ca =&caSetup();
	$ca->saveProduct();
}


function deleteProduct() {
	//checkPerm("del");

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0))
		$prd_id=(int)$_GET["prd_id"];
	else
		return FALSE;


	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$ca =&caSetup();
	$del_res =$ca->deleteProduct($prd_id);

	if (!empty($del_res)) {
		$res ="";
		$um =& UrlManager::getInstance();
		$back_url =$um->getUrl();
		$title[$back_url] =$ca->lang->def("_CATALOG");
		$prd_info =$ca->catalogManager->getProductInfo($prd_id);
		$title[]=$ca->lang->def("_DELETE_PRODUCT").": ".$prd_info["title"];
		$res.=$ca->titleArea($title);
		$res.=$ca->getHead();
		$res.=$ca->backUi($back_url);

		$res.=$del_res;

		$res.=$ca->getFooter();
		$out->add($res);
	}
}


function selProductFolders() {
	//checkPerm("mod");
	$res="";

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0)) {
		$id =$_GET["prd_id"];
	}
	else {
		return FALSE;
	}

	$ca =& caSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url] =$ca->lang->def("_CATALOG");
	$info =$ca->catalogManager->getProductInfo($id);
	$title[]=$ca->lang->def("_SEL_PRODUCT_FOLDERS").": ".$info["title"];
	$res.=$ca->titleArea($title);
	$res.=$ca->getHead();
	$res.=$ca->backUi($back_url);

	$res.=$ca->selProductFolders($id);

	$res.=$ca->getFooter();
	$out->add($res);
}


function manageProductPics() {
	//checkPerm("mod");
	$res="";

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0)) {
		$id =$_GET["prd_id"];
	}
	else {
		return FALSE;
	}

	$ca =& caSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url] =$ca->lang->def("_CATALOG");
	$info =$ca->catalogManager->getProductInfo($id);
	$title[]=$ca->lang->def("_MANAGE_PRODUCT_PICTURES").": ".$info["title"];
	$res.=$ca->titleArea($title);
	$res.=$ca->getHead();
	$res.=$ca->backUi($back_url);

	$vis_item =20; //$GLOBALS["ecom"]["visuItem"];
	$res.=$ca->getProductImagesTable($id, $vis_item);

	$res.=$ca->backUi($back_url);
	$res.=$ca->getFooter();
	$out->add($res);
}


function addeditImage() {
	$res="";

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0)) {
		$prd_id =$_GET["prd_id"];
	}
	else {
		return FALSE;
	}

	if ((isset($_GET["img_id"])) && ($_GET["img_id"] > 0)) {
		$id =$_GET["img_id"];
	}
	else {
		$id =0;
	}

	$ca =&caSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		//checkPerm("mod");
		$info =$ca->catalogManager->getProductImageInfo($id);
		$title_label =$ca->lang->def("_EDIT_PRODUCT").": ".$info["title"];
	}
	else {
		//checkPerm("add");
		$title_label=$ca->lang->def("_ADD_PRODUCT");
	}

	$um=& UrlManager::getInstance();
	$url =$um->getUrl();
	$title[$url] =$ca->lang->def("_CATALOG");
	$back_url =$um->getUrl("op=manpics&prd_id=".$prd_id);
	$prd_info =$ca->catalogManager->getProductInfo($prd_id);
	$title[$back_url]=$ca->lang->def("_MANAGE_PRODUCT_PICTURES").": ".$prd_info["title"];
	$title[]=$title_label;
	$res.=$ca->titleArea($title);
	$res.=$ca->getHead();
	$res.=$ca->backUi($back_url);

	$res.=$ca->addeditImage($id, $prd_id);

	$res.=$ca->backUi($back_url);
	$res.=$ca->getFooter();
	$out->add($res);
}


function saveProductImage() {
	//checkPerm("mod");

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0)) {
		$prd_id =$_GET["prd_id"];
	}
	else {
		return FALSE;
	}

	$ca =&caSetup();
	$ca->saveProductImage($prd_id);
}


function moveImage($direction) {
	//checkPerm("mod");

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0))
		$prd_id=(int)$_GET["prd_id"];
	else
		return FALSE;

	if ((isset($_GET["img_id"])) && ($_GET["img_id"] > 0))
		$img_id=(int)$_GET["img_id"];
	else
		return FALSE;

	$ca =&caSetup();
	$ca->moveImage($prd_id, $img_id, $direction);
}


function switchImgPublish() {
	//checkPerm("mod");

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0))
		$prd_id=(int)$_GET["prd_id"];
	else
		return FALSE;

	if ((isset($_GET["img_id"])) && ($_GET["img_id"] > 0))
		$img_id=(int)$_GET["img_id"];
	else
		return FALSE;

	$ca =&caSetup();
	$ca->switchImgPublish($prd_id, $img_id);
}


function deleteProductImage() {
	//checkPerm("del");

	if ((isset($_GET["prd_id"])) && ($_GET["prd_id"] > 0))
		$prd_id=(int)$_GET["prd_id"];
	else
		return FALSE;

	if ((isset($_GET["img_id"])) && ($_GET["img_id"] > 0))
		$img_id=(int)$_GET["img_id"];
	else
		return FALSE;

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$ca =&caSetup();
	$del_res =$ca->deleteProductImage($prd_id, $img_id);

	if (!empty($del_res)) {
		$res ="";
		$um =& UrlManager::getInstance();
		$url =$um->getUrl();
		$title[$url] =$ca->lang->def("_CATALOG");
		$back_url =$um->getUrl("op=manpics&prd_id=".$prd_id);
		$prd_info =$ca->catalogManager->getProductInfo($prd_id);
		$title[$back_url]=$ca->lang->def("_MANAGE_PRODUCT_PICTURES").": ".$prd_info["title"];
		$title[]=$ca->lang->def("_DELETE_IMAGE");
		$res.=$ca->titleArea($title);
		$res.=$ca->getHead();
		$res.=$ca->backUi($back_url);

		$res.=$del_res;

		$res.=$ca->getFooter();
		$out->add($res);
	}
}



// ----------------------------------------------------------------------------

function catalogDispatch($op) {
	switch($op) {
		case "main" : {
			catalogMain();
		} break;
		case "addproduct": {
			addeditProduct();
		} break;
		case "editproduct": {
			addeditProduct((int)$_GET["prd_id"]);
		} break;
		case "saveproduct": {
			if (!isset($_POST["undo"]))
				saveProduct();
			else
				catalogMain();
		} break;
		case "delproduct": {
			deleteProduct();
		} break;
		case "selfolders": {
			selProductFolders();
		} break;
		case "manpics": {
			manageProductPics();
		} break;
		case "addimage":
		case "editimage": {
			addeditImage();
		} break;
		case "saveimage": {
			if (!isset($_POST["undo"]))
				saveProductImage();
			else
				manageProductPics();
		} break;
		case "delimage": {
			deleteProductImage();
		} break;
		case "moveimgup": {
			moveImage("up");
		} break;
		case "moveimgdown": {
			moveImage("down");
		} break;
		case "switchimgpublish": {
			switchImgPublish();
		} break;
	}
}

?>