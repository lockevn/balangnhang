<?php

/************************************************************************/
/* DOCEBO Ecom - E-commerce System										*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package Configuration
 * @author 	Demarinis Claudio (claudiodema@docebo.com)
 * @version $Id: lib.cart.php
 **/

class Cart
{
	/**
	 *class constructor: $array of items (an item has an id , quantity and price ) and total price
	 */
	var $array_item;
	var $total;
	var $use_quantities =FALSE;

	/**
	 * class constructor
	 */
	function Cart()
	{
		$this->array_item=array();
		$this->total=0;
	}

	function getUseQuantities() {
		return (bool)$this->use_quantities;
	}


	function setUseQuantities($val) {
		$this->use_quantities =(bool)$val;
	}


	function &createInstance( ) {

		if (!defined("_ECOM_CURRENCY")) {
			$currency_label = getPLSetting("ecom", "currency_label", "");
			define("_ECOM_CURRENCY", $currency_label);
		}

		if( !isset( $GLOBALS['cart_instance']) )
		if(isset($_SESSION['cart'])) {
			$GLOBALS['cart_instance'] = unserialize(urldecode($_SESSION['cart']));
		} else
			$GLOBALS['cart_instance']= new Cart();
		return $GLOBALS['cart_instance'];
	}

	function saveCart() {
		$_SESSION['cart'] = urlencode(serialize($GLOBALS['cart_instance']));
	 }

	 function loadCart() {
	 	if(isset($_SESSION['cart']))
	 	$GLOBALS['cart_instance'] = unserialize(urldecode($_SESSION['cart']));
	 }

	 function emptyCart() {
	 	unset($GLOBALS['cart_instance']);
	 	unset($_SESSION['cart']);
	 }

	 function isEmpty() {
		 return ($this->getCartItemCount() > 0 ? FALSE : TRUE);
	 }

	/* add an item to cart
	* @param  int  	$id_item    item id to add
	*
	* @param  string  	$descriptor_item    item id to add
	* @param  int  	$id_item    item id to add
	* @return bool	 	return TRUE if the item is successfully removed
	*					return FALSE if the item is  not successfully removed
	*/
	function addItemToCart($item_code, $id_item, $descriptor_item, $price_item,$quantity_item, $type)
	{
		if(!isset($this->array_item[$item_code.'_'.$id_item]))
		{
			$this->array_item[$item_code.'_'.$id_item]['descriptor'] = $descriptor_item;
			$this->array_item[$item_code.'_'.$id_item]['price'] = $price_item;
			$this->array_item[$item_code.'_'.$id_item]['quantity'] = $quantity_item;
			$this->array_item[$item_code.'_'.$id_item]['type'] = $type;

			return true;
		} else return false;
	}

	function isInCart($search_item) {

		return (isset($this->array_item[$search_item]));
	}

	/**
	 * return information about the login attempt for the user
	 * @param  int		$id_item  of  item to update
	 *
	 *
	 * @return bool	 	return TRUE if the item is successfully updated
	 *					return FALSE if the item is  not successfully updated
	 */
	function updateQuantityItem($id_item, $quantity)	{

		if(isset($this->array_item[$id_item]))
		{
			$this->array_item[$id_item]['quantity']=$quantity;
			if($this->array_item[$id_item]['quantity'] == 0) {
				$this->deleteItem($id_item);
			} return true;
		}
		else return false;
	}
	/**
	 * return information about the login attempt for the user
	 * @param int	 	$item_id    item id to remove
	 *
	 *
	 * @return bool	 	return TRUE if the item is successfully removed
	 *					return FALSE if the item is  not successfully removed
	 */

	function deleteItem($id_item) {
		if(isset($this->array_item[$id_item])) {
			unset($this->array_item[$id_item]);
			return true;
		}
		else return false ;
	}
	/**
	 * return information about the total amount of the cart
	 *
	 *
	 *
	 * @return string   $total total amount of cart
	 *
	 */

	function getTotalAmount()
	{
		$this->total = 0;
		if(isset($this->array_item))
		{
			foreach($this->array_item as $key => $val)
			{
				$this->total += $this->array_item[$key]['price']* $this->array_item[$key]['quantity'];
			}
		}
		return $this->total;
	}

	/**
	 	 * @return string	the html code for cart
	 */

	function getCart($small=FALSE, $tax_zone=FALSE, $can_remove=TRUE, $tab_style=FALSE) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		$lang =& DoceboLanguage::createInstance('cart', 'ecom');
		$tb	= new TypeOne('', $lang->def('_CART_CAPTION'), $lang->def('_CART_SUMMARY'));
		$type_h = array('', '','');

		if ($tab_style === FALSE) {
			$tab_style ="cart_table";
		}
		$tb->setTableStyle($tab_style);

		if (!$small) {
			$type_h[]="image";
		}

