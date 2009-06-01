<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * Regional settings management classes
 *
 * @package admin-library
 * @subpackage pubblication flow
 * @author   Giovanni Derks <virtualdarkness[AT]gmail-com>
 */


define("_PUBFLOW_CODE_ONESTATE", "pub_onestate");
define("_PUBFLOW_CODE_TWOSTATE", "pub_twostate");


class PublicationFlow {
	/** RegionalSettingsManager object */
	var $pfManager=NULL;

	var $lang=NULL;

	/**
	 * PublicationFlow constructor
	 * @param string $pfm_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function PublicationFlow($platform, $pfm_prefix=FALSE, $dbconn=NULL) {

		$this->pfManager=new PublicationFlowManager($pfm_prefix, $dbconn);
		$this->lang =& DoceboLanguage::createInstance('pubflow', $platform);

	}


	function getFlowDropdown(& $form, $sel=false) {

		$res="";
		$avail_flow=array();

		switch($GLOBALS["default_pubflow_method"]) {
			case "onestate": {
				$res.=$form->getHidden("flow_id", "flow_id", $this->pfManager->getFlowIdFromCode(_PUBFLOW_CODE_ONESTATE));
			} break;
			case "twostate": {
				$res.=$form->getHidden("flow_id", "flow_id", $this->pfManager->getFlowIdFromCode(_PUBFLOW_CODE_TWOSTATE));
			} break;
			case "advanced": {

				$all_flow=$this->pfManager->getAllFlow();

				foreach($all_flow as $key=>$val) {
					//if (($val["flow_code"] != _PUBFLOW_CODE_ONESTATE) && ($val["flow_code"] != _PUBFLOW_CODE_TWOSTATE))
					$avail_flow[$val["flow_id"]]=$this->pfManager->getItemLangText("flow", $val["flow_id"], getLanguage(), "label");
				}

				$res.=$form->getDropdown($this->lang->def("_SELECT_FLOW"),"flow_id","flow_id",	$avail_flow, $sel);

			} break;
		}

		return $res;
	}


}




class PublicationFlowManager {
	/** db connection */
	var $dbconn;
	/** prefix for the database */
	var $prefix;
	/** prefix for database containg tables souch as log and status */
	var $data_prefix;

	var $localized_strings=array();
	var $change_log=NULL;

	/**
	 * PublicationFlowManager constructor
	 * @param string $param_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function PublicationFlowManager( $param_prefix = FALSE, $dbconn = NULL ) {
		if( $param_prefix === FALSE ) {
			$this->prefix=$GLOBALS["prefix_fw"];
		} else {
			$this->prefix=$param_prefix;
		}
		$this->dbConn=$dbconn;
	}


	/**
	 * @return string table name for the list of flows
	 **/
	function _getListTable() {
		return $this->prefix."_pflow_list";
	}

	/**
	 * @return string table name with the localized values
	 **/
	function _getLangTable() {
		return $this->prefix."_pflow_lang";
	}

	/**
	 * @return string table name for the list of steps
	 **/
	function _getStepTable() {
		return $this->prefix."_pflow_step";
	}

	/**
	 * @return string table name for the saved status
	 **/
	function _getStatusTable() {
		return $this->getDataPrefix()."_pflow_status";
	}

	/**
	 * @return string table name of the log table
	 **/
	function _getLogTable() {
		return $this->getDataPrefix()."_pflow_log";
	}

	/**
	 * @return string table name of the log table
	 **/
	function _getUserTable() {
		return $GLOBALS["prefix_fw"]."_user";
	}


