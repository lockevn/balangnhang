<?php // $Id: index.php,v 1.2 2007/01/05 03:17:39 mark-nielsen Exp $
/**
 * Gallery Module index.php page
 *
 * This page lists all the instances of gallery in a particular course
 * Replace gallery with the name of your module
 *
 * @author Mark Nielsen
 * @version $Id: index.php,v 1.2 2007/01/05 03:17:39 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 **/
 
    require_once('../../config.php');
    require_once('lib.php');

    $id = required_param('id', PARAM_INT);

    if (! $course = get_record('course', 'id', $id)) {
        error('Course ID is incorrect');
    }

    require_login($course->id);

    add_to_log($course->id, 'gallery', 'view all', "index.php?id=$course->id", '');

/// Get all required strings

    $strgallerys = get_string('modulenameplural', 'gallery');
    $strgallery  = get_string('modulename', 'gallery');


/// Print the header

    if ($course->category) {
        $navigation = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }

    print_header("$course->shortname: $strgallerys", "$course->fullname", "$navigation $strgallerys", '', '', true, '', navmenu($course));

/// Get all the appropriate data

    if (! $gallerys = get_all_instances_in_course('gallery', $course)) {
        notice('There are no gallerys', "$CFG->wwwroot/course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances

    $timenow = time();
    $strname  = get_string('name');
    $strweek  = get_string('week');
    $strtopic  = get_string('topic');

    if ($course->format == 'weeks') {
        $table->head  = array ($strweek, $strname);
        $table->align = array ('center', 'left');
    } else if ($course->format == 'topics') {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ('center', 'left', 'left', 'left');
    } else {
        $table->head  = array ($strname);
        $table->align = array ('left', 'left', 'left');
    }

    foreach ($gallerys as $gallery) {
        if (!$gallery->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"$CFG->wwwroot/mod/gallery/view.php?id=$gallery->coursemodule\">$gallery->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"$CFG->wwwroot/mod/gallery/view.php?id=$gallery->coursemodule\">$gallery->name</a>";
        }

        if ($course->format == 'weeks' or $course->format == 'topics') {
            $table->data[] = array ($gallery->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo '<br />';

    print_table($table);

/// Finish the page

    print_footer($course);

?>
