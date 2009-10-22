<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Unit Testing</title>
<style>
body
{
    font-size: 100%;
    font-family: Arial;
}
</style>
</head>
<body>
<?php

 /*
 * created by Andrew Chow, of Lambda Solutions Inc., Vancouver, BC, Canada
 * http://www.lambdasolutions.net/ - andrew@lambdasolutions.net
 * based on block tutorial by Jon Papaioannou (pj@uom.gr)
 * with all the French translation files in /lang/fr_utf8/ created by Valery Fremaux at http://www.ethnoinformatique.fr/

*  unit_test.php - part of block_completion_report
*            - batch testing of all the custom-built functions used in block_completion_report
*              using the unit_test function above to step through the range of input and displays the corresponding output
*/

require_once('../../config.php');
require_once($CFG->dirroot .'/lib/datalib.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->dirroot .'/lib/blocklib.php');
require_once('lib.php');

$str_test_function = optional_param('test_function', '', PARAM_RAW); 

/** list of functions in lib.php tested by unit_test.php
function strChecked ( $bln_status ) 
function check_user_status ( $courseid=0, $userid=0 ) 
function array_of_required_resources_name( $courseid ) 
function display_required_resources( $courseid, $return=true ) 
function clearview_check( $courseid )
function array_of_accessed_resources_name( $courseid, $userid ) 
function array_of_accessed_required_resources_id( $courseid, $userid ) 
function display_accessed_resources( $courseid, $userid )
**/
// array containing the name of the functions, used to generate the drop down menu in HTML
$arr_function_names = array(
      'strChecked'
    , 'check_user_status'
    , 'array_of_required_resources_name'
    , 'display_required_resources'
    , 'clearview_check'
    , 'array_of_accessed_resources_name'
    , 'array_of_accessed_required_resources_id'
    , 'display_accessed_resources'
);


if ($str_test_function!='') {
    echo '<h3>Testing Function: \'' .  $str_test_function . '\'</h3>';
} else {
    echo '<h3>Completion Report Unit_Testing</h3>';
}

?>
<form action="unit_test.php" action="post">
<select name="test_function" onchange="this.form.submit()">
<option value="">select a function</option>

<?php
// generates the dropdown menu using $arr_function_names
foreach ($arr_function_names as $str_function_names) {
    if ( $str_function_names == $str_test_function ) {
        echo '<option selected value="' . $str_function_names . '">' . $str_function_names . '</option>';
    } else {
        echo '<option value="' . $str_function_names . '">' . $str_function_names . '</option>';
    }
}
?>
</select>
</form>

<?php

// list of boolean switches to display the various unit tests, one for each function
$bln_strChecked = false; // checked
$bln_check_user_status = false; // $str_role not ready
$bln_array_of_required_resources_name = false; // checked
$bln_clearview_check = false; // checked for normal, not checked for clearview (but should be okay since tested in lambdadev)
$bln_display_required_resources = false; // checked
$bln_array_of_accessed_resources_name = false; // checked
$bln_array_of_accessed_required_resources_id = false; // checked
$bln_display_accessed_resources = false; // checked but need $bln_array_of_accessed_resources_name

$arr_all_users = get_users_confirmed (true, '', false, '', 'firstname ASC', '', '', '', '', '*');
$arr_all_courses = get_courses();

switch ( $str_test_function ) {
    case 'strChecked':
        $bln_strChecked = true;
        break;
    case 'check_user_status':
        $bln_check_user_status = true;
        break;
    case 'array_of_required_resources_name':
        $bln_array_of_required_resources_name = true;
        break;
    case 'clearview_check':
        $bln_clearview_check = true;
        break;
    case 'display_required_resources':
        $bln_display_required_resources = true;
        break;
    case 'array_of_accessed_resources_name':
        $bln_array_of_accessed_resources_name = true;
        break;
    case 'array_of_accessed_required_resources_id':
        $bln_array_of_accessed_required_resources_id = true;
        break;
    case 'display_accessed_resources':
        $bln_display_accessed_resources = true;
        break;
}

/**
* function unit_test( $input, $output, $str_function_name ) 
* simple function to display the inputs to a function and the output from it
* produces nice and simple HTML, e.g:
* <hr />
<b>function: </b> $str_function_name
<b>Input of type -</b> string<b>:</b> $input
<b>Output of type -</b> boolean<b>:</b> $output
* <hr />
* @param boolean $input - string displaying input information
* @param boolean $output - string displaying $output information
* @param boolean $str_function_name - string displaying $str_function_name information
* @return null
* action echo HTML
*/
function unit_test( $input=null, $output=null, $str_function_name='' ) {
    if ( $str_function_name!= '' ) {    
        
        $str_output = '<br /><b>function: </b>' . $str_function_name;
        
        $str_input_type =  gettype($input);
        
        if ( empty ($input) || $input==null  ) {
            $input = 'null';
        } // end of null input test
    
        if ( $str_input_type == 'boolean'  ) {
            if ( $input===true ) {
                $input = 'true';
            } else {
                $input = 'false';
            }
        } // end of boolean input test
    
        $str_output .= '<br /><b>Input of type -</b> ' .  $str_input_type . '<b>:</b> '. $input ;
        
        $str_output_type =  gettype($output);
        
        if ( $str_output_type == 'boolean'  ) {
            if ( $output===true ) {
                $output = 'true';
            } else {
                $output = 'false';
            }
        } // end of boolean output test
          
        if ( $str_output_type == 'array' ) {
            $output = '<pre>' . print_r( $output, true ) . '</pre>';
        }
        if ( empty ($output) || $output==null  ) {
            $output = 'null';
        } else if ( trim($output) == '' ) {
            $output = 'empty or blank string';
        } // end of null output test
    
        $str_output .= '<br /><b>Output of type -</b> ' .  $str_output_type . '<b>:</b> '. $output ;

        $str_output .= '<hr />' ;

        echo $str_output;
    } // end of   if ( $str_function_name!= '' )
}
// end of function unit_test( $input, $output, $str_function_name )


/* 
* Testing  
* function strChecked ( $bln_status ) 
* simple function to provide support in html form processing
* produces the attribute checked="checked" when needed
* @param boolean $bln_status
* @return string ' checked="checked" ' if $bln_status is 'on'
*                ' ' if $bln_status is 'off'
*/

if ( $bln_strChecked == true ) {
    $bln_status = true;
    unit_test( $bln_status, strChecked($bln_status), 'strChecked');
    
    $bln_status = false;
    unit_test( $bln_status, strChecked($bln_status), 'strChecked');
}

/*
* Testing
* function check_user_status ( $courseid=0, $userid=0 ) 
* check the current user's status using the Moodle function
* (@link has_capability() ) makes use of the global variables
* @param global $CFG    - to access server location in case of testing
* @param global $USER   - to access user id
* @param global $COURSE - to define context and permission
* @return boolean true - if user has access to completion report
*                        either as site:administrator
*                        or site:editing teacher
*                        or site:nonediting teacher
*                        or course:editing teacher
*                        or course:nonediting teacher
*                        or course:administrator
*/

if ( $bln_check_user_status == true ) {    
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        foreach ( $arr_all_users as $obj_user ) {
            $userid = $obj_user->id;
            $context = get_context_instance( CONTEXT_COURSE, $courseid );
            $str_role = get_user_roles_in_context($userid, $context->id);
            echo '<b>user:</b>' . $obj_user->firstname . ' ' . $obj_user->lastname . ' ' . $str_role .  '<br />';
            echo '<b>role:</b>' . $str_role .  '<br />';
            unit_test( 'courseid: null, userid: null', check_user_status ( $courseid, $userid ), 'check_user_status' );
        }
    }
}


