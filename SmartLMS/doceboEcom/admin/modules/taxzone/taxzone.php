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


function taxzone() {

	// read from database the zones available

	// display into a typeone the zones available  with these operation : modify,
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');


	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_payaccount
	$lang 		=& DoceboLanguage::createInstance('admin_taxzone', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$tb	= new TypeOne(20, $lang->def('_TAXZONE_CAPTION'), $lang->def('_TAXZONE_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=taxzone&amp;op=taxzone");
	$ini=$tb->getSelectedElement();

	//search query of taxzones
	$query_tax_zone = "
	SELECT id_zone, name_zone
	FROM ".$GLOBALS['prefix_ecom']."_tax_zone
	ORDER BY id_zone
	LIMIT ".$ini.", 20"; //.$GLOBALS['lms']['visuItem'];

	$query_tax_zone_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_ecom']."_tax_zone";

	$re_tax_zone = mysql_query($query_tax_zone);
	list($tot_tax_zone) = mysql_fetch_row(mysql_query($query_tax_zone_tot));


	$type_h = array('image', 'news_short_td');
	$cont_h	= array(
	$lang->def('_ZONE'),
	$lang->def('_DESCRIPTION')
	);
	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_TAXZONE').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';


	}

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_zone, $name_zone) = mysql_fetch_row($re_tax_zone)) {

		$cont = array(
			$lang->def($name_zone),
			$lang->def('_DESCRIPTION'.$name_zone)
		);
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=taxzone&amp;op=modtaxzone&amp;modtaxzone='.$id_zone.'"'
						.'title="'.$lang->def('_TITLE_MOD_TAXZONE').' : '.$lang->def($name_zone).'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$lang->def($name_zone).'" /></a>';


		}
		$tb->addBody($cont);
	}

	$out->add(getTitleArea($lang->def('_TITLE_ZONE'), 'taxzone', $lang->def('_ALT_TITLE_TAXZONE'))
			.'<div class="std_block">'	);
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_tax_zone).'</div>');

}


