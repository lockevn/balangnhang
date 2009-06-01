<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS['where_lms'].'/lib/lib.light_repo.php');

function repoList(&$url) {
	checkPerm('view');
	
	$lang =& DoceboLanguage::createInstance('light_repo');

	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
		
	$mod_perm = checkPerm('mod', true);
	
	$repositories 	= $file_man->getRepoList(!$mod_perm);
	
	if($repositories !== false && mysql_num_rows($repositories) == 1 && !$mod_perm) { 
		$repo = mysql_fetch_row($repositories);
		return repoMyDetails($url, $repo[LR_ID]); 
	}
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_LIGHT_REPO'), 'light_repo')
		.'<div id="light_repo_block" class="std_block">'
	, 'content');
	
	if($repositories !== false && mysql_num_rows($repositories) > 0) {
		
		if(isset($_GET['result'])) {
			
			switch($_GET['result']) {
				case "ok_mod" : { $GLOBALS['page']->add(getAppendAlert($lang->def('_OK_MOD_REPO')), 'content'); };break;
				case "ok_del" : { $GLOBALS['page']->add(getAppendAlert($lang->def('_OK_DEL_REPO')), 'content'); };break;
			}
		}
	} else { 
		
		$GLOBALS['page']->add($lang->def('_NO_REPOSITORY_FOUND'), 'content');
	}
	
	while($repo = mysql_fetch_row($repositories)) {
		$last_enter = $file_man->getUserLastEnterInRepo($repo[LR_ID]);
		$new_file = $file_man->getNumberOfFileInReport($repo[LR_ID], $last_enter);
		$GLOBALS['page']->add(
			'<div class="repository_container" id="repo_container_'.$repo[LR_ID].'">'
			.'<h2><a href="'.$url->getUrl('op='.( $mod_perm ? 'repo_manager_details' : 'repo_my_details' ).'&id_repo='.$repo[LR_ID]).'">'
				.$repo[LR_TITLE]
			.'</a></h2>'
			.'<div class="descr">'.$repo[LR_DESCR].'</div>'
			.'<div>'.$lang->def('_FILE_COUNT').': '.$repo[LR_FILECOUNT].'</div>'
		, 'content');
		if($mod_perm) {
			
			if(isset($new_file) && $new_file !== '0')
					$GLOBALS['page']->add('<b>('.$new_file.' '.$lang->def('_REPO_NEW_FILE').')</b>', 'content');
			
			$GLOBALS['page']->add('<ul class="adjac_link">', 'content');
			
			$GLOBALS['page']->add('<li>'
				.'<a href="'.$url->getUrl('op=mod_repo&id_repo='.$repo[LR_ID]).'" title="'.$lang->def('_MOD').'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').': '.strip_tags($repo[LR_TITLE]).'" /> '
				.$lang->def('_MOD_REPOSITORY').'</a>'
				.'</li>'
			, 'content');
			
			$GLOBALS['page']->add('<li>'
				.'<a href="'.$url->getUrl('op=del_repo&id_repo='.$repo[LR_ID]).'" title="'.$lang->def('_DEL_REPOSITORY').' : '.$repo[LR_TITLE].'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" /> '
				.$lang->def('_DEL_REPOSITORY').'</a>'
				.'</li>'
			, 'content');
			
			$GLOBALS['page']->add('</ul>', 'content');
		}
		
		$GLOBALS['page']->add('</div>', 'content');
	}
	if($mod_perm) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=del_repo]');
		
		$GLOBALS['page']->add('<div class="new_elem_link">'
				.'<a href="'.$url->getUrl('op=mod_repo').'">'.$lang->def('_NEW_REPOSITORY').'</a>'
				.'</div>', 'content');
	}
	$GLOBALS['page']->add('</div>', 'content');
}