	function _executeQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}

	function getDataPrefix() {
		if ($this->data_prefix != "")
			return $this->data_prefix;
		else
			return $this->prefix;
	}

	function setDataPrefix($prefix) {
		$this->data_prefix=$prefix;
	}


	/**
	 * return an array with all the region_id presents on system
	 * @return array with all the region_id in system (index in array is numeric
	 *			starting from 0, value is region_id)
	 */
	function getAllFlow() {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getListTable()." ORDER BY ord";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				$res[$i]=$row;

				$i++;
			}
		}

		return $res;
	}


	/**
	 * @return array
	 */
	function getAllSteps($flow_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getStepTable()." WHERE flow_id='$flow_id' ORDER BY ord";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				$res[$i]=$row;

				$i++;
			}
		}

		return $res;
	}


	/**
	 * @return array
	 */
	function getStepNeighbors($flow_id, $ord) {

		$res=array();

		if ($ord > 1) {
			$ord=$ord-1;
			$limit=3;
		}
		else {
			$limit=2;
		}

		$qtxt ="SELECT * FROM ".$this->_getStepTable()." WHERE flow_id='".$flow_id."' ";
		$qtxt.="AND ord>='".$ord."' ORDER BY ord LIMIT 0,".$limit;
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				$res[$i]=$row;

				$i++;
			}
		}

		return $res;
	}


	function getFlowIdFromCode($flow_code) {

		$flow_id=false;
		$qtxt="SELECT flow_id FROM ".$this->_getListTable()." WHERE flow_code='$flow_code' ORDER BY ord LIMIT 0,1";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$flow_id=$row["flow_id"];
		}

		return $flow_id;
	}


	/**
	 * @return array
	 */
	function getFirstStep($flow_id, $flow_code=FALSE) {

		$res=FALSE;

		if ($flow_id === FALSE) {

			if ($flow_code === FALSE)
				return 0;

			$flow_id=$this->getFlowIdFromCode($flow_code);

		}

		$qtxt="SELECT step_id FROM ".$this->_getStepTable()." WHERE flow_id='$flow_id' ORDER BY ord LIMIT 0,1";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["step_id"];
		}

		return $res;
	}


	/**
	 * @return array
	 */
	function getFlowInfo($flow_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getListTable()." WHERE flow_id='$flow_id' ORDER BY ord";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row;
		}

		return $res;
	}


	/**
	 * @return array
	 */
	function getStepInfo($step_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getStepTable()." WHERE step_id='$step_id' ORDER BY ord";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row;
		}

		return $res;
	}


	/**
	 */
	function getLocalizedStrings($type) {

		if ((!isset($this->localized_strings[$type])) || (!is_array($this->localized_strings[$type]))
			 || (count($this->localized_strings[$type]) < 1)) {

			$this->localized_strings=$this->loadLocalizedStrings($type);
		}

		return $this->localized_strings;
	}


	/**
	 */
	function loadLocalizedStrings($type) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getLangTable()." WHERE type='$type' ORDER BY language";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$res[$type][$row["id"]][$row["language"]][$row["val_name"]]=$row["value"];
			}
		}

		return $res;
	}


	/**
	 */
	function getItemLangText($type, $id, $language, $name) {

		$locstr=$this->getLocalizedStrings($type);

		if (isset($locstr[$type][$id][$language][$name]))
			return $locstr[$type][$id][$language][$name];
		else
			return false;
	}


	/**
	 */
	function saveFlow($data) {

		$id=(int)$data["flow_id"];

		if ($id == 0) {
			$ord=$this->getLastOrd($this->_getListTable())+1;
			$qtxt="INSERT INTO ".$this->_getListTable()." (ord) VALUES('$ord')";
			$id=$this->_executeInsert($qtxt);
		}
		else {
			$qtxt="DELETE FROM ".$this->_getLangTable()." WHERE id='$id' AND type='flow'";
			$q=$this->_executeQuery($qtxt);
		}

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {

			$this->addItemLangText("flow", $id, $val, "label", $data["label"][$val]);
			$this->addItemLangText("flow", $id, $val, "description", $data["description"][$val]);

		}

	}


	/**
	 */
	function deleteFlow($flow_id) {
		//TODO: delete references in other tables like _pflow_log and so on..

		$flow_info=$this->getFlowInfo($flow_id);
		if ($flow_info["default"])
			die();

		$steps=$this->getAllSteps($flow_id);

		$step_id_arr=array();
		foreach ($steps as $cur_step) {
			$step_id_arr[]="'".$cur_step["step_id"]."'";
		}

		$step_id_str=implode(",", $step_id_arr);
		$default=chr(96)."default".chr(96);

		$qtxt="SELECT * FROM ".$this->_getListTable()." WHERE flow_id='".$flow_id."' AND ".$default."='0'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$qtxt="DELETE FROM ".$this->_getListTable()." WHERE flow_id='".$flow_id."' AND ".$default."='0'";
			$q=$this->_executeQuery($qtxt);

			$qtxt="DELETE FROM ".$this->_getLangTable()." WHERE id='".$flow_id."' AND type='flow'";
			$q=$this->_executeQuery($qtxt);

			$qtxt="DELETE FROM ".$this->_getLangTable()." WHERE id IN (".$step_id_str.") AND type='step'";
			$q=$this->_executeQuery($qtxt);
		}
	}


	/**
	 */
	function addItemLangText($type, $id, $language, $name, $value) {
		$qtxt ="INSERT INTO ".$this->_getLangTable()." (id, type, language, val_name, value) ";
		$qtxt.="VALUES('$id', '$type', '$language', '$name', '".$value."')";

		$q=$this->_executeQuery($qtxt);
	}


	/**
	 */
	function getLastOrd($table) {
		$qtxt="SELECT ord FROM ".$table." ORDER BY ord DESC";
		$q=$this->_executeQuery($qtxt);

		$res=0;

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["ord"];
		}

		return $res;
	}


	/**
	 */
	function saveStep($data) {
		require_once($GLOBALS['where_framework']."/lib/lib.aclmanager.php");
		$acl=new DoceboACLManager();

		$flow_id=(int)$data["flow_id"];
		$step_id=(int)$data["step_id"];
		$is_published=(int)$data["is_published"];

		if ($step_id == 0) {
			$ord=$this->getLastOrd($this->_getStepTable())+1;
			$qtxt="INSERT INTO ".$this->_getStepTable()." (flow_id, ord, is_published) VALUES('$flow_id', '$ord', '$is_published')";

			$step_id=$this->_executeInsert($qtxt);
			$st_id=$acl->registerGroup("/pubflow_step_".$step_id, "", true);

			$qtxt="UPDATE ".$this->_getStepTable()." SET st_id='$st_id' WHERE step_id='$step_id'";
			$q=$this->_executeQuery($qtxt);
		}
		else {
			$qtxt="UPDATE ".$this->_getStepTable()." SET is_published='$is_published' WHERE step_id='$step_id'";
			$q=$this->_executeQuery($qtxt);

			$qtxt="DELETE FROM ".$this->_getLangTable()." WHERE id='$step_id' AND type='step'";
			$q=$this->_executeQuery($qtxt);
		}

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {

			$this->addItemLangText("step", $step_id, $val, "label", $data["label"][$val]);
			$this->addItemLangText("step", $step_id, $val, "description", $data["description"][$val]);

		}

	}


	/**
	 */
	function deleteStep($flow_id, $step_id) {
		//TODO: delete references in other tables like _pflow_log and so on..

		$flow_info=$this->getFlowInfo($flow_id);
		if ($flow_info["default"])
			die();

		$qtxt="DELETE FROM ".$this->_getStepTable()." WHERE step_id='".$step_id."'";
		$q=$this->_executeQuery($qtxt);

		$qtxt="DELETE FROM ".$this->_getLangTable()." WHERE id='".$step_id."' AND type='step'";
		$q=$this->_executeQuery($qtxt);
	}


	/**
	 */
	function getStId($step_id) {
		$qtxt="SELECT st_id FROM ".$this->_getStepTable()." WHERE step_id='$step_id'";
		$q=$this->_executeQuery($qtxt);

		$res=0;

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["st_id"];
		}

		return $res;
	}



	function canSetStep($st_id) {

		return $GLOBALS["current_user"]->matchUserST($st_id);

	}


	function getFlowMembers($st_id) {

		$acl_manager=$GLOBALS["current_user"]->getAclManager();

		return $acl_manager->getGroupMembers($st_id);
	}


	function getCurrentStep($module, $item, $key1, $key2=FALSE) {

		$res=FALSE;

		$qtxt ="SELECT step_id FROM ".$this->_getStatusTable()." WHERE ";
		$qtxt.="modname='$module' AND item='$item' AND key1='$key1'";
		if ($key2 !== FALSE)
			$qtxt.=" AND key2='$key2'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["step_id"];
		}

		return $res;
	}


	function resetAllStep($flow_id, $module, $item, $key1, $key2=FALSE) {
		// todo[?]: impostare a defaultStep tuti gli stati salvati per la data chiave.
		// da usare quando l'utente cambia il flusso per l'oggetto.
	}


	function setStatusTo($step_id, $module, $item, $key1, $key2=FALSE) {

		$where =" WHERE modname='$module' AND item='$item' AND key1='$key1'";
		if ($key2 !== FALSE)
			$where.=" AND key2='$key2'";

		$qtxt="SELECT step_id FROM ".$this->_getStatusTable().$where;
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) { // UPDATE
			//$where.=" AND step_id='".(int)$step_id."'";
			$qtxt="UPDATE ".$this->_getStatusTable()." SET step_id='".(int)$step_id."'".$where;
		}
		else { // INSERT
			$qtxt ="INSERT INTO ".$this->_getStatusTable()." (modname, item, key1, key2, step_id) ";
			$qtxt.="VALUES('".$module."', '".$item."', '".(int)$key1."', '".($key2 !== FALSE ? $key2 : NULL)."', '".(int)$step_id."')";
		}

		return $this->_executeQuery($qtxt);
	}


	/**
	 * @param int $flow_id is used when the user is using the advanced publication flow management
	 * 	the custom flow id ($flow_id) depends on the selected item.
	 */
	function getDefaultStep($flow_id) {

		switch($GLOBALS["default_pubflow_method"]) {
			case "onestate": {
				return $this->getFirstStep(false, _PUBFLOW_CODE_ONESTATE);
			} break;
			case "twostate": {
				return $this->getFirstStep(false, _PUBFLOW_CODE_TWOSTATE);
			} break;
			case "advanced": {
				return $this->getFirstStep((int)$flow_id);
			} break;
		}

	}


	function saveChangeLog($step_id, $module, $item, $key1, $key2=FALSE) {

		if ($step_id > $_POST["old_step"])
			$ctype="upgrade";
		else
			$ctype="downgrade";

		$user_id=(int)$GLOBALS["current_user"]->getIdSt();

		$qtxt ="INSERT INTO ".$this->_getLogTable()." (modname, item, key1, key2, step_id, user_id, ctime, ctype, note) ";
		$qtxt.="VALUES('".$module."', '".$item."', '".(int)$key1."', '".($key2 !== FALSE ? $key2 : NULL)."', ";
		$qtxt.="'".(int)$step_id."', '".$user_id."', NOW(), '".$ctype."', '".$_POST["sc_note"]."')";

		return $this->_executeQuery($qtxt);
	}


	function loadChangeLog($module, $item, $key1, $key2=FALSE) {

		$res=array();

		$where =" WHERE t1.modname='$module' AND t1.item='$item' AND t1.key1='$key1'";
		if ($key2 !== FALSE)
			$where.=" AND t1.key2='$key2'";

		$fields="t1.step_id, t2.userid as user, t1.ctime, t1.ctype, t1.note";
		$qtxt ="SELECT $fields FROM ".$this->_getLogTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$this->_getUserTable()." as t2 ON (t1.user_id=t2.idst) ";
		$qtxt.=$where." ORDER BY t1.log_id DESC";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				$res[$i]=$row;

				$i++;
			}
		}

		return $res;
	}


	function getChangeLog($module, $item, $key1, $key2=FALSE) {

		$key=$module."-".$item."-".$key1;
		if ($key2 !== FALSE)
			$key.="-".$key2;

		if ((!isset($this->change_log[$key])) || (!is_array($this->change_log[$key]))) {
			$this->change_log[$key]=$this->loadChangeLog($module, $item, $key1, $key2);
		}

		return $this->change_log[$key];
	}


}