/*
* Testing
* function array_of_required_resources_name( $courseid )
* Returns list of Required Resources id, retrieved from the database
* table completion_configure using the parameter $courseid (see below)
* the output is an array from the Moodle function (@link get_fieldset_sql() )
* or null if either the courseid is invalid or no record was found in the table
* 
* @param int $courseid - a valid id from the course table
* @return array from the Moodle function (@link get_fieldset_sql() ) or null
*/ 

if ( $bln_array_of_required_resources_name == true ) {    
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        unit_test( 'courseid: ' . $courseid , array_of_required_resources_name ( $courseid), 'array_of_required_resources_name' );
    }
}

/*
* Testing
* function display_required_resources( $courseid, $return=true ) 
* Displays an unordered list of the resources which are required
* in order to meet the Course Completion criteria.
* The list of Required Resources are retrieved using the function
* (@link array_of_required_resources() )
* 
* @param int $courseid - a valid id from the course table
* @param boolean $return - default to true - outputs string of html instead of echo
*                        - false - echoes output
* @return true   - if echo was successful
*         string - html containing header, name of course, and unordered list
*         false  - if courseid was invalid
*/ 

if ( $bln_display_required_resources == true ) {    
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        unit_test( 'courseid: ' . $courseid , display_required_resources ( $courseid), 'display_required_resources' );
    }
}

