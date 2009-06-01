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

function ShowCompetences($language) {
	checkPerm('view');
 	
 	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	
	$url =& UrlManager::getInstance();
	$url->setStdQuery('modname=competences');
  
	$lang =& DoceboLanguage::createInstance('competences', 'lms');
  
	$out=&$GLOBALS['page'];
	$out->setWorkingZone('content');
  
  
	$tab=new TypeOne($GLOBALS['visuItem'],$lang->def('_COMPETENCES_TAB_CAPTION'),'summary');
	$tab->initNavBar('ini','button');
  
	$img_shw='<img src="'.getPathImage().'standard/policy.gif" title="'.$lang->def("_SHOW_USERS").'" alt="'.$lang->def("_SHOW_USERS").'" />';
	$img_usr='<img src="'.getPathImage().'standard/add_subscribe.gif" title="'.$lang->def("_ASSIGN").'" alt="'.$lang->def("_ASSIGN").'" />';
	$img_obl='<img src="'.getPathImage().'standard/moduser.gif" title="'.$lang->def("_ASSIGN_REQUIRED").'" alt="'.$lang->def("_ASSIGN_REQUIRED").'" />';
	$img_mod='<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def("_MOD").'" alt="'.$lang->def("_MOD").'" />';
	$img_del='<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def("_DEL").'" alt="'.$lang->def("_DEL").'" />';
	  
	$col_type = array('','','image','image','image','image','','image','image','image','image','image');
	$col_content = array($lang->def('_CATEGORY'),
			              $lang->def('_COMPETENCE'),
			              $lang->def('_TYPE'),
			              $lang->def('_COMP_TYPE'),
			              $lang->def('_SCOREMIN'),
			              $lang->def('_SCORE'),
			              $lang->def('_DESCRIPTION'),
			              $img_shw,
			              $img_usr,
			              $img_obl,
			              $img_mod,
			              $img_del);
  
	$tab->setColsStyle($col_type);
	$tab->addHead($col_content);
	
	$mng = new Competences_Manager();
	$cats = $mng->GetCompetencesCategories(true, $language);
	$comps = $mng->GetCompetencesGrouped(true, $language);
	
	$GLOBALS['page']->add('<style>.line_hide { display:none; } .line_show { }</style>', 'page_head');
	
	$tempname='';
	$first = true;
	foreach ($cats as $key=>$value) {
		
		$_cat_id = $value['id_competence_category'];
		
		if ($value['name']!='')
		  $tempname = $value['name'];
		else
		  $tempname = '<i>( '.$lang->def('_CATEGORY_NOT_TRANSLATED').' : '.$language.' )</i>';
		
		$tabrow=array();
		$tabrow[]=$tempname.' ('.(isset($comps[$value['id_competence_category']]) ? count($comps[$value['id_competence_category']]) : 0).')';
		$tabrow[]='';
		$tabrow[]='';
		$tabrow[]='';
		$tabrow[]='';
		$tabrow[]='';
		$tabrow[]=$value['description'];
		$tabrow[]='';
		$tabrow[]=($mng->GetCompetencesCount($_cat_id)>0 ? '<a href="index.php?op=change_user&id_cat='.$_cat_id.'&modname=competences&of_platform=lms&load=1">'.$img_usr.'</a>' : '');
		$tabrow[]='';
		$tabrow[]=($_cat_id!=0 ? '<a href="index.php?op=mod_category&id_cat='.$_cat_id.'&modname=competences&of_platform=lms&load=1">'.$img_mod.'</a>' : '');
		$tabrow[]=($_cat_id!=0 ? '<a href="#" onclick="del_category('.$_cat_id.'); return false;">'.$img_del.'</a>'/*'<a href="javascript:del_category('.$_cat_id.');">'.$img_del.'</a>'*/ : '');
		$tab->addBody($tabrow, ($first ? 'line-first' : 'line-head') );
	    
	  if ($first) $first=false;
	    
	    $i=0;
	    if(isset($comps[$value['id_competence_category']]) && is_array($comps[$value['id_competence_category']]))
	    foreach ($comps[$value['id_competence_category']] as $key2=>$value2) {
	    
      if ($value2['name']!='')
		    $tempname = $value2['name'];
		  else
		    $tempname = '<i>( '.$lang->def('_COMPETENCE_NOT_TRANSLATED').' : '.$language.' )</i>';
	    
			$tabrow=array();
			$tabrow[]='';
			$tabrow[]=$tempname;//$value2['name'];
			$tabrow[]=$value2['type'];
	      
			switch ($value2['type']) {
				case 'flag': { $sc_min='-'; $sc_max='-'; } break;
				case 'score': { $sc_min=$value2['score_min']; $sc_max=$value2['score']; } break;
				default: { $sc_min='-'; $sc_max='-'; }
			}
	      
			$tabrow[]=$value2['competence_type'];
			$tabrow[]=$sc_min;
			$tabrow[]=$sc_max;
			$tabrow[]=$value2['description'];
	 	    
			$tabrow[]='<a href="index.php?op=show_user&id_comp='.$value2['id_competence'].'&modname=competences&of_platform=lms&load=1">'.$img_shw.'</a>';
	 	    $tabrow[]='<a href="index.php?op=change_user&id_comp='.$value2['id_competence'].'&modname=competences&of_platform=lms&load=1">'.$img_usr.'</a>';
	 	    $tabrow[]='<a href="index.php?op=mod_user&id_comp='.$value2['id_competence'].'&modname=competences&of_platform=lms&load=1">'.$img_obl.'</a>';
	 	    $tabrow[]='<a href="index.php?op=mod_competence&id_comp='.$value2['id_competence'].'&category='.$value['id_competence_category'].'&modname=competences&of_platform=lms&load=1">'.$img_mod.'</a>';
	 	    $tabrow[]='<a href="#" onclick="del_competence('.$value2['id_competence'].'); return false;">'.$img_del.'</a>';//'<a href="javascript:del_competence('.$value2['id_competence'].');">'.$img_del.'</a>';
	       
			$tab->addBody($tabrow,'line-'.($i%2>0 ? 'cl1' : 'cl2'));
			$i++;
	    } // competence foreach
	    
	 } // category foreach
  
	$mod_perm=checkPerm('mod',true);
	if($mod_perm) {
		$tab->addActionAdd(
			'<a class="new_element_link" style="float:left;" href="'.$url->getUrl('op=mod_category').'">'.$lang->def('_ADD_CATEGORY').'</a>'.
			'<a class="new_element_link" style="float:left;" href="'.$url->getUrl('op=mod_competence').'">'.$lang->def('_ADD_COMPETENCE').'</a>'
		);
	}
  
	if (isset($_POST['result'])) {
		switch ($_POST['result']) {
			case 'ok_save': $out->add(getErrorUi($lang->def('_OK_SAVE')));
			case 'error_save': $out->add(getErrorUi($lang->def('_ERROR_SAVE')));
			case 'ok_assign': $out->add(getErrorUi($lang->def('_OK_SAVE')));
			case 'error_assign': $out->add(getErrorUi($lang->def('_ERROR_SAVE')));
		}
		//$out->add(print_r($_POST,true).' - '.mysql_error());
	}
  
  //draw table
  $out->add($tab->getTable());

  cout('<script type="text/javascript">
    var oLang={
      _CONFIRM:"'.$lang->def('_CONFIRM').'",
      _UNDO:"'.$lang->def('_UNDO').'",
      _CONNECTION_ERROR:"'.$lang->def('_CONNECTION_ERROR').'"
    };
    YAHOO.util.Event.onDOMReady(initPopUp);</script>', 'page_head');
}


function competence_list() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('competences', 'lms');
	
	//adding YUI modules, JSON and XHR
	$yui_path=$GLOBALS['where_framework_relative'].'/addons/yui';
	addJs($yui_path.'/yahoo/','yahoo-min.js');
	addJs($yui_path.'/json/','json-beta-min.js');
	addJs($yui_path.'/dom/','dom-min.js');
	addJs($yui_path.'/event/','event-min.js');
	addJs($yui_path.'/connection/','connection-min.js');
	addJs($yui_path.'/animation/','animation-min.js');
	addJs($yui_path.'/yahoo-dom-event/','yahoo-dom-event.js');
	addJs($yui_path.'/dragdrop/','dragdrop-min.js');
	addJs($yui_path.'/container/','container-min.js');
	addJs($yui_path.'/element/','element-beta-min.js');
	addJs($yui_path.'/button/','button-min.js');
	//addJs($yui_path.'','');
	addJs($GLOBALS['where_lms_relative'].'/admin/modules/competences/','competences.js');
	
	//$ajax_path=$GLOBALS['where_lms_relative'].'/admin/modules/competences/ajax.competences.php';
	$ajax_path=$GLOBALS['where_lms_relative'].'/ajax.adm_server.php?plf=lms&mn=competences';
	
	$GLOBALS['page']->add('<link rel="stylesheet" type="text/css" href="'.$yui_path.'/assets/skins/sam/skin.css">','page_head');
	$GLOBALS['page']->add('<link rel="stylesheet" type="text/css" href="'.$yui_path.'/assets/skins/sam/container.css">','page_head');
	
	$GLOBALS['page']->add('<script type="text/javascript">ajax_path="'.$ajax_path.'";</script>'.
                        '<div class="yui-skin-sam" id="yui-content"><div class="area_block"><h1 id="main_area_title" class="main_title_course">'.
                        $lang->def('_TITLE_COMPETENCES').'</h1></div><div class="std_block"><div id="competences_table">', 'content');
	
	$temp = $GLOBALS['globLangManager']->getAllLangCode();
	$array_lang = array();
	foreach ($temp as $val) { $array_lang[$val] = $val; }
	$language = get_req('comp_sel_lang', DOTY_ALPHANUM, getLanguage());
  cout(
    Form::openForm('lang_form', 'index.php', false, 'GET').
    Form::getHidden('op', 'op', 'main').
    Form::getHidden('modname', 'modname', 'competences').
    Form::getDropDown($lang->def('_COMP_SEL_LANG').':', 'comp_sel_lang', 'comp_sel_lang', $array_lang, $language, '', '', 'onchange="YAHOO.util.Dom.get(\'lang_form\').submit();"').
    //Form::getButton('comp_set_lang', 'comp_set_lang', $lang->def('_CONFIRM')).
    Form::getBreakRow().
    Form::closeForm()
  );
	
	ShowCompetences($language);
  
	$GLOBALS['page']->add('</div></div></div>', 'content');
	
}


