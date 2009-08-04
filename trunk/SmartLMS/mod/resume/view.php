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
    
        if (! $resume = get_record("resume", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $resume = get_record("resume", "id", $l)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $resume->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("resume", $resume->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    //*****Can't find a datalib.php method that will construct a suitable SQL query for this module*****
    //*****So write it by hand here instead*****

    $sql="SELECT module, url FROM ".$CFG->prefix."log WHERE userid=$USER->id AND course=$course->id AND module!=\"course\" AND module!=\"resume\" AND action=\"view\" ORDER BY time DESC";
    $record=get_record_sql($sql,true);

    if ($record==false)
        redirect("$CFG->wwwroot/course/view.php?id=$course->id");
    else
    {
        redirect("$CFG->wwwroot/mod/".$record->module."/".$record->url);
    }
?>

