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

if($GLOBALS['current_user']->isAnonymous() || !isset($_SESSION['idCourse'])) die( "You can't access to oragnization");

require_once( dirname(__FILE__) . '/orglib.php' );

function organization( &$treeView ) {
	
	//getTitleArea('organizations');

	// contruct and initialize TreeView to manage organization
	/*$orgDb = new OrgDirDb();
	if( !checkPerm('lesson') ) {
		$treeView->tdb->setFilterVisibility( TRUE );
		$treeView->tdb->setFilterAccess( $GLOBALS['current_user']-> );
	}

	$treeView = new Org_TreeView($orgDb, $_SESSION['idCourse']);
	
	$treeView->parsePositionData($_POST, $_POST, $_POST);*/
	
	
	// manage items addition
	if( isset($_GET['replay']) ) {
		$treeView->op = 'playitem';
	} else if( isset($_GET['itemdone']) ) {
		$treeView->op = 'itemdone';
	} else if( isset($_POST['_orgrules_save']) || isset($_POST['_repoproperties_save']) ) {
		$treeView->tdb->modifyItem( $_POST, false, true );
		$treeView->op = '';
	} else if( isSet($_POST['_orgrules_cancel']) || isset($_POST['_repoproperties_cancel']) ) {
		$treeView->op = '';
	}
	//echo $treeView->op;
	switch( $treeView->op ) {
		case 'newfolder':
		case 'renamefolder':
		case 'movefolder':
		case 'deletefolder':
			organization_opfolder( $treeView, $treeView->op );
		break;
		case 'import':
			organization_import( $treeView );
		break;
		case 'org_properties':
		case 'org_opproperties':
			// organization_rules( $treeView, $treeView->opContextId );
			require_once(dirname(__FILE__).'/orgprop.php');
			organization_property( $treeView, $treeView->opContextId );
		break;
		case 'org_opaccess':
		case 'org_access':
			require_once(dirname(__FILE__).'/orgprop.php');
			organization_access( $treeView, $treeView->opContextId );
		break;
		case 'save': 
			$treeView->tdb->modifyItem( $_POST, false, true );
			organization_display( $treeView );
		break;
		/*case 'playitem':
			organization_play( $treeView, $treeView->_getOpPlayEnd() );
		break;*/
		case 'treeview_error':
			organization_showerror($treeView);
		break;
		case 'itemdone':
			organization_itemdone( $treeView, (int)$_GET[$treeView->_getOpPlayEnd()], (int)getLogUserId() );
			//refresh the tree 
		// no break, we would display after LO completition
		default:
			organization_display( $treeView );
		break;
	}
	
}