function mod_user() {
  //...$output=array();
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');

  $_perm=checkPerm('mod',true);
  if ($_perm) {
    //this draws user competence assignation form
    require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
    $url =& UrlManager::getInstance();
    $url->setStdQuery('modname=competences&op=main');
    if (isset($_POST['cancelselector'])) jumpTo($url->getUrl());
	
    $id_competence = importVar('id_comp', true, 0);
    require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
    $competence_man = new Competences_Manager();
    $info = $competence_man->GetCompetence($id_competence);	
	
    $lang =& DoceboLanguage::createInstance('competences', 'lms');
	
    require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
    require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
    // instance assessment =================================================
	
    $user_select = new Module_Directory();
    $user_select->show_user_selector = TRUE;
    $user_select->show_group_selector = TRUE;
    $user_select->show_orgchart_selector = TRUE;
	
    if(isset($_POST['okselector'])) {
		
      $selected = $user_select->getSelection($_POST);
      if(!$competence_man->UpdateCompetenceUsers($id_competence, $selected))
        jumpTo($url->getUrl('result=error_assign'));
      else
        jumpTo($url->getUrl('result=ok_assign'));
    }
    
    if(isset($_GET['load'])) {
		
      $user_select->requested_tab = PEOPLEVIEW_TAB;
      $selected = $competence_man->GetCompetenceUsersIds($id_competence);
      $user_select->resetSelection($selected);
    }
    $user_select->addFormInfo(Form::getHidden('id_comp', 'id_comp', $id_competence));
	
    $user_select->setPageTitle(getTitleArea(array(
      $url->getUrl() => $lang->def('_COMPETENCES'), 
		  $lang->def('_ASSIGN_USER_TITLE').': <b>"'.strip_tags($info['name']).'"</b>')
      , 'competences'));
    $user_select->loadSelector($url->getUrl('op=mod_user'), false, false, true, true );
    
  } else {
    //...$output['content']='';
    die('You can\'t access.');
  }
}

