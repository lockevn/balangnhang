<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri (esandri@tiscali.com)           */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly!');

include_once($GLOBALS['where_framework']."/lib/lib.listview.php");
include_once($GLOBALS['where_framework']."/lib/lib.treedb.php");
include_once($GLOBALS['where_framework']."/lib/lib.treeview.php");

/**
 * Customization of DataRetriever for _homerepo table
 **/


class Content_DataRetriever extends DataRetriever {
	// id of selected folder in _content_dir (TreeView)
	// used in query composition to filter items
	var $idFolder = 0;
	var $found_rows = 0;
	var $sel_mode=false;
	var $total_rows=0;

	var $use_admin_filter=FALSE; //&
	var $node_perm=FALSE;

	function _getOpPublishId() { return 'do_publishcontent_'; }
	function _getOpUnPublishId() { return 'do_unpublishcontent_'; }
	function _getOpMoveDownId() { return 'do_movedowncontent_'; }
	function _getOpMoveUpId() { return 'do_moveupcontent_'; }
	function _getOpModifyId() { return 'do_modcontent_'; }
	function _getOpMoveFormId() { return 'do_movecontent_form_'; }
	function _getOpRemoveId() { return 'do_remcontent_'; }


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

			$ctp=new CmsTreePermissions("content");

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
		$query = "SELECT idContent, idFolder, publish_date, type, title, important, publish, ord, language "
			." FROM ".$this->prefix."_content"
			." WHERE idFolder='". (int)$this->idFolder . "' ORDER BY ord ASC";
		$this->total_rows=mysql_num_rows(mysql_query($query));
		$q=$this->_getData( $query, $startRow, $numRows );
		$this->found_rows=mysql_num_rows($q);
		return $q;
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

		$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

		$folder_id =(int)$arrData["idFolder"]; //&
		if (!$this->canAccessToNode($GLOBALS["current_user"]->getIdSt(), $folder_id)) {
			$can_mod =FALSE;
			$can_del =FALSE;
		}
		else {
			$can_mod=checkPerm('mod', true);
			$can_del=checkPerm('del', true);
		}

		$tot=$this->found_rows;

		if ($can_mod) {
			if (($arrData["ord"] < $tot) || ($arrData["ord"] == 0))  // Move Down Button:
				$arrData["movedown"]=
					'<input type="submit" class="OrgDown" value="" name="'
					.$this->_getOpMoveDownId().$arrData['idContent'] .'" title="'.$lang->def("_DOWN").'" id="select_'. $arrData['idContent'] .'" />';
			else
				$arrData["movedown"]="&nbsp;";

			if ($arrData["ord"] > 1) // Move Up Button:
				$arrData["moveup"]=
					'<input type="submit" class="OrgUp" value="" name="'
					.$this->_getOpMoveUpId().$arrData['idContent'] .'" title="'.$lang->def("_UP").'" id="select_'. $arrData['idContent'] .'" />';
			else
				$arrData["moveup"]="&nbsp;";

			if(!$arrData['publish'])
				$arrData['publish'] = '<input type="submit" class="publishbtn" value="" name="'
						.$this->_getOpPublishId().$arrData['idContent'] .'" title="'.$lang->def("_PUBLISH").'" id="select_'. $arrData['idContent'] .'" />';
			else
				$arrData['publish'] = '<input type="submit" class="unpublishbtn" value="" name="'
						.$this->_getOpUnPublishId().$arrData['idContent'] .'" title="'.$lang->def("_UNPUBLISH").'" id="select_'. $arrData['idContent'] .'" />';

			$arrData['modify'] = '<input type="submit" class="TVActionEdit" value="" name="'
					.$this->_getOpModifyId().$arrData['idContent'] .'" id="select_'. $arrData['idContent'] .'" />';

			$arrData['move'] = '<input type="submit" class="TVActionMove" value="" name="'
					.$this->_getOpMoveFormId().$arrData['idContent'] .'" title="'.$lang->def("_ALT_MOVE").'" id="select_'. $arrData['idContent'] .'" />';
		}
		else {
			$arrData["moveup"]="&nbsp;";
			$arrData["movedown"]="&nbsp;";
			$arrData['move']="&nbsp;";
			$arrData['modify']="&nbsp;";
			$arrData['publish']="&nbsp;";
		}

