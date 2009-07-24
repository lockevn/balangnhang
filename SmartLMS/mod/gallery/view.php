<?php // $Id: view.php,v 1.3 2007/01/05 03:17:40 mark-nielsen Exp $
/**
 * Gallery Module
 *
 * This page prints a particular instance of gallery.
 *
 * READ THIS BEFORE MODIFYING THIS CODE
 *     The gallery code automatically creates a gallery object
 *     so the variable $gallery is off limits!
 *
 * @author Mark Nielsen
 * @version $Id: view.php,v 1.3 2007/01/05 03:17:40 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 * @todo Possible idea: force gallery data directory to be in moodle's data dir and gallery install in /gallery/
 *       Check permissions for graceful fail if user does not have permission to access gallery item
 **/

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/gallery/lib.php');
    
    $id       = required_param('id', PARAM_INT); // Course Module ID
    $g2view   = optional_param('g2_view', 0, PARAM_CLEAN);
    $g2itemid = optional_param('g2_itemId', 0, PARAM_INT);

    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
            error('Course Module ID was incorrect');
        }
    
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
    
        if (! $instance = get_record('gallery', 'id', $cm->instance)) {
            error('Course module is incorrect');
        } else {
            // prep the permissions so they can be used
            $instance->permissions = gallery_decode_permissions($instance->permissions);
        }
    } else {
        error('Course Module ID was incorrect');
    }

    require_login($course->id);

    add_to_log($course->id, 'gallery', 'view', "view.php?id=$cm->id", $instance->id);

    if ($course->category) {
        $navigation = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }

    $strgallerys = get_string('modulenameplural', 'gallery');
    $strgallery  = get_string('modulename', 'gallery');

/// need to make sure we have the relativeG2path config variable set
    if (!gallery_check_config()) {
        print_header("$course->shortname: $instance->name", "$course->fullname",
                 "$navigation <a href=index.php?id=$course->id>$strgallerys</a> -> $instance->name", 
                  '', '', true, update_module_button($cm->id, $course->id, $strgallery), navmenu($course, $cm));
        
        if (isadmin()) {
            notify(get_string('gallerynotconfiguredadmin', 'gallery', $CFG->wwwroot));
        } else {
            notify(get_string('gallerynotconfigured', 'gallery'));
        }
        
        print_footer($course);
        exit();
    }
    
// Redirect the user into the album for this gallery instance
//  if user is coming from:
//     course/view
//     course/mod
//     mod/gallery/index
    $referer = get_referer();
    if (!empty($instance->albumid) and ($referer == $CFG->wwwroot.'/course/view.php' or 
                                        $referer == $CFG->wwwroot.'/mod/gallery/index.php' or
                                        $referer == $CFG->wwwroot.'/course/mod.php') ) {
        
        redirect('view.php?id='.$cm->id.'&g2_view=core.ShowItem&g2_itemId='.$instance->albumid);
    }

/// All prechecking finished - start running gallery code

    gallery_init($cm->id);

    // run synchronizations
    if (!gallery_group_sync($course->id, $instance)) {
        error('Group Synchronization Failed.');
    }
    if (!gallery_permissions_sync($course->id, $instance)) {
        error('Permissions Synchronization Failed.');
    }
    
    _gallery_upgrade();

    // Process the G2 request.
    $g2data = GalleryEmbed::handleRequest();

    if ($g2data['isDone']) {
        exit(); // G2 has already sent output (redirect or binary data)
    }

/// Break out the javascript and css that needs to be pushed to moodle's header
    $javascript = $css = '';
    if (isset($g2data['headHtml'])) {
        list($title, $gallerycss, $javascript) = GalleryEmbed::parseHead($g2data['headHtml']);
        
        $javascript = implode("\n",$javascript);
        $css = implode("\n",$gallerycss);
    }
    
/// Create Breadcrumb
    $breadcrumb = array();
    $breadcrumb[] = "$navigation <a href=\"$CFG->wwwroot/mod/gallery/index.php?id=$course->id\">$strgallerys</a>";  // base
    
    // Determine if we link the instance name
    if (!empty($g2itemid) and $g2itemid != $instance->albumid) {
        $breadcrumb[] = "<a href=\"$CFG->wwwroot/mod/gallery/view.php?id=$cm->id&amp;g2_itemId=$instance->albumid\">$instance->name</a>";
    } else {
        $breadcrumb[] = $instance->name;
    }
    
    // Build up the breadcrumb based on the gallery2 parents
    if (isset($g2data['themeData'])) {
        $urlGenerator =& $GLOBALS['gallery']->getUrlGenerator();
        foreach ($g2data['themeData']['parents'] as $parent) {
            if ($parent['parentId'] == 0 or $parent['id'] == $instance->albumid) {
                // Skip root album and linked album
                continue;
            }
            $parent_title = $parent['title'];
            // Simple strip of bbcode (italics) 
            $parent_title = str_replace("[i]", "<i>", $parent_title);
            $parent_title = str_replace("[/i]", "</i>", $parent_title);
            // Add the link
            $breadcrumb[] = '<a href="'.$urlGenerator->generateUrl(array('itemId' => $parent['id'])) .'">'.$parent_title."</a>";
        }
    }
    // Add our current location unless viewing our linked album
    if (!empty($g2itemid) and $g2itemid != $instance->albumid) {
        $breadcrumb[] = $title;
    }
    $breadcrumb = implode('->', $breadcrumb); // make it

    gallery_r();
    print_header("$course->shortname: $instance->name", "$course->fullname", "$breadcrumb",
              '', "\n <!-- Gallery Header Info --> \n $javascript \n $css \n <!-- End Gallery Info --> \n", 
              true, update_module_button($cm->id, $course->id, $strgallery), navmenu($course, $cm));
    
    // For students, analyze the item ID and make sure it is or is a child of the linked album
    if (isstudent($course->id)) {
        if ($g2data['themeData']['item']['id'] != $instance->albumid) {
            if (empty($g2data['themeData']['parents'][1]) or 
                $g2data['themeData']['parents'][1]['id'] != $instance->albumid) {
                
                error(get_string('youcannotview', 'gallery'), 
                      "$CFG->wwwroot/mod/gallery/view.php?id=$cm->id&g2_view=core.ShowItem&g2_itemId=$instance->albumid");
            }
        }
    }
    
    echo $g2data['bodyHtml'];

    //Debug: Helps with looking for div id/class
    //foreach ($g2data as $key => $value) {
    //    echo "<p><p>Key: $key </p></p>";
    //    
    //    if ($key == 'themeData') {
    //        print_object($value);
    //    } else {
    //        echo htmlentities($value);
    //    }
    //}
    
/// Complete the G2 transaction.
    $ret = GalleryEmbed::done();
    if ($ret) {
        error($ret->getAsHtml()); // has error details..
    }
    gallery_r();

/// Finish the page
    print_footer($course);

?>
