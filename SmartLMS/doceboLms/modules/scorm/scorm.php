<?php
/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri (esandri@tiscali.it)  			 */
/*																		 */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @module scorm.php
 * Impor module for scorm content packages
 * @version $Id: scorm.php 1002 2007-03-24 11:55:51Z fabio $
 * @copyright 2004 
 * @author Emanuele Sandri
 **/

define( "STRPOSTCONTENT", '_content');
 
function additem($object_item) {
	checkPerm( 'view', FALSE, 'storage' );
	
	$lang =& DoceboLanguage::createInstance('scorm', 'lms');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form = new Form();
	
	//area title
	$GLOBALS['page']->add( getTitleArea(
							$lang->getLangText('_SCORMIMGSECTION'),
							'scorm',
							$lang->getLangText('_SCORMSECTIONNAME'))
			);
			
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( ereg_replace('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=insitem", 
								false, 
								false, 
								'multipart/form-data')
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->getFilefield( $lang->getLangText('_CONTENTPACKAGE'), "attach", "attach" ) );

	$GLOBALS['page']->add( $form->getCheckbox( 	$lang->getLangText('_SCORMIMPORTRESOURCES'), 
												"lesson_resources", 
												"lesson_resources", 
												"import" ) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												$lang->getLangText('_SCORMLOAD') ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
}
 
function insitem() {
	checkPerm( 'view', FALSE, 'storage' );
	
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_framework'].'/addons/pclzip.lib.php');
	require_once(dirname(__FILE__).'/RendererDb.php');
	require_once(dirname(__FILE__).'/CPManager.php');
	
	$back_url = urldecode($_POST['back_url']);
	
	// there is a file?
	if($_FILES['attach']['name'] == '') {
		$_SESSION['last_error'] = _FILEUNSPECIFIED;
		jumpTo( ''.$back_url.'&create_result=0' );
	}
	$path = str_replace ( '\\', '/', '/doceboLms/'.$GLOBALS['lms']['pathscorm']); 
	$savefile = getLogUserId().'_'.rand(0,100).'_'.time().'_'.$_FILES['attach']['name'];
	if(!file_exists ($GLOBALS['where_files_relative'].$path.$savefile)) {
		sl_open_fileoperations();
		if(!sl_upload($_FILES['attach']['tmp_name'], $path.$savefile)) {
		//if( !move_uploaded_file($_FILES['attach']['tmp_name'], $GLOBALS['where_files_relative'].$path.$savefile ) ) {
			sl_close_fileoperations();				
			$_SESSION['last_error'] = _ERRORUPLOAD;
			jumpTo( ''.$back_url.'&create_result=0' );
		}
	} else {
		sl_close_fileoperations();
		$_SESSION['last_error'] = _ERRORUPLOAD;
		jumpTo( ''.$back_url.'&create_result=0' );
	}

	// compute filepath
	$filepath = $path.$savefile.STRPOSTCONTENT;
	// extract zip file
	$zip = new PclZip($path.$savefile);
	
	// check disk quota --------------------------------------------------
	$zip_content = $zip->listContent();
	$zip_extracted_size = 0;
	while(list(, $file_info) = each($zip_content)) {
		
		$zip_extracted_size += $file_info['size']; 
	}
	
	$quota = $GLOBALS['course_descriptor']->getQuotaLimit();
	$used = $GLOBALS['course_descriptor']->getUsedSpace();
	
	if(fileExceedQuota(false, $quota, $used, $zip_extracted_size)) {
		
		sl_unlink($path.$savefile);
		$_SESSION['last_error'] = def('_QUOTA_EXCEDED');
		jumpTo( ''.$back_url.'&create_result=0' );
	}
	$GLOBALS['course_descriptor']->addFileToUsedSpace(false, $zip_extracted_size);
	// extract zip ------------------------------------------------------
	
	$zip->extract(PCLZIP_OPT_PATH, $filepath );
	if( $zip->errorCode() != PCLZIP_ERR_NO_ERROR && $zip->errorCode() != 1 ) {	
		sl_unlink($path.$savefile);
		$_SESSION['last_error'] = _ERRORUPLOAD;
		sl_close_fileoperations();
		jumpTo( ''.$back_url.'&create_result=0' );
	}

	/* remove zip file */
	sl_unlink($path.$savefile);
	sl_close_fileoperations();

	
	$cpm = new CPManager();
	// try to open content package
	if( !$cpm->Open( $GLOBALS['where_files_relative'].$filepath ) ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		jumpTo( ''.$back_url.'&create_result=0' );
	}
	// and parse the manifest
	if( !$cpm->ParseManifest() ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		jumpTo( ''.$back_url.'&create_result=0' );
	}

	// create entry in content package table
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package"
			." (idpackage,idProg,path,defaultOrg,idUser,scormVersion) VALUES"
			." ('".addslashes($cpm->identifier)
			."','0','".$savefile.STRPOSTCONTENT
			."','".addslashes($cpm->defaultOrg)
			."','".(int)getLogUserId()
			."','".$cpm->scorm_version
			."')";
	if( !($result = mysql_query($query)) ) {
		$_SESSION['last_error'] = _ERRORDB;
		jumpTo( ''.$back_url.'&create_result=0' );
	}
	
	$idscorm_package = mysql_insert_id();
	
	// create the n entries in resources table
	for( $i = 0; $i < $cpm->GetResourceNumber(); $i++ ) {
		$info = $cpm->GetResourceInfo( $cpm->GetResourceIdentifier($i) );
		$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources (idsco,idscorm_package,scormtype,href)"
				." VALUES ('".addslashes($info['identifier'])."','"
				.(int)$idscorm_package."','"
				.$info['scormtype']."','"
				.addslashes($info['href']) ."')";

		$result = mysql_query( $query );

		if(!$result){
			$_SESSION['last_error'] = _ERRORDB;
			jumpTo( ''.$back_url.'&create_result=0' );
		} else if(mysql_affected_rows() == 0)  {
			$_SESSION['last_error'] = _ERRORDB;
			jumpTo( ''.$back_url.'&create_result=0' );
		}
	}

	$rdb = new RendererDb($GLOBALS['dbConn'], $GLOBALS['prefix_lms'], $idscorm_package);
	$orgElems = $cpm->orgElems;
	// save all organizations
	for( $iOrg = 0; $iOrg < $orgElems->getLength(); $iOrg++ ) {
		$org = $orgElems->item($iOrg);
		$cpm->RenderOrganization( $org->getAttribute('identifier'), $rdb );
	}
	
	if( $_POST['lesson_resources'] == 'import' || $cpm->defaultOrg == '-resource-' ) {
		// save flat organization with resources
		$cpm->RenderOrganization( '-resource-', $rdb );
	}
	
	$so = new Scorm_Organization( $cpm->defaultOrg, $idscorm_package, $GLOBALS['dbConn'] );
	if( $so->err_code > 0 ) {
		$_SESSION['last_error'] = 'Error: '. $so->getErrorText() . ' [' . $so->getErrorCode() .']';
		jumpTo( ''.$back_url.'&create_result=0' );
	} else {
		//jumpTo( ''.$back_url.'&id_lo='.$so->idscorm_organization.'&create_result=1' );
		jumpTo( ''.$back_url.'&id_lo='.$idscorm_package.'&create_result=2' );
	}
}
 
function moditem($object_item) {
	checkPerm( 'view', FALSE, 'storage' );
	
	$lang =& DoceboLanguage::createInstance('scorm', 'lms');
	
	//area title
	$GLOBALS['page']->add( getTitleArea(
							$lang->getLangText('_SCORMIMGSECTION'),
							'scorm',
							$lang->getLangText('_SCORMSECTIONNAME'))
			);
			
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( ereg_replace('&', '&amp;', $object_item->back_url).'&amp;edit_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);

	$GLOBALS['page']->add( $lang->def('_NOTHINGTOMODIFY'));
	$GLOBALS['page']->add( '</div>' );;

}

function play($aidResource, $aidReference, $aback_url, $aautoplay, $aplayertemplate ) {
	//die( $aidResource );
	$GLOBALS['idReference'] = $aidReference;
	$GLOBALS['idResource'] = $aidResource;
	$GLOBALS['back_url'] = $aback_url;
	$GLOBALS['autoplay'] = $aautoplay;
	$GLOBALS['playertemplate'] = $aplayertemplate;
	require( dirname(__FILE__) . '/scorm_frameset.php' );
}

function deleteitem( ) {
	$idscorm_package = (int)$_GET['idscorm_package'];
	$query = "SELECT idpackage, path" 
			." FROM ".$GLOBALS['prefix_lms']."_scorm_package"
			." WHERE idscorm_package = '".$idscorm_package."'";

	list( $idpackage, $path ) = mysql_fetch_row( mysql_query( $query ) );
		$path = preg_replace( '/^.+_.+_.+_(.+)_content$/', '\\1', $path ); 

	echo '<form id="scormdeleteform" method="POST"'
		.' action="index.php?modname=scorm&amp;op=dodelete&amp;idscorm_package='.$idscorm_package.'">';
	echo '<div class="stdBlock">';
	echo _DELETECONFIRM.' '.$idpackage.' ['.$path.']';
	echo '<br /><br /><img src="'.getPathImage().'standard/rem.gif" title="'._SCORMREMOVE.'" alt="'._SCORMREMOVE.'" />'
		.'<input type="submit" class="button" value="'._SCORMREMOVE.'"'
		.' name="deletescormcp" id="deletescormcp" />';
	echo ' <img src="'.getPathImage().'standard/undo.gif" alt="'._CANCEL.'" title="'._CANCEL.'" />'
		.'<input type="submit" class="button" value="'._CANCEL.'"'
		.' name="cancelscorm" id="cancelscorm" />';			
	echo '</div>';
	echo '</form>';
}

/*function dodelete( ) {
	if( isset($_POST['deletescormcp']) ) {
		_scorm_deleteitem( $_GET['idscorm_package'] );
	}
	jumpTo( " index.php?modname=scorm&op=display" );
}*/

function _scorm_deleteitem( $idscorm_package, $idscorm_organization, $erasetrackcontent = FALSE ) {
	
	/* remove items: based on organizations */
	//$rs = mysql_query("SELECT idscorm_organization FROM ".$prefix."_scorm_organizations WHERE idscorm_package=".$idscorm_package);			
	//while(list($idscorm_organization) = mysql_fetch_row($rs)) {
	if( $erasetrackcontent ) { // selected tracking remove
		$rsItems = mysql_query( "SELECT idscorm_item FROM ".$GLOBALS['prefix_lms']."_scorm_items WHERE idscorm_organization=".$idscorm_organization );
		while(list($idscorm_item) = mysql_fetch_row($rsItems)) {
			mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_tracking WHERE idscorm_resource=".$idscorm_item);
		}
	}
	mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_items WHERE idscorm_organization=".$idscorm_organization);

	//}
	
	/* remove organizations */
	mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_organizations WHERE idscorm_organization=".$idscorm_organization);

	// detect if there are other organization in package
	$rs = mysql_query("SELECT idscorm_organization FROM ".$GLOBALS['prefix_lms']."_scorm_organizations WHERE idscorm_package=".$idscorm_package);

	if( mysql_num_rows( $rs ) == 0 ) {
		$rs = mysql_query("SELECT path FROM ".$GLOBALS['prefix_lms']."_scorm_package WHERE idscorm_package='".(int)$idscorm_package."'")
			or die(mysql_error());
	
		list($path) = mysql_fetch_row($rs);
		$scopath = str_replace ( '\\', '/', $GLOBALS['where_files_relative'].'/doceboLms/'.$GLOBALS['lms']['pathscorm']); 
		/* remove all zip directory */
		if(file_exists($scopath.$path)) {
			
			/* if is the only occurrence of path in db delete files */
			$rs = mysql_query(	"SELECT idscorm_package FROM ".$GLOBALS['prefix_lms']."_scorm_package"
								." WHERE path = '".$path."'");
			if( mysql_num_rows( $rs ) == 1 ) {
				
				$size = getDirSize($scopath.$path);
			
				require_once( dirname(__FILE__). '/scorm_utils.php'); // for del tree
				delDirTree($scopath.$path);
				
				$GLOBALS['course_descriptor']->subFileToUsedSpace(false, $size);
			}
		}
	
		/* remove resources */
		mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_resources WHERE idscorm_package=".$idscorm_package);
	
		
		/* remove packages */
		mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_package WHERE idscorm_package=".$idscorm_package);
	}
	
}

function _scorm_copyitem( $idscorm_package, $idscorm_organization ) {
	funAccess( 'additem','NEW', false, 'scorm' );
	
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once(dirname(__FILE__) .'/RendererDb.php');
	require_once(dirname(__FILE__) .'/CPManager.php');
	
	if( ($rs = mysql_query("SELECT path FROM ".$GLOBALS['prefix_lms']."_scorm_package "
							."WHERE idscorm_package='"
							.(int)$idscorm_package."'")) === FALSE ) {
		$_SESSION['last_error'] = _ERRORDB.': '.mysql_error();
		return FALSE;
	}

	list($path) = mysql_fetch_row($rs);
	$scopath = str_replace ( '\\', '/', $GLOBALS['where_files_relative'].'/doceboLms/'.$GLOBALS['lms']['pathscorm']); 
	
	/* copy all zip directory */
	/* remove copy - use same files 
	$fname = explode ( '_', $path, 4);
	$savefile = $_SESSION['sesUser'].'_'.rand(0,100).'_'.time().'_'.$fname[3];
	$filepath = $pathscorm.$savefile;

	if(file_exists($path)) {
		if( !sl_copyr($path, $filepath ) ) {
			$_SESSION['last_error'] = _ERRORCOPYFILE;
			return FALSE;
		}
	}
	*/
	/* copy package record */
	$rs_package = mysql_query(	"SELECT idpackage,idProg,'".$path."',defaultOrg,idUser "
								." FROM ".$GLOBALS['prefix_lms']."_scorm_package "
								." WHERE idscorm_package='".(int)$idscorm_package."'");
	
	$arr_package = mysql_fetch_row($rs_package);
	for( $i = 0; $i < count($arr_package); $i++)
		$arr_package[$i] = addslashes($arr_package[$i]);
	mysql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package "
				." (idpackage,idProg,path,defaultOrg,idUser) VALUES "
				."('".implode("','", $arr_package)."')");
		
	
/*	mysql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package "
				." (idpackage,idProg,path,defaultOrg,idUser) " 
				." SELECT idpackage,idProg,'".$path."',defaultOrg,idUser "
				."   FROM ".$GLOBALS['prefix_lms']."_scorm_package "
				."  WHERE idscorm_package='".(int)$idscorm_package."'");*/
	
	$new_idscorm_package = mysql_insert_id();
	
	/* copy resources */
	$rs_resources = mysql_query(" SELECT idsco,'".$new_idscorm_package."',scormtype,href "
								."  FROM ".$GLOBALS['prefix_lms']."_scorm_resources "
								." WHERE idscorm_package='".(int)$idscorm_package."'");
								
	while( $arr_resource = mysql_fetch_row($rs_resources) ) {
		for( $i = 0; $i < count($arr_resource); $i++)
			$arr_resource[$i] = addslashes($arr_resource[$i]);
		mysql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources "
					." (idsco,idscorm_package,scormtype,href) VALUES "
					."('".implode("','", $arr_resource)."')");
	}
	/*mysql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources "
				." (idsco,idscorm_package,scormtype,href) "
				." SELECT idsco,'".$new_idscorm_package."',scormtype,href "
				."   FROM ".$GLOBALS['prefix_lms']."_scorm_resources "
				."  WHERE idscorm_package='".(int)$idscorm_package."'");*/

	$cpm = new CPManager();
	// try to open content package
	if( !$cpm->Open( $scopath.$path ) ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		return FALSE;
	}
	
	// and parse the manifest
	if( !$cpm->ParseManifest() ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		return FALSE;
	}


	$rdb = new RendererDb($GLOBALS['dbConn'], $GLOBALS['prefix_lms'], $new_idscorm_package);
	/*$orgElems = $cpm->orgElems;
	// save all organizations
	foreach( $orgElems as $org )
		$cpm->RenderOrganization( $org->get_attribute('identifier'), $rdb );*/
	
	list($org_identifier) = mysql_fetch_row(mysql_query(
				"SELECT org_identifier FROM ".$GLOBALS['prefix_lms']."_scorm_organizations "
				." WHERE idscorm_organization='".(int)$idscorm_organization."'"));
	
	$cpm->RenderOrganization( $org_identifier, $rdb );
	
	// save flat organization with resources
	//$cpm->RenderOrganization( '-resource-', $rdb );
	
	$so = new Scorm_Organization( addslashes($org_identifier), $new_idscorm_package, $GLOBALS['dbConn'] );
	if( $so->err_code > 0 ) {
		$_SESSION['last_error'] = 'Error: '. $so->getErrorText() . ' [' . $so->getErrorCode() .']';
		return FALSE;
	} else {
		return $so->idscorm_organization;
	}
}


if( isset( $GLOBALS['op'] ) ) {
	
	switch($GLOBALS['op']) {
		/*case "display": {
			display();
		}; break;*/
		case "additem" : {
			additem();
		};break;
		case "insitem" : {
			insitem();
		};break;
		case "deleteitem": {
			deleteitem();
		}; break;
		case "dodelete": {
			dodelete();
		}; break;
		case "category" : {
			category();
		};break;
		case "categorysave": {
			categorysave();
		};break;
		case "play": {
			play();
		};break;
		case "tree": {
			require( dirname(__FILE__) . '/scorm_page_tree.php');
		};break;
		case "head": {
			require( dirname(__FILE__) . '/scorm_page_head.php');
		};break;
		case "body": {
			require( dirname(__FILE__) . '/scorm_page_body.php');
		};break;
		case "scoload": {
			require( dirname(__FILE__) . '/soaplms.php');
		};break;
	}
}
 
?>
