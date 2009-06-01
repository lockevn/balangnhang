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


function payaccount() {

	// read from database the account available

	// display into a typeone the account info : (name, description) with these operation : modify, active/deactivate
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');


	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_payaccount
	$lang 		=& DoceboLanguage::createInstance('admin_payaccount', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$tb	= new TypeOne(20, $lang->def('_PAYACCOUNT_CAPTION'), $lang->def('_PAYACCOUNT_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=payaccount&amp;op=payaccount");
	$ini=$tb->getSelectedElement();

	//search query of payaccounts
	$query_payaccount = "
	SELECT account_name, active
	FROM ".$GLOBALS['prefix_ecom']."_payaccount
	ORDER BY account_name
	LIMIT ".$ini.",20"; //.$GLOBALS['lms']['visuItem'];

	$query_payaccount_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_ecom']."_payaccount";

	$re_payaccount = mysql_query($query_payaccount);
	list($tot_payaccount) = mysql_fetch_row(mysql_query($query_payaccount_tot));


	$type_h = array('image', 'news_short_td');
	$cont_h	= array(
	$lang->def('_NAME'),
	$lang->def('_DESCRIPTION')
	);
	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_PAYACCOUNT').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
		$cont_h[] = 'Status';
		$type_h[] = 'image';

	}

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($account_name, $active) = mysql_fetch_row($re_payaccount)) {

		$cont = array(
			$lang->def('_PAYACCOUNT_'.$account_name.'_NAME'),
			$lang->def('_PAYACCOUNT_'.$account_name.'_DESCRIPTION')
		);
		if($mod_perm) {
			if($account_name=='mark') $cont[]='';
			else
			$cont[] = '<a href="index.php?modname=payaccount&amp;op=modpayaccount&amp;account_name='.$account_name.'" '
						.'title="'.$lang->def('_TITLE_MOD_PAYACCOUNT').' : '.$lang->def('_PAYACCOUNT_'.$account_name.'_NAME').'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$lang->def('_PAYACCOUNT_'.$account_name.'_NAME').'" /></a>';
		switch($active)
		{
			case 'true' : {
				$change_status='false';
				$image_activation='active.gif';
				$status=$lang->def('_ACTIVE');
				break;

			}
			case 'false' : {
				$change_status='true';
				$image_activation='deactive.gif';
				$status=$lang->def('_PAYACCOUNT_DEACTIVE');
				break;
			}
		}
			$cont[] = '<a href="index.php?modname=payaccount&amp;op=change_status&amp;account_name='.$account_name.'&amp;change_status='.$change_status.'" '
						.'title="'.$status.'">'
						.'<img src="'.getPathImage().'standard/'.$image_activation.'" alt="'.$lang->def('_DEL').' : '.$status.'" /></a>';
		}
		$tb->addBody($cont);
	}

	$out->add(getTitleArea($lang->def('_TITLE_PAYACCOUNT'), 'payaccount', $lang->def('_ALT_TITLE_PAYACCOUNT'))
			.'<div class="std_block">'	);
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_payaccount).'</div>');

}


function modpayaccount() {

	require_once($GLOBALS['where_ecom'].'/lib/lib.payaccount.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	// read account selected and instanciate class
	$lang 		=& DoceboLanguage::createInstance('admin_payaccount', 'ecom');
	$account_name = importVar('account_name', false, '');

	$obj_pa =& getInstancePayAccount($account_name);
	$out	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$out->add(
		getTitleArea(array($lang->def('_ADMIN_PAYACCOUNT') , $lang->def('_ADMIN_PAYACCOUNT_'.$account_name.'')), 'payaccount')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=payaccount&amp;op=payaccount', $lang->def('_BACK') ));
	if(isset($_POST['save'])) {

		$re = $obj_pa->saveDetails($_POST);
		if ($re=true)
		jumpTo('index.php?modname=payaccount&amp;op=payaccount&amp;result=ok');
		else jumpTo('index.php?modname=payaccount&amp;op=payaccount&amp;result=error');
	}
	$out->add(
		Form::openForm('mod_account', 'index.php?modname=payaccount&amp;op=modpayaccount')

		.Form::openElementSpace()
		.Form::getHidden('account_name', 'account_name', $account_name)
		.$obj_pa->getFormDetails()
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()

		.Form::closeForm()
	, 'content');

}


function changestatus() {

	$account_name = importVar('account_name', false, '');
	$change_status=importVar('change_status');
	$change_status_query= "UPDATE ".$GLOBALS['prefix_ecom']."_payaccount
	SET active = '".$change_status."'
	WHERE account_name = '".$account_name."'";
	echo $change_status_query;
	if(!mysql_query($change_status_query))	{
		jumpTo('index.php?modname=payaccount&amp;op=payaccount&amp;result=error');
	} else {
		jumpTo('index.php?modname=payaccount&amp;op=payaccount&amp;result=ok');
	}

}



// Module dispatcher ================================================== //

function payaccountDispatch($op) {

	if(isset($_POST['undo'])) $op = 'payaccount';
	if(isset($_GET['change_status'])) $op = 'changestatus';
	switch($op) {
		case "payaccount" : {
			payaccount();
		};break;
		case "modpayaccount" : {
			modpayaccount();
		};break;
		case "changestatus" : {
			changestatus();
		};break;
	}

}

?>
