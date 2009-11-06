<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');

function deleteTestRanges($testid) {
	return delete_records("smartcom_testroom", 'testid', $testid );
}

function addTestRanges($testid, $gradeArr, $mainCourseArr, $minorCourseArr, $nextTestIdArr) {
	if(empty($testid) || empty($gradeArr) || empty($mainCourseArr)) {
		return false;
	}
	$maxGrade = $gradeArr[0];
	$maxGradeIndex = 0;
	for($i = 1; $i < sizeof($gradeArr); $i++) {
		$minGrade = $gradeArr[$i];
		if(empty($minGrade)) {
			continue;
		}
		if($minGrade > $maxGrade) {
			return false;
		}
		/*nếu tìm thấy $minGrade, insert vào db*/
		$nextTestId = $nextTestIdArr[$maxGradeIndex];
		if(empty($nextTestId)) {
			$mainCourseId = $mainCourseArr[$maxGradeIndex];
			$minorCourseId1 = $minorCourseArr[$maxGradeIndex * 2];
			$minorCourseId2 = $minorCourseArr[$maxGradeIndex * 2 + 1];
		}
		if(!empty($mainCourseId) || !empty($nextTestId)) {
			$range = new object();
			$range->testid = $testid;
			$range->mingrade = $minGrade;
			$range->maxgrade = $maxGrade;
			$range->nexttestid = $nextTestId;
			if(empty($nextTestId)) {
				$range->maincourseid = $mainCourseId;
				$range->minorcourseid1 = $minorCourseId1;
				$range->minorcourseid2 = $minorCourseId2;
			}
			
			insert_record("smartcom_testroom", $range);			
		}
		
		$maxGrade = $minGrade;			
		$maxGradeIndex = $i;	
	}
}

/**
 * get array of course obj từ kết quả test
 *
 * @param unknown_type $testid
 * @param unknown_type $percentage
 */
function selectCourseByGrade($testid, $percentage) {
	if(empty($testid) || empty($percentage)) {
		return false;
	}
	$obj = get_record_select("smartcom_testroom", "testid = $testid AND mingrade <= $percentage AND $percentage < maxgrade");
	if(!$obj) {
		return false;
	}
	$courseArr = array();
	
	/*nếu có test tiếp theo, trả về id test tiếp theo */
	if(!empty($obj->nexttestid)) {
		return $obj->nexttestid;
	}
	if(isset($obj->maincourseid)) {
		$mainCourseObj = get_record("course", "id", $obj->maincourseid, 'visible', 1, '', '', 'id, category, fullname, shortname');
		if($mainCourseObj) {
			/*get category name*/
			$cat = get_record("course_categories", "id", $mainCourseObj->category, 'visible', 1, '','', 'name');
			if($cat) {
				$mainCourseObj->categoryname = $cat->name;
			}
			$courseArr["maincourse"] = $mainCourseObj;
		}		
	}
	if(isset($obj->minorcourseid1)) {
		$minorCourseObj = get_record("course", "id", $obj->minorcourseid1);
		$courseArr["minorcourse1"] = $minorCourseObj;
	}
	if(isset($obj->minorcourseid2)) {
		$minorCourseObj = get_record("course", "id", $obj->minorcourseid2);
		$courseArr["minorcourse2"] = $minorCourseObj;
	}
	if(empty($courseArr)) {
		return false;
	}
	return $courseArr;
	
}



?>