<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');
require_once $CFG->dirroot .'/smartcom/util/memcachedutil.php';

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
	//$memcached->set($key, $results, 24*3600);
	$memcached->set($key, $results, 10);
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
	$memcached->set($key, $cmArr, 10);	
	return $cmArr;

}

//$results = getCourseSupportTools(104);
//foreach($results as $result) {
//	echo "$result->id , $result->name, $result->modulename";
//}

?>