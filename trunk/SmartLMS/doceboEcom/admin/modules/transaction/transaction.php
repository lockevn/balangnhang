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
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

require_once($GLOBALS['where_ecom'].'/lib/lib.transaction.php');
$test =new TransactionManager();
$test->getTransactionPrdInfoFromArr(array('course_edition_5'));

function transaction() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.company.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_payaccount
	$lang 		=& DoceboLanguage::createInstance('admin_transaction', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form= new Form();
	$tb	= new TypeOne(20, $lang->def('_MANAGEMENT_TRANSACTION_CAPTION'), $lang->def('_MANAGEMENT_TRANSACTION_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=transaction&amp;op=transaction");
	$ini=$tb->getSelectedElement();
	$query_filter='';
	//if(isset($_POST['transaction_filter_name']) && ($_POST['transaction_filter_name'] != '')) {
		//	$query_filter .= " AND ... LIKE '%".$_POST['transaction_filter_name']."%'";
	//}

	$ccm=new CoreCompanyManager();

	$pay=new Payment();
	$payment_status_list=$pay->getPaymentStatusList();
	$order_status_list=$pay->getOrderStatusList();

	//search query of taxcatgods
	$query_transaction = "
	SELECT id_trans,id_user,company_id,order_status,payment_status,total_amount,transaction_date,active_status
	FROM ".$GLOBALS['prefix_ecom']."_transaction
	WHERE 1".$query_filter."
	ORDER BY id_trans DESC
	LIMIT ".$ini.", 20"; //.$GLOBALS['lms']['visuItem'];

	$query_transaction_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_ecom']."_transaction
	WHERE 1 ".$query_filter."";

	$re_transaction = mysql_query($query_transaction);
	list($tot_transaction) = mysql_fetch_row(mysql_query($query_transaction_tot));

	$cont_h	= array($lang->def('_ORDER'), $lang->def("_COMPANY"), $lang->def('_USER_TRANS'),$lang->def('total_amount'),$lang->def("_TRANSACTION_DATE"),$lang->def('_STATUS_TRANS'),$lang->def('_PAYMENT_TRANS'));
	$type_h = array('', '', '','','','','');


	$img ="<img src=\"".getPathImage()."standard/dot_green.gif\" alt=\"".$lang->def('_ACTIVATION_STATUS')."\" ";
	$img.="title=\"".$lang->def('_ACTIVATION_STATUS')."\" />";
	$cont_h[] = $img;
	$type_h[] = 'image';

	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_TRANSACTION').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';

	}


	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_trans,$id_user, $company_id, $order_status,$payment_status,$total_amount,$transaction_date, $active_status) = mysql_fetch_row($re_transaction)) {
		$user=$GLOBALS["current_user"]->getAclManager();
		$username=$user->getUserName($id_user);
		$cont = array($id_trans);

		$company_info=$ccm->getCompanyInfo($company_id);
		if (isset($company_info["name"]))
			$cont[]=$company_info["name"];
		else
			$cont[]="&nbsp;";

		$cont[]=$username;
		$cont[]=$total_amount;

		$cont[]=$GLOBALS["regset"]->databaseToRegional($transaction_date);

		if (isset($order_status_list[$order_status])) {
			$cont[]=$order_status_list[$order_status];
		}
		else {
			$cont[]="&nbsp;";
		}

		if (isset($payment_status_list[$payment_status])) {
			$cont[]=$payment_status_list[$payment_status];
		}
		else {
			$cont[]="&nbsp;";
		}

		switch($active_status) {
			case "none": {
				$img ="<img src=\"".getPathImage()."standard/dot_red.gif\" alt=\"".$lang->def('_NONE_ACTIVE')."\" ";
				$img.="title=\"".$lang->def('_NONE_ACTIVE')."\" />";
			} break;
			case "partial": {
				$img ="<img src=\"".getPathImage()."standard/dot_yellow.gif\" alt=\"".$lang->def('_SOME_ACTIVE')."\" ";
				$img.="title=\"".$lang->def('_SOME_ACTIVE')."\" />";
			} break;
			case "all": {
				$img ="<img src=\"".getPathImage()."standard/dot_green.gif\" alt=\"".$lang->def('_ALL_ACTIVE')."\" ";
				$img.="title=\"".$lang->def('_ALL_ACTIVE')."\" />";
			} break;
		}

		$cont[] = $img;

		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=transaction&amp;op=modtransaction&amp;id_trans='.$id_trans.'"'
						.'title="'.$lang->def('_TITLE_MOD_TRANSACTION').' : '.$id_trans.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$id_trans.'" /></a>';
		}
		$tb->addBody($cont);

	}



	$out->add(getTitleArea($lang->def('_TITLE_MANAGEMENT_TRANSACTION'), 'transaction', $lang->def('_ALT_TITLE_TRANSACTION'))
			.'<div class="std_block">'

	);



	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_transaction).'</div>');


}


