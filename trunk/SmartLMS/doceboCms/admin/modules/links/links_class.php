<?php

/*************************************************************************/
/* DOCEBO - Content Management System                                    */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Claudio Erba, Fabio Pirovano                    */
/* & Giovanni Derks - http://www.spaghettilearning.com                   */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_framework'].'/lib/lib.listview.php');
require_once($GLOBALS['where_framework'].'/lib/lib.treedb.php');
require_once($GLOBALS['where_framework'].'/lib/lib.treeview.php');

/**
 * Customization of DataRetriever for _homerepo table
 **/


class Links_DataRetriever extends DataRetriever {
	// id of selected folder in _links_dir (TreeView)
	// used in query composition to filter items
	var $idFolder = 0;
	var $total_rows=0;

	function _getOpModifyId() { return 'do_modlinks_'; }
	function _getOpMoveFormId() { return 'do_movelinks_form_'; }
	function _getOpMoveLinksId() { return 'do_movelinks_'; }
	function _getOpPublishId() { return 'do_publishlinks_'; }
	function _getOpUnPublishId() { return 'do_unpublishlinks_'; }
	function _getOpRemoveId() { return 'do_remlinks_'; }

	// set the folder
	function setFolder( $idFolder ) { $this->idFolder = $idFolder; }

	// getRows: overload of method of the DataRetriever class
	// execute query for data retrieving
	// tipically called from listView
	function getRows( $startRow, $numRows ) {

		$t1=$this->prefix."_links";
		$t2=$this->prefix."_links_info";
		$sel_lang=getLanguage();
		$query = "SELECT idLinks, publish_date, url, title, fpreview, important, publish "
			." FROM $t1"
			." LEFT JOIN $t2 ON ($t2.idl=$t1.idLinks AND $t2.lang='$sel_lang')"
			." WHERE idFolder='". (int)$this->idFolder . "'";
		$this->total_rows=mysql_num_rows(mysql_query($query));
		return $this->_getData( $query, $startRow, $numRows );
	}

	function getTotalRows() {
		return $this->total_rows;
	}

	// fetchRecord: overload of method of the DataRetriever class
	function fetchRecord() {

		$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

		// fetch a record from record set
		$arrData = parent::fetchRecord();
		if( $arrData === FALSE )
			return FALSE;

		if($arrData["fpreview"] != "") {
			$img="<img src=\""._PPATH.$arrData["fpreview"]."\" width=\"80\" alt=\"\" />\n";
			$arrData["fpreview"]=$img;
		}
		else {
			$arrData["fpreview"]="&nbsp;";
		}


		if(!isset($arrData["title"])) $arrData["title"]="&nbsp;";

		if(!$arrData['publish'])
			$arrData['publish'] = '<input type="submit" class="publishbtn" value="" name="'
					.$this->_getOpPublishId().$arrData['idLinks'] .'" title="'.$lang->def("_PUBLISH").'" id="select_'. $arrData['idLinks'] .'" />';
		else
			$arrData['publish'] = '<input type="submit" class="unpublishbtn" value="" name="'
					.$this->_getOpUnPublishId().$arrData['idLinks'] .'" title="'.$lang->def("_UNPUBLISH").'" id="select_'. $arrData['idLinks'] .'" />';

		$arrData['modify'] = '<input type="submit" class="TVActionEdit" value="" name="'
				.$this->_getOpModifyId().$arrData['idLinks'] .'" id="select_'. $arrData['idLinks'] .'" />';

		$arrData['move'] = '<input type="submit" class="TVActionMove" value="" name="'
				.$this->_getOpMoveFormId().$arrData['idLinks'] .'" title="'.$lang->def("_ALT_MOVE").'" id="select_'. $arrData['idLinks'] .'" />';

		$arrData['remove'] = '<input type="submit" class="TVActionDelete" value="" name="'
				.$this->_getOpRemoveId().$arrData['idLinks'] .'" id="select_'. $arrData['idLinks'] .'" />';

		return $arrData;
	}
}

/**
 * Customizaton of ListView class for homerepo
 **/
class Links_ListView extends ListView {

	var $new_perm;
	var $mod_perm;
	var $rem_perm;

	function _getOpAddId() { return 'do_addlinks_'; }

	function Links_ListView( $title, &$data, &$rend, $id ) {
		parent::ListView( $title, $data, $rend, $id );
		$this->new_perm = checkPerm('add', true);
		$this->mod_perm = checkPerm('mod', true);
		$this->rem_perm = checkPerm('del', true);

	}

	// overload for _getAddLabel operation

