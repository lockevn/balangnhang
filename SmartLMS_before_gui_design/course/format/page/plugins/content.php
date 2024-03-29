<?php
/**
 * Page Item Definition
 *
 * @author Mark Nielsen
 * @version $Id$
 * @package format_page
 **/

/**
 * Add content to a block instance. This
 * method should fail gracefully.  Do not
 * call something like error()
 *
 * @param object $block Passed by refernce: this is the block instance object
 *                      Course Module Record is $block->cm
 *                      Module Record is $block->module
 *                      Module Instance Record is $block->moduleinstance
 *                      Course Record is $block->course
 *
 * @return boolean If an error occures, just return false and 
 *                 optionally set error message to $block->content->text
 *                 Otherwise keep $block->content->text empty on errors
 **/
function content_set_instance(&$block) {
    global $CFG;

    require_once($CFG->dirroot.'/mod/content/locallib.php');

    $module = mod_content_plugin::factory('module', $block->cm->id);

    if (!$text = $module->pageitem($block)) {
        // Run the default
        require_once($CFG->dirroot.'/course/format/page/plugins/page_item_default.php');

        return page_item_default_set_instance($block);
    }

    return true;
}

?>