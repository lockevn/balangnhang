<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2002 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if(!$GLOBALS['current_user']->isLoggedIn() || !isset($_SESSION['idCourse'])) die( "You can't access to oragnization");

require_once( $GLOBALS['where_lms'].'/modules/organization/orglib.php' );
require_once( $GLOBALS['where_lms'].'/lib/lib.stats.php' );

class StatOrg_TreeDb extends OrgDirDb {
	var $stat_filter_on_items = FALSE;
	var $filterGroup = FALSE;

	function _getOtherTables($tname = FALSE) {
			$prefix = $GLOBALS['prefix_lms'];
		if( $this->filterGroup !== FALSE ) {
			echo "\n\n<!-- filterGroup: ".$this->filterGroup."-->";
			if( $tname === FALSE )
				return   ' LEFT JOIN '.$prefix.'_organization_access'
						.' ON ( '.$prefix.'_organization.idOrg = '.$prefix.'_organization_access.idOrgAccess '
						.'   AND '.$prefix.'_organization_access.kind = \'group\' )';
						/*.' LEFT JOIN '.$prefix.'_coursegroupuser'
						.' ON ('.$prefix."_organization_access.kind = 'group'"
						.'     AND '.$prefix.'_organization_access.value = '.$prefix.'_coursegroupuser.idGroup )';*/
			else
				return   ' LEFT JOIN '.$prefix.'_organization_access'
						.' ON ( '.$tname.'.idOrg = '.$prefix.'_organization_access.idOrgAccess '
						.'   AND '.$prefix.'_organization_access.kind = \'group\' )';
						/*.' LEFT JOIN '.$prefix.'_coursegroupuser'
						.' ON ('.$prefix."_organization_access.kind = 'group'"
						.'     AND '.$prefix.'_organization_access.value = '.$prefix.'_coursegroupuser.idGroup )';*/
		} else
			return "";
	}
	function _getFilter($tname = FALSE) {
		$prefix = $GLOBALS['prefix_lms'];
		$result = "";
		if( $tname === FALSE ) {
			if( $this->stat_filter_on_items ) {
				$result .= " AND (idCourse = '".$this->idCourse."')"
						." AND (idObject <> 0)";
			} else {
				$result .= " AND (idCourse = '".$this->idCourse."')";
			}
		} else {
			if( $this->stat_filter_on_items ) {
				$result .= " AND (".$tname.".idCourse = '".$this->idCourse."')"
					 	." AND (".$tname.".idObject <> 0)";
			} else {
				$result .= " AND (".$tname.".idCourse = '".$this->idCourse."')";
			}
		}
		if( $this->filterGroup !== FALSE ) {
			$result .= " AND ( ".$prefix."_organization_access.value = '".(int)$this->filterGroup."'"
						."  OR ".$prefix."_organization_access.value IS NULL "
					  	.")";
			/*if( $tname === FALSE )
				$result .= ' AND ( '.$prefix.'_organization.idOrg = '.$prefix.'_organization_access.idOrgAccess '
						  .'   AND '.$prefix.'_organization_access.kind = \'group\' )';
			else
				$result .= ' AND ( '.$tname.'.idOrg = '.$prefix.'_organization_access.idOrgAccess '
						  .'   AND '.$prefix.'_organization_access.kind = \'group\' )';*/
		}
		return $result;
	}

	function _getJoinFilter($tname = FALSE) {
		return FALSE;
		if( $this->filterGroup !== FALSE ) {
			$prefix = $GLOBALS['prefix_lms'];
			return $tname.'.idOrg = '.$prefix.'_organization_access.idOrgAccess';
		} else
			return FALSE;
	}

}

define("ONEUSERVIEW", "1");
define("ITEMSVIEW", "2");

class StatOrg_TreeView extends Org_TreeView {
	var $kindOfView = ONEUSERVIEW;
	var $stat_idUser;

	function extendedParsing( $arrayState, $arrayExpand, $arrayCompress ) {
		$arrayState; $arrayExpand; $arrayCompress;
	}


	function printElement(&$stack, $level) {
		$out = TreeView::printElement($stack, $level);
		if( $this->kindOfView == ONEUSERVIEW )
		    $out .= $this->printElementOneUser($stack, $level);
		else
      		$out .= $this->printElementItem($stack, $level);
		return $out;
	}
	function printElementItem(&$stack, $level) {
		if( $level > 0 ) {
			$arrData = $stack[$level]['folder']->otherValues;
			if( is_array($arrData) && $arrData[3] != '' ) {
				return '<input type="submit" class="OrgPlay" value="" name="'
					.$this->_getOpPlayItemId().$stack[$level]['folder']->id .'"'
					.'title="'.$this->_getOpPlayTitle().'" />';
			}
		}
	}
	function printElementOneUser(&$stack, $level) {
		if( $level > 0 ) {
			$arrData = $stack[$level]['folder']->otherValues;
			if( is_array($arrData) && $arrData[3] != '' ) {
				require_once($GLOBALS['where_lms'].'/class.module/track.object.php' );
				$status = Track_Object::getStatusFromId(
							$stack[$level]['folder']->id,
							$this->stat_idUser );
				return printReport( $status, TRUE );
			} else {
				$this->tdb->stat_filter_on_items = TRUE;
				$totC = getSubStatStatusCount(	$this->stat_idUser,
												$this->tdb->idCourse,
												array( 'completed', 'passed'),
												$stack[$level]['folder'],
												$this->tdb);
				$totF = getSubStatStatusCount(	$this->stat_idUser,
												$this->tdb->idCourse,
												array( 'failed'),
												$stack[$level]['folder'],
												$this->tdb);
				$tot = count( $this->tdb->getDescendantsId($stack[$level]['folder']) );
				$this->tdb->stat_filter_on_items = TRUE;
				$out = '<div class="fright" >';
				$out .= renderProgress($totC,$totF,$tot,130);
				$out .= '</div>';
				return $out;
			}
		}
	}
}

