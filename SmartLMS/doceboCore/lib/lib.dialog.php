<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2008													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function addDialogLibraries() {
	addYahooJs(array(
		'button' 	=> 'button-min.js',
		'container'	=> 'container-min.js',
		'selector' 	=> 'selector-beta-min.js'
	), array(
		'container/assets/skins/sam' => 'container.css',
		'button/assets/skins/sam' => 'button.css'
	));
	
	//add js file for courses
	addJs($GLOBALS['where_framework_relative'].'/lib/', 'lib.dialog.js');
}



/*
	params:
	- $formId : id of the form which contains the delete elements;
	- $dialogFormAction: action of the form created inside the dialogbox, who will submit the delete action;
	- $elementsFilter: string for yui Selector, select the delete inputs and then append events to them;
	- $title: string, the title of the dialogbox which will be displayed in the caption;
	- $okButton: string, the text of the submit button;
	- $cancelbutton: string, the text of the undo button;
	- $composeBody: string, a JS script with a function which will return the content of the dialogbox, in form of string;
	- $idFilter: string, a filter used to extract the numeric id of the data to delete from the id of the delete input element;
	- $idParamName: name of the input parameter in the submit form which will contain the ID of the data to delete;
	- $confirmParamName: name of the input parameter in the submit form which will contain the confirmation of the delete action;
	- $other: other optional parameters (not yet used)
*/
function setupFormDialogBox(
	$formId,
	$dialogFormAction,
	$elementsFilter,
	$title,
	$okButton,
	$cancelButton,
	$composeBody,
	$idFilter,
	$idParamName,
	$confirmParamName,
	$other = array() )
{	
	addDialogLibraries();
	
	$params = '{'.
		'formId: "'.$formId.'", '."\n".
		'dialogFormAction: "'.$dialogFormAction.'", '."\n".
		'elementsFilter: "'.$elementsFilter.'", '."\n".
		'title: "'.$title.'", '."\n".
		'okButton: "'.$okButton.'", '."\n".
		'cancelButton: "'.$cancelButton.'", '."\n".
		'composeBody: '.$composeBody.', '."\n".
		'idFilter: "'.$idFilter.'", '."\n".
		'idParamName: "'.$idParamName.'", '."\n".
		'confirmParamName: "'.$confirmParamName.'" '."\n";
	$temp=array();
	foreach ($other as $key=>$val) {
		if ($key!='' && !is_int($key)) {
			$temp[] = $key.': '.(is_string($val) ? '"'.$val.'"' : $val);
		}
	}		
	if (count($temp)>0) $params .= implode(', '."\n", $temp);
	$params .= '}';
		
	$script = 'YAHOO.util.Event.onDOMReady(initDialogForm, '.$params.', true);';
	
	cout('<script type="text/javascript">'.$script.'</script>', 'page_head');
}


/*
	params:
	- $elementsFilter: string for yui Selector, select the delete inputs and then append events to them;
	- $title: string, the title of the dialogbox which will be displayed in the caption;
	- $okButton: string, the text of the submit button;
	- $cancelbutton: string, the text of the undo button;
	- $composeBody: string, a JS script with a function who will return the content of the dialogbox, in form of string;
*/
function setupHrefDialogBox(
	$elementsFilter, 
	$title = false, 
	$okButton = false, 
	$cancelButton = false, 
	$composeBody = false )
{
	addDialogLibraries();
	
	if($title == false) $title = def('_AREYOUSURE');
	if($okButton == false) $okButton = def('_CONFIRM');
	if($cancelButton == false)	$cancelButton = def('_UNDO');
	if($composeBody == false) $composeBody = " 
		function (o) { 
			if((o.title).match(':')) return (o.title).replace(/:/, ':<b>') + '<b>'
			return o.title;
		 } ";
	
	$params = '{'.
		'elementsFilter: "'.$elementsFilter.'", '."\n".
		'title: "'.$title.'", '."\n".
		'okButton: "'.$okButton.'", '."\n".
		'cancelButton: "'.$cancelButton.'", '."\n".
		'composeBody: '.$composeBody.' '."\n".
	'}';
		
	$script = 'YAHOO.util.Event.onDOMReady(initDialogHref, '.$params.', true);';
	
	cout('<script type="text/javascript">'.$script.'</script>', 'page_head');
}




function setupSimpleFormDialogBox(
  $formId,
  $elementsFilter,
  $composeBody=false)
{
  if($composeBody == false) $composeBody = " 
		function (o) { 
			if((o.title).match(':')) return (o.title).replace(/:/, ':<b>') + '<b>'
			return o.title;
		 } ";

  $params = '{'.
    'formId: "'.$formId.'", '."\n".
		'elementsFilter: "'.$elementsFilter.'", '."\n".
		'title: "'.def('_AREYOUSURE').'", '."\n".
		'okButton: "'.def('_CONFIRM').'", '."\n".
		'cancelButton: "'.def('_UNDO').'", '."\n".
		'composeBody: '.$composeBody.' '."\n".
	'}';
		
	$script = 'YAHOO.util.Event.onDOMReady(initDialogFormSimple, '.$params.', true);';
	
	cout('<script type="text/javascript">'.$script.'</script>', 'page_head');
}

?>