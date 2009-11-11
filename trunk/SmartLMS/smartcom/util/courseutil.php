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
			$raw = html_to_text($result->summary);
			$tmp = substr($raw, 0, $summaryTextLength);
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
	
	if(empty($sequence)) {
		return false;
	}

	$sql = "SELECT cm.id as id, m.name as modulename, cm.instance as instanceid
	FROM mdl_modules m, mdl_course_modules cm
	WHERE m.id = cm.module AND cm.id in ($sequence)";
	$results = get_records_sql($sql);
	if(empty($results)) {
		return false;
	}
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
		$cmArr[$result->modulename] = $result;
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
function getLessonActivitiesFromLOId($courseid, $selectedCmid, $type) {
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
				$activity->selected = 1;
				break;
			}
			else {
				$activity->selected = 0;
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
function getLessonActivitiesFromSectionId($courseid, $sectionid) {
	if(empty($courseid) || empty($sectionid)) {
		return false;
	}
	$section = getCourseSectionStructure($courseid, $sectionid);
	if(empty($section)) {
		return false;
	}
	return $section->activities;

}

function getAvgGradeOfAllQuizInActivityOfUserFromLOId($courseid, $selectedCmid, $activityid, $userid) {
	/*select current section id from selectedCmID*/
	$result = get_record("course_modules", "id", $selectedCmid, "course", $courseid, "", "", "section");
	if(empty($result)) {
		return false;
	}
	$sectionId = $result->section;
	return getAvgGradeOfAllQuizInActivityOfUser($courseid, $sectionId, $activityid, $userid);
}

/**
 * Lấy điểm TB của 1 activity của 1 user
 * và xác định user đã hòan thành tất cả quiz của activity đó chưa
 *
 * @param int $courseid
 * @param int $sectionid
 * @param int $activityid
 * @param int $userid
 * @return GradeObj $grade->avg :điểm trung bình nếu có hoặc false
 * 					$grade->status: 'failed' / 'passed' / 'incompleted'
 */
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
	$quizCount = 0;
	foreach($section->activities as $activity) {
		if($activity->id == $activityid) {
			foreach($activity->los as $lo) {
				if($lo->type == "exercise") {
					$quizIdStr .= $lo->instance . ",";
					$quizCount++;
				}
			}
			$quizIdStr = trim($quizIdStr, ",");
			break;
		}
	}
	if(!empty($quizIdStr)) {
		$sql = "SELECT count(qg.grade) as count, avg(qg.grade/q.sumgrades) avg_
				FROM " . $CFG->prefix ."quiz_grades qg, mdl_quiz q 
		WHERE qg.quiz=q.id AND qg.userid=$userid
		AND qg.quiz in ($quizIdStr) ";
		$result = get_record_sql($sql);
		if(empty($result)) {
			return false;
		}
	}
	$grade = new stdClass();
	if(!empty($result)) {
		$grade = new stdClass();
		$grade->avg = $result->avg_;
		if($result->avg_ !== null && $result->avg_ < 0.6) {
			$grade->status = 'failed';
		}
		else if($result->avg_ >= 0.6 && $quizCount == $result->count) {
			$grade->status = 'passed';
		}
		else {
			$grade->status = 'incompleted';
		}
	}
	else {
		$grade->status = 'incompleted';
		$grade->avg_ = '';
	}
	

	return $grade;
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
 ->type 	(lecture, exercise, practice, activity)
 ->name  (lecture1, exercise1 ..)
 */
function getCourseSectionStructure($courseid, $sectionid) {
	global $CFG;

	if(empty($courseid) || empty($sectionid) ) {
		return false;
	}

	if($CFG->cachetype === 'memcached') {
		$memcached = new memcached();
		if($memcached === false) {
			return false;
		}

		$key = MemcachedUtil::$COURSE_SECTION_KEY . $courseid . "_" . $sectionid;
		$results = $memcached->get($key);
		if(!empty($results)) {
			//cache hits
			//echo "cache hits";
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
			$tmpArr = array(LABEL=>"label", QUIZ=>"quiz", RESOURCE=>"resource", "lecture"=>"resource", "exercise"=>"quiz", "practice"=>"quiz", "test"=>"quiz");
			if(!in_array($cmObj->module, array_keys($tmpArr))) {
				continue;
			}
			$moduleName = $tmpArr[$cmObj->module];
			if($cmObj->module == LABEL) {
				$result = get_record($moduleName, "id", $cmObj->instance, "", "", "", "", "name");
			} else {
				$result = get_record($moduleName, "id", $cmObj->instance, "", "", "", "", "name, lotype");
			}
			$lo = new stdClass();
			$lo->id = $loCmIdArr[$j];
			$lo->instance = $cmObj->instance;
			if(isset($result->lotype)) {
				$lo->type = $result->lotype	;
			} else {
				$lo->type = "activity";
			}
				
			if(!empty($result)) {
				$lo->name = $result->name;
			}
			$loArr[] = $lo;
		}
		/*set loArr cho activity*/
		$activityArr[$i]->los = $loArr;
		/*get first lo cmid*/
		if(sizeof($activityArr[$i]->los) > 0) {
			if(!empty($tmpArr[$activityArr[$i]->los[0]->type]))
			/*link cho activity sẽ là view lo đầu tiên của activity*/
			$activityArr[$i]->link = $CFG->wwwroot . "/mod/" . $tmpArr[$activityArr[$i]->los[0]->type] . "/view.php?id=" . $activityArr[$i]->los[0]->id;
		}
	}

	/*lưu activity list vào section*/
	$section->activities = $activityArr;

	/*luu vao cache*/
	if(!empty($memcached)) {
		$memcached->set($key, $section, 10);
	}
	return $section;
}

/**
 * list course objects of an user
 *
 * @param int $userid
 * @return array of course obj with key is $course->id
 * $course->id
 * $course->name
 * return false if empty
 */
function getMyCourseList($userid) {
	if(empty($userid)) {
		return false;
	}
	global $CFG;
	$sql = "SELECT c.id,c.fullname as name
	FROM {$CFG->prefix}course c, {$CFG->prefix}context ctx, {$CFG->prefix}role_assignments r
	WHERE ctx.instanceid=c.id AND ctx.contextlevel=" .CONTEXT_COURSE.
			" AND r.contextid=ctx.id AND r.roleid in (5,10) AND c.id <> 1 AND r.userid=$userid";
	$results = get_records_sql($sql);
	if(empty($results)){
		return false;
	}
	return $results;

}

/**
 * get list of all lectures in an activity to display  "Bạn đang xem bài giảng số 1 2 3 của bài học này in Lecture page
 *
 * @param int $cmid lecture cmid
 * @param ActivityObj $selectedActivity
 * @return array of LectureObj ->id ->selected
 */
function getLectureListOfCurrentLecture($cmid, $selectedActivity) {
	if(empty($cmid) || empty($selectedActivity)) {
		return false;
	}
	$lectureArr = array();
	foreach($selectedActivity->los as $lo) {
		if($lo->type == "lecture" || $lo->type == "practice") {
			if($lo->id == $cmid && $lo->type == "lecture") {
				$lo->selected = 1;
			}
			else {
				$lo->selected = 0;
			}
			$lectureArr[] = $lo;
		}

	}
	return $lectureArr;
}

/**
 * get list of all lectures in current section to display
 * Xem lại bài giảng ngữ phap 1 2 3
 * Xem lại bài giảng từ vựng 1 2 3
 in quiz page
 * @param int $cmid quiz cmid
 * @param Activity array $activityArr
 * @return Associate array of array of lectureObj
 * $activityArr['vocab'][]
 * $activityArr['listening'][]
 */
function getLectureListOfCurrentQuiz($cmid, $activityArr) {
	if(empty($cmid) || empty($activityArr)) {
		return false;
	}
	$lectureArr = array();
	foreach($activityArr as $selectedActivity) {
		$lectureArr[$selectedActivity->name] = array();
		foreach($selectedActivity->los as $lo) {
			if($lo->type == "lecture") {
				$lectureArr[$selectedActivity->name][] = $lo;
			}
		}
	}
	return $lectureArr;

}

function isTicketRequired($userid, $courseid) {
	if($courseid == 1) {
		return false;
	}
	$context = get_context_instance(CONTEXT_COURSE, $courseid);
	if(empty($context)) {
		return false;
	}
	$course = get_record("course", "id", $courseid, "", "", "",  "","cost, currency");
	if(empty($course)) {
		return false;
	}
	
	$result = get_record_select("role_assignments", "contextid=$context->id AND userid=$userid AND roleid >= 5", "id");
	if(!empty($result) && !empty($course->cost) && $course->cost > 0) {
		return $course->cost;
	}
	return false;
}






?>