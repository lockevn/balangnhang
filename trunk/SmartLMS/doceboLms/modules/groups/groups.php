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

function groups() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$lang 		=& DoceboLanguage::createInstance('groups', 'lms');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$mod_perm 	= checkPerm('mod', true);
	$subs_perm 	= checkPerm('subscribe', true);
	
	// Retrive groups
	$acl_man->setContext('/lms/course/'.$_SESSION['idCourse'].'/group');
	$id_groups 	= $acl_man->getGroupsIdstFromBasePath('/lms/course/'.$_SESSION['idCourse'].'/group', array('course'));
	if(!empty($id_groups)) $groups = $acl_man->getGroups($id_groups);
	else $groups = array();
	
	// compose table
	$tb = new TypeOne(0, $lang->def('_GROUP_CAPTION'), $lang->def('_GROUP_SUMMARY'));
	$type_h = array('', '');
	$cont_h = array($lang->def('_NAME'), $lang->def('_DESCRIPTION'));
	if($subs_perm) {
		
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/groups.gif" alt="'.$lang->def('_ALT_SUBSCRIBE').'" '
						.'title="'.$lang->def('_SUBSCRIBE_USER').'" />';
	}
	if($mod_perm) {
		
		$type_h[] = 'image';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" '
			.'title="'.$lang->def('_MOD_GROUP').'" />';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" '
			.'title="'.$lang->def('_DEL_GROUP').'" />';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	if(!empty($groups))
	while(list(, $group) = each($groups)) {
		
		$id_group = $group[ACL_INFO_IDST];
		$group_id = $acl_man->relativeId($group[ACL_INFO_GROUPID]);
		if($group_id != 'alluser') {
			
			$cont = array(	$group_id, 
							$group[ACL_INFO_GROUPDESCRIPTION]);
			if($subs_perm) {
				
				$cont[] = '<a href="index.php?modname=groups&amp;op=subscribe&amp;id_group='.$id_group.'&amp;load=1" '
							.'title="'.$lang->def('_SUBSCRIBE_USER').' : '.strip_tags($group_id).'">'
						.'<img src="'.getPathImage().'standard/groups.gif" alt="'.$lang->def('_ALT_SUBSCRIBE').'"  /></a>';
			}
			if($mod_perm) {
				
				$cont[] = '<a href="index.php?modname=groups&amp;op=editgroup&amp;id_group='.$id_group.'" '
							.'title="'.$lang->def('_MOD_GROUP').' : '.strip_tags($group_id).'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'"  /></a>';
				$cont[] = '<a href="index.php?modname=groups&amp;op=delgroup&amp;id_group='.$id_group.'" '
							.'title="'.$lang->def('_DEL_GROUP').' : '.strip_tags($group_id).'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" /></a>';
			}
			$tb->addBody($cont);
		}
	}
	if($mod_perm) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delgroup]');
		
		$tb->addActionAdd('<a href="index.php?modname=groups&amp;op=editgroup" '
							.'title="'.$lang->def('_ADD_GROUP_T').'">'
						.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'"  /> '
						.$lang->def('_ADD_GROUP').'</a>');
	}
	// output
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_GROUPS'), 'groups')
		.'<div class="std_block">'
	, 'content');
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" : $GLOBALS['page']->add( getResultUi($lang->def('_OPERATION_SUCCESSFUL')), 'content');break;
			case "err" : $GLOBALS['page']->add( getErrorUi($lang->def('_OPERATION_FAILURE')), 'content');break;
		}
	}
	$GLOBALS['page']->add(
		$tb->getTable()
		.'</div>'
	, 'content');
}

function editgroup() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$lang 		=& DoceboLanguage::createInstance('groups', 'lms');
	
	if(isset($_GET['id_group'])) {
		
		$acl_man->setContext('/lms/course/'.$_SESSION['idCourse'].'/group');
		$group = $acl_man->getGroup($_GET['id_group'], false);
		$group_name 	= $acl_man->relativeId($group[ACL_INFO_GROUPID]);
		$group_descr 	= $group[ACL_INFO_GROUPDESCRIPTION];
	} else {
		
		$group_name = '';
		$group_descr = '';
	}
	$page_title = array(
		'index.php?modname=groups&amp;op=groups' => $lang->def('_GROUPS'), 
		( isset($_GET['id_group']) ? $lang->def('_MOD_GROUP').' : '.$group_name : $lang->def('_ADD_GROUP')) 
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'groups', $lang->def('_ALT_GROUPS'))
		.'<div class="std_block">'
		.getBackUi('index.php?modname=groups&amp;op=groups', $lang->def('_BACK'))
		.Form::openForm('new_groups', 'index.php?modname=groups&amp;op=savegroup')
		
		.Form::openElementSpace()
		.( isset($_GET['id_group']) ? Form::getHidden('id_group', 'id_group', $_GET['id_group']) : '' )
		.Form::getTextfield($lang->def('_NAME'), 'group_groupid', 'group[groupid]', 255, $group_name)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'group_description', 'group_description', $group_descr)
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.form::getButton('save', 'save', $lang->def('_SAVE'))
		.form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function savegroup() {
	checkPerm('mod');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$acl_man->setContext('/lms/course/'.$_SESSION['idCourse'].'/group');
	if(isset($_POST['id_group'])) {
		
		$groupoid = $_POST['group']['groupid'];
		if($acl_man->updateGroup( $_POST['id_group'], $groupoid, $_POST['group_description'], false, 'course', 'lms,')) 
			jumpTo('index.php?modname=groups&op=groups&amp;result=ok');
		else jumpTo('index.php?modname=groups&op=groups&amp;result=err');
	} else {
		
		$groupoid = $_POST['group']['groupid'];
		if($acl_man->registerGroup( $groupoid, $_POST['group_description'], false, 'course', 'lms,')) 
			jumpTo('index.php?modname=groups&op=groups&amp;result=ok');
		else jumpTo('index.php?modname=groups&op=groups&amp;result=err');
	}
}

