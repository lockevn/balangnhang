<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');
require_once $CFG->dirroot .'/smartcom/util/memcachedutil.php';

define("LABEL", 9);
define("QUIZ", 12);
define("RESOURCE", 13);
define("ASSIGNMENT", 1);


/**
 * get list of recommended course for a course id
 *
 * @param int $courseid
 * @return array of course objects
 * truy xuat = cach:
 *
 * foreach($results as $course) {
 * 		$course->id
 * 		$course->fullname
 * 		$course->summary
 * }
 *
 */
function getRecommendCourseList($courseid, $summaryTextLength = 250) {

	global $CFG;
	if(empty($courseid)) {
		return false;
	}
	if($CFG->cachetype === 'memcached') {
		$memcached = new memcached();
		if($memcached === false) {
			return false;
		}
		$key = MemcachedUtil::$RECOMMEND_COURSE_KEY . $courseid;
		$results = $memcached->get($key);
		if(!empty($results)) {
			//cache hits
			echo "cache hits";
			return $results;
		}
	}	

	$sql = "select c.id as id, c.fullname as fullname, c.summary as summary
			from " . $CFG->prefix . "course c, " . $CFG->prefix. "smartcom_recommend_course rc
	where c.visible=1 and c.id = rc.recommendcourseid and rc.courseid=$courseid limit 5";

	$results = get_records_sql($sql);

	if(empty($results)) {
		return false;
	}
	/*cut summary text if necessary*/
	foreach($results as $result) {
		if(strlen($result->summary) > $summaryTextLength) {
			$tmp = substr($result->summary, 0, $summaryTextLength);
			$lastSpaceIndex = strripos($tmp, ' ');
			if(!empty($lastSpaceIndex)) {
				$result->summary = 	substr($tmp, 0, $lastSpaceIndex) . " ...";
			}
			else {
				$result->summary .= " ...";
			}
			trim($result->summary, " ");
		}
	}
	if(!empty($memcached)) {
		//$memcached->set($key, $results, 24*3600);
		$memcached->set($key, $results, 10);
	}
	return $results;

}

/**
 * get list các supporting tools của 1 course để hiển thị trên thanh menu màu đỏ
 *
 * @param int $courseid id course
 * @return array các object:
 * foreach($results as $result) {
 echo "$result->id , $result->modulename, $result->name";
 }
 *
 */
function getCourseSupportTools($courseid) {
	global $CFG;
	if(empty($courseid)) {
		return false;
	}
	if($CFG->cachetype === 'memcached') {
		$memcached = new memcached();
		if($memcached === false) {
			return false;
		}
		$key = MemcachedUtil::$COURSE_SUPPORT_TOOL_KEY . $courseid;
		$results = $memcached->get($key);
		if(!empty($results)) {
			//cache hits
			return $results;
		}
	}
	$sql = "SELECT sequence
		FROM " . $CFG->prefix . "course_sections cs 
		WHERE cs.course=" . $courseid . " AND cs.section=0 AND cs.visible=1";
	$result = get_record_sql($sql);

	if(empty($result)) {
		return false;
	}
	$sequence = $result->sequence;

	$sql = "SELECT cm.id as id, m.name as modulename, cm.instance as instanceid
	FROM mdl_modules m, mdl_course_modules cm
	WHERE m.id = cm.module AND cm.id in ($sequence)";
	$results = get_records_sql($sql);

	/*lấy module name để hiển thị trên menu*/
	$cmArr = array();
	foreach($results as $result) {
		if($result->modulename != "forum") {
			$sql = "SELECT name FROM " . $CFG->prefix . $result->modulename . " WHERE id = $result->instanceid";
		}
		else {
			$sql = "SELECT name FROM " . $CFG->prefix . $result->modulename . " WHERE id = $result->instanceid AND type='general'";
		}
		$tmpResult = get_record_sql($sql);
		if(empty($tmpResult)) {
			continue;
		}
		$result->name = $tmpResult->name;
		$cmArr[] = $result;
	}
	if(empty($cmArr)) {
		return false;
	}
	if(!empty($memcached)) {
		$memcached->set($key, $cmArr, 10);
	}
	return $cmArr;

}

/**
 * get list of activity to display in lo page corresponding to a loid
 *
 * @param int $courseid
 * @param int $selectedCmid
 * @param int $type 
 * @return array of activity obj
 * $activity->id (course module id)
 * 			->name (listening  ...)
 * 			->los (array of lo obj)
 * 			->link 
 * 			->selected (true/false)
 */
