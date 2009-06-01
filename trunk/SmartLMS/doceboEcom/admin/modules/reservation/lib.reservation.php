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


class ReservationAdmin {


	var $lang=NULL;
	var $um=NULL;
	var	$table_style=FALSE;

	// Reservation manager object
	var $rsv_manager=NULL;


	function ReservationAdmin() {
		$this->lang =& DoceboLanguage::createInstance('admin_reservation', "ecom");
		$this->rsv_manager=new ReservationManager();
	}


	function getTableStyle() {
		return $this->table_style;
	}


	function setTableStyle($style) {
		$this->table_style=$style;
	}


	function titleArea($text, $image = '', $alt_image = '') {
		$res="";

		$res=getTitleArea($text, $image = '', $alt_image = '');

		return $res;
	}


	function getHead() {
		$res="";
		$res.="<div class=\"std_block\">\n";
		return $res;
	}


	function getFooter() {
		$res="";
		$res.="</div>\n";
		return $res;
	}


	function backUi($url=FALSE) {
		$res="";
		$um=& UrlManager::getInstance();

		if ($url === FALSE)
			$url=$um->getUrl();

		$res.=getBackUi($url, $this->lang->def( '_BACK' ));
		return $res;
	}


	function urlManagerSetup($std_query) {
		require_once($GLOBALS['where_framework']."/lib/lib.urlmanager.php");

		/* if (!isset($GLOBALS["url_manager"]))
			$GLOBALS["url_manager"]=new UrlManager(); */

		$um=& UrlManager::getInstance();

		$um->setStdQuery($std_query);
	}


	function getBuyerCompaniesTable($vis_item, $company_arr) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_BUYER_COMPANIES_CAP");
		$table_summary=$this->lang->def("_TABLE_BUYER_COMPANIES_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_COMPANY"));
		$head[]=$this->lang->def("_RESERVATIONS");
		$head[]=$this->lang->def("_LAST_RESERVATION");


/*		$img ="<img src=\"".getPathImage('fw')."standard/export.gif\" alt=\"".$this->lang->def("_EXPORT")."\" ";
		$img.="title=\"".$this->lang->def("_EXPORT")."\" />";
		$head[]=$img; */

		/*
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
		$head[]=$img;


		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;*/

		$head_type=array("", "", "");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=(int)$tab->getSelectedElement();

		$data_info=$this->rsv_manager->getBuyerCompaniesList($ini, $vis_item, $company_arr);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["company_id"];

			$rowcnt=array();

			if ($data_arr[$i]["tot"] > 0) {
				$url=$um->getUrl("op=reservations&company_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$data_arr[$i]["company_name"]."</a>\n";
			}
			else {
				$rowcnt[]=$data_arr[$i]["company_name"];
			}

			$rowcnt[]=$data_arr[$i]["tot"];

			if ($data_arr[$i]["reservation_date"] !== FALSE) {
				$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($data_arr[$i]["reservation_date"]);
			}
			else {
				$rowcnt[]="--";
			}
/*
			$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
			$url=$um->getUrl("op=setperm&wiki_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editwiki&wiki_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delwiki&wiki_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
*/
			$tab->addBody($rowcnt);
		}
