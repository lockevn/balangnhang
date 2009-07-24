<?php // $Id: mysql.php,v 1.2 2007/01/05 03:17:41 mark-nielsen Exp $
/**
 * MySQL database upgrade path
 *
 * @author Mark Nielsen
 * @version $Id: mysql.php,v 1.2 2007/01/05 03:17:41 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 **/

/**
 * This function does anything necessary to upgrade 
 * older versions to match current functionality
 *
 * @param int $oldversion The old version of the gallery module that is being upgraded
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2006102800) {
        require_once($CFG->dirroot.'/mod/gallery/lib.php');
        
        // Clear out config first
        set_config('gallery_embedpath', '');
        delete_records('config', 'name', 'gallery_relativepath');
        delete_records('config', 'name', 'gallery_embedpathuri');
        
        // New permission settings, turn them on by default for teachers
        $teacher = gallery_decode_permissions($CFG->gallery_teacherpermissions);
        $teacher['rating.add']  = 1;
        $teacher['rating.view'] = 1;
        $teacher['rating.all']  = 1;
        set_config('gallery_teacherpermissions', gallery_encode_permissions($teacher));
        
        $student = gallery_decode_permissions($CFG->gallery_studentpermissions);
        // gallery_encode_permissions() will turn new ones off automatically for students
        set_config('gallery_studentpermissions', gallery_encode_permissions($student));
    }
    
    if ($oldversion < 2006102801) {
        set_config('gallery_upgrade', '2006102801');
    }

    return true;
}

?>
