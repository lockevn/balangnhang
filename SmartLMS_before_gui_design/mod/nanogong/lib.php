<?php  // $Id: lib.php,v 3.0 2008/07/24 00:00:00 gibson Exp $

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted nanogong record
 **/
function nanogong_add_instance($nanogong) {
    $nanogong->timecreated = time();
    $nanogong->timemodified = time();
    $nanogong->maxmessages = clean_param($nanogong->maxmessages, PARAM_INT);
    $nanogong->maxscore = clean_param($nanogong->maxscore, PARAM_INT);

    return insert_record("nanogong", $nanogong);
}

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function nanogong_update_instance($nanogong) {
    $nanogong->timemodified = time();
    $nanogong->id = $nanogong->instance;
    $nanogong->maxmessages = clean_param($nanogong->maxmessages, PARAM_INT);
    $nanogong->maxscore = clean_param($nanogong->maxscore, PARAM_INT);

    return update_record("nanogong", $nanogong);
}

/**
 * Given an ID of an instance of this module, 
 * this function will permanently delete the instance 
 * and any data that depends on it. 
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function nanogong_delete_instance($id) {
    if (! $nanogong = get_record("nanogong", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (!delete_records("nanogong", "id", "$nanogong->id")) {
        $result = false;
    }
    if ($nanogong_messages = get_records("nanogong_message", "nanogongid", "$nanogong->id")) {
        global $CFG;
        foreach ($nanogong_messages as $nanogong_message) {
            $soundfile = $CFG->dataroot.$nanogong_message->path;
            if (file_exists($soundfile)) unlink($soundfile);
        }
    }
    if (!delete_records("nanogong_message", "nanogongid", "$nanogong->id")) {
        $result = false;
    }

    return $result;
}

/**
 * Must return an array of grades for a given instance of this module, 
 * indexed by user.  It also returns a maximum allowed grade.
 * 
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $modid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function nanogong_grades($modid) {
    if (!$nanogong = get_record('nanogong', 'id', $modid)) {
        return null;
    }

    $grades = array();
    $students = get_course_students($nanogong->course);
    $nanogong_messages = get_records("nanogong_message", "nanogongid", $nanogong->id);
    if ($students != null) {
        foreach ($students as $student) {
            $grade = "-";
            if ($nanogong_messages) {
                $count = 0;
                foreach ($nanogong_messages as $nanogong_message) {
                    if ($nanogong_message->userid != $student->id) continue;
                    if ($grade == "-")
                        $grade = $nanogong_message->score;
                    else
                        $grade += $nanogong_message->score;
                    $count++;
                }
                if ($count > 0) $grade = $grade / $count;
            }
            $grades[$student->id] = $grade;
        }
    }
    $return->grades = $grades;
    $return->maxgrade = $nanogong->maxscore;

    return $return;
}

function nanogong_get_types() {
    $types = array();

    $type = new object;
    $type->modclass = MOD_CLASS_RESOURCE;
    $type->type = 'nanogong';
    $type->typestr = get_string('nanogong', 'nanogong');
    $types[] = $type;

    return $types;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other nanogong functions go here.  Each of them must have a name that 

?>
