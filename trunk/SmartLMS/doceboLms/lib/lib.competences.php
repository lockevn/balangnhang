<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define('_TYPE_0','score');
define('_TYPE_1','flag');

define('_COMP_TYPE_0','skill');
define('_COMP_TYPE_1','attitude');
//define('_COMP_TYPE_2','_unknown');

define('_LEV_STUD', 3);

define('_ASSIGN_MANUAL', 'manual');
define('_ASSIGN_COURSE', 'course');

require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

class Competences_Manager {

	function Competences_Manager() { }

	function GetAllTypes($keys=false) {
		if ($keys) {
			return array(_TYPE_0=>_TYPE_0,_TYPE_1=>_TYPE_1);
		} else{
			return array(_TYPE_0,_TYPE_1);
		}
	}

	function GetCompetenceType($id) {
		$query="SELECT type FROM ".$GLOBALS['prefix_lms']."_competence WHERE id_competence=$id";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		return $row[0];
	}

	function GetAllCompetenceTypes($keys=false) {
		if ($keys)
		//return array(_COMP_TYPE_0=>_COMP_TYPE_0, _COMP_TYPE_1=>_COMP_TYPE_1, _COMP_TYPE_2=>_COMP_TYPE_2);
			return array(_COMP_TYPE_0=>_COMP_TYPE_0, _COMP_TYPE_1=>_COMP_TYPE_1);
		else
		//return array(_COMP_TYPE_0, _COMP_TYPE_1, _COMP_TYPE_2);
			return array(_COMP_TYPE_0, _COMP_TYPE_1);
	}

	function GetCompetenceCategory($id_cat, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		if ($id_cat==0) {
			$lang =& DoceboLanguage::createInstance('competences', 'lms');
			$temp=array('name'=>$lang->def('_NULL_CATEGORY'), 'description'=>'');
			return $temp;
		}
	/*$query="SELECT t2.text_name as name, t2.text_desc as description ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1, ".$GLOBALS['prefix_lms']."_competence_category_text as t2 ".
		   "WHERE t1.id_competence_category=$id_cat AND t2.lang_code='".getLanguage()."' AND t2.id_category=$id_cat";*/
		$query="SELECT t2.text_name as name, t2.text_desc as description ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_category_text as t2 ON ".
		   "(t1.id_competence_category=t2.id_category AND t2.lang_code='".$lang_code."') ".
		   "WHERE t1.id_competence_category=$id_cat";
		$res=mysql_query($query);
		$row=mysql_fetch_assoc($res);
		return $row;
	}



	function GetCompetenceCategoryAllLanguages($id_cat) {
		if ($id_cat<=0) return false;
		$langs = $GLOBALS['globLangManager']->getAllLangCode();
		$query="SELECT t2.text_name as name, t2.text_desc as description, t2.lang_code as lang_code ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_category_text as t2 ON ".
		   "(t1.id_competence_category=t2.id_category) ".
		   "WHERE t1.id_competence_category=$id_cat";
		$res=mysql_query($query);
		$output=array();
		while ($row=mysql_fetch_assoc($res)) {
			$output[$row['lang_code']]=array('name'=>$row['name'], 'description'=>$row['description']);
		}
		foreach ($langs as $lang_code) {
			if (!isset($output[$lang_code]))
			$output[$lang_code]=array('name'=>'', 'description'=>'');
		}
		return $output;
	}



	function GetCompetencesCategories($nocat=false, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$data=array();
	/*$query="SELECT t1.*, t2.text_name as name, t2.text_desc as description ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1, ".
		   $GLOBALS['prefix_lms']."_competence_category_text as t2 ".
		   "WHERE t1.id_competence_category=t2.id_category AND t2.lang_code='".getLanguage()."'";*/
		$query="SELECT t1.id_competence_category, t2.text_name as name, t2.text_desc as description ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_category_text as t2 ON ".
		   "(t1.id_competence_category=t2.id_category AND t2.lang_code='".$lang_code."')";

		//filter, if set
		if ($nocat) {
			$lang =& DoceboLanguage::createInstance('competences', 'lms');
			$data[] = array('id_competence_category'=>0,'name'=>$lang->def('_NULL_CATEGORY'),'description'=>'');
		}
		if ($res=mysql_query($query)) {
			while ($row=mysql_fetch_assoc($res)) {
				$data[]=$row;
			}
			return $data;
		} else {
			return false;
		}
	}


	function GetCompetencesCount($id_cat/*=0*/) {
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence WHERE id_category=$id_cat";
		//if ($id_cat!=0) $query.=" WHERE id_category=$id_cat";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		return $row[0];
	}