class FlowStepSelector {

	/** PublicationFlow Manager object */
	var $pfManager=NULL;

	var $current_step=FALSE;

	var $lang=FALSE;

	var $module=FALSE;
	var $item=FALSE;
	var $key1=FALSE;
	var $key2=FALSE;

	/**
	 * FlowStepSelector constructor
	 */
	function FlowStepSelector($platform, $prefix, $module, $item, $key1, $key2=FALSE, $pfm_prefix=FALSE, $dbconn=NULL) {
		/* if( $param_prefix === FALSE ) {
			$this->prefix=$GLOBALS["prefix_fw"];
		} else {
			$this->prefix=$param_prefix;
		}
		$this->dbConn=$dbconn; */

		$this->pfManager=new PublicationFlowManager($pfm_prefix, $dbconn);
		$this->lang =& DoceboLanguage::createInstance('pubflow', $platform);

		$this->pfManager->setDataPrefix($prefix);
		$this->module=$module;
		$this->item=$item;
		$this->key1=$key1;
		$this->key2=$key2;
	}


	function getCurrentStep() {
		return $this->pfManager->getCurrentStep($this->module, $this->item, $this->key1, $this->key2);
	}


	function setCurrentStep($cs) {
		$this->current_step=$cs;
	}


	function getDefaultStep($flow_id) {
		return $this->pfManager->getDefaultStep($flow_id);
	}