function mod_competence() {
  $yui_path=$GLOBALS['where_framework_relative'].'/addons/yui';
	addJs($yui_path.'/yahoo/','yahoo-min.js');
	addJs($yui_path.'/event/','event-min.js');
	addJs($yui_path.'/dom/','dom-min.js');
	addJs($GLOBALS['where_lms_relative'].'/admin/modules/competences/','competences.js');	

	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
  
  $out=&$GLOBALS['page'];
  $out->setWorkingZone('content');
  
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  
  $out=&$GLOBALS['page'];
  $out->setWorkingZone('content');
  
  require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
  $url =& UrlManager::getInstance();
	$url->setStdQuery('modname=competences&op=main');
  
  //============================================================================

	checkPerm('mod');
	
	$id_competence = importVar('id_comp', true, 0);
	
	$lang =& DoceboLanguage::createInstance('competences', 'lms');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form = new Form();
	
	// instance assessment =================================================
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	$competence_man = new Competences_Manager();
	
	// intest page =========================================================
	if ($id_competence > 0)//(get_req('id_comp', DOTY_INT, false)===false)
	 $temp = $lang->def('_MOD_COMPETENCE');
	else
	 $temp = $lang->def('_ADD_COMPETENCE');
	$out->add(getTitleArea(array( $url->getUrl() => $lang->def('_COMPETENCE'), $temp), 'competences').'<div class="std_block">');
	
	$langs = $GLOBALS['globLangManager']->getAllLangCode();
	
	// save param ==========================================================
	if(isset($_POST['save'])) {
		$errlang=array();
    $competence_data = array(//'name' => importVar('name'), 
				    					       'type' => importVar('type'),
						    			       'score' => importVar('score'),
								    	       'score_min' => importVar('score_min'),
									           //'description' => importVar('description'),
                             'category'=>importVar('category'),
                             'comp_type'=>importVar('comp_type'));
	
    $id_ins = $competence_man->SaveCompetenceData($id_competence, $competence_data);
    if ($id_ins!=false) {
      foreach ($langs as $lang_code) {
		    $temp =  get_req(str_replace(' ', '_',$lang_code), DOTY_MIXED, false);
		    if ($temp!=false) {									               
		      if(trim($temp['name']) != '') 
            if (!$competence_man->SaveCompetenceLanguage($id_ins, $lang_code, $temp['name'], $temp['description']))
		        $errlang[]=$lang_code;
		    }
      } //end for
		}
		
		if ($id_ins==false) {
      $out->add(getErrorUi($lang->def('_ERROR_SAVE')));
    } else {
		
		  if (count($errlang)>0)
        $out->add(getErrorUi($lang->def('_ERROR_SAVE').' ('.implode(', ',$errlang).')'));
		  else
        jumpTo($url->getUrl('result=ok_save'));
    }
	} //end save
	
	
	
	// write form ===========================================================
	
	$out->add(
		$form->openForm('add_competence', $url->getUrl('op=mod_competence'))
	);
	
	
	// load init data ========================================================
	if($id_competence == 0)  
    $competence = array('name'=>'', 'type'=>'', 'score'=>'', 'score_min'=>'', 'description'=>'', 'category'=>0, 'comp_type'=>'');
	else {
    $competence = $competence_man->GetCompetence($id_competence);
    $clangs = $competence_man->GetCompetenceAllLanguages($id_competence);
	}
	
	foreach ($langs as $lang_code) {
	
  	$tlang = str_replace(' ', '_', $lang_code);
  	$temp = get_req($tlang, DOTY_MIXED, false);
  	
    $out->add(
  		 $form->openElementSpace()
  		.$form->getOpenFieldset($lang_code)
		
  		.$form->getTextfield(	$lang->def('_TITLE'),
            								$tlang.'_name',
            								$tlang.'[name]',  
            								255, 
            								($temp!=false ? $temp['name'] : (isset($clangs[$lang_code]['name']) ? $clangs[$lang_code]['name'] : '') ) )
		
   		.$form->getTextarea(	$lang->def('_DESCRIPTION'), 
            								$tlang.'_description',
            								$tlang.'[description]',
            								($temp!=false ? $temp['description'] : (isset($clangs[$lang_code]['description']) ? $clangs[$lang_code]['description'] : '') ) )
  		.$form->getCloseFieldset()
  		.$form->closeElementSpace()
  	);
	
  } //end for
	
	
	$out->add(
     $form->openElementSpace()
		.$form->getHidden('id_comp', 'id_comp', $id_competence)
    .$form->getDropDown(	$lang->def('_CATEGORY'),
								'category',
								'category',
								 $competence_man->GetCompetencesCategoriesList(true), 
								importVar('category', false, $competence['category']))
		
		.$form->getDropDown(	$lang->def('_TYPE'),
								'type',
								'type',
								 $competence_man->GetAllTypes(true), 
								importVar('type', false, $competence['type']))		
		
		.$form->getTextfield(	$lang->def('_SCOREMIN'),
								'score_min',
								'score_min',
								 255, 
								importVar('score_min', false, $competence['score_min']))
		
		.$form->getTextfield(	$lang->def('_SCORE'),
								'score',
								'score',
								 255, 
								importVar('score', false, $competence['score']))
		
		.$form->getDropDown(	$lang->def('_COMP_TYPE'),
								'comp_type',
								'comp_type',
								 $competence_man->GetAllCompetenceTypes(true), 
								importVar('comp_type', false, $competence['comp_type']))
		.$form->closeElementSpace()
  );
	
	
	$out->add(
		 $form->openButtonSpace()
		.$form->getButton('save', 'save', $lang->def('_SAVE'))
		.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		
		.$form->closeForm()
	);
	
	$out->add('</div>');
	
	$out->add('<script type="text/javascript">YAHOO.util.Event.addListener(window, "load", function(e) {'.
            'YAHOO.util.Event.addListener("type","change",mod_competence_type_event);'.
            'YAHOO.util.Event.addListener("score_min","keydown",check_float);'.
            'YAHOO.util.Event.addListener("score","keydown",check_float);'.
            'set_competence_type_parameter();});</script>');
}

