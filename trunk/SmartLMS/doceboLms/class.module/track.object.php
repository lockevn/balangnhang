<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Track_Object {
	
	var $idTrack;
	var $idReference;
	var $idUser;
	var $dateAttempt;
	var $status;
	var $objectType;
	
	/** 
	 * object constructor
	 * Table : learning_commontrack
	 * idReference | idUser | idTrack | objectType | date_attempt  | status |
	 **/
	function Track_Object( $idTrack ) {
		
		if($idTrack) { 
			
			$this->idTrack = $idTrack;
			$query = "SELECT `idReference` , `idUser` , `idTrack` , `objectType` , `dateAttempt`, `status`"
					." FROM `".$GLOBALS['prefix_lms']."_commontrack`"
					." WHERE idTrack='".(int)$idTrack."'"
					."   AND objectType='".$this->objectType."'";
			$rs = mysql_query( $query ) or
					errorCommunication( 'Track_Object.Track_Object' );
			if( mysql_num_rows( $rs ) == 1 ) {
				list( $this->idReference, $this->idUser, $this->idTrack, 
					  $this->objectType, $this->dateAttempt, $this->status ) = mysql_fetch_row( $rs );
			}         
		}
	}  
	
	/** 
	 * object constructor
	 * @return bool
	 * create a row in global track
	 **/
	function createTrack( $idReference, $idTrack, $idUser, $dateAttempt, $status, $objectType = FALSE ) {
		
		$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_commontrack "
				."( `idReference` , `idUser` , `idTrack` ,"
				." `objectType` , `firstAttempt`, `dateAttempt` , `status` )"
				." VALUES ("
				." '".(int)$idReference."',"
				." '".(int)$idUser."',"
				." '".(int)$idTrack."',"
				." '".(($objectType==FALSE)?($this->objectType):($objectType))."',"
				." '".date("Y-m-d H:i:s")."', "
				." '".$dateAttempt."', "
				." '".$status."'"
				." )";
		
		$result = mysql_query($query) 
			or errorCommunication( 'createTrack' );
		
		if(isset($this)) {
			
			$this->idReference = $idReference;
			$this->idUser = $idUser;
			$this->idTrack = $idTrack;
			$this->objectType = (($objectType==FALSE)?($this->objectType):($objectType));
			$this->dateAttempt = $dateAttempt;
			$this->status = $status;
						
			$this->_setCourseCompleted();
		}
	}
	
	function getObjectType() {
		return $this->objectType;
	}
	
	function getDate() {
		return $this->dateAttempt;
	}
	
	function setDate( $new_date ) {
		$this->dateAttempt = $new_date;
	}
	
	function getStatus() {
		return $this->status;
	}
	
	function setStatus( $new_status ) {
		$this->status = $new_status;
	}
	
	function update() {
		
		$query = "UPDATE ".$GLOBALS['prefix_lms']."_commontrack SET "
				." dateAttempt ='".$this->dateAttempt."',"
				." status ='".$this->status."'"
				." WHERE idTrack = '".(int)$this->idTrack."' AND objectType = '".$this->objectType."'";
//$GLOBALS['page']->add( '<div>STATO CORSO AGGIORNATO = '.$this->idTrack.', '.$this->objectType.'</div>' , 'content' );		
		mysql_query( $query );
		
		$this->_setCourseCompleted();
				
	}
	
	function _setCourseCompleted() {
		
		if( $this->status == 'completed' || $this->status == 'passed' ) {
			
			if(isset($_SESSION['idCourse'])) {
				
				$idCourse = $_SESSION['idCourse'];
			} else {
				
				// the only way is a direct query :(
				$query = "SELECT idCourse "
					."FROM ".$GLOBALS['prefix_lms']."_organization "
					."WHERE idOrg = '".(int)$this->idReference."' ";
				list($idCourse) = mysql_fetch_row(mysql_query($query));
			}
//$GLOBALS['page']->add( '<div>CORSO = '.$idCourse.'</div>' , 'content' );
			$useridst = $this->idUser;
			require_once( $GLOBALS['where_lms'].'/modules/organization/orglib.php' );
			$repoDb = new OrgDirDb( $idCourse );
			
			$item = $repoDb->getFolderById( $this->idReference );
			$values = $item->otherValues;
			$isTerminator = $values[ORGFIELDISTERMINATOR];
			
			if( $isTerminator ) {
				require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
				require_once($GLOBALS['where_lms'].'/lib/lib.stats.php');
				saveTrackStatusChange((int)$useridst, (int)$idCourse , _CUS_END);
//$GLOBALS['page']->add( '<div>SAVETRACKSTATUSCHANGE('.$useridst.', '.$idCourse.', '._CUS_END.')</div>' , 'content' );
			}
			
		}
		
	}
	
	/**
	 * print in standard output 
	 **/
	function loadReport( $idUser = false ) {
		
	}
	
	/**
	 * print in standard output the details of a track 
	 **/
	function loadReportDetail( $idUser, $idItemDetail ) {
		
	}
	
	/**
	* print in standard output 
	 * @return nothing
	 **/
	function loadObjectReport( ) {
		return;
	}
	
	/**
	 * static function to fast compute prerequisites
	 **/
	function isPrerequisitesSatisfied( $arrId, $idUser ) {
		
		if( is_string($arrId) )
			if( strlen($arrId)>0 )
				if( $arrId{0} == ',' )
					$arrId = substr($arrId,1);
		if( $arrId == '' ) { 
			return TRUE;
		} else {
			// in this brach we extract two array
			// 1) $idList array of id for use in query
			// 2) $arrPre array composed by $id => $status
			$idList = array();
			$arrTokens = explode( ',', $arrId );
			while( ($val = current( $arrTokens )) !== FALSE ) {
				$arrPeer = explode( '=', $val );
				if( $arrPeer[0] !== 'rray' ) { 	// patch to skip wrong prerequisites 
												// saved in db in first version of 3.0.1
					if( count($arrPeer) > 1 ) {
						$arrPre[$arrPeer[0]] = $arrPeer[1];
					} else {
						$arrPre[$arrPeer[0]] = 'completed';
					}
					$idList[] = $arrPeer[0];
				}
				next( $arrTokens );				
			}
		}
		if(empty($idList)) {
			return true;
		} else {
			$query = "SELECT idReference, status "
					." FROM ".$GLOBALS['prefix_lms']."_commontrack"
					." WHERE ((idReference IN ( ".implode( ',', $idList)." ))"
					."   AND (idUser = '".(int)$idUser."'))";
		}
				// ."   AND ((status = 'completed') OR (status = 'passed')))";
		$rs = mysql_query( $query )
			or die( "Error in query=[ $query ] ". mysql_error() );
			
		//echo "\n".'<!-- sto controllando i prerequisiti con questa query : '.$query.' -->';
		while( list( $id, $status ) = mysql_fetch_row( $rs ) ) 
			$arrStatus[$id] = $status;
		
		//if(isset($arrStatus)) echo "\n".'<!-- gli stati letti per i prerequisiti chiesti sono : '.print_r($arrStatus, true).' -->';
		//else echo "\n".'<!-- nessuno dei prerequisiti � stato tracciato -->';
		foreach( $arrPre as $id => $status ) {
			switch( $status ) {
				case 'NULL':
					if( isset( $arrStatus[$id] ) )
						return FALSE;
				break;
				case 'completed':
				case 'passed':
					if( !isset( $arrStatus[$id] ) 
						|| ($arrStatus[$id] != 'completed' && $arrStatus[$id] != 'passed') )
						return FALSE;
				break;
				case 'failed':
				case 'incomplete':
				case 'not attempted':
				case 'attempted':
				case 'ab-initio':
					if( isset( $arrStatus[$id] ) 
						&& ($arrStatus[$id] != 'failed' 
						&&  $arrStatus[$id] != 'incomplete'
						&&  $arrStatus[$id] != 'not attempted'
						&&  $arrStatus[$id] != 'attempted'
						&&  $arrStatus[$id] != 'ab-initio') )
						return FALSE;
				break;
			}
		}
			
		return TRUE;
	}
	
	/**
	 * static function to get status
	 **/
	function getStatusFromId( $idReference, $idUser ) {
		
		$query = "SELECT status "
				." FROM ".$GLOBALS['prefix_lms']."_commontrack"
				." WHERE (idReference = ".(int)$idReference.")"
				."   AND (idUser = '".(int)$idUser."')";
		$rs = mysql_query( $query )
			or die( "Error in query=[ $query ] ". mysql_error() );
			
		if( mysql_num_rows( $rs ) == 0 )
			return 'not attempted';
		else {
			list( $status ) = mysql_fetch_row( $rs );
			return $status;
		}				
	}
	/**
	 * @return idTrack if found else false
	 **/
	function getIdTrackFromCommon( $idReference, $idUser) {
		
		$query = "SELECT idTrack "
				." FROM ".$GLOBALS['prefix_lms']."_commontrack"
				." WHERE (idReference = ".(int)$idReference.")"
				."   AND (idUser = '".(int)$idUser."')";
		$rs = mysql_query( $query )
			or die( "Error in query=[ $query ] ". mysql_error() );
			
		if( mysql_num_rows( $rs ) == 0 )
			return false;
		else {
			list( $idTrack ) = mysql_fetch_row( $rs );
			return $idTrack;
		}	
	}
	
	function delIdTrackFromCommon( $idReference ) {
		
		$query = "DELETE FROM ".$GLOBALS['prefix_lms']."_commontrack"
				." WHERE (idReference = ".(int)$idReference.")";
		$rs = mysql_query( $query )
			or die( "Error in query=[ $query ] ". mysql_error() );
	}
	
	
	/**
	 * @return bool	true if this object use extra colum in user report
	 */
	function otherUserField() {
		return false;
	}
	
	/**
	 * @return array	an array with the header of extra colum
	 */
	function getHeaderUserField() {
		return array();
	}
	
	/**
	 * @return array	an array with the extra colum
	 */
	function getUserField() {
		return array();
	}
	
	
	function updateObjectTitle($idResource, $objectType, $new_title) {
		
		$new_title = str_replace('/', '', $new_title);
		
		$re = true;
		
		$query_search = "
		SELECT path
		FROM ".$GLOBALS['prefix_lms']."_homerepo 
		WHERE idResource = '".(int)$idResource."'  
			AND objectType = '".$objectType."'
		LIMIT 1";
		$re_search = mysql_query($query_search);
		while(list($path) = mysql_fetch_row($re_search)) {
			
			$path_piece = explode('/', $path);
			unset($path_piece[count($path_piece)-1]);
			$new_path = implode('/', $path_piece).   "/" . $new_title;
			
			$query_lo = "
			UPDATE ".$GLOBALS['prefix_lms']."_homerepo
			SET path = '".$new_path."', title = '".$new_title."' 
			WHERE idResource = '".(int)$idResource."'  
				AND objectType = '".$objectType."'";
			$re &= mysql_query($query_lo);
		}
		
		$query_lo = "
		UPDATE ".$GLOBALS['prefix_lms']."_organization
		SET title = '".$new_title."' 
		WHERE idResource = '".(int)$idResource."'  
			AND objectType = '".$objectType."'";
		$re &= mysql_query($query_lo);
		
		$query_search = "
		SELECT path
		FROM ".$GLOBALS['prefix_lms']."_repo 
		WHERE idResource = '".(int)$idResource."'  
			AND objectType = '".$objectType."'
		LIMIT 1";
		$re_search = mysql_query($query_search);
		while(list($path) = mysql_fetch_row($re_search)) {
			
			$path_piece = explode('/', $path);
			unset($path_piece[count($path_piece)-1]);
			$new_path = implode('/', $path_piece).   "/" . $new_title;
			
			$query_lo = "
			UPDATE ".$GLOBALS['prefix_lms']."_repo
			SET path = '".$new_path."', title = '".$new_title."' 
			WHERE idResource = '".(int)$idResource."'  
				AND objectType = '".$objectType."'";
			$re &= mysql_query($query_lo);
		}
		
		return $re;
	}
	
}

?>
