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



function taxcatgod() {

	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_payaccount
	$lang 		=& DoceboLanguage::createInstance('admin_taxcatgod', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form= new Form();
	$tb	= new TypeOne(20, $lang->def('_MANAGEMENT_TAX_CATGOD_CAPTION'), $lang->def('_MANAGEMENT_TAX_CATGOD_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=taxcatgod&amp;op=taxcatgod");
	$ini=$tb->getSelectedElement();
	$query_filter ="";
	if(isset($_POST['taxcatgod_filter_name']) && ($_POST['taxcatgod_filter_name'] != '')) {
			$query_filter .= " AND name_cat_god LIKE '%".$_POST['taxcatgod_filter_name']."%'";
	}

	//search query of taxcatgods
	$query_tax_cat_god = "
	SELECT id_cat_god, name_cat_god, cat_code
	FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
	WHERE 1".$query_filter."
	ORDER BY name_cat_god
	LIMIT ".$ini.", 20"; //.$GLOBALS['lms']['visuItem'];

	$query_tax_cat_god_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
	WHERE 1 ".$query_filter."";

	$re_tax_cat_god = mysql_query($query_tax_cat_god);
	list($tot_tax_cat_god) = mysql_fetch_row(mysql_query($query_tax_cat_god_tot));

	$type_h = array('news_short_td');
	$cont_h	= array($lang->def('_NAME_CAT_GOD'));

	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_TAX_CAT_GOD').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
			$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DEL_TAX_CAT_GOD').'" '
						.'alt="'.$lang->def('_DEL').'"" />';
		$type_h[] = 'image';

	}

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_cat_god, $name_cat_god, $cat_code) = mysql_fetch_row($re_tax_cat_god)) {
		$cont = array(
			$name_cat_god
		);
		if($mod_perm && $cat_code == NULL) {
			$cont[] = '<a href="index.php?modname=taxcatgod&amp;op=modtaxcatgod&amp;id_cat_god='.$id_cat_god.'"'
						.'title="'.$lang->def('_TITLE_MOD_TAX_CAT_GOD').' : '.$name_cat_god.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$name_cat_god.'" /></a>';

	$cont[] = '<a href="index.php?modname=taxcatgod&amp;op=deltaxcatgod&amp;id_cat_god='.$id_cat_god.'" '
						.'title="'.$lang->def('_TITLE_DEL_TAX_CAT_GOD').' : '.$name_cat_god.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$name_cat_god.'" /></a>';
		}
		else {
			$cont[]="&nbsp;";
			$cont[]="&nbsp;";
		}
		$tb->addBody($cont);

	}
	if($mod_perm) {
		$tb->addActionAdd(
			'<a href="index.php?modname=taxcatgod&amp;op=addtaxcatgod" title="'.$lang->def('_TITLE_NEW_TAX_CAT_GOD').'">'
				.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_NEW_TAX_CAT_GOD').'</a>'
		);
	}

	$out->add(getTitleArea($lang->def('_TITLE_MANAGEMENT_TAX_CAT_GOD'), 'tax_cat_god', $lang->def('_ALT_TITLE_TAX_CAT_GOD'))
			.'<div class="std_block">'

		.$form->openForm('taxcatgod_list', 'index.php?modname=taxcatgod&amp;op=taxcatgod')
		.$form->getOpenFieldset($lang->def('_FILTER'))
		.$form->getHidden('id_cat_god','id_cat_god',(isset($this_tax_zone) ? $this_tax_zone : 0))
		.$form->getTextfield($lang->def('_NAME'), 'taxcatgod_filter_name', 'taxcatgod_filter_name', '255',
			( isset($_POST['taxcatgod_filter_name']) ? $_POST['taxcatgod_filter_name'] : '' ))
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

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_tax_cat_god).'</div>');


}


function getTaxCatCodes() {
	$res=array();

	$lang=& DoceboLanguage::createInstance('admin_taxcatgod', 'ecom');

	$res[0]=$lang->def("_NONE");
	$res["course"]=$lang->def("_COURSE");

	return $res;
}



