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



function taxrate() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$mod_perm	= checkPerm('mod', true);
	$lang 		=& DoceboLanguage::createInstance('admin_taxrate', 'ecom');
	 DoceboLanguage::createInstance('admin_taxzone', 'ecom');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form= new Form();
	$tb	= new TypeOne(20, $lang->def('_MANAGEMENT_TAX_RATE_CAPTION'), $lang->def('_MANAGEMENT_TAX_RATE_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=taxrate&amp;op=taxrate");
	$ini=$tb->getSelectedElement();
	$out->add(getTitleArea($lang->def('_TITLE_MANAGEMENT_TAX_RATE'), 'tax_rate', $lang->def('_ALT_TITLE_TAX_RATE'))
	.'<div class="std_block">'

	.$form->openForm('taxrate_list', 'index.php?modname=taxrate&amp;op=savetaxrate')


	);
	//search query of taxcatgods
	$query_tax_cat_god = "
	SELECT id_cat_god, name_cat_god
	FROM ".$GLOBALS['prefix_ecom']."_tax_cat_god
	ORDER BY name_cat_god";

	$query_tax_zone = "
	SELECT id_zone, name_zone
	FROM ".$GLOBALS['prefix_ecom']."_tax_zone
	ORDER BY id_zone";

	$re_tax_cat_god = mysql_query($query_tax_cat_god);
	$re_tax_zone = mysql_query($query_tax_zone);

	$query_tax_rate="
	SELECT id_zone, id_cat_god,rate
	FROM ".$GLOBALS['prefix_ecom']."_tax_rate";
	$re_tax_rate=mysql_query($query_tax_rate);
	while(list($id_zone_p,$id_cat_god_p,$rate_p)=mysql_fetch_row($re_tax_rate)){
		$rate_present[$id_cat_god_p][$id_zone_p]=$rate_p;
	}

	$type_h = array('news_short_td');
	$cont_h	= array($lang->def('_NAME_CAT_GOD').'-'.$lang->def('_NAME_ZONE'));
	while(list($id_zone, $name_zone) = mysql_fetch_row($re_tax_zone)) {
		$zone_id[]=$id_zone;
		if($mod_perm) {
			$cont_h[] = $lang->def($name_zone,'admin_taxzone');
			$type_h[] = 'taxrate';
		}
	}
	//var_dump($zone_id);

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_cat_god, $name_cat_god) = mysql_fetch_row($re_tax_cat_god)) {
		$cont = array(
		$name_cat_god
		);
		$cat_id[]=$id_cat_god;

		foreach ($zone_id as $id_zone_text){
			$cont[] = '<label class="access_only" for="tax_rate_'.$id_cat_god.'_'.$id_zone_text.'">'.$lang->def('_ALIQUOTA').'</label> '
.Form::getInputTextfield(  'textfield',
       'tax_rate_'.$id_cat_god.'_'.$id_zone_text,
       'tax_rate['.$id_cat_god.']['.$id_zone_text.']',
       ( isset($rate_present[$id_cat_god][$id_zone_text]) ? $rate_present[$id_cat_god][$id_zone_text] : 0),
       $lang->def('_ALIQUOTA'),
       2,
       '' ).'%';

		}
		$tb->addBody($cont);

	}

	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;

		}
	}

	$out->add($tb->getTable()
	.$form->openButtonSpace()
	.$form->getButton('taxcountry', 'taxcountry', $lang->def('_SAVE'))
	.$form->closeButtonSpace()
	.$form->closeForm().
	'</div>');
}


function savetaxrate() {
	checkPerm('mod');
	$query_tax_rate="
	SELECT id_zone, id_cat_god,rate
	FROM ".$GLOBALS['prefix_ecom']."_tax_rate";
	$re_tax_rate=mysql_query($query_tax_rate);
	while(list($id_zone_p,$id_cat_god_p,$rate_p)=mysql_fetch_row($re_tax_rate)){
		$rate_present[$id_cat_god_p][$id_zone_p]=$rate_p;
	}
	foreach ($_POST['tax_rate'] as $id_cat => $cat_id  ) {

		foreach ($cat_id as $id_zone => $rate){
			//echo 'Categoria '.$id_cat.'<br>';
			//echo 'zona '.$id_zone.'Tasso'.$rate.'<br>';
			if(isset($rate_present[$id_cat][$id_zone]))
			{
				$query_insert = "
				UPDATE ".$GLOBALS['prefix_ecom']."_tax_rate
				SET rate ='".$rate."'
				WHERE id_cat_god = '".$id_cat."'
				AND id_zone ='".$id_zone."'";
				//echo 'Valore presente , query di aggiornamento<br>';
			}else {
				$query_insert = "
				INSERT INTO ".$GLOBALS['prefix_ecom']."_tax_rate
				(id_zone,id_cat_god,rate) VALUES
				('".$id_zone."',
				 '".$id_cat."',
				'".$rate."'
				)";
				//echo 'Valore non presente , query di inserimento<br>';
			}
			if(!mysql_query($query_insert)) jumpTo('index.php?modname=taxcatgod&op=taxrate&result=err');

		}
	}jumpTo('index.php?modname=taxrate&op=taxrate&result=ok');


}


// Module dispatcher ================================================== //

function taxrateDispatch($op) {



	switch($op) {
		case "taxrate" : {
			taxrate();
		};break;
		case "savetaxrate" : {
			savetaxrate();
		};break;
	}

}

?>
