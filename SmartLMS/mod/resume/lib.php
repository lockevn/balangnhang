<?PHP  // $Id: lib.php,v 1.4 2003/11/21 14:03:06 moodler Exp $

/// Library of functions and constants for module resume


define("RESUME_MAX_NAME_LENGTH", 50);

function resume_add_instance($resume) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $resume->name = strip_tags($resume->content);
    if (strlen($resume->name) > RESUME_MAX_NAME_LENGTH) {
        $resume->name = substr($resume->name, 0, RESUME_MAX_NAME_LENGTH)."...";
    }
    $resume->timemodified = time();

    return insert_record("resume", $resume);
}


function resume_update_instance($resume) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $resume->timemodified = time();
    $resume->id = $resume->instance;
    $resume->name = strip_tags($resume->content);
    if (strlen($resume->name) > RESUME_MAX_NAME_LENGTH) {
        $resume->name = substr($resume->name, 0, RESUME_MAX_NAME_LENGTH)."...";
    }

    return update_record("resume", $resume);
}


function resume_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $resume = get_record("resume", "id", $id)) {
        return false;
    }

    $result = true;

    if (! delete_records("resume", "id", $resume->id)) {
        $result = false;
    }

    return $result;
}

function resume_get_participants($resumeid) {
//Returns the users with data in one resource
//(NONE, but must exist on EVERY mod !!)

    return false;
}

function resume_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any 
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php

   return false;
}

?>