	function selectNextStep(& $form) {
		$res="";

		$step_info=$this->pfManager->getStepInfo($this->current_step);
		$flow_id=$step_info["flow_id"];
		$ord=$step_info["ord"];
		$all_steps=$this->pfManager->getStepNeighbors($flow_id, $ord);

		$sel_lang=getLanguage();
		$avail_steps=array();
		$stop=0;

		while((list($key, $val) = each($all_steps)) && (!$stop)) {
			if (($this->current_step == $val["step_id"]) || ($this->pfManager->canSetStep($val["st_id"])))
				$avail_steps[$val["step_id"]]=$this->pfManager->getItemLangText("step", $val["step_id"], $sel_lang, "label");
			/*else
				$stop=1;*/
		}

		$res.=$form->getDropdown($this->lang->def("_SELECT_STEP"),"next_step","next_step",	$avail_steps, $this->current_step);

		$res.=$form->getSimpleTextarea($this->lang->def("_STEP_CHANGE_NOTE"), "sc_note", "sc_note");

		$res.=$form->getHidden("old_step", "old_step", $this->current_step);
		$res.=$form->getHidden("old_was_published", "old_was_published", $step_info["is_published"]);

		//-debug-// print_r($all_steps);
		return $res;
	}


	function getNextStep($arr=FALSE) {

		if ($arr === FALSE) {
			$arr=$_POST;
		}

		return $arr["next_step"];
	}