function edittransaction() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
	require_once($GLOBALS["where_lms"]."/lib/lib.coursesubscribe.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$lang 		=& DoceboLanguage::createInstance('admin_transaction', 'ecom');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$cs=& CourseSubscribe::getInstance();

	addYahooJs();

	$id_trans = importVar('id_trans', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();

	$qtxt ="SELECT * FROM ".$GLOBALS['prefix_ecom']."_transaction ";
	$qtxt.="WHERE id_trans = '".$id_trans."'";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_assoc($q);

		$order_status=$row["order_status"];
		$payment_status=$row["payment_status"];
		$order_notes=$row["order_notes"];
		$payment_notes=$row["payment_notes"];
	}
	else {
		$order_status=FALSE;
		$payment_status=FALSE;
		$order_notes="";
		$payment_notes="";
	}

	$pay=new Payment();
	$payment_status_list=$pay->getPaymentStatusList();
	$order_status_list=$pay->getOrderStatusList();

	$res="";

	$res.=getTitleArea($lang->def('_TITLE_MANAGEMENT_TRANSACTION'), 'transaction', $lang->def('_ALT_TITLE_TRANSACTION'));
	$res.='<div class="std_block">';

	$url="index.php?modname=transaction&amp;op=updatetransaction";
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();

	$res.=$form->getDropdown($lang->def("_STATUS_TRANS"), "order_status", "order_status", $order_status_list, $order_status);
	$res.=$form->getSimpleTextarea($lang->def("_ORDER_NOTES"), "order_notes", "order_notes", $order_notes);

	$res.=$form->getDropdown($lang->def("_PAYMENT_TRANS"), "payment_status", "payment_status", $payment_status_list, $payment_status);
	$res.=$form->getSimpleTextarea($lang->def("_PAYMENT_NOTES"), "payment_notes", "payment_notes", $payment_notes);

	$res.=$form->getHidden("id_trans", "id_trans", $id_trans);

	// ---------------------------------------------------------------------------

	$vis_item=$GLOBALS["visuItem"];
	$table_caption=$lang->def("_TAB_TRANSACTION_PRODUCTS_CAP");
	$table_summary=$lang->def("_TAB_TRANSACTION_PRODUCTS_SUM");

	$tab=new typeOne($vis_item, $table_caption, $table_summary);

	$data_info=$pay->getProductsList(FALSE, FALSE, "id_trans='".$id_trans."'");
	$data_arr=$data_info["data_arr"];
	$user_info=$data_info["user"];
	$db_tot=$data_info["data_tot"];
	$has_courses =$data_info["has_courses"];

	$head=array($lang->def("_NAME"), $lang->def("_USER"));

	if ($has_courses) {

		$alt=$lang->def("_MAX_SUBSCRIPTIONS_REACHED");
		$img ="<img src=\"".getPathImage('fw')."standard/warning.gif\" ";
		$img.="alt=\"".$alt."\" title=\"".$alt."\" />";
		$head[]=$img;

		$head[]=$lang->def("_ACTIVATE");
		$head[]=$lang->def("_DEACTIVATE");
	}

	$head_type=array('', '', 'image', 'colum_width_date align_center', 'colum_width_date align_center');

	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$js = '';
	
	$tot=count($data_arr);
	for($i=0; $i<$tot; $i++ ) {

		$rowcnt=array();

		$id=$data_arr[$i]["product_id"];

		$rowcnt[]=$data_arr[$i]["name"];

		$user_id=$data_arr[$i]["id_user"];
		$rowcnt[]=$user_info[$user_id];

		if ($has_courses) {
			$course_id=FALSE;
			$edition_id=FALSE;
			$is_course =FALSE;
			switch ($data_arr[$i]["type"]) {
				case "course": {
					$course_id=substr($data_arr[$i]["id_prod"], strlen("course_"));
					$edition_id=FALSE;
					$is_course =TRUE;
				} break;
				case "course_edition": {
					$course_id=FALSE;
					$edition_id=(int)substr($data_arr[$i]["id_prod"], strlen("course_edition_"));
					$is_course =TRUE;
				} break;
			}

			if (!$is_course || !$cs->isFull($course_id, $edition_id)) {
				$is_full=FALSE;
				$rowcnt[]="&nbsp;";
			}
			else {
				$is_full=TRUE;
				$alt=$lang->def("_MAX_SUBSCRIPTIONS_REACHED");
				$img ="<img src=\"".getPathImage('fw')."standard/warning.gif\" ";
				$img.="alt=\"".$alt."\" title=\"".$alt."\" />";

				$hidden_field=$form->getHidden("full_product_".$id, "full_product[".$id."]", $id);

				$rowcnt[]=$img.$hidden_field;
			}

			$active=($data_arr[$i]["active"] == 1 ? TRUE : FALSE);
			$deactivated=($data_arr[$i]["active"] == -1 ? TRUE : FALSE);
			$disabled="disabled=\"disabled\"";

			$overbooking =FALSE;

			if ((!$is_course) || ( ($is_full) && (!$cs->allowOverbooking($course_id, $edition_id)) )) {
				// Forcing disabled checkbox..
				$other_code=$disabled;
			}

			$other_code=($active || $deactivated || $overbooking ? $disabled : "");
			$rowcnt[]=$form->getCheckBox("", "sel_product_".$id, "sel_product[".$id."]", 1, $active, $other_code);

			$other_code=($active || $deactivated ? $disabled : "");
			$rowcnt[]=$form->getCheckBox("", "to_deactivate_".$id, "to_deactivate[".$id."]", 1, $deactivated, $other_code);
			
			$js .= 'YAHOO.util.Event.addListener("sel_product_'.$id.'", "click", function(){'
			.'	YAHOO.util.Dom.get("to_deactivate_'.$id.'").checked = false;'
			.'} ); ';
			
			$js .='YAHOO.util.Event.addListener("to_deactivate_'.$id.'", "click", function(){'
			.'	YAHOO.util.Dom.get("sel_product_'.$id.'").checked = false;'
			.'} ); ';
		}

		$tab->addBody($rowcnt);
	}

	$res.=$tab->getTable();
	
	$res.='<script type="text/javascript">'
		.$js
		.'</script>';
	// ---------------------------------------------------------------------------

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def('_SAVE'));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.="</div>\n";
	$out->add($res);
}