	function _getAddAlt() { return _ADD; }
	function _getAddLabel() { return _ADD; }
	function _getAddImage() { return getPathImage().'standard/add.gif'; }
	function _getAddUrl() { return $this->_getOpAddId(); }

	// utility function
	function _createColInfo( $label, $hClass, $fieldClass, $data, $toDisplay, $sortable ) {
		return array( 	'hLabel' => $label,
						'hClass' => $hClass,
						'fieldClass' => $fieldClass,
						'data' => $data,
						'toDisplay' => $toDisplay,
						'sortable' => $sortable );
	}

	function _getLinkPagination() {
		return 'index.php?modname=links&amp;op=links' /*&amp;ord='
				.$this->_getOrd() */
				.'&amp;ini=';
	}

	function _getRowsPage() { return 20; }

	// overload
	function _getCols() {

		$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

		$colInfos = array();
		$colInfos[] = $this->_createColInfo( $lang->def("_PUBDATE"),'','','publish_date',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_PREVIEW"),'','align_center','fpreview',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_TITLE"),'','','title',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_URL"),'','','url',true, false );


		$colInfos[] = $this->_createColInfo(
		'<img src="'.getPathImage().'standard/publish.gif" alt="'.$lang->def("_PUBLISH").'" title="'.$lang->def("_PUBLISH").'" />' ,
		'image','image','publish', $this->mod_perm , false );

		$colInfos[] = $this->_createColInfo(
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />' ,
		'image','image','modify', $this->mod_perm , false );
		$colInfos[] = $this->_createColInfo(
		'<img src="'.getPathImage().'treeview/move.png" alt="'.$lang->def("_ALT_MOVE").'" title="'.$lang->def("_ALT_MOVE").'" />' ,
		'image','image','move', $this->mod_perm , false );
		$colInfos[] = $this->_createColInfo('<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />' ,
		'image','image','remove', $this->rem_perm , false );

		return $colInfos;
	}

}

// customization of TreeDb for homerepo_dir
class linksDb extends TreeDb {

	function linksDb() {

		$this->table = $GLOBALS["prefix_cms"] . '_links_dir';
		$this->fields = array( 'id' => 'id', 'idParent' => 'idParent', 'path' => 'path', 'lev' => 'lev' );
	}

}

class Links_TreeView extends TreeView {

	var $idLinks;
	var $showbtn=0;
	var $show_icons=1;

	var $pathToExpand=NULL;

	function _getOpAddId() { return 'do_addlinks_'; }
	function _getOpModifyId() { return 'do_modlinks_'; }
	function _getOpMoveFormId() { return 'do_movelinks_form_'.$this->idLinks; }
	function _getOpMoveLinksId() { return 'do_movelinks_'.$this->idLinks; }
	function _getOpPublishId() { return 'do_publishlinks_'.$this->idLinks; }
	function _getOpUnPublishId() { return 'do_unpublishlinks_'.$this->idLinks; }
	function _getOpRemoveId() { return 'do_remlinks_'; }
	function _getCancelId() 		{ return 'do_treeview_cancel_'		.$this->idLinks; }

	function getLinksSelected() { return $this->idLinks; }

	function _getAddImage() { return getPathImage().'treeview/folder_new.png'; }
	function _getCreateImage() { return getPathImage().'treeview/folder_new.png'; }

	function _getAddLabel() { return def("_NEWHOMEFOLDER", "admin_links", "cms"); }
	function _getAddAlt() { return def("_NEWHOMEFOLDER", "admin_links", "cms"); }
	function _getCreateLabel() { return def("_NEWHOMEFOLDER", "admin_links", "cms"); }
	function _getCreateAlt() { return def("_NEWHOMEFOLDER", "admin_links", "cms"); }
	function _getMoveLinksTargetLabel()  { return def("_NEWLINKSFOLDER", "admin_links", "cms").": "; }


	function _getOpDelTitle() { return def("_LINKSDELF", "admin_links", "cms"); }
	function _getOpDelId() { return 'do_opdeletefolder_links_'.$this->id; }
	function _getOpMoveTitle() { return def("_LINKSMOVEF", "admin_links", "cms"); }
	function _getOpMoveId() { return 'do_opmovefolder_links_'.$this->id; }
	function _getOpRenTitle() { return def("_LINKSRENF", "admin_links", "cms"); }
	function _getOpRenId() { return 'do_oprenamefolder_links_'.$this->id; }

