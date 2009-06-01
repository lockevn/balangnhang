<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <giovanni[AT]docebo-com>         */
/* http://www.docebocms.org                                              */
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

class Docs_DataRetriever extends DataRetriever {
	// id of selected folder in _docs_dir (TreeView)
	// used in query composition to filter items
	var $idFolder = 0;
	var $sel_mode=false;
	var $total_rows=0;

	var $use_admin_filter=FALSE; //&
	var $node_perm=FALSE;

	function _getOpModifyId() { return 'do_moddocs_'; }
	function _getOpMoveFormId() { return 'do_movedocs_form_'; }
	function _getOpPublishId() { return 'do_publishdocs_'; }
	function _getOpUnPublishId() { return 'do_unpublishdocs_'; }
	function _getOpRemoveId() { return 'do_remdocs_'; }


	function setUseAdminFilter($val) { //&
		$this->use_admin_filter=$val;
	}


	function getUseAdminFilter() {
		return $this->use_admin_filter;
	}


	function setNodePerm($user_id, $page_perm) {
		$this->node_perm[$user_id]=$page_perm;
	}


	function getNodePerm($user_id) {

		if (isset($this->node_perm[$user_id]))
			$res=$this->node_perm[$user_id];
		else
			$res=FALSE;

		return $res;
	}


	function canAccessToNode($user_id, $folder_id) {
		$res=FALSE;

		if (!$this->getUseAdminFilter())
			return TRUE;

		$node_perm =$this->getNodePerm($user_id);
		if ($node_perm === FALSE) {
			require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

			$ctp=new CmsTreePermissions("document");

			$node_perm=$ctp->loadAllNodePerm($user_id, TRUE);
			$node_perm=$node_perm["all"];
			$this->setNodePerm($user_id, $node_perm);
		}

		$res=(in_array((int)$folder_id, $node_perm) ? TRUE : FALSE);
		return $res;
	}


	// set the folder
	function setFolder( $idFolder ) { $this->idFolder = $idFolder; }

	// getRows: overload of method of the DataRetriever class
	// execute query for data retrieving
	// tipically called from listView
	function getRows( $startRow, $numRows ) {

		$t1=$this->prefix."_docs";
		$t2=$this->prefix."_docs_info";
  	$sel_lang=getLanguage();
		$query = "SELECT idDocs, idFolder, publish_date, fname, real_fname, important, publish, sdesc "
			." FROM $t1"
			." LEFT JOIN $t2 ON ($t2.idd=$t1.idDocs AND $t2.lang='$sel_lang')"
			." WHERE idFolder='". (int)$this->idFolder . "'";
		$this->total_rows=mysql_num_rows(mysql_query($query));
		return $this->_getData( $query, $startRow, $numRows );
	}

	function getTotalRows() {
		return $this->total_rows;
	}