		if ($can_del) {
			$arrData['remove'] = '<input type="submit" class="TVActionDelete" value="" name="'
					.$this->_getOpRemoveId().$arrData['idContent'] .'" id="select_'. $arrData['idContent'] .'" />';
		}
		else {
			$arrData['remove']="&nbsp;";
		}

		$arrData['publish_date']=$GLOBALS["regset"]->databaseToRegional($arrData['publish_date']);

		if ($this->sel_mode) {

			$block_id=(int)importVar("block_id");
			$sub_id=(int)importVar("sub_id");

			$url ="index.php?modname=manpage&amp;op=modblock&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=".$sub_id;
			$url.="&amp;blk_op=additem&amp;type=content&amp;item_id=".$arrData['idContent'];
			$img='<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def("_ATTACHMENT").'" title="'.$lang->def("_ATTACHMENT").'" />';
			$arrData["attach"]="<a href=\"".$url."\">".$img."</a>\n";
		}

		return $arrData;
	}
}

/**
 * Customizaton of ListView class
 **/
class Content_ListView extends ListView {

	var $new_perm;
	var $mod_perm;
	var $rem_perm;
	var $id;
	var $sel_mode=false;

	function _getOpCreateItemId() { return 'do_addcontent_'; }

	function Content_ListView( $title, &$data, &$rend, $id ) {
		parent::ListView( $title, $data, $rend, $id );
		$this->new_perm = checkPerm('add', true);
		$this->mod_perm = checkPerm('mod', true);
		$this->rem_perm = checkPerm('del', true);

	}

	// overload for _getAddLabel operation