function modtaxzone() {

	// read from database the zones available

	// display into a typeone the zones available  with these operation : modify,
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$this_tax_zone=importVar('modtaxzone');
	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_payaccount
	$lang 		=& DoceboLanguage::createInstance('admin_taxzone', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form= new Form();
	$tb	= new TypeOne(20, $lang->def('_TAXZONE_COUNTRY_CAPTION'), $lang->def('_TAXZONE_COUNTRY_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=taxzone&amp;op=modtaxzone&amp;modtaxzone=".$this_tax_zone."");
	$ini=$tb->getSelectedElement();

	if(isset($_POST['country_filter_code']) && ($_POST['country_filter_code'] != '')) {
			$query_filter .= " AND iso_code_country LIKE '%".$_POST['country_filter_code']."%'";
		}
		if(isset($_POST['country_filter_name']) && ($_POST['country_filter_name'] != '')) {
			$query_filter .= " AND name_country LIKE '%".$_POST['country_filter_name']."%'";
		}
		if(isset($_POST['country_this_zone']) && ($_POST['country_this_zone'] != '')) {
			$query_filter .= " AND id_zone = '".$_POST['id_zone']."'";
		}
		if(isset($_POST['country_not_assigned']) && ($_POST['country_not_assigned'] != '')) {
			$query_filter .= " AND id_zone = '0'";
		}
	$query_name_zone="SELECT
	name_zone
	FROM ".$GLOBALS['prefix_ecom']."_tax_zone
	WHERE id_zone='".$this_tax_zone."'
	";

	if (!isset($query_filter))
		$query_filter="";

	list($name_zone)=mysql_fetch_row(mysql_query($query_name_zone));
	//search query of taxzones
	$query_tax_zone_country = "
	SELECT id_country, name_country,iso_code_country,id_zone
	FROM ".$GLOBALS['prefix_fw']."_country
	WHERE 1 ".$query_filter."
	ORDER BY name_country
	LIMIT ".$ini.", 20"; //.$GLOBALS['lms']['visuItem'];

	$query_tax_zone_country_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_fw']."_country
	WHERE 1".$query_filter;

	$re_tax_zone_country = mysql_query($query_tax_zone_country);

	list($tot_tax_zone) = mysql_fetch_row(mysql_query($query_tax_zone_country_tot));


	$type_h = array('image', 'news_short_td');
	$cont_h	= array(
	$lang->def('_ISO_CODE_COUNTRY'),
	$lang->def('_NAME_COUNTRY'));

	if($mod_perm) {
		$cont_h[] =  '';
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
			$cont[] = ($this_tax_zone == $id_zone || $id_zone == 0 ) ? $form->getCheckbox('', 'country_to_zone['.$id_country.']', 'country_to_zone['.$id_country.']',$this_tax_zone,$id_zone == $this_tax_zone)
			:'<img src="'.getPathImage().'standard/error.gif" alt="'.$lang->def('_ALT_ASSIGNED').'" />'
			;

		}
		$tb->addBody($cont);

	}

	if($mod_perm) {
		$tb->addActionAdd($form->openButtonSpace()
			.$form->getButton('taxzone', 'taxzone', ($lang->def('_SAVE')) )
			.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			.$form->closeForm()
		);
	}
	$page_title = array(
		'index.php?modname=taxzone&amp;op=taxzone' => $lang->def('_TITLE_ZONE_COUNTRY'),
		$lang->def('_TITLE_ZONE_COUNTRY').' : '.$lang->def($name_zone)
	);

	$out->add(getTitleArea($page_title, 'taxzone_country', $lang->def('_ALT_TITLE_TAXZONE_COUNTRY'))
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=taxzone&amp;op=taxzone', $lang->def('_BACK') )
		.$form->openForm('country_list', 'index.php?modname=taxzone&amp;op=modtaxzone')
		.$form->getOpenFieldset($lang->def('_FILTER'))
		.$form->getHidden('id_zone','id_zone',$this_tax_zone)
		.$form->getTextfield($lang->def('_FILTER_ISO_CODE'), 'country_filter_code', 'country_filter_code', '3',
			( isset($_POST['country_filter_code']) ? $_POST['country_filter_code'] : '' ))
		.$form->getTextfield($lang->def('_NAME'), 'country_filter_name', 'country_filter_name', '255',
			( isset($_POST['country_filter_name']) ? $_POST['country_filter_name'] : '' ))
		.$form->getCheckbox($lang->def('_FILTER_THIS_ZONE'), 'country_this_zone', 'country_this_zone', '1', ( isset($_POST['country_this_zone']) ? $_POST['country_this_zone'] : false ) )
		.$form->getCheckbox($lang->def('_FILTER_NOT_ASSIGNED'), 'country_not_assigned', 'country_not_assigned', '1', ( isset($_POST['country_not_assigned']) ? $_POST['country_not_assigned'] : false ) )
		.$form->openButtonSpace()
		.$form->getButton('filter', 'filter', $lang->def('_SEARCH'), 'button_nowh')
		.$form->closeButtonSpace()
		.$form->getCloseFieldset()
		.$form->closeForm()

	);


	$out->add(

			$form->openForm('updtaxzone', 'index.php?modname=taxzone&amp;op=updtaxzone')
			.$form->getHidden('id_zone','id_zone',$this_tax_zone));

		//filter---------------------------------------------------------------------



	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_tax_zone).'</div>');


}


