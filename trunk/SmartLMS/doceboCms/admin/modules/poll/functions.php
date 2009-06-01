<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


class CmsPoll {
	/** RegionalSettingsManager object */


	/**
	 * PublicationFlow constructor
	 * @param string $param_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function CmsPoll ($param_prefix=FALSE, $dbconn=NULL) {

	}


}




class PollManager {
	/** db connection */
	var $dbconn;
	/** prefix for the database */
	var $prefix;

	var $localized_strings=array();

	/**
	 * PollManager constructor
	 * @param string $param_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function PollManager( $param_prefix = FALSE, $dbconn = NULL ) {
		if( $param_prefix === FALSE ) {
			$this->prefix=$GLOBALS["prefix_cms"];
		} else {
			$this->prefix=$param_prefix;
		}
		$this->dbConn=$dbconn;
	}


	/**
	 * @return string table name for the list of polls
	 **/
	function _getPollTable() {
		return $this->prefix."_poll";
	}


	/**
	 * @return string table name for the list of answers
	 **/
	function _getAnswerTable() {
		return $this->prefix."_poll_answer";
	}


	/**
	 * @return string table name for store user's vote 
	 **/
	function _getVoteTable() {
		return $this->prefix."_poll_vote";
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

	/**
	 *
	 * @param int poll_id the id of the poll
	 * @return array with the row's column of the specified poll
	 *
	 */
	function getPollInfo($poll_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getPollTable()." WHERE poll_id='".$poll_id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row;
		}

		return $res;
	}


	/**
	 * return an array with all the region_id presents on system
	 * @return array with all the region_id in system (index in array is numeric
	 *			starting from 0, value is region_id)
	 */
	function getAllPoll() {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getPollTable()." ORDER BY poll_id DESC";
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
	function getAllAnswers($poll_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getAnswerTable()." WHERE poll_id='".$poll_id."' ORDER BY ord";
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
	function getAnswerInfo($answer_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getAnswerTable()." WHERE answer_id='".$answer_id."' ORDER BY ord";
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

		return $locstr[$type][$id][$language][$name];
	}


	/**
	 */
	function savePoll($data) {

		$id=(int)$data["poll_id"];

		if ($id == 0) {
			$qtxt="INSERT INTO ".$this->_getPollTable()." (question) VALUES('".$data["question"]."')";
			$id=$this->_executeInsert($qtxt);
		}
		else {
			$qtxt="UPDATE ".$this->_getPollTable()." SET question='".$data["question"]."' WHERE poll_id='$id'";
			$q=$this->_executeQuery($qtxt);
		}

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
	function saveAnswer($data) {
		require_once($GLOBALS['where_framework']."/lib/lib.aclmanager.php");
		$acl=new DoceboACLManager();

		$poll_id=(int)$data["poll_id"];
		$answer_id=(int)$data["answer_id"];
		$answer_txt=$data["answer_txt"];

		if ($answer_id == 0) {
			$ord=$this->getLastOrd($this->_getAnswerTable())+1;
			$qtxt ="INSERT INTO ".$this->_getAnswerTable()." (poll_id, ord, answer_txt) ";
			$qtxt.="VALUES ('".$poll_id."', '".$ord."', '".$answer_txt."')";

			$answer_id=$this->_executeInsert($qtxt);
		}
		else {
			$qtxt="UPDATE ".$this->_getAnswerTable()." SET answer_txt='$answer_txt' WHERE answer_id='$answer_id'";
			$q=$this->_executeQuery($qtxt);
		}

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

	
	function deletePoll($poll_id) {
		
		$qtxt="DELETE FROM ".$this->_getPollTable()." WHERE poll_id='".$poll_id."'";
		$q=$this->_executeQuery($qtxt);
		
		$qtxt="DELETE FROM ".$this->_getAnswerTable()." WHERE poll_id='".$poll_id."'";
		$q=$this->_executeQuery($qtxt);

		$qtxt="DELETE FROM ".$this->_getVoteTable()." WHERE poll_id='".$poll_id."'";
		$q=$this->_executeQuery($qtxt);
		
	}
	
	
	function deleteAnswer($answer_id) {
		$qtxt="DELETE FROM ".$this->_getAnswerTable()." WHERE answer_id='".$answer_id."'";
		$q=$this->_executeQuery($qtxt);				
	}	

	
	function getVotedPoll() {
		if (isset($_COOKIE["voted_poll"]))
			$voted_arr=unserialize(urldecode($_COOKIE["voted_poll"]));
		else
			$voted_arr=array();

		return $voted_arr;
	}
	
	
	function addVotedPoll($poll_id) {		
		$voted_arr=$this->getVotedPoll();
		$voted_arr[]=$poll_id;
		
		$ten_days=3600*24*10;		
		setcookie("voted_poll", urlencode(serialize($voted_arr)), time()+$ten_days);
	}	

	
	function alreadyVoted($poll_id) {
		return in_array($poll_id, $this->getVotedPoll());		
	}
	
	
	function savePollVote($poll_id, $answer_id){
		
		if ($this->alreadyVoted($poll_id)) {
			return 0;
		}
		
		$this->addVotedPoll($poll_id);
		
		$qtxt="SELECT answer_id FROM ".$this->_getVoteTable()." WHERE answer_id='".$answer_id."'";
		$q=$this->_executeQuery($qtxt);
		
		if (($q) && (mysql_num_rows($q) > 0)) {
			$qtxt ="UPDATE ".$this->_getVoteTable()." SET votes=votes+1 ";
			$qtxt.="WHERE answer_id='".$answer_id."' AND poll_id='".$poll_id."'";
			$q=$this->_executeQuery($qtxt);
		}
		else if (($q) && (mysql_num_rows($q) == 0)) {
			$qtxt ="INSERT INTO ".$this->_getVoteTable()." (poll_id, answer_id, votes) ";
			$qtxt.="VALUES ('".$poll_id."', '".$answer_id."', '1')";
			$q=$this->_executeQuery($qtxt);
		}

	}
	
	
	function getVotesTotal($poll_id) {
		
		$qtxt="SELECT votes FROM ".$this->_getVoteTable()." WHERE poll_id='".$poll_id."'";
		$q=$this->_executeQuery($qtxt);

		$tot=0;		
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$tot=$tot+$row["votes"];
			}
		}
		
		return $tot;		
	}
	
	
	function getPollResult($poll_id) {

		$res=array();

		$fields="t1.answer_id, t1.answer_txt, t2.votes";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getAnswerTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$this->_getVoteTable()." as t2 ";
		$qtxt.="ON (t1.answer_id = t2.answer_id)";
		$qtxt.="WHERE t1.poll_id='".$poll_id."' AND t2.poll_id=t1.poll_id ORDER BY t2.votes DESC";
		$q=$this->_executeQuery($qtxt);

		$tot=$this->getVotesTotal($poll_id);
		
		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				
				$row["percent"]=number_format($row["votes"]*100/$tot, 1, '.', '');
				$res[$i]=$row;

				$i++;
			}
		}

		return $res;		
	}
	
	
}

?>