function delgroup() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang 		=& DoceboLanguage::createInstance('groups', 'lms');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$acl_man->setContext('/lms/course/'.$_SESSION['idCourse'].'/group');
	$id_group 	= importVar('id_group', true, 0);
	
	if(isset($_POST['confirm']) || isset($_GET['confirm'])) {
		
		if($acl_man->deleteGroup($id_group))
			jumpTo('index.php?modname=groups&op=groups&result=ok');
		else jumpTo('index.php?modname=groups&op=groups&result=err');
	} else {
		
		$acl_man->setContext('/lms/course/'.$_SESSION['idCourse'].'/group');
		$group = $acl_man->getGroup($_GET['id_group'], false);
		
		$form = new Form();
		$page_title = array(
			'index.php?modname=groups&amp;op=groups' => $lang->def('_GROUPS'), 
			$lang->def('_DEL_GROUP') 
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'groups', $lang->def('_ALT_GROUPS'))
			.'<div class="std_block">'
			.$form->openForm('del_advice', 'index.php?modname=groups&amp;op=delgroup')
			.$form->getHidden('id_group', 'id_group', $id_group)
			.getDeleteUi(	$lang->def('_AREYOUSURE'), 
							'<span>'.$lang->def('_NAME').' : </span>'.$acl_man->relativeId($group[ACL_INFO_GROUPID]), 
							false, 
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}

function subscribe() {
	checkPerm('subscribe');
	
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	$lang =& DoceboLanguage::createInstance('groups', 'lms');
	$out =& $GLOBALS['page'];
	$id_group = importVar('id_group', true, 0);
	
	$acl_man = new DoceboACLManager();
	$user_select = new Module_Directory();
	
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = FALSE;
	$user_select->show_orgchart_selector = FALSE;
	
	$user_select->nFields = 0;
	
	if(isset($_GET['load'])) {
		
		$users = $acl_man->getGroupUMembers($id_group);
		$user_select->resetSelection($users);
	}
	$arr_idstGroup = $acl_man->getGroupsIdstFromBasePath('/lms/course/'.(int)$_SESSION['idCourse'].'/subscribed/');
	$user_select->setUserFilter('group',$arr_idstGroup);
	
	$user_select->setPageTitle( getTitleArea(
		array('index.php?modname=groups&amp;op=groups' => $lang->def('_GROUPS'), 
		$lang->def('_SUBSCRIBE_USER') ), 
	'groups', $lang->def('_ALT_GROUPS')) );
	
	$user_select->loadSelector('index.php?modname=groups&amp;op=subscribe&amp;id_group='.$id_group, 
			false, 
			$lang->def('_MANAGE_GROUP_SUBSCRIPTION'), 
			true, 
			true );
	
}

function savemembers() {
	checkPerm('subscribe');
	
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	
	$id_group = importVar('id_group', true, 0);
	
	$acl_man = new DoceboACLManager();
	$user_select = new Module_Directory();
	
	$user_selected 	= $user_select->getSelection($_POST);
	$old_users = $acl_man->getGroupUMembers($id_group);
	
	$add_members = array_diff($user_selected, $old_users);
	$del_members = array_diff($old_users, $user_selected);
	
	$re = true;
	if ($user_selected === $old_users)
	{
		jumpTo('index.php?modname=groups&op=groups&result=ok');
		return;
	}
	
	if(count($add_members)) {
		
		while(list(, $idst_user) = each($add_members)) { 
			
			$re &= $acl_man->addToGroup( $id_group, $idst_user );
		}
		
	}
	if(count($del_members)) {
		
		while(list(, $idst_user) = each($del_members)) { 
			
			$re &= $acl_man->removeFromGroup( $id_group, $idst_user );
		}
	}
	if(!$re) jumpTo('index.php?modname=groups&op=groups&result=err');
	jumpTo('index.php?modname=groups&op=groups&result=ok');
}

function groupDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'groups';
	if(isset($_POST['cancelselector'])) $op = 'groups';
	if(isset($_POST['okselector'])) $op = 'savemembers';
	switch($op) {
		case "groups" : {
			groups();
		};break;
		
		case "subscribe" : {
			subscribe();
		};break;
		case "savemembers" : {
			savemembers();
		};break;
		
		case "editgroup" : {
			editgroup();
		};break;
		case "savegroup" : {
			savegroup();
		};break;
		
		case "delgroup" : {
			delgroup();
		};break;
	}
}

?>