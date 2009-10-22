<?PHP  // $Id: view.php,v 1.1 2004/5/10 15:46:00 moodler Exp $

require_once("../../config.php");

$id = required_param('id', PARAM_INT);    // course id
$sectionIndex = required_param('section' ,PARAM_INT);    // section index

if (! $cs = get_record("course_sections", "course", $id, "section", $sectionIndex)) {
	error("Course id and section index were incorrect");
}

require_login($id);


$allResources=explode(",", $cs->sequence);
for ($loop=0; $loop<count($allResources); $loop++)
{
	$sql="SELECT cm.module, cm.visible, m.name FROM ".$CFG->prefix."course_modules cm, ".$CFG->prefix."modules m
	WHERE 1 AND cm.`id` = '$allResources[$loop]' AND cm.module = m.id AND m.name not in ('label')";
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

