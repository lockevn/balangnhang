<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_framework'].'/lib/lib.treedb.php');
require_once($GLOBALS['where_framework'].'/lib/lib.treeview.php');


define("ORGFIELDTITLE", 0);
define("ORGFIELDALIAS", 1);
define("ORGFIELDKEYWORD", 4);
define("ORGFIELDLINK", 7);
define("ORGFIELDPUBLISH", 8);

// organization customization of TreeDb class
class PageDb extends TreeDb {
	var $page_title;
	var $page_alias;
	var $page_template;
	var $page_mr_title; // mod. rewrite title
	var $page_browser_title;
	var $page_keyword;
	var $page_sitedesc;
	var $page_link;
	var $page_publish;
	var $langdef;
	var $show_in_menu;
	var $show_in_macromenu;

	function PageDb( ) {

		$this->table = $GLOBALS["prefix_cms"] . '_area';
		$this->fields = array( 'id' => 'idArea', 'idParent' => 'idParent', 'path' => 'path', 'lev' => 'lev' );
	}

	// Page are stored in a table with the structure requested by
	// TreeDb to manage tree. In addition the table contains
	// title
	function _getOtherFields($tname = FALSE) {
		if( $tname === FALSE )
			return ", title, alias, template, mr_title, browser_title, keyword, sitedesc, link, publish, langdef, show_in_menu, show_in_macromenu ";
		else
			return   ", ".$tname.".title, ".$tname.".alias, ".$tname.".template, ".$tname.".mr_title, ".$tname.".browser_title, ".$tname.".keyword, ".$tname.".sitedesc, ".$tname.".link, ".$tname.".publish, ".$tname.".langdef, ".$tname.".show_in_menu, ".$tname.".show_in_macromenu ";
	}

	function _getOtherValues() {
		return ", '".$this->page_title."' "
			.", '".$this->page_alias."' "
			.", '".$this->page_template."' "
			.", '".$this->page_mr_title."' "
			.", '".$this->page_browser_title."' "
			.", '".$this->page_keyword."' "
			.", '".$this->page_sitedesc."' "
			.", '".$this->page_link."' "
			.", '".$this->page_publish."' "
			.", '".$this->langdef."' "
			.", '".$this->show_in_menu."' "
			.", '".$this->show_in_macromenu."' ";
	}

	function _getOtherUpdates() {
		return " title='".$this->page_title."' "
			.", alias='".$this->page_alias."' "
			.", template='".$this->page_template."' "
			.", link='".$this->page_link."' "
			.", mr_title='".$this->page_mr_title."' "
			.", browser_title='".$this->page_browser_title."' "
			.", keyword='".$this->page_keyword."' "
			.", sitedesc='".$this->page_sitedesc."' "
			.", show_in_menu='".$this->show_in_menu."' "
			.", show_in_macromenu='".$this->show_in_macromenu."' ";
	}

	function getMaxChildPos( $idFolder ) {
		$query = "SELECT MAX(SUBSTRING_INDEX(path, '/', -1))"
				." FROM ". $this->table
				." WHERE (". $this->fields['idParent'] ." = '". (int)$idFolder ."')"
				.$this->_getFilter();
		$rs = mysql_query( $query )
				or die( "Error [$query] <br />". mysql_error() );
		if( mysql_num_rows( $rs ) == 1 ) {
			list( $result ) = mysql_fetch_row( $rs );
			return $result;
		} else {
			return '00000001';
		}
	}

	function getNewPos( $idFolder ) {
		return substr('00000000' .($this->getMaxChildPos( $idFolder )+1), -8);
	}

