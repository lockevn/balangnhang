<?php  // $Id: view.php,v 1.42 2007/08/17 12:15:33 skodak Exp $

    require_once("../../config.php");
    require_once("lib.php");
    require_once($CFG->libdir.'/weblib.php');
 
    $id = optional_param('id', 0, PARAM_INT);  // Course Module ID
    $a  = optional_param('a', 0, PARAM_INT);   // Assignment ID

    if ($id) {
        if (! $cm = get_coursemodule_from_id('assignment', $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $assignment = get_record("assignment", "id", $cm->instance)) {
            error("assignment ID was incorrect");
        }

        if (! $course = get_record("course", "id", $assignment->course)) {
            error("Course is misconfigured");
        }
    } else {
        if (!$assignment = get_record("assignment", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $assignment->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course, true, $cm);

    require ("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

    $assignmentinstance->view();   // Actually display the assignment!

?>
