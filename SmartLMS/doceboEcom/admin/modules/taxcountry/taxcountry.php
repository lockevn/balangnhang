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




function taxcountry() {


	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_payaccount
	$lang 		=& DoceboLanguage::createInstance('admin_taxcountry', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form= new Form();
	$tb	= new TypeOne(20, $lang->def('_MANAGEMENT_TAX_COUNTRY_CAPTION'), $lang->def('_MANAGEMENT_TAX_COUNTRY_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=taxcountry&amp;op=taxcountry");
	$ini=$tb->getSelectedElement();

	$query_filter ="";
	if(isset($_POST['country_filter_code']) && ($_POST['country_filter_code'] != '')) {
			$query_filter .= " AND iso_code_country LIKE '%".$_POST['country_filter_code']."%'";
		}
		if(isset($_POST['country_filter_name']) && ($_POST['country_filter_name'] != '')) {
			$query_filter .= " AND name_country LIKE '%".$_POST['country_filter_name']."%'";
		}


	//search query of taxcountrys
	$query_tax_country = "
	SELECT id_country, name_country,iso_code_country,id_zone
	FROM ".$GLOBALS['prefix_fw']."_country
	WHERE 1".$query_filter."
	ORDER BY name_country
	LIMIT ".$ini.",20"; //.$GLOBALS['lms']['visuItem'];

	$query_tax_country_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_fw']."_country
	WHERE 1 ".$query_filter."";

	$re_tax_zone_country = mysql_query($query_tax_country);
	list($tot_tax_country) = mysql_fetch_row(mysql_query($query_tax_country_tot));


	$type_h = array('image', 'news_short_td');
	$cont_h	= array(
	$lang->def('_ISO_CODE_COUNTRY'),
	$lang->def('_NAME_COUNTRY'));

	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_TAX_COUNTRY').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
			$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DEL_TAX_COUNTRY').'" '
						.'alt="'.$lang->def('_DEL').'"" />';
		$type_h[] = 'image';

	}

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_country, $name_country,$iso_code_country,$id_zone) = mysql_fetch_row($re_tax_zone_country)) {

		$cont = array(
			$iso_code_country,
			$name_country
		);
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=taxcountry&amp;op=modtaxcountry&amp;id_country='.$id_country.'"'
						.'title="'.$lang->def('_TITLE_MOD_TAX_COUNTRY').' : '.$name_country.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$name_country.'" /></a>';

	$cont[] = '<a href="index.php?modname=taxcountry&amp;op=deltaxcountry&amp;id_country='.$id_country.'" '
						.'title="'.$lang->def('_TITLE_DEL_TAX_COUNTRY').' : '.$name_country.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$name_country.'" /></a>';
		}
		$tb->addBody($cont);

	}

	if($mod_perm) {
		$tb->addActionAdd(
			'<a href="index.php?modname=taxcountry&amp;op=addtaxcountry" title="'.$lang->def('_TITLE_NEW_TAX_COUNTRY').'">'
				.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_NEW_TAX_COUNTRY').'</a>'
		);
	}


	$out->add(getTitleArea($lang->def('_TITLE_MANAGEMENT_TAX_COUNTRY'), 'tax_country', $lang->def('_ALT_TITLE_TAX_COUNTRY'))
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=taxcountry&amp;op=taxcountry', $lang->def('_BACK') )
		.$form->openForm('country_list', 'index.php?modname=taxcountry&amp;op=taxcountry')
		.$form->getOpenFieldset($lang->def('_FILTER'))
		.$form->getHidden('id_zone','id_zone',(isset($this_tax_zone) ? $this_tax_zone : 0))
		.$form->getTextfield($lang->def('_FILTER_ISO_CODE'), 'country_filter_code', 'country_filter_code', '3',
			( isset($_POST['country_filter_code']) ? $_POST['country_filter_code'] : '' ))
		.$form->getTextfield($lang->def('_NAME'), 'country_filter_name', 'country_filter_name', '255',
			( isset($_POST['country_filter_name']) ? $_POST['country_filter_name'] : '' ))
		.$form->openButtonSpace()
		.$form->getButton('filter', 'filter', $lang->def('_SEARCH'), 'button_nowh')
		.$form->closeButtonSpace()
		.$form->getCloseFieldset()
		.$form->closeForm()

	);



	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_tax_country).'</div>');


}