	function GetCompetencesCategoriesList($nocat=false, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$data=array();
	/*$query="SELECT t1.id_competence_category as id, t2.text_name as name ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1, ".
		   $GLOBALS['prefix_lms']."_competence_category_text as t2 ".
		   "WHERE t1.id_competence_category=t2.id_category AND t2.lang_code='".getLanguage()."'";*/
		$query="SELECT t1.id_competence_category as id, t2.text_name as name ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_category_text as t2 ON ".
		   "(t1.id_competence_category=t2.id_category AND t2.lang_code='".$lang_code."')";

		//filter, if set
		if ($nocat) {
			$lang =& DoceboLanguage::createInstance('competences', 'lms');
			$data[0]=$lang->def('_NULL_CATEGORY');
		}
		if ($res=mysql_query($query)) {
			while ($row=mysql_fetch_assoc($res)) {
				$data[ $row['id'] ] = $row['name'] ;
			}
			return $data;
		} else {
			return false;
		}
	}


	function GetCompetencesList($id_cat=false, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$data=array();
	/*$query="SELECT t1.id_competence_category as id, t2.text_name as name ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_category as t1, ".
		   $GLOBALS['prefix_lms']."_competence_category_text as t2 ".
		   "WHERE t1.id_competence_category=t2.id_category AND t2.lang_code='".getLanguage()."'";*/
		$query="SELECT t1.id_competence as id, t2.text_name as name ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_text as t2 ON ".
		   "(t1.id_competence=t2.id_competence AND t2.lang_code='".$lang_code."')".
		(is_int($id_cat) ? " WHERE t1.id_category=$id_cat" : "");

		//filter, if set
		if ($res=mysql_query($query)) {
			while ($row=mysql_fetch_assoc($res)) {
				$data[ $row['id'] ] = $row['name'] ;
			}
			return $data;
		} else {
			return false;
		}
	}


	function GetCompetencesGrouped($groupby=true, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$output=array();
		$query="SELECT t1.*, t2.text_name as name, t2.text_desc as description FROM ".
		$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_text as t2 ON ".
		   "(t1.id_competence=t2.id_competence AND t2.lang_code='".$lang_code."') ";
		if ($groupby) $query.= " ORDER BY name";
		if ($res=mysql_query($query)) {
			while ($row=mysql_fetch_assoc($res)) {
				$output[ $row['id_category'] ][] = $row;
			}
			return $output;
		}
		return false;
	}


	function GetCompetences($categories=false, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		//reset old data
		$data=array();

		//check if $categories is a string or an array or an integer
		$_conds='';
		$_where='';
		//if ($categories) {
		if (is_array($categories)) {
			//...
		} elseif (is_string($categories)) {
			$_conds.=", ".$GLOBALS['prefix_lms']."_competence_category as t3 ";
			$_where.=" WHERE t1.id_category=t3.id_competence_category AND t2.text_name='$categories'";
		} elseif (is_int($categories)) {
			$_conds.='';
			$_where.=" WHERE t1.id_category=$categories";
		} else {
			return false; //must be one of the types abov
		}
		//}

		//compose query string
		$query="SELECT t1.*, t2.text_name as name, t2.text_desc as description FROM ".
		$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_text as t2 ON ".
		   "(t1.id_competence=t2.id_competence AND t2.lang_code='".$lang_code."') ";
		$query.=$_conds.' '.$_where;

		//execute query and extrapolate values
		$res=mysql_query($query);
		while ($row=mysql_fetch_assoc($res)) {
			$data[]=$row;
		}
		return $data;
	}

	function GetCompetence($id, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
	/*$query="SELECT t1.id_competence as id, t1.id_category as category, t1.type as type, t1.competence_type as comp_type, ".
		   "t1.score as score, t2.text_name as name, t2.text_desc as description  ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence as t1, ".$GLOBALS['prefix_lms']."_competence_text as t2 ".
		   "WHERE t1.id_competence=$id AND t2.id_competence=$id AND t2.lang_code='".getLanguage()."'";*/
		$query="SELECT t1.id_competence as id, t1.id_category as category, t1.type as type, t1.competence_type as comp_type, ".
		   "t1.score_min as score_min, t1.score as score, t2.text_name as name, t2.text_desc as description  ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".$GLOBALS['prefix_lms']."_competence_text as t2 ".
		   "ON (t1.id_competence=t2.id_competence AND t2.lang_code='".$lang_code."') WHERE t1.id_competence=$id";
		if ($res=mysql_query($query)) {
			$data=mysql_fetch_assoc($res);
			return $data;
		} else {
			return false;
		}
	}


	function GetCompetenceAllLanguages($id) {
		$langs = $GLOBALS['globLangManager']->getAllLangCode();
		$query="SELECT t2.text_name as name, t2.text_desc as description, t2.lang_code as lang_code ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".$GLOBALS['prefix_lms']."_competence_text as t2 ".
		   "ON (t1.id_competence=t2.id_competence) WHERE t1.id_competence=$id";
		if ($res=mysql_query($query)) {
			$output=array();
			while ($row=mysql_fetch_assoc($res)) {
				$output[$row['lang_code']]=array('name'=>$row['name'], 'description'=>$row['description']);
			}
			foreach($langs as $lang_code) {
				if (!isset($output[$lang_code]))
				$output[$lang_code]=array('name'=>'', 'description'=>'');
			}
			return $output;
		} else {
			return false;
		}
	}




