<?PHP  // $Id: lib.php,v 1.4 2003/11/21 14:03:06 moodler Exp $

/// Library of functions and constants for module start


define("START_MAX_NAME_LENGTH", 50);

function start_add_instance($start) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $start->name = strip_tags($start->content);
    if (strlen($start->name) > START_MAX_NAME_LENGTH) {
        $start->name = substr($start->name, 0, START_MAX_NAME_LENGTH)."...";
    }
    $start->timemodified = time();

    return insert_record("start", $start);
}


function start_update_instance($start) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $start->timemodified = time();
    $start->id = $start->instance;
    $start->name = strip_tags($start->content);
    if (strlen($start->name) > START_MAX_NAME_LENGTH) {
        $start->name = substr($start->name, 0, START_MAX_NAME_LENGTH)."...";
    }

    return update_record("start", $start);
}


function start_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $start = get_record("start", "id", $id)) {
        return false;
    }

    $result = true;

    if (! delete_records("start", "id", $start->id)) {
        $result = false;
    }

    return $result;
}

function start_get_participants($startid) {
//Returns the users with data in one resource
//(NONE, but must exist on EVERY mod !!)

    return false;
}

function start_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any 
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php

   return false;
}

?>