/**
 * This function print a colored box based on given $status
 * If $returnToCaller id TRUE the function return the output string and
 *  don't put out it.
 * @param String $status the status of the box to be printed
 * @param BOOL $returnToCaller optional parameter; put it to TRUE to get
 *  avoid output and give it as return of function
 **/
function printReport( $status, $returnToCaller = FALSE ) {
	global $statusLabels;

	switch( $status ) {
		case "completed":
		case "passed":
			$div_class = "reportcomplete";
		break;
		case "failed":
			$div_class = "reportfailed";
		break;
		default:
			$div_class = "reportincomplete";
		break;
	}
	$strOut = '<div class="report_on_tree '.$div_class.'" >';
	if( isset($statusLabels[$status]) )
		$strOut .= $statusLabels[$status];
	else
		$strOut .= def($status, 'standard', 'framework');
	$strOut .= '</div>';
	if($returnToCaller)
    return $strOut;
  else
    echo $strOut;
}

function getSubStatStatusCount($stat_idUser, $stat_idCourse, $arrStauts, $folder, &$tdb) {
	$prefix = $GLOBALS['prefix_lms'];
	$arrItems = $tdb->getDescendantsId($folder);
	if( $arrItems === FALSE )
		return 0;
	$query = "SELECT count(ct.idreference)"
		." FROM ".$prefix."_commontrack ct, ".$prefix."_organization org"
		." WHERE (ct.idReference = org.idOrg)"
		."   AND (idUser = '".(int)$stat_idUser."')"
		."   AND (idCourse = '".(int)$stat_idCourse."')"
		."   AND (idOrg IN (".implode(",", $arrItems)."))"
		."   AND (status IN ('".implode("','",$arrStauts)."'))";
	if( ($rsItems = mysql_query( $query )) === FALSE ) {
		echo $query;
		errorCommunication( "Error on query to get user count based on status" );
		return;
	}

	list($tot) = mysql_fetch_row( $rsItems );
	mysql_free_result( $rsItems );
	return $tot;
}