	// fetchRecord: overload of method of the DataRetriever class
	function fetchRecord() {
		// fetch a record from record set
		$arrData = parent::fetchRecord();
		if( $arrData === FALSE )
			return FALSE;

		$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

		$folder_id =(int)$arrData["idFolder"]; //&
		if (!$this->canAccessToNode($GLOBALS["current_user"]->getIdSt(), $folder_id)) {
			$can_mod =FALSE;
			$can_del =FALSE;
		}
		else {
			$can_mod=checkPerm('mod', true);
			$can_del=checkPerm('del', true);
		}

		// Tipo:
		$fn=$arrData["real_fname"];
		$expFileName = explode('.', $fn);
		$totPart = count($expFileName) - 1;
		$mime=mimetype($expFileName[$totPart]);
		$img="<img src=\"".getPathImage().mimeDetect($fn)."\" alt=\"$mime\" title=\"$mime\" />\n";
		$arrData["type"]=$img;

		if(!isset($arrData["sdesc"])) $arrData["sdesc"]="&nbsp;";

		if ($can_mod) {

			if(!$arrData['publish'])
				$arrData['publish'] = '<input type="submit" class="publishbtn" value="" name="'
						.$this->_getOpPublishId().$arrData['idDocs'] .'" title="'.$lang->def("_PUBLISH")
						.'" id="id_'.$this->_getOpPublishId().$arrData['idDocs'] .'" />';
			else
				$arrData['publish'] = '<input type="submit" class="unpublishbtn" value="" name="'
						.$this->_getOpUnPublishId().$arrData['idDocs'] .'" title="'.$lang->def("_UNPUBLISH")
						.'" id="id_'.$this->_getOpUnPublishId().$arrData['idDocs'] .'" />';

			$arrData['modify'] = '<input type="submit" class="TVActionEdit" value="" name="'
					.$this->_getOpModifyId().$arrData['idDocs'] .'" id="select_'. $arrData['idDocs'] .'" />';

			$arrData['move'] = '<input type="submit" class="TVActionMove" value="" name="'
					.$this->_getOpMoveFormId().$arrData['idDocs'] .'" title="'.$lang->def("_ALT_MOVE").'" id="select_'. $arrData['idDocs'] .'" />';
		}
		else {
			$arrData['move']="&nbsp;";
			$arrData['modify']="&nbsp;";
			$arrData['publish']="&nbsp;";
		}

		if ($can_del) {
			$arrData['remove'] = '<input type="submit" class="TVActionDelete" value="" name="'
					.$this->_getOpRemoveId().$arrData['idDocs'] .'" id="select_'. $arrData['idDocs'] .'" />';
		}
		else {
			$arrData['remove']="&nbsp;";
		}

		if ($this->sel_mode) {

			$block_id=(int)importVar("block_id");
			$sub_id=(int)importVar("sub_id");

			$url ="index.php?modname=manpage&amp;op=modblock&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=".$sub_id;
			$url.="&amp;blk_op=additem&amp;type=docs&amp;item_id=".$arrData['idDocs'];
			$img='<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def("_ATTACHMENT").'" title="'.$lang->def("_ATTACHMENT").'" />';
			$arrData["attach"]="<a href=\"".$url."\">".$img."</a>\n";
		}

		return $arrData;
	}
}

/**
 * Customizaton of ListView class for homerepo
 **/
class Docs_ListView extends ListView {

	var $new_perm;
	var $mod_perm;
	var $rem_perm;
	var $sel_mode=false;

	function _getOpAddId() { return 'do_adddocs_'; }

	function Docs_ListView( $title, &$data, &$rend, $id ) {
		parent::ListView( $title, $data, $rend, $id );
		$this->new_perm = checkPerm('add', true);
		$this->mod_perm = checkPerm('mod', true);
		$this->rem_perm = checkPerm('del', true);

	}

	// overload for _getAddLabel operation

	function _getAddAlt() { return def("_ADD", "admin_docs", "cms"); }
	function _getAddLabel() { return def("_ADD", "admin_docs", "cms"); }
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
		return 'index.php?modname=docs&amp;op=docs' /*&amp;ord='
				.$this->_getOrd() */
				.'&amp;ini=';
	}

	function _getRowsPage() { return 20; }

	// overload
	function _getCols() {

		$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

		$colInfos = array();
		if (!$this->sel_mode) {
			$colInfos[] = $this->_createColInfo( $lang->def("_PUBDATE"),'','','publish_date',true, false );
		}
		$colInfos[] = $this->_createColInfo( $lang->def("_TYPE"),'image','align_center','type',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_FILENAME"),'','','fname',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_SHORTDESC"),'','','sdesc',true, false );


		if (!$this->sel_mode) {

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

		}
		else {
			$colInfos[] = $this->_createColInfo(
			'<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def("_ATTACHMENT").'" title="'.$lang->def("_ATTACHMENT").'" />' ,
			'image','image','attach', $this->mod_perm , false );
		}

		return $colInfos;
	}

	function setUseAdminFilter($val) { //&
		$this->data->setUseAdminFilter($val);
	}

}

// customization of TreeDb for homerepo_dir
class docsDb extends TreeDb {

	function docsDb() {

		$this->table = $GLOBALS["prefix_cms"] . '_docs_dir';
		$this->fields = array( 'id' => 'id', 'idParent' => 'idParent', 'path' => 'path', 'lev' => 'lev' );
	}

}

class Docs_TreeView extends TreeView {