	function addItemById( $idParent, $title, $alias, $template, $mr_title, $browser_title, $keyword, $sitedesc, $link, $publish, $langdef, $show_in_menu, $show_in_macromenu ) {

		$this->page_title = $title;
		$this->page_alias = $alias;
		$this->page_template = $template;
		$this->page_mr_title = $mr_title;
		$this->page_browser_title = $browser_title;
		$this->page_keyword = $keyword;
		$this->page_sitedesc = $sitedesc;
		$this->page_link = $link;
		$this->page_publish = $publish;
		$this->langdef = $langdef;
		$this->show_in_menu = $show_in_menu;
		$this->show_in_macromenu = $show_in_macromenu;

		if( parent::addFolderById( $idParent, $this->getNewPos( $idParent )) ) {
			list($idArea) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			return $idArea;
		}
		return false;
	}

	function addFolderById( $idParent, $title, $alias, $template, $mr_title, $browser_title, $keyword, $sitedesc, $link, $publish, $langdef, $show_in_menu, $show_in_macromenu ) {

		$this->page_title = $title;
		$this->page_alias = $alias;
		$this->page_template = $template;
		$this->page_mr_title = $mr_title;
		$this->page_browser_title = $browser_title;
		$this->page_keyword = $keyword;
		$this->page_sitedesc = $sitedesc;
		$this->page_link = $link;
		$this->page_publish = $publish;
		$this->langdef = $langdef;
		$this->show_in_menu = $show_in_menu;
		$this->show_in_macromenu = $show_in_macromenu;

		parent::addFolderById( $idParent, $this->getNewPos( $idParent ));
	}

	function renameFolder( &$folder, $newName, $alias, $template, $mr_title, $browser_title, $keyword, $sitedesc, $link, $publish, $show_in_menu, $show_in_macromenu ) {

		$this->page_title = $newName;
		$this->page_alias = $alias;
		$this->page_template = $template;
		$this->page_mr_title = $mr_title;
		$this->page_browser_title = $browser_title;
		$this->page_keyword = $keyword;
		$this->page_sitedesc = $sitedesc;
		$this->page_link = $link;
		$this->page_publish = $publish;
		$this->show_in_menu = $show_in_menu;
		$this->show_in_macromenu = $show_in_macromenu;

		$this->changeOtherData( $folder );
	}

	function modifyItem( $arrData ) {
		$folder = $this->getFolderById( $arrData['idItem'] );

		$this->page_title = $arrData['title'];
		$this->page_alias = $arrData['alias'];
		$this->page_template = $arrData['template'];
		$this->page_mr_title = $arrData['mr_title'];
		$this->page_browser_title = $arrData['browser_title'];
		$this->page_keyword = $arrData['keyword'];
		$this->page_sitedesc = $arrData['sitedesc'];
		$this->page_link = $arrData['link'];
		$this->page_publish = $arrData['publish'];
		$this->show_in_menu = $arrData['show_in_menu'];
		$this->show_in_macromenu = $arrData['show_in_macromenu'];
		$this->changeOtherData( $folder );
	}

	function moveUp( $idFolder ) {
		$folder = $this->getFolderById( $idFolder );
		$arrIdSiblings = $this->getChildrensIdById( $folder->idParent );
		if( !is_array( $arrIdSiblings ) )
			return;
		$pos = array_search( $idFolder, $arrIdSiblings );
		if( $pos === NULL || $pos === FALSE ) // prior to php 4.2.0 and after
			return;
		if( $pos == 0 ) // I know it's possible the merge with previous if but this is clear ...
			return;
		$folder2 = $this->getFolderById( $arrIdSiblings[$pos-1] );
		$tmpArr = explode( '/', $folder->path );
		$folderName = $tmpArr[count($tmpArr)-1];
		$tmpArr = explode( '/', $folder2->path );
		$folderName2 = $tmpArr[count($tmpArr)-1];

		$this->setPathToTemp($folder);
		parent::renameFolder( $folder, $folderName2 );
		$this->setPathToTemp($folder2);
		parent::renameFolder( $folder2, $folderName );
		$this->restorePathFromTemp($folder, $folder2);
		$this->restorePathFromTemp($folder2, $folder);
	}