function statuserfilter() {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$form = new Form();
	$aclManager =& $GLOBALS['current_user']->getACLManager();
	 
	$out->setWorkingZone('content');
	
	$GLOBALS['module_assigned_name'][$GLOBALS['modname']] = $lang->def('_STATFORUSER');
	
	$out->add(getTitleArea($lang->def('_STATFORUSER'), 'stats'));
	$out->add('<div class="std_block">');
	
	$group_filter = importVar('group_filter', false, -1);
	$status_filter = importVar('status_filter', true, -1);
	
	/*
	 * Print form for group and status selection
	 */
	$out->add( $form->openForm( "statuserfilter" , "index.php?modname=stats&amp;op=statuser" ) );
	
	// ------- Filter on group
	$arr_idst = $aclManager->getBasePathGroupST('/lms/course/'.(int)$_SESSION['idCourse'].'/group');
	$arr_result_groups = $aclManager->getGroups($arr_idst);
	
	$std_content = $aclManager->getContext();
	$aclManager->setContext('/lms/course/'.(int)$_SESSION['idCourse'].'/group');
	
	
	$arr_groups = array(-1 => $lang->def('_FILTERGROPSELECTONEOPTION'));
	foreach( $arr_result_groups as $idst_group => $info_group ) {
		if( !$info_group[ACL_INFO_GROUPHIDDEN] )
			$arr_groups[$idst_group] = $aclManager->relativeId($info_group[ACL_INFO_GROUPID]);
	}
	$aclManager->setContext($std_content);
	
	$out->add( $form->getDropdown( 	$lang->def('_FILTERGROUPSELECTLABEL'),
									'group_filter',
									'group_filter',
									$arr_groups , 
									$group_filter ) );

	// ------ Filter on status
	$arr_status = array( 	-1 => $lang->def('_FILTERSTATUSSELECTONEOPTION'),
							_CUS_SUBSCRIBED => $lang->def('_USER_STATUS_SUBS'),
							_CUS_BEGIN => $lang->def('_USER_STATUS_BEGIN'),
							_CUS_END => $lang->def('_USER_STATUS_END'), 
							_CUS_SUSPEND => $lang->def('_USER_STATUS_SUSPEND') );
	$out->add( $form->getDropdown( 	$lang->def('_FILTERSTATUSSELECTTITLE'),
									'status_filter',
									'status_filter',
									$arr_status , 
									$status_filter ) );
	if(isset($_POST['start_filter']) && $_POST['start_filter'] = 1)
		$out->add($form->getCheckBox($lang->def('_FILTEROBJECTFINISHED'), 'start_filter', 'start_filter', '1', true));
	else
		$out->add($form->getCheckBox($lang->def('_FILTEROBJECTFINISHED'), 'start_filter', 'start_filter', '1'));

	$out->add('<br/>');

	$out->add( $form->getButton('gofilter', 'gofilter', $lang->def('_FILTERSELECTED')) );
	$out->add( $form->closeForm() );

	/*
	 * Get all students of course that is contained in selected group
	 * For any student compute progress
	 */

	// step 2) load all students of course in selected group
	$lev = 3;
	$students = getSubscribedInfo((int)$_SESSION['idCourse'], FALSE, $lev, TRUE, ( $status_filter != -1 ? $status_filter : false ), false, true);
	
	// step 2) load all students of course
	
	$tabStat = new TypeOne(400, $lang->def('_STATS_USERS'), $lang->def('_STATS_USERS'));
	
	$content_h 	= array(
		$lang->def('_USERNAME'), 
		$lang->def('_STATS_FULLNAME'),
		'<img src="'.getPathImage().'stats/status.gif" alt="'.$lang->def('_STATUS').'" />',
		$lang->def('_STATS_LO'),
		$lang->def('_STATS_PROGRESS')
		);
	$type_h 	= array('', '', 'image', 'image', 'image', '', '');
	//	'<img src="'.getPathImage().'stats/notes.gif" alt="'.$lang->def('_STATS_NOTES').'" />',
	//	'<img src="'.getPathImage().'stats/notes.gif" alt="'.$lang->def('_STATS_NOTES').'" />',

	$tabStat->setColsStyle($type_h);
	$tabStat->addHead($content_h);

	$aclManager =& $GLOBALS['current_user']->getACLManager();
	$acl =& $GLOBALS['current_user']->getACL();
	
	// search memebers of the selected group
	$group_all_members = array();
	if($group_filter != '-1') $group_all_members = $aclManager->getGroupAllUser($group_filter);
	
	foreach( $students as $idst => $user_course_info ) {
		
		if($group_filter == '-1' || in_array($idst, $group_all_members) ) {
			$user_info = $aclManager->getUser( $idst, FALSE ); 
			
			if($user_info != false) {
				$totItems = getNumCourseItems( 		(int)$_SESSION['idCourse'],
													FALSE,
													$idst,
													FALSE );
				$totComplete = getStatStatusCount(	$idst,
													(int)$_SESSION['idCourse'],
													array( 'completed', 'passed' )
													);
				$totFailed = getStatStatusCount(	$idst,
													(int)$_SESSION['idCourse'],
													array( 'failed' )
													);
				// TODO: come si ottengono?
				$stat_status = $user_course_info['status'];
				$perm_pagella = "aa";
				
				if(isset($_POST['start_filter']) && $_POST['start_filter'] = 1)
				{
				     if($totComplete)
				     {
	                    // now print entry
	        			$content = array('<a href="index.php?modname=stats&amp;op=statoneuser&amp;idUser='.$idst.'" >'
	        							.$aclManager->relativeId($user_info[ACL_INFO_USERID]).'</a>',
	        						$user_info[ACL_INFO_LASTNAME].'&nbsp;'.$user_info[ACL_INFO_FIRSTNAME],
	        						'<a href="index.php?modname=stats&amp;op=modstatus&amp;idUser='.$idst.'">'
	        						.'<img src="'.getPathImage().'stats/'.$stat_status.'.gif" alt="'.$lang->def('_STATUS').'" /></a>');
	        			/*if($perm_pagella) {
	        				$content[] = '<a href="index.php?modname=stats&amp;op=modpagel&amp;iduser='.$idst.'">'
	        						.'<img src="'.getPathImage().'stats/notes.gif" alt="'.$lang->def('_STATS_NOTES').'" /></a>';
	        				$content[] = '<a href="index.php?modname=stats&amp;op=showsema&amp;iduser='.$idst.'">'
	        						.'<img src="'.getPathImage().'stats/notes2.gif" alt="'.$lang->def('_STATS_NOTES').'" /></a>';
	        			}*/
	        			$content[] = $totComplete.'/'.$totFailed.'/'.$totItems;
	        			$content[] = renderProgress($totComplete,$totFailed,$totItems);
	        			$tabStat->addBody($content);
	                 }
	            }
	            else
	            {
	                // now print entry
	    			$content = array('<a href="index.php?modname=stats&amp;op=statoneuser&amp;idUser='.$idst.'" >'
	    							.$aclManager->relativeId($user_info[ACL_INFO_USERID]).'</a>',
	    						$user_info[ACL_INFO_LASTNAME].'&nbsp;'.$user_info[ACL_INFO_FIRSTNAME],
	    						'<a href="index.php?modname=stats&amp;op=modstatus&amp;idUser='.$idst.'">'
	    						.'<img src="'.getPathImage().'stats/'.$stat_status.'.gif" alt="'.$lang->def('_STATUS').'" /></a>');
	    			/*if($perm_pagella) {
	    				$content[] = '<a href="index.php?modname=stats&amp;op=modpagel&amp;iduser='.$idst.'">'
	    						.'<img src="'.getPathImage().'stats/notes.gif" alt="'.$lang->def('_STATS_NOTES').'" /></a>';
	    				$content[] = '<a href="index.php?modname=stats&amp;op=showsema&amp;iduser='.$idst.'">'
	    						.'<img src="'.getPathImage().'stats/notes2.gif" alt="'.$lang->def('_STATS_NOTES').'" /></a>';
	    			}*/
	    			$content[] = $totComplete.'/'.$totFailed.'/'.$totItems;
	    			$content[] = renderProgress($totComplete,$totFailed,$totItems);
	    			$tabStat->addBody($content);
	            }
			}
		}
	}
	$out->add($tabStat->getTable());
	$out->add('</div>');
}