	var $idDocs;
	var $showbtn=0;
	var $show_icons_for_item =array(); //&

	var $use_admin_filter=FALSE; //&
	var $node_perm=FALSE;

	function _getOpAddId() { return 'do_adddocs_'; }
	function _getOpModifyId() { return 'do_moddocs_'; }
	function _getOpMoveFormId() { return 'do_movedocs_form_'.$this->idDocs; }
	function _getOpMoveDocsId() { return 'do_movedocs_'.$this->idDocs; }
	function _getOpPublishId() { return 'do_publishdocs_'.$this->idDocs; }
	function _getOpUnPublishId() { return 'do_unpublishdocs_'.$this->idDocs; }
	function _getOpRemoveId() { return 'do_remdocs_'; }
	function _getCancelId() 		{ return 'do_treeview_cancel_'		.$this->idDocs; }

	function getDocsSelected() { return $this->idDocs; }

	function _getAddImage() { return getPathImage().'treeview/folder_new.png'; }
	function _getCreateImage() { return getPathImage().'treeview/folder_new.png'; }

	function _getAddLabel() { return def("_NEWHOMEFOLDER", "admin_docs", "cms"); }
	function _getAddAlt() { return def("_NEWHOMEFOLDER", "admin_docs", "cms"); }
	function _getCreateLabel() { return def("_NEWHOMEFOLDER", "admin_docs", "cms"); }
	function _getCreateAlt() { return def("_NEWHOMEFOLDER", "admin_docs", "cms"); }
	function _getMoveDocsTargetLabel()  { return def("_NEWDOCSFOLDER", "admin_docs", "cms"); }


	function _getOpDelTitle() { return def("_DOCSDELF", "admin_docs", "cms"); }
	function _getOpDelId() { return 'do_opdeletefolder_docs_'.$this->id; }
	function _getOpMoveTitle() { return def("_DOCSMOVEF", "admin_docs", "cms"); }
	function _getOpMoveId() { return 'do_opmovefolder_docs_'.$this->id; }
	function _getOpRenTitle() { return def("_DOCSRENF", "admin_docs", "cms"); }
	function _getOpRenId() { return 'do_oprenamefolder_docs_'.$this->id; }

	function _getFolderNameLabel() { return def("_DOCSFNAME", "admin_docs", "cms");}
	function _getCreateFolderId() { return 'do_treeview_create_folder_'	.$this->id; }
	function _getMyRenameFolderId() { return 'do_treeview_rename_folder_'	.$this->id; }
	function _getMoveFolderId() 	{ return 'do_treeview_move_folder_'	.$this->id; }
	function _getDeleteFolderId() 	{ return 'do_treeview_delete_folder_'	.$this->id; }

	function canAdd() { return checkPerm('add', true); }
	function canDelete() { return FALSE; }
	function canRename() { return FALSE; }
	function canMove() { return FALSE; }
	function canInlineRename()  { return ($this->showbtn && checkPerm('mod', true)); }
	function canInlineMove()  { return ($this->showbtn && checkPerm('mod', true)); }

	function canInlineMoveItem( &$stack, $level ) {
		if( $level == 0 )
			return FALSE;
		$folder_id =$stack[$level]['folder']->id; //&
		if ((isset($this->show_icons_for_item[$folder_id])) && (($this->show_icons_for_item[$folder_id] === FALSE)))
			return FALSE;
		return TRUE;
	}
	function canInlineRenameItem( &$stack, $level ) {
		if( $level == 0 )
			return FALSE;
		$folder_id =$stack[$level]['folder']->id; //&
		if ((isset($this->show_icons_for_item[$folder_id])) && (($this->show_icons_for_item[$folder_id] === FALSE)))
			return FALSE;
		return TRUE;
	}


	function setUseAdminFilter($val) { //&
		$this->use_admin_filter=$val;
	}


	function getUseAdminFilter() {
		return $this->use_admin_filter;
	}


	function setNodePerm($user_id, $page_perm) {
		$this->node_perm[$user_id]=$page_perm;
	}


	function getNodePerm($user_id) {

		if (isset($this->node_perm[$user_id]))
			$res=$this->node_perm[$user_id];
		else
			$res=FALSE;

		return $res;
	}


