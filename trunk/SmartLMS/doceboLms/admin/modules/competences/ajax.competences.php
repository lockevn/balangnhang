<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
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

require_once($GLOBALS['where_lms_relative'].'/lib/lib.competences.php');

function DeleteCategory() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  $_man = new Competences_Manager();
  $id_cat=importVar('id_cat',true,0);
  
  $count=$_man->GetCompetencesCount($id_cat);
  $cname=$_man->GetCompetenceCategory($id_cat);

  $output=array();
	$output['head']=$lang->def('_DEL', 'standard', 'framework').': "'.$cname['name'].'"';
  
  require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

  if ($count>0) {
    $list=$_man->GetCompetencesCategoriesList(true);
    unset($list[$id_cat]);
    $output['body']=$count.' '.$lang->def('_CATEGORY_ASK_CONFIRM').
                    Form::getDropDown('', 'move_cat', 'move_cat',$list , false).
                    Form::getHidden('id_cat', 'id_cat', $id_cat);
  } else {
    $output['body']=$lang->def('_CATEGORY_ASK_CONFIRM_NONE').
                    Form::getHidden('move_cat', 'move_cat', 0).
                    Form::getHidden('id_cat', 'id_cat', $id_cat);
  }
  $json=new Services_JSON();
  docebo_cout($json->encode($output));
}

function DeleteCompetence() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
	$_man = new Competences_Manager();
	$id_comp=importVar('id_comp',true,0);
	
	$cname=$_man->GetCompetence($id_comp);
	
	$clist=$_man->GetCompetenceCoursesCount($id_comp);
	$ulist=$_man->GetCompetenceUsersCount($id_comp);
	$rlist=$_man->GetCompetenceRequiredCount($id_comp);
	
	$output=array();
	$output['head']=$lang->def('_DEL_COMPETENCE').': "'.$cname['name'].'"';
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	if (($clist+$ulist+$rlist)>0) {
    $output['body']='<p>'.$lang->def('_COMPETENCE_ASK_CONFIRM').':</p><ul>'.
                    ($clist>0 ? '<li>'.$clist.' '.$lang->def('_COURSES').'</li>' : '').
                    ($ulist>0 ? '<li>'.$ulist.' '.$lang->def('_USERS').'</li>' : '').
                    ($rlist>0 ? '<li>'.$rlist.' '.$lang->def('_REQUIRED').'</li>' : '').'</ul><p>'.$lang->def('_COMPETENCE_ASK_WARNING').'</p>'.
                    Form::getHidden('id_comp', 'id_comp', $id_comp);
  } else {
    $output['body']=$lang->def('_COMPETENCE_ASK_CONFIRM_NONE').Form::getHidden('id_comp', 'id_comp', $id_comp);
  }
  
  $json=new Services_JSON();
  docebo_cout($json->encode($output));
  //if confirmed, delete data from database
}

function ConfirmDeleteCategory() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  $_man = new Competences_Manager();
  $output=array();
  
  $output['success']=$_man->DeleteCategory($_POST['id_cat'],$_POST['move_cat']);
  if (!$output['success']) {
    $output['message']='<span>'.$lang->def('_CATEGORY_ERROR_DELETE').'.</span>';
    //$output['foot']='<button class="button" onclick="cat_panel.hide();">'.$lang->def('_CONFIRM').'</button>';
  }
  
  $json=new Services_JSON();
  docebo_cout($json->encode($output));
}

function ConfirmDeleteCompetence() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  $_man = new Competences_Manager();
  $output=array();
  
  $output['success']=$_man->DeleteCompetence($_POST['id_comp']);
  if (!$output['success']) {
    $output['message']='<span>'.$lang->def('_COMPETENCE_ERROR_DELETE').'.</span>';
    //$output['foot']='<button class="button" onclick="comp_panel.hide();">'.$lang->def('_CONFIRM').'</button>';
  }
  
  $json=new Services_JSON();
  docebo_cout($json->encode($output));
}


function UpdateCourseCompetence() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  $_man = new Competences_Manager();
  $output=array();  
  if ($output['success']=$_man->UpdateCourseCompetence($_POST['id_course'],$_POST['id_comp'],$_POST['comp_value'])) {
    $output['new_value']=$_POST['comp_value'];
  } else {
    $output['error']='';
  }
  
  $json=new Services_JSON();
  docebo_cout($json->encode($output));
}

$op=get_req('op', DOTY_ALPHANUM, '');

//request dispatcher
switch ($op) {
  
  case 'confirm_del_category': {
    ConfirmDeleteCategory();
  } break;
  
  case 'confirm_del_competence': {
    ConfirmDeleteCompetence();
  } break;
  
  case 'del_category': {
    DeleteCategory();
  } break;
    
  case 'del_competence': {
    DeleteCompetence();
  } break;
  
  /*case 'test': {
    //$output['content']=serialize($_SESSION)."\n".serialize($_POST);
    $output['content']='Testing functionality. This is yet to implement.';
    $json=new Services_JSON();
    docebo_cout($json->encode($output));  
  } break;*/
  
  case 'update_course_comp': {
    UpdateCourseCompetence();
  } break;
}

?>