function statoneuser() {
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$aclManager =& $GLOBALS['current_user']->getACLManager();
	 
	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_STATFORUSER'), 'stats', false, true));
	$out->add('<div class="std_block">');
	
	$idst 		= (int)$_GET['idUser'];
	$user_info 	= $aclManager->getUser( $idst, FALSE ); 
	
	$orgDb = new StatOrg_TreeDb();
	$treeView = new StatOrg_TreeView($orgDb, $_SESSION['idCourse']);
	$treeView->stat_idUser = $idst;
	$treeView->parsePositionData($_POST, $_POST, $_POST);

	// print container div and form
	$out->add(getBackUi('index.php?modname=stats&amp;op=statuser', $lang->def('_BACK')));
	$out->add('<div class="title">'
		.$lang->def('_STATFORUSER').' '.$user_info[ACL_INFO_FIRSTNAME].' '.$user_info[ACL_INFO_LASTNAME]
		.'</div>');
	$out->add('<form name="orgshow" method="post"'
	.' action="index.php?modname=stats&amp;op=statoneuser&amp;idUser='.$idst.'"'
	.' >'."\n");

	$out->add($treeView->load());
	//if( funAccess('orgedit','MOD', TRUE, 'organization' ) ) $treeView->loadActions();

	$out->add('</form>');
	// print form for import action

	// display track if exists
	$item = $orgDb->getFolderById( $treeView->getSelectedFolderId() );
	$values = $item->otherValues;

	$param = $treeView->printState(FALSE);
	$arrBack_Url = array( 	'address' => 'index.php?modname=stats&op=statoneuser&idUser='.$treeView->stat_idUser,
							'end_address' => 'index.php?modname=stats&op=statoneuser&idUser='.$treeView->stat_idUser,
							'param' => $param
						);

	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	//find idTrack
	$idTrack = Track_Object::getIdTrackFromCommon( $treeView->getSelectedFolderId(), $treeView->stat_idUser );


	if($idTrack) {
		$lo = createLOTrack( $idTrack,
						$values[REPOFIELDOBJECTTYPE],
						$values[REPOFIELDIDRESOURCE],
						$values[ORGFIELDIDPARAM],
						$arrBack_Url);

		if($lo !== false) {
			$GLOBALS['wrong_way_to_pass_parameter'] = $values[REPOFIELDIDRESOURCE];
			$out->add($lo->loadReport( $treeView->stat_idUser ));
		}
	}
	$out->add('</div>');
}