	function _getAddAlt() { return def("_ADD", "standard", "framewor"); }
	function _getAddLabel() { return def("_ADD", "standard", "framewor"); }
	function _getAddImage() { return getPathImage().'standard/add.gif'; }
	function _getAddUrl() { return $this->_getOpCreateItemId(); }

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
		return 'index.php?modname=content&amp;op=content' /*&amp;ord='
				.$this->_getOrd() */
				.'&amp;ini=';
	}

	function _getRowsPage() { return 20; }

	// overload
	function _getCols() {

		$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

		$colInfos = array();
		if (!$this->sel_mode) {
		}
		$colInfos[] = $this->_createColInfo( $lang->def("_PUBDATE"),'','','publish_date',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_TITLE"),'','','title',true, false );
		$colInfos[] = $this->_createColInfo( $lang->def("_LANGUAGE"),'','','language',true, false );


		if (!$this->sel_mode) {

			$colInfos[] = $this->_createColInfo(
			'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def("_DOWN").'" title="'.$lang->def("_DOWN").'" />',
			'image','image','movedown', $this->mod_perm , false );

			$colInfos[] = $this->_createColInfo(
			'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def("_UP").'" title="'.$lang->def("_UP").'" />',
			'image','image','moveup', $this->mod_perm , false );

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
class contentDb extends TreeDb {

	function contentDb() {

		$this->table = $GLOBALS["prefix_cms"] . '_content_dir';
		$this->fields = array( 'id' => 'id', 'idParent' => 'idParent', 'path' => 'path', 'lev' => 'lev' );
	}

}

class Content_TreeView extends TreeView {

	var $idContent;
	var $showbtn=0;
	var $show_icons=1;

	var $show_icons_for_item =array(); //&

	var $use_admin_filter=FALSE; //&
	var $node_perm=FALSE;


 	function _getOpCreateItemId() { return 'do_addcontent_'; }
	function _getOpModifyId() { return 'do_modcontent_'.$this->idContent; }
	function _ma_getOpAttachId() { return 'do_attachitem_'.$this->idContent; }
	function _getOpMoveFormId() { return 'do_movecontent_form_'.$this->idContent; }
	function _getOpMoveContentId() { return 'do_movecontent_'.$this->idContent; }
	function _getOpPublishId() { return 'do_publishcontent_'.$this->idContent; }
	function _getOpUnPublishId() { return 'do_unpublishcontent_'.$this->idContent; }
	function _getOpRemoveId() { return 'do_remcontent_'.$this->idContent; }
	function _getCancelId() 		{ return 'do_treeview_cancel_'		.$this->idContent; }
	function _getOpMoveDownId() { return 'do_movedowncontent_'.$this->idContent; }
	function _getOpMoveUpId() { return 'do_moveupcontent_'.$this->idContent; }

	function getContentSelected() { return $this->idContent; }

	function _getAddImage() { return getPathImage().'treeview/folder_new.png'; }
	function _getCreateImage() { return getPathImage().'treeview/folder_new.png'; }

	function _getAddLabel() { return def("_NEWHOMEFOLDER", "admin_content", "cms"); }
	function _getAddAlt() { return def("_NEWHOMEFOLDER", "admin_content", "cms"); }
	function _getCreateLabel() { return def("_NEWHOMEFOLDER", "admin_content", "cms"); }
	function _getCreateAlt() { return def("_NEWHOMEFOLDER", "admin_content", "cms"); }
	function _getMoveContentTargetLabel()  { return def("_NEWCONTENTFOLDER", "admin_content", "cms"); }


	function _getOpDelTitle() { return def("_CONTENTDELF", "admin_content", "cms"); }
	function _getOpDelId() { return 'opdeletefolder_content_'.$this->id; }
	function _getOpMoveTitle() { return def("_CONTENTMOVEF", "admin_content", "cms"); }
	function _getOpMoveId() { return 'opmovefolder_content_'.$this->id; }
	function _getOpRenTitle() { return def("_CONTENTRENF", "admin_content", "cms"); }
	function _getOpRenId() { return 'oprenamefolder_content_'.$this->id; }

	function _getFolderNameLabel() { return def("_CONTENTFNAME", "admin_content", "cms");}
	function _getCreateFolderId() { return 'treeview_create_folder_'	.$this->id; }
	function _getMyRenameFolderId() { return 'do_treeview_rename_folder_'	.$this->id; }
	function _getMoveFolderId() 	{ return 'treeview_move_folder_'	.$this->id; }
	function _getDeleteFolderId() 	{ return 'treeview_delete_folder_'	.$this->id; }

	function canAdd() { return checkPerm('add', true); }
	function canDelete() { return FALSE; }
	function canRename() { return FALSE; }
	function canMove() { return FALSE; }

	function canInlineRename()  { return ($this->showbtn && checkPerm('mod', true)); }
	function canInlineMove()  { return ($this->showbtn && checkPerm('mod', true)); }

	function canInlineMoveItem( &$stack, $level ) {
		$folder_id =$stack[$level]['folder']->id; //&
		if ((isset($this->show_icons_for_item[$folder_id])) && (($this->show_icons_for_item[$folder_id] === FALSE)))
			return FALSE;
		if (($level == 0) || (!$this->show_icons))
			return FALSE;
		return TRUE;
	}
	function canInlineRenameItem( &$stack, $level ) {
		$folder_id =$stack[$level]['folder']->id; //&
		if ((isset($this->show_icons_for_item[$folder_id])) && (($this->show_icons_for_item[$folder_id] === FALSE)))
			return FALSE;
		if (($level == 0) || (!$this->show_icons))
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

			$ctp=new CmsTreePermissions("content");

			$node_perm=$ctp->loadAllNodePerm($user_id, TRUE);
			$node_perm=$node_perm["all"];
			$this->setNodePerm($user_id, $node_perm);
		}

		$res=(in_array((int)$folder_id, $node_perm) ? TRUE : FALSE);
		return $res;
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


	   if((isset($this->pathToExpand)) && ($this->pathToExpand != NULL)) {
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
				$lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$lang];
				if( trim($folderName) != "" ) {
					$this->tdb->addFolderById( $this->selectedFolder, $folderName );
					list($idContent)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idContent, $arr);

					content_appendNewNodePerm($idContent); //&

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getMyRenameFolderId() ) ) { // rinomina cartella e titoli lingue
				$lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$lang];
				if ($folderName != "") {
					$idContent=$this->selectedFolder;
					$folder = $this->tdb->getFolderById( $idContent );
					$folder_path =$folder->getFolderPath(); // Missing "/root" prefix on rename fix //&
					if (substr($folder_path, 0, 5) !== "/root") {
						$folder->setFolderPath("/root".$folder_path);
					}
					$this->tdb->renameFolder( $folder, $folderName );

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idContent, $arr);

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getDeleteFolderId() ) ) { // rimuove cartella e titoli lingue
				$q=mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_content_titles WHERE iddir='".$this->selectedFolder."';");
				// Nota: la cartella viene rimossa dalla parent!
				$this->refresh = true;
			}
		}
		parent::extendedParsing( $arrayState, $arrayExpand, $arrayCompress );
	}


	function parsePositionData( $arrayState, $arrayExpand, $arrayCompress ) {


		require_once($GLOBALS["where_cms"]."/lib/admin_common.php");

		foreach( $_POST as $nameField => $valueField ) {

			if( strstr( $nameField, $this->_getOpModifyId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModifyId()));
				$this->op = 'modcontent';
				$this->idContent = $id;
			}
			else if( strstr( $nameField, $this->_ma_getOpAttachId() ) ) {
				$id = substr( $nameField, strlen($this->_ma_getOpAttachId()));
				$this->op = 'attachitem';
				$this->idContent = $id;
			}
			else if( strstr( $nameField, $this->_getOpRemoveId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpRemoveId()));
				$this->op = 'delcontent';
				$this->idContent = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveFormId() ) ) { // Sposta content (mostra form)
				$id = substr( $nameField, strlen($this->_getOpMoveFormId()));
				$this->op = 'content_move_form';
				$this->idContent = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveContentId() ) ) { // Sposta content (ora!)
				$id = substr( $nameField, strlen($this->_getOpMoveContentId()));
				$this->op = '';
				$this->idContent = $id;
				$this->folder_id = $_POST["folder_id"];
				movecontent($this);
			}
			else if( strstr( $nameField, $this->_getOpPublishId() ) ) { // Publish
				$id = substr( $nameField, strlen($this->_getOpPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_content SET publish='1' WHERE idContent='$id';");
				if (!$q) echo ("<script>alert('Error: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpUnPublishId() ) ) { // Un-Publish
				$id = substr( $nameField, strlen($this->_getOpUnPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_content SET publish='0' WHERE idContent='$id';");
				if (!$q) echo ("<script>alert('Error: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpRenameFolderId() ) ) { // Rinomina cartella
				$id = substr( $nameField, strlen($this->_getOpRenameFolderId()));
				$this->idFolder=$id;
				$this->op = 'renamefolder';
			}
			else if( strstr( $nameField, $this->_getOpMoveDownId() ) ) { // Sposta in basso
				$id = substr( $nameField, strlen($this->_getOpMoveDownId()));
				change_item_order("content", "down", $id);
			}
			else if( strstr( $nameField, $this->_getOpMoveUpId() ) ) { // Sposta in alto
				$id = substr( $nameField, strlen($this->_getOpMoveUpId()));
				change_item_order("content", "up", $id);
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

		$elem="";
		$can_access_to_node =TRUE; //&


		$folder_id =$stack[$level]['folder']->id; //&
		if (!$this->canAccessToNode($GLOBALS["current_user"]->getIdSt(), $folder_id)) {
			$this->show_icons_for_item[$folder_id] =FALSE;
			$can_access_to_node =FALSE;
		}

		if (($this->showbtn) && ($can_access_to_node)) { //&
			if( $level > 0 ) {

				$arrData = $stack[$level]['folder']->otherValues;
				$content_cnt=count_child($stack[$level]['folder']->id);
				$nochild=$stack[$level]['isLeaf'];
				if( checkPerm('del', TRUE ) && $nochild && $content_cnt == 0) {
					$elem.='<input type="submit" class="OrgDelFolder" value="" name="'
						.$this->_getOpDeleteFolderId().$stack[$level]['folder']->id .'"'
						.' title="'.$this->_getDeleteLabel().'" />';
				}
				else {
					$elem.='<input type="submit" class="OrgPlay" value="" name="'
						.$this->_getCancelId().'"'
						.' title="" />';
				}
			}
		}

		$elem.=parent::printElement($stack, $level);
		return $elem;
	}


	function loadNewFolder() {
		$out=& $GLOBALS['page'];
		$lang=& DoceboLanguage::createInstance('admin_content', 'cms');
		$out->setWorkingZone('content');

		$larr=$GLOBALS['globLangManager']->getAllLangCode();

		$back_ui_url="index.php?modname=content&amp;op=content";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
		$title_arr[]=$lang->def("_NEWHOMEFOLDER");
		$out->add(getTitleArea($title_arr, "content"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$out->add($this->printState());

		foreach ($larr as $key=>$cl) {
			$out->add('<label for="'.$this->_getFolderNameId().'_'.$cl.'">'.$this->_getFolderNameLabel().': </label>');
			$out->add(' <input type="text" value="" name="'.$this->_getFolderNameId()
				.'_'.$cl.'" id="'.$this->_getFolderNameId().'_'.$cl.'" /> ('.$cl.')<br />');
		}

		$out->add(' <br /><img src="'.$this->_getCreateImage().'" alt="'.$this->_getCreateAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCreateLabel().'"'
			.' name="'.$this->_getCreateFolderId().'" id="'.$this->_getCreateFolderId().'" />');
		$out->add(' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />');

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
		$out->add("</div>\n");
	}


	function loadRenameFolder() {

		$out=& $GLOBALS['page'];
		$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_content_titles WHERE iddir='".$this->getSelectedFolderId()."';";
		$q=mysql_query($qtxt);

		$lval=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$lval[$row["lang"]]=$row["title"];
			}
		}


		$back_ui_url="index.php?modname=content&amp;op=content";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
		$title_arr[]=$lang->def("_RENAME_FOLDER").": ".$lval[getLanguage()];
		$out->add(getTitleArea($title_arr, "content"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$out->add($this->printState());

		$larr=$GLOBALS['globLangManager']->getAllLangCode();

		foreach ($larr as $key=>$cl) {
			$out->add('<label for="'.$this->_getFolderNameId().'_'.$cl.'">'.$this->_getFolderNameLabel().': </label>');
			$out->add(' <input type="text" name="'.$this->_getFolderNameId()
				.'_'.$cl.'" id="'.$this->_getFolderNameId().'_'.$cl.'" '
				.'value="'.$lval[$cl].'" /> ('.$cl.')<br />'."\n");
		}

		$out->add(' <br /><img src="'.$this->_getRenameImage().'" alt="'.$this->_getRenameAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getRenameLabel().'"'
			.' name="'.$this->_getMyRenameFolderId().'" id="'.$this->_getMyRenameFolderId().'" />');
		$out->add(' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />');

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
		$out->add("</div>\n");
	}

	function setSelMode($value) {
		$listView=& $this->getListView();
		$data=& $listView->getDataRetrivier();
		$listView->sel_mode=(bool)$value;
		$data->sel_mode=(bool)$value;
	}

	function loadState() {
		if( isset($_SESSION['content_tree_state']) )
			$this->setState( unserialize(stripslashes($_SESSION['content_tree_state'])));
	}

	function saveState() {
		$_SESSION['content_tree_state'] = addslashes(serialize($this->getState()));
	}


	function setSelectedFolderId($folder_id) { //&
		$this->selectedFolder=$folder_id;
	}


}


function count_child($id) {


	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_content WHERE idFolder='$id';";
	$q=mysql_query($qtxt);

	if ($q) {
		$res=mysql_num_rows($q);
	}


	return $res;
}


function save_cat_lang($id, $arr) {


	$db_arr=array();
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_content_titles WHERE iddir='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_arr[]=$row["lang"];
		}
	}


	foreach ($arr as $key=>$val) {
		if (in_array($key, $db_arr)) { // Aggiorno
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_content_titles SET title='$val' WHERE iddir='$id' AND lang='$key';";
			$q=mysql_query($qtxt);
		}
		else { // Inserisco
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_content_titles (iddir, lang, title) VALUES('$id','$key','$val');";
			$q=mysql_query($qtxt);
		}
	}
}


function content_getOp( &$treeView ) {
	$op = $treeView->op;

	if( $op == "" ) {
		$listView = & $treeView->getListView();
		if( $listView !== NULL )
		 	$op = $listView->op;
	}

	return $op;
}

function content_addfolder( &$treeView ) {
	checkPerm('add');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	content_checkTreePerm($idFolder);

	$treeView->loadNewFolder();
}


function content_renamefolder( &$treeView ) {
	checkPerm('mod');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	content_checkTreePerm($idFolder);

	$treeView->loadRenameFolder();
}

function content_move_form(&$tree) {
	checkPerm('mod');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');


	$back_ui_url="index.php?modname=content&amp;op=content";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
	$title_arr[]=$lang->def("_MOVE_CONTENT");
	$out->add(getTitleArea($title_arr, "content"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	if( isset($_POST["idContent"]) )
		$content_id=$_POST["idContent"];
	else
		$content_id=$tree->idContent;

	if( isset($_POST[$tree->_getFolderNameId()]) )
		$folderid = $_POST[$tree->_getFolderNameId()];
	else
		$folderid = $tree->getSelectedFolderId();

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );
	$out->add('<input type="hidden" value="" name="'.$tree->_getOpMoveFormId().'" />');
	$out->add('<input type="hidden" value="'.$folderid.'" name="'.$tree->_getFolderNameId().'" />');
	$out->add('<input type="hidden" value="'.$tree->getSelectedFolderId().'" name="folder_id" />');
	$out->add('<input type="hidden" value="'.$content_id.'" name="idContent" />');
	$out->add('<div>'.$tree->_getMoveContentTargetLabel()." ".$tree->getFolderPrintName($folder).'</div>');
	$out->add($tree->load());
	$out->add(' <img src="'.$tree->_getMoveImage().'" alt="'.$tree->_getMoveAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$lang->def("_ALT_MOVE").'"'
		.' name="'.$tree->_getOpMoveContentId().$content_id.'" id="'.$tree->_getOpMoveContentId().$content_id.'" />');
	$out->add(' <img src="'.$tree->_getCancelImage().'" alt="'.$tree->_getCancelAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$tree->_getCancelLabel().'"'
		.' name="'.$tree->_getCancelId().'" id="'.$tree->_getCancelId().'" />');

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add('</div>');

}

function content_move_folder( &$treeView ) {
	checkPerm('mod');

	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	content_checkTreePerm($idFolder);

	$res="";
	$back_ui_url="index.php?modname=content&amp;op=content";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
	$title_arr[]=$lang->def("_MOVE_FOLDER");
	$res.=getTitleArea($title_arr, "content");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.=$treeView->loadMoveFolder();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$GLOBALS['page']->add($res);
}

function content_deletefolder( &$treeView ) {
	checkPerm('del');

	$lang=& DoceboLanguage::createInstance('admin_content', 'cms');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	content_checkTreePerm($idFolder);

	$res="";
	$back_ui_url="index.php?modname=content&amp;op=content";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TEXTOF");
	$title_arr[]=$lang->def("_DELETE_FOLDER");
	$res.=getTitleArea($title_arr, "content");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.=$treeView->loadDeleteFolder();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$GLOBALS['page']->add($res);
}

function movecontent($tree) {
	checkPerm('mod');

	$idContent=$tree->idContent;
	$folder_id=$tree->folder_id;

	content_checkTreePerm($folder_id); //&

	$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_content SET idFolder='$folder_id' WHERE idContent='$idContent' LIMIT 1;");

	if (!$q) $GLOBALS['page']->add(mysql_error());
}


function content_appendNewNodePerm($folder_id) { //&
	$res =TRUE;

	$user_level=$GLOBALS["current_user"]->getUserLevelId();
	if ($user_level != ADMIN_GROUP_GODADMIN) {

		require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

		$ctp=new CmsTreePermissions("content");
		$res =$ctp->appendNewNodePerm($GLOBALS["current_user"]->getIdSt(), $folder_id);
	}

	return $res;
}


function &createTreeView( $withContents = TRUE, $multiSelect = FALSE, $withActions = FALSE, $sel_path=FALSE ) {

	$dirDb = new contentDb();
	$treeView = new Content_TreeView( $dirDb, 'content' );
	$treeView->loadState();
	$treeView->parsePositionData( $_POST, $_POST, $_POST );
	$treeView->saveState();

	$dataRetriever = new Content_DataRetriever( NULL, $GLOBALS["prefix_cms"] );
	$typeOneRenderer = new typeOne(20);
	$listView = new Content_ListView( '', $dataRetriever, $typeOneRenderer, 'idContent');

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
