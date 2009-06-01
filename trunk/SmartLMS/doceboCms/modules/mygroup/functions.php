<?php

/************************************************************************/
/* DOCEBO CMS - Content Managment System								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!$GLOBALS['current_user']->isAnonymous()) {

function group() {
	//-TP//checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.navbar.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	$lang =& DoceboLanguage::createInstance('mygroup', 'cms');

	$field_search = importVar('field_search');
	$search = ( isset($_POST['search']) && $_POST['search'] != '' ? $_POST['search'] : false );
	if(isset($_POST['clear'])) $search = false;

	$acl_man 		=& $GLOBALS['current_user']->getAclManager();
	$acl 			=& $GLOBALS['current_user']->getAcl();
	$groups 		=& $acl_man->getAllGroupsId(	array('free', 'moderate', 'private'),
												$search);
	$hidden_group = array();
	if(!isset($_POST['working']))	{

		$user_group 	= $acl->getSTGroupsST(getLogUserId());
		$user_group 	= array_flip($user_group);
	} else {

		if(isset($_POST['group_sel'])) {
			$user_group = $_POST['group_sel'];
			$hidden_group = array_diff($user_group, $groups);
		}
		else $user_group = array();
	}
	$GLOBALS['page']->add(
		//getTitleArea($lang->def('_MYGROUP'), 'mygroup')
		'<div class="std_block">'
		.Form::openForm('group_subscription', 'index.php?mn=mygroup&amp;pi='.getPI().'&amp;op=group')
		.Form::getHidden('working', 'working', 1)
		.Form::getOpenFieldset($lang->def('_FILTER'))
		.Form::getTextfield($lang->def('_SEARCH_ARG'), 'search', 'search', '255',
			( isset($_POST['search']) && !isset($_POST['clear']) ? $_POST['search'] : '' ) )
		.Form::openButtonSpace()
		.Form::getButton('search_button', 'search_button', $lang->def('_SEARCH'))
		.( $search ? Form::getButton('clear', 'clear', $lang->def('_CLEAR')) : '' )
		.Form::closeButtonSpace()
		.Form::getCloseFieldset()
	, 'content');
	if(is_array($hidden_group)) {
		while(list(, $id) = each($hidden_group)) {

			$GLOBALS['page']->add(Form::getHidden('group_sel_'.$id, 'group_sel['.$id.']', $id), 'content');
		}
	}
	if(!empty($groups)) {

		$tb = new TypeOne(0, $lang->def('_MYGROUP_CAPTION'), $lang->def('_MYGROUP_SUMMARY'));

		$type_h = array('image', 'nowrap', '');
		$cont_h = array('<span class="access-only">'.$lang->def('_SUBSCRIBE').'</span>',
						$lang->def('_NAME'),
						$lang->def('_DESCRIPTION') );
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		while(list($id, $info) = each($groups)) {

			$cont = array();
			if($info['type'] != 'private') {
				$cont[] = Form::getInputCheckbox(	'group_sel_'.$id,
											'group_sel['.$id.']',
											$id,
											isset($user_group[$id]),
											'' );
				$cont[] = '<label for="group_sel_'.$id.'">'.$info['type_ico'].' '.$info['groupid'].'</label>';
				$cont[] = $info['description'];
			} elseif(isset($user_group[$id])) {

				$cont[] = '<img src="'.getPathImage().'standard/check.gif" alt="'.$lang->def('_SUBSCRIBE').'" />';
				$cont[] = '<label for="group_sel_'.$id.'">'.$info['type_ico'].' '.$info['groupid'].'</label>';
				$cont[] = $info['description'];
			}
			$tb->addBody($cont);
		}
		$GLOBALS['page']->add(
			$tb->getTable()
			.Form::openButtonSpace()
			.Form::getButton('save', 'save', $lang->def('_SAVE'))
			.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
			.Form::closeButtonSpace()
		, 'content');
	}
	$GLOBALS['page']->add(
			Form::closeForm()
			.'</div>', 'content');
}

function savesel() {
	//checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('mygroup', 'cms');

	$mand_sym = '<span class="mandatory">*</span>';
	$extra_field = new FieldList();

	$GLOBALS['page']->add(
		//getTitleArea($lang->def('_MYGROUP'), 'mygroup')
		'<div class="std_block">'
	, 'content');

	$selected = array();
	if(isset($_POST['group_sel'])) $selected = $_POST['group_sel'];
	elseif(isset($_POST['group_sel_implode'])) $selected = explode(',', $_POST['group_sel_implode']);

	$play_field = $extra_field->playFieldsForUser( 	getLogUserId(),
											$selected,
											false,
											true,
											array('readwrite'));

	if(isset($_POST['save_field']) || $play_field === false || $play_field == '') {

		$re_filled = $extra_field->isFilledFieldsForUser(	getLogUserId(),
															$selected );
		if($re_filled !== true) {

			$GLOBALS['page']->add(getErrorUi(implode('<br/>', $re_filled)), 'content');
		} else {

			$acl	 =& $GLOBALS['current_user']->getAcl();
			$acl_man =& $GLOBALS['current_user']->getAclManager();

			$groups 		=& $acl_man->getAllGroupsId(array('free', 'moderate'));
			$groups_id 		= array_keys($groups);
			$user_group 	= $acl->getSTGroupsST(getLogUserId());

			$add_groups 	= array_diff($selected, $user_group);
			$del_groups 	= array_diff($groups_id, $selected);

			$moderate_add = false;
			if(!empty($add_groups))
			while(list(, $idst) = each($add_groups)) {

				if($groups[$idst]['type'] == 'free') {
					$acl_man->addToGroup($idst, getLogUserId());
				} elseif($groups[$idst]['type'] == 'moderate') {
					$acl_man->addToWaitingGroup($idst, getLogUserId());
					$moderate_add = true;
				}
			}
			if($moderate_add === true) {

				require_once($GLOBALS['where_framework'] . '/lib/lib.eventmanager.php');

				// message to user that is odified
				$msg_composer = new EventMessageComposer('admin_directory', 'framework');

				$msg_composer->setSubjectLangText('email', '_TO_APPROVE_GROUP_USER_SBJ', false);
				$msg_composer->setBodyLangText('email', '_TO_APPROVE_GROUP_USER_TEXT', array(	'[url]' => $GLOBALS['lms']['url']) );

				$msg_composer->setSubjectLangText('sms', '_TO_APPROVE_GROUP_USER_SBJ_SMS', false);
				$msg_composer->setBodyLangText('sms', '_TO_APPROVE_GROUP_USER_TEXT_SMS', array(	'[url]' => $GLOBALS['lms']['url']) );
				$idst_approve = $acl->getRoleST('/framework/admin/admin_directory/editgroup');
				$recipients = $acl_man->getRoleMembers($idst_approve);

				createNewAlert(	'UserGroupModerated', 'directory', 'moderate', '1', 'User group subscription to moderate',
							$recipients, $msg_composer );
			}
			if(!empty($del_groups))
			while(list(, $idst_group) = each($del_groups)) {

				$extra_field->removeUserEntry(getLogUserId(), $idst_group);
				$acl_man->removeFromGroup($idst_group, getLogUserId());
			}
			// Save fields
			$extra_field->storeFieldsForUser( getLogUserId() );
			jumpTo('index.php?mn=mygroup&amp;pi='.getPI().'&amp;op=group');
		}
	}

	$GLOBALS['page']->add(
		'<div class="reg_note">'
		.$lang->def('_GROUPS_FIELDS')
		.'<ul class="reg_instruction">'
			.'<li>'.str_replace('[mandatory]', $mand_sym, $lang->def('_REG_MANDATORY')).'</li>'
		.'</ul>'
		.'</div>'
		.Form::openForm('group_subscription', 'index.php?mn=mygroup&amp;pi='.getPI().'&amp;op=savesel')
		.Form::openElementSpace()
		.Form::getHidden('group_sel_implode', 'group_sel_implode',
			( isset($_POST['group_sel_implode']) ? $_POST['group_sel_implode'] : implode(',', $selected) ))
		.$play_field
		.Form::getBreakRow()
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save_field', 'save_field', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function mygroupDispatch($op) {

	if(isset($_POST['undo'])) $op = 'group';
	if(isset($_POST['save'])) $op = 'savesel';


	switch($op) {
		case "group" : {
			group();
		};break;
		case "savesel" : {
			savesel();
		};break;
	}
}

}

?>