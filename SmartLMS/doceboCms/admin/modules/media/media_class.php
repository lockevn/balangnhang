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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly!');

require_once($GLOBALS['where_framework']."/lib/lib.listview.php");
require_once($GLOBALS['where_framework']."/lib/lib.treedb.php");
require_once($GLOBALS['where_framework']."/lib/lib.treeview.php");
require_once($GLOBALS['where_cms']."/lib/lib.manModules.php");

/**
 * Customization of DataRetriever for _homerepo table
 **/

class Media_DataRetriever extends DataRetriever {
	// id of selected folder in _media_dir (TreeView)
	// used in query composition to filter items
	var $idFolder = 0;
	var $sel_mode=false;
	var $sel_mode_url=null;
	var $total_rows=0;

	var $tiny_popup = false;
	var $tiny_url = '';

	var $use_admin_filter=FALSE; //&
	var $node_perm=FALSE;

	function _getOpModifyId() { return 'do_modmedia_'; }
	function _getOpMoveFormId() { return 'do_movemedia_form_'; }
	function _getOpMoveMediaId() { return 'do_movemedia_'; }
	function _getOpPublishId() { return 'do_publishmedia_'; }
	function _getOpUnPublishId() { return 'do_unpublishmedia_'; }
	function _getOpRemoveId() { return 'do_remmedia_'; }
	function _getCancelId() 		{ return 'do_treeview_cancel_'; }


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

			$ctp=new CmsTreePermissions("media");

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

		$t1=$this->prefix."_media";
		$t2=$this->prefix."_media_info";
		$lang=getLanguage();
		$query = "SELECT idMedia, idFolder, publish_date, fname, real_fname, fpreview, media_url, important, publish, sdesc "
			." FROM $t1"
			." LEFT JOIN $t2 ON ($t2.idm=$t1.idMedia AND $t2.lang='$lang')"
			." WHERE idFolder='". (int)$this->idFolder . "'";
		//echo $query;
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

		$lang=& DoceboLanguage::createInstance('admin_media', 'cms');


		$folder_id =(int)$arrData["idFolder"]; //&
		if (!$this->canAccessToNode($GLOBALS["current_user"]->getIdSt(), $folder_id)) {
			$can_mod =FALSE;
			$can_del =FALSE;
		}
		else {
			$can_mod=checkPerm('mod', true);
			$can_del=checkPerm('del', true);
		}

		include_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");
		include_once($GLOBALS["where_framework"]."/lib/lib.multimedia.php");

		if($arrData["fpreview"] != "") {
			$img="<img src=\""._PPATH.$arrData["fpreview"]."\" width=\"80\" alt=\"\" />\n";
			$arrData["fpreview"]=$img;
		}
		else {
			$arrData["fpreview"]="&nbsp;";
		}

		if (isYouTube($arrData["media_url"])) {
			$arrData['fname'] =$arrData["media_url"];
		}

		// Tipo:
		$fn=$arrData["real_fname"];
		$expFileName = explode('.', $fn);
		$totPart = count($expFileName) - 1;
		$media_url =$arrData["media_url"];
		if (empty($media_url)) {
			$mime=mimetype($expFileName[$totPart]);
			$img="<img src=\"".getPathImage().mimeDetect($fn)."\" alt=\"$mime\" title=\"$mime\" />\n";
			$arrData["type"]=$img;
		}
		else {
			$title =$lang->def("_STREAMING_MEDIA");
			$img ="<img src=\"".getPathImage('fw')."standard/network.png\" ";
			$img.="alt=\"".$title."\" title=\"$title\" />\n";
			$arrData["type"]=$img;
		}

		if(!isset($arrData["sdesc"])) $arrData["sdesc"]="&nbsp;";

		if ($can_mod) {

			if(!$arrData['publish'])
				$arrData['publish'] = '<input type="submit" class="publishbtn" value="" name="'
						.$this->_getOpPublishId().$arrData['idMedia'] .'" title="'.$lang->def("_PUBLISH").'" id="select_'. $arrData['idMedia'] .'" />';
			else
				$arrData['publish'] = '<input type="submit" class="unpublishbtn" value="" name="'
						.$this->_getOpUnPublishId().$arrData['idMedia'] .'" title="'.$lang->def("_UNPUBLISH").'" id="select_'. $arrData['idMedia'] .'" />';

			$arrData['modify'] = '<input type="submit" class="TVActionEdit" value="" name="'
					.$this->_getOpModifyId().$arrData['idMedia'] .'" id="select_'. $arrData['idMedia'] .'" />';

			$arrData['move'] = '<input type="submit" class="TVActionMove" value="" name="'
					.$this->_getOpMoveFormId().$arrData['idMedia'] .'" title="'.$lang->def("_ALT_MOVE").'" id="select_'. $arrData['idMedia'] .'" />';
		}
		else {
			$arrData['move']="&nbsp;";
			$arrData['modify']="&nbsp;";
			$arrData['publish']="&nbsp;";
		}

		if ($can_del) {
			$arrData['remove'] = '<input type="submit" class="TVActionDelete" value="" name="'
					.$this->_getOpRemoveId().$arrData['idMedia'] .'" id="select_'. $arrData['idMedia'] .'" />';
		}
		else {
			$arrData['remove']="&nbsp;";
		}

		$arrData['publish_date']=$GLOBALS["regset"]->databaseToRegional($arrData['publish_date']);

		if ($this->sel_mode) {

			$block_id=(int)importVar("block_id");
			$sub_id=(int)importVar("sub_id");

			if ((!isset($this->sel_mode_url)) || (empty($this->sel_mode_url))) {
				$url ="index.php?modname=manpage&amp;op=modblock&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=".$sub_id;
				$url.="&amp;blk_op=additem&amp;type=media&amp;item_id=".$arrData['idMedia'];
			}
			else {
				if (!empty($arrData["media_url"])) {
					if (isYouTube($arrData["media_url"])) {
						$fname =$arrData["media_url"];
					}
					else {
						$media_name =basename($arrData["media_url"]);
						$media_name =(strpos($media_name, "?") !== FALSE ? preg_replace("/(\?.*)/", "", $media_name) : $media_name);
					}
				}
				else {
					$media_name =$arrData["real_fname"];
				}

				$type ="";
				if ($this->getShowType()) {
					$type ="&amp;type=";
					if (!empty($arrData["media_url"])) {
						$type.=getMediaType($arrData["media_url"]);
					}
					else {
						$type.=getMediaType($media_name);
					}
				}
				$url=$this->sel_mode_url.$type."&amp;item_id=".$arrData['idMedia'];
			}

			$img='<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def("_ATTACHMENT").'" title="'.$lang->def("_ATTACHMENT").'" />';
			$arrData["attach"]="<a href=\"".$url."\""
				.( $this->tiny_popup 
					? ' onclick="FileBrowserDialogue.mySubmit(\''.$this->tiny_url.$arrData['real_fname'].'\'); return false;"'
					: '' )
				.">"
				.$img."</a>\n";
		}

		return $arrData;
	}

	function getShowType() {
		return $this->show_type;
	}
}

/**
 * Customizaton of ListView class for homerepo
 **/
class Media_ListView extends ListView {

	var $new_perm;
	var $mod_perm;
	var $rem_perm;
	var $sel_mode=false;

	function _getOpAddId() { return 'do_addmedia_'; }


	function Media_ListView( $title, &$data, &$rend, $id ) {
		parent::ListView( $title, $data, $rend, $id );
		$this->new_perm = checkPerm('add', true, "media", "cms");
		$this->mod_perm = checkPerm('mod', true, "media", "cms");
		$this->rem_perm = checkPerm('del', true, "media", "cms");

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
		return 'index.php?modname=media&amp;op=media' /*&amp;ord='
				.$this->_getOrd() */
				.'&amp;ini=';
	}

	function _getRowsPage() { return 20; }

	// overload
	function _getCols() {

		$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

		$colInfos = array();
		if (!$this->sel_mode) {
			$colInfos[] = $this->_createColInfo( $lang->def("_PUBDATE"),'','','publish_date',true, false );
		}
		$colInfos[] = $this->_createColInfo( $lang->def("_PREVIEW"),'image','align_center','fpreview',true, false );
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
class mediaDb extends TreeDb {

	function mediaDb() {

		$this->table = $GLOBALS["prefix_cms"] . '_media_dir';
		$this->fields = array( 'id' => 'id', 'idParent' => 'idParent', 'path' => 'path', 'lev' => 'lev' );
	}

}

class Media_TreeView extends TreeView {

	var $idMedia;
	var $showbtn=0;
	var $show_icons_for_item =array(); //&

	var $use_admin_filter=FALSE; //&
	var $node_perm=FALSE;

	function _getOpAddId() { return 'do_addmedia_'; }
	function _getOpModifyId() { return 'do_modmedia_'; }
	function _getOpMoveFormId() { return 'do_movemedia_form_'.$this->idMedia; }
	function _getOpMoveMediaId() { return 'do_movemedia_'.$this->idMedia; }
	function _getOpPublishId() { return 'do_publishmedia_'.$this->idMedia; }
	function _getOpUnPublishId() { return 'do_unpublishmedia_'.$this->idMedia; }
	function _getOpRemoveId() { return 'do_remmedia_'; }
	function _getCancelId() 		{ return 'do_treeview_cancel_'		.$this->idMedia; }

	function getMediaSelected() { return $this->idMedia; }

	function _getAddImage() { return getPathImage().'treeview/folder_new.png'; }
	function _getCreateImage() { return getPathImage().'treeview/folder_new.png'; }

	function _getAddLabel() { return def("_NEWHOMEFOLDER", "admin_media", "cms"); }
	function _getAddAlt() { return def("_NEWHOMEFOLDER", "admin_media", "cms"); }
	function _getCreateLabel() { return def("_NEWHOMEFOLDER", "admin_media", "cms"); }
	function _getCreateAlt() { return def("_NEWHOMEFOLDER", "admin_media", "cms"); }
	function _getMoveMediaTargetLabel()  { return def("_NEWMEDIAFOLDER", "admin_media", "cms"); }


	function _getOpDelTitle() { return def("_MEDIADELF", "admin_media", "cms"); }
	function _getOpDelId() { return 'do_opdeletefolder_media_'.$this->id; }
	function _getOpMoveTitle() { return def("_MEDIAMOVEF", "admin_media", "cms"); }
	function _getOpMoveId() { return 'do_opmovefolder_media_'.$this->id; }
	function _getOpRenTitle() { return def("_MEDIARENF", "admin_media", "cms"); }
	function _getOpRenId() { return 'do_oprenamefolder_media_'.$this->id; }

	function _getFolderNameLabel() { return def("_MEDIAFNAME", "admin_media", "cms");}
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

			$ctp=new CmsTreePermissions("media");

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


		foreach( $_POST as $nameField => $valueField ) {
			// create folder
			if( strstr( $nameField, $this->_getCreateFolderId() ) ) { // create folder
				$lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$lang];
				if( trim($folderName) != "" ) {
					$this->tdb->addFolderById( $this->selectedFolder, $folderName );
					list($idMedia)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idMedia, $arr);

					media_appendNewNodePerm($idMedia); //&

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getMyRenameFolderId() ) ) { // rinomina cartella e titoli lingue
				$lang=getLanguage();
				$folderName=$arrayState[$this->_getFolderNameId()."_".$lang];
				if ($folderName != "") {
					$idMedia=$this->selectedFolder;
					$folder = $this->tdb->getFolderById( $idMedia );
					$folder_path =$folder->getFolderPath(); // Missing "/root" prefix on rename fix //&
					if (substr($folder_path, 0, 5) !== "/root") {
						$folder->setFolderPath("/root".$folder_path);
					}
					$this->tdb->renameFolder( $folder, $folderName );

					$larr=$GLOBALS['globLangManager']->getAllLangCode();
					foreach ($larr as $key=>$val) {
						$arr[$val]=$arrayState[$this->_getFolderNameId()."_".$val];
					}
					save_cat_lang($idMedia, $arr);

					$this->refresh = true;
				}
			}
			else if( strstr( $nameField, $this->_getDeleteFolderId() ) ) { // rimuove cartella e titoli lingue
				$q=mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_media_titles WHERE iddir='".$this->selectedFolder."';");
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
				$this->op = 'addmedia';
				$this->idMedia = $id;
			}
			else if( strstr( $nameField, $this->_getOpModifyId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModifyId()));
				$this->op = 'modmedia';
				$this->idMedia = $id;
			}
			else if( strstr( $nameField, $this->_getOpRemoveId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpRemoveId()));
				$this->op = 'delmedia';
				$this->idMedia = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveFormId() ) ) { // Sposta media (mostra form)
				$id = substr( $nameField, strlen($this->_getOpMoveFormId()));
				$this->op = 'media_move_form';
				$this->idMedia = $id;
			}
			else if( strstr( $nameField, $this->_getOpMoveMediaId() ) ) { // Sposta media (ora!)
				$id = substr( $nameField, strlen($this->_getOpMoveMediaId()));
				$this->op = '';
				$this->idMedia = $id;
				$this->folder_id = $_POST["folder_id"];
				movemedia($this);
			}
			else if( strstr( $nameField, $this->_getOpPublishId() ) ) { // Publish
				$id = substr( $nameField, strlen($this->_getOpPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_media SET publish='1' WHERE idMedia='$id';");
				if (!$q) echo ("<script>alert('Errore: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpUnPublishId() ) ) { // Un-Publish
				$id = substr( $nameField, strlen($this->_getOpUnPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_media SET publish='0' WHERE idMedia='$id';");
				if (!$q) echo ("<script>alert('Errore: ".mysql_error()."');</script>");
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
				$media_cnt=count_child($stack[$level]['folder']->id);
				$nochild=$stack[$level]['isLeaf'];
				if( checkPerm('del', true) && $nochild && $media_cnt == 0) {
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
		$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
		$out->setWorkingZone('content');

		$back_ui_url="index.php?modname=media&amp;op=media";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_MEDIA");
		$title_arr[]=$lang->def("_NEWHOMEFOLDER");
		$out->add(getTitleArea($title_arr, "media"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$out->add($this->printState());

		$larr=$GLOBALS['globLangManager']->getAllLangCode();

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
		$lang=& DoceboLanguage::createInstance('admin_media', 'cms');
		$out->setWorkingZone('content');


		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media_titles WHERE iddir='".$this->getSelectedFolderId()."';";
		$q=mysql_query($qtxt);

		$lval=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$lval[$row["lang"]]=$row["title"];
			}
		}

		$back_ui_url="index.php?modname=media&amp;op=media";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_MEDIA");
		$title_arr[]=$lang->def("_RENAME_FOLDER").": ".$lval[getLanguage()];
		$out->add(getTitleArea($title_arr, "media"));
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

	function setSelModeUrl($value, $show_type=FALSE) {
		$listView=& $this->getListView();
		$data=& $listView->getDataRetrivier();
		$data->sel_mode_url=$value;
		$this->setShowType($show_type);
	}


	function setTiny($tiny_url) {
		$listView=& $this->getListView();
		$data=& $listView->getDataRetrivier();
		$data->tiny_popup = true;
		$data->tiny_url = $tiny_url;
	}

	function setShowType($val) {
		$listView=& $this->getListView();
		$data=& $listView->getDataRetrivier();
		$data->show_type=$val;
	}


	function loadState() {
		if( isset($_SESSION['media_tree_state']) )
			$this->setState( unserialize(stripslashes($_SESSION['media_tree_state'])));
	}

	function saveState() {
		$_SESSION['media_tree_state'] = addslashes(serialize($this->getState()));
	}

	function setSelectedFolderId($folder_id) { //&
		$this->selectedFolder=$folder_id;
	}

}


function count_child($id) {


	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media WHERE idFolder='$id';";
	$q=mysql_query($qtxt);

	if ($q) {
		$res=mysql_num_rows($q);
	}


	return $res;
}


function save_cat_lang($id, $arr) {


	$db_arr=array();
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_media_titles WHERE iddir='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$db_arr[]=$row["lang"];
		}
	}


	foreach ($arr as $key=>$val) {
		if (in_array($key, $db_arr)) { // Aggiorno
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_media_titles SET title='$val' WHERE iddir='$id' AND lang='$key';";
			$q=mysql_query($qtxt);
		}
		else { // Inserisco
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_media_titles (iddir, lang, title) VALUES('$id','$key','$val');";
			$q=mysql_query($qtxt);
		}
	}
}


function media_getOp( &$treeView ) {
	$op = $treeView->op;

	if( $op == "" ) {
		$listView = $treeView->getListView();
		if( $listView !== NULL )
		 	$op = $listView->op;
	}

	return $op;
}

function media_addfolder( &$treeView ) {
	checkPerm('add');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	media_checkTreePerm($idFolder);

	$treeView->loadNewFolder();
}


function media_renamefolder( &$treeView ) {
	checkPerm('mod');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	media_checkTreePerm($idFolder);

	$treeView->loadRenameFolder();
}

function media_move_form(&$tree) {
	checkPerm('mod');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	$back_ui_url="index.php?modname=media&amp;op=media";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MEDIA");
	$title_arr[]=$lang->def("_MOVE_MEDIA");
	$out->add(getTitleArea($title_arr, "media"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	if( isset($_POST["idMedia"]) )
		$media_id=$_POST["idMedia"];
	else
		$media_id=$tree->idMedia;

	if( isset($_POST[$tree->_getFolderNameId()]) )
		$folderid = $_POST[$tree->_getFolderNameId()];
	else
		$folderid = $tree->getSelectedFolderId();

	media_checkTreePerm($folderid); //&

	$folder=$tree->tdb->getFolderById( $tree->getSelectedFolderId() );
	$out->add('<input type="hidden" value="" name="'.$tree->_getOpMoveFormId().'" />');
	$out->add('<input type="hidden" value="'.$folderid.'" name="'.$tree->_getFolderNameId().'" />');
	$out->add('<input type="hidden" value="'.$tree->getSelectedFolderId().'" name="folder_id" />');
	$out->add('<input type="hidden" value="'.$media_id.'" name="idMedia" />');
	$out->add('<div>'.$tree->_getMoveMediaTargetLabel().$tree->getFolderPrintName($folder).'</div>');
	$out->add($tree->load());
	$out->add(' <img src="'.$tree->_getMoveImage().'" alt="'.$tree->_getMoveAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$lang->def("_ALT_MOVE").'"'
		.' name="'.$tree->_getOpMoveMediaId().$media_id.'" id="'.$tree->_getOpMoveMediaId().$media_id.'" />');
	$out->add(' <img src="'.$tree->_getCancelImage().'" alt="'.$tree->_getCancelAlt().'" /> '
		.'<input type="submit" class="TreeViewAction" value="'.$tree->_getCancelLabel().'"'
		.' name="'.$tree->_getCancelId().'" id="'.$tree->_getCancelId().'" />');

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add('</div>');

}

function media_move_folder( &$treeView ) {
	checkPerm('mod');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	$tree_perm =media_checkTreePerm($idFolder, TRUE);

	$err ="";
	if (!$tree_perm) {
		$folder =$treeView->tdb->getFolderById($idFolder);
		$folder_name =$treeView->getFolderPrintName($folder);
		$err.=getErrorUi(def("_NO_FOLDER_ACCESS_PERM", "standard").": ".$folder_name);
		$old_folder =(int)$_POST[$treeView->_getFolderNameId()];
		$treeView->setSelectedFolderId($old_folder);
	}

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	$back_ui_url="index.php?modname=media&amp;op=media";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MEDIA");
	$title_arr[]=$lang->def("_MOVE_FOLDER");
	$out->add(getTitleArea($title_arr, "media"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add($err); //&
	$GLOBALS['page']->add($treeView->loadMoveFolder());
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$GLOBALS['page']->add('</div>');
}

function media_deletefolder( &$treeView ) {
	checkPerm('del');

	$idFolder = (int)$treeView->getSelectedFolderId(); //&
	media_checkTreePerm($idFolder);

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_media', 'cms');

	$back_ui_url="index.php?modname=media&amp;op=media";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MEDIA");
	$title_arr[]=$lang->def("_DELETE_FOLDER");
	$out->add(getTitleArea($title_arr, "media"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$GLOBALS['page']->add($treeView->loadDeleteFolder());
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$GLOBALS['page']->add('</div>');
}

function movemedia($tree) {
	checkPerm('mod');

	$idMedia=$tree->idMedia;
	$folder_id=$tree->folder_id;

	media_checkTreePerm($folder_id); //&

	$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_media SET idFolder='$folder_id' WHERE idMedia='$idMedia' LIMIT 1;");

	if (!$q) echo mysql_error();
}


function media_appendNewNodePerm($folder_id) { //&
	$res =TRUE;

	$user_level=$GLOBALS["current_user"]->getUserLevelId();
	if ($user_level != ADMIN_GROUP_GODADMIN) {

		require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

		$ctp=new CmsTreePermissions("media");
		$res =$ctp->appendNewNodePerm($GLOBALS["current_user"]->getIdSt(), $folder_id);
	}

	return $res;
}


function &createTreeView( $withContents = TRUE, $multiSelect = FALSE, $withActions = FALSE, $sel_path=FALSE ) {

	$dirDb = new mediaDb();
	$treeView = new Media_TreeView( $dirDb, 'media' );
	$treeView->loadState();
	$treeView->parsePositionData( $_POST, $_POST, $_POST );
	$treeView->saveState();

	$dataRetriever = new Media_DataRetriever( NULL, $GLOBALS["prefix_cms"] );
	$typeOneRenderer = new typeOne(20);
	$listView = new Media_ListView( '', $dataRetriever, $typeOneRenderer, 'idMedia');

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
