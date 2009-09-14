<?PHP  // $Id: view.php,v 1.1 2004/5/10 15:46:00 moodler Exp $

    require_once("../../config.php");

    $id = required_param('id',PARAM_INT);    // Course Module ID, or

    if ($id) {
    
        if (! $course = get_record("course", "id", $id)) {
            error("Course does not exist");
        }            
    } 

    require_login($course->id);

    //*****Can't find a datalib.php method that will construct a suitable SQL query for this module*****
    //*****So write it by hand here instead*****

    $sql="SELECT module, url FROM ".$CFG->prefix."log WHERE userid=$USER->id AND course=$course->id AND module in ('quiz','resource', 'assignment') ORDER BY time DESC";
    $record=get_record_sql($sql,true);

    if ($record==false)
        redirect("$CFG->wwwroot/course/view.php?id=$course->id");
    else
    {
        redirect("$CFG->wwwroot/mod/".$record->module."/".$record->url);
    }
?>

