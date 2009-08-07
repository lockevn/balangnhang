<?PHP  // $Id: view.php,v 1.1 2004/5/10 15:46:00 moodler Exp $

require_once("../../config.php");

$id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
$l = optional_param('l',0,PARAM_INT);     // Label ID

if ($id) {
	if (! $cm = get_record("course_modules", "id", $id)) {
		error("Course Module ID was incorrect");
	}

	if (! $course = get_record("course", "id", $cm->course)) {
		error("Course is misconfigured");
	}

	if (! $start = get_record("start", "id", $cm->instance)) {
		error("Course module is incorrect");
	}

} else {
	if (! $start = get_record("start", "id", $l)) {
		error("Course module is incorrect");
	}
	if (! $course = get_record("course", "id", $start->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("start", $start->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
}

require_login($course->id);

//*****Can't find a datalib.php method that will construct a suitable SQL query for this module*****
//*****So write it by hand here instead*****

//    $sql="SELECT numsections FROM ".$CFG->prefix."course WHERE 1 AND id=$course->id";
//    $record=get_record_sql($sql);
//    $numSections=$record->numsections;

//    for ($section=1; $section <= $numSections ; $section++)
//    {

/* danhut: thay vì start first resource of course thì
 * start first resource (resource type 0 phải label, start)
 * của section mà start button thuộc về*/

$sql="SELECT sequence FROM ".$CFG->prefix."course_sections WHERE course=$course->id AND id=".$cm->section;
$record=get_record_sql($sql);
$allResources=explode(",", $record->sequence);
for ($loop=0; $loop<count($allResources); $loop++)
{
	$sql="SELECT cm.module, cm.visible, m.name FROM ".$CFG->prefix."course_modules cm, ".$CFG->prefix."modules m
	WHERE 1 AND cm.`id` = '$allResources[$loop]' AND cm.module = m.id AND m.name not in ('label', 'start', 'resume')";
	$record=get_record_sql($sql);
	if ($record==true && $record->visible==1)
	{
		redirect("$CFG->wwwroot/mod/".$record->name."/view.php?id=".$allResources[$loop]);
		die;
	}
}

/*không tìm thấy resource thỏa mãn, view course page*/
redirect("$CFG->wwwroot/course/view.php?id=$course->id");
?>