function mod_category() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  
  $out=&$GLOBALS['page'];
  $out->setWorkingZone('content');
  
  require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
  $url =& UrlManager::getInstance();
	$url->setStdQuery('modname=competences&op=main');
  	
	checkPerm('mod');
	
	$id_category = importVar('id_cat', true, 0);
	
	$lang =& DoceboLanguage::createInstance('competences', 'lms');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form = new Form();
	
	// instance competences =================================================
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	$competence_man = new Competences_Manager();
	
	// intest page =========================================================
	$out->add(
		getTitleArea(array( $url->getUrl() => $lang->def('_CATEGORY'), $lang->def(($id_category>0 ? '_MOD_CATEGORY' : '_NEW_CATEGORY') )), 'categories')
		.'<div class="std_block">');
	
	$langs = $GLOBALS['globLangManager']->getAllLangCode();
		
	// save param ==========================================================
	if(isset($_POST['save'])) {
    $errlang=array();
    $id_ins = $competence_man->SaveCompetenceCategoryData($id_category);
    if ($id_ins!=false) {
      foreach ($langs as $lang_code) {
		    $temp =  get_req(str_replace(' ', '_',$lang_code), DOTY_MIXED, false);
		    if ($temp!=false) {									               
		      if(trim($temp['name']) != '') 
            if (!$competence_man->SaveCompetenceCategoryLanguage($id_ins, $lang_code, $temp['name'], $temp['description']))
		          $errlang[]=$lang_code;
		    }
      } //end for
		}
		
		if ($id_ins==false) {
      $out->add(getErrorUi($lang->def('_ERROR_SAVE')));
    } else {
		
		  if (count($errlang)>0)
        $out->add(getErrorUi($lang->def('_ERROR_SAVE').' ('.implode(', ',$errlang).')'));
		  else
        jumpTo($url->getUrl('result=ok_save'));
    }
	} //end save
	
	// load init data ========================================================
	if($id_category > 0) {
    $clangs = $competence_man->GetCompetenceCategoryAllLanguages($id_category); //$out->add('debug: '.print_r($temp,true).';');
	}
	
	// write form ===========================================================
	
	$out->add(
		 $form->openForm('add_category', $url->getUrl('op=mod_category'))
		.$form->getHidden('id_cat', 'id_cat', $id_category)
	);
	
	
	
	foreach ($langs as $lang_code) {
	
  	$tlang = str_replace(' ', '_', $lang_code);
  	$temp = get_req($tlang, DOTY_MIXED, false);
  	
    $out->add(
  		 $form->openElementSpace()
  		.$form->getOpenFieldset($lang_code)
		
  		.$form->getTextfield( $lang->def('_NAME_CATEGORY'),
            								$tlang.'_name',
            								$tlang.'[name]',  
            								255, 
            								($temp!=false ? $temp['name'] : (isset($clangs[$lang_code]['name']) ? $clangs[$lang_code]['name'] : '') ) )
		
   		.$form->getTextarea(	$lang->def('_DESCRIPTION_CATEGORY'), 
            								$tlang.'_description',
            								$tlang.'[description]',
            								($temp!=false ? $temp['description'] : (isset($clangs[$lang_code]['description']) ? $clangs[$lang_code]['description'] : '') ) )
  		.$form->getCloseFieldset()
  		.$form->closeElementSpace()
  	);
	
  } //end for
	
	$out->add(
		 $form->openButtonSpace()
		.$form->getButton('save', 'save', $lang->def('_SAVE'))
		.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		
		.$form->closeForm()
	);
	
	$out->add('</div>');
}