	function _getFolderNameLabel() { return def("_LINKSFNAME", "admin_links", "cms");}
	function _getCreateFolderId() { return 'do_treeview_create_folder_'	.$this->id; }
	function _getRenameFolderId() { return 'do_treeview_rename_folder_'	.$this->id; }
	function _getMoveFolderId() 	{ return 'do_treeview_move_folder_'	.$this->id; }
	function _getDeleteFolderId() 	{ return 'do_treeview_delete_folder_'	.$this->id; }

	function canAdd() { return checkPerm('add', true); }
	function canDelete() { return FALSE; }
	function canRename() { return FALSE; }
	function canMove() { return FALSE; }
	function canInlineRename()  { return ($this->showbtn && checkPerm('mod', true)); }
	function canInlineMove()  { return ($this->showbtn && checkPerm('mod', true)); }

	function canInlineMoveItem( &$stack, $level ) {
		if( $level == 0 || !$this->show_icons )
			return FALSE;
		return TRUE;
	}
	function canInlineRenameItem( &$stack, $level ) {
		if( $level == 0 || !$this->show_icons )
			return FALSE;
		return TRUE;
	}

	function expandPath( $path ) {
		$parentFolder =& $this->tdb->getFolderByPath($path);
		$this->selectedFolder =$parentFolder->id;
		while($path != "") {

			$parentFolder =& $this->tdb->getFolderByPath($path);
			if($parentFolder !== NULL && $parentFolder->id != false) {
				$this->expand($parentFolder->id);
				$path = $parentFolder->getParentPath();
			} else {
				$path = '';
			}
		}
	}