	function moveDown( $idFolder ) {
		$folder = $this->getFolderById( $idFolder );
		$arrIdSiblings = $this->getChildrensIdById( $folder->idParent );
		if( !is_array( $arrIdSiblings ) )
			return;
		$pos = array_search( $idFolder, $arrIdSiblings );
		if( $pos === NULL || $pos === FALSE ) // prior to php 4.2.0 and after
			return;
		if( $pos == (count($arrIdSiblings)-1) )
			return;
		$folder2 = $this->getFolderById( $arrIdSiblings[$pos+1] );
		$tmpArr = explode( '/', $folder->path );
		$folderName = $tmpArr[count($tmpArr)-1];
		$tmpArr = explode( '/', $folder2->path );
		$folderName2 = $tmpArr[count($tmpArr)-1];

		$this->setPathToTemp($folder);
		parent::renameFolder( $folder, $folderName2 );
		$this->setPathToTemp($folder2);
		parent::renameFolder( $folder2, $folderName );
		$this->restorePathFromTemp($folder, $folder2);
		$this->restorePathFromTemp($folder2, $folder);
	}


	function setPathToTemp($folder) {
		$folder_id =(int)$folder->id;
		$prefix ="/tmp_".$folder_id;
		$query = "UPDATE ". $this->table
				." SET "
				. $this->fields['path']
				." = CONCAT('".$prefix."', path)"
				." WHERE ((path LIKE '".mysql_escape_string($folder->path)."/%')"
				."   AND (".$this->fields['id']." != '".$folder_id."')) "
				.$this->_getFilter();
		$res =$this->_executeQuery( $query );
		if (!$res) { echo $query; die(); }
		return $res;
	}


	function restorePathFromTemp($prev_folder, $new_folder) {
		$folder_id =(int)$prev_folder->id;
		$new_folder_id =(int)$new_folder->id;
		$prefix ="/tmp_".$folder_id;
		$len = strlen( $prefix.$prev_folder->path )+1;
		$query = "UPDATE ". $this->table
				." SET "
				. $this->fields['path']
				." = CONCAT('". $prev_folder->path ."', SUBSTRING( path, ". $len ."))"
				." WHERE ((path LIKE '".mysql_escape_string($prefix.$new_folder->path)."/%')"
				."   AND (".$this->fields['id']." != '".$new_folder_id."')) "
				.$this->_getFilter();
		$res =$this->_executeQuery( $query );
		if (!$res) { echo $query; die(); }
		return $res;
	}


	// overload to modify folder internal name to avoid conflicts
	// and send it to the end of parent
	function moveFolder( &$folder, &$parentFolder ) {
		// change folder name
		$folder->path = $this->getNewPos( $parentFolder->id );
		parent::moveFolder( $folder, $parentFolder );
	}

}

class Page_TreeView extends TreeView {

	var $kind = '';
	var $opContextId = 0;
	var $idSelected = 0;

	var $use_admin_filter=FALSE;
	var $page_perm=FALSE;


	function getSelectedId() { return $this->idSelected; }

	function _getAddImage() { return getPathImage().'standard/add.gif'; }
	function _getAddFolderImage() { return getPathImage().'standard/addfolder.gif'; }
	function _getAddLabel($what="page") {
		switch($what) {
			case "page": {
				return def("_ADDPAGE", "admin_manpage", "cms");
			} break;
			case "homepage": {
				return def("_ADDHOMEPAGE", "admin_manpage", "cms");
			} break;
			case "macroarea": {
				return def("_ADDMACROAREA", "admin_manpage", "cms");
			} break;
		}
	}
	function _getAddFolderLabel() { return def("_ADDLANGUAGE", "admin_manpage", "cms"); }
	function _getAddAlt() { return def("_ADD", "admin_manpage", "cms"); }
	function _getAddFolderAlt() { return def("_ADDLANGUAGE", "admin_manpage", "cms"); }

	function _getOpPublishTitle() { return def("_OPAGEPUBLISH", "admin_manpage", "cms"); }
	function _getOpPublishId() { return '_pagepub_'.$this->id; }