function modRepo(&$url) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang 		=& DoceboLanguage::createInstance('light_repo');
	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
	
	$id_repo = importVar('id_repo', true, 0);
	
	// recovering file repository information
	$repo = false;
	if($id_repo != 0) { $repo = $file_man->getRepoDetails($id_repo); } 
	if($repo == false) {
		$repo[LR_TITLE] = '';
		$repo[LR_DESCR] = '';
	}  
	
	$GLOBALS['page']->add(
		getTitleArea(array(	$url->getUrl() => $lang->def('_TITLE_LIGHT_REPO'), 
			( $id_repo == 0 ? $lang->def('_NEW_REPOSITORY') : $lang->def('_MOD_REPOSITORY').' '.$repo[LR_TITLE] )
		), 'light_repo')
		.'<div class="std_block">', 'content');
	
	// save modification if needed
	if(isset($_POST['save'])) {
		
		$data[LR_IDCOURSE] = $_SESSION['idCourse'];
		$data[LR_TITLE] = importVar('repo_title', false, '');
		$data[LR_DESCR] = importVar('repo_descr', false, '');
		
		if(trim($data[LR_TITLE]) == '') $data[LR_TITLE] = $lang->def('_NOTITLE');
		
		if(!$file_man->saveRepo($id_repo, $data)) { 
			$GLOBALS['page']->add(getAppendAlert($lang->def('_ERR_MOD_REPO')), 'content'); 
		} else { jumpTo( $url->getUrl('result=ok_mod') ); }
	}
	
	// form for input 
	$GLOBALS['page']->add(''
		.Form::openForm('mod_repo_form', $url->getUrl('op=mod_repo'))
		
		.Form::openElementSpace()
		.Form::getHidden('id_repo', 'id_repo', $id_repo)
		.Form::getTextfield(	$lang->def('_TITLE'),
								'repo_title',
								'repo_title',
								255, 
								importVar('repo_title', false, $repo[LR_TITLE]) )
		.Form::getTextarea(		$lang->def('_DESCRIPTION'), 
								'repo_descr', 
								'repo_descr', 
								importVar('repo_descr', false, $repo[LR_DESCR]) )
		
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		
		.Form::closeButtonSpace()
		
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function delRepo(&$url) {
	checkPerm('mod');
	
	require_once($GLOBALS["where_lms"]."/lib/lib.light_repo.php");
	
	$re = false;
	if(isset($_GET['confirm'])) {
		$id_repo = get_req('id_repo', DOTY_INT, 0);
		$file_man = new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
		$re = $file_man->deleteRepo($id_repo);
	}
	jumpTo($url->getUrl('op=repolist&result='.($re?'ok_del':'err')));	
}

function repoMyDetails(&$url, $passed_repo = 0) {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$lang 		=& DoceboLanguage::createInstance('light_repo');
	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	
	$id_repo = importVar('id_repo', true, $passed_repo);
	// recovering file repository information
	$repo = $file_man->getRepoDetails($id_repo);
	
	$file_man->setUserLastEnterInRepo($id_repo);
	
	$of_user = getLogUserId();
	$page_title = array($url->getUrl() => $lang->def('_TITLE_LIGHT_REPO'), $repo[LR_TITLE]);
	
	$file_list = $file_man->getRepoFileListOfAuthor($id_repo, $of_user);
	
	$GLOBALS['page']->add(
		getTitleArea($page_title , 'light_repo')
		.'<div class="std_block" id="light_repo_block">', 'content');
	
	if(isset($_GET['result'])) {
			
			switch($_GET['result']) {
				case "file_ok" : { $GLOBALS['page']->add(getAppendAlert($lang->def('_FILE_OK')), 'content'); };break;
				case "file_err" : { $GLOBALS['page']->add(getAppendAlert($lang->def('_FILE_ERR'), 'failure'), 'content'); };break;
			}
		}
	
	$table = new TypeOne(0, $lang->def('_CAPTION_USER_FILE_LIST'), $lang->def('_SUMMARY_USER_FILE_LIST'));
	
	$content_h = array(
		$lang->def('_FILENAME'),
		$lang->def('_DESCRIPTION'),
		$lang->def('_LOAD_DATE'),
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD_FILE').'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL_FILE').'" />'
	);
	$type_h = array('', '', '', 'image', 'image');
	$table->addHead($content_h, $type_h);
	
	$url->addToStdQuery('id_repo='.$id_repo);
	
	while($file = mysql_fetch_row($file_list)) {
		
		// convert filename
		$file[LR_FILE_NAME] = implode( '_', array_slice(explode('_', $file[LR_FILE_NAME]), 3) );
		
		$content = array();
		
		$content[] = ''
			.'<a href="'.$url->getUrl('op=download_file&id_repo='.$id_repo.'&id_file='.$file[LR_FILE_ID]).'" title="'.$lang->def('_DOWNLOAD_FILE').''.strip_tags($file[LR_FILE_NAME]).'">'
				.'<img src="'.getPathImage('fw').'standard/download.gif" alt="'.$lang->def('_DOWNLOAD_FILE').'" /> '.$file[LR_FILE_NAME]
			.'</a>';
		
		$content[] = $file[LR_FILE_DESCR];
		
		$content[] = createDateDistance($file[LR_FILE_POSTDATE], 'standard', true);
		//$content[] = $file[LR_FILE_DESCR];
		
		$content[] = ''
			.'<a href="'.$url->getUrl('op=mod_file&id_repo='.$id_repo.'&id_file='.$file[LR_FILE_ID]).'"' .
					' title="'.$lang->def('_MOD_FILE').''.strip_tags($file[LR_FILE_NAME]).'">'
				.'<img src="'.getPathImage('fw').'standard/mod.gif" alt="'.$lang->def('_MOD_FILE').' : '.strip_tags($file[LR_FILE_NAME]).'" />'
			.'</a>';
		
		$content[] = ''
			.'<a href="'.$url->getUrl('op=del_file&id_repo='.$id_repo.'&id_file='.$file[LR_FILE_ID]).'"' .
					' title="'.$lang->def('_DEL_FILE').''.strip_tags($file[LR_FILE_NAME]).'">'
				.'<img src="'.getPathImage('fw').'standard/rem.gif" alt="'.$lang->def('_DEL_FILE').' : '.strip_tags($file[LR_FILE_NAME]).'" />'
			.'</a>';
		
		$table->addBody($content, false, false, 'file_container_'.$file[LR_FILE_ID]);
	}
	$table->addActionAdd('<a class="dd_link" href="'.$url->getUrl('op=mod_file&id_repo='.$id_repo).'" title="'.$lang->def('_ADD_FILE').'">'
			.$lang->def('_UPLOAD_NEW_FILE')
		.'</a>');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=del_file]');
	
	$GLOBALS['page']->add($table->getTable(), 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function modFile(&$url) {
	checkPerm('view');
	$mod_perm = checkPerm('mod', true);
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang 		=& DoceboLanguage::createInstance('light_repo');
	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
	
	$id_repo = importVar('id_repo', true, 0);
	$id_file = importVar('id_file', true, 0);
	
	if(isset($_POST['undo'])) jumpTo($url->getUrl('op='.( $mod_perm ? 'repo_manager_details' : 'repo_my_details' ).'&id_repo='.$id_repo));
	if(isset($_POST['save'])) {
		
		// save changes 
		$file_info[LR_FILE_ID_REPO] = $id_repo;
		$file_info[LR_FILE_NAME] 	= ( isset($_FILES['file_name']) ? $_FILES['file_name'] : false );
		$file_info[LR_FILE_DESCR] 	= $_POST['file_descr'];
		$file_info[LR_FILE_AUTHOR] 	= getLogUserId();
		$file_info[LR_FILE_POSTDATE] = date("Y-m-d H:i:s");
		
		$re = $file_man->saveFile($id_file, $file_info);
		
		jumpTo($url->getUrl('op='.( $mod_perm ? 'repo_manager_details' : 'repo_my_details' ).'&id_repo='.$id_repo.'&result='.($re?'file_ok':'file_err')));
	}
	
	$repo = $file_man->getRepoDetails($id_repo);
	$page_title = array($url->getUrl() => $lang->def('_TITLE_LIGHT_REPO'), $url->getUrl('op='.( $mod_perm ? 'repo_manager_details' : 'repo_my_details' ).'&id_repo='.$id_repo) => $repo[LR_TITLE]);
	
	if($id_file == 0) {
		$page_title[] = $lang->def('_UPLOAD_NEW_FILE');
		$file = array();
		$file[LR_FILE_NAME] = false;
		$file[LR_FILE_DESCR] = '';
	} else {
		$file = mysql_fetch_row($file_man->getFileInfo($id_file));
		$page_title[] = implode( '_', array_slice(explode('_', $file[LR_FILE_NAME]), 3) );
	}
	
	$GLOBALS['page']->add(
		getTitleArea($page_title , 'light_repo')
		.'<div class="std_block">', 'content');
	
	$GLOBALS['page']->add(
		Form::openForm('mod_file', $url->getUrl('op=mod_file&id_repo='.$id_repo), false, false, 'multipart/form-data')
		.Form::openElementSpace()
		
		.Form::getHidden('id_file', 'id_file', $id_file)
		.Form::getHidden('id_repo', 'id_repo', $id_repo)
		.Form::getExtendedFileField(	$lang->def('_UPLOAD'), 
										'file_name', 
										'file_name', 
										$file[LR_FILE_NAME], 
										implode( '_', array_slice(explode('_', $file[LR_FILE_NAME]), 3) ),
										true,
										false
									)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'file_descr', 'file_descr', importVar('file_descr', false, $file[LR_FILE_DESCR], true) )
		
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function delFile(&$url) {
	checkPerm('view');
	$mod_perm = checkPerm('mod', true);
	
	require_once($GLOBALS["where_lms"]."/lib/lib.light_repo.php");
	
	$re = false;
	if(isset($_GET['confirm'])) {
		$id_file = get_req('id_file', DOTY_INT, 0);
		$id_repo = get_req('id_repo', DOTY_INT, 0);
		
		$file_man = new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
		$re = $file_man->deleteFile($id_file);
	}
	jumpTo($url->getUrl('op='.( $mod_perm ? 'repo_manager_details' : 'repo_my_details' ).'&id_repo='.$id_repo.'&result='.($re?'file_ok':'file_err')));	
}

function repoManagerDetails(&$url) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$lang 		=& DoceboLanguage::createInstance('light_repo');
	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
	
	$id_repo = importVar('id_repo', true, 0);
	
	// recovering file repository information
	$repo = $file_man->getRepoDetails($id_repo);
	
	$file_man->setUserLastEnterInRepo($id_repo);	
	
	$GLOBALS['page']->add(
		getTitleArea( array($url->getUrl() => $lang->def('_TITLE_LIGHT_REPO'), $repo[LR_TITLE]), 'light_repo')
		.'<div class="std_block">', 'content');
	
	$last_enter = $file_man->getUserLastEnterInRepo($id_repo);
	$file_list = $file_man->getRepoUserListWithFileCount($id_repo, $last_enter);
	
	$table = new TypeOne(0, $lang->def('_CAPTION_USER_FILE_LIST'), $lang->def('_SUMMARY_USER_FILE_LIST'));
	
	$content_h = array(
		$lang->def('_USERNAME'),
		$lang->def('_LOADED_FILE'),
		$lang->def('_ACTIONS')
	);
	$type_h = array('', '', 'image');
	$table->addHead($content_h, $type_h);
	
	$url->addToStdQuery('id_repo='.$id_repo);
	
	while(list(,$file) = each($file_list)) {
		
		$content = array();
		$content[] = $file['username'];
		$content[] = ( isset($file['file_count']) ? $file['file_count'] : '0' )
			.( isset($file['file_new']) ? '<b>('.$file['file_new'].$lang->def('_REPO_NEW_FILE').' )</b> ' : '' );
		
		if(isset($file['file_count'])) {
			$content[] = ''
			.'<a href="'.$url->getUrl('op=repo_user_details&id_user='.$file['id_user']).'" title="'.$lang->def('_VIEW_USER_FILE_LIST').''.strip_tags($file['username']).'">'
				.'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_VIEW_USER_FILE_LIST').''.strip_tags($file['username']).'" />'
			.'</a>';
		} else {
			$content[] = '';
		}
		
		$table->addBody($content);
	}
	$GLOBALS['page']->add($table->getTable(), 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function repoUserDetails(&$url, $passed_repo = 0) {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$lang 		=& DoceboLanguage::createInstance('light_repo');
	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	
	$id_repo = importVar('id_repo', true, $passed_repo);
	$of_user = importVar('id_user', true, 0);
	// recovering file repository information
	$repo = $file_man->getRepoDetails($id_repo);
	
	if(checkPerm('mod', true)) { 
		$page_title = array(
			$url->getUrl() => $lang->def('_TITLE_LIGHT_REPO'), 
			$url->getUrl('op=repo_manager_details&id_repo='.$id_repo) => $repo[LR_TITLE],
			$acl_man->getUserName($of_user)
		);
	} else {
		$of_user = getLogUserId();
		$page_title = array($url->getUrl() => $lang->def('_TITLE_LIGHT_REPO'), $repo[LR_TITLE]);
	}
	$file_list = $file_man->getRepoFileListOfAuthor($id_repo, $of_user);
	
	$GLOBALS['page']->add(
		getTitleArea($page_title , 'light_repo')
		.'<div class="std_block">', 'content');
	
	$table = new TypeOne(0, $lang->def('_CAPTION_USER_FILE_LIST'), $lang->def('_SUMMARY_USER_FILE_LIST'));
	
	$content_h = array(
		$lang->def('_FILENAME'),
		$lang->def('_DESCRIPTION'),
		$lang->def('_LOAD_DATE'),
		'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_DOWNLOAD_FILE').'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL_FILE').'" />'
	);
	$type_h = array('', '', '', 'image', 'image');
	$table->addHead($content_h, $type_h);
	
	$url->addToStdQuery('id_repo='.$id_repo);
	
	while($file = mysql_fetch_row($file_list)) {
		
		$content = array();
		$content[] = implode( '_', array_slice(explode('_', $file[LR_FILE_NAME]), 3) );
		
		$content[] = $file[LR_FILE_DESCR];
		
		$content[] = createDateDistance($file[LR_FILE_POSTDATE], 'standard', true);
		//$content[] = $file[LR_FILE_DESCR];
		
		$content[] = ''
			.'<a href="'.$url->getUrl('op=download_file&id_file='.$file[LR_FILE_ID]).'" title="'.$lang->def('_DOWNLOAD_FILE').''.strip_tags($file[LR_FILE_NAME]).'">'
				.'<img src="'.getPathImage('fw').'standard/download.gif" alt="'.$lang->def('_DOWNLOAD_FILE').''.strip_tags($file[LR_FILE_NAME]).'" />'
			.'</a>';
		
		$content[] = ''
			.'<a href="'.$url->getUrl('op=del_file&id_repo='.$id_repo.'&id_file='.$file[LR_FILE_ID]).'"' .
					' title="'.$lang->def('_DEL_FILE').''.strip_tags($file[LR_FILE_NAME]).'">'
				.'<img src="'.getPathImage('fw').'standard/rem.gif" alt="'.$lang->def('_DEL_FILE').' : '.strip_tags($file[LR_FILE_NAME]).'" />'
			.'</a>';
		
		$table->addBody($content);
	}
	$GLOBALS['page']->add($table->getTable(), 'content');

	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=del_file]');

	$GLOBALS['page']->add('</div>', 'content');
}

