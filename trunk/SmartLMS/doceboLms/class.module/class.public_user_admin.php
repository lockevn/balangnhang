<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2005 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package admin-public
 * @subpackage user
 */

require_once(dirname(__FILE__).'/class.definition.php');
require_once($GLOBALS["where_framework"].'/setting.php');
require_once($GLOBALS["where_framework"].'/lib/lib.aclmanager.php');
require_once($GLOBALS["where_lms"].'/lib/lib.public_user_admin.php' );


define("FILTER_FOLD","FILTER_FOLD");

class Module_Public_User_Admin extends LmsModule {

	var $lang 							= NULL;
	var $tab 							= NULL;
	var $aclManager 					= NULL;
	var $selection 						= array();
	var $selection_alt 					= array();
	var $selector_mode 					= FALSE;
	var $use_multi_sel 					= FALSE;
	var $sel_extend 					= NULL;
	var $show_user_selector 			= TRUE;
	var $show_group_selector 			= FALSE;
	var $show_orgchart_selector 		= TRUE;
	var $show_orgchart_simple_selector 	= FALSE;
	var $multi_choice 					= TRUE;
	var	$group_filter 					= array();
	var $user_filter 					= array();
	var $not_idst_filter				= array();
	var $page_title 					= FALSE;
	var $select_all						= FALSE;
	var $nFields						= FALSE;
	var $requested_tab					= FALSE;
	var $_extra_form					= array();
	var $show_only_group_name 			= FALSE;

	var $show_simple_filter 			= FALSE;

	var $hide_anonymous 				= TRUE;
	var $hide_suspend					= TRUE;

	var $lms_editions_filter 			= FALSE;


	function Module_Public_User_Admin() {
		parent::LmsModule();
		$this->aclManager = new DoceboACLManager();
		$this->lang =& DoceboLanguage::createInstance('admin_directory', 'framework');

		require_once($GLOBALS["where_framework"]."/lib/lib.selextend.php");
		$this->sel_extend= new ExtendSelector();
		$this->multi_choice = $GLOBALS['use_org_chart_multiple_choice'];
	}

	function public_user_admin_save_state( &$data, &$selection, &$selection_alt ) {
		$_SESSION['public_user_admin'] = serialize($data);
		$_SESSION['public_user_admin_selection'] = serialize($selection);
		$_SESSION['public_user_admin_selection_alt'] = serialize($selection_alt);
	}

	function &public_user_admin_load_state() {

		$result = array( array(), array(), array() );
		if( isset($_SESSION['public_user_admin']) && isset($_SESSION['public_user_admin_selection']) ) {
			$result = array( unserialize( $_SESSION['public_user_admin'] ),
							unserialize( $_SESSION['public_user_admin_selection'] ),
							unserialize( $_SESSION['public_user_admin_selection_alt'] ));
		}
		return $result;
	}

	function isParseDataAvailable( $arrayState ) {
		return isset( $arrayState[DIRECTORY_ID] );
	}