function show_user() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  
  $out=&$GLOBALS['page'];
  $out->setWorkingZone('content');
  
  require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
  require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
  $url =& UrlManager::getInstance();
	$url->setStdQuery('modname=competences');
  
  $acl = new DoceboACLManager();
  
	checkPerm('view');
	
	// instance competences =================================================
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	$_man = new Competences_Manager();
	
	// intest page =========================================================
	$out->add(
		getTitleArea(array( $url->getUrl('op=main') => $lang->def('_COMPETENCES'), $lang->def('_SHOW_USERS')), 'show_users')
		.'<div class="std_block">');
		
	$id_comp=importVar('id_comp', true, 0);
	$type=$_man->GetCompetenceType($id_comp);
	
	$orderby = importVar('order_by',false,'userid');
	$dir = importVar('order_dir',false,'asc');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$tb = new TypeOne($GLOBALS['visuItem'],$lang->def('_COMPETENCES_USER_TAB_CAPTION'),'summary');
  
	$tb->initNavBar('ini', 'button');//'link');
	$tb->setLink($url->getUrl('op=show_user&id_comp='.$id_comp));
	if (!$ini=importVar('order_ini',true,false))
		$ini = $tb->getSelectedElement();
	
	$users = $_man->GetCompetenceUsersAll($id_comp, $orderby, $dir, $ini, $GLOBALS['lms']['visuItem']);
	$tot_comp = $_man->GetCompetenceUsersCount($id_comp);
	
	if (count($users)>0) {
	
    $out->add('<a href="'.
						$url->getUrl('op=export_cvs&id_comp='.$id_comp.'&order_by='.$orderby.'&order_dir='.$dir).
						'"><img src="'.getPathImage().'report/export_cvs.gif" />'.
						$lang->def('_EXPORT_CSV').'</a>');
	
		$order_url = $url->getUrl('op=show_user&id_comp='.$id_comp.'&order_ini='.$ini);
		//['.(int)(($ini/$GLOBALS['lms']['visuItem'])+1).']' );
	
		function get_order_link($which, $orderby, $dir, $url) {
			$img_path = getPathImage('fw').'directory/'; 
			//$GLOBALS['where_framework_relative'].'/templates/standard/images/directory/';
			$img_sort = 'sort.png';
			$img_up   = '1uparrow.png';
			$img_down = '1downarrow.png';
			$img_url  = $url;
			
			return
			'<a href="'.$img_url.'&order_by='.$which.'&order_dir='.
			($which==$orderby ? ($dir=='asc' ? 'desc' : 'asc') :  'asc').
			'"><img src="'.$img_path.
			($which==$orderby ? ($dir=='asc' ? $img_down : $img_up  ) :  $img_sort).
			'" /></a>';
		}

	
    $acl_man =new DoceboACLManager();
    switch ($type) {
      case 'flag': {
        $tcolstyle = array('','align_center');
	      $tcolhead  = array('<span>'.$lang->def('_USERID').'</span>'.get_order_link('userid', $orderby, $dir, $order_url),
													 '<span>'.$lang->def('_SHOW_USER_TAB_USER').'</span>'.get_order_link('name', $orderby, $dir, $order_url),
													 '<span>'.$lang->def('_EMAIL').'</span>'.get_order_link('email', $orderby, $dir, $order_url),
													 $lang->def('_COMPETENCE_STATUS'));
	      
	      $tb->setColsStyle($tcolstyle);
        $tb->addHead($tcolhead);
	      
        foreach ($users as $key=>$value) {
          //$res=mysql_query()
        
          $trow=array();
          $trow[] = $acl->relativeId($value['userid']);
          $trow[] = $value['lastname'].' '.$value['firstname'];//$acl_man->getUserName($value['id_user']);
          $trow[] = $value['email'];
          $trow[] = $lang->def('_COMPETENCE_ACQUIRED');
          //$trow[]='';
          
          $tb->addBody($trow);
        } 
      } break;
      
      case 'score': {
        $tcolstyle = array('','image','image','image','image');
	      $tcolhead  = array('<span>'.$lang->def('_USERID').'</span>'.get_order_link('userid', $orderby, $dir, $order_url),
													 '<span>'.$lang->def('_SHOW_USER_TAB_USER').'</span>'.get_order_link('name', $orderby, $dir, $order_url),
                           '<span>'.$lang->def('_EMAIL').'</span>'.get_order_link('email', $orderby, $dir, $order_url),
													 '<span>'.$lang->def('_SHOW_USER_TAB_SCORE_INIT').'</span>'.get_order_link('score_init', $orderby, $dir, $order_url),
                           '<span>'.$lang->def('_SHOW_USER_TAB_SCORE_GOT').'</span>'.get_order_link('score_got', $orderby, $dir, $order_url),
                           '<span>'.$lang->def('_SHOW_USER_TAB_SCORE_TOTAL').'</span>'.get_order_link('total', $orderby, $dir, $order_url),
                           '<span>'.$lang->def('_COMPETENCE_STATUS').'</span>' );
	
        $tb->setColsStyle($tcolstyle);
        $tb->addHead($tcolhead);
      
        $comp_score = $_man->GetCompetenceMinScore($id_comp);
        foreach ($users as $key=>$value) {
          
          $total = (float)$value['score_init'] + (float)$value['score_got'];
          $need  = $comp_score-$total; 
          
          $trow=array();
          $trow[] = $acl->relativeId($value['userid']);
          $trow[]=$value['lastname'].' '.$value['firstname'];//$acl_man->getUserName($value['id_user']);
          $trow[] = $value['email'];
          $trow[]=$value['score_init'];//number_format($value['score_init'],2,'.','');
          $trow[]=$value['score_got'];//number_format($value['score_got'],2,'.','');
          $trow[]=$value['total'];//number_format($value['total'],2,'.','');//(string)$total;
          if ((float)$need<0) $need=0;
          $trow[]=($need<=0 ? $lang->def('_COMPETENCE_ACQUIRED') : $need.'/'.$comp_score );
            
          $tb->addBody($trow);
        }
      } break;
    }
    
    require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form = new Form();
    
    $out->add(
			$form->openForm('show_user_form', $url->getUrl('op=show_user&id_comp='.$id_comp)/*,,'post'*/).
			$form->getHidden('order_by', 'order_by', $orderby).
			$form->getHidden('order_dir', 'order_dir', $dir).
			$tb->getTable().
			$tb->getNavbar($ini, $tot_comp).
			$form->closeForm() 
			);
  } else {
    $out->add('<br /><div>'.$lang->def('_NO_COMPETENCE_ASSIGNED').'</div>');
  }
	
	$out->add('</div>');
} 