function organization_display( &$treeView ) {
	// print conainer div and form
	require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');
	TrackUser::setActionTrack(getLogUserId(), $_SESSION['idCourse'], 'organization', 'view');
	
	global $modname, $op;
	$GLOBALS['page']->setWorkingZone('content');
	$GLOBALS['page']->add( '<div class="std_block">' );
	$GLOBALS['page']->add( '<form id="orgshow" method="post"'
	.' action="index.php?modname='.$modname.'&amp;op='.$op.'"'
	.' >'."\n");
	
	if( funAccess('moditem','MOD', TRUE, 'organization' ) ) {
		$treeView->withActions = TRUE;
	} else {
		$tdb = $treeView->getTreeDb();
	}
	$GLOBALS['page']->add( $treeView->load() );
	if( funAccess('moditem','MOD', TRUE, 'organization' ) ) {
		$GLOBALS['page']->add( $treeView->loadActions() );
	}
	
	
	

	$GLOBALS['page']->add( '</form>' );
	// print form for import action
	$GLOBALS['page']->add( '</div>' );
	
	
	addYahooJs(array(), array());
	
	addCss('shadowbox');
	addJs($GLOBALS['where_framework_relative'].'/addons/shadowbox/', 'shadowbox-yui.js');
	addJs($GLOBALS['where_framework_relative'].'/addons/shadowbox/', 'shadowbox.js');
	
	$GLOBALS['page']->add( ''
	.'<script type="text/javascript">
		YAHOO.util.Event.onDOMReady(function() { 
			var options = { listenOverlay:false, overlayOpacity:"0.8", 
				loadingImage:"'.getPathImage('lms').'standard/loading.gif", overlayBgImage:"'.getPathImage('lms').'standard/overlay-85.png", 
				text: {close: "'.def('_CLOSE').'", cancel: "'.def('_UNDO').'", loading:"'.def('_LOADING').'" },
				onOpen: function (gallery) { window.onbeforeunload = function() { return "'.def('_CONFIRM_EXIT', 'organization', 'lms').'"; } }
		    }; 
			Shadowbox.init(options); 
			Shadowbox.close = function() { 
				window.onbeforeunload = null; 
				if(window.frames[\'shadowbox_content\'].playerConfig.backurl != undefined) {
					window.location = window.frames[\'shadowbox_content\'].playerConfig.backurl;
				} else window.location.reload();
			}
		});
	</script>' );
}

function organization_opfolder(&$treeView, $op) {
	global $modname;
	$GLOBALS['page']->add( '<div class="std_block">', 'content');
	$GLOBALS['page']->add( '<form id="orgnewfolder" method="post"'
	.' action="index.php?modname='.$modname.'&amp;op=organization"'
	.' >'."\n", 'content');
	
	switch( $op ) {
		case 'newfolder':
			$GLOBALS['page']->add( $treeView->loadNewFolder(), 'content');
		break;
		case 'renamefolder':
			$GLOBALS['page']->add( $treeView->loadRenameFolder(), 'content');
		break;
		case 'movefolder':
			$GLOBALS['page']->add( $treeView->loadMoveFolder(), 'content');
		break;
		case 'deletefolder':
			$GLOBALS['page']->add( $treeView->loadDeleteFolder(), 'content');
		break;
	}
	
	$GLOBALS['page']->add( '</form>', 'content');
	$GLOBALS['page']->add( '</div>', 'content');
}

function organization_import(&$treeView) {
	$lang =& DoceboLanguage::createInstance('organization', 'lms');
	global $modname, $op;
	require_once( $GLOBALS['where_lms'].'/lib/lib.homerepo.php' );
	
	// ----------------------------------
	$GLOBALS['page']->add( '<div class="std_block">' );
	$GLOBALS['page']->add( '<form id="orgimport" method="post"'
	.' action="index.php?modname='.$modname.'&amp;op=import" >'."\n" );
		// call pubrepo visualization to select items to import
	$GLOBALS['page']->add( $treeView->printState() );
	$treeViewPR = manHomerepo(FALSE, TRUE, NULL, TRUE );
	
	$GLOBALS['page']->add( '</form>' );
	
	// ----------------------------------
	// then use an other form to submit back to organization op whit id of
	// selected items
	$GLOBALS['page']->add( '<form id="orgimport" method="post"'
	.' action="index.php?modname='.$modname.'&amp;op=organization&amp;import=1" >'."\n");
		
	$GLOBALS['page']->add( $treeView->printState() );
	$listView = $treeViewPR->getListView();
	$arrSelected = $listView->getIdSelectedItem(); 
	$GLOBALS['page']->add( '<input type="hidden" value="'
		.addslashes(serialize($arrSelected))
		.'" name="idSelectedObjects">');
	$GLOBALS['page']->add( '<input type="submit" value="'.$lang->def( '_IMPORT' ).'" name="import">' );
	
	$GLOBALS['page']->add( '</form>' );
	$GLOBALS['page']->add( '</div>' );
}

function organization_play( &$treeView, $idItem ) {
	global $modname, $op;
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php');
	$tdb = $treeView->getTreeDb();
	$item = $tdb->getFolderById( $idItem );
	$values = $item->otherValues;
	$objectType = $values[REPOFIELDOBJECTTYPE];
	$idResource = $values[REPOFIELDIDRESOURCE];
	$idParams = $values[ORGFIELDIDPARAM];
	
	$param = $treeView->printState(FALSE);
	$back_url = 'index.php?modname='.$modname.'&op=organization&itemdone='.$idItem;
						
	$lo = createLO(	$objectType, 
					$idResource );
					
	$lo->play($idResource, $idParams, $back_url);
}

function organization_itemdone( &$treeView, $idItem, $idUser ) {
	
	$lang =& DoceboLanguage::createInstance('organization', 'lms');
	
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.stats.php');

	$tdb = $treeView->getTreeDb();
	$item = $tdb->getFolderById( $idItem );
	$values = $item->otherValues;
	$objectType = $values[REPOFIELDOBJECTTYPE];
	$idResource = $values[REPOFIELDIDRESOURCE];
	$idParams = $values[ORGFIELDIDPARAM];
	$isTerminator = $values[ORGFIELDISTERMINATOR];
	$idCourse = $tdb->idCourse;
	
	if( $isTerminator ) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		$idTrack = Track_Object::getIdTrackFromCommon( $idItem, $idUser );
		$track = createLOTrack( $idTrack, $objectType, $idResource, $idParams, "" );
		if( $track->getStatus() == 'completed' || $track->getStatus() == 'passed' ) {
			if( !saveTrackStatusChange((int)$idUser, (int)$idCourse , _CUS_END) ) {
				errorCommunication($lang->def( '_ERRMODSTATUS' ));
				return;
			}
		}
	}
}

function import() {
	
	$orgDb = new OrgDirDb();
	$treeView = new Org_TreeView($orgDb, $_SESSION['idCourse']);
	$treeView->parsePositionData($_POST, $_POST, $_POST);
	
	organization_import($treeView);
}

function edit() {
	$orgDb = new OrgDirDb();
	$treeView = new Org_TreeView($orgDb, $_SESSION['idCourse']);
	$treeView->parsePositionData($_POST, $_POST, $_POST);
	
	organization_properties($treeView);
}

function organization_showerror( &$treeView ) {
	$lang =& DoceboLanguage::createInstance('organization', 'lms');
	global $modname, $op;
	$GLOBALS['page']->add( '<form id="orgshow" method="post"'
	.' action="index.php?modname='.$modname.'&amp;op=organization"'
	.' >'."\n" );
	$GLOBALS['page']->add( '<div class="std_block">' );
	if( $treeView->error == TVERR_MOVEONDESCENDANT )
		$GLOBALS['page']->add( $lang->def( '_ERROR_TVERR_MOVEONDESCENDANT' ) );
	$GLOBALS['page']->add( ' <img src="'.$treeView->_getCancelImage().'" alt="'.$treeView->_getCancelAlt().'" />'
		.'<input type="submit" class="LVAction" value="'.$treeView->_getCancelLabel().'"'
		.' name="'.$treeView->_getCancelId().'" id="'.$treeView->_getCancelId().'" />' );
	$GLOBALS['page']->add( '</div>' );
	$GLOBALS['page']->add( '</form>' );
}

/*switch( $op ) {
	case "organization":
	case "display":
		organization();
	break;
	case "import":
		import();
	break;
}*/

?>