function statcourse() {
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$aclManager =& $GLOBALS['current_user']->getACLManager();
	$form = new Form();

	if( isset( $_POST['group_filter'] ) ) {
		$group_filter = $_POST['group_filter'];
	} else {
		$group_filter = "";
	}

	$orgDb = new StatOrg_TreeDb();
	if( $group_filter != "" ) {
		$orgDb->filterGroup = $group_filter;
	}
	$treeView = new StatOrg_TreeView($orgDb, $_SESSION['idCourse']);
	$treeView->kindOfView = ITEMSVIEW;

	$treeView->parsePositionData($_POST, $_POST, $_POST);
	if( $treeView->op == 'playitem' )
    	jumpTo(" index.php?modname=stats&op=statitem&idItem=".$treeView->getItemToPlay());

	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_STATCOURSE'), 'stats'));
	$out->add('<div class="std_block">');
	$out->add( $form->openForm( 'orgshow', "index.php?modname=stats&amp;op=statcourse" ) );
	
	/*
	 * Print form for group selection
	 */
	// ------- Filter on group
	$arr_idst = $aclManager->getBasePathGroupST('/lms/course/'.(int)$_SESSION['idCourse'].'/');
	$arr_result_groups = $aclManager->getGroups($arr_idst);
	$arr_groups = array('' => $lang->def('_FILTERGROPSELECTONEOPTION'));
	
	$std_content = $aclManager->getContext();
	$aclManager->setContext('/lms/course/'.(int)$_SESSION['idCourse'].'/group');
	
	
	$arr_groups = array('' => $lang->def('_FILTERGROPSELECTONEOPTION'));
	foreach( $arr_result_groups as $idst_group => $info_group ) {
		if( !$info_group[ACL_INFO_GROUPHIDDEN] )
			$arr_groups[$idst_group] = $aclManager->relativeId($info_group[ACL_INFO_GROUPID]);
	}
	$aclManager->setContext($std_content);
	
	$out->add( $form->getDropdown( 	$lang->def('_FILTERGROUPSELECTLABEL'),
									'group_filter',
									'group_filter',
									$arr_groups , 
									$group_filter ) );

	$out->add( $form->getButton('gofilter', 'gofilter', $lang->def('_FILTERSELECTED')) );

	$out->add($treeView->load());
	//if( funAccess('orgedit','MOD', TRUE, 'organization' ) ) $treeView->loadActions();

	$out->add($form->closeForm());
	// print form for import action

	$out->add('</div>');

	$idFolder = $treeView->getSelectedFolderId();
	if( $idFolder != 0 ) {
		$item = $orgDb->getFolderById( $idFolder );
		$values = $item->otherValues;
		
		$param = $treeView->printState(FALSE);
		$arrBack_Url = array('address' => 'index.php?modname=stats&op=statcourse',
							 'end_address' => 'index.php?modname=stats&op=statcourse',
							 'param' => $param
						 );
		$lo = createLOTrack( NULL,
							$values[REPOFIELDOBJECTTYPE],
							$values[REPOFIELDIDRESOURCE],
							$values[ORGFIELDIDPARAM],
							$arrBack_Url);
	
		if($lo !== false) {
			$out->add($lo->loadObjectReport( ));
		} else {
			if( $GLOBALS['do_debug'] == 'on' ) 
				$out->add(	"<!-- createLOTrack fallita".
							"oggetto type: ".$values[REPOFIELDOBJECTTYPE]."<br/>".
							" resource id: ".$values[REPOFIELDIDRESOURCE]."<br/>".
							    "param id: ".$values[ORGFIELDIDPARAM]." -->" );
		}
	}
}

/**
 * Print statistic on one item
 *
 **/
