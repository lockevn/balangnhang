<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004              */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Learning_Test extends Learning_Object {
	
	var $id;
	
	var $idAuthor;
	
	var $title;
	
	var $back_url;
	
	/**
	 * function learning_Test()
	 * class constructor
	 **/
	function Learning_Test( $id = NULL ) {
		parent::Learning_Object( $id );
		if( $id !== NULL ) {
			list( $this->idAuthor, $this->title ) = mysql_fetch_row(mysql_query("
			SELECT author, title
			FROM ".$GLOBALS['prefix_lms']."_test
			WHERE idTest = '".(int)$id."'"));
		}
	}
	
	function getObjectType() {
		return 'test';
	}
	
	function getParamInfo() {
		
		return false;
	}
	
	/**
	 * function create( $back_url )
	 * @param string $back_url contains the back url
	 * @return nothing
	 * attach the id of the created object at the end of back_url with the name, in attach the result in create_result
	 *
	 * static
	 **/
	function create( $back_url ) {
		$this->back_url = $back_url;
		
		unset($_SESSION['last_error']);
		
		require_once( $GLOBALS['where_lms'].'/modules/test/test.php' );
		addtest( $this );
	}
	
	/**
	 * function edit
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url
	 * @return nothing
	 * attach in back_url id_lo that is passed $id and attach edit_result with the result of operation in boolean format 
	 **/
	function edit( $id, $back_url ) {
		$this->id = $id;
		$this->back_url = $back_url;
		
		unset($_SESSION['last_error']);
		
		require_once( $GLOBALS['where_lms'].'/modules/test/test.php' );
		modtestgui( $this );
	}
	
	/**
	 * function del
	 * @param int $id contains the resource id
	 * @param string $back_url contains the back url (not used yet)
	 * @return false if fail, else return the id lo
	 **/
	function del( $id, $back_url = NULL ) {
		checkPerm('view', false, 'storage');
		
		unset($_SESSION['last_error']);
		
		// finding track
		$re_quest_track = mysql_query("
		SELECT idTrack 
		FROM ".$GLOBALS['prefix_lms']."_testtrack 
		WHERE idTest = '".$id."'");
		$id_tracks = array();
		while(list($id_t) = mysql_fetch_row($re_quest_track)) {
			$id_tracks[] = $id_t;
		}
		
		//finding quest
		$reQuest = mysql_query("
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$id."' AND q.type_quest = t.type_quest");
		if(!mysql_num_rows($reQuest)) return true;
		//deleting answer
		while( list($idQuest, $type_quest, $type_file, $type_class) = mysql_fetch_row($reQuest) ) {
			
			require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
			
			$quest_obj = eval("return new $type_class( $idQuest );");
			if(!$quest_obj->del())  {
				$_SESSION['last_error'] = def('_ERRREMANSWER');
				return false;
			}
			if(!mysql_query("
			DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
			WHERE idQuest = '".(int)$idQuest."'")) {
				$_SESSION['last_error'] = def('_ERRREMANSWER');
				return false;
			}
		}
		// delete tracking
		if(!empty($id_tracks)) {
			
			if(!mysql_query("
			DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_page 
			WHERE idTrack IN ('".implode(',', $id_tracks)."') ")) {
				$_SESSION['last_error'] = def('_ERRREMANSWER');
				return false;
			}
		}
		
		if( !mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack WHERE idTest = '".$id."'") ) {
			$_SESSION['last_error'] = def('_ERRREMTRACK');
			return false;
		}
		if( !mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_testquest WHERE idTest = '".$id."'") ) {
			$_SESSION['last_error'] = def('_ERRREMQUEST');
			return false;
		}
		if( !mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_test WHERE idTest = '".$id."'") ) {
			$_SESSION['last_error'] = def('_ERRREMTEST');
			return false;
		}
		return $id;
	}
	
	/**
	 * function copy( $id, $back_url )
	 * @param int $id contains the resource id
	 * @param string $back_url contain the back url (not used yet)
	 * @return int $id if success FALSE if fail
	 **/
	function copy( $id, $back_url = NULL ) {
		
		//find source info
		$test_info = mysql_fetch_array(mysql_query("
		SELECT author, title, description, 
			point_type, point_required, 
			display_type, order_type, shuffle_answer, question_random_number, 
			save_keep, mod_doanswer, can_travel, show_only_status, 
			show_score, show_score_cat, show_doanswer, show_solution, 
			time_dependent, time_assigned, penality_test, 
			penality_time_test, penality_quest, penality_time_quest, max_attempt 
		FROM ".$GLOBALS['prefix_lms']."_test 
		WHERE idTest = '".(int)$id."'"));
		
		//insert new item
		$ins_query = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_test
		SET author = '".(int)$test_info['author']."', 
			title = '".mysql_escape_string($test_info['title'])."', 
			description = '".mysql_escape_string($test_info['description'])."', 
			point_type = '".(int)$test_info['point_type']."', 
			point_required = '".(int)$test_info['point_required']."', 
			display_type = '".(int)$test_info['display_type']."', 
			order_type = '".(int)$test_info['order_type']."', 
			shuffle_answer = '".(int)$test_info['shuffle_answer']."',
			question_random_number = '".(int)$test_info['question_random_number']."',
			save_keep = '".(int)$test_info['save_keep']."', 
			mod_doanswer = '".(int)$test_info['mod_doanswer']."', 
			can_travel = '".(int)$test_info['can_travel']."',
			show_only_status = '".(int)$test_info['show_only_status']."',
			show_score = '".(int)$test_info['show_score']."', 
			show_score_cat = '".(int)$test_info['show_score_cat']."', 
			show_doanswer = '".(int)$test_info['show_doanswer']."', 
			show_solution = '".(int)$test_info['show_solution']."', 
			time_dependent = '".(int)$test_info['time_dependent']."', 
			time_assigned = '".(int)$test_info['time_assigned']."', 
			penality_test = '".(int)$test_info['penality_test']."', 
			penality_time_test = '".(int)$test_info['penality_time_test']."', 
			penality_quest = '".(int)$test_info['penality_quest']."', 
			penality_time_quest = '".(int)$test_info['penality_time_quest']."', 
			max_attempt = '".(int)$test_info['max_attempt']."'";
		if(!mysql_query($ins_query)) return false;
		list($id_new_test) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		if(!$id_new_test) return false;
		
		//finding quest
		$reQuest = mysql_query("
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$id."' AND q.type_quest = t.type_quest");
		//retriving quest
		while( list($idQuest, $type_quest, $type_file, $type_class) = mysql_fetch_row($reQuest) ) {
			
			require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
			$quest_obj = new $type_class( $idQuest );
			$new_id = $quest_obj->copy($id_new_test);
			if(!$new_id) {
				$this->del( $id_new_test );
				
				$_SESSION['last_error'] = def('_TEST_ERR_COPY_QUEST').' : '.$type_class.'( '.$idQuest.' )';
				return false;
			}
		}
		return $id_new_test;
	}
	
	/**
	 * function play( $id, $id_param, $back_url )
	 * @param int $id contains the resource id
	 * @param int $id_param contains the id needed for params retriving
	 * @param string $back_url contain the back url
	 * @return nothing return
	 **/
	function play( $id, $id_param, $back_url ) {
		require_once( $GLOBALS['where_lms'].'/modules/test/do.test.php' );
		
		$this->id = $id;
		$this->back_url = $back_url;
		
		$step = importVar('next_step');
		switch($step) {
			case "test_review" : {
				review($this, $id_param);
			};break;
			case "play" : {
				playTestDispatch($this, $id_param);
			};break;
			default : {
				intro($this, $id_param);
			};break;
		}
	}
	
	function canBeMilestone() {
		return TRUE;
	}
}

?>