/*
* Testing
* function clearview_check( $courseid )
 * simple function to provide support in sql query processing
 * produces the segment for the where clause when checking log table action column
 * 
 * @param int $courseid
 * @return string " AND action='view' " if the course is a regular Moodle course
 *                " AND action='clearview' " if the course is in ClearView format
 *
*/

if ( $bln_clearview_check == true ) {
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        unit_test( 'courseid: ' . $courseid , clearview_check ( $courseid), 'clearview_check' );
    }
}

/*
* Testing
* function array_of_accessed_resources_name( $courseid, $userid ) {
*/

if ( $bln_array_of_accessed_resources_name == true ) {
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        foreach ( $arr_all_users as $obj_user ) {
            $userid = $obj_user->id;
            echo '<b>user:</b>' . $obj_user->firstname . ' ' . $obj_user->lastname . '<br />';
            unit_test( 'courseid: ' . $courseid , array_of_accessed_resources_name ( $courseid, $userid), 'array_of_accessed_resources_name' );
        }
    }
}


/*
* Testing
* function array_of_accessed_required_resources_id( $courseid, $userid ) {
*/

if ( $bln_array_of_accessed_required_resources_id == true ) {
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        foreach ( $arr_all_users as $obj_user ) {
            $userid = $obj_user->id;
            echo '<b>user:</b>' . $obj_user->firstname . ' ' . $obj_user->lastname . '<br />';
            unit_test( 'courseid: ' . $courseid , array_of_accessed_required_resources_id ( $courseid, $userid), 'array_of_accessed_required_resources_id' );
        }
    }
}

/*
* Testing
* function display_accessed_resources( $courseid, $userid )
 * Displays an unordered list of the resources which are required
 * in order to meet the Course Completion criteria.
 * The list of Required Resources are retrieved from the database
 * table completion_configure using the parameter $courseid (see below)
 * the output is an array from the Moodle function (@link get_fieldset_sql() )
 * or null if either the courseid is invalid or no record was found in the table
 * 
 * @param int $courseid - a valid id from the course table
 * @param int $userid
 * @return array from the Moodle function (@link get_fieldset_sql() ) or null 
 */         

if ( $bln_display_accessed_resources == true ) {
    foreach ( $arr_all_courses as $obj_course ) {
        echo '<hr /><b>course:</b> ' . $obj_course->fullname . '<hr />';
        $courseid = $obj_course->id;
        foreach ( $arr_all_users as $obj_user ) {
            $userid = $obj_user->id;
            echo '<b>user:</b>' . $obj_user->firstname . ' ' . $obj_user->lastname . '<br />';
            unit_test( 'courseid: ' . $courseid , display_accessed_resources ( $courseid, $userid), 'display_accessed_resources' );
        }
    }
}



?>
</body>
</html>