function getLessonActivities($courseid, $selectedCmid, $type) {
	global $CFG;
		
	/*select current section id from selectedCmID*/	
	$result = get_record("course_modules", "id", $selectedCmid, "course", $courseid, "", "", "section");
	if(empty($result)) {
		return false;
	}
	$sectionId = $result->section;
	
	/*build section structure*/
	$section = getCourseSectionStructure($courseid, $sectionId);
	if(empty($section)) {
		return false;
	}	
	$activityArr = $section->activities;
	
	/*get selected activity*/
	foreach($activityArr as $activity) {
		foreach($activity->los as $lo) {
			if($lo->id == $selectedCmid && $lo->type == $type) {
				$activity->selected = true;
				break;
			} 
			else {
				$activity->selected = false;
			}
		}
	}
	return $activityArr;
		
}

/**
 * Get list of unit / lesson of course
 *
 * @param int $courseid
 * @return array of section obj
 * section->id
 * section->label (Unit 1/ lesson1 ...)
 * section->summary (Overview ...)
 */
function getSectionListOfCourse($courseid) {
	if(empty($courseid)) {
		return false;
	}
	$sectionList = get_records_select("course_sections", "course=$courseid and visible=1", "", "id, label, summary");
	return $sectionList;	
}

/**
 * get list of activities (listen, reading ...) of a lesson/unit
 *
 * @param int $courseid
 * @param int $sectionid
 * @return array of Activity obj
 * * $activity->id (course module id)
 * 			->name (listening  ...)
 * 			->content 			
 * 			->link 
 */
function getActivityListOfSection($courseid, $sectionid) {
	if(empty($courseid) || empty($sectionid)) {
		return false;
	}
	$section = getCourseSectionStructure($courseid, $sectionid);
	if(empty($section)) {
		return false;
	}
	return $section->activities;
	
}

function getAvgGradeOfAllQuizInActivityOfUser($courseid, $sectionid, $activityid, $userid) {
	global $CFG;
	if(empty($courseid) || empty($sectionid) || empty($activityid) || empty($userid)) {
		return false;
	}
	$section = getCourseSectionStructure($courseid, $sectionid);
	if(empty($section)) {
		return false;
	}	
	$quizIdStr = "";
	foreach($section->activities as $activity) {
		if($activity->id == $activityid) {
			foreach($activity->los as $lo) {
				if($lo->type == QUIZ) {
					$quizIdStr .= $lo->instance . ",";
				}
			}
			$quizIdStr = trim($quizIdStr, ",");
			break;
		}
	}
	$sql = "SELECT avg(qg.grade/q.sumgrades) avg_
				FROM " . $CFG->prefix ."quiz_grades qg, mdl_quiz q 
				WHERE qg.quiz=q.id AND qg.userid=$userid 
				AND qg.quiz in ($quizIdStr) ";
	$result = get_record_sql($sql);
	if(empty($result)) {
		return false;							
	}
	return $result->avg_;	
}


/**
 * dựng cấu trúc activity-lo của 1 section trong 1 course
 *
 * @param unknown_type $courseid
 * @param unknown_type $sectionid
 * @return section object có dạng:
 * 
 * $section ->id
 * 			->label (lesson 1 , unit1..)
 * 			->section
 * 			->summary
 * 			->activities (array of activity obj)
 * $activity->id (course module id)
 * 			->name (listening  ...)
 * 			->content
 * 			->los (array of lo obj)
 * 			->link 
 * 
 * $lo->id (course module id)
	  ->instance (instance id)
	  ->type 	(resource, quiz, label)
	  ->name  (lecture1, exercise1 ..)
 */