	function _getOpUnPublishTitle() { return def("_OPAGEUNPUBLISH", "admin_manpage", "cms"); }
	function _getOpUnPublishId() { return '_pageunpub_'.$this->id; }

	function _getOpUpTitle() { return def("_MOVE_UP", "admin_manpage", "cms"); }
	function _getOpUpId() { return '_orgopup_'; }
	function _getOpDownTitle() { return def("_ORGDOWNTITLE", "admin_manpage", "cms"); }
	function _getOpDownId() { return '_orgopdown_'; }

	function _getOpModTitle() { return def("_PAGEMOD", "admin_manpage", "cms"); }
	function _getOpModId() { return '_pagemod_'; }
	function _getOpModLangTitle() { return def("_PAGEMODLANG", "admin_manpage", "cms"); }
	function _getOpModLangId() { return '_pagemodlang_'; }
	function _getOpModBTitle() { return def("_PAGEMODBLOCK", "admin_manpage", "cms"); }
	function _getOpModBId() { return '_pagemodblock_'; }

	function _getOpDelTitle() { return def("_PAGEDEL", "admin_manpage", "cms"); }
	function _getOpDelId() { return '_pagedel_'.$this->id; }
	function _getOpConfDelId() { return '_confpagedel_'.$this->id; }


	function setUseAdminFilter($val) {
		$this->use_admin_filter=$val;
	}


	function getUseAdminFilter() {
		return $this->use_admin_filter;
	}


	function setPagePerm($user_id, $page_perm) {
		$this->page_perm[$user_id]=$page_perm;
	}


	function getPagePerm($user_id) {

		if (isset($this->page_perm[$user_id]))
			$res=$this->page_perm[$user_id];
		else
			$res=FALSE;

		return $res;
	}

	function canAccessToPage($user_id, $area_id) {
		$res=FALSE;

		if (!$this->getUseAdminFilter())
			return TRUE;

		$page_perm=$this->getPagePerm($user_id);
		if ($page_perm === FALSE) {
			require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");

			$ctp=new CmsTreePermissions("page");

			$page_perm=$ctp->loadAllNodePerm($user_id, TRUE);
			$page_perm=$page_perm["all"];
			$this->setPagePerm($user_id, $page_perm);
		}

		$res=(in_array((int)$area_id, $page_perm) ? TRUE : FALSE);
		return $res;
	}

	function loadActions() {

		if (!checkPerm('add', true))
			return "";


		$out=& $GLOBALS['page'];
		$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

		$folder_id=$this->getSelectedFolderId();

		require_once($GLOBALS["where_cms"]."/lib/lib.tree_perm.php");
		$ctp=new CmsTreePermissions("page");

		if (!$ctp->checkNodePerm($GLOBALS["current_user"]->getIdSt(), (int)$folder_id, TRUE))
			return "";

		$folder=$this->tdb->getFolderById($folder_id);
		$path=$folder->path;
		$level=$folder->level;
		$folder->countChildrens();
		$arrData=$folder->otherValues;
		if ((is_array($arrData)) && (count($arrData) > 0)) {
			$link=$arrData[ORGFIELDLINK]; // link field
		}


		// perche quando non si e' ancora selezionata una cartella path = "/root"
		// mentre se poi si seleziona root path = "" ??? [to be fixed*]

		// *temp fix: ($path != "/root") :
		if (($path != "/root") && (substr_count($path, "/") > 0)) {
			if ($level == 1) {

				// Please let me know If there is a better way to get
				// the number of childrens of this folder ;)
				$arr=array($folder_id);
				$coll=$this->tdb->getFoldersCollection($arr);
				$selected_fc=$coll->getFirst();
				$countChildrens=$selected_fc->countChildrens();

				if ($countChildrens > 0)
					$what="macroarea";
				else
					$what="homepage";
			}
			else if ($level > 1) {
				$what="page";
			}
			else {
				$what="page";
			}

			if ($link == "") {
				$out->add('<div class="TreeViewActionContainer">');
				$out->add('<img src="'.$this->_getAddImage().'" alt="'.$this->_getAddAlt().'" /> '
					.'<input type="submit" class="TreeViewAction" value="'.$this->_getAddLabel($what).'"'
					.' name="'.$this->_getAddUrl().'" />');

				$out->add('</div>');
			}

		}
		else {
			$out->add('<div class="TreeViewActionContainer">');
			$out->add('<img src="'.$this->_getAddFolderImage().'" alt="'.$this->_getAddFolderAlt().'" /> '
				.'<input type="submit" class="TreeViewAction" value="'.$this->_getAddFolderLabel().'"'
				.' name="'.$this->_getAddUrl().'" />');

			$out->add('</div>');
		}
	}

