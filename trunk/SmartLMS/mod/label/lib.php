<?php  // $Id: lib.php,v 1.12.4.11 2008/10/08 01:46:40 fmarier Exp $

/// Library of functions and constants for module label




define("LABEL_MAX_NAME_LENGTH", 50);

function get_label_name($label) {
    $textlib = textlib_get_instance();

    $name = addslashes(strip_tags(format_string(stripslashes($label->content),true)));
    if ($textlib->strlen($name) > LABEL_MAX_NAME_LENGTH) {
        $name = $textlib->substr($name, 0, LABEL_MAX_NAME_LENGTH)."...";
    }

    if (empty($name)) {
        // arbitrary name
        $name = get_string('modulename','label');
    }

    return $name;
}

function label_add_instance($label) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $label->name = get_label_name($label);
    $label->timemodified = time();
	$result = insert_record("label", $label);
	global $CFG;
	
	include_once($CFG->libdir.'/filelib.php');
	include_once($CFG->libdir.'/moodlelib.php');
	
	/*danhut added: create data folder, category corresponding to this new label (which is a smartcom activity)*/
	/*retrieve section object with courseid and section index to get the parent directory (Lesson 1, Review1 ...)*/
	$cs = get_record('course_sections', 'course', $label->course, 'section', $label->section);
	if($cs) {
		
    	
		$sectionLabel = strtolower($cs->label);
		$labelName = strtolower($label->name);
		
		
		/*make data directory*/
		make_upload_directory("$label->course/$sectionLabel/$labelName");
		
		/*retrieve course context*/
		$context = get_record('context', 'contextlevel', CONTEXT_COURSE, 'instanceid', $label->course);
		if($context) {
			
			/*find the parent category e.g.: Lesson 1, review 1 ...*/
			$parentCat = get_record('question_categories', 'contextid', $context->id, 'name', strtolower($sectionLabel)) ;			
			if($parentCat) {
				/*check if category with the same level and name already exists*/
				$oldCat = get_record('question_categories', 'contextid', $context->id, 'parent', $parentCat->id, 'name', $labelName);
				/*if not yet exist, insert new category*/
				if(!$oldCat) {
					/*add new category corresponding to new label as child category of $parentCat*/				
					require_capability('moodle/question:managecategory', $context);
					$sql = "select count(id) as count from $CFG->prefix" . "question_categories where contextid=$context->id AND parent=$parentCat->id";
					$retObj = get_record_sql($sql);
					if($retObj) {
						$count = $retObj->count;
					} else {
						$count = 0;
					}
					$cat = new object();
					$cat->name = $labelName;
					$cat->contextid = $context->id;
					$cat->info = "$sectionLabel/$labelName category";
					$cat->sortorder = $count;
					$cat->parent = $parentCat->id;
					$cat->stamp = make_unique_id_code(); 
					insert_record('question_categories', $cat);
				}
			}			
		}
	}	
	return $result;
        
}

function label_update_instance($label) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $label->name = get_label_name($label);
    $label->timemodified = time();
    $label->id = $label->instance;

    return update_record("label", $label);
}


function label_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $label = get_record("label", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("label", "id", "$label->id")) {
        $result = false;
    }

    return $result;
}

function label_get_participants($labelid) {
//Returns the users with data in one resource
//(NONE, but must exist on EVERY mod !!)

    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 */
function label_get_coursemodule_info($coursemodule) {
    if ($label = get_record('label', 'id', $coursemodule->instance, '', '', '', '', 'id, content, name')) {
        if (empty($label->name)) {
            // label name missing, fix it
            $label->name = "label{$label->id}";
            set_field('label', 'name', $label->name, 'id', $label->id);
        }
        $info = new object();
        $info->extra = urlencode($label->content);
        $info->name = urlencode($label->name);
        return $info;
    } else {
        return null;
    }
}

function label_get_view_actions() {
    return array();
}

function label_get_post_actions() {
    return array();
}

function label_get_types() {
    $types = array();

    $type = new object();
    $type->modclass = MOD_CLASS_RESOURCE;
    $type->type = "label";
    $type->typestr = get_string('resourcetypelabel', 'resource');
    $types[] = $type;

    return $types;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function label_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 */
function label_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

?>