		$cont_h	= array();
		$cont_h[]=$lang->def("_ITEM_NAME");
		$cont_h[]=$lang->def("_PRICE");
		$cont_h[]=$lang->def("_QUANTITY");
		if ((!$small) && ($can_remove)) {
			$img="<img src=\"".getPathImage()."standard/rem.gif"."\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\"></a>\n";
			$cont_h[]=$img;
		}
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);

		$tax_rate_arr=$this->getTaxRateArr(FALSE, $tax_zone, "course");
		$rate=(isset($tax_rate_arr[0]["rate"]) ? $tax_rate_arr[0]["rate"] : '0');

		if (!empty($this->array_item)){
			foreach ($this->array_item as $id_item => $value_item) {

				$price=$value_item['price'];

				$cont = array($value_item['descriptor'],$price." "._ECOM_CURRENCY);
				//$cont[]=Form::getInputTextfield('textfield','item['.$id_item.']','item['.$id_item.']',$value_item['quantity'],'',5,'');

				if ((!$small) && ($this->getUseQuantities())) {
					$qtt_field ="";

					$field_id ='item_'.$id_item;
					$field_name ='item['.$id_item.']';
					$qtt_field.='<input type="text" id="'.$field_id.'" name="'.$field_name.'" ';
					$qtt_field.='value="'.$value_item['quantity'].'" size="2" />';

					$cont[]=$qtt_field;
				}
				else {
					$cont[]=$value_item['quantity'];
				}

				if ((!$small) && ($can_remove)) {
					$other="title=\"".$lang->def("_REM_FROM_CART").": ".substr($value_item['descriptor'], 0, 30)."\"";
					$cont[]=Form::getButton('del_item'.$id_item,'del_item['.$id_item.']','', "del_item", $other);
				}

				$tb->addBody($cont);
			}
		}
		$out=$tb->getTable();
		$out.="<div class=\"cart_summary\">";
		if (!empty($this->array_item)) {
			$vat=round(($this->getTotalAmount() / 100 * $rate), 2);
			$out.=$lang->def('_TOTAL_AMOUNT').': '. $this->getTotalAmount()." "._ECOM_CURRENCY;

			if ($tax_zone) {
				$out.="<div>".$lang->def('_RATE').': '. ($rate!='' ? $rate : '0')."%</div>";
				$out.='<b>'.$lang->def('_TOTAL_AMOUNT').': '. ($this->getTotalAmount()+$vat)." "._ECOM_CURRENCY."</b>\n";
			}
			else {
				$out.="<div class=\"no_tax_info\">".$lang->def("_PRICES_WITHOUT_TAX")."</div>";
			}
		}
		else {
			$out.=$lang->def('_NOARTICLE');
		}
		$out.="</div>\n"; // cart_summary

		//var_dump($this->array_item);
		return $out;
	}


	function getCartItems() {
		$res =array();

		if (!empty($this->array_item)){
			$res =$this->array_item;
		}

		return $res;
	}


	function getCartDashboard($url) {
	}


	function getTaxRateArr($cat_id=FALSE, $tax_zone=FALSE, $cat_code=FALSE) {

		if (($cat_id === FALSE) && ($cat_code === FALSE)) {
			return FALSE;
		}
		else if (($cat_id === FALSE) && ($cat_code !== FALSE)) {
			// find cat id
			$table=$GLOBALS["prefix_ecom"]."_tax_cat_god";

			$qtxt="SELECT * FROM ".$table." WHERE cat_code='".$cat_code."' LIMIT 0,1";
			$q=mysql_query($qtxt);

			$res=array();
			if (($q) && (mysql_num_rows($q) > 0)) {
				$row=mysql_fetch_assoc($q);
				$cat_id=$row["id_cat_god"];
			}
			else {
				return FALSE;
			}
		}

		$table=$GLOBALS["prefix_ecom"]."_tax_rate";
		$qtxt ="SELECT * FROM ".$table." WHERE id_cat_god='".(int)$cat_id."'";
		if ($tax_zone !== FALSE) {
			$qtxt.=" AND id_zone='".(int)$tax_zone."' ";
		}
		$q=mysql_query($qtxt);

		$res=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {

				$res[]=$row;

			}
		}

		return $res;
	}


	function getTaxZoneArr() {

		$table=$GLOBALS["prefix_ecom"]."_tax_zone";
		$qtxt="SELECT * FROM ".$table;
		$q=mysql_query($qtxt);

		$res=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {

				$res[]=$row;

			}
		}

		return $res;
	}


	function getTaxZoneDropdownArr() {
		$lang 		=& DoceboLanguage::createInstance('admin_taxzone', 'ecom');
		$tax_zone_arr=$this->getTaxZoneArr();

		$res=array();
		foreach ($tax_zone_arr as $row) {
			$res[$row["id_zone"]]=$lang->def($row["name_zone"]);
		}

		return $res;
	}


	/**
	 *
	 */
	function getCartItemCount() {

		if (is_array($this->array_item)) {
			$res=count($this->array_item);
		}
		else {
			$res=0;
		}

		return $res;
	}

}


?>