	function setStatusTo($step_id) {
		return $this->pfManager->setStatusTo($step_id, $this->module, $this->item, $this->key1, $this->key2);
	}

	function saveChangeLog($step_id) {
		return $this->pfManager->saveChangeLog($step_id, $this->module, $this->item, $this->key1, $this->key2);
	}

	function stepIsPublished($step_id) {

		$step_info=$this->pfManager->getStepInfo($step_id);

		return $step_info["is_published"];
	}



	function showStatusChangeLog($url, $ini_name="ini") {
		require_once($GLOBALS["where_framework"]. "/lib/lib.typeone.php");
		require_once($GLOBALS["where_framework"]. "/lib/lib.mimetype.php");

		$change_log=$this->pfManager->getChangeLog($this->module, $this->item, $this->key1, $this->key2);


		$res="";
		$sel_lang=getLanguage();

		if (count($change_log) > 0) {

			$ini=importVar($ini_name, true, 0);

			$table=new typeOne($GLOBALS["visuItem"]);
			$res.=$table->OpenTable("");

			$head = array(
				'<img src="'.getPathImage().'documents/updown.gif" alt="'.$this->lang->def("_ALT_CTYPE").'" title="'.$this->lang->def("_ALT_CTYPE").'" />',
				$this->lang->def("_STEP_LABEL"), $this->lang->def("_STEP_CHANGE_USER"),
				$this->lang->def("_STEP_CHANGE_DATE"), $this->lang->def("_STEP_CHANGE_NOTE"));
			$head_type = array('img', '', '', '', '');

			$res.=$table->WriteHeader($head, $head_type);

			$tot=(count($change_log) < ($ini+$GLOBALS["visuItem"])) ? count($change_log) : $ini+$GLOBALS["visuItem"];
			for($i=$ini; $i<$tot; $i++ ) {

				$rowcnt=array();


				switch($change_log[$i]["ctype"]) {
					case "none": {
						$rowcnt[]="&nbsp;";
					} break;
					case "upgrade": {
						$alt=$this->lang->def("_ALT_UPGRADE");
						$rowcnt[]="<img src=\"".getPathImage()."documents/up.gif\" alt=\"".$alt."\" title=\"$alt\" />\n";
					} break;
					case "downgrade": {
						$alt=$this->lang->def("_ALT_DOWNGRADE");
						$rowcnt[]="<img src=\"".getPathImage()."documents/down.gif\" alt=\"".$alt."\" title=\"$alt\" />\n";
					} break;
				}

				$rowcnt[]=$this->pfManager->getItemLangText("step", $change_log[$i]["step_id"], $sel_lang, "label");;
				$rowcnt[]=substr($change_log[$i]["user"], 1);
				$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($change_log[$i]["ctime"]);
				$rowcnt[]=$change_log[$i]["note"];

				$res.=$table->WriteRow($rowcnt);
			}

			$res.=$table->CloseTable();
			$res.=$table->WriteNavBar('',
								$url,
								$ini,
								count($change_log));

		}

		return $res;
	}


}



?>