function downloadFile(&$url) {
	checkPerm('view');
	
	// retrive file info
	$id_file = importVar('id_file', true, 0);
	$file_man 	= new LightRepoManager( getLogUserId(), $_SESSION['idCourse'] );
	
	$file = $file_man->getFileInfo($id_file);
	if($file !== false) $file = mysql_fetch_row($file);
		
	if(!checkPerm('mod', true) && ($file[LR_FILE_AUTHOR] != getLogUserId())) { 
		jumpTo($url->getUrl());
	}
	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	sendFile($file_man->getFilePath(), $file[LR_FILE_NAME]);
}

function lightrepoDispatch($op) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url =& UrlManager::getInstance('light_repo');
	$url->setStdQuery('modname=light_repo&op=repolist');
	
	if(isset($_POST['undo'])) $op = 'repolist';
	switch($op) {
		case "repolist" : {
			repoList($url);
		};break;
		case "mod_repo" : {
			modRepo($url);
		};break;
		case "del_repo" : {
			delRepo($url);
		};break;
		
		case "repo_my_details" : {
			repoMyDetails($url);
		};break;
		case "mod_file" : {
			modFile($url);
		};break;
		case "del_file" : {
			delFile($url);
		};break;
		
		case "repo_manager_details" : {
			repoManagerDetails($url);
		};break;
		case "repo_user_details" : {
			repoUserDetails($url);
		};break;
		case "download_file" : {
			downloadFile($url);
		};break;
	}
}