	function parseInput($arrayState) {
		$itemSelectedMulti = array();
		$printedItems = array();
		$itemSelectedMulti_alt = array();
		$printedItems_alt = array();
		if( isset( $arrayState[DIRECTORY_ID] ) ) {
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTITEM] )) {
				$itemSelectedMulti = array_keys ( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTITEM] );
				//print_r( $arrayState[DIRECTORY_ID] );
			}
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTFOLD] )) {
				//print_r( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTFOLD] );
				$itemSelectedMulti_alt = array_keys ( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTFOLD] );
			}
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_ID_PRINTEDITEM] )) {
				$printedItems = unserialize(urldecode($arrayState[DIRECTORY_ID][DIRECTORY_ID_PRINTEDITEM]));
			}
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_ID_PRINTEDFOLD] )) {
				$printedItems_alt = unserialize(urldecode($arrayState[DIRECTORY_ID][DIRECTORY_ID_PRINTEDFOLD]));
				//print_r( $printedItems_alt );
			}
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTMONO] )) {
				$itemSelectedMulti = array($arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTMONO]);
			}
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTRADIO] )) {
				foreach( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTRADIO] as $key => $val ) {
					// $key contains tree normal group and descendants group idst
					// concat with an _
					list( $idst, $idst_desc ) = split( '_', $key );
					$printedItems[] = $idst;
					$printedItems[] = $idst_desc;
					if( $val != '' ) $itemSelectedMulti[] = $val;
				}
			}
			if( isset( $arrayState[DIRECTORY_ID][DIRECTORY_OP_SELECTALL] )) {
				$this->select_all = TRUE;
			}
		}
		$unselectedItems = array_diff( $printedItems, $itemSelectedMulti);
		$this->selection = array_diff($this->selection, $unselectedItems );
		$this->selection = array_values(array_unique(array_merge( $this->selection, $itemSelectedMulti )));

		$unselectedItems_alt = array_diff( $printedItems_alt, $itemSelectedMulti_alt);
		$this->selection_alt = array_diff($this->selection_alt, $unselectedItems_alt );
		$this->selection_alt = array_values(array_unique(array_merge( $this->selection_alt, $itemSelectedMulti_alt )));
		//print_r($this->selection_alt );
		//print_r($this->selection);
	}

	function public_user_admin_create_TabView( $op ) {
		global $_tab_op_map;
		$arr_tabs = array();
		require_once($GLOBALS["where_framework"].'/lib/lib.tab.php');
		$this->tab = new TabView( DIRECTORY_TAB, 'index.php?modname=public_user_admin&amp;op=public_user_admin' );

		if( $this->show_user_selector ) {
			$tabPeople = new TabElemDefault(PEOPLEVIEW_TAB, $this->lang->def( '_DIRECTORY_PEOPLEVIEWTITLE' ), getPathImage('fw').'area_title/directory_people.gif');
			$this->tab->addTab($tabPeople);
			$arr_tabs[] = PEOPLEVIEW_TAB;
		}
		
		if( $this->show_group_selector && $GLOBALS['use_groups'] == '1' ) {
			$tabGroup = new TabElemDefault(GROUPVIEW_TAB, $this->lang->def( '_DIRECTORY_GROUPVIEWTITLE' ), getPathImage('fw').'area_title/directory_group.gif');
			$this->tab->addTab($tabGroup);
			$arr_tabs[] = GROUPVIEW_TAB;
		}
		if( $this->show_orgchart_selector && $GLOBALS['use_org_chart'] == '1') {
			$tabOrg = new TabElemDefault(ORGVIEW_TAB, $this->lang->def( '_DIRECTORY_ORGVIEWTITLE' ), getPathImage('fw').'area_title/directory_org.gif');
			$this->tab->addTab($tabOrg);
			$arr_tabs[] = ORGVIEW_TAB;
		}

		if( count($this->selection) == 0 )
			list($extra_data, $this->selection, $this->selection_alt) = $this->public_user_admin_load_state();
		$this->parseInput( $_POST );
		$this->tab->parseInput( $_POST, $extra_data );

		if( $this->tab->getActiveTab() === NULL )
			if( in_array ( $op, $arr_tabs ) )
				$this->tab->setActiveTab($op);
			else
				$this->tab->setActiveTab(ORGVIEW_TAB);
		if( $this->requested_tab !== FALSE )
			$this->tab->setActiveTab($this->requested_tab);
	}

	function public_user_admin_destroy_TabView( ) {
		$this->public_user_admin_save_state( $this->tab->getState(), $this->selection, $this->selection_alt );
	}


	function resetSelection($array_selection = NULL, $array_selection_alt = NULL) {
		if( $array_selection === NULL )
			$array_selection = array();
		if( $array_selection_alt === NULL )
			$array_selection_alt = array();
		$this->selection = $array_selection;
		$this->selection_alt = $array_selection_alt;
		$_SESSION['public_user_admin_selection'] 			= serialize($array_selection);
		$_SESSION['public_user_admin_start_selection'] 		= serialize($array_selection);
		$_SESSION['public_user_admin_selection_alt'] 		= serialize($array_selection_alt);
		$_SESSION['public_user_admin_start_selection_alt'] 	= serialize($array_selection_alt);
	}

	function getSelection( $arrayData ) {
		list($extra_data,$this->selection,$this->selection_alt) = $this->public_user_admin_load_state();
		$this->parseInput( $arrayData );
		return $this->selection;
	}

	function getSelectionAlt( $arrayData ) {
		list($extra_data,$this->selection,$this->selection_alt) = $this->public_user_admin_load_state();
		$this->parseInput( $arrayData );
		return $this->selection_alt;
	}

	function getAllSelection( $arrayData ) {
		list($extra_data,$this->selection,$this->selection_alt) = $this->public_user_admin_load_state();
		$this->parseInput( $arrayData );
		return array( $this->selection, $this->selection_alt);
	}

	function getPrintedItems($arrayState) {
		return unserialize(urldecode($arrayState[DIRECTORY_ID][DIRECTORY_ID_PRINTEDITEM]));
	}

	function getStartSelection() {
		return unserialize( $_SESSION['public_user_admin_start_selection'] );
	}

	function getStartSelectionAlt() {
		return unserialize( $_SESSION['public_user_admin_start_selection_alt'] );
	}

	function getUnselected() {
		return array_diff( $this->getStartSelection(), $this->selection );
	}

	function getUnselectedAlt() {
		return array_diff( $this->getStartSelectionAlt(), $this->selection_alt );
	}

	function useExtraMenu() {
		return true;
	}

	function loadExtraMenu() {
		loadAdminModuleLanguage($this->module_name);
	}

	function loadBody() {
		global $op, $modname;
		
		$pref = new UserPreferences(getLogUserId());
		if(!$pref->getPreference('admin_rules.max_user_insert') && $pref->getPreference('admin_rules.limit_user_insert') == 'on')
		{
			$lang =& DoceboLanguage::createInstance('profile', 'framework');
			$GLOBALS['page']->add('<p class="result_container"><strong>'.$lang->def('_INSERT_USER_LIMIT_REACHED').'</strong></p>', 'content');
		}
		switch( $op ) {

			// group related actions ==========================================
			/*case 'listgroup': {
				checkPerm('view_group', false, 'directory', 'framework');
				$this->loadGroupView();
			};break;
			case 'editgroup': {
				checkPerm('editgroup', false, 'directory', 'framework');
				$this->editGroup( importVar( 'groupid', FALSE, '' ) );
			};break;
			case 'deletegroup': {
				checkPerm('delgroup', false, 'directory', 'framework');
				$this->deleteGroup( importVar( 'groupid', FALSE, '' ) );
			};break;

			// group members related actions ===================================
			case 'import_groupuser' : {
				checkPerm('associate_group', false, 'directory', 'framework');
				$this->importToGroup();
			};break;
			case 'import_groupuser_2' : {
				checkPerm('associate_group', false, 'directory', 'framework');
				$this->importToGroup_step2();
			};break;
			case 'import_groupuser_3' : {
				checkPerm('associate_group', false, 'directory', 'framework');
				$this->importToGroup_step3();
			};break;

			case 'addtogroup': {
				checkPerm('associate_group', false, 'directory', 'framework');
				$this->addToGroup( importVar( 'groupid', FALSE, '' ) );
			};break;
			case 'membersgroup': {
				checkPerm('view_group', false, 'directory', 'framework');
				$this->membersGroup( importVar( 'groupid', FALSE, '' ) );
			};break;
			case "waitinggroup" : {
				checkPerm('view_group', false, 'directory', 'framework');
				$this->waitingUserGroup( importVar( 'groupid', FALSE, '' ) );
			};break;*/

			// org chart related actions ======================================
			case 'public_user_admin':
			case 'org_chart': {
				checkPerm('view_org_chart', false, 'public_user_admin', true);
				$this->loadOrgChartView();
			};break;
			case 'addtotree': {
				checkPerm('edituser_org_chart', false, 'public_user_admin', true);
				$this->addToTree( importVar( 'treeid', FALSE, '' ) );
			};break;
			case 'assignfield': {
				checkPerm('edituser_org_chart', false, 'public_user_admin', true);
				$this->loadAssignField( importVar( 'groupid', FALSE, '' ) );
			};break;
			case 'assignfieldmandatory': {
				checkPerm('edituser_org_chart', false, 'public_user_admin', true);
				$this->loadAssignField2( importVar( 'groupid', FALSE, '' ) );
			};break;

			// users related actions =========================================
			case 'listuser': {
				checkPerm('view_org_chart', false, 'public_user_admin', true);
				$this->loadPeopleView();
			};break;
			case 'org_createuser': {

				$this->org_createUser();
			};break;
			case 'org_waitinguser': {
				$this->org_waitingUser();
			};break;


			case 'org_manageuser': {
				$this->org_manageuser();
			};break;

			case 'view_deleted_user':
				$this->viewDeletedUser();
			break;

			default: {
				checkPerm('view_org_chart', false, 'public_user_admin', true);
				$this->loadSelector( '', '', '', FALSE );
			}
		}
	}
	
	function _getTableDeletedUser()
	{
		return 'core_deleted_user';
	}
	
	function _getTableUser()
	{
		return 'core_user';
	}
	
	function viewDeletedUser()
	{
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		
		$lang =& DoceboLanguage::createInstance('profile', 'framework');
		$out =& $GLOBALS['page'];
		$out->setWorkingZone('content');
		$acl_man =& $GLOBALS['current_user']->getAclManager();
		
		$max_row = 10;
		$tb = new TypeOne($max_row, $lang->def('_DELETED_USER_CAPTION'), $lang->def('_DELETED_USER_SUMMARY'));
		$tb->initNavBar('ini', 'link');
		$ini = $tb->getSelectedElement();
		
		$query = "SELECT * FROM ".$this->_getTableDeletedUser()."";
		$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		//print_r($ini);
		if($ini)
			$limit = $ini;
		else
			$limit = 0;
		
		$query = "SELECT d.idst, d.userid, d.firstname, d.lastname, d.email, d.lastenter, d.deletion_date, d.deleted_by, u.userid, u.firstname, u.lastname" .
				" FROM ".$this->_getTableDeletedUser()." AS d JOIN" .
				" ".$this->_getTableUser()." AS u ON d.deleted_by = u.idst" .
				" LIMIT ".$limit.", ".$max_row."";
		
		$result = mysql_query($query);
		
		$out->add(getTitleArea($lang->def('_DELETED_USER_TITLE')).'<div class="std_block">');
		$out->add(getBackUi('index.php?modname=public_user_admin&amp;op=org_chart', "&lt;&lt;".$lang->def('_BACK')));
		
		if ($num_rows)
		{
			$cont_h = array
			(
				$lang->def('_IDST_DELETED_USER'),
				$lang->def('_USERNAME'),
				$lang->def('_FIRSTNAME'),
				$lang->def('_LASTNAME'),
				$lang->def('_EMAIL'),
				$lang->def('_DELETION_DATE'),
				$lang->def('_USERID_DELETER'),
				$lang->def('_FIRSTNAME_DELETER'),
				$lang->def('_LASTNAME_DELETER')
			);
			$type_h = array('', '', '', '', '', '', '', '', '', '', '');
			
			$tb->setColsStyle($type_h);
			$tb->addHead($cont_h);
			
			while(list($idst_deleted, $userid_deleted, $firstname_deleted, $lastname_deleted, $email_deleted, $last_enter_deleted, $deletion_date, $idst_deleter, $userid_deleter, $firstname_deleter, $lastname_deleter) = mysql_fetch_row($result))
			{
				$count = array();
				
				$count[] = $idst_deleted;
				$count[] = $acl_man->relativeId($userid_deleted);
				$count[] = $firstname_deleted;
				$count[] = $lastname_deleted;
				$count[] = $email_deleted;
				
				$count[] = $GLOBALS['regset']->databaseToRegional($deletion_date);
				
				$count[] = $acl_man->relativeId($userid_deleter);
				$count[] = $firstname_deleter;
				$count[] = $lastname_deleter;
				
				$tb->addBody($count);
			}
			
			$out->add(
				$tb->getTable()
				.$tb->getNavBar($ini, $num_rows)
				.'</div>'
			);
		}
		else
		{
			$out->add($lang->def('_NO_USER_DELETED'));
		}
		
		$out->add(getBackUi('index.php?modname=public_user_admin&amp;op=org_chart', "&lt;&lt;".$lang->def('_BACK')));
		$out->add('</div>');
	}
	
	function org_manageuser() {
		checkPerm('view_org_chart', false, 'public_user_admin', true);
		require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');

		$lang =& DoceboLanguage::createInstance('profile', 'framework');

		$profile = new UserProfile( importVar('id_user', true, 0) );
		$profile->init('profile', 'lms', 'modname=public_user_admin&op=org_manageuser&id_user='.importVar('id_user', true, 0), 'ap');
		$profile->enableGodMode();

		$profile->setEndUrl('index.php?modname=public_user_admin&op=org_chart');

		$GLOBALS['page']->add(
			$profile->getHead()
			.getBackUi('index.php?modname=public_user_admin&amp;op=org_chart', $lang->def('_BACK'))

			.$profile->performAction()

			.$profile->getFooter()
		, 'content');
	}

	/**
	 * Set filters for user data retriever
	 * @param string $filter_type one of the following:
	 * 								- "platform": retrieve only user of the platforms
	 * 												given in $filter_arg array
	 * 								- "group": retrieve only user members of the
	 * 												groups given in $filter_arg array
	 * 								- "exclude": exclude users with idst passed in
	 * 												$filter_arg array
	 * @param array $filter_arg an array of platforms or an array of groups or
	 * 								an array of idst (see $filter_type)
	 * @return NULL
	 **/
	function setUserFilter($filter_type, $filter_arg) {
		switch($filter_type) {
			case "platform" : $this->user_filter['platform'] = $filter_arg;break;
			case "user" 	: $this->user_filter['user'] = $filter_arg;break;
			case "group" 	: $this->user_filter['group'] = $filter_arg;break;
			case "exclude"	: $this->user_filter['exclude'] = $filter_arg;break;
		}
		return;
	}

	function setGroupFilter($filter_type, $filter_arg) {

		switch($filter_type) {
			case "platform" : $this->group_filter['platform'] = $filter_arg;break;
			case "group" 	: $this->group_filter['group'] = $filter_arg;break;
			case "path" 	: $this->group_filter['path'] = $filter_arg;break;
		}
		return;
	}

	/**
	 * @param string 	$page_title	the value returned by getTitleArea or an equivalent intestation for the page
	 */
	function setPageTitle($page_title) {

		$this->page_title = $page_title;
	}

	function addFormInfo($string) {

		$this->_extra_form[] = $string;
	}

	function resetFormInfo() {

		$this->_extra_form = array();
	}

	function loadSelector($url, $title, $text, $selector_mode = TRUE ) {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		global $op, $modname;
		$this->public_user_admin_create_TabView($op);
		$this->selector_mode = $selector_mode;

		//print_r($this->selection);

		if( $selector_mode ) {
			if($this->page_title === false) {
				$GLOBALS['page']->add( getTitleArea($title, 'public_user_admin' ), 'content' );
			} else {
				$GLOBALS['page']->add( $this->page_title, 'content' );
			}
			$GLOBALS['page']->add( '<div class="std_block">', 'content');
			$GLOBALS['page']->add( $text.'<br /><br />', 'content' );
			$GLOBALS['page']->addEnd( '</div>', 'content');
		}
		$GLOBALS['page']->add( '<form action="'.$url.'" method="post" id="directoryselector">', 'content' );

		if(is_array($this->_extra_form) && !empty($this->_extra_form)) {

			$GLOBALS['page']->add( implode('', $this->_extra_form), 'content' );
		}

		if (($this->use_multi_sel)) {
			$GLOBALS['page']->add("\n\n");
			$GLOBALS['page']->add($this->sel_extend->writeSelectedInfo());
			$GLOBALS['page']->add("\n\n");
		}

		switch( $this->tab->getActiveTab() ) {
			case PEOPLEVIEW_TAB: {
				$GLOBALS['page']->add( $this->tab->printTabView_Begin('', FALSE), 'content' );
				$this->loadPeopleView($url);
			};break;
			case GROUPVIEW_TAB: {
				$GLOBALS['page']->add( $this->tab->printTabView_Begin('', FALSE), 'content' );
				$this->loadGroupView();
			};break;
			case ORGVIEW_TAB: {
				$GLOBALS['page']->add( $this->tab->printTabView_Begin('', FALSE), 'content' );
				$this->loadOrgChartView();
			};break;
			default : {
				if($this->show_user_selector) {

					$this->tab->setActiveTab(PEOPLEVIEW_TAB);
					$GLOBALS['page']->add( $this->tab->printTabView_Begin('', FALSE), 'content' );
					$this->loadPeopleView($url);
				} elseif($this->show_group_selector && $GLOBALS['use_groups'] == '1') {

					$this->tab->setActiveTab(GROUPVIEW_TAB);
					$GLOBALS['page']->add( $this->tab->printTabView_Begin('', FALSE), 'content' );
					$this->loadGroupView();
				} elseif($this->show_orgchart_selector && $GLOBALS['use_org_chart'] == '1') {

					$this->tab->setActiveTab(ORGVIEW_TAB);
					$GLOBALS['page']->add( $this->tab->printTabView_Begin('', FALSE), 'content' );
					$this->loadOrgChartView();
				}
			};break;
		}
		$GLOBALS['page']->add( $this->tab->printTabView_End(), 'content' );
		$GLOBALS['page']->add( Form::openButtonSpace(), 'content' );
		$GLOBALS['page']->add( Form::getButton( DIRECTORY_ID.'_'.DIRECTORY_OP_SELECTALL, DIRECTORY_ID.'['.DIRECTORY_OP_SELECTALL.']', $this->lang->def('_SELECTALL') ), 'content' );
		$GLOBALS['page']->add( Form::getButton( "okselector", "okselector", $this->lang->def('_SAVE') ), 'content' );
		$GLOBALS['page']->add( Form::getButton( "cancelselector", "cancelselector", $this->lang->def('_CANCEL') ), 'content' );
		$GLOBALS['page']->add( Form::closeButtonSpace(), 'content' );
		$GLOBALS['page']->add( '</form>', 'content' );
		$this->public_user_admin_destroy_TabView( );
	}


	function setNFields($nFields) {
		$this->nFields=$nFields;
	}


	function loadPeopleView($url = '') {
		checkPerm('view_user', false, 'public_user_admin', true);
		$data = new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
		$rend = new TypeOne($GLOBALS['framework']['visuUser']);
		$lv = new PeopleListView('', $data, $rend, 'pepledirectory');

		if ($this->nFields !== FALSE)
			$lv->setNFields($this->nFields);

		if ($this->show_simple_filter !== FALSE)
			$lv->show_simple_filter = TRUE;

		if ($this->lms_editions_filter !== FALSE)
			$lv->lms_editions_filter = TRUE;

		if ($this->hide_anonymous !== FALSE)
			$lv->hide_anonymous = TRUE;
		
		if ($this->hide_suspend !== TRUE)
			$lv->hide_suspend = FALSE;

		if( $url == '' )
			$url = "index.php?modname=public_user_admin&amp;op=listuser";
		$lv->setLinkPagination( $url );
		$lv->aclManager =& $this->aclManager;
		$lv->selector_mode = $this->selector_mode;
		$lv->select_all = $this->select_all;
		$lv->use_multi_sel=$this->use_multi_sel;
		$lv->sel_extend=$this->sel_extend;
		$lv->idModule = 'directory_selector';
		if( $this->selector_mode === FALSE )
			$lv->setInsNew(TRUE);
		$lv->parsePositionData( $_POST );
		$lv->itemSelectedMulti = $this->selection;
		if( $lv->getOp() == 'newitem' ) {
			$this->editPerson();
		} elseif( $lv->getOp() == 'editperson' ) {
			$this->editPerson($lv->getIdSelectedItem());
		} elseif( $lv->getOp() == 'deleteperson' ) {
			$this->deletePerson($lv->getIdSelectedItem());
		} elseif( $lv->getOp() == 'suspendperson' ) {
			$this->suspendPerson($lv->getIdSelectedItem());
			$GLOBALS['page']->add(getResultUi($this->lang->def('_SUSPENDED_USER') ));
		} elseif( $lv->getOp() == 'recoverperson' ) {
			$this->recoverPerson($lv->getIdSelectedItem());
			$GLOBALS['page']->add(getResultUi($this->lang->def('_RECOVERD_USER') ));
		} else {
			if( !$this->selector_mode ) {
				$GLOBALS['page']->add( getTitleArea($this->lang->def( '_DIRECTORY_PEOPLEVIEWTITLE' ), 'directory_people' ), 'content' );
				$GLOBALS['page']->add( '<div class="std_block">', 'content' );
				$GLOBALS['page']->addEnd( '</div>', 'content' );
				$GLOBALS['page']->add( '<form id="dirctory_listpeople" action="index.php?modname=public_user_admin&amp;op=listuser" method="post">', 'content' );
				$GLOBALS['page']->addEnd( '</form>', 'content' );
			}
			if( isset( $this->user_filter['exclude'] ) )
				$data->addNotFilter($this->user_filter['exclude']);

			if( isset( $this->user_filter['user'] ) ) {
					$data->setUserFilter($this->user_filter['user']);
			}

			if( isset( $this->user_filter['group'] ) ) {
				foreach( $this->user_filter['group'] as $idstGroup )
					$data->setGroupFilter($idstGroup);
			} else {
				$userlevelid = $GLOBALS['current_user']->getUserLevelId();
				if( $userlevelid != ADMIN_GROUP_GODADMIN) {
					require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
					$adminManager = new PublicAdminManager();
					$data->intersectGroupFilter($adminManager->getAdminTree($GLOBALS['current_user']->getIdSt()));
				}
			}
			// print out the listview
			$GLOBALS['page']->add($lv->printOut(), 'content');
		}
	}

	function deletePerson($userid) {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		if( $userid === FALSE )
			return;
		$arrUser = $this->aclManager->getUser( FALSE, $userid );
		if( $arrUser !== FALSE ) {
			$idst = $arrUser[0];
			$firstname = $arrUser[2];
			$lastname = $arrUser[3];
		}
		$are_title = array(

		);
		$GLOBALS['page']->add(

			'<h2 id="directory_deluser">'.$this->lang->def('_DIRECTORY_DELPERSON').'</h2>'

			.Form::openForm( 	'directorydeleteperson',
								'index.php?modname=public_user_admin&amp;op=org_chart')
			.'<input type="hidden" id="idst" name="idst" value="'.$idst.'" 	\>'
			.getDeleteUi(	$this->lang->def('_AREYOUSURE'),
							$this->lang->def('_USERNAME').' : '.$userid.'<br />'
								.$this->lang->def('_LASTNAME').' : '.$lastname.'<br />'
								.$this->lang->def('_DIRECTORY_FIRSTNAME').' : '.$firstname,
							false,
							'deleteperson',
							"deletepersoncancel"
						)
			.Form::closeForm(), 'content');
	}

	function editPerson($userid = FALSE, $arr_idst_groups = FALSE, $form_url=FALSE) {
		require_once($GLOBALS['where_framework'] . "/lib/lib.form.php");
		require_once($GLOBALS['where_framework'] . "/lib/lib.publicadminmanager.php");
		if( $userid === FALSE ) {
			$userid	= importVar('userid',	FALSE, 	'' );
			$userLabel = importVar('userid',	FALSE, 	$this->lang->def( '_DIRECTORY_NEWPERSON' ) );
		} else {
			$userLabel = $userid;
		}
		$firstname = importVar('firstname',	FALSE, 	'' );;
		$lastname = importVar('lastname',	FALSE, 	'' );;
		$email = importVar('email',	FALSE, 	'' );;
		$pass = importVar('pass',	FALSE, 	'' );;
		$idst = '';
		// get all levels
		$arr_levels_id = $this->aclManager->getAdminLevels();
		$arr_levels_idst = array_values($arr_levels_id);
		$arr_levels_id = array_flip( $arr_levels_id );
		$arr_levels_translation = array();
		foreach( $arr_levels_id as $lev_idst => $lev_id ) {

			if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {

				if($lev_id == ADMIN_GROUP_USER)
					$arr_levels_translation[$lev_idst] = $this->lang->def('_DIRECTORY_'.$lev_id);
			} else {
				$arr_levels_translation[$lev_idst] = $this->lang->def('_DIRECTORY_'.$lev_id);
			}
		}
		// set default level
		$userlevel = array_search(ADMIN_GROUP_USER, $arr_levels_id);
		if( $userid != '' ) {
			$arrUser = $this->aclManager->getUser( FALSE, $userid );
			if( $arrUser !== FALSE ) {
				$idst = $arrUser[0];
				$firstname = $arrUser[2];
				$lastname = $arrUser[3];
				$email = $arrUser[5];
				// compute user level
				$arr_groups = $this->aclManager->getGroupsContainer($idst);

				$arr_user_level = array_intersect( $arr_levels_idst, $arr_groups );
				$arr_user_level = array_values($arr_user_level);
				if( count( $arr_user_level ) > 0 )
					$userlevel = $arr_user_level[0];
				else
					$userlevel = $arr_levels_idst[0];
			} else {
				// the user don't exist
				$firstname = $_POST['firstname'];
				$lastname = $_POST['lastname'];
				$email = $_POST['email'];
				// get arr_folders to know collect custom fields
				if( $arr_idst_groups === FALSE && isset( $_POST['arr_idst_groups'] ) ) {
					$arr_idst_groups = unserialize( urldecode( $_POST['arr_idst_groups'] ) );
				}
			}
		} else {
			if( $arr_idst_groups === FALSE && isset( $_POST['arr_idst_groups'] ) ) {
				$arr_idst_groups = unserialize( urldecode( $_POST['arr_idst_groups'] ) );
			}
		}
		/*
		$GLOBALS['page']->add( getTitleArea($this->lang->def( '_DIRECTORY_PEOPLEVIEWTITLE' )
								.': '.$userLabel, 'directory_people' ), 'content' );
*/
		$GLOBALS['page']->add( '<div class="std_block">', 'content');

		if ($form_url === FALSE)
			$form_url='index.php?modname=public_user_admin&amp;op=org_chart';

		$GLOBALS['page']->add( Form::getFormHeader( $this->lang->def( '_DIRECTORY_EDITPERSON' ) ), 'content' );
		$GLOBALS['page']->add( Form::openForm( 	'directoryeditperson',
												$form_url,
												FALSE,
												'post',
												'multipart/form-data'),
								'content');
		$GLOBALS['page']->add( Form::openElementSpace(), 'content' );

		$GLOBALS['page']->add( Form::getOpenFieldset( $this->lang->def( '_DIRECTORY_EDITPERSON' ).' - '.$userLabel ), 'content' );
		$GLOBALS['page']->add( Form::getTextfield( $this->lang->def( '_USERNAME' ), "userid", "userid", 50, $userid), 'content' );
		$GLOBALS['page']->add( Form::getTextfield( $this->lang->def( '_DIRECTORY_FIRSTNAME' ), "firstname", "firstname", 50, $firstname), 'content' );
		$GLOBALS['page']->add( Form::getTextfield( $this->lang->def( '_LASTNAME' ), "lastname", "lastname", 50, $lastname), 'content' );
		$GLOBALS['page']->add( Form::getTextfield( $this->lang->def( '_EMAIL' ), "email", "email", 50, $email), 'content' );
		$GLOBALS['page']->add( Form::getPassword( $this->lang->def( '_PASSWORD' ), "pass", "pass", 50), 'content' );

		$GLOBALS['page']->add( Form::getDropdown(
										$this->lang->def( '_DIRECTORY_USER_LEVEL' ),
										"userlevel",
										"userlevel",
										$arr_levels_translation,
										$userlevel),
								'content');
		$GLOBALS['page']->add( Form::getHidden('olduserlevel', 'olduserlevel', $userlevel), 'content' );
		$GLOBALS['page']->add( Form::getHidden('idst', 'idst', $idst), 'content' );
		$GLOBALS['page']->add( Form::getHidden('arr_idst_groups',
												'arr_idst_groups',
												urlencode(serialize($arr_idst_groups))),
								'content' );
		$GLOBALS['page']->add( Form::getCloseFieldset(), 'content' );
		/*
		$GLOBALS['page']->add( Form::closeElementSpace(), 'content' );
		$GLOBALS['page']->add( Form::openButtonSpace(), 'content' );
		$GLOBALS['page']->add( Form::getButton("editpersonsave","editpersonsave",$this->lang->def( '_SAVE' )), 'content' );
		$GLOBALS['page']->add( Form::getButton("editpersoncancel","editpersoncancel",$this->lang->def( '_CANCEL' )), 'content' );
		$GLOBALS['page']->add( Form::closeButtonSpace(), 'content' );
		$GLOBALS['page']->add( Form::openElementSpace(), 'content' );
		*/
		//-extra field-----------------------------------------------
		$GLOBALS['page']->add( Form::getOpenFieldset( $this->lang->def( '_ASSIGNED_EXTRAFIELD' ) ), 'content' );
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		$fields = new FieldList();
		if( $arr_idst_groups != FALSE ) {
			$acl =& $GLOBALS['current_user']->getACL();
			$arr_idst_all = $acl->getArrSTGroupsST(array_values($arr_idst_groups));
		} else
			$arr_idst_all = FALSE;
		$GLOBALS['page']->add( $fields->playFieldsForUser(
															( $userid !== false ? $idst : -1 ),
															$arr_idst_all
														), 'content' );
		$GLOBALS['page']->add( Form::getCloseFieldset(), 'content' );
		//-----------------------------------------------------------

		$GLOBALS['page']->add( Form::closeElementSpace(), 'content' );
		$GLOBALS['page']->add( Form::openButtonSpace(), 'content' );
		$GLOBALS['page']->add( Form::getButton("editpersonsave_2","editpersonsave",$this->lang->def( '_SAVE' )), 'content' );
		$GLOBALS['page']->add( Form::getButton("editpersoncancel_2","editpersoncancel",$this->lang->def( '_CANCEL' )), 'content' );
		$GLOBALS['page']->add( Form::closeButtonSpace(), 'content' );
		$GLOBALS['page']->add( Form::closeForm(), 'content' );
		$GLOBALS['page']->add( '</div>', 'content');
	}

	function loadGroupView() {
		$data = new GroupDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
		$rend = new TypeOne($GLOBALS['framework']['visuItem']);
		$lv = new GroupListView('', $data, $rend, 'groupdirectory');
		$lv->aclManager =& $this->aclManager;
		$lv->selector_mode = $this->selector_mode;
		$lv->select_all = $this->select_all;
		$lv->use_multi_sel=$this->use_multi_sel;
		$lv->sel_extend=$this->sel_extend;
		$lv->idModule = 'directory_selector';
		if($this->show_only_group_name === true) {
			$lv->showOnlyGroupName(true);
		}
		if(isset($this->group_filter['platform'])) {
			$data->addPlatformFilter($this->group_filter['platform']);
		} else {
			$data->addPlatformFilter(array($GLOBALS['platform']));
		}
		if(isset($this->group_filter['group'])) {
			$data->addGroupFilter($this->group_filter['group']);
		}
		if(isset($this->group_filter['path'])) {
			$data->addPathFilter($this->group_filter['path']);
		}
		if( $this->selector_mode === FALSE )
			$lv->setInsNew(TRUE);
		$lv->parsePositionData( $_POST );
		$lv->itemSelectedMulti = $this->selection;

		if( $lv->getOp() == 'newitem' ) {
			jumpTo( 'index.php?modname=directory&op=editgroup' );
		} elseif( $lv->getOp() == 'addtogroup' ) {
			jumpTo( 'index.php?modname=directory&op=addtogroup&groupid='.$lv->getIdSelectedItem() );
		} elseif( $lv->getOp() == 'assignfield' ) {
			jumpTo( 'index.php?modname=directory&op=assignfield&groupid='.$lv->getIdSelectedItem() );
		} elseif( $lv->getOp() == 'membersgroup' ) {
			jumpTo( 'index.php?modname=directory&op=membersgroup&groupid='.$lv->getIdSelectedItem() );


		} elseif( $lv->getOp() == 'import_groupuser' ) {
			jumpTo( 'index.php?modname=directory&op=import_groupuser' );
		} elseif( $lv->getOp() == 'import_groupuser_2' ) {
			jumpTo( 'index.php?modname=directory&op=import_groupuser_2' );
		} elseif( $lv->getOp() == 'import_groupuser_3' ) {
			jumpTo( 'index.php?modname=directory&op=import_groupuser_3' );


		} elseif( $lv->getOp() == 'editgroup' ) {
			jumpTo( 'index.php?modname=directory&op=editgroup&groupid='.$lv->getIdSelectedItem() );
		} elseif( $lv->getOp() == 'deletegroup' ) {
			jumpTo( 'index.php?modname=directory&op=deletegroup&groupid='.$lv->getIdSelectedItem() );
		} elseif( $lv->getOp() == 'waitinggroup' ) {
			jumpTo( 'index.php?modname=directory&op=waitinggroup&groupid='.$lv->getIdSelectedItem() );
		} else {
			if( !$this->selector_mode ) {
				$GLOBALS['page']->add( getTitleArea($this->lang->def( '_DIRECTORY_GROUPVIEWTITLE' ), 'directory_group' ), 'content' );
				$GLOBALS['page']->add( '<div class="std_block">', 'content' );
				$GLOBALS['page']->addEnd( '</div>', 'content' );
				$GLOBALS['page']->add( '<form id="dirctory_listgroup" action="index.php?modname=directory&amp;op=listgroup" method="post">', 'content' );
				$GLOBALS['page']->addEnd( '</form>', 'content' );
			}
			$GLOBALS['page']->add($lv->printOut(), 'content');
		}
	}

	function &getOrgDb() {
		$org_db = new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');
		return $org_db;
	}

	function &getTreeView_OrgView() {
		require_once($GLOBALS["where_framework"].'/modules/org_chart/tree.org_chart.php');
		$orgDb =& new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');
		$treeView =& new TreeView_OrgView($orgDb, 'organization_chart', $GLOBALS['title_organigram_chart']);
		$treeView->aclManager =& $this->aclManager;
		return $treeView;
	}

	function &getPeopleView() {
		$lv_data =& new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
		$rend =& new TypeOne($GLOBALS['framework']['visuUser']);
		$lv_view =& new PeopleListView('', $lv_data, $rend, 'usersmembersdirectory');
		$lv_view->aclManager =& $this->aclManager;
		return $lv_view;
	}

	function loadOrgChartView() {

		require_once($GLOBALS["where_framework"].'/modules/org_chart/tree.org_chart.php');
		$lang =& DoceboLanguage::createInstance('organization_chart', 'framework');
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();

		$repoDb = new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');

		$treeView = new TreeView_OrgView($repoDb, 'organization_chart', $GLOBALS['title_organigram_chart']);
		$treeView->setLanguage( $lang );
		$treeView->aclManager =& $this->aclManager;

		if( $userlevelid != ADMIN_GROUP_GODADMIN /*&& $userlevelid != ADMIN_GROUP_GODADMIN*/ ) {
			require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
			$adminManager = new PublicAdminManager();
			$treeView->setFilterNodes($adminManager->getAdminTree($GLOBALS['current_user']->getIdSt()));
		}

		$treeView->loadState();
		$treeView->parsePositionData($_POST, $_POST, $_POST);
		$treeView->selector_mode = $this->selector_mode;
		$treeView->simple_selector = $this->show_orgchart_simple_selector;

		$treeView->itemSelectedMulti = $this->selection;
		$treeView->itemSelectedMulti_alt = $this->selection_alt;
		$treeView->multi_choice = $this->multi_choice;
		$treeView->select_all = $this->select_all;

		$treeView->saveState();

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$GLOBALS['page']->add('<link href="'.getPathTemplate('framework').'/style/style_treeview.css" rel="stylesheet" type="text/css" />', 'page_head');
		$GLOBALS['page']->setWorkingZone('content');
		if( !$this->selector_mode ) {
			$GLOBALS['page']->add( getTitleArea($lang->def('_ORG_CHART'), 'org_chart' ) );
			$GLOBALS['page']->add( '<div class="std_block">' );
			$GLOBALS['page']->addEnd( '</div>' );
		}
		if( $treeView->op != '' ) {
			$processed = FALSE;
			switch( $treeView->op ) {
				case 'reedit_person':
					$processed = TRUE;
					$this->editPerson();
				break;
				case 'create_user':
					//$this->org_createUser($treeView->getSelectedFolderId());
					$processed = TRUE;
					jumpTo( 'index.php?modname=public_user_admin&op=org_createuser&treeid='.$treeView->getSelectedFolderId() );
				break;
				case 'addtotree':
					$processed = TRUE;
					jumpTo( 'index.php?modname=public_user_admin&op=addtotree&treeid='.$treeView->getSelectedFolderId() );
				break;
				case 'waiting_user':
					$processed = TRUE;
					jumpTo( 'index.php?modname=public_user_admin&op=org_waitinguser&treeid='.$treeView->getSelectedFolderId() );
				break;
			}
			if( !$this->selector_mode && !$processed ) {
				$GLOBALS['page']->add( Form::openForm('directory_org_chart', 'index.php?modname=public_user_admin&amp;op=org_chart', 'std_form', 'post', 'multipart/form-data') );
				$GLOBALS['page']->addEnd( Form::closeForm() );
			}
			switch( $treeView->op ) {
				case 'newfolder':
					$GLOBALS['page']->add($treeView->loadNewFolder());
				break;
				case 'deletefolder':
					$GLOBALS['page']->add($treeView->loadDeleteFolder());
				break;
				case 'renamefolder':
					$GLOBALS['page']->add($treeView->loadRenameFolder());
				break;
				case 'movefolder':
					$GLOBALS['page']->add($treeView->loadMoveFolder());
				break;
				case 'import_users':
					$GLOBALS['page']->add($treeView->loadImportUsers());
				break;
				case 'import_users2':
					$GLOBALS['page']->add($treeView->loadImportUsers2());
				break;
				case 'import_users3':
					$GLOBALS['page']->add($treeView->loadImportUsers3());
				break;
				case 'assign2_field':
					$GLOBALS['page']->add($treeView->loadAssignField2());
				break;
				case 'assign_field':
					$GLOBALS['page']->add($treeView->loadAssignField());
				break;
				case 'folder_field2':
					$GLOBALS['page']->add($treeView->loadFolderField2());
				break;
				case 'folder_field':
					$GLOBALS['page']->add($treeView->loadFolderField());
				break;
			}
		} else {
			if(!$this->selector_mode){
				$treeView->lv_data = new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
				$rend = new TypeOne($GLOBALS['framework']['visuUser']);
				$treeView->lv_view = new PeopleListView('', $treeView->lv_data, $rend, 'usersmembersdirectory');
				$treeView->lv_view->hide_suspend = FALSE;
				$treeView->lv_view->setLinkPagination( "index.php?modname=public_user_admin&amp;op=org_chart" );
				$treeView->lv_view->aclManager =& $this->aclManager;
				$treeView->lv_view->parsePositionData( $_POST );

				if( $treeView->lv_view->getOp() == 'newitem' ) {
					$this->editPerson();
				} elseif( $treeView->lv_view->getOp() == 'editperson' ) {
					$this->editPerson($treeView->lv_view->getIdSelectedItem());
				} elseif( $treeView->lv_view->getOp() == 'deleteperson' ) {
					$this->deletePerson($treeView->lv_view->getIdSelectedItem());
				} else {
					if( $treeView->lv_view->getOp() == 'removeperson') {
						$idmember = $treeView->lv_view->getIdSelectedItem();
						$idmember_idst = $this->aclManager->getUserST($idmember);
						$id_org = $treeView->getSelectedFolderId();
						$id_org_idst = $treeView->tdb->getGroupST($id_org);
						$id_org_desc_idst = $treeView->tdb->getGroupDescendantsST($id_org);

						// echo "\nmember idst: $member_idst, org_idst: $id_org_idst, org_desc_idst: $id_org_desc_idst\n";
						$this->aclManager->removeFromGroup($id_org_idst, $idmember_idst);
						$this->aclManager->removeFromGroup($id_org_desc_idst, $idmember_idst);
					}
					$GLOBALS['page']->add( Form::openForm('directory_org_chart', 'index.php?modname=public_user_admin&amp;op=org_chart') );
					$GLOBALS['page']->addEnd( Form::closeForm() );
					if( $GLOBALS['use_org_chart'] == '1' ) {
						$GLOBALS['page']->add($treeView->load());
						$GLOBALS['page']->add( $treeView->loadActions() );
					}
					if( $GLOBALS['use_org_chart'] == '1') {
						$id_org = $treeView->getSelectedFolderId();
						if( $id_org > 0 && $treeView->isFolderAccessible()) {
							if( $treeView->lv_view->flat_mode ) {
								$groupid = $treeView->tdb->getGroupDescendantsId($id_org);
							} else {
								$groupid = $treeView->tdb->getGroupId($id_org);
							}
						}
					} else {
						$id_org = 0;
					}
					if( $id_org > 0 && $treeView->isFolderAccessible() )
						$this->membersTree($groupid, $treeView);
					elseif($id_org == 0)
						$this->membersTree('', $treeView);
					if( $GLOBALS['use_org_chart'] != '1' ) {
						$GLOBALS['page']->add( $treeView->loadActions() );
					}
				}
			} else {
				$GLOBALS['page']->add($treeView->load());
			}
		}
	}

	/**
	 * Print list of user in org_chart pages
	 **/
	function membersTree( $groupid, &$treeView ) {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		if ($GLOBALS['framework']['register_deleted_user'] == 'on')
		{
			$lang =& DoceboLanguage::createInstance('profile', 'framework');
			$GLOBALS['page']->add('<br />'.'<a href="index.php?modname=public_user_admin&amp;op=view_deleted_user">'.$lang->def('_DELETED_USER_LIST').'</a>');
		}
		$data =& $treeView->lv_data;
		$lv =& $treeView->lv_view;
		$lv->show_flat_mode_flag = TRUE;
		if( $groupid === FALSE )
			return;
		if( $groupid != '' ) {
			$arrGroup = $this->aclManager->getGroup( FALSE, $groupid );
			if( $arrGroup !== FALSE ) {
				$idst = $arrGroup[0];
				$description = $arrGroup[2];
			}
		} else {
			$lv->show_flat_mode_flag = FALSE;
		}
		if( $lv->op == 'deleteperson' ) {
			$userid = $lv->getIdSelectedItem();
			$idst_user = $this->aclManager->getUserST( $userid );
			$id_org = $treeView->getSelectedFolderId();
			$idst_group = $treeView->tdb->getGroupST($id_org);
			$this->aclManager->removeFromGroup( $idst_group, $idst_user );
			$idst_group_desc = $treeView->tdb->getGroupDescendantsST($id_org);
			$this->aclManager->removeFromGroup( $idst_group_desc, $idst_user );
		} elseif( $lv->op == 'suspendperson' ) {
			$userid = $lv->getIdSelectedItem();
			$idst_user = $this->aclManager->getUserST( $userid );
			$this->aclManager->suspendUser( $idst_user );
			$GLOBALS['page']->add(getResultUi($this->lang->def('_SUSPENDED_USER') ));
		} elseif($lv->op == 'recoverperson' ) {
			$userid = $lv->getIdSelectedItem();
			$idst_user = $this->aclManager->getUserST( $userid );
			$this->aclManager->recoverUser( $idst_user );
			$GLOBALS['page']->add(getResultUi($this->lang->def('_RECOVERD_USER') ));
		}
		if( $groupid != '' )
			$data->setGroupFilter($idst, $lv->flat_mode );
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();
		if( $userlevelid != ADMIN_GROUP_GODADMIN ) {
			require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
			$adminManager = new PublicAdminManager();
			$data->intersectGroupFilter($adminManager->getAdminTree($GLOBALS['current_user']->getIdSt()));
		}
		$GLOBALS['page']->add($lv->printOut(), 'content');
		//$this->selected = $lv->printedItems;
	}

	/**
	 *
	 **/
	function addToTree($treeid) {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		if( $treeid === FALSE )
			return;

		require_once($GLOBALS["where_framework"].'/modules/org_chart/tree.org_chart.php');
		$repoDb = new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');

		if( isset($_POST['okselector']) ) {
			// aggiungere i selezionati al gruppo
			$idst = $repoDb->getGroupST($treeid);
			$idst_desc = $repoDb->getGroupDescendantsST($treeid);
			$arr_selection = $this->getSelection($_POST);
			$arr_unselected = $this->getUnselected();
			foreach( $arr_unselected as $idstMember ) {
				$this->aclManager->removeFromGroup($idst, $idstMember );
				$this->aclManager->removeFromGroup($idst_desc, $idstMember );
			}
			foreach( $arr_selection as $idstMember ) {
				$this->aclManager->addToGroup($idst, $idstMember );
				$this->aclManager->addToGroup($idst_desc, $idstMember );
			}
			jumpTo( 'index.php?modname=public_user_admin&op=org_chart' );
		} elseif( isset($_POST['cancelselector']) ) {
			jumpTo( 'index.php?modname=public_user_admin&op=org_chart' );
		} else {
			if( !isset($_GET['stayon']) ) {
				$idst = $repoDb->getGroupST($treeid);
				$this->resetSelection($this->aclManager->getGroupUMembers($idst));
			}
			$arr_translations = $repoDb->getFolderTranslations($treeid);
			$this->show_group_selector = FALSE;
			$this->show_orgchart_selector = FALSE;
			$this->hide_suspend = FALSE;
			$this->loadSelector('index.php?modname=public_user_admin&amp;op=addtotree&amp;treeid='.$treeid.'&amp;stayon=1',
							$this->lang->def( '_DIRECTORY_ADDTOTREE' ).' '.$arr_translations[getLanguage()],
							$this->lang->def( '_DIRECTORY_ADDTOTREEDESCR' ),
							TRUE);
		}
	}

	function org_createUser( $treeid = FALSE ) {
		checkPerm('createuser_org_chart', false, 'public_user_admin', true);
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$title_page = array(
			$this->lang->def('_DIRECTORY_PEOPLEVIEWTITLE'),
			$this->lang->def('_ORG_CHART_CREATE')
		);

		//$GLOBALS['page']->add('<div class="std_block">', 'content');

		if( $GLOBALS['use_org_chart'] == '1' || $GLOBALS['use_groups'] == '1' ) {
			if( isset($_POST['okselector']) ) {
				// go to user creation with folders selected
				require_once($GLOBALS["where_framework"].'/modules/org_chart/tree.org_chart.php');
				$repoDb = new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');

				$arr_selection = $this->getSelection($_POST);
				if( count($arr_selection) > 0 ) {
					
					$arr_selection = array_merge( $arr_selection, $repoDb->getDescendantsSTFromST($arr_selection));
					
					$arr_selection = array_merge( 	$arr_selection,
													$this->aclManager->getArrGroupST( array('/oc_0', '/ocd_0') ) );
					
					$this->editPerson( FALSE, $arr_selection );
					$editing = true;
				} else {
					
					$GLOBALS['page']->add(getResultUi($this->lang->def('_CHOOSE_AT_LEAST_ONE')), 'content');
				}
			} elseif( isset($_POST['cancelselector']) ) {
				jumpTo( 'index.php?modname=public_user_admin&op=org_chart' );
			} 
			if(!$editing) {
				if( !isset($_GET['stayon']) ) {
					if( $treeid === FALSE && isset($_GET['treeid']) )
						$treeid = (int)$_GET['treeid'];
					if( $treeid != 0 ) {
						require_once($GLOBALS["where_framework"].'/modules/org_chart/tree.org_chart.php');
						$repoDb = new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');
						$idst = $repoDb->getGroupST($treeid);
						$this->resetSelection(array($idst));
					} else {
						$this->resetSelection(array());
					}

				}
				$this->show_user_selector = FALSE;
				$this->show_group_selector = FALSE;
				if( $GLOBALS['use_org_chart'] == '1' ) {
					$this->show_orgchart_selector = TRUE;
					$this->show_orgchart_simple_selector = TRUE;
				} else {
					$this->show_orgchart_selector = FALSE;
				}
				
				if($GLOBALS['current_user']->getUserLevelId() === '/framework/level/admin')
				{
					require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
					
					$publicAdminManager = new PublicAdminManager();
					
					$this->setGroupFilter('group', $publicAdminManager->getAdminTree(getLogUserId()));
				}
				
				$this->loadSelector('index.php?modname=public_user_admin&amp;op=org_createuser&amp;stayon=1',
								$this->lang->def( '_DIRECTORY_TREECREATEUSER' ),
								$this->lang->def( '_DIRECTORY_TREECREATEUSERDESCR' ),
								TRUE);
			}
		} else {
			$this->editPerson( FALSE, array() );
		}

		//$GLOBALS['page']->add('</div>', 'content');
	}

	function org_waitingUser() {
		checkPerm('approve_waiting_user', false, 'public_user_admin', true);

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');

		if( isset($_POST['ok_waiting']) ) {

			$user_man = new UserManager();
			// Remove refused users
			$refused = array();
			$aopproved = array();
			if(isset($_POST['waiting_user_refuse'])) {

				while(list($idst) = each($_POST['waiting_user_refuse'])) {

					$this->aclManager->deleteTempUser( $idst , false, false, true );
				}
				$refused[] = $idst;
			}
			// Subscribed accepted users
			if(isset($_POST['waiting_user_accept'])) {

				$idst_usergroup 	= $this->aclManager->getGroup(false, ADMIN_GROUP_USER);
				$idst_usergroup 	= $idst_usergroup[ACL_INFO_IDST];

				$idst_oc 			= $this->aclManager->getGroup(false, '/oc_0');
				$idst_oc 			= $idst_oc[ACL_INFO_IDST];

				$idst_ocd 			= $this->aclManager->getGroup(false, '/ocd_0');
				$idst_ocd 			= $idst_ocd[ACL_INFO_IDST];

				$request = $this->aclManager->getTempUsers(false, true);

				while(list($idst) = each($_POST['waiting_user_accept'])) {

					if($this->aclManager->registerUser(addslashes($request[$idst]['userid']),
						addslashes($request[$idst]['firstname']),
						addslashes($request[$idst]['lastname']),
						$request[$idst]['pass'],
						addslashes($request[$idst]['email']),
						'',
						'',
						'',
						true,
						$idst )) {

						$approved[] = $idst;

						$this->aclManager->addToGroup($idst_usergroup, $idst);
						$this->aclManager->addToGroup($idst_oc, $idst);
						$this->aclManager->addToGroup($idst_ocd, $idst);

						if($request[$idst]['create_by_admin'] != 0) {

							$pref = new UserPreferences($request[$idst]['create_by_admin']);
							if($pref->getPreference('admin_rules.limit_user_insert') == 'on') {

								$max_insert = $pref->getPreference('admin_rules.max_user_insert');
								$pref->setPreference('admin_rules.max_user_insert', $max_insert -1 );
							}
						}
						$this->aclManager->deleteTempUser( $idst , false, false, false );
					}
				}
			}

			require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
			require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
			// send the alert
			/*
			if(!empty($refused)) {

				$array_subst = array('[url]' => $GLOBALS['framework']['url']);

				$msg_composer = new EventMessageComposer('admin_directory', 'framework');

				$msg_composer->setSubjectLangText('email', '_REFUSED_USER_SBJ', false);
				$msg_composer->setBodyLangText('email', '_REFUSED_USER_TEXT', $array_subst);

				$msg_composer->setSubjectLangText('sms', '_REFUSED_USER_SBJ_SMS', false);
				$msg_composer->setBodyLangText('sms', '_REFUSED_USER_TEXT_SMS', $array_subst);

				createNewAlert(	'UserApproved', 'directory', 'edit', '1', 'Users refused',
							$refused, $msg_composer );
			}*/
			if(!empty($approved)) {
				$pl_man =& PlatformManager::createInstance();
				$array_subst = array('[url]' => $GLOBALS[$pl_man->getHomePlatform()]['url']);

				$msg_composer2 = new EventMessageComposer('admin_directory', 'framework');

				$msg_composer2->setSubjectLangText('email', '_APPROVED_USER_SBJ', false);
				$msg_composer2->setBodyLangText('email', '_APPROVED_USER_TEXT', $array_subst);

				$msg_composer2->setSubjectLangText('sms', '_APPROVED_USER_SBJ_SMS', false);
				$msg_composer2->setBodyLangText('sms', '_APPROVED_USER_TEXT_SMS', $array_subst);

				createNewAlert(	'UserApproved', 'directory', 'edit', '1', 'Users approved',
							$approved, $msg_composer2, true );
			}

			jumpTo( 'index.php?modname=public_user_admin&op=org_chart' );
		} elseif( isset($_POST['cancel_waiting']) ) {

			jumpTo( 'index.php?modname=public_user_admin&op=org_chart' );
		} else {

			$tb = new Typeone(0,
				$this->lang->def('_WAITING_USER_CAPTION'),
				$this->lang->def('_WAITING_USER_SUMMARY'));

			$type_h = array('', '', '', 'image', 'image');
			$cont_h = array(
				$this->lang->def('_USERNAME'),
				$this->lang->def('_DIRECTORY_FULLNAME'),
				$this->lang->def('_DIRECTORY_ISCR_BY'),
				'<img src="'.getPathImage('fw').'directory/wuser_accept.gif" alt="'.$this->lang->def('_ACCEPT_USER').'" '
					.'title="'.$this->lang->def('_ACCEPT_USER_TITLE').'" />',
				'<img src="'.getPathImage('fw').'directory/wuser_refuse.gif" alt="'.$this->lang->def('_REFUSE_USER').'" '
					.'title="'.$this->lang->def('_REFUSE_USER_TITLE').'" />'
			);
			$tb->setColsStyle($type_h);
			$tb->addHead($cont_h);

			$temp_users = $this->aclManager->getTempUsers(false, true);

			if($temp_users !== false) {
				$idst_admins = array();
				while(list($idst, $info) = each($temp_users)) {

					if($info['create_by_admin'] != 0) {
						$idst_admins[] = $info['create_by_admin'];
					}
				}
				$admins = $this->aclManager->getUsers($idst_admins);

				reset($temp_users);
				while(list($idst, $info) = each($temp_users)) {

					if($info['create_by_admin'] != 0) {
						$creator = $admins[$info['create_by_admin']][ACL_INFO_LASTNAME].' '
									.$admins[$info['create_by_admin']][ACL_INFO_FIRSTNAME];
						if($creator == '') {
							$creator = $this->aclManager->relativeId($admins[$info['create_by_admin']][ACL_INFO_USERID]);
						}
					} else {
						$creator = $this->lang->def('_DIRECOTRY_SELFREGISTERED');
					}
					$more = ( isset($_GET['id_user']) && $_GET['id_user'] == $idst
						? '<a href="index.php?modname=public_user_admin&amp;op=org_waitinguser"><img src="'.getPathImage('fw').'standard/less.gif"></a> '
						: '<a href="index.php?modname=public_user_admin&amp;op=org_waitinguser&amp;id_user='.$idst.'"><img src="'.getPathImage('fw').'standard/more.gif"></a> ');

					$cont = array(
						$more.$this->aclManager->relativeId($info['userid']),
						$info['lastname'].' '.$info['firstname'],
						$creator,
						Form::getInputCheckbox('waiting_user_accept_'.$idst,
												'waiting_user_accept['.$idst.']',
												$idst, false, '' )
						.Form::getLabel('waiting_user_accept_'.$idst, $this->lang->def('_ACCEPT_USER'), 'access-only'),
						Form::getInputCheckbox('waiting_user_refuse_'.$idst,
												'waiting_user_refuse['.$idst.']',
												$idst, false, '' )
							.Form::getLabel('waiting_user_refuse_'.$idst, $this->lang->def('_REFUSE_USER'), 'access-only')
					);
					$tb->addBody($cont);

					if ( isset($_GET['id_user']) && $idst == $_GET['id_user']) {
						$field = new FieldList();
						$tb->addBodyExpanded($field->playFieldsForUser( $idst, false, true ), 'user_specific_info');
					}
				}
			}
			$GLOBALS['page']->add(
				getTitleArea($this->lang->def('_WAITING_USER'),'directory')
				.'<div class="std_block">'
				.Form::openForm('waiting_user', 'index.php?modname=public_user_admin&amp;op=org_waitinguser')
				.$tb->getTable()
				.Form::openButtonSpace()
				.Form::getButton('ok_waiting', 'ok_waiting', $this->lang->def('_SAVE'))
				.Form::getButton('cancel_waiting', 'cancel_waiting', $this->lang->def('_UNDO'))
				.Form::closeButtonSpace()
				.Form::closeForm()
				.'</div>', 'content' );
		}
	}


	// Function for permission managment
	function getAllToken($op) {

		return array(
			'view' => array( 'code' => 'view_org_chart',
										'name' => '_VIEW_ORG_CHART',
										'image' => 'standard/view.gif'),
			'add' => array( 	'code' => 'createuser_org_chart',
										'name' => '_CREATEUSER_ORG_CHART',
										'image' => 'standard/add.gif'),
			'mod' => array( 	'code' => 'edituser_org_chart',
										'name' => '_EDITUSER_ORG_CHART',
										'image' => 'standard/mod.gif'),
			'del' => array( 	'code' => 'deluser_org_chart',
										'name' => '_DELUSER_ORG_CHART',
										'image' => 'standard/rem.gif'),
			'moderate' => array( 	'code' => 'approve_waiting_user',
										'name' => '_MODERATE',
										'image' => 'org_chart/waiting_user.gif')
				);
	}

	function getUsersStats($stats_required = false, $arr_users = false) {

		$users = array();
		if($stats_required == false || empty($stats_required) || !is_array($stats_required)) {
			$stats_required = array('all', 'suspended', 'register_today', 'register_yesterday', 'register_7d',
				'now_online', 'inactive_30d', 'waiting', 'superadmin', 'admin');
		}
		$stats_required = array_flip($stats_required);

		if(isset($stats_required['all'])) {
			$data = new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
			$users['all'] 	= $data->getTotalRows();
		}
		if(isset($stats_required['suspended'])) {
			$data->addFieldFilter('valid', 0);
			$users['suspended'] = $data->getTotalRows();
			$users['suspended']--; // one is anonymous
		}
		if(isset($stats_required['register_today'])) {
			$data->resetFieldFilter();
			$data->addFieldFilter('register_date', date("Y-m-d").' 00:00:00', '>');
			$users['register_today'] = $data->getTotalRows();
		}
		if(isset($stats_required['register_yesterday'])) {
			$data->resetFieldFilter();
			$yesterday = date("Y-m-d", time() - 86400);
			$data->addFieldFilter('register_date', $yesterday.' 00:00:00', '>');
			$data->addFieldFilter('register_date', $yesterday.' 23:59:59', '<');
			$users['register_yesterday'] = $data->getTotalRows();
		}
		if(isset($stats_required['register_7d'])) {
			$data->resetFieldFilter();
			$sevendaysago = date("Y-m-d", time() - (7 * 86400));
			$data->addFieldFilter('register_date', $sevendaysago.' 00:00:00', '>');
			$users['register_7d'] = $data->getTotalRows();
		}
		if(isset($stats_required['now_online'])) {
			$data->resetFieldFilter();
			$data->addFieldFilter('lastenter', date("Y-m-d H:i:s", time() - REFRESH_LAST_ENTER), '>');
			$users['now_online'] = $data->getTotalRows();
			if (($arr_users !== false) && (is_array($arr_users)) && (count($arr_users) > 0)) {
				$data->setUserFilter($arr_users);
				$users['now_online_filtered'] = $data->getTotalRows();
			}
			else {
				$users['now_online_filtered'] =0;
			}
		}
		if(isset($stats_required['inactive_30d'])) {
			$data->resetFieldFilter();
			$data->addFieldFilter('lastenter', date("Y-m-d", time() - 30 * 86400).' 00:00:00', '<');
			$users['inactive_30d'] = $data->getTotalRows();
		}
		if(isset($stats_required['waiting'])) {
			$users['waiting'] 	= $this->aclManager->getTempUserNumber();
		}
		if(isset($stats_required['superadmin'])) {
			$userlevelid = $GLOBALS['current_user']->getUserLevelId();
			if ($userlevelid == ADMIN_GROUP_PUBLICADMIN)
				$idst_sadmin = $this->aclManager->getGroupST(ADMIN_GROUP_PUBLICADMIN);
			/*if ($userlevelid == ADMIN_GROUP_GODADMIN)
				$idst_sadmin = $this->aclManager->getGroupST(ADMIN_GROUP_GODADMIN);*/
			//$idst_sadmin = $this->aclManager->getGroupST(ADMIN_GROUP_GODADMIN);
			$users['superadmin'] 	= $this->aclManager->getGroupUMembersNumber($idst_sadmin);
		}
		if(isset($stats_required['admin'])) {
			$userlevelid = $GLOBALS['current_user']->getUserLevelId();
			if ($userlevelid == ADMIN_GROUP_PUBLICADMIN)
				$idst_sadmin = $this->aclManager->getGroupST(ADMIN_GROUP_PUBLICADMIN);
			/*if ($userlevelid == ADMIN_GROUP_GODADMIN)
				$idst_sadmin = $this->aclManager->getGroupST(ADMIN_GROUP_GODADMIN);*/
			//$idst_admin = $this->aclManager->getGroupST(ADMIN_GROUP_ADMIN);
			$users['admin'] 		= $this->aclManager->getGroupUMembersNumber($idst_sadmin);
		}
		return $users;
	}

}

?>
