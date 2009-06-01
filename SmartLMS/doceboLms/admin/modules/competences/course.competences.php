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

require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
function courseCompetences() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
	
	$yui_path=$GLOBALS['where_framework_relative'].'/addons/yui';
	addJs($yui_path.'/yahoo/','yahoo-min.js');
	addJs($yui_path.'/json/','json-beta-min.js');
	addJs($yui_path.'/dom/','dom-min.js');
	addJs($yui_path.'/event/','event-min.js');
	addJs($yui_path.'/yahoo-dom-event/','yahoo-dom-event.js');
	addJs($yui_path.'/connection/','connection-min.js');
	addJs($yui_path.'/container/','container-min.js');
	addJs($yui_path.'/animation/','animation-min.js');
	addJs($GLOBALS['where_lms_relative'].'/admin/modules/competences/','competences.js');
	
	$lang =& DoceboLanguage::CreateInstance('competences', 'lms');
	$ajax_path=$GLOBALS['where_lms_relative'].'/ajax.adm_server.php?mn=competences&plf=lms';//$GLOBALS['where_lms_relative'].'/admin/modules/competences/ajax.competences.php';
	$save = $lang->def('_SAVE');
	$undo = $lang->def('_UNDO');
	
	$GLOBALS['page']->add('<script>YAHOO.util.Event.addListener(window,"load",course_init);'.
                        'ajax_path="'.$ajax_path.'";_SAVE="'.$save.'";_UNDO="'.$undo.'";</script>' ,'page_head');
	
	$GLOBALS['page']->add('<link rel="stylesheet" type="text/css" href="'.$yui_path.'/assets/skins/sam/skin.css">','page_head');
	$GLOBALS['page']->add('<link rel="stylesheet" type="text/css" href="'.$yui_path.'/assets/skins/sam/container.css">','page_head');
		
	$id_course = importVar('id_course', false, 0);
	$url = "index.php?modname=course&amp;op=course_list&amp;id_course=".$id_course;
		
	//$img_mod='<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_COMPETENCE_COURSE_MODIFY").'" />';
  //$img_del='<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_COMPETENCE_COURSE_DELETE").'" />';
	
	$form = new Form();
	$_man = new Competences_Manager();
		
	$query_course = "SELECT code, name FROM ".$GLOBALS['prefix_lms']."_course WHERE idCourse = '".$id_course."'";
	$course = mysql_fetch_array(mysql_query($query_course));
	
	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_COMPETENCES_TO_COURSE_CAPTION'), $lang->def('_COMPETENCES_TO_COURSE_CAPTION'/*SUMMARY*/));
	$categories_list 	= $_man->GetCompetencesCategoriesList(true);
	$course_comps 		= $_man->GetCourseCompetencesIds($id_course);
	
	$type_h = array(/*'image',*/ '', '', '', 'image', 'image', 'image', 'image', /*'image', 'image'*/);
	$cont_h	= array(
		//'&nbsp;',//checkbox
		$lang->def('_CATEGORY'),
		$lang->def('_TITLE'),
		$lang->def('_DESCRIPTION'),
		$lang->def('_TYPE'),
		$lang->def('_SCOREMIN'),
		$lang->def('_SCORE'),
		$lang->def('_COMPETENCE_SCORE')//,
		//$img_mod, //modify
		//$img_del  //delete
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	$comp=$_man->GetCompetencesGrouped();	
	foreach ($categories_list as $id_cat=>$cat) {
	 if (isset($comp[ $id_cat ]))	{
		$cont=array();
		//$cont[]=''; //cumulative checkbox?
		$cont[]=$cat;
		$cont[]='';
		$cont[]='';
		$cont[]='';
		$cont[]='';
		$cont[]='';
		$cont[]='';
		//$cont[]='';
		//$cont[]='';
		
		$tb->addBody($cont,'line');
		
		//$comp=$_man->GetCompetences((int)$id_cat);	
    
    $i=0;	
		//foreach ($comp as $key=>$value) {
		foreach ($comp[ $id_cat ] as $key=>$value) {
		  $id=$value['id_competence'];
		  if (array_key_exists($id,$course_comps)) $check=true; else $check=false; //$GLOBALS['page']->add($id.' , '.print_r($course_comps,true).'  ,  '.print_r($check,true).'<br />');
		  
		  $cont = array();
		  //$cont[] = $form->getInputcheckBox('competence_assign_'.$id, 'competence_assign['.$id.']', $id, $check, '');
		  $cont[] = '';
		  $cont[] = '<label for="competence_assign_'.$id.'">'.$value['name'].'</label>';
		  $cont[] = $value['description'];
		  $cont[] = $value['type'];
		  /*if (!$check) {
		    $cont[] = $form->getInputTextField('align_right',
											                     'competence_assign_score_'.$id, 
											                     'competence_assign_score['.$id.']',
											                     '', '', '',	'' );
				$cont[] = '';
      } else {
        $cont[] = '<div style="text-align:center">'.$_man->GetCourseScore($id_course, $id).'</div>';
        $cont[] = '[elimina]';
      }*/
      
      //$cont[] = $value['score_min'];
      //$cont[] = $value['score'];
      
      switch ($value['type']) {
       	case 'flag': {
       	  $cont[] = '-';
          $cont[] = '-';
          $cont[] = $form->getInputcheckBox('competence_assign_'.$id, 'competence_assign_flag['.$id.']', $id, $check, 'onclick="course_flag_change(this);"');
          //$cont[] = '-';
          //$cont[] = '-';
        } break;
        
        case 'score': {
          $cont[] = $value['score_min'];
          $cont[] = $value['score'];
          $cont[] = /*$form->getInputTextField('align_right',
											                     'competence_assign_'.$id, 
											                     'competence_assign_score['.$id.']',
											                     ($check ? $_man->GetCourseScore($id_course, $id) : '' ),
                                           '', '', 'style="width:5em"' );*/
                    '<div id="score_value_'.$id.'">'.
                    ($check ? '<span style="font-weight:bold">'.$_man->GetCourseScore($id_course, $id).'</span>' : '<span>-</span>').
                    '</div>';
          
          //$cont[]='<a id="a_mod_'.$id.'" href="javascript:course_modify('.$id_course.','.$id.');">'.$img_mod.'</a>';
          //$cont[]='<a id="a_del_'.$id.'" href="javascript:course_remove('.$id_course.','.$id.');">'.$img_del.'</a>';
        } break;
        
        default: $temp=''; 
      }
          
      //$cont[]='<a href="javascript:alert(\'modify\');">'.$img_mod.'</a>';
      //$cont[]='<a href="javascript:alert(\'delete\');">'.$img_del.'</a>';
      
		  $tb->addBody($cont,'line-'.($i%2>0 ? 'cl1' : 'cl2'));
		  $i++;
		}
	 }
	}	
	// print table ===========================================================
	$clang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms'); 
	$GLOBALS['page']->add(
		getTitleArea(array($url => $clang->def('_COURSE'), $lang->def('_MANAGE_COMPETENCES').': '.$course['name']), 'competences')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=course&op=course_list', $clang->def('_BACK'), 'content')
				
		//.$form->openForm("main_form", $url)   //form no more needed, now it works with ajax
		.$form->getHidden('id_course', 'id_course', $id_course)
		
		.'<div id="course_table">'.$tb->getTable().'</div>'
		
		/*.$form->openButtonSpace()
		.$form->getButton('save', 'save', $lang->def('_SAVE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()*/
		
		//.$form->closeForm()
		.'</div>'
	, 'content');
	
}

//not used anymore
//$GLOBALS['page']->add(print_r($_POST,true), 'content');
function updateCompetences() {
  checkPerm('mod'); //die(print_r($_POST,true));
	
	$id_course = importVar('id_course', false, 0);
	
	if(isset($_POST['competence_assign_score']) || isset($_POST['competence_assign_flag'])) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.competences.php');
		$_man = new Competences_Manager();
		if(!$_man->UpdateCompetencesCourseAssign($id_course, $_POST['competence_assign_score'], $_POST['competence_assign_flag'])) {
			jumpTo('index.php?modname=course&op=course_list&result=err');
		}
	}
	jumpTo('index.php?modname=course&op=course_list&result=ok');

}

?>