function edittaxcatgod($load = false) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang 		=& DoceboLanguage::createInstance('admin_taxcatgod', 'ecom');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$id_cat_god = importVar('id_cat_god', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();

	if($load) {
/*
		$query_cat_god = "
		SELECT name_cat_god, cat_code
		FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
		WHERE id_cat_god = '".$id_cat_god."'";

		list($name_cat_god, $sel_cat_code) = mysql_fetch_row(mysql_query($query_cat_god)); */

		$query_cat_god = "
		SELECT name_cat_god
		FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
		WHERE id_cat_god = '".$id_cat_god."'";

		list($name_cat_god) = mysql_fetch_row(mysql_query($query_cat_god));
	} else {

		$name_cat_god =  '';
		//$sel_cat_code = 0;

	}

	$page_title = array(
		'index.php?modname=taxcatgod&amp;op=taxcatgod' => $lang->def('_TITLE_MANAGEMENT_TAX_CAT_GOD'),
		$load ? ($lang->def('_TITLE_MODIFY_TAX_CAT_GOD').' : '.$name_cat_god) : $lang->def('_TITLE_NEW_TAX_CAT_GOD')
	);
	$out->add(getTitleArea($page_title, 'tax_cat_god', $lang->def('_ALT_TITLE_TAX_CAT_GOD'))
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=taxcatgod&amp;op=taxcatgod', $lang->def('_BACK') )

			.$form->openForm('mod_cat_god_form', 'index.php?modname=taxcatgod&amp;op=savetaxcatgod')
	);
	if($load) {

		$out->add($form->getHidden('load', 'load', 1)	);
	}

	$cat_codes=getTaxCatCodes();

	$out->add($form->openElementSpace()
			.$form->getTextfield($lang->def('_NAME_CAT_GOD'), 'name_cat_god', 'name_cat_god', 255, $name_cat_god)
			//.$form->getDropdown($lang->def('_CAT_CODE'), 'cat_code', 'cat_code', $cat_codes, $sel_cat_code)
			.$form->getHidden('id_cat_god','id_cat_god',$id_cat_god)
			.$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('taxcatgod', 'taxcatgod', ( $load ? $lang->def('_SAVE') : $lang->def('_INSERT') ) )
			.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			.$form->closeForm()
			.'</div>');


}


function savetaxcatgod() {
	checkPerm('mod');

	$id_cat_god 	= importVar('id_cat_god', true, 0);
	$load 		= importVar('load', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	$lang 		=& DoceboLanguage::createInstance('admin_taxcatgod', 'ecom');

/*	if ((isset($_POST['cat_code'])) && (!empty($_POST['cat_code']))) {
		$cat_code=$_POST['cat_code'];

		$qtxt ="UPDATE  ".$GLOBALS['prefix_ecom']."_tax_cat_god SET ";
		$qtxt.="cat_code=NULL WHERE cat_code='".$cat_code."'";

		mysql_query($qtxt);
	}
	else {
		$cat_code="";
	} */

	if($load == 1) {

		$query_insert = "
		UPDATE ".$GLOBALS['prefix_ecom']."_tax_cat_god
		SET name_cat_god = '".$_POST['name_cat_god']."', ".
//		cat_code=".(!empty($cat_code) ? "'".$cat_code."'" : "NULL")."
		"WHERE id_CAT_GOD = '".$id_cat_god."'";
		if(!mysql_query($query_insert)) {
			jumpTo('index.php?modname=taxcatgod&op=taxcatgod&result=err');
		}
		else {
			jumpTo('index.php?modname=taxcatgod&op=taxcatgod&result=ok');
		}

	} else {

/*		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_ecom']."_tax_cat_god
		(name_cat_god, cat_code) VALUES
		('".$_POST['name_cat_god']."', ".(!empty($cat_code) ? "'".$cat_code."'" : "NULL")."
			)"; */

		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_ecom']."_tax_cat_god
		(name_cat_god) VALUES
		('".$_POST['name_cat_god']."'
			)";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=taxcatgod&op=taxcatgod&result=err');
		jumpTo('index.php?modname=taxcatgod&op=taxcatgod&result=ok');

	}
}


function deltaxcatgod() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$id_cat_god 	= importVar('id_cat_god', true, 0);
	$lang 		=& DoceboLanguage::createInstance('admin_taxcatgod', 'ecom');

	if(isset($_POST['confirm'])) {

		$query_cat_god = "
		DELETE FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
		WHERE id_cat_god = '".$id_cat_god."'";
		if(!mysql_query($query_cat_god)) jumpTo('index.php?modname=taxcatgod&op=taxcatgod&result=err_del');
		else jumpTo('index.php?modname=taxcatgod&op=taxcatgod&result=ok');
	} else {

		list($name_cat_god) = mysql_fetch_row(mysql_query("
		SELECT name_cat_god
		FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
		WHERE id_cat_god = '".$id_cat_god."'"));

		$form = new Form();
		$page_title = array(
			'index.php?modname=taxcatgod&amp;op=taxcatgod' => $lang->def('_TITLE_MANAGEMENT_TAX_CAT_GOD'),
			$lang->def('_DEL_TAX_CAT_GOD')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_taxcatgod')
			.'<div class="std_block">'
			.$form->openForm('del_cat_god', 'index.php?modname=taxcatgod&amp;op=deltaxcatgod')
			.$form->getHidden('id_cat_god', 'id_cat_god', $id_cat_god)
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span>'.$lang->def('_NAME_CAT_GOD').' : </span>'.$name_cat_god.'<br />'
							,
							false,
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}


// Module dispatcher ================================================== //

function taxcatgodDispatch($op) {

	if(isset($_POST['undo'])) $op = 'taxcatgod';

	switch($op) {
		case "taxcatgod" : {
			taxcatgod();
		};break;
		case "modtaxcatgod" : {
			 edittaxcatgod(1);
		};break;
		case "savetaxcatgod" : {
			savetaxcatgod();
		};break;
		case "addtaxcatgod" : {
			edittaxcatgod();
		};break;
		case "deltaxcatgod" : {
			deltaxcatgod();
		};break;
	}

}

?>