/*
		$url=$um->getUrl("op=addwiki");
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$tab->addActionAdd($add_box); */

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function getReservationListTable($company_id) {
		$res="";
		require_once($GLOBALS["where_lms"]."/lib/lib.coursesubscribe.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		if (isset($_SESSION["reservations_to_buy"]))
			unset($_SESSION["reservations_to_buy"]);

		$table_caption=$this->lang->def("_TABLE_BUYER_COMPANIES_CAP");
		$table_summary=$this->lang->def("_TABLE_BUYER_COMPANIES_SUM");

		$um=& UrlManager::getInstance();
		$cs=& CourseSubscribe::getInstance();

		$form=new Form();
		$url=$um->getUrl("op=updatersv&company_id=".$company_id);
		$res.=$form->openForm("main_form", $url);

		$res.=$form->getHidden("company_id", "company_id", $company_id);

		$tab=new typeOne(0, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_NAME"));

		$head[]=$this->lang->def("_USER");

		$head[]=$this->lang->def("_PRICE");

		$img ="<img src=\"".getPathImage('fw')."standard/check.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/cancel16.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;

		$head_type=array("", "", "", "image", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=(int)$tab->getSelectedElement();

		$data_info=$this->rsv_manager->getReservationList(FALSE, FALSE);
		$data_arr=$data_info["data_arr"]; //print_r($data_arr);
		$user_info=$data_info["user"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["reservation_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["name"];

			$rowcnt[]=$user_info[$data_arr[$i]["user_id"]];

			$rowcnt[]=$data_arr[$i]["price"];// print_r($data_arr[$i]);

			switch ($data_arr[$i]["type"]) {
				case "course": {
					$course_id=(int)substr($data_arr[$i]["product_code"], strlen("course_"));
					$edition_id=FALSE;
				} break;
				case "course_edition": {
					$course_id=FALSE;
					$edition_id=(int)substr($data_arr[$i]["product_code"], strlen("course_edition_"));
				} break;
			}

			//if ((!$cs->isFull($course_id, $edition_id)) || ($cs->allowOverbooking($course_id, $edition_id))) {
			if (!$cs->isFull($course_id, $edition_id)) {
				$rowcnt[]=$form->getCheckbox("", "reservation_accept_".$id, "reservation_accept[".$id."]", $id);
			}
			else {
				$alt=$this->lang->def("_MAX_SUBSCRIPTIONS_REACHED");
				$img ="<img src=\"".getPathImage('fw')."standard/warning.gif\" ";
				$img.="alt=\"".$alt."\" title=\"".$alt."\" />";
				$rowcnt[]=$img;
			}
			$rowcnt[]=$form->getCheckbox("", "reservation_refuse_".$id, "reservation_refuse[".$id."]", $id);


			$tab->addBody($rowcnt);
		}



		$res.=$tab->getTable(); //.$tab->getNavBar($ini, $db_tot);

		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $this->lang->def("_GO_ON"));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function updateReservation($data) {

		$company_id=$data["company_id"];

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=reservations&company_id=".$company_id);

		if ((isset($data["reservation_refuse"])) && (is_array($data["reservation_refuse"]))) {
			foreach($data["reservation_refuse"] as $reservation_id) {

				$this->rsv_manager->deleteReservation($reservation_id);

			}
		}

		if ((isset($data["reservation_accept"])) && (is_array($data["reservation_accept"]))) {

			$where="reservation_id IN (".implode(",", $data["reservation_accept"]).")";
			$reservation_list=$this->rsv_manager->getReservationList(FALSE, FALSE, $where);
			
			$_SESSION["reservations_to_buy"]=$reservation_list;
			$url=$um->getUrl("op=buy&company_id=".$company_id);

		}

		jumpTo($url);
	}


	function buyReservation() {
		$res="";

		$um=& UrlManager::getInstance();

		if ((isset($_GET["company_id"])) && ($_GET["company_id"] > 0)) {
			$company_id=(int)$_GET["company_id"];
		}
		else {
			return FALSE;
		}

		if (isset($_POST["undo"])) {
			jumpTo($um->getUrl("op=reservations&company_id=".$company_id));
			die();
		}

		if ((isset($_GET["step"])) && ($_GET["step"] > 0)) {
			$step=(int)$_GET["step"];
		}
		else {
			$step=1;
		}


		switch ($step) {
			case 1: {
				$res=$this->payMethodSelect($company_id);
			} break;
			case 2: {
				$res = $this->saveAsTransaction($company_id);
				jumpTo($um->getUrl("op=reservations&company_id=".$company_id.'&result='.( $res ? 'ok_approve' : 'err_approve' )));
			} break;
		}

		return $res;
	}


	function payMethodSelect($company_id) {
		$res="";
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=buy&company_id=".$company_id."&step=2");

		$payment= new Payment();

		$res.=Form::openForm('payment', $url);
		$res.=$payment->getFormSelection();
		$res.=Form::openButtonSpace()
		.Form::getButton('undo', 'undo', $this->lang->def('_BACK'))
		.Form::getButton('payment_selected', 'payment_selected', $this->lang->def('_GO_CART_TWO'))
		.Form::closeButtonSpace()
		.Form::closeForm().'</div>';

		return $res;
	}


	function saveAsTransaction($company_id) {
		require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=buy&company_id=".$company_id."&step=2");

		$payment= new Payment();

		$valid_paymod=$payment->getActivePayment();
		if (in_array($_POST['paymod'], $valid_paymod))
		require_once($GLOBALS['where_ecom'].'/modules/payment/'.$_POST['paymod'].'.php');

		$default_payment_status=$payment->getDefaultStatus("payment");
		$default_order_status=$payment->getDefaultStatus("order");

		switch($_POST['paymod']){
			case "wire_transfer" : {
				$payment_info = getWireTransferInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;

			case "mark" : {
				$payment_info = getMarkInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;
			case "check" : {
				$payment_info = getCheckInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;

			case "money_order" : {
				$payment_info = getMoneyOrderInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;
			case "paypal" : {

			};break;
		}

		$items=array();
		$total	=0;

		$reservation_list=$_SESSION["reservations_to_buy"]["data_arr"];
		
		foreach ($reservation_list as $reservation) {

			$code=$reservation["product_code"];
			$items[$code]["user"]=$reservation["user_id"];
			$items[$code]["descriptor"]=addslashes($reservation["name"]);
			$items[$code]["type"]=$reservation["type"];
			$items[$code]["price"]=$reservation["price"];
			$items[$code]["quantity"]=1;

			$total=$total+$reservation["price"];
		}
		$tansaction_created = $payment->saveTransaction($company_id, $total, $order_status, $payment_status, $_POST["paymod"], $items);
		if($tansaction_created) {
			
			foreach ($reservation_list as $reservation) { 
			
				$this->rsv_manager->deleteReservation($reservation['reservation_id']);
			}
			return true;
		}
		return false;
	}


}





Class ReservationManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $reservation_info=NULL;

	function ReservationManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_ecom"]);
		$this->dbconn=$dbconn;
	}


	function _executeQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	function _getReservationTable() {
		return $this->prefix."_reservation";
	}


	function getBuyerCompaniesList($ini=FALSE, $vis_item=FALSE, $company_arr) {

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$ccm=new CoreCompanyManager();

		$data_info=array();
		$data_info["data_arr"]=array();

		if (!is_array($company_arr))
			$company_arr=array();

		$fields="company_id, count(company_id) as tot, reservation_date";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getReservationTable()." ";

		$qtxt.="WHERE company_id IN (".implode(",", $company_arr).") ";
		$qtxt.="GROUP BY company_id ";

		$qtxt.="ORDER BY reservation_id DESC ";
		$q=$this->_executeQuery($qtxt); //echo $qtxt;

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}


		$processed_companies=array();

		$i=0;
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {

				$company_info=$ccm->getCompanyInfo($row["company_id"]);

				$data_info["data_arr"][$i]=$row;
				$data_info["data_arr"][$i]["company_name"]=$company_info["name"];

				$processed_companies[]=$row["company_id"];

				$i++;
			}
		}

		$not_processed=array_diff($company_arr, $processed_companies);

		foreach($not_processed as $company_id) {

			$company_info=$ccm->getCompanyInfo($company_id);

			$data_info["data_arr"][$i]["company_id"]=$company_id;
			$data_info["data_arr"][$i]["tot"]="0";
			$data_info["data_arr"][$i]["reservation_date"]=FALSE;
			$data_info["data_arr"][$i]["company_name"]=$company_info["name"];

			$data_info["data_tot"]++;
			$i++;
		}

//print_r($data_info);
		return $data_info;
	}


	function getReservationList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getReservationTable()." ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="WHERE ".$where." ";
		}

		$qtxt.="ORDER BY reservation_id DESC ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}


		$users_arr=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

				$id=$row["reservation_id"];
				$data_info["data_arr"][$i]=$row;
				$this->reservation_info[$id]=$row;

				if (!in_array($row["user_id"], $users_arr))
					$users_arr[]=$row["user_id"];

				$i++;
			}
		}


		if (count($users_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($users_arr);
			foreach ($users_arr as $idst) {
				if ((!empty($user_info[$idst][ACL_INFO_FIRSTNAME])) ||
				    (!empty($user_info[$idst][ACL_INFO_LASTNAME]))) {
					$username =$user_info[$idst][ACL_INFO_LASTNAME]." ";
					$username.=$user_info[$idst][ACL_INFO_FIRSTNAME];
					$data_info["user"][$idst]=$username;
				}
				else {
					$data_info["user"][$idst]=$acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
				}
			}
		}
		else {
			$data_info["user"]=array();
		}

		return $data_info;
	}


	function deleteReservation($reservation_id) {

		$qtxt ="DELETE FROM ".$this->_getReservationTable()." WHERE ";
		$qtxt.="reservation_id='".(int)$reservation_id."'";

		$q=$this->_executeQuery($qtxt);
	}


}



?>
