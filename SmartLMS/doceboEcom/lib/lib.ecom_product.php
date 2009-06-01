<?php
/************************************************************************/
/* DOCEBO ECOM - E-Commerce                                             */
/* ============================================	                         */
/*	                                                                      */
/* Copyright (c) 2006                                                   */
/* http://www.docebo.org                                                */
/*	                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/**
 * @version 	$Id:  $
 */
// ----------------------------------------------------------------------------



class EcomProduct {

	VAR $product_info=array();


	function EcomProduct() {


	}


	function doActivate() {


	}


	function doDeactivate() {


	}


	function setProductInfo($info) {
		$this->product_info=$info;
	}


	function getProductInfo() {
		return (array)$this->product_info;
	}


}


?>