	function extendedParsing( $arrayState, $arrayExpand, $arrayCompress ) {


	   if( $this->pathToExpand != NULL ) {
	       if( is_array($this->expandList) ) {
	           $this->expandList = $this->expandList + $this->pathToExpand;
           }
	       else {
	           $this->expandList = $this->pathToExpand;
           }
	   }

		foreach( $_POST as $nameField => $valueField ) {
			// create folder
			if( strstr( $nameField, $this->_getCreateFolderId() ) ) { // create folder
				$sel_lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$sel_lang];
				if( trim($folderName) != "" ) {
					$this->tdb->addFolderById( $this->selectedFolder, $folderName );
					list($idLinks)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idLinks, $arr);

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getRenameFolderId() ) ) { // rinomina cartella e titoli lingue
				$sel_lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$sel_lang];
				if ($folderName != "") {
					$idLinks=$this->selectedFolder;
					$folder = $this->tdb->getFolderById( $idLinks );
					$this->tdb->renameFolder( $folder, $folderName );

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idLinks, $arr);

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getDeleteFolderId() ) ) { // rimuove cartella e titoli lingue
				$q=mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_links_titles WHERE iddir='".$this->selectedFolder."';");
				// Nota: la cartella viene rimossa dalla parent!
				$this->refresh = true;
			}
		}
		parent::extendedParsing( $arrayState, $arrayExpand, $arrayCompress );
	}


	function parsePositionData( $arrayState, $arrayExpand, $arrayCompress ) {


		foreach( $_POST as $nameField => $valueField ) {
			 if( strstr( $nameField, $this->_getOpAddId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpAddId()));
				$this->op = 'addlinks';
				$this->idLinks = $id;
			}
			else if( strstr( $nameField, $this->_getOpModifyId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModifyId()));
				$this->op = 'modlinks';
				$this->idLinks = $id;
			}
			else if( strstr( $nameField, $this->_getOpRemoveId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpRemoveId()));
				$this->op = 'dellinks';
				$this->idLinks = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveFormId() ) ) { // Sposta links (mostra form)
				$id = substr( $nameField, strlen($this->_getOpMoveFormId()));
				$this->op = 'links_move_form';
				$this->idLinks = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveLinksId() ) ) { // Sposta links (ora!)
				$id = substr( $nameField, strlen($this->_getOpMoveLinksId()));
				$this->op = '';
				$this->idLinks = $id;
				$this->folder_id = $_POST["folder_id"];
				movelinks($this);
			}
			else if( strstr( $nameField, $this->_getOpPublishId() ) ) { // Publish
				$id = substr( $nameField, strlen($this->_getOpPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_links SET publish='1' WHERE idLinks='$id';");
				//if (!$q) echo ("<script>alert('Errore: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpUnPublishId() ) ) { // Un-Publish
				$id = substr( $nameField, strlen($this->_getOpUnPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_links SET publish='0' WHERE idLinks='$id';");
				//if (!$q) echo ("<script>alert('Errore: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpRenameFolderId() ) ) { // Rinomina cartella
				$id = substr( $nameField, strlen($this->_getOpRenameFolderId()));
				$this->idFolder=$id;
				$this->op = 'renamefolder';
			}
			else if( strstr( $nameField, $this->_getCancelId() ) ) { // Annulla
				$id = substr( $nameField, strlen($this->_getCancelId()));
				$this->op = '';
			}
		}
		parent::parsePositionData( $arrayState, $arrayExpand, $arrayCompress );
	}


	function printElement(&$stack, $level) {

		$res="";

		if ($this->showbtn) {
			if( $level > 0 ) {

				$arrData = $stack[$level]['folder']->otherValues;
				$links_cnt=count_child($stack[$level]['folder']->id);
				$nochild=$stack[$level]['isLeaf'];
				if( checkPerm('del', true) && $nochild && $links_cnt == 0) {
					$res.='<input type="submit" class="OrgDelFolder" value="" name="'
						.$this->_getOpDeleteFolderId().$stack[$level]['folder']->id .'"'
						.' title="'.$this->_getDeleteLabel().'" />';
				}
				else {
					$res.='<input type="submit" class="OrgPlay" value="" name="'
						.$this->_getCancelId().'"'
						.' title="" />';
				}
			}
		}
		$res.=parent::printElement($stack, $level);

		return $res;
	}


	function loadNewFolder() {
		$res="";

		$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

		$back_ui_url="index.php?modname=links&amp;op=links";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_LINKS");
		$title_arr[]=$lang->def("_NEWHOMEFOLDER");
		$res.=getTitleArea($title_arr, "links");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

		$res.=$this->printState();

		$larr=$GLOBALS['globLangManager']->getAllLangCode();

		foreach ($larr as $key=>$cl) {
			$res.='<label for="'.$this->_getFolderNameId().'_'.$cl.'">'.$this->_getFolderNameLabel().': </label>';
			$res.=' <input type="text" value="" name="'.$this->_getFolderNameId()
				.'_'.$cl.'" id="'.$this->_getFolderNameId().'_'.$cl.'" /> ('.$cl.')<br />';
		}

		$res.=' <br /><img src="'.$this->_getCreateImage().'" alt="'.$this->_getCreateAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCreateLabel().'"'
			.' name="'.$this->_getCreateFolderId().'" id="'.$this->_getCreateFolderId().'" />';
		$res.=' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />';

		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
		$res.="</div>\n";
		return $res;
	}


	function loadRenameFolder() {
		$res="";

		$lang=& DoceboLanguage::createInstance('admin_links', 'cms');


		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_links_titles WHERE iddir='".$this->getSelectedFolderId()."';";
		$q=mysql_query($qtxt);

		$lval=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$lval[$row["lang"]]=$row["title"];
			}
		}

		$back_ui_url="index.php?modname=links&amp;op=links";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_LINKS");
		$title_arr[]=$lang->def("_RENAME_FOLDER").": ".$lval[getLanguage()];
		$res.=getTitleArea($title_arr, "links");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

		$res.=$this->printState();

		$larr=$GLOBALS['globLangManager']->getAllLangCode();

		foreach ($larr as $key=>$cl) {
			$res.='<label for="'.$this->_getFolderNameId().'_'.$cl.'">'.$this->_getFolderNameLabel().': </label>';
			$res.=' <input type="text" name="'.$this->_getFolderNameId()
				.'_'.$cl.'" id="'.$this->_getFolderNameId().'_'.$cl.'" '
				.'value="'.$lval[$cl].'" /> ('.$cl.')<br />'."\n";
		}

		$res.=' <br /><img src="'.$this->_getRenameImage().'" alt="'.$this->_getRenameAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getRenameLabel().'"'
			.' name="'.$this->_getRenameFolderId().'" id="'.$this->_getRenameFolderId().'" />';
		$res.=' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />';

		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
		$res.="</div>\n";
		return $res;
	}

	function loadState() {
		if( isset($_SESSION['links_tree_state']) )
			$this->setState( unserialize(stripslashes($_SESSION['links_tree_state'])));
	}

	function saveState() {
		$_SESSION['links_tree_state'] = addslashes(serialize($this->getState()));
	}

}


function count_child($id) {


	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_links WHERE idFolder='$id';";
	$q=mysql_query($qtxt);

	if ($q) {
		$res=mysql_num_rows($q);
	}


	return $res;
}


function save_cat_lang($id, $arr) {


	$db_arr=array();
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_links_titles WHERE iddir='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_arr[]=$row["lang"];
		}
	}


	foreach ($arr as $key=>$val) {
		if (in_array($key, $db_arr)) { // Aggiorno
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_links_titles SET title='$val' WHERE iddir='$id' AND lang='$key';";
			$q=mysql_query($qtxt);
		}
		else { // Inserisco
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_links_titles (iddir, lang, title) VALUES('$id','$key','$val');";
			$q=mysql_query($qtxt);
		}
	}
}


function links_getOp( &$treeView ) {
	$op = $treeView->op;

	if( $op == "" ) {
		$listView = & $treeView->getListView();
		if( $listView !== NULL )
		 	$op = $listView->op;
	}

	return $op;
}

function links_addfolder( &$treeView ) {

	$GLOBALS["page"]->add($treeView->loadNewFolder());
}


function links_renamefolder( &$treeView ) {

	$GLOBALS["page"]->add($treeView->loadRenameFolder());
}

function links_move_form(&$tree) {
	checkPerm('mod');

	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

	$res="";

	$back_ui_url="index.php?modname=links&amp;op=links";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_LINKS");
	$title_arr[]=$lang->def("_MOVE_LINKS");
	$res.=getTitleArea($title_arr, "links");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	if( isset($_POST["idLinks"]) )
		$links_id=$_POST["idLinks"];
	else
		$links_id=$tree->idLinks;

	if( isset($_POST[$tree->_getFolderNameId()]) )
		$folderid = $_POST[$tree->_getFolderNameId()];
	else
		$folderid = $tree->getSelectedFolderId();

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );
	$res.='<input type="hidden" value="" name="'.$tree->_getOpMoveFormId().'" />';
	$res.='<input type="hidden" value="'.$folderid.'" name="'.$tree->_getFolderNameId().'" />';
	$res.='<input type="hidden" value="'.$tree->getSelectedFolderId().'" name="folder_id" />';
	$res.='<input type="hidden" value="'.$links_id.'" name="idLinks" />';
	$res.='<div>'.$tree->_getMoveLinksTargetLabel().$tree->getFolderPrintName($folder).'</div>';
	$res.=$tree->load();
	$res.=' <img src="'.$tree->_getMoveImage().'" alt="'.$tree->_getMoveAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$lang->def("_MOVELINKS").'"'
		.' name="'.$tree->_getOpMoveLinksId().$links_id.'" id="'.$tree->_getOpMoveLinksId().$links_id.'" />';
	$res.=' <img src="'.$tree->_getCancelImage().'" alt="'.$tree->_getCancelAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$tree->_getCancelLabel().'"'
		.' name="'.$tree->_getCancelId().'" id="'.$tree->_getCancelId().'" />';

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.='</div>';

	return $res;
}

function links_move_folder( &$treeView ) {

	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

	$res="";
	$back_ui_url="index.php?modname=links&amp;op=links";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_LINKS");
	$title_arr[]=$lang->def("_MOVE_FOLDER");
	$res.=getTitleArea($title_arr, "links");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.=$treeView->loadMoveFolder();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$GLOBALS['page']->add($res);
}

function links_deletefolder( &$treeView ) {

	$lang=& DoceboLanguage::createInstance('admin_links', 'cms');

	$res="";
	$back_ui_url="index.php?modname=links&amp;op=links";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_LINKS");
	$title_arr[]=$lang->def("_DELETE_FOLDER");
	$res.=getTitleArea($title_arr, "links");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.=$treeView->loadDeleteFolder();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$GLOBALS['page']->add($res);
}

function movelinks($tree) {


	$idLinks=$tree->idLinks;
	$folder_id=$tree->folder_id;

	$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_links SET idFolder='$folder_id' WHERE idLinks='$idLinks' LIMIT 1;");

	if (!$q) echo mysql_error();
}

function &createTreeView( $withContents = TRUE, $multiSelect = FALSE, $withActions = FALSE, $sel_path=FALSE ) {

	$dirDb = new linksDb();
	$treeView = new Links_TreeView( $dirDb, 'links' );
	$treeView->loadState();
	$treeView->parsePositionData( $_POST, $_POST, $_POST );
	$treeView->saveState();

	$dataRetriever = new Links_DataRetriever( NULL, $GLOBALS["prefix_cms"] );
	$typeOneRenderer = new typeOne(20);
	$listView = new Links_ListView( '', $dataRetriever, $typeOneRenderer, 'idLinks');

	$listView->multiSelect = $multiSelect;

	$listView->parsePositionData( $_POST );

	if (!empty($sel_path))
		$treeView->expandPath($sel_path); // espande l'albero in base al path selezionato

	$dataRetriever->setFolder( $treeView->selectedFolder );

	$listView->addurl = $treeView->_getOpNewFolderId();
	
	$treeView->setlistView( $listView );

	return $treeView;
}

?>