function getCourseSectionStructure($courseid, $sectionid) {
	global $CFG;
	
	if(empty($courseid) || empty($sectionid) ) {
		return false;
	}

	echo "cache type: $CFG->cachetype";
	if($CFG->cachetype === 'memcached') {
		$memcached = new memcached();
		if($memcached === false) {
			return false;
		}
		
		$key = MemcachedUtil::$COURSE_SECTION_KEY . $courseid . "_" . $sectionid;
		$results = $memcached->get($key);
		if(!empty($results)) {
			//cache hits
			echo "cache hits";
			return $results;
		}
	}	
	
	/*get section info*/
	$result = get_record("course_sections", "id", $sectionid, "visible", 1);
	if(empty($result)) {
		return false;
	}		
	/*lưu section*/
	$section = $result;	
	
	/*get activity list of section*/
	$sql = "SELECT cm.id, l.name, l.label as content
			FROM {$CFG->prefix}course_modules cm, {$CFG->prefix}label l
			WHERE cm.module=9 AND cm.indent=0 AND cm.visible=1 AND cm.instance=l.id
		  	AND cm.section=$sectionid AND cm.course=$courseid";	
	$results = get_records_sql($sql);
	
	
	$activityArr = array(); 
		
	foreach($results as $activity) {
		$index = stripos("$section->sequence", "$activity->id");		
		if($index !== false) {			
			$activity->index = $index;
			$activityArr[] = $activity;
		}
	}
	
	/*sắp xếp $activityArr*/
	for($i = 0; $i < sizeof($activityArr)-1; $i++) {
		for($j = $i+1; $j < sizeof($activityArr); $j++) {
			if($activityArr[$i]->index > $activityArr[$j]->index) {
				$temp = $activityArr[$i];
				$activityArr[$i] = $activityArr[$j];
				$activityArr[$j] = $temp;				
			}
		}
	}
	
	/*duyet $activityArr để xác định cmid của các lecture, exercise ... con của activity*/
	for($i = 0; $i < sizeof($activityArr); $i++){
		if($i < sizeof($activityArr) - 1) {
			$tmpStr = substr($section->sequence, $activityArr[$i]->index, $activityArr[$i+1]->index - $activityArr[$i]->index);
		} else {
			$tmpStr = substr($section->sequence, $activityArr[$i]->index);
		}
		$loCmIdArr = split(",", trim($tmpStr, ","));
		$loArr = array(); /*luu LO list cua 1 activity*/
		/*lay thong tin chi tiet cho cac LO*/
		for($j = 1; $j<sizeof($loCmIdArr); $j++) {
			$cmObj = get_record("course_modules", "id", $loCmIdArr[$j], "indent", 1, "visible", 1, "module, instance");
			if(empty($cmObj)) {
				continue;
			}
			$tmpArr = array(LABEL=>"label", QUIZ=>"quiz", RESOURCE=>"resource");
			if(!in_array($cmObj->module, array_keys($tmpArr))) {
				continue;
			} 			
			$moduleName = $tmpArr[$cmObj->module];
			$result = get_record($moduleName, "id", $cmObj->instance, "", "", "", "", "name");
			$lo = new stdClass();
			$lo->id = $loCmIdArr[$j];
			$lo->instance = $cmObj->instance;
			$lo->type = $cmObj->module;
			if(!empty($result)) {
				$lo->name = $result->name;
			}			
			$loArr[] = $lo;
		}
		/*set loArr cho activity*/
		$activityArr[$i]->los = $loArr;
		/*get first lo cmid*/
		if(sizeof($activityArr[$i]->los) > 0) {
			/*link cho activity sẽ là view lo đầu tiên của activity*/
			$activityArr[$i]->link = $CFG->wwwroot . "/mod/" . $tmpArr[$activityArr[$i]->los[0]->type] . "/view.php?id=" . $activityArr[$i]->los[0]->id;
		}
	}
		
	/*lưu activity list vào section*/
	$section->activities = $activityArr; 

	/*luu vao cache*/
	if(!empty($memcached)) {
		$memcached->set($key, $section, 10);
		echo "set memcached $key";
	}
	return $section;
}

//$section = getCourseSectionStructure(104, 172);
//if($section !== false) {
//echo "sectionid: $section->id label: $section->label summary: $section->summary <br>";
//foreach($section->activities as $activity) {
//	echo "=====	activity id: $activity->id , activity name: $activity->name <br>";
//	foreach($activity->lolist as $lo) {
//		echo "===========	lo id: $lo->id , lo name: $lo->name, lo instance: $lo->instance, lo type: $lo->type <br>";
//	}
//}

//$activityArr = getLessonActivities(104, 309, QUIZ);
//foreach($activityArr as $activity) {
//	echo "id: $activity->id name: $activity->name link: $activity->link selected: $activity->selected "; 
//}

//$quizIdArr = getAllQuizOfActivity(104, 172, 302);
//foreach($quizIdArr as $id ) {
//	echo "$id ";
//}

//$grade = getAvgGradeOfAllQuizInActivityOfUser(7, 52, 255, 4);
//echo $grade;



?>