function edittaxcountry($load = false) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang 		=& DoceboLanguage::createInstance('admin_taxcountry', 'ecom');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$id_country = importVar('id_country', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();

	if($load) {

		$query_country = "
		SELECT id_country,name_country,iso_code_country,id_zone
		FROM ".$GLOBALS['prefix_fw']."_country
		WHERE id_country = '".$id_country."'";

		list($id_country, $name_country,$iso_code_country,$id_zone) = mysql_fetch_row(mysql_query($query_country));
	} else {

		$name_country =  '';
		$iso_code_country = '';

	}

	$page_title = array(
		'index.php?modname=taxcountry&amp;op=taxcountry' => $lang->def('_TITLE_MANAGEMENT_TAX_COUNTRY'),
		$load ? ($lang->def('_TITLE_MODIFY_TAX_COUNTRY').' : '.$name_country) : $lang->def('_TITLE_NEW_TAX_COUNTRY')
	);
	$out->add(getTitleArea($page_title, 'texcountry', $lang->def('_ALT_TITLE_TAX_COUNTRY'))
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=taxcountry&amp;op=taxcountry', $lang->def('_BACK') )

			.$form->openForm('modcountryform', 'index.php?modname=taxcountry&amp;op=savetaxcountry')
	);
	if($load) {

		$out->add($form->getHidden('load', 'load', 1)	);
	}
	$out->add($form->openElementSpace()
			.$form->getTextfield($lang->def('_ISO_CODE_COUNTRY'), 'iso_code_country', 'iso_code_country', 3, $iso_code_country)
			.$form->getTextfield($lang->def('_NAME_COUNTRY'), 'name_country', 'name_country', 255, $name_country)
			.$form->getHidden('id_zone','id_zone',$id_zone)
			.$form->getHidden('id_country','id_country',$id_country)
			.$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('taxcountry', 'taxcountry', ( $load ? $lang->def('_SAVE') : $lang->def('_INSERT') ) )
			.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			.$form->closeForm()
			.'</div>');


}


function savetaxcountry() {
	checkPerm('mod');

	$id_country 	= importVar('id_country', true, 0);
	$load 		= importVar('load', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	$lang 		=& DoceboLanguage::createInstance('admin_taxcountry', 'ecom');

	if($load == 1) {

		$query_insert = "
		UPDATE ".$GLOBALS['prefix_fw']."_country
		SET iso_code_country = '".$_POST['iso_code_country']."',
		name_country = '".$_POST['name_country']."'
		WHERE id_country = '".$id_country."'";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=taxcountry&op=taxcountry&result=err');
		jumpTo('index.php?modname=taxcountry&op=taxcountry&result=ok');

	} else {

		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_fw']."_country
		(iso_code_country, name_country) VALUES
		( 	'".$_POST['iso_code_country']."' ,
			'".$_POST['name_country']."'
			)";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=taxcountry&op=taxcountry&result=err');
		jumpTo('index.php?modname=taxcountry&op=taxcountry&result=ok');

	}
}


function deltaxcountry() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$id_country 	= importVar('id_country', true, 0);
	$lang 		=& DoceboLanguage::createInstance('admin_taxcountry', 'ecom');

	if(isset($_POST['confirm'])) {

		$query_country = "
		DELETE FROM ".$GLOBALS['prefix_fw']."_country
		WHERE id_country = '".$id_country."'";
		if(!mysql_query($query_country)) jumpTo('index.php?modname=taxcountry&op=taxcountry&result=err_del');
		else jumpTo('index.php?modname=taxcountry&op=taxcountry&result=ok');
	} else {

		list($name_country) = mysql_fetch_row(mysql_query("
		SELECT name_country
		FROM ".$GLOBALS['prefix_fw']."_country
		WHERE id_country = '".$id_country."'"));

		$form = new Form();
		$page_title = array(
			'index.php?modname=taxcountry&amp;op=taxcountry' => $lang->def('_TITLE_MANAGEMENT_TAX_COUNTRY'),
			$lang->def('_DEL_TAX_COUNTRY')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_taxcountry')
			.'<div class="std_block">'
			.$form->openForm('del_country', 'index.php?modname=taxcountry&amp;op=deltaxcountry')
			.$form->getHidden('id_country', 'id_country', $id_country)
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span>'.$lang->def('_NAME_COUNTRY').' : </span>'.$name_country.'<br />'
							,
							false,
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}


// Module dispatcher ================================================== //

function taxCountryDispatch($op) {

	if(isset($_POST['undo'])) $op = 'taxcountry';

	switch($op) {
		case "taxcountry" : {
			taxcountry();
		};break;
		case "modtaxcountry" : {
			 edittaxcountry(1);
		};break;
		case "savetaxcountry" : {
			savetaxcountry();
		};break;
		case "addtaxcountry" : {
			edittaxcountry();
		};break;
		case "deltaxcountry" : {
			deltaxcountry();
		};break;
	}

}

?>
