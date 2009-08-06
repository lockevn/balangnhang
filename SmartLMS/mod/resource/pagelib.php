<?php // $Id: pagelib.php,v 1.14.4.1 2007/11/02 16:19:58 tjhunt Exp $

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/course/lib.php'); // needed for some blocks

define('PAGE_RESOURCE_VIEW',   'mod-resource-view');

page_map_class(PAGE_RESOURCE_VIEW, 'page_resource');

$DEFINEDPAGES = array(PAGE_RESOURCE_VIEW);

/**
 * Class that models the behavior of a quiz
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_resource extends page_generic_activity {

    function init_quick($data) {
        if(empty($data->pageid)) {
            error('Cannot quickly initialize page: empty course id');
        }
        $this->activityname = 'resource';
        parent::init_quick($data);
    }
  
    function get_type() {
        return PAGE_RESOURCE_VIEW;
    }
    
	// And finally, a little block move logic. Given a block's previous position and where
    // we want to move it to, return its new position. Pretty self-documenting.
    /*danhut added to enable moving blocks to the right in resource page*/
    function blocks_move_position(&$instance, $move) {
        if ($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
            return BLOCK_POS_RIGHT;
        } else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
            return BLOCK_POS_LEFT;
        }
        return $instance->position;
    }
    
    /*danhut added to add mod-quiz-view option in admin/stickyblocks.php*/
    
	function user_is_editing() {
        global $USER;
		if (has_capability('moodle/my:manageblocks', get_context_instance(CONTEXT_SYSTEM)) && defined('ADMIN_STICKYBLOCKS')) {
            return true;
        }

        return (!empty($USER->editing));
    }
    
	function url_get_path() {
        global $CFG;
        page_id_and_class($id,$class);
        if ($id == PAGE_RESOURCE_VIEW) {
            return $CFG->wwwroot.'/mod/quiz/index.php';
        } elseif (defined('ADMIN_STICKYBLOCKS')){
            return $CFG->wwwroot.'/'.$CFG->admin.'/stickyblocks.php';
        }
    }
    
	function url_get_parameters() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return array('pt' => ADMIN_STICKYBLOCKS);
        } else {
            return array();
        }
    }
    
	function user_allowed_editing() {
        page_id_and_class($id,$class);
        if ($id == PAGE_RESOURCE_VIEW) {
            return true;
        } else if (has_capability('moodle/my:manageblocks', get_context_instance(CONTEXT_SYSTEM)) && defined('ADMIN_STICKYBLOCKS')) {
            return true;
        }
        return false;
    }
    
	function print_header($title) {

        global $USER, $CFG;

        $replacements = array(
                              '%fullname%' => get_string('stickyblocksresourceview','admin')
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        $site = get_site();

        $button = update_mymoodle_icon($USER->id);
        $nav = get_string('stickyblocksresourceview','admin');
        $header = $site->shortname.': '.$nav;
        $navlinks = array(array('name' => $nav, 'link' => '', 'type' => 'misc'));
        $navigation = build_navigation($navlinks);
        
        $loggedinas = user_login_string($site);

        if (empty($CFG->langmenu)) {
            $langmenu = '';
        } else {
            $currlang = current_language();
            $langs = get_list_of_languages();
            $langlabel = get_accesshide(get_string('language'));
            $langmenu = popup_form($CFG->wwwroot .'/my/index.php?lang=', $langs, 'chooselang', $currlang, '', '', '', true, 'self', $langlabel);
        }

        print_header($title, $header,$navigation,'','',true, $button, $loggedinas.$langmenu);

    }
    
    /*end of danhut added*/
}


page_map_class(PAGE_RESOURCE_VIEW, 'page_resource');
?>