function edittaxcountry($load = false) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang 		=& DoceboLanguage::createInstance('admin_taxzone', 'ecom');
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
		'index.php?modname=taxzone&amp;op=taxzone' => $lang->def('_TITLE_ZONE_COUNTRY'),
		$lang->def('_TITLE_ZONE_COUNTRY')
	);
	$out->add(getTitleArea($page_title, 'certificate', $lang->def('_ALT_TITLE_TAXZONE_COUNTRY'))
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=taxzone&amp;op=taxzone', $lang->def('_BACK') )

			.$form->openForm('modcountryform', 'index.php?modname=taxzone&amp;op=savetaxcountry')
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
			.$form->getButton('taxzone', 'taxzone', ( $load ? $lang->def('_SAVE') : $lang->def('_INSERT') ) )
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
	$lang 		=& DoceboLanguage::createInstance('admin_taxzone', 'ecom');

	if($load == 1) {

		$query_insert = "
		UPDATE ".$GLOBALS['prefix_fw']."_country
		SET iso_code_country = '".$_POST['iso_code_country']."',
		name_country = '".$_POST['name_country']."'
		WHERE id_country = '".$id_country."'";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=taxzone&op=taxzone&result=err');
		jumpTo('index.php?modname=taxzone&op=taxzone&result=ok');

	} else {

		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_fw']."_country
		(iso_code_country, name_country) VALUES
		( 	'".$_POST['iso_code_country']."' ,
			'".$_POST['name_country']."'
			)";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=taxzone&op=taxzone&result=err');
		jumpTo('index.php?modname=taxzone&op=taxzone&result=ok');

	}
}


function deltaxcountry() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$id_country 	= importVar('id_country', true, 0);
	$lang 		=& DoceboLanguage::createInstance('admin_taxzone', 'ecom');

	if(isset($_POST['confirm'])) {

		$query_country = "
		DELETE FROM ".$GLOBALS['prefix_fw']."_country
		WHERE id_country = '".$id_country."'";
		if(!mysql_query($query_country)) jumpTo('index.php?modname=taxzone&op=taxzone&result=err_del');
		else jumpTo('index.php?modname=taxzone&op=taxzone&result=ok');
	} else {

		list($name_country) = mysql_fetch_row(mysql_query("
		SELECT name_country
		FROM ".$GLOBALS['prefix_fw']."_country
		WHERE id_country = '".$id_country."'"));

		$form = new Form();
		$page_title = array(
			'index.php?modname=taxzone&amp;op=taxzone' => $lang->def('_TITLE_ZONE_COUNTRY'),
			$lang->def('_DEL_TAX_COUNTRY')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_taxzone')
			.'<div class="std_block">'
			.$form->openForm('del_country', 'index.php?modname=taxzone&amp;op=deltaxcountry')
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

function updtaxzone($countries_id) {
	$this_tax_zone=importVar('id_zone');
	// select countries associated to this zone
	$query_actual_country="SELECT id_country
	FROM ".$GLOBALS['prefix_fw']."_country
	WHERE id_zone  = '$this_tax_zone'";

	$re_country=mysql_query($query_actual_country);
	$countries_actual_id=array();
	while (list($cid) = mysql_fetch_row($re_country)){
			$countries_actual_id[$cid]=$this_tax_zone;
	}

	foreach ($countries_actual_id as $key => $value) {
		if(array_key_exists($key,$countries_id)==FALSE){
			$query_update="
				UPDATE ".$GLOBALS['prefix_fw']."_country
				SET id_zone = '0'
				WHERE id_country ='".$key."'";
				mysql_query($query_update);

		}

	}
	//var_dump($countries_actual_id);
	foreach ($countries_id as $id_country => $id_zone) {
		if(array_key_exists($id_country,$countries_actual_id)) {
		}
			else if(!array_key_exists($id_country,$countries_actual_id)) {
				$query_update_country = "UPDATE ".$GLOBALS['prefix_fw']."_country
				SET id_zone = '".$id_zone."'
				WHERE id_country ='".$id_country."'";
				mysql_query($query_update_country);
				//echo $query_actual_country;
			}
	}
	jumpTo('index.php?modname=taxzone&op=taxzone&result=ok');


}

// Module dispatcher ================================================== //

function taxzoneDispatch($op) {

	if(isset($_POST['undo'])) $op = 'taxzone';
	//if(isset($_POST['save'])) $op = 'updtaxzone';
	switch($op) {
		case "taxzone" : {
			taxzone();
		};break;
		case "modtaxzone" : {
			modtaxzone();
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
		case "updtaxzone": {
			updtaxzone($_POST['country_to_zone']);
		}
	}

}

?>