function updateTransaction() {
	checkPerm("mod");
	require_once($GLOBALS["where_ecom"]."/modules/payment/class.payment.php");


	if (isset($_POST["full_product"])) {
		selectProductToOverbook();
	}
	else {
		$pay=new Payment();
		$pay->updateTransaction($_POST);

		jumpTo("index.php?modname=transaction&op=transaction");
	}
}


function selectProductToOverbook() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
	require_once($GLOBALS["where_lms"]."/lib/lib.coursesubscribe.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$lang 		=& DoceboLanguage::createInstance('admin_transaction', 'ecom');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$cs=& CourseSubscribe::getInstance();

	$pay=new Payment();

	$res="";

	$res.=getTitleArea($lang->def('_TITLE_MANAGEMENT_TRANSACTION'), 'transaction', $lang->def('_ALT_TITLE_TRANSACTION'));
	$res.='<div class="std_block">';

	$res.=getInfoUI($lang->def("_PRODUCTS_CONFIRM_OVERBOOKING"));

	$url="index.php?modname=transaction&amp;op=updatetransaction";
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();

	$full_product=$_POST["full_product"];
	unset($_POST["full_product"]);

	$exit=FALSE;
	if (isset($_POST["sel_product"])) {
		$sel_product=array_keys($_POST["sel_product"]);
		foreach($full_product as $key=>$val) {
			if (in_array($val, $sel_product)) {
				unset($_POST["sel_product"][$val]);
			}
			else {
				unset($full_product[$key]);
			}
		}
	}
	else {
		$exit=TRUE;
	}


	if (($exit) || (!is_array($full_product)) || (count($full_product) < 1)) {
		updateTransaction();
		return FALSE;
	}

	foreach($_POST as $key=>$val) {

		if (is_array($val)) {
			foreach($val as $item_key=>$item_val) {
				$res.=$form->getHidden($key."_".$item_key, $key."[".$item_key."]", $item_val);
			}
		}
		else {
			$res.=$form->getHidden($key, $key, $val);
		}
	}


	$vis_item=$GLOBALS["visuItem"];
	$table_caption=$lang->def("_TAB_TRANSACTION_PRODUCTS_CAP");
	$table_summary=$lang->def("_TAB_TRANSACTION_PRODUCTS_SUM");

	$tab=new typeOne($vis_item, $table_caption, $table_summary);

	$head=array($lang->def("_COURSE_NAME"), $lang->def("_USER"));


	$head[]=$lang->def("_CONFIRM");

	$head_type=array('', '', 'image');

	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$data_info=$pay->getProductsList(FALSE, FALSE, "product_id IN (".implode(",", $full_product).")");
	$data_arr=$data_info["data_arr"];
	$user_info=$data_info["user"];
	$db_tot=$data_info["data_tot"];

	$tot=count($data_arr);
	for($i=0; $i<$tot; $i++ ) {

		$rowcnt=array();

		$id=$data_arr[$i]["product_id"];

		$rowcnt[]=$data_arr[$i]["name"];

		$user_id=$data_arr[$i]["id_user"];
		$rowcnt[]=$user_info[$user_id];

		switch ($data_arr[$i]["type"]) {
			case "course": {
				$course_id=substr($data_arr[$i]["id_prod"], strlen("course_"));
				$edition_id=FALSE;
			} break;
			case "course_edition": {
				$course_id=FALSE;
				$edition_id=(int)substr($data_arr[$i]["id_prod"], strlen("course_edition_"));
			} break;
		}


		$rowcnt[]=$form->getCheckBox("", "sel_product_".$id, "sel_product[".$id."]", 1, 1);

		if (($cs->isFull($course_id, $edition_id)) && ($cs->allowOverbooking($course_id, $edition_id))) {
			$tab->addBody($rowcnt);
		}
	}

	$res.=$tab->getTable();

	// ---------------------------------------------------------------------------

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def('_SAVE'));
	$res.=$form->getButton('back_to_transaction', 'back_to_transaction', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.="</div>\n";
	$out->add($res);
}



// Module dispatcher ================================================== //

function transactionDispatch($op) {

	switch($op) {
		case "transaction" : {
			transaction();
		} break ;
		case "modtransaction" : {
			edittransaction();
		} break;
		case "updatetransaction" : {
			if (isset($_POST["undo"]))
				transaction();
			else if (isset($_POST["back_to_transaction"]))
				edittransaction();
			else
				updateTransaction();
		} break;
	}

}

?>
