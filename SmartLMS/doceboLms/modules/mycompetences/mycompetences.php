<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function mycompetences(&$url) {
	checkPerm('view');
		
	$lang =& DoceboLanguage::createInstance('competences', 'lms');
	
	//$out=&$GLOBALS['page'];
  //$out->setWorkingZone('content');
	
	$html = getTitleArea($lang->def('_COMPETENCES'), 'competences')
		.'<div class="std_block">';
	
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	$_man = new Competences_Manager();
		
	$user = getLogUserId();
	$data = $_man->GetAllUserCompetences($user);
	//echo print_r($data, true);//debug
	//*******************
	
	//formats the allUserCompetences array, insert categories
    
    //$comp=$_man->GetAllUserCompetences($id_user);
    
    $data2=array();
    foreach ($data as $key=>$value) {
     if ($value['id_competence']!='') {
      $compdat = $_man->GetCompetence($value['id_competence']);
      $cat = $compdat['category'];
      $temp=array();
    	$temp['name'] = $compdat['name'];
    	$temp['type'] = $compdat['type'];
    	$temp['required'] = isset($value['idst']) ? ($value['idst']!='' ? 'yes' : 'no') : 'no';
    	$temp['score_init'] = (isset($value['score_init']) ? $value['score_init'] : '');
    	$temp['score_got'] = (isset($value['score_got']) ? $value['score_got'] : '');
    	if ($temp['score_init']=='' && $temp['score_got']=='')
        $temp['score_total'] = '';
      else
        $temp['score_total'] = $temp['score_init'] + $value['score_got'];
    	$data2[$cat]['competences'][]=$temp;
     }
    }
    
    foreach ($data2 as $key=>$value) {
      $catdat = $_man->GetCompetenceCategory($key);
      $data2[$key]['category'] = $catdat['name'];
    }
    
    
	
	
	
	//*******************
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$tab=new TypeOne($GLOBALS['visuItem'],$lang->def('_COMPETENCES_USER_TAB_CAPTION'),'summary');
	
	$colstyle = array('', '','image','image','image','image','image');
	$colhead  = array($lang->def('_CATEGORY'),
	                  $lang->def('_COMPETENCES_NAME'),
	                  $lang->def('_COMPETENCES_REQUIRED'),
                    $lang->def('_TYPE'),                    
                    $lang->def('_COMPETENCES_SCORE_INIT'),
                    $lang->def('_COMPETENCES_SCORE_GOT'),
                    $lang->def('_COMPETENCES_SCORE_TOTAL'));
	
	$tab->setColsStyle($colstyle);
	$tab->addHead($colhead);
	foreach ($data2 as $key=>$value) {
	 /*if ($value['id_competence']!='') {
    $comp=$_man->GetCompetence($value['id_competence']);

	  $trow=array();
	  $trow[]=$comp['name'];
	  $trow[]=$comp['type'];
    $trow[]=($value['idst']!='' ? 'yes' : 'no');
    $trow[]=$value['score_init'];
    $trow[]=$value['score_got'];
    $trow[]=$value['score_init']+$value['score_got'];
    
    $tab->addBody($trow);
   }*/
    
    $trow=array();
    $trow[]=$value['category'];
	  $trow[]='';
	  $trow[]='';
    $trow[]='';
    $trow[]='';
    $trow[]='';
    $trow[]='';
    $tab->addBody($trow, 'line');
    
    $i=0;
    foreach ($value['competences'] as $key2=>$value2) {
     //if ($value2['required']=='yes') { //filter for required, exclude not required competences
      $trow=array();
      $trow[]='';
      $trow[]=$value2['name'];
      $trow[]=$value2['required'];
      $trow[]=$value2['type'];
      
      switch ($value2['type']) {
          case 'flag': {
            $scoreinit='-';
            $scoregot='-';
            $scoretotal=(isset($value2['score_got']) ? $lang->def('_COMPETENCE_ACQUIRED') : '-');
          } break;
          case 'score': { 
            $scoreinit  = $value2['score_init'];
            $scoregot   = $value2['score_got'];
            $scoretotal = $value2['score_total']; 
          } break;
        }
      $trow[]=( $scoreinit  == '' ? '0' : $scoreinit  );
      $trow[]=( $scoregot   == '' ? '0' : $scoregot   );
      $trow[]=( $scoretotal == '' ? '0' : $scoretotal );
      /*$trow[]=($value['idst']!='' ? 'yes' : 'no');
      $trow[]=$value['score_init'];
      $trow[]=$value['score_got'];
      $trow[]=$value['score_init']+$value['score_got'];*/
      $tab->addBody($trow, 'line-'.($i%2>0 ? 'cl1' : 'cl2'));
      $i++;
     //} 
    }
   
  }
	$html .= $tab->getTable();
	$html .= '</div>';
	
	$GLOBALS['page']->add($html, 'content');
}


// ================================================================================

function mycompetencesDispatch($op) {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url =& UrlManager::getInstance('competences');
	$url->setStdQuery('modname=mycompetences&op=mycompetences');
	
	switch($op) {
		
		case "mycompetences" :
		default : {
			mycompetences($url);
		}
	}
	
}

?>