//assign competences to users manually
function change_user() {
  $lang =& DoceboLanguage::createInstance('competences', 'lms');
  
  $out=&$GLOBALS['page'];
  $out->setWorkingZone('content');
  
  // instance competences =================================================
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	$_man = new Competences_Manager();
  
  require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
  $url =& UrlManager::getInstance();
	$url->setStdQuery('modname=competences&op=main');
  	
	checkPerm('mod');
	
	if(isset($_POST['save'])) {
      
    foreach ($_POST['competences'] as $key=>$value) { $out->add('<div>ciclo compet.:'.$value.'</div>');
      $type = $_man->GetCompetenceType($value);
    	switch ($type) {
        case 'flag': {
          foreach ($_POST['competence_users'] as $key2=>$value2) {
            if (isset($_POST['competence_user_assign_flag'][$value][$value2])) {
              if (!$_man->UpdateUserScore($value2,$value,'0','0')) jumpTo($url->getUrl('result=error_assign'));
            } else {
              if ($_man->UserHasCompetence($value2,$value)) $_man->DeleteUserScore($value2,$value);
            }
          }
          //jumpTo($url->getUrl('result=ok_assign'));
        } break;
      
        case 'score': {
          foreach ($_POST['competence_users'] as $key2=>$value2) {
            $newscoreinit = $_POST['competence_user_assign_score_init'][$value][$value2];
            $newscoregot  = $_POST['competence_user_assign_score_got'][$value][$value2];            
            if (!$_man->UpdateUserScore($value2,$value,$newscoreinit,$newscoregot))  { jumpTo($url->getUrl('result=error_assign')); }
              //{$out->add('<div>'.$newscoreinit.' - '.$newscoregot.'; '.mysql_error().'</div>');}
          }
          //$out->add('<div>'.print_r($_POST,true).'</div>');
          
        } break;
      }
      
    }
    jumpTo($url->getUrl('result=ok_assign'));    	
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	//$td_style='style="border-left:0px; border-right:0px;"';
	$td_style='style="border:none;"';
	$inputtextstyle='style="width:4em"';
	
	$out->add(
		getTitleArea(array( $url->getUrl() => $lang->def('_COMPETENCES'), $lang->def('_CHANGE_USERS')), 'show_users')
		.'<div class="std_block">');
	
	
  //fast filter
  
  $filterstring = "1";
  $filter = importVar('hiddenfilter', false, "");
  $tohidden = $filter;
  
  if (importVar('searchfilter', false,false)) {
    $filter = importVar('filter', false, "");
    $tohidden = $filter;
  }
  
  if (importVar('resetfilter', false,false)) {
    $filter = "";
    $tohidden = $filter;
  }
  
  if ($filter != '') {
    $fvar = addslashes($filter);
    $filterstring = " (userid LIKE '%$fvar%' OR firstname  LIKE '%$fvar%' OR lastname  LIKE '%$fvar%') ";
  }
  $filterstring .= " ORDER BY userid, firstname, lastname ";
  
  $tab = new TypeOne($GLOBALS['visuItem'],$lang->def('_COMPETENCES_CHANGEUSER_TAB_CAPTION'),'summary');
	
	//pagination
	$tab->initNavBar('ini', 'button');//'link');
	$tab->setLink($url->getUrl('op=show_user&'.( !importVar('id_cat',false,false) ? 'id_comp='.importVar('id_comp', false, false) : 'id_cat='.importVar('id_cat',false,false)) ) );
	if (!$ini=importVar('order_ini',true,false))
		$ini = $tab->getSelectedElement();
	//...
	
	$form = new Form();

	
	//table
	$data = new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
	
  //$data->addFieldFilter('userid', '', '<>');
  $temp = $filterstring;
  $data->addCustomFilter("", $temp ); 
  $totalrows = $data->getTotalRows();
	$rows_ = $data->getRows($ini, $GLOBALS['visuItem']); //paginator will be implemented
  
  $rows=array();
	while ($row=mysql_fetch_array($rows_)) { $rows[]=$row; }
	
	$acl_man = new DoceboACLManager();
  $id_cat = importVar('id_cat',false,false);
	if ($id_cat!==false) {//(is_int($id_cat) && $id_cat>=0) {
    //retrieve competences of this category
    $comps = $_man->GetCompetences((int)$id_cat);
  } else {
    $id_comp = importVar('id_comp',true,0);
    $comps = array();
    $temp = $_man->GetCompetence((int)$id_comp);
    $temp['id_competence'] = $temp['id'];
    $comps[] = $temp;
  }
    
  $headstyle[] = '';
  $headstyle[] = '';
  $headstyle[] = '';
  $head[] = $lang->def('_USERNAME'); //userid
  $head[] = $lang->def('_NAME'); //full name
  $head[] = $lang->def('_EMAIL'); //email
  foreach ($comps as $key=>$value) {
    $headstyle[] = 'image';
    $head[] = $value['name'].$form->getHidden('competences_'.$value['id_competence'],'competences[]',$value['id_competence']);
  }
    
  $tab->setColsStyle($headstyle);
  $tab->addHead($head);
  foreach ($rows as $key=>$value) { //TO DO : check if user Anonymous (that shouldn't be listed)
     
   if ($value['idst']!=$acl_man->getAnonymousId()) { //$acl_man->getAnonymousId();
    
    $trow=array();
    $trow[]=$acl_man->relativeId($value['userid']).$form->getHidden('competence_users_'.$value['idst'],'competence_users[]',$value['idst']);
    
    $trow[]=$value['lastname'].' '.$value['firstname'];
    $trow[]=$value['email'];
    
    foreach ($comps as $key2=>$value2) { //ciclo competenze (riga utente)
      if ($_man->UserHasCompetence($value['idst'],$value2['id_competence'])) $check=true; else $check=false;
      switch ($value2['type']) {
        case 'flag': {
          $trow[]=$form->getInputcheckBox('competence_user_assign_'.$value2['id_competence'], 'competence_user_assign_flag['.$value2['id_competence'].']['.$value['idst'].']', 0, $check, ''); //chekcbox
        } break;
          
        case 'score': {
          /*$trow[]=  '<label for="competence_user_assign_init_'.$value2['id_competence'].'_'.$value['idst'].'">'.
                    $lang->def('_COMPETENCE_USER_INIT_ABBR').'</label>'.
                    $form->getInputTextField('align_right',
											                    'competence_user_assign_init_'.$value2['id_competence'].'_'.$value['idst'], 
											                    'competence_user_assign_score_init['.$value2['id_competence'].']['.$value['idst'].']',
											                    ($check ? $_man->GetUserInitialScore($value['idst'],$value2['id_competence']) : '' ),
                                          '', '', 'style="width:5em"' ).'<br />'.
                    '<label for="competence_user_assign_'.$value2['id_competence'].'_'.$value['idst'].'">'.
                    $lang->def('_COMPETENCE_USER_GOT_ABBR').'</label>'.         
                    $form->getInputTextField('align_right',
											                    'competence_user_assign_'.$value2['id_competence'].'_'.$value['idst'], 
											                    'competence_user_assign_score_got['.$value2['id_competence'].']['.$value['idst'].']',
											                    ($check ? $_man->GetUserScore($value['idst'],$value2['id_competence']) : '' ),
                                          '', '', 'style="width:5em"' ); //textfields*/
          $trow[]=  '<table class="_paginator"><tr><td '.$td_style.'>'.
                    '<label for="competence_user_assign_init_'.$value2['id_competence'].'_'.$value['idst'].'">'.
                    $lang->def('_COMPETENCE_USER_INIT_ABBR').'</label></td><td '.$td_style.'>'.
                    $form->getInputTextField('align_right',
											                    'competence_user_assign_init_'.$value2['id_competence'].'_'.$value['idst'], 
											                    'competence_user_assign_score_init['.$value2['id_competence'].']['.$value['idst'].']',
											                    ($check ? $_man->GetUserInitialScore($value['idst'],$value2['id_competence']) : '' ),
                                          '', '', $inputtextstyle ).'</td></tr><tr><td '.$td_style.'>'.
                    '<label for="competence_user_assign_'.$value2['id_competence'].'_'.$value['idst'].'">'.
                    $lang->def('_COMPETENCE_USER_GOT_ABBR').'</label></td><td '.$td_style.'>'.         
                    $form->getInputTextField('align_right',
											                    'competence_user_assign_'.$value2['id_competence'].'_'.$value['idst'], 
											                    'competence_user_assign_score_got['.$value2['id_competence'].']['.$value['idst'].']',
											                    ($check ? $_man->GetUserScore($value['idst'],$value2['id_competence']) : '' ),
                                          '', '', $inputtextstyle ).'</td></tr></table>'; //textfields
        } break;
      }
    }
      
    $tab->addBody($trow);
    
   }
  }
  //$img_path=$GLOBALS['where_framework_relative'].'/templates/standard/images/standard/';
  //$out->add("<script type=\"text/javascript\">img_path='$img_path';</script>");
  $out->add(
		$form->openForm('change_user_form', $url->getUrl('op=change_user'),'')
		
			
	//filter interface
	
    .$form->openElementSpace()
    .$form->getTextField(def('_FILTER', 'faq', 'framework'), 'filter', 'filter', 200, $filter)
    .$form->getHidden('hiddenfilter', 'hiddenfilter', $tohidden)
    .$form->openButtonSpace()
    .$form->getButton('searchfilter', 'searchfilter', $lang->def('_SEARCH'))
    .$form->getButton('resetfilter', 'resetfilter', $lang->def('_UNDO'))
    .$form->closeButtonSpace()
    .$form->closeElementSpace()
	
		
		.$form->openElementSpace('')
		.($id_cat ? $form->getHidden('id_cat', 'id_cat', $id_cat) : $form->getHidden('id_comp', 'id_comp', $id_comp))
		
    .$tab->getNavBar($ini, $totalrows)
    .$tab->getTable()
		.$tab->getNavBar($ini, $totalrows)
    
    .$form->closeElementSpace()
		
		.$form->openButtonSpace()
		.$form->getButton('save', 'save', $lang->def('_SAVE'))
		.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		
		.$form->closeForm()
	);
  
  $out->add('</div>');
}

function export_as_csv() {
	ob_end_clean();
	//content type forcing dowlad
	header("Content-type: application/download\n");
	//cache control
	header("Cache-control: private");
	//sending creation time
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	//content type
	header('Content-Disposition: attachment; filename="competence_user_list '.gmdate('d-m-Y H i s').'.csv"');

	//checkperms ....

	$id_comp = importVar('id_comp', true, false);
	$order_by = importVar('order_by', false, false);
	$order_dir = importVar('order_dir', false, false);
	
	if ($id_comp===false) { echo 'Error: invalid competence'; exit(0); }

	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	$_man = new Competences_Manager();

	$lang =& DoceboLanguage::createInstance('competences', 'lms');
	$users = $_man->GetCompetenceUsersAll($id_comp, $order_by, $order_dir);
	
	if (count($users)>0) {
		
		switch ($_man->GetCompetenceType($id_comp)) {
      case 'flag': {
        echo $lang->def('_SHOW_USER_TAB_USER').','.$lang->def('_COMPETENCE_STATUS')."\r\n";
        foreach ($users as $key=>$value) {
          echo $value['lastname'].' '.$value['firstname'].','.$lang->def('_COMPETENCE_ACQUIRED')."\r\n";
        } 
      } break;
      
      case 'score': {
        echo $lang->def('_SHOW_USER_TAB_USER').','.
             $lang->def('_SHOW_USER_TAB_SCORE_INIT').','.
             $lang->def('_SHOW_USER_TAB_SCORE_GOT').','.
             $lang->def('_SHOW_USER_TAB_SCORE_TOTAL').','.
             $lang->def('_SHOW_USER_TAB_SCORE_NEED')."\r\n";

        $comp_score = $_man->GetCompetenceMinScore($id_comp);
        foreach ($users as $key=>$value) {
          $total = (float)$value['score_init'] + (float)$value['score_got'];
          $need  = $comp_score-$total; 
          echo $value['lastname'].' '.$value['firstname'].','.
          		 $value['score_init'].','.
          		 $value['score_got'].','.
          		 $value['total'].',';
          if ((float)$need<0) $need=0;
          	echo ($need<=0 ? $lang->def('_COMPETENCE_ACQUIRED') : $need.'/'.$comp_score );
          
          echo "\r\n";
        }
      } break;
    }
	}
	exit(0);
}

//================================================



//dispatcher
function competencesDispatch($op) {

  if (isset($_POST['undo'])) $op='main';
  /*if (isset($_POST['result'])) {
    $out=&$GLOBALS['page'];
    $out->setWorkingZone('content');
    switch ($_POST['result']) {
      case 'ok_save': $out->add(getErrorUi($lang->def('_OK_SAVE')));
      case 'error_save': $out->add(getErrorUi($lang->def('_ERROR_SAVE')));
      case 'ok_assign': $out->add(getErrorUi($lang->def('_OK_SAVE')));
      case 'error_assign': $out->add(getErrorUi($lang->def('_ERROR_SAVE')));
		}
	}*/

	switch($op) {
    case 'main': {
      competence_list();
    } break;
    
    case 'show_user': {
      show_user();
    } break;
    
    case 'change_user': {
      change_user();
    } break;
    
    case 'mod_category': {
      mod_category();
    }; break;

    case 'mod_user': {
      mod_user();
    } break;
    
    case 'mod_competence': {
      mod_competence();
    } break;
    
    case 'del_competence': {
      //DeleteCompetence();
    } break;
    
    case 'del_category': {
      
    } break;
    
    case 'export_cvs': {
			export_as_csv();
		} break;
    
		default: {
			competence_list();
		}
	}
}

?>