	function getFolderPrintName( &$folder ) {
		//print title unstead of the folder name
		if ((isset($folder->otherValues[ORGFIELDALIAS])) && (!empty($folder->otherValues[ORGFIELDALIAS])))
			return $folder->otherValues[ORGFIELDALIAS];
		if( isset( $folder->otherValues[ORGFIELDTITLE] ) )
			return $folder->otherValues[ORGFIELDTITLE];
		else
			return parent::getFolderPrintName( $folder );
	}


	function extendedParsing( $arrayState, $arrayExpand, $arrayCompress ) {

		foreach( $_POST as $nameField => $valueField ) {
			if( strstr( $nameField, $this->_getOpConfDelId() ) ) { // Si': voglio cancellare..
				$id=substr( $nameField, strlen($this->_getOpConfDelId()));

				$this->tdb->del_page_now($id);
				$this->refresh=true;
			}
		}
		parent::extendedParsing( $arrayState, $arrayExpand, $arrayCompress );
	}


	function parsePositionData( $arrayState, $arrayExpand, $arrayCompress ) {


		foreach( $_POST as $nameField => $valueField ) {
			if( strstr( $nameField, $this->_getOpModId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModId()));
				$this->idSelected = $id;
				$this->op = 'editpage';
			}
			elseif( strstr( $nameField, $this->_getOpModLangId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModLangId()));
				$this->idSelected = $id;
				$this->op = 'editpagelang';
			}
			else if( strstr( $nameField, $this->_getOpUpId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpUpId()));
				$this->tdb->moveUp( $id );
				update_home();
			} else if( strstr( $nameField, $this->_getOpDownId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpDownId()));
				$this->tdb->moveDown( $id );
				update_home();
			} else if( strstr( $nameField, $this->_getOpModBId() ) ) {
				$id = substr( $nameField, strlen($this->_getOpModBId()));
				$this->op = 'pagemodblock';
				$this->idSelected = $id;
			}
			else if( strstr( $nameField, $this->_getOpDelId() ) ) { // Vuoi cancellare ?
				$id = substr( $nameField, strlen($this->_getOpDelId()));
				$this->op = 'pagedel';
				$this->idSelected = $id;
			}
			else if( strstr( $nameField, $this->_getOpPublishId() ) ) { // Publish
				$id = substr( $nameField, strlen($this->_getOpPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_area SET publish='1' WHERE idArea='$id';");
				update_home();
				if (!$q) echo ("<script>alert('Errore: ".mysql_error()."');</script>");
			}
			else if( strstr( $nameField, $this->_getOpUnPublishId() ) ) { // Un-Publish
				$id = substr( $nameField, strlen($this->_getOpUnPublishId()));
				$q=mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_area SET publish='0' WHERE idArea='$id';");
				update_home();
				if (!$q) echo ("<script>alert('Errore: ".mysql_error()."');</script>");
			}
		}
		parent::parsePositionData( $arrayState, $arrayExpand, $arrayCompress );
	}


	function printParentElement(&$stack, $level) {
		$elem=parent::printElement($stack, $level);

		return $elem;
	}


	function printElement(&$stack, $level) {
		$elem=parent::printElement($stack, $level);

		if (!$this->canAccessToPage($GLOBALS["current_user"]->getIdSt(), $stack[$level]['folder']->id)) {
			return $elem;
		}

		$out=& $GLOBALS['page'];
		$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

		$can_mod=checkPerm('mod', true);
		$can_del=checkPerm('del', true);

		if( $level > 0 ) {

			$arrData = $stack[$level]['folder']->otherValues;
			$link=$arrData[ORGFIELDLINK];
			$nochild=$stack[$level]['isLeaf'];
			if( $can_del && $nochild ) {
				$elem.='<input type="submit" class="OrgDel" value="" name="'
					.$this->_getOpDelId().$stack[$level]['folder']->id .'" id="'
					.$this->_getOpDelId().$stack[$level]['folder']->id .'"'
					.' title="'.$this->_getOpDelTitle().'" />';
			}
			else {
				$elem.='<input type="submit" class="OrgPlay" value="" name="'
					.$this->_getCancelId().'"'
					.' title="" />';
			}
			if( $can_mod ) {

				if ($level > 1) {
					$elem.='<input type="submit" class="OrgMod" value="" name="'
						.$this->_getOpModId().$stack[$level]['folder']->id .'"'
						.'title="'.$this->_getOpModTitle().'" />'."\n";
					if ($link != "") {
						$elem.='<input type="submit" class="OrgPlay" value="" name="'
							.$this->_getCancelId().'"'
							.' title="" />'."\n";
					}
					else {
						$elem.='<input type="submit" class="OrgModBlock" value="" name="'
							.$this->_getOpModBId().$stack[$level]['folder']->id .'"'
							.'title="'.$this->_getOpModBTitle().'" />'."\n";
					}
				}
				else {
					if ($level == 1) {
						$elem.='<input type="submit" class="OrgMod" value="" name="'
							.$this->_getOpModLangId().$stack[$level]['folder']->id .'"'
							.'title="'.$this->_getOpModLangTitle().'" />'."\n";
					}
					else {
						$elem.='<input type="submit" class="OrgPlay" value="" name="'
							.$this->_getCancelId().'"'
							.' title="" />'."\n";
					}
					$elem.='<input type="submit" class="OrgPlay" value="" name="'
						.$this->_getCancelId().'"'
						.' title="" />'."\n";
				}

				$elem.='<input type="submit" class="OrgUp" value="" name="'
					.$this->_getOpUpId().$stack[$level]['folder']->id .'"'
					.'title="'.$this->_getOpUpTitle().'" />'."\n";
				$elem.='<input type="submit" class="OrgDown" value="" name="'
					.$this->_getOpDownId().$stack[$level]['folder']->id .'"'
					.'title="'.$this->_getOpDownTitle().'" />'."\n";

			if(!$arrData[ORGFIELDPUBLISH]) { // publish field
				$elem.='<input type="submit" class="OrgPublish" value="" name="'
					.$this->_getOpPublishId().$stack[$level]['folder']->id .'"'
					.'title="'.$this->_getOpPublishTitle().'" />'."\n";
			}
			else {
				$elem.='<input type="submit" class="OrgUnPublish" value="" name="'
					.$this->_getOpUnPublishId().$stack[$level]['folder']->id .'"'
					.'title="'.$this->_getOpUnPublishTitle().'" />'."\n";
			}

			}
		}
		return $elem;
	}

	function getImage( &$stack, $currLev, $maxLev ) {
		if( $currLev > 1 && $currLev == $maxLev ) {
			$arrData = $stack[$currLev]['folder']->otherValues;
			if ($arrData[ORGFIELDLINK] == "") { // link field
				return array( 'page', 'manpage/page.gif', "_PAGE");
			}
			else {
				return array( 'page', 'manpage/link.gif', "_PAGE");
			}
		}
		return parent::getImage( $stack, $currLev, $maxLev );
	}
}

?>