function statitem() {
	require_once( $GLOBALS['where_lms'].'/class.module/track.object.php' );
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$form =  new Form();
	$aclManager =& $GLOBALS['current_user']->getACLManager();
	$acl =& $GLOBALS['current_user']->getACL();

	$idItem = (int)$_GET['idItem'];
	
	if( isset( $_POST['group_filter'] ) ) {
		$group_filter = $_POST['group_filter'];
	} else {
		$group_filter = "";
	}
	if( isset( $_POST['status_filter'] ) ) {
		$status_filter = (int)$_POST['status_filter'];
	} else {
		$status_filter = -1;
	}

	list($titleLO, $objectType) = mysql_fetch_row(mysql_query("SELECT title, objectType FROM "
																.$GLOBALS['prefix_lms']."_organization"
																." WHERE idOrg='".$idItem."'"));

	$lev = 3;
	$students = getSubscribedInfo((int)$_SESSION['idCourse'], FALSE, $lev, TRUE, ( $status_filter != -1 ? $status_filter : false ), false, true);
	
	// get idst of the access in item
	$query = "SELECT value FROM ".$GLOBALS['prefix_lms']."_organization_access"
			." WHERE idOrgAccess = '".$idItem."'";
	if( ($rs = mysql_query( $query )) === FALSE ) {
		errorCommunication( "Error on query to load item access" );
		return;
	}
	
	$arr_access = array();
	while( list($value) = mysql_fetch_row( $rs ) ) 
		$arr_access[] = $value;
	
	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_STATSITEM').$titleLO, 'stats'));
	$out->add('<div class="std_block">'
			.getBackUi('index.php?modname=stats&amp;op=statcourse', $lang->def('_BACK')));
	$out->add( $form->openForm( 'orgshow', 'index.php?modname=stats&amp;op=statitem&amp;idItem='.$idItem ) );
	if (isset($_POST['view_open_quest']))
	{
		$query_resource = "SELECT idResource" .
							" FROM ".$GLOBALS['prefix_lms']."_organization" .
							" WHERE idOrg = '".$idItem."'";
		
		list($id_poll) = mysql_fetch_row(mysql_query($query_resource));
		
		$query_quest = "SELECT id_quest, title_quest" .
						" FROM ".$GLOBALS['prefix_lms']."_pollquest" .
						" WHERE id_poll = '".$id_poll."'" .
						" AND type_quest = 'extended_text'";
		
		$result_quest = mysql_query($query_quest);
		
		$type_h = array('');
		$cont_h = array($lang->def('_ANSWER'));
		
		while (list($id_quest, $title_quest) = mysql_fetch_row($result_quest))
		{
			$tb = new TypeOne(400, $title_quest);
			$tb->setColsStyle($type_h);
			$tb->addHead($cont_h);
			
			$query_answer = "SELECT more_info" .
							" FROM ".$GLOBALS['prefix_lms']."_polltrack_answer" .
							" WHERE id_quest = '".$id_quest."'";
			
			$result_answer = mysql_query($query_answer);
			
			while (list($answer) = mysql_fetch_row($result_answer))
			{
				$cont = array();
				$cont[] = $answer;
				
				$tb->addBody($cont);
			}
			
			$out->add($tb->getTable().'<br/>');
		}
		
		$out->add(
			$form->openButtonSpace()
			.$form->getButton('back', 'back', $lang->def('_BACK'))
			.$form->closeButtonSpace());
	}
	else
	{
		// ------- Filter on group
		$arr_idst = $aclManager->getBasePathGroupST('/lms/course/'.(int)$_SESSION['idCourse'].'/');
		$arr_result_groups = $aclManager->getGroups($arr_idst);
		$arr_groups = array('' => $lang->def('_FILTERGROPSELECTONEOPTION'));
		
		$std_content = $aclManager->getContext();
		$aclManager->setContext('/lms/course/'.(int)$_SESSION['idCourse'].'/group');
		
		$arr_groups = array('' => $lang->def('_FILTERGROPSELECTONEOPTION'));
		foreach( $arr_result_groups as $idst_group => $info_group ) {
			if( !$info_group[ACL_INFO_GROUPHIDDEN] )
				$arr_groups[$idst_group] = $aclManager->relativeId($info_group[ACL_INFO_GROUPID]);
		}
		$aclManager->setContext($std_content);
		
		$out->add( $form->getDropdown( 	$lang->def('_FILTERGROUPSELECTLABEL'),
										'group_filter',
										'group_filter',
										$arr_groups , 
										$group_filter ) );
	
		// ------ Filter on status
		$arr_status = array( 	-1 => $lang->def('_FILTERSTATUSSELECTONEOPTION'),
								_CUS_SUBSCRIBED => $lang->def('_USER_STATUS_SUBS'),
								_CUS_BEGIN 		=> $lang->def('_USER_STATUS_BEGIN'),
								_CUS_END 		=> $lang->def('_END'),
								_CUS_SUSPEND 	=> $lang->def('_SUSPENDED') );
		$out->add( $form->getDropdown( 	$lang->def('_FILTERSTATUSSELECTTITLE'),
										'status_filter',
										'status_filter',
										$arr_status , 
										$status_filter ) );
		
		$out->add( $form->getButton('gofilter', 'gofilter', $lang->def('_FILTERSELECTED')) );
		
	
		//-----------------------------------------
		$tabStat = new TypeOne(400, $lang->def('_STATSITEM').$titleLO, $lang->def('_STATSITEM').$titleLO);
		
		$content_h 	= array(
			$lang->def('_USERNAME'), 
			$lang->def('_STATS_FULLNAME'),
			'<img src="'.getPathImage().'stats/status.gif" alt="'.$lang->def('_STATUS').'" />',
			'<img src="'.getPathImage().'stats/notes.gif" alt="'.$lang->def('_STATS_NOTES').'" />',
			$lang->def('_STATS_PROGRESS')
			);
		$type_h 	= array('', '', 'image', 'image', '');
	
		$tabStat->setColsStyle($type_h);
		$tabStat->addHead($content_h);
																	
		//-----------------------------------------
		foreach( $students as $idst => $user_course_info ) {
			$user_info = $aclManager->getUser( $idst, FALSE );
			if($user_info != false) {
				
				$arr_allst = $acl->getUserAllST( $user_info[ACL_INFO_USERID] );
				
				if( $group_filter === "" || in_array($group_filter,$arr_allst)  ) {
					if( count($arr_access) === 0 || count(array_intersect($arr_access,$arr_allst)) > 0 ) {
						$status = Track_Object::getStatusFromId(
										$idItem,
										$idst );
						// NOTE: How to get stat_status for users?
						$stat_status = $user_course_info['status'];
						$tabStat->addBody(
							array( '<a href="index.php?modname=stats&amp;op=statoneuseroneitem&amp;idUser='.$idst.'&amp;idItem='.$idItem.'" >'
										.$aclManager->relativeId($user_info[ACL_INFO_USERID]).'</a>',
									$user_info[ACL_INFO_LASTNAME].'&nbsp;'.$user_info[ACL_INFO_FIRSTNAME],
									'<a href="index.php?modname=stats&amp;op=modstatus&amp;idUser='.$idst.'&amp;idItem='.$idItem.'">'
									.'<img src="'.getPathImage().'stats/'.$stat_status.'.gif" alt="'.$lang->def('_STATUS').'" /></a>',
									'<img src="'.getPathImage().'stats/notes.gif" alt="'.$lang->def('_NOTES').'" />',
									printReport( $status, TRUE )
									)
							);
					}
				}
			}
		}
		$out->add($tabStat->getTable());
		
		$query = "SELECT idResource" .
				" FROM ".$GLOBALS['prefix_lms']."_organization" .
				" WHERE idOrg = '".$idItem."'";
		
		list($id_poll) = mysql_fetch_row(mysql_query($query));
		
		$query = "SELECT id_quest" .
				" FROM ".$GLOBALS['prefix_lms']."_pollquest" .
				" WHERE id_poll = '".$id_poll."'" .
				" AND type_quest = 'extended_text'";
		
		$result = mysql_query($query);
		
		if (mysql_num_rows($result) && $objectType == 'poll')
		{
			$out->add(
				$form->openButtonSpace()
				.'<br/>'
				.$form->getButton('view_open_quest', 'view_open_quest', $lang->def('_VIEW_OPEN_QUEST'))
				.$form->closeButtonSpace());
		}
	}
	
	$out->add($form->closeForm());
	$out->add('</div>'."\n");

}

