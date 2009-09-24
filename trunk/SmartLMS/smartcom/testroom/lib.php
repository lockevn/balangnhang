<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');

function deleteTestRanges($testid) {
	return delete_records("smartcom_testroom", 'testid', $testid );
}

function addTestRanges($testid, $gradeArr, $mainCourseArr, $minorCourseArr) {
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
		$mainCourseId = $mainCourseArr[$maxGradeIndex];
		$minorCourseId1 = $minorCourseArr[$maxGradeIndex * 2];
		$minorCourseId2 = $minorCourseArr[$maxGradeIndex * 2 + 1];
		if($mainCourseId) {
			$range = new object();
			$range->testid = $testid;
			$range->maincourseid = $mainCourseId;
			$range->minorcourseid1 = $minorCourseId1;
			$range->minorcourseid2 = $minorCourseId2;
			$range->mingrade = $minGrade;
			$range->maxgrade = $maxGrade;
			insert_record("smartcom_testroom", $range);			
		}
		
		$maxGrade = $minGrade;			
		$maxGradeIndex = $i;	
	}
}

?>