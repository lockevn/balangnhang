<?php // $Id: configpopup.php,v 1.3 2007/01/05 03:17:39 mark-nielsen Exp $
/**
 * This file is used by config.html to test the user's relative path
 * to the gallery directory.
 *
 * @author Mark Nielsen
 * @version $Id: configpopup.php,v 1.3 2007/01/05 03:17:39 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 **/

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/gallery/lib.php');
    print_header();
    
    if ($path = optional_param('path', 0, PARAM_CLEAN)) {
        if (gallery_get_embedpath($path)) {
            notify(get_string('embedpathcorrect', 'gallery'), 'notifysuccess');
        } else {
            notify(get_string('embedpathincorrect', 'gallery'));
            print_string('pathsattempted', 'gallery');
            echo ":<ul><li>{$CFG->dirroot}{$path}embed.php</li><li>{$path}embed.php</li></ul>";
        }
    } else if ($path = optional_param('pathuri', 0, PARAM_CLEAN)) {
        
        if (substr($path, 0, 1) == '/') { // Not a full URI
            // Find our base URL
            $me = me();  // current page
            $qualifiedme = qualified_me();  // current page + base URL

            $baseurl = str_replace($me, '', $qualifiedme);  // get rid of current page information
            $testpath = $baseurl.$path.'main.php';
        } else {
            // Full URI expected
            $testpath = $path.'main.php';
        }
        
        // Test to see if main.php can be found.  Supress errors!
        if (@file($testpath)) {
            notify(get_string('g2uricorrect', 'gallery'), 'notifysuccess');
        } else {
            notify(get_string('uripathincorrect', 'gallery', $testpath));
        }
    } else {
        notify(get_string('nothingentered', 'gallery'));
    }
    
    close_window_button();
?>