/** 
 * Callback for make link in scorm renderer
 * @param $text string the text
 * @param $idItemDetail string the unique id of item
 * @return string the link to be renderd
 **/      
function cbMakeReportLink($text, $idItemDetail ) {
	if(isset($_GET['idItem'])) { 
		$idItem = (int)$_GET['idItem'];
		$backto = 'statoneuseroneitem';
	}
	if(isset($GLOBALS['wrong_way_to_pass_parameter'])) { 
		$idItem = (int)$GLOBALS['wrong_way_to_pass_parameter'];
		$backto = 'statoneuser';
	}
	$idst_user = (int)$_GET['idUser'];

	return '<a href="index.php?modname=stats&amp;op=statoneuseroneitemdetail&amp;idUser='.$idst_user.'&amp;idItem='.$idItem.'&amp;idItemDetail='.$idItemDetail.'&amp;backto='.$backto.'" >'
			.$text.'</a>';
}


/**
 * Print statistics for one user and one item
 *  $_GET['idUser']
 *  $_GET['idItem']
 **/
function statoneuseroneitem() {
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php' );
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$form =  new Form();
	$aclManager =& $GLOBALS['current_user']->getACLManager();
	$acl =& $GLOBALS['current_user']->getACL();

	$idItem = (int)$_GET['idItem'];
	$idst_user = (int)$_GET['idUser'];

	$out->add('	<link href="'.getPathTemplate().'/style/style_stats.css" rel="stylesheet" type="text/css" />','page_head');

	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_STATSUSERITEM'), 'stats'));
	$out->add('<div class="std_block">'
			.getBackUi('index.php?modname=stats&amp;op=statitem&amp;idItem='.$idItem, $lang->def('_BACK')));
	//$out->add( $form->openForm( 'orgshow', 'index.php?modname=stats&amp;op=statitem&amp;idItem='.$idItem ) );

	list($titleLO, $objectType) = mysql_fetch_row(mysql_query("SELECT title, objectType FROM "
												 				.$GLOBALS['prefix_lms']."_organization"
												 				." WHERE idOrg='".(int)$_GET['idItem']."'"));

	$user_info = $aclManager->getUser( $idst_user, FALSE );

	$out->add( '<div class="title">'
		.$lang->def('_STATFORUSER').' '.$user_info[ACL_INFO_FIRSTNAME].' '.$user_info[ACL_INFO_LASTNAME].' '
		.$lang->def('_STATSFORITEM').' <img src="'.getPathImage().'lobject/'.$objectType.'.gif"'
		.' alt="'.$objectType.'" />'.$titleLO
		. '</div>');

	$loTrack = createLOTrackShort( 	$idItem,
									$idst_user,
									'index.php?modname=stats&op=statitem&idItem='.$idItem);
	if( $loTrack === FALSE )
		$out->add( $lang->def('_STATNOTRACKFORUSER') );
	else
		$out->add( $loTrack->loadReport($idst_user) );
	$out->add( '</div>' );
}

/**
 * Print statistics for one user and one item
 *  $_GET['idUser']
 *  $_GET['idItem']
 **/
function statoneuseroneitemdetails() {
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php' );
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$form =  new Form();
	$aclManager =& $GLOBALS['current_user']->getACLManager();
	$acl =& $GLOBALS['current_user']->getACL();

	$backto = $_GET['backto'];
	$idItem = (int)$_GET['idItem'];
	$idst_user = (int)$_GET['idUser'];
	$idItemDetail = (int)$_GET['idItemDetail'];

	$out->add('	<link href="'.getPathTemplate().'/style/style_stats.css" rel="stylesheet" type="text/css" />','page_head');

	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_STATSUSERITEM'), 'stats'));
	$out->add('<div class="std_block">'
			.getBackUi('index.php?modname=stats&amp;op='.$backto.'&amp;idUser='.$idst_user.'&amp;idItem='.$idItem, $lang->def('_BACK')));
	//$out->add( $form->openForm( 'orgshow', 'index.php?modname=stats&amp;op=statitem&amp;idItem='.$idItem ) );

	list($titleLO, $objectType) = mysql_fetch_row(mysql_query("SELECT title, objectType FROM "
												 				.$GLOBALS['prefix_lms']."_organization"
												 				." WHERE idOrg='".(int)$_GET['idItem']."'"));

	$user_info = $aclManager->getUser( $idst_user, FALSE );

	$out->add( '<div class="title">'
		.$lang->def('_STATFORUSER').' '.$user_info[ACL_INFO_FIRSTNAME].' '.$user_info[ACL_INFO_LASTNAME].' '
		.$lang->def('_STATSFORITEM').' <img src="'.getPathImage().'lobject/'.$objectType.'.gif"'
		.' alt="'.$objectType.'" />'.$titleLO
		. '</div>');

	$loTrack = createLOTrackShort( 	$idItem,
									$idst_user,
									'index.php?modname=stats&op=statitem&idItem='.$idItem);
	if( $loTrack === FALSE )
		$out->add( $lang->def('_STATNOTRACKFORUSER') );
	else
		$out->add( $loTrack->loadReportDetail($idst_user,$idItemDetail) );
	$out->add( '</div>' );
}