	function DeleteCompetence($id) {
		$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_competence=$id";
		if (mysql_query($query)) {
			$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence_required WHERE id_competence=$id";
			if (mysql_query($query)) {
				$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_competence=$id";
				if (mysql_query($query)) {
					$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence_text WHERE id_competence=$id";
					if (mysql_query($query)) {
						$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence WHERE id_competence=$id";
						return mysql_query($query);
					}
				}
			}
		}
		return false;
	}


	function DeleteCategory($id,$move) {
		$query="UPDATE ".$GLOBALS['prefix_lms']."_competence SET id_category=$move WHERE id_category=$id";
		if (mysql_query($query)) {
			$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence_category WHERE id_competence_category=$id";
			if (mysql_query($query)) {
				$query="DELETE FROM ".$GLOBALS['prefix_lms']."_competence_category_text WHERE id_category=$id";
				return mysql_query($query);
			}
		}
		return false;
	}


	function GetCourseCompetences($id_course, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$output=array();
		$query="SELECT t1.id_competence as id, t1.type as type, t1.score as score, t1.competence_type as comp_type, ".
		   "t1.score_min as score_min, t1.score as score_max, t2.text_name as name, ".
		   "t2.text_desc as description, t3.score as course_score FROM ".
		$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_text as t2 ".
		   "ON (t1.id_competence=t2.id_competence AND t2.lang_code='".$lang_code."'), ".
		$GLOBALS['prefix_lms']."_competence_course as t3 ".
		   "WHERE t3.id_competence=t1.id_competence AND t3.id_course=$id_course";
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) {
			$output[]=$row;
		}
		return $output;
	}

	function GetCourseCompetencesIds($id_course,$which=false, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$output=array();
		$query="SELECT t1.id_competence, t3.score FROM ".
		$GLOBALS['prefix_lms']."_competence as t1 LEFT JOIN ".
		$GLOBALS['prefix_lms']."_competence_text as t2 ".
		   "ON (t1.id_competence=t2.id_competence AND t2.lang_code='".$lang_code."'), ".
		$GLOBALS['prefix_lms']."_competence_course as t3 ".
		   "WHERE t3.id_competence=t1.id_competence AND t3.id_course=$id_course";
		if ($which=='flag' || $which=='score') {
			$query.=" AND t1.type='$which'";
		}
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) {
			$output[$row['id_competence']]=$row['score'];
		}
		return $output;
	}

	function GetCourseScore($id_course,$id_comp) {
		$query="SELECT score FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_course=$id_course AND id_competence=$id_comp";
		if ($res=mysql_query($query)) {
			if (mysql_num_rows($res)>0) {
				$row=mysql_fetch_row($res);
				return $row[0];
			} else
			return '';
		} else
		return false;
	}

	function GetCourseScores($id_course) {
		$output=array();
		$query="SELECT id_competence, score FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_course=$id_course";
		if ($res=mysql_query($query)) {
			while ($row=mysql_fetch_row($res)) {
				$output[$row[0]]=$row[1];
			}
			return $output;
		} else
		return false;
	}

	function GetCompetenceMinScore($id_comp) {
		$query="SELECT score_min FROM ".$GLOBALS['prefix_lms']."_competence WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		return $row[0];
	}


	function UpdateUserScore($id_user,$id_comp,$score_init,$score_got) {
		//if (!$score_init && !$score_got) return true;
		$query="SELECT * FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_competence=$id_comp AND id_user=$id_user";
		$res=mysql_query($query);
		if (mysql_num_rows($res)>0) {
			if ($score_init=='' && $score_got=='') return $this->DeleteUserScore($id_user,$id_comp);
			if ($score_init=='') $score_init='0';
			if ($score_got=='') $score_got='0';
	  /*$query="UPDATE ".$GLOBALS['prefix_lms']."_competence_user SET "
			 .($score_init ? "score_init=$score_init".($score_got ? ', ' : '') : "")
			 .($score_got ? "score_got=$score_got " : "").
			 "WHERE id_competence=$id_comp AND id_user=$id_user";*/
			$query="UPDATE ".$GLOBALS['prefix_lms']."_competence_user SET ".
			 "score_init=$score_init, score_got=$score_got ".
			 "WHERE id_competence=$id_comp AND id_user=$id_user";
			return mysql_query($query);
		} else {
			if ($score_init=='' && $score_got=='') return true;
			if ($score_init=='') $score_init='0';
			if ($score_got=='') $score_got='0';
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_user ".
			 "(id_user, id_competence, score_init, score_got) VALUES ($id_user , $id_comp , $score_init , $score_got)";
			if (mysql_query($query)){
				$this->TrackCompetenceAssignment($id_comp, $id_user, $score_got, _ASSIGN_MANUAL);
			}
		}
		return false;
	}

	function DeleteUserScore($id_user,$id_comp) {
		return mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_competence=$id_comp AND id_user=$id_user");
	}


	function UpdateCompetencesCourseAssign($id_course, &$score, &$flag) {
		$new_comps_score=array();
		foreach ($score as $key=>$value) { //filter '' values
			if ($value!='') $new_comps_score[$key]=$value;
		}
		$new_comps_flag=$flag;

		//$old_comps_flag   = $this->GetCourseCompetencesIds($id_course,'flag');
		//$old_comps_scores = $this->GetCourseCompetencesIds($id_course,'score');
		$old_comps = $this->GetCourseCompetencesIds($id_course);
		$query="";

		foreach ($old_comps as $key=>$value) {
			if (!in_array($value,$new_comps_score) && !in_array($value,$new_comps_flag)) {
				$query="delete FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_competence=$value AND id_course=$id_course";
				if (!$output=mysql_query($query)) return $output;
			}
		}

		$qry_values=array();
		foreach ($new_comps_score as $key=>$value) {
			if (!in_array($value,$old_comps)) {
				$qry_values[]='('.$value.','.$id_course.','.$new_scores[$value].')';
			} /*else {
		if ($new_scores[$value]!=$old_scores[$value]) {
		  $query="UPDATE ".$GLOBALS['prefix_lms']."_competence_course SET score=".$new_scores[$value]." ".
				 "WHERE id_competence=$value AND id_course=$id_course";
		  if (!mysql_query($query)) return false;
		}
	  }*/
		}
		if (count($qry_values)>0) {
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_course (id_competence,id_course,score) VALUES ".implode(',',$qry_values);
			if (!mysql_query($query)) return false;
		}

		foreach ($new_comps_flag as $key=>$value) {
			if (!in_array($value,$old_comps)) {
				$qry_values[]='('.$value.','.$id_course.','.$new_scores[$value].')';
			} /*else {
		if ($new_scores[$value]!=$old_scores[$value]) {
		  $query="UPDATE ".$GLOBALS['prefix_lms']."_competence_course SET score=".$new_scores[$value]." ".
				 "WHERE id_competence=$value AND id_course=$id_course";
		  if (!mysql_query($query)) return false;
		}
	  }*/
		}
		if (count($qry_values)>0) {
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_course (id_competence,id_course,score) VALUES ".implode(',',$qry_values);
			$output=mysql_query($query);
		}
		return $output;
	}


	//update previous competences assignments to users
	function RefreshCourseUsersScore($id_course, $id_comp, $diff) {

		$type=$this->GetCompetenceType($id_comp);
		//$users = $this->GetCompetenceUsersIds($id_comp);

		//retrieve students who have completed the modified course
		$query =
				"SELECT idUser FROM ".$GLOBALS['prefix_lms']."_courseuser ".
				"WHERE idCourse=$id_course AND status="._CUS_END." AND level="._LEV_STUD;
		$res=mysql_query($query);
		$users=array();
		while ($row=mysql_fetch_row($res)) {
			$users[]=$row[0];
		}

		switch ($type) {
			case 'flag': {
				if ($diff=='') { //delete all users with this flag competence
					/*$query =
							"DELETE FROM ".$GLOBALS['prefix_lms']."_competence_user ".
							"WHERE id_comp=$id_comp AND id_user IN (".implode(',',$users).")";*/
					return;
					//we are yet not sure how to handle this case, since the user may have acquired the
					//competence from other courses

				} else { //assign the competence to all people who has finished the course
					$values=array();
					foreach ($users as $user) {
						if (!$this->UserHasCompetence($user, $id_comp))
						$values[]="($id_comp, $user, 0, 0)";
					}
					$query =
							"INSERT INTO ".$GLOBALS['prefix_lms']."_competence_user ".
							"(id_competence, id_user, score_init, score_got) VALUES ".
					implode(',', $values);
				}
			} break;

			case 'score': {

				$query = "UPDATE ".$GLOBALS['prefix_lms']."_competence_user ".
					"SET score_got=score_got+$diff ". //this can be < 0 .. should be handled
					"WHERE id_competence=$id_comp AND id_user IN (".implode(',',$users).")";

				//delete eventual score competences <= 0
				mysql_query("UPDATE".$GLOBALS['prefix_lms']."_competence_user SET score_got=0 WHERE score_got<0");
				mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE score_got<=0 AND score_init<=0");
			} break;

		}

		//execute query
		return mysql_query($query);
	}


	//assign a score to a course for a specific competence
	function UpdateCourseCompetence($id_course, $id_comp, $value, $update=true) {
		$res=mysql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_course=$id_course AND id_competence=$id_comp");
		if (mysql_num_rows($res)>0) {
			$row=mysql_fetch_assoc($res);
			$old_score=$row['score'];
		}

		$type=$this->GetCompetenceType($id_comp);
		$result=false;

		switch ($type) {

			case 'flag': {
				if (mysql_num_rows($res)>0) {
					if (!$value) {
						$query = "DELETE FROM ".$GLOBALS['prefix_lms']."_competence_course ".
					 "WHERE id_course=$id_course AND id_competence=$id_comp";
						$result=mysql_query($query);
						$new_score=''; //this means to delete flag competences
					}
				} else {
					if ($value) {
						$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_competence_course (id_course,id_competence,score) ".
					 "VALUES ($id_course,$id_comp,0)";
						$result=mysql_query($query);
						$new_score=0;
					}
				}
				$result=true;
			} break;

			case 'score': {
				if ($value!='') {
					if (mysql_num_rows($res)>0) {
						$query = "UPDATE ".$GLOBALS['prefix_lms']."_competence_course SET score=$value ".
					 "WHERE id_course=$id_course AND id_competence=$id_comp";
						$new_score=$value-$old_score;
					} else {
						$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_competence_course (id_course,id_competence,score) ".
					 "VALUES ($id_course,$id_comp,$value)";
						$new_score=$value;
					}
				} else {
					$query = "DELETE FROM ".$GLOBALS['prefix_lms']."_competence_course ".
				   "WHERE id_course=$id_course AND id_competence=$id_comp";
					$new_score= (-1.0) * $old_score;
				}
				$result=mysql_query($query);
			} break;
		}

		if ($result) {
			if ($update) {
				$this->RefreshCourseUsersScore($id_course, $id_comp, $new_score);
			}
		}
		return $result;
	}


	function GetCompetenceUsers($id_comp) {
		$output=array();
		$query="SELECT id_competence,idst,type_of FROM ".$GLOBALS['prefix_lms']."_competence_required WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) {
			$output[]=$row;
		}
		return $output;
	}

	function GetCompetenceUsersAcquired($id_comp) {
		$output=array();
		$query="SELECT * FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) {
			$output[]=$row;
		}
		return $output;
	}

	function GetCompetenceUsersAll($id_comp, $order=false, $dir='ASC', $ini=false, $tot=false) {
		$output=array();
		$query="SELECT t1.*, t1.score_init+t1.score_got as total, t2.firstname, t2.lastname, t2.userid, t2.email FROM ".
		$GLOBALS['prefix_lms']."_competence_user as t1 JOIN ".
		$GLOBALS['prefix_fw']."_user as t2 ON (t1.id_user=t2.idst) ".
					 "WHERE id_competence=$id_comp";
		if ($order) {
			$dir=strtoupper($dir);
			$temp=(($dir=='ASC' || $dir=='DESC') ? " $dir" : "");
			switch ($order) {
				case 'name': $orderby='t2.lastname '.$temp.', t2.firstname '.$temp.', t2.userid '.$temp; break;
				case 'userid': $orderby='t2.userid '.$temp.', t2.lastname '.$temp.', t2.firstname '.$temp; break;
				case 'email': $orderby='t2.email '.$temp.', t2.lastname '.$temp.', t2.firstname '.$temp; break;
				case 'score_init': $orderby='t1.score_init '.$temp.', t2.lastname '.$temp.', t2.firstname '.$temp; break;
				case 'score_got': $orderby='t1.score_got '.$temp.', t2.lastname '.$temp.', t2.firstname '.$temp; break;
				case 'total': $orderby='total '.$temp.', t2.lastname '.$temp.', t2.firstname '.$temp; break;
				default: $orderby='';
			}
			if ($orderby!='') $query.=" ORDER BY $orderby";
		}
		if ($ini!==false && $tot!==false) {
			$query.=" LIMIT $ini, $tot";
		}
		if ($res=mysql_query($query)) {
			while ($row=mysql_fetch_array($res)) {
				$output[]=$row;
			}
			return $output;
		} else return false;
	}

	function GetCompetenceUsersCount($id_comp) {
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_array($res);
		return $row[0];
	}

	function GetCompetenceUsersIds($id_comp) {
		$output=array();
		$query="SELECT idst FROM ".$GLOBALS['prefix_lms']."_competence_required WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) {
			$output[]=$row[0];
		}
		return $output;
	}


	function GetAllUserCompetences($id_user=false) {
		//no full outer join in mysql, we use 2 arrays
		$t1=array(); //required competences
		$t2=array(); //user's competences

		if (!$id_user) {
			$id_user=getUserLogId();
			$arrst = $GLOBALS['current_user']->getArrSt();
		} else {
			$acl =& $GLOBALS['current_user']->getAcl();
			$arrst = $acl->getUserAllST(false,'',$id_user);
		}

		$query="SELECT * ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_required as t1, ".$GLOBALS['prefix_lms']."_competence as t2 ".
		   "WHERE idst IN (".implode($arrst,',').") AND t1.id_competence=t2.id_competence ".
		   "ORDER BY t2.id_competence";
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) { $t1[]=$row; }

		//if (!$id_user) $id_user=getUserLogId(); //get current user id, if no other specified
		$query="SELECT * ".
		   "FROM ".$GLOBALS['prefix_lms']."_competence_user as t1, ".$GLOBALS['prefix_lms']."_competence as t2 ".
		   "WHERE id_user=$id_user AND t1.id_competence=t2.id_competence ".
		   "ORDER BY t2.id_competence";
		$res=mysql_query($query);
		while ($row=mysql_fetch_array($res)) { $t2[]=$row; }



		$output=array();

		foreach ($t1 as $k1=>$v1) {
			$output[ $v1['id_competence'] ]=$v1;
		}

		foreach ($t2 as $k2=>$v2) {
			$key=$v2['id_competence'];
			if ( array_key_exists($key,$output) ) {
				$output[$key]['score_init'] = $v2['score_init'];
				$output[$key]['score_got']  = $v2['score_got'];
			} else {
				$output[$key]=$v2;
			}


		}

	/*$t1 = $GLOBALS['prefix_lms']."_competence_required";
	$t2 = $GLOBALS['prefix_lms']."_competence_user";
	$query =  "SELECT DISTINCT $t1.idst, $t2.id_competence, $t2.score_init, $t2.score_got ".
			  "FROM $t1 LEFT JOIN $t2 ON ($t1.idst IN (".implode($arrst,',').") AND $t2.id_user=$id_user AND $t1.id_competence=$t2.id_competence) ".
			  "UNION ALL ".
			  "SELECT DISTINCT $t1.idst, $t2.id_competence, $t2.score_init, $t2.score_got ".
			  "FROM $t1 RIGHT JOIN $t2 ON ($t1.idst IN (".implode($arrst,',').") AND $t2.id_user=$id_user AND $t1.id_competence=$t2.id_competence) ".
			  "WHERE $t1.idst IS NULL";

	$res=mysql_query($query);
	while ($row=mysql_fetch_array($res)) {
		$output[]=$row;
	}*/
		//merge the two arrays

	/*foreach ($t1 as $k1=>$v1) {

	  if (in_array($v1,$t2)) {
		$second
	  }

	  $output[]=array('comp_id', 'required'=>)
	}
	$output=array('comp_required'=>$t1,'comp_user'=>$t2);*/
		//$output['debug']=$query;
		//$output['error']=mysql_error();
		return $output;

	}


	//required competences for users or groups
	function UpdateCompetenceUsers($id_comp,$user_list) {
		$old_users=$this->GetCompetenceUsersIds($id_comp);

		//delete unselected users
		foreach ($old_users as $key=>$value) {
			if (!in_array($value,$user_list	)) {
				$query="delete FROM ".$GLOBALS['prefix_lms']."_competence_required WHERE id_competence=$id_comp AND idst=$value";
				if (!$output=mysql_query($query)) return $output;
			}
		}

		//filter new selected users
		$qry_values=array();
		foreach ($user_list as $key=>$value) {
			if (!in_array($value,$old_users)) {
				$qry_values[]='('.$id_comp.','.$value.',\'user\')';
			}
		}
		if (count($qry_values)>0) {
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_required (id_competence,idst,type_of) VALUES ".implode(',',$qry_values);
			$output=mysql_query($query);
		}
		return $output; //boolean value
	}


	function UserHasCompetence($id_user,$id_comp) {
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_user=$id_user AND id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		return ($row[0]>0);
	}

	function GetUserScore($id_user,$id_comp) {
		$query="SELECT score_got FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_user=$id_user AND id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		return ($row[0]);
	}

	function GetUserInitialScore($id_user,$id_comp) {
		$query="SELECT score_init FROM ".$GLOBALS['prefix_lms']."_competence_user WHERE id_user=$id_user AND id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		return ($row[0]);
	}

	function GetCompetenceCoursesCount($id_comp) {
	/*$output=array();
	$query="SELECT id_competence,idst,type_of FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_competence=$id_comp";
	$res=mysql_query($query);
	while ($row=mysql_fetch_array($res)) {
	  $output[]=$row;
	}
	return $output;*/
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence_course WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_array($res);
		return $row[0];
	}

	function GetCompetenceRequiredCount($id_comp) {
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence_required WHERE id_competence=$id_comp";
		$res=mysql_query($query);
		$row=mysql_fetch_array($res);
		return $row[0];
	}

	function HasTextCompetence($id, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence_text WHERE id_competence=$id AND lang_code='".$lang_code."'";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		if ($row[0]>0) return true; else return false;
	}

	function HasTextCategory($id, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		$query="SELECT COUNT(*) FROM ".$GLOBALS['prefix_lms']."_competence_category_text WHERE id_category=$id AND lang_code='".$lang_code."'";
		$res=mysql_query($query);
		$row=mysql_fetch_row($res);
		if ($row[0]>0) return true; else return false;
	}

	//save a new competence in DB or update a pre-existent competence
	function SaveCompetence($id_comp,$data, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		if ($data['type']=='flag') { $data['score']='0'; $data['score_min']='0'; }
		if ($data['score']=='') $data['score']='0'; //set a default value, or the query will rise an error
		if ($data['score_min']=='') $data['score_min']='0';
		if ($id_comp==0) {
			$query1="INSERT INTO ".$GLOBALS['prefix_lms']."_competence ".
			  "(id_category, type, score, score_min, competence_type) VALUES ".
			  "(".$data['category'].",'".$data['type']."',".$data['score'].",".$data['score_min'].",'".$data['comp_type']."')";
			if (mysql_query($query1)) {
				$res=mysql_query("SELECT LAST_INSERT_ID()");
				$row=mysql_fetch_row($res);
				$last=$row[0];
				$query2="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_text ".
				"(id_competence,lang_code,text_name,text_desc) VALUES ".
				"($last,'".$lang_code."','".$data['name']."','".$data['description']."')";
				if (mysql_query($query2)) {
					return $last;
				} else {
					return false;
				}
			}
		} else {
			$query1="UPDATE ".$GLOBALS['prefix_lms']."_competence ".
			  "SET id_category=".$data['category'].", type='".$data['type']."', score=".$data['score'].",".
			  "score_min=".$data['score_min'].", competence_type='".$data['comp_type']."' ".
			  "WHERE id_competence=$id_comp";
			if ($this->HasTextCompetence($id_comp, $lang_code)) {
				$query2="UPDATE ".$GLOBALS['prefix_lms']."_competence_text ".
				"SET text_name='".$data['name']."', text_desc='".$data['description']."' ".
				"WHERE id_competence=$id_comp AND lang_code='".$lang_code."'";
			} else {
				$query2="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_text ".
				"(id_competence,lang_code,text_name,text_desc) VALUES ".
				"($id_comp,'".$lang_code."','".$data['name']."','".$data['description']."')";
			}
			return (mysql_query($query1) && mysql_query($query2));
		}
		return false;
	}


	//save a competence but not languages, then return the inserted id
	function SaveCompetenceData($id_comp=false, $data) {
		if ($data['type']=='flag') { $data['score']='0'; $data['score_min']='0'; }
		if ($data['score']=='') $data['score']='0'; //set a default value, or the query will rise an error
		if ($data['score_min']=='') $data['score_min']='0';
		if ($id_comp!=false && $id_comp>0) {
			$query1="UPDATE ".$GLOBALS['prefix_lms']."_competence ".
			  "SET id_category=".$data['category'].", type='".$data['type']."', score=".$data['score'].",".
			  "score_min=".$data['score_min'].", competence_type='".$data['comp_type']."' ".
			  "WHERE id_competence=$id_comp";
			if (mysql_query($query1)) return $id_comp;
			else return false;
		} else {
			$query1="INSERT INTO ".$GLOBALS['prefix_lms']."_competence ".
			  "(id_category, type, score, score_min, competence_type) VALUES ".
			  "(".$data['category'].",'".$data['type']."',".$data['score'].",".$data['score_min'].",'".$data['comp_type']."')";
			if (mysql_query($query1)) {
				$res=mysql_query("SELECT LAST_INSERT_ID()");
				$row=mysql_fetch_row($res);
				return $row[0];
			} else return false;
		}
	}

	//save e language for a given competence
	function SaveCompetenceLanguage($id_comp, $lang_code, $name, $description) {
	/*$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_text ".
		   "(id_competence,lang_code,text_name,text_desc) VALUES ".
		   "($id_comp,'".$lang_code."','".$name."','".$description."')";
	return (mysql_query($query));*/
		if ($this->HasTextCompetence($id_comp, $lang_code)) {
			$query ="UPDATE ".$GLOBALS['prefix_lms']."_competence_text ".
			  "SET text_name='".$name."', text_desc='".$description."' ".
			  "WHERE id_competence=$id_comp AND lang_code='".$lang_code."'";
		} else {
			$query ="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_text ".
			  "(id_competence,lang_code,text_name,text_desc) VALUES ".
			  "($id_comp,'".$lang_code."','".$name."','".$description."')";
		}
		return (mysql_query($query));
	}

	//save a new category in DB or update a pre-existent category
	function SaveCompetenceCategory($id_cat,$data, $lang_code=false) {
		if (!$lang_code) $lang_code = getLanguage();
		if ($id_cat==0 || $id_cat==false) {
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_category () VALUES ()";
			if (mysql_query($query)) {
				$res=mysql_query("SELECT LAST_INSERT_ID()");
				$row=mysql_fetch_row($res);
				$last=$row[0];
				$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_category_text ".
			   "(id_category,lang_code,text_name,text_desc) VALUES ".
			   "($last,'".$lang_code."','".$data['name']."','".$data['description']."')";
				return mysql_query($query);
			}
		} else {
			if ($this->HasTextCategory($id_cat, $lang_code)) {
				$query="UPDATE ".$GLOBALS['prefix_lms']."_competence_category_text ".
			   "SET text_name='".$data['name']."', text_desc='".$data['description']."' ".
			   "WHERE id_category=$id_cat AND lang_code='".$lang_code."'";
			} else {
				$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_category_text ".
			   "(id_category,lang_code,text_name,text_desc) VALUES ".
			   "($id_cat,'".$lang_code."','".$data['name']."','".$data['description']."')";
			}
			return mysql_query($query);
		}
		return false;
	}


	function SaveCompetenceCategoryData($id_cat=false) {
		$output=false;
		if ($id_cat!=false && $id_cat>0) {
			$output=$id_cat;
		} else {
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_category () VALUES ()";
			if (mysql_query($query)) {
				$res=mysql_query("SELECT LAST_INSERT_ID()");
				$row=mysql_fetch_row($res);
				$output=$row[0];
			}
		}
		return $output;
	}


	function SaveCompetenceCategoryLanguage($id_cat, $lang_code, $name, $description) {
	/*$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_category_text ".
		   "(id_category,lang_code,text_name,text_desc) VALUES ".
		   "($id_cat,'".$lang_code."','".$name."','".$description."')";
	return (mysql_query($query));*/
		if ($this->HasTextCategory($id_cat, $lang_code)) {
			$query="UPDATE ".$GLOBALS['prefix_lms']."_competence_category_text ".
			 "SET text_name='".$name."', text_desc='".$description."' ".
			  "WHERE id_category=$id_cat AND lang_code='".$lang_code."'";
		} else {
			$query="INSERT INTO ".$GLOBALS['prefix_lms']."_competence_category_text ".
			 "(id_category,lang_code,text_name,text_desc) VALUES ".
			 "($id_cat,'".$lang_code."','".$name."','".$description."')";
		}
		return mysql_query($query);
	}



	function GetCompetencesGroupedList() {
		//$res=mysql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_competence_category");
		$temp = $this->GetCompetencesCategoriesList(true);
		$output = array();

		foreach ($temp as $key=>$value) {
	  /*$cat = $this->GetCompetenceCategory($key);
	  //$id = $cat['id'];
	  $output[ $key ] ['name'] = $cat['name'];
	  $output[ $key ] ['rows'] = $this->GetCompetencesList($key);*/
			$output[ $key ] ['name'] = $value;
			$output[ $key ] ['rows'] = $this->GetCompetencesList((int)$key);
		}

		return $output;
	}

	function AssignCourseCompetencesToUser($id_course, $idst, $track_type=false) {
		$comps = $this->GetCourseCompetences($id_course);
		$err=false;
		//now update all competences to user ...
		foreach ($comps as $key=>$value) {
			$res=mysql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_competence_user ".
					   "WHERE id_user=$idst AND id_competence=".$value['id']);
			if (mysql_num_rows($res)>0) $check=true; else $check=false;
			switch ($value['type']) {

				case 'flag': {
					if (!$check) {
						$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_competence_user (id_user,id_competence,score_init,score_got) ".
					 "VALUES ($idst,".$value['id'].",0,0)";
						if (!mysql_query($query))
						$err=true;
						else
						$this->TrackCompetenceAssignment($value['id'], $idst, 0, _ASSIGN_COURSE);
					} //otherwise competence has already been acquired by the user
				} break;

				case 'score': {
					if (!$check) {
						$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_competence_user (id_user,id_competence,score_init,score_got) ".
					 "VALUES ($idst,".$value['id'].",0,".$value['course_score'].")";
					} else {
						//first: check if points for this course have already been assigned
						$tmp_qry = "SELECT status FROM ".$GLOBALS['prefix_lms']."_courseuser ".
											 "WHERE idCourse=$id_course AND idUser=$idst"; //and the edition?
						$tmp = mysql_fetch_row(mysql_query($tmp_qry));
						if ($tmp[0]!=_CUS_END)
						$query = "UPDATE ".$GLOBALS['prefix_lms']."_competence_user ".
						 "SET score_got=score_got+".$value['course_score']." WHERE ".
						 "id_user=$idst AND id_competence=".$value['id'];
						else
						return false; //competence already assigned for this course
					}
					if (!mysql_query($query))
					$err=true;
					else {
						$this->TrackCompetenceAssignment($value['id'], $idst, $value['course_score'], _ASSIGN_COURSE);
					}
				} break;
			}
		}
		return !$err;
	}

	function GetCompetenceScores($id) {
		$query = "SELECT score_min, score FROM ".$GLOBALS['prefix_lms']."_competence WHERE id_competence=$id";
		$res=mysql_query($query);
		$row=mysql_fetch_array($res);
		return $row;
	}

	function TrackCompetenceAssignment($id_comp, $id_user, $score, $source) {
		$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_competence_track ".
						 "(id_competence, id_user, score, source, date_assignment) VALUES ".
						 "($id_comp, $id_user, $score, '$source', NOW() )";
		return mysql_query($query);
	}


}

?>