	function canAccessToNode($user_id, $folder_id) {
		$res=FALSE;

		if (!$this->getUseAdminFilter())
			return TRUE;

		$node_perm =$this->getNodePerm($user_id);
		if ($node_perm === FALSE) {
			require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

			$ctp=new CmsTreePermissions("document");

			$node_perm=$ctp->loadAllNodePerm($user_id, TRUE);
			$node_perm=$node_perm["all"];
			$this->setNodePerm($user_id, $node_perm);
		}

		$res=(in_array((int)$folder_id, $node_perm) ? TRUE : FALSE);
		return $res;
	}


	function extendedParsing( $arrayState, $arrayExpand, $arrayCompress ) {


		foreach( $_POST as $nameField => $valueField ) {
			// create folder
			if( strstr( $nameField, $this->_getCreateFolderId() ) ) { // create folder
				$sel_lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$sel_lang];
				if( trim($folderName) != "" ) {
					$this->tdb->addFolderById( $this->selectedFolder, $folderName );
					list($idDocs)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idDocs, $arr);

					docs_appendNewNodePerm($idDocs); //&

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getMyRenameFolderId() ) ) { // rinomina cartella e titoli lingue
    		$sel_lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$sel_lang];
				if ($folderName != "") {
					$idDocs=$this->selectedFolder;
					$folder = $this->tdb->getFolderById( $idDocs );
					$folder_path =$folder->getFolderPath(); // Missing "/root" prefix on rename fix //&
					if (substr($folder_path, 0, 5) !== "/root") {
						$folder->setFolderPath("/root".$folder_path);
					}
					$this->tdb->renameFolder( $folder, $folderName );

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idDocs, $arr);

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getDeleteFolderId() ) ) { // rimuove cartella e titoli lingue
				$q=mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_docs_titles WHERE iddir='".$this->selectedFolder."';");
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
				$this->op = 'adddocs';
				$this->idDocs = $id;
			}
			else if( strstr( $nameField, $this->_getOpModifyId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModifyId()));
				$this->op = 'moddocs';
				$this->idDocs = $id;
			}
			else if( strstr( $nameField, $this->_getOpRemoveId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpRemoveId()));
				$this->op = 'deldocs';
				$this->idDocs = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveFormId() ) ) { // Sposta docs (mostra form)
				$id = substr( $nameField, strlen($this->_getOpMoveFormId()));
				$this->op = 'docs_move_form';
				$this->idDocs = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveDocsId() ) ) { // Sposta docs (ora!)
				$id = substr( $nameField, strlen($this->_getOpMoveDocsId()));
				$this->op = '';
				$this->idDocs = $id;
				$this->folder_id = $_POST["folder_id"];
				movedocs($this);
			}
			else if( strstr( $nameField, $this->_getOpPublishId() ) ) { // Publish
				$id = substr( $nameField, strlen($this->_getOpPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_docs SET publish='1' WHERE idDocs='$id';");
				//if (!$q) $out->add("<script>alert('Error: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpUnPublishId() ) ) { // Un-Publish
				$id = substr( $nameField, strlen($this->_getOpUnPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_docs SET publish='0' WHERE idDocs='$id';");
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


	function printParentElement(&$stack, $level) {
		$elem=parent::printElement($stack, $level);

		return $elem;
	}


	function printElement(&$stack, $level) {

		$res="";
		$can_access_to_node =TRUE; //&

		$folder_id =$stack[$level]['folder']->id; //&
		if (!$this->canAccessToNode($GLOBALS["current_user"]->getIdSt(), $folder_id)) {
			$this->show_icons_for_item[$folder_id] =FALSE;
			$can_access_to_node =FALSE;
		}

		if (($this->showbtn) && ($can_access_to_node)) { //&
			if( $level > 0 ) {

				$arrData = $stack[$level]['folder']->otherValues;
				$docs_cnt=count_child($stack[$level]['folder']->id);
				$nochild=$stack[$level]['isLeaf'];
				if( checkPerm('del', true) && $nochild && $docs_cnt == 0) {
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

		$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

		$back_ui_url="index.php?modname=docs&amp;op=docs";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_DOCS");
		$title_arr[]=$lang->def("_NEWHOMEFOLDER");
		$res.=getTitleArea($title_arr, "docs");
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

		$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_docs_titles WHERE iddir='".$this->getSelectedFolderId()."';";
		$q=mysql_query($qtxt);

		$lval=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$lval[$row["lang"]]=$row["title"];
			}
		}

		$back_ui_url="index.php?modname=docs&amp;op=docs";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_DOCS");
		$title_arr[]=$lang->def("_RENAME_FOLDER").": ".$lval[getLanguage()];
		$res.=getTitleArea($title_arr, "docs");
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
			.' name="'.$this->_getMyRenameFolderId().'" id="'.$this->_getMyRenameFolderId().'" />';
		$res.=' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />';

		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
		$res.="</div>\n";
		return $res;
	}


	function setSelMode($value) {
		$listView=& $this->getListView();
		$data=& $listView->getDataRetrivier();
		$listView->sel_mode=(bool)$value;
		$data->sel_mode=(bool)$value;
	}

	function loadState() {
		if( isset($_SESSION['docs_tree_state']) )
			$this->setState( unserialize(stripslashes($_SESSION['docs_tree_state'])));
	}

	function saveState() {
		$_SESSION['docs_tree_state'] = addslashes(serialize($this->getState()));
	}

	function setSelectedFolderId($folder_id) { //&
		$this->selectedFolder=$folder_id;
	}

}


function count_child($id) {


	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_docs WHERE idFolder='$id';";
	$q=mysql_query($qtxt);

	if ($q) {
		$res=mysql_num_rows($q);
	}


	return $res;
}


function save_cat_lang($id, $arr) {


	$db_arr=array();
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_docs_titles WHERE iddir='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_arr[]=$row["lang"];
		}
	}


	foreach ($arr as $key=>$val) {
		if (in_array($key, $db_arr)) { // Aggiorno
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_docs_titles SET title='$val' WHERE iddir='$id' AND lang='$key';";
			$q=mysql_query($qtxt);
		}
		else { // Inserisco
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_docs_titles (iddir, lang, title) VALUES('$id','$key','$val');";
			$q=mysql_query($qtxt);
		}
	}
}


function docs_getOp( &$treeView ) {
	$op = $treeView->op;

	if( $op == "" ) {
		$listView = & $treeView->getListView();
		if( $listView !== NULL )
		 	$op = $listView->op;
	}

	return $op;
}

function docs_addfolder( &$treeView ) {
	checkPerm('add');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	docs_checkTreePerm($idFolder);

	$GLOBALS["page"]->add($treeView->loadNewFolder());
}


function docs_renamefolder( &$treeView ) {
	checkPerm('mod');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	docs_checkTreePerm($idFolder);

	$GLOBALS["page"]->add($treeView->loadRenameFolder());
}

function docs_move_form(&$tree) {
	checkPerm('mod');

	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

	$res="";
	$back_ui_url="index.php?modname=docs&amp;op=docs";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_DOCS");
	$title_arr[]=$lang->def("_MOVE_DOCUMENT");
	$res.=getTitleArea($title_arr, "docs");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	if( isset($_POST["idDocs"]) )
		$docs_id=$_POST["idDocs"];
	else
		$docs_id=$tree->idDocs;

	if( isset($_POST[$tree->_getFolderNameId()]) )
		$folderid = $_POST[$tree->_getFolderNameId()];
	else
		$folderid = $tree->getSelectedFolderId();

	docs_checkTreePerm($folderid); //&

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );
	$res.='<input type="hidden" value="" name="'.$tree->_getOpMoveFormId().'" />';
	$res.='<input type="hidden" value="'.$folderid.'" name="'.$tree->_getFolderNameId().'" />';
	$res.='<input type="hidden" value="'.$tree->getSelectedFolderId().'" name="folder_id" />';
	$res.='<input type="hidden" value="'.$docs_id.'" name="idDocs" />';
	$res.='<div>'.$tree->_getMoveDocsTargetLabel().$tree->getFolderPrintName($folder).'</div>';
	$res.=$tree->load();
	$res.=' <img src="'.$tree->_getMoveImage().'" alt="'.$tree->_getMoveAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$lang->def("_ALT_MOVE").'"'
		.' name="'.$tree->_getOpMoveDocsId().$docs_id.'" id="'.$tree->_getOpMoveDocsId().$docs_id.'" />';
	$res.=' <img src="'.$tree->_getCancelImage().'" alt="'.$tree->_getCancelAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$tree->_getCancelLabel().'"'
		.' name="'.$tree->_getCancelId().'" id="'.$tree->_getCancelId().'" />';

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.='</div>';

	return $res;
}

function docs_move_folder( &$treeView ) {
	checkPerm('mod');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	$tree_perm =docs_checkTreePerm($idFolder, TRUE);

	$err ="";
	if (!$tree_perm) {
		$folder =$treeView->tdb->getFolderById($idFolder);
		$folder_name =$treeView->getFolderPrintName($folder);
		$err.=getErrorUi(def("_NO_FOLDER_ACCESS_PERM", "standard").": ".$folder_name);
		$old_folder =(int)$_POST[$treeView->_getFolderNameId()];
		$treeView->setSelectedFolderId($old_folder);
	}

	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

	$res="";
	$back_ui_url="index.php?modname=docs&amp;op=docs";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_DOCS");
	$title_arr[]=$lang->def("_MOVE_FOLDER");
	$res.=getTitleArea($title_arr, "docs");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.=$err; //&
	$res.=$treeView->loadMoveFolder();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$GLOBALS['page']->add($res);
}

function docs_deletefolder( &$treeView ) {
	checkPerm('del');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	docs_checkTreePerm($idFolder);

	$lang=& DoceboLanguage::createInstance('admin_docs', 'cms');

	$res="";
	$back_ui_url="index.php?modname=docs&amp;op=docs";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_DOCS");
	$title_arr[]=$lang->def("_DELETE_FOLDER");
	$res.=getTitleArea($title_arr, "docs");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.=$treeView->loadDeleteFolder();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$GLOBALS['page']->add($res);
}

function movedocs($tree) {
	checkPerm('mod');

	$idDocs=$tree->idDocs;
	$folder_id=$tree->folder_id;

	$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_docs SET idFolder='$folder_id' WHERE idDocs='$idDocs' LIMIT 1";
	$q=mysql_query($qtxt);

	if (!$q)
		echo mysql_error();
}


function docs_appendNewNodePerm($folder_id) { //&
	$res =TRUE;

	$user_level=$GLOBALS["current_user"]->getUserLevelId();
	if ($user_level != ADMIN_GROUP_GODADMIN) {

		require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

		$ctp=new CmsTreePermissions("document");
		$res =$ctp->appendNewNodePerm($GLOBALS["current_user"]->getIdSt(), $folder_id);
	}

	return $res;
}


function &createTreeView( $withContents = TRUE, $multiSelect = FALSE, $withActions = FALSE, $path =FALSE ) {

	$dirDb = new docsDb();
	$treeView = new Docs_TreeView( $dirDb, 'docs' );
	$treeView->loadState();
	$treeView->parsePositionData( $_POST, $_POST, $_POST );
	$treeView->saveState();

	$dataRetriever = new Docs_DataRetriever( NULL, $GLOBALS["prefix_cms"] );
	$typeOneRenderer = new typeOne(20);
	$listView = new Docs_ListView( '', $dataRetriever, $typeOneRenderer, 'idDocs');

	$listView->multiSelect = $multiSelect;

	$listView->parsePositionData( $_POST );

	if ($path !== FALSE) {
		$parentFolder =& $dirDb->getFolderByPath($path);
		$treeView->selectedFolder =$parentFolder->id;
		while($path != "") {

			$parentFolder =& $dirDb->getFolderByPath($path);
			if($parentFolder !== NULL && $parentFolder->id != false) {
				$treeView->expand($parentFolder->id);
				$path = $parentFolder->getParentPath();
			} else {
				$path = '';
			}
		}
	}


	$dataRetriever->setFolder( $treeView->selectedFolder );

	$listView->addurl = $treeView->_getOpNewFolderId();

	$treeView->setlistView( $listView );


	return $treeView;
}

?>