function modstatus() {
	funAccess('statuser', 'OP');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('stats', 'lms');
	$out  =& $GLOBALS['page'];
	$form =  new Form();
	$aclManager =& $GLOBALS['current_user']->getACLManager();

	$idUser = (int)$_GET['idUser'];
	//$idItem = (int)$_GET['idItem'];

	$user_info = $aclManager->getUser( $idUser, FALSE );
	
	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_STATSUSERMODSTATUS').$user_info[ACL_INFO_FIRSTNAME].' '.$user_info[ACL_INFO_LASTNAME], 'stats'));
	$out->add('<div class="std_block">'
			.getBackUi('index.php?modname=stats&amp;op=statuser&amp;idUser='.$idUser, $lang->def('_BACK')));

	$query = "
	SELECT status
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$idUser."'
		AND idCourse = '".(int)$_SESSION['idCourse']."'";
	list($status) = mysql_fetch_row(mysql_query($query));

	$out->add( $form->openForm('modstatus', 'index.php?modname=stats&amp;op=upstatus') );
	
	$out->add( $form->getHidden( 'idUser', 'idUser', $idUser ) );
	
	$arr_status = array(	_CUS_SUBSCRIBED => $lang->def('_USER_STATUS_SUBS'),
							_CUS_BEGIN 		=> $lang->def('_USER_STATUS_BEGIN'),
							_CUS_END 		=> $lang->def('_END'),
							_CUS_SUSPEND 	=> $lang->def('_SUSPENDED') );
	$out->add( $form->getDropdown( 	$lang->def('_STATUS'),
									'status',
									'status',
									$arr_status , 
									$status ) );

	$out->add( $form->getButton('gofilter', 'gofilter', $lang->def('_SAVE')) );
	$out->add( $form->closeForm() );
	$out->add( '</div>' );
}

function upstatus() {
	funAccess('statuser', 'OP');

	if( !saveTrackStatusChange($_POST['idUser'], $_SESSION['idCourse'] , $_POST['status']) ) {
		errorCommunication(_ERRMODSTATUS);
		return;
	}
	jumpTo('index.php?modname=stats&op=statuser');
}

function exportTxt() {
	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	
	$id_quest = importVar('id_quest', true, 0);
	
	$query_quest = "SELECT id_quest, title_quest" .
					" FROM ".$GLOBALS['prefix_lms']."_pollquest" .
					" WHERE id_quest = '".$id_quest."'";
	
	$result_quest = mysql_query($query_quest);
	
	list($id_quest, $title_quest) = mysql_fetch_row($result_quest);
	
	$filename = str_replace('?', '', $title_quest).'.txt';
	
	$txt = $title_quest."\r\n"."\r\n";
	
	$query_answer = "SELECT more_info" .
					" FROM ".$GLOBALS['prefix_lms']."_polltrack_answer" .
					" WHERE id_quest = '".$id_quest."'";
	
	$result_answer = mysql_query($query_answer);
	
	$separator = "--------------------\r\n";
	while (list($answer) = mysql_fetch_row($result_answer))
		$txt .= $separator.$answer."\r\n";
	
	sendStrAsFile($txt, $filename);
}


addCss('style_stats');
switch( $GLOBALS['op'] ) {  // ---------------------------------------------------------------------
	case "statuser":
		statuserfilter();
	break;
	case "statoneuser":
		statoneuser();
	break;
	case "statcourse":
		statcourse();
	break;
	case "statitem":
		statitem();
	break;
	case "statoneuseroneitem":
		statoneuseroneitem();
	break;
	case "statoneuseroneitemdetail":
		statoneuseroneitemdetails();
	break;
	case "modstatus":
		modstatus();
	break;
	case "upstatus":
		upstatus();
	break;

	case "modpagel":
		modpagel();
	break;

	case "showsema":
		showsema();
	break;

	case "add_atvt": {
		add_edit_atvt();
	};break;

	case "edit_atvt": {
		add_edit_atvt("edit");
	};break;

	case "del_atvt": {
		confdel();
	};break;
	
	case "export_txt":
		exportTxt();
	break;
}

?>

