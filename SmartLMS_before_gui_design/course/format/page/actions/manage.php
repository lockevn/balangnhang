<?php
/**
 * Page management
 * 
 * @author Jeff Graham, Mark Nielsen
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

$moving = optional_param('moving', 0, PARAM_INT);

require_capability('format/page:managepages', $context);

$PAGE->print_tabs('manage');

if ($pages = page_get_all_pages($course->id, 'flat')) {
    $table->head = array(get_string('pagename','format_page'),
                         get_string('pageoptions','format_page'),
                         get_string('displaytheme', 'format_page'),
                         get_string('displaymenu', 'format_page'),
                         get_string('publish', 'format_page'));
    $table->align       = array('left', 'center', 'center', 'center', 'center');
    $table->width       = '70%';
    $table->cellspacing = '0';
    $table->id          = 'editing-table';
    $table->class       = 'generaltable pageeditingtable';
    $table->data        = array();

    foreach ($pages as $page) {
        $a       = strip_tags(format_string($page->nameone));
        $editalt = get_string('edita', 'format_page', $a);
        $movealt = get_string('movea', 'format_page', $a);
        $delealt = get_string('deletea', 'format_page', $a);

        // Page link/name
        $name = page_pad_string('<a href="'.$PAGE->url_build('page', $page->id).'" title="'.$a.'">'.format_string($page->nameone).'</a>', $page->depth);

        // Edit, move and delete widgets
        $widgets  = '<a title="'.$editalt.'" href="'.$PAGE->url_build('page', $page->id, 'action', 'editpage', 'returnaction', 'manage').'" class="icon edit"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.$editalt.'" /></a>&nbsp;';
        $widgets .= '<a title="'.$movealt.'" href="'.$PAGE->url_build('action', 'moving', 'moving', $page->id, 'sesskey', sesskey()).'" class="icon move"><img src="'.$CFG->pixpath.'/t/move.gif" alt="'.$movealt.'" /></a>&nbsp;';
        $widgets .= '<a title="'.$delealt.'" href="'.$PAGE->url_build('action', 'confirmdelete', 'page', $page->id, 'sesskey', sesskey()).'" class="icon delete"><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.$delealt.'" /></a>';

        // Theme, menu and publish widgets
        if ($page->parent == 0) {
            // Only master pages get this one
            $theme = page_manage_showhide($page, DISP_THEME);
        } else {
            $theme = '';
        }
        $menu    = page_manage_showhide($page, DISP_MENU);
        $publish = page_manage_showhide($page, DISP_PUBLISH);

        $table->data[] = array($name, $widgets, $theme, $menu, $publish);
    }

    print_table($table);
} else {
    error(get_string('nopages', 'format_page'), $PAGE->url_build('action', 'editpage'));
}

/**
 * Local methods to assist with generating output
 * that is specific to this page
 *
 */

/**
 * This function displays the hide/show icon & link page display settings
 *
 * @param object $page Page to show the widget for
 * @param int $type a display type to show
 * @uses $CFG
 */
function page_manage_showhide($page, $type) {
    global $CFG;

    if ($page->display & $type) {
        $showhide = 'showhide=0';
        $image = 'hide';
    } else {
        $showhide = 'showhide=1';
        $image = 'show';
    }

    switch ($type) {
        case DISP_PUBLISH:
            $str = 'publish';
            break;
        case DISP_MENU:
            $str = 'menu';
            break;
        case DISP_THEME:
            $str = 'theme';
            break;
    }

    $alt = get_string($image.$str, 'format_page', strip_tags(format_string($page->nameone)));

    $return = "<a title=\"$alt\" href=\"$CFG->wwwroot/course/format/page/format.php?id=$page->courseid&amp;page=$page->id".
               "&amp;action=showhide&amp;display=$type&amp;$showhide&amp;sesskey=".sesskey().'">'.
               "<img src=\"$CFG->pixpath/i/$image.gif\" alt=\"$alt\" /></a>";

    return $return;
}

?>