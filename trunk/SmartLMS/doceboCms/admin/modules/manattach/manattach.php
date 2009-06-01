<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
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


class ManAttach_DataRetriever extends DataRetriever {
	// id of selected folder in _table_dir (TreeView)
	// used in query composition to filter items
	var $idFolder = 0;

	// set the folder
	function setFolder( $idFolder ) { $this->idFolder = $idFolder; }

	// getRows: overload of method of the DataRetriever class
	// execute query for data retrieving
	// tipically called from listView
	function getRows( $startRow, $numRows ) {
		$t1=$this->prefix."_"._MA_TABLE;
		$t2=$this->prefix."_"._MA_TABLE2;
		if (defined("_MA_TAB2NOTNULL"))
			$nnarr=explode("-", _MA_TAB2NOTNULL);
		$query=_MA_QUERY." WHERE $t1.idFolder='". (int)$this->idFolder . "' AND $t1.publish='1'";
		if ((isset($nnarr)) && (is_array($nnarr)) && (count($nnarr) > 0)) {
			foreach ($nnarr as $key=>$val) {
				$query.=" AND $t2.$val!=''";
			}
		}
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
		require_once($GLOBALS['where_framework'].'/lib/lib.mimetype.php');

		$lang=& DoceboLanguage::createInstance('admin_manattach', 'cms');

		$arrData = parent::fetchRecord();
		if( $arrData === FALSE )
			return FALSE;

		if (_MA_SFT) {
			// Tipo:
			$fn=$arrData["real_fname"];
			$expFileName = explode('.', $fn);
			$totPart = count($expFileName) - 1;
			$mime=mimetype($expFileName[$totPart]);
			$img="<img src=\"".getPathImage().mimeDetect($fn)."\" alt=\"$mime\" title=\"$mime\" />\n";
			$arrData["type"]=$img;
		}

		$arrData['attach'] ="<a href=\"index.php?modname="._SELMOD."&amp;op=manattach&amp;act_op=attachitem&amp;id="._BACKID."&amp;type="._MA_ADD."&amp;add_id=".$arrData[_MA_ID]."\">";
		$arrData['attach'].="<img src=\"".getPathImage()."standard/attach.gif\" alt=\"".$lang->def("_ATTACHMENT")."\" title=\"".$lang->def("_ATTACHMENT")."\" /></a>";

		return $arrData;
	}
}

/**
 * Customizaton of ListView class for homerepo
 **/
class ManAttach_ListView extends ListView {

	var $new_perm;
	var $mod_perm;
	var $rem_perm;

	function ManAttach_ListView( $title, &$data, &$rend, $id ) {
		parent::ListView( $title, $data, $rend, $id );
		$this->new_perm = true;
		$this->mod_perm = true;
		$this->rem_perm = true;

	}


	// utility function
	function _createColInfo( $label, $hClass, $fieldClass, $data, $toDisplay, $sortable ) {
		return array( 	'hLabel' => $label,
						'hClass' => $hClass,
						'fieldClass' => $fieldClass,
						'data' => $data,
						'toDisplay' => $toDisplay,
						'sortable' => $sortable );
	}

	// overload
	function _getCols() {
		$lang=& DoceboLanguage::createInstance('admin_manattach', 'cms');
		$colInfos = array();
		if (_MA_SFT)
			$colInfos[] = $this->_createColInfo( $lang->def("_TYPE"),'image','image','type',true, false );
		$colInfos[] = $this->_createColInfo( _MA_TITLE1,'','',_MA_FIELD1,true, false );
		$colInfos[] = $this->_createColInfo( _MA_TITLE2,'','',_MA_FIELD2,true, false );


		$colInfos[] = $this->_createColInfo(
		'<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def("_ATTACHMENT").'" title="'.$lang->def("_ATTACHMENT").'" />' ,
		'image','image','attach', $this->mod_perm , false );

		return $colInfos;
	}

}

// customization of TreeDb for homerepo_dir
class manattachDb extends TreeDb {

	function manattachDb() {

		$this->table = $GLOBALS["prefix_cms"] . '_'._MA_TABLE.'_dir';
		$this->fields = array( 'id' => 'id', 'idParent' => 'idParent', 'path' => 'path', 'lev' => 'lev' );
	}

}

class ManAttach_TreeView extends TreeView {

	var $idManAttach;
	var $showbtn=0;

	function getManAttachSelected() { return $this->idManAttach; }


	function _getCreateFolderId() { return '_treeview_create_folder_'	.$this->id; }
	function _getRenameFolderId() { return '_treeview_rename_folder_'	.$this->id; }
	function _getMoveFolderId() 	{ return '_treeview_move_folder_'	.$this->id; }
	function _getDeleteFolderId() 	{ return '_treeview_delete_folder_'	.$this->id; }

	function canDelete() { return FALSE; }
	function canRename() { return FALSE; }
	function canMove() { return FALSE; }
	function canAdd() { return FALSE; }

	function canInlineRename()  { return FALSE; }
	function canInlineMove()  { return FALSE; }
	function canInlineMoveItem( &$stack, $level ) {	return FALSE; }
	function canInlineRenameItem( &$stack, $level ) { return FALSE; }


}



function &ManAttach_createTreeView( $withContents = TRUE, $multiSelect = TRUE, $withActions = FALSE ) {

		$dirDb = new manattachDb();
		$treeView = new ManAttach_TreeView( $dirDb, _MA_TABLE );
		$treeView->parsePositionData( $_POST, $_POST, $_POST );

			$dataRetriever = new ManAttach_DataRetriever( NULL, $GLOBALS["prefix_cms"] );
			$typeOneRenderer = new typeOne(20);
			$listView = new ManAttach_ListView( '', $dataRetriever, $typeOneRenderer, _MA_ID);

			$listView->multiSelect = $multiSelect;

			$listView->parsePositionData( $_POST );

			$dataRetriever->setFolder( $treeView->selectedFolder );

			$treeView->setlistView( $listView );


		return $treeView;
}


function manattach_getOp( &$treeView ) {
	$op = $treeView->op;

	if( $op == "" ) {
		$listView = $treeView->getListView();
		if( $listView !== NULL )
		 	$op = $listView->op;
	}

	return $op;
}


function manattach() {
	global $visuItem;


	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manattach', 'cms');

	$treeView = ManAttach_createTreeView();

	switch ( manattach_getOp( $treeView ) ) {
		case 'display':
		default: {
			$out->add('<form method="post" action="index.php?modname='._SELMOD.'&amp;op=manattach&amp;id='._BACKID.'&amp;add='._MA_ADD.'" >'."\n");
			$out->add("<input type=\"hidden\" id=\""._BACKID."\" name=\""._BACKID."\" value=\"1\" />\n");
			$listView = $treeView->getListView();
			$treeView->showbtn=1;
			$out->add($treeView->load()."<br />\n");
			$out->add($treeView->loadActions());
			$listView->setInsNew( false );
			$out->add($listView->printOut());
			$out->add('</form>');
		};break;
	}

}


if (!isset($op))
	$op="";

switch($op) {
	case "manattach" : {
		manattach();
	};break;
}


?>