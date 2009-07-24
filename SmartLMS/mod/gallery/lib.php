<?php // $Id: lib.php,v 1.7 2007/04/10 16:53:21 mark-nielsen Exp $
/**
 * Gallery Module Library of functions and constants
 *
 * @author Mark Nielsen
 * @version $Id: lib.php,v 1.7 2007/04/10 16:53:21 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 * @todo Some of the group sync could be moved to a cron.
 **/

// Handy debuging stuff when dealing with gallery classes
// echo get_class($class);
// print_object(get_class_methods($class));

/**
 * REMOVE ONCE GALLERY FIXES BUG 1325291 (IN GALLERY'S BUG TRACKER)
 **/
if (!defined("GALLERY_ADODB_FETCH_MODE")) {
    define("GALLERY_ADODB_FETCH_MODE",   "0");
}

// Set config variable defaults
if (!isset($CFG->gallery_permissions)) {
    set_config('gallery_permissions', '1');
}

if (!isset($CFG->gallery_embedpath)) {
    set_config('gallery_embedpath', '');
}

if (!isset($CFG->gallery_g2uri)) {
    set_config('gallery_g2uri', '');
}

if (!isset($CFG->gallery_studentpermissions)) {
    set_config('gallery_studentpermissions', gallery_encode_permissions(gallery_get_default_student_permissions()));
}

if (!isset($CFG->gallery_teacherpermissions)) {
    set_config('gallery_teacherpermissions', gallery_encode_permissions(gallery_get_default_teacher_permissions()));
}

/*  Notes to self (read at your own risk)

    Pondering:  I'm attempting to think of a better way to display permissions to the moodle user.
    
    Allow Comments Setting
        For ON would translate to:
            [comment] Add comments - comment.add        ON
            [comment] All access - comment.all          OFF
            [comment] Delete comments - comment.delete  OFF
            [comment] Edit comments - comment.edit      OFF
            [comment] View comments - comment.view      ON
        For Off, all of the above would be OFF
    
    Allow Create New Album
        [core] Add sub-album - core.addAlbumItem ON/OFF
    
    Allow Add Pictures
        [core] Add sub-item - core.addDataItem  ON/OFF
    
    Allow Viewing
        For ON would stranslate to:
            [core] View item - core.view                        ON
            [core] View all versions - core.viewAll             OFF
            [core] View resized version(s) - core.viewResizes   OFF
            [core] View original version - core.viewSource      OFF
        For OFF, all of the above would be OFF
    
    All of these would not be configurable and would be OFF
        All access - core.all
        [core] Change item permissions - core.changePermissions
        [core] Delete item  - core.delete
        [core] Edit item - core.edit
*/


/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted gallery record
 * @author Mark Nielsen
 **/
function gallery_add_instance($instance) {
    global $CFG, $USER;
    
    $instance->timecreated = time();
    $instance->timemodified = time();

/// permission handling
    if (!isset($instance->permissions)) {
        $instance->permissions = array();
    }
    $instance->permissions = gallery_encode_permissions($instance->permissions);

/// remember window setting
    set_user_preference('gallery_permissionsettingspref', $instance->permissionsettingspref);

    // need the id before we finnish things up
    if (!$instance->id = insert_record('gallery', $instance)) {
        return false;
    }

/// create the gallery album
    gallery_init();
    
    if ($instance->albumid) {
        // Choose an existing album
        if (empty($instance->title)) {
            unset($instance->title);
        }
        if (empty($instance->summary)) {
            unset($instance->summary);
        }
        if (empty($instance->description)) {
            unset($instance->description);
        }
        if (empty($instance->keywords)) {
            unset($instance->keywords);
        }
        gallery_update_album($instance);
    } else {
        // Creating a new one
        $instance->directory = 'moodle_'.$instance->id;    
        $instance->albumid = gallery_create_album($instance, $instance->course);
    }
    
    gallery_r();
    
    update_record('gallery', $instance);
    
    return $instance->id;
}

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_update_instance($instance) {
    global $CFG, $USER;
    
    $instance->timemodified = time();
    $instance->id = $instance->instance;
    
/// permission handling
    if (!isset($instance->permissions)) {
        $instance->permissions = array();
    }
    $instance->permissions = gallery_encode_permissions($instance->permissions);
    
    set_user_preference('gallery_permissionsettingspref', $instance->permissionsettingspref);
    
    gallery_init();
    
    if ($instance->albumid == 0) {
        // Creating a new one
        $instance->directory = 'moodle_'.$instance->id;    
        $instance->albumid = gallery_create_album($instance, $instance->course);
    } else {
        // Updating existing album
        if ($instance->oldalbumid != $instance->albumid) {
            // Switched
            if (empty($instance->title)) {
                unset($instance->title);
            }
            if (empty($instance->summary)) {
                unset($instance->summary);
            }
            if (empty($instance->description)) {
                unset($instance->description);
            }
            if (empty($instance->keywords)) {
                unset($instance->keywords);
            }
            // Permissions are album based, remove group and start from scratch
            gallery_delete_group("__MOODLE_{$instance->id}_STUDENTS__");
        }
        gallery_update_album($instance);
    }
    
    gallery_r();
    
    update_record('gallery', $instance);
    //exit();
    return true;
}

/**
 * Given an ID of an instance of this module, 
 * this function will permanently delete the instance 
 * and any data that depends on it. 
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 * @author Mark Nielsen
 **/
function gallery_delete_instance($id) {
    global $CFG;
    
    if (!$instance = get_record('gallery', 'id', $id)) {
        return false;
    }
    
/// delete associated album and groups
    gallery_init();
    $ret = GalleryCoreApi::deleteEntityById($instance->albumid);
    if ($ret) {
        // error($ret->getAsHtml()); // DEBUG
        return false;
    }
    if (!gallery_delete_group("__MOODLE_{$id}_STUDENTS__")) {
        return false;
    }
        
    gallery_r();
    
    if (! delete_records('gallery', 'id', $instance->id)) {
        return false;
    }

    return true;
}

/**
 * Return a small object with summary information about what a 
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_user_outline($course, $user, $mod, $gallery) {
    $return = null;
    
    return $return;
}

/**
 * Print a detailed representation of what a  user has done with 
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_user_complete($course, $user, $mod, $gallery) {

    return true;
}

/**
 * Given a course and a time, this module should find recent activity 
 * that has occurred in gallery activities and print it out. 
 * Return true if there was output, or false is there was none. 
 *
 * @return boolean
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such 
 * as sending out mail, toggling flags etc ... 
 *
 * @return boolean
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_cron () {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module, 
 * indexed by user.  It also returns a maximum allowed grade.
 *
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @return mixed Null or array of grades
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_grades($galleryid) {

   return NULL;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of gallery. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @return mixed boolean/array of students
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_get_participants($galleryid) {
    return false;
}

/**
 * This function returns if a scale is being used by one gallery
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @return mixed
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_scale_used ($galleryid,$scaleid) {
    $return = false;

    //$rec = get_record("gallery","id","$galleryid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}
   
    return $return;
}

/**
 * Pre-process the options form data
 *
 * Encode the review options from the setup form into the bits of $form->review
 * and other options into $form->optionflags
 * The form data is passed by reference and modified by this function
 *
 * @uses $CFG
 * @param object $form  The variables set on the form.
 * @return void
 **/
function gallery_process_options(&$form) {
    global $CFG;

    if (isset($form->module)) {
        unset($form->module);
    }
    
    if (isset($form->sesskey)) {
        unset($form->sesskey);
    }
    
    if (gallery_check_config()) {
        // Only Saving if config works
        if (!isset($form->studentpermissions)) {
            $form->studentpermissions = array();
        }

        if (!isset($form->teacherpermissions)) {
            $form->teacherpermissions = array();
        }

        $form->studentpermissions = gallery_encode_permissions($form->studentpermissions);
        $form->teacherpermissions = gallery_encode_permissions($form->teacherpermissions);
        
        if (!$form->permissions and $form->permissions != $CFG->gallery_permissions) {
            // Turning Off
            gallery_init();
            // Remove all instance groups
            if ($instances = get_records('gallery')) {
                foreach ($instances as $instance) {
                    gallery_delete_group("__MOODLE_{$instance->id}_STUDENTS__");
                }
            }
            // Remove teacher group
            gallery_delete_group("__MOODLE_TEACHERS__");
            gallery_r();
        }
    } else {
        unset($form->studentpermissions);
        unset($form->teacherpermissions);
    }
}


/**
 * Any other gallery functions go here.  Each of them must have a name that 
 * starts with gallery_
 **/

/**
 * REMOVE ONCE GALLERY FIXES BUG 1325291 (IN GALLERY'S BUG TRACKER)
 * AND ALL CALLS TO GALLERY_R()
 * AFTER EVERY CHUNK OF EXECUTED GALLERY CODE, THIS MUST BE CALLED OR
 * AT THE END OF A FUNCTION THAT USES GALLERY CODE 
 *
 * @return void
 **/
function gallery_r() {
   global $ADODB_FETCH_MODE;
   $ADODB_FETCH_MODE = GALLERY_ADODB_FETCH_MODE;
}

/**
 * Determine if a directory path to the gallery2 install is 
 * either full or relative and return the proper path.
 * 
 * If both are found to be invalid, return false.
 *
 * @uses $CFG
 * @param string $path The directory path to be used (relative or full to gallery directory)
 * @return mixed Full Directory path to gallery2's embed.php or false
 **/
function gallery_get_embedpath($path = '') {
    global $CFG;
    
    if (empty($path)) {
        $path = $CFG->gallery_embedpath;
    }
    if (empty($path)) {
        return false;
    }
    
    $embedpathrelative = $CFG->dirroot.$path.'embed.php';
    $embedpathfull     = $path.'embed.php';
    
    if (file_exists($embedpathrelative)) {
        return $embedpathrelative;
    } else if (file_exists($embedpathfull)) {
        return $embedpathfull;
    } else {
        return false; // Both are wrong
    }
}

/**
 * Checks to make sure the necessary gallery configurations
 * are set.
 *
 * @uses $CFG
 * @param string $path embedpath
 * @return boolean
 * @todo Add more checking to make sure the set values are actually valid (?)
 **/
function gallery_check_config() {
    global $CFG;
    
    if (!gallery_get_embedpath() or empty($CFG->gallery_g2uri)) {
        return false;
    } else {
        return true;
    }
}

/**
 * A upgrade hack
 *
 * @uses $CFG
 * @uses $USER
 * @return boolean
 **/
function _gallery_upgrade() {
    global $CFG, $USER;
    // Need to add the default core.viewAll permission to MGM linked albums
    if (isset($CFG->gallery_upgrade) and $CFG->gallery_upgrade == '2006102801') {
        // Login as the first admin
        $primaryadmin = get_admin();
        gallery_map_user_to_gallery($primaryadmin->id); // ensure that the admin is mapped into gallery
        GalleryEmbed::login($primaryadmin->id);
        gallery_r();
        // Correct badness from before
        if ($instances = get_records('gallery')) {
            list ($ret, $everybodygroupid) = GalleryCoreApi::getPluginParameter('module', 'core', 'id.everybodyGroup');
            if (!$ret) {
                foreach ($instances as $instance) {
                    GalleryCoreApi::addGroupPermission($instance->albumid, $everybodygroupid, 'core.viewAll', true);
                }
            }
        }
        gallery_r();
        delete_records('config', 'name', 'gallery_upgrade');
    }
    
/// log the current user back in
    GalleryEmbed::login($USER->id);
    gallery_r();
    return true;
}

/**
 * Primary function for embedding gallery into Moodle.  This function
 * should be called before the use of gallery API.
 *
 * This function initializes the gallery embed code and it
 * makes sure that the current user is mapped and that their information
 * is correct in the gallery database.
 *
 * @param int $cmid course module id of the moodle gallery instance
 * @return boolean True - if anything goes wrong, an error should be called
 * @author Mark Nielsen
 **/
function gallery_init($cmid = 0) {
    global $CFG, $USER;

    if (isguest()) {
        $userid = '';
    } else {
        $userid = $USER->id;
    }

    require_once(gallery_get_embedpath());

    $initparams = array('embedUri' => 'view.php?id='.$cmid,
                        'g2Uri' => $CFG->gallery_g2uri,
                        'loginRedirect' => $CFG->wwwroot.'/login/index.php', 
                        'activeUserId' => $userid,
                        'fullInit' => true);

    $ret = GalleryEmbed::init($initparams); 

    if ($ret && $ret && $ret->getErrorCode() & ERROR_MISSING_OBJECT) {
        // user mapping has gone bonkers - map the user
        gallery_map_user_to_gallery();
        
        // try again after user mapping
        $ret = GalleryEmbed::init($initparams);
        if ($ret) {
            error('User mapping failed twice<br /><br />'.$ret->getAsHtml());
        }
    } else if (!isguest()) {
        // user map successful
        
        // update user to make sure everything is in sync
        $ret = GalleryEmbed::updateUser($USER->id, array('username'       => $USER->username,
                                                         'email'          => $USER->email,
                                                         'fullname'       => $USER->firstname.' '.$USER->lastname,
                                                         'language'       => gallery_moodle_to_gallery_language(),
                                                         'hashedpassword' => $USER->password,
                                                         'hashmethod'     => 'md5'));
        if ($ret) {
            error('Gallery User update failed <br />'.$ret->getAsHtml());
        }
    }
    
    gallery_r();
    return true;
}

/**
 * Maps a moodle user to a gallery user
 * 
 * This should not be called if current user is guest
 *
 * @param int $userid optionally can check the mapping of a specific user (default $USER)
 * @return int Gallery user id
 * @author Mark Nielsen
 **/
function gallery_map_user_to_gallery($userid = 0) {
    global $USER;
    
    if ($userid) {
        $user = get_record('user', 'id', $userid);
    } else {
        $user = $USER;
    }
    
    $g2user = null;

    // Get the G2 user that matches the Moodle username
    list ($ret, $g2user) = GalleryCoreApi::fetchUserByUsername($user->username);
    if ($ret and !($ret && $ret->getErrorCode() & ERROR_MISSING_OBJECT)) {
        error('Gallery Core Error');  // something is wrong besides user mapping
    }
    
    $ismapped = GalleryEmbed::isExternalIdMapped($user->id, 'GalleryUser');
    
    if ($ismapped && isset($g2user)) {
        // the user is not already mapped, but exists in gallery.  Restore mapping now
        $ret = GalleryEmbed::addExternalIdMapEntry($user->id, $g2user->getId(), 'GalleryUser');
        if ($ret) {
            error('Moodle user to Gallery User Mapping Failed<br />'.$ret->getAsHtml());
        }
    } else if (!isset($g2user)) {
        // No matching G2 user found -- create one.
        $ret = GalleryEmbed::createUser($user->id, array('username'       => $user->username,
                                                         'email'          => $user->email,
                                                         'fullname'       => $user->firstname.' '.$user->lastname,
                                                         'language'       => gallery_moodle_to_gallery_language($user),
                                                         'hashedpassword' => $user->password,
                                                         'hashmethod'     => 'md5'));
        if ($ret) {
            error('Gallery User creation failed <br />'.$ret->getAsHtml());
        }
        
        // if user is admin, add them to admin group in gallery
        if (isadmin($user->id)) {
            gallery_admin_group(true, $user->username);
        }
        
        // get the user again
        list ($ret, $g2user) = GalleryCoreApi::fetchUserByUsername($user->username);
        if ($ret) {
            error('Could not fetch user after user was just created <br />'.$ret->getAsHtml());
        }
    }
    gallery_r();
    return $g2user->getId();
}

/**
 * Maps the Moodle user's language to gallery's language
 *
 * @param object moodle user object (defaults to $USER if not passed)
 * @return string gallery language
 * @author Mark Nielsen
 **/
function gallery_moodle_to_gallery_language($user = null) {
    global $gallery, $USER;
    
    if (empty($gallery)) {
        gallery_r();
        return 'en';
    }
    
    if ($user == null) {
        $user = $USER;
    }
    
    // map to user's language to gallery's lang scheme
    $translator =& $gallery->getTranslator();
    $gallerylanguages = $translator->getSupportedLanguages();
    gallery_r();

    // NOTE: this may need to be expanded to improve language mapping
    $userlang = explode('_', $USER->lang); // explode to get rid of _utf8
    if (array_key_exists($userlang[0], $gallerylanguages)) {
        return $userlang[0];
    } else {
        // default
        return 'en';
    }
}


/*********************************
 * Permission Handling Functions *
 *********************************/


/**
 * Syncronize the permissions set in moodle with gallery's
 *
 * Found that only admins can change permissions recursivly (in some cases).
 * To get around that, this function temporarily logs in a gallery admin
 * to change the permissions.  This function should be called after {@link gallery_group_sync()}
 *
 * @param int $courseid id of the course that the moodle gallery module is related
 * @param object $instance moodle gallery module instance
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_permissions_sync($courseid, $instance) {
    global $USER, $CFG;
    
    if (isadmin() or !$CFG->gallery_permissions or isguest()) {
        return true; // should be handled by group sync
    }

/// Login as the first admin
    $primaryadmin = get_admin();
    gallery_map_user_to_gallery($primaryadmin->id); // ensure that the admin is mapped into gallery
    GalleryEmbed::login($primaryadmin->id);

/// Get the correct permissions and gallery group
    if (isteacher($courseid)) {
        $permissions = gallery_decode_permissions($CFG->gallery_teacherpermissions);
        list($ret, $group) = GalleryCoreApi::fetchGroupByGroupName('__MOODLE_TEACHERS__');
        if ($ret) {
            error('Gallery teacher group not found.');
        }
    } else if (isstudent($courseid)) {
        $permissions = $instance->permissions;
        list($ret, $group) = GalleryCoreApi::fetchGroupByGroupName("__MOODLE_{$instance->id}_STUDENTS__");
        if ($ret) {
            error('Gallery student group not found.');
        }
    } else {
        // guests ???
        $permissions = array();
        $group = new stdClass;
    }
    
    //print_object($permissions);   // DEBUG
    //print_object($group);         // DEBUG
    
/// remove permissions first!
    foreach ($permissions as $permission => $on) {
        if (!$on) {
            list ($ret, $hasIt) = GalleryCoreApi::hasPermission($instance->albumid, $group->getId(), $permission);
            if ($hasIt and !$ret) {
                // it is on, but should be off, remove
                $ret = GalleryCoreApi::removeGroupPermission($instance->albumid, $group->getId(), $permission, true);
                
                if ($ret) {
                    error('Could not remove proper gallery permission.<br /><br />'.$ret->getAsHtml());
                }
            }
        }    
    }

/// add permissions
    foreach ($permissions as $permission => $on) {
        if ($on) {
            list ($ret, $hasIt) = GalleryCoreApi::hasPermission($instance->albumid, $group->getId(), $permission);
            if (!$hasIt and !$ret) {
                // does not have, but should be on, add
                $ret = GalleryCoreApi::addGroupPermission($instance->albumid, $group->getId(), $permission, true);
                
                if ($ret) {
                    error('Could not add proper gallery permission.<br /><br />'.$ret->getAsHtml());
                }
            }            
        }
    }

/// log the current user back in
    GalleryEmbed::login($USER->id);

    gallery_r();
    return true;
}

/**
 * Array of Gallery permissions
 *
 * @uses $CFG;
 * @return array
 **/
function gallery_get_permissions() {
    global $CFG;
    
    if (gallery_check_config()) {
        gallery_init();
        list ($ret, $allpermissions) = GalleryCoreApi::getPermissionIds();
        gallery_r();
        if ($ret) {
            error('Could not find Gallery permission');
        }
    } else {
        // Not configured, so cannot use Gallery's API
        $allpermissions = array('comment.add' => 'ERROR: not configured',
                                'comment.all' => 'ERROR: not configured',
                                'comment.delete' => 'ERROR: not configured',
                                'comment.edit' => 'ERROR: not configured',
                                'comment.view' => 'ERROR: not configured',
                                'core.addAlbumItem' => 'ERROR: not configured',
                                'core.addDataItem' => 'ERROR: not configured',
                                'core.all' => 'ERROR: not configured',
                                'core.changePermissions' => 'ERROR: not configured',
                                'core.delete' => 'ERROR: not configured',
                                'core.edit' => 'ERROR: not configured',
                                'core.view' => 'ERROR: not configured',
                                'core.viewAll' => 'ERROR: not configured',
                                'core.viewResizes' => 'ERROR: not configured',
                                'core.viewSource' => 'ERROR: not configured',
                                'rating.add' => 'ERROR: not configured',
                                'rating.view' => 'ERROR: not configured',
                                'rating.all' => 'ERROR: not configured',
                                );
    }
    return $allpermissions;
}

/**
 * Default Permissions for Students.  Student can Add/Edit/View Comments and View everything in the album.
 *
 * @return array
 **/
function gallery_get_default_student_permissions() {
    $permissions = gallery_get_permissions();

    $student = array();
    foreach ($permissions as $permission => $text) {
        switch ($permission) {
            case 'comment.add':
            case 'comment.edit':
            case 'comment.view':
            case 'core.view':
                $student[$permission] = 1;
                break;
            default:
                $student[$permission] = 0;
                break;
        }
    }
    
    return $student;
}

/**
 * Default Permissions for Teachers.  Teacher can do everything except change permissions. *
 *
 * @return array
 **/
function gallery_get_default_teacher_permissions() {
    $permissions = gallery_get_permissions();

    $teacher = array();
    foreach ($permissions as $permission => $text) {
        switch ($permission) {
            case 'core.all':
            case 'core.changePermissions':
                $teacher[$permission] = 0;
                break;
            default:
                $teacher[$permission] = 1;
                break;
        }
    }
    
    return $teacher;
}

/**
 * Serializes and encodes the permissions array for database storage.
 *
 * @param array $permissions An array of permissions organized like so... array('permissionname' => boolean, ...)
 * @return string The permission array serialized and encoded
 * @author Mark Nielsen
 **/
function gallery_encode_permissions($permissions) {
    foreach (gallery_get_permissions() as $permission => $text) {
        if (!array_key_exists($permission, $permissions)) {
            // not set, so turn it off
            $permissions[$permission] = 0;
        }
    }
    
    return base64_encode(serialize($permissions));
}

/**
 * Decodes and unserializes the permissions array for regular usage.
 *
 * @param string $permissions The permission array serialized and encoded
 * @return array An array of permissions organized like so... array('permissionname' => boolean, ...)
 * @author Mark Nielsen
 **/
function gallery_decode_permissions($permissions) {
    return unserialize(base64_decode($permissions));
}


/****************************
 * Group Handling Functions *
 ****************************/


/**
 * Syncronize moodle's groups with gallery's groups for this course.
 * Should be called before {@link gallery_permissions_sync()}
 *
 * This function ensures that two special groups exist to help with
 * managing permissions in Gallery. The two groups are
 *       __MOODLE_TEACHERS__
 *       __MOODLE_moduleid_STUDENTS__ (of course, one of these per instance)
 *
 * @uses $CFG
 * @uses $USER
 * @param int $courseid id of the course that the moodle gallery module is related
 * @param object $instance moodle gallery module instance
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_group_sync($courseid, $instance) {
    global $CFG, $USER;

    if (isguest()) {
        // Do not run with guests
        return true;
    }

    // get the galler user
    list($ret, $g2user) = GalleryCoreApi::fetchUserByUsername($USER->username);
    gallery_r();
    
    // mapping of groups and sync groups - not sure why we are going with this
    if ($groups = get_groups($courseid)) {
        
        foreach ($groups as $group) {
            list($ret, $gallerygroup) = GalleryCoreApi::fetchGroupByGroupName($group->name);
            
            if ($ret) {
                // Group does not exist in Gallery
                $ret = GalleryEmbed::isExternalIdMapped($group->id, 'GalleryGroup');
                        
                if ($ret && $ret->getErrorCode() & ERROR_MISSING_OBJECT) {
                    // not mapped so create
                    $ret = GalleryEmbed::createGroup($group->id, $group->name);
                } else {
                    // update - I think it might be just as fast to update without checking as it is to check then update
                    $ret = GalleryEmbed::updateGroup($group->id, array('groupname' => $group->name));
                }
                if ($ret) {
                    error($ret->getAsHtml()); // DEBUG
                    gallery_r();
                    return false;
                }
            }
        }
        
        gallery_r();
        
        // make sure that the current user is in the correct group
        if ($mygroups = user_group($courseid, $USER->id)) {
            
            foreach ($mygroups as $mygroup) {
                list($ret, $gallerygroup) = GalleryCoreApi::fetchGroupByGroupName($mygroup->name);
                list($ret, $ingroup) = GalleryCoreApi::isUserInGroup($g2user->getId(), $gallerygroup->getId());
                gallery_r();
            
                if (!$ingroup) {
                    foreach($groups as $group) {
                        GalleryEmbed::removeUserFromGroup($USER->id, $group->id);
                    }

                    $ret = GalleryEmbed::addUserToGroup($USER->id, $mygroup->id);    
                    if ($ret) {
                        // error($ret->getAsHtml()); // DEBUG
                        gallery_r();
                        return false;
                    }
                }
            }
        }
    }
    
/// make sure the necessary groups exist (check every time because a silly admin could delete them!)
    list($ret, $moodleteachers) = GalleryCoreApi::fetchGroupByGroupName('__MOODLE_TEACHERS__');
    if ($ret) {
        // teacher group does not exist - create it
        gallery_create_group('__MOODLE_TEACHERS__');
        // try again
        list($ret, $moodleteachers) = GalleryCoreApi::fetchGroupByGroupName('__MOODLE_TEACHERS__');
    }
    
    list($ret, $moodlestudents) = GalleryCoreApi::fetchGroupByGroupName("__MOODLE_{$instance->id}_STUDENTS__");
    if ($ret) {
        // student group does not exist - create it
        gallery_create_group("__MOODLE_{$instance->id}_STUDENTS__");
        // try again
        list($ret, $moodlestudents) = GalleryCoreApi::fetchGroupByGroupName("__MOODLE_{$instance->id}_STUDENTS__");
    }
    
/// Remove any gallery groups that no longer exist in moodle

    // Get the an array of all mapped external ids
    $externalidmap = GalleryEmbed::getExternalIdMap('entityId');

    if ($moodlegroups = get_records('groups')) {
        foreach ($externalidmap[1] as $map) {
            if ($map['entityType'] == 'GalleryGroup') {
                if (!array_key_exists($map['externalId'], $moodlegroups)) {
                    // does not exist any more, trash it
                    $ret = GalleryEmbed::deleteGroup($map['externalId']);

                    if ($ret) {
                        // error($ret->getAsHtml()); // DEBUG
                        gallery_r();
                        return false;
                    }
                }
            }
        }
    }
    gallery_r();
    
    // Make sure if current user is admin, that s/he is in gallery's admin group, otherwise remove them
    list($ret, $flag) = GalleryCoreApi::isUserInSiteAdminGroup();
    if (isadmin() and !$flag) {
        gallery_admin_group(); // add
    } else if (!isadmin() and $flag) {
        gallery_admin_group(false); // remove
    }
    
    // Now add to our special groups (not in moodle) if permission handling is on
    if ($CFG->gallery_permissions and !isadmin()) {

        if (isteacher($courseid)) {
    
        /// remove user from student group
            list($ret, $result) = GalleryCoreApi::isUserInGroup($g2user->getId(), $moodlestudents->getId());
            if ($result) {
                $ret = GalleryCoreApi::removeUserFromGroup($g2user->getId(), $moodlestudents->getId());
            }
        /// add user to teacher group
            list($ret, $result) = GalleryCoreApi::isUserInGroup($g2user->getId(), $moodleteachers->getId());
            if (!$result) {
                $ret = GalleryCoreApi::addUserToGroup($g2user->getId(), $moodleteachers->getId());
            }
        } else if (isstudent($courseid)) {
    
        /// remove user from teacher group
            list($ret, $result) = GalleryCoreApi::isUserInGroup($g2user->getId(), $moodleteachers->getId());
            if ($result) {
                $ret = GalleryCoreApi::removeUserFromGroup($g2user->getId(), $moodleteachers->getId());
            }
        /// add user to student group
            list($ret, $result) = GalleryCoreApi::isUserInGroup($g2user->getId(), $moodlestudents->getId());
            if (!$result) {
                $ret = GalleryCoreApi::addUserToGroup($g2user->getId(), $moodlestudents->getId());
            }
        }
    }

    gallery_r();
    return true;
}

/**
 * Creates a group in gallery
 *
 * @param string $groupname name of the group to be created
 * @return True Success (errors otherwise)
 * @author Mark Nielsen
 **/
function gallery_create_group($groupname) {
    list ($ret, $group) = GalleryCoreApi::newFactoryInstance('GalleryEntity', 'GalleryGroup');
    if ($ret) {
        error('Gallery Group Creation Error.<br /><br />'.$ret->getAsHtml());
    }
    if (!isset($group)) {
        error('Gallery Group Creation Error.<br /><br />'.$ret->getAsHtml());
    }
    $ret = $group->create($groupname);
    if ($ret) {
        error('Gallery Group Creation Error.<br /><br />'.$ret->getAsHtml());
    }
    $ret = $group->save();
    if ($ret) {
        error('Gallery Group Creation Error.<br /><br />'.$ret->getAsHtml());
    }
    
    return true;
}

/**
 * Deletes a group in gallery
 *
 * @param string $groupname the name of the group to delete
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_delete_group($groupname) {
    list($ret, $group) = GalleryCoreApi::fetchGroupByGroupName($groupname);
    if (!$ret) {
        $ret = GalleryCoreApi::deleteEntityById($group->getid());
        if ($ret) {
            // error($ret->getAsHtml()); // DEBUG
            return false;
        }
    }
    
    return true;
}

/**
 * Add or remove a user from the gallery admin group
 *
 * @param boolean $add if true = add user to admin, if false = remove user from admin.  Default true
 * @param string $username username of the user to add (Defaults to $USER->username)
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_admin_group($add = true, $username = '') {
    global $USER;

    if (empty($username)) {
        $username = $USER->username;
    }
    
    list ($ret, $g2_user) = GalleryCoreApi::fetchUserByUsername($username);
    if ($ret && !($ret && $ret->getErrorCode() & ERROR_MISSING_OBJECT)) {
        gallery_r();
        return false;
    }
    list ($ret, $adminGroupId) = GalleryCoreApi::getPluginParameter('module', 'core', 'id.adminGroup');
    if ($ret) {
        gallery_r();
        return false;
    }
    
    if ($add) {
        $ret = GalleryCoreApi::addUserToGroup($g2_user->getId(), $adminGroupId);
    } else {
        $ret = GalleryCoreApi::removeUserFromGroup($g2_user->getId(), $adminGroupId);
    }
    gallery_r();
    if ($ret) {
        //error($ret->getAsHtml());  // DEBUG
        return false;
    } else {
        return true;
    }
}


/****************************
 * Album Handling Functions *
 ****************************/


/**
 * Creates a album in gallery.
 *
 * @param object $albuminfo with the following set:
 *                     stdClass Object
 *                     (
 *                         [parentid] => ...        NOTE: parentid optional.  If not passed, root is assumed
 *                         [ownerid] => ...         NOTE: ownerid optional.  If not passed, main teacher is used
 *                         [directory] => ...
 *                         [title] => ...
 *                         [summary] => ...
 *                         [description] => ...
 *                         [keywords] => ...
 *                     )
 * @param int $courseid The id of the course that contains the moodle gallery instance
 * @return int The id of the newly created gallery album
 * @author Mark Nielsen
 **/
function gallery_create_album($albuminfo, $courseid) {
    global $USER;
    
    // get root album id
    if (empty($albuminfo->parentid)) {
        list ($ret, $albuminfo->parentid) = GalleryCoreApi::getPluginParameter('module', 'core', 'id.rootAlbum');
    }
    
    // create the album
    list($ret, $album) = GalleryCoreApi::createAlbum($albuminfo->parentid,          // parent album id
                                                     $albuminfo->directory,         // directory name for 
                                                     $albuminfo->title,             // album title
                                                     $albuminfo->summary,           // summary
                                                     $albuminfo->description,       // description
                                                     $albuminfo->keywords           // keywords for searching
                                                    );
    
    if ($ret) {
        error('Album creation failed<br /><br />'.$ret->getAsHtml());
    }
    
    $albumid = $album->getId();
    
    gallery_r();
    
    // get the gallery user id of the main teacher
    if (!empty($albuminfo->ownerid)) {
        $ownerid = $albuminfo->ownerid;
    } else if ($mainteacher = get_teacher($courseid)) {
        $ownerid = gallery_map_user_to_gallery($mainteacher->id);
    } else {
        $ownerid = gallery_map_user_to_gallery();
    }
    
    // set the owner: lock, set, save, release lock
    list ($ret, $lockid) = GalleryCoreApi::acquireWriteLock($albumid);
    $album->setOwnerId($ownerid);
    $ret = $album->save();
    GalleryCoreApi::releaseLocks($lockid);

    //if ($ret) {  // Debug
    //    error($ret->getAsHtml());
    //}

    return $albumid;
}

/**
 * Update a Gallery Album
 *
 * @param object $albuminfo with the following set:
 *                     stdClass Object
 *                     (
 *                         [title] => ...
 *                         [summary] => ...
 *                         [description] => ...
 *                         [keywords] => ...
 *                     )
 * @return boolean Success/Fail
 * @author Mark Nielsen
 * @todo change owner of album if master teacher changes function remapOwnerId($oldUserId, $newUserId) may help
 *       Actually check it update fails
 **/
function gallery_update_album($albuminfo) {
    
    list($ret, $album) = GalleryCoreApi::loadEntitiesById($albuminfo->albumid);
    list ($ret, $lockid) = GalleryCoreApi::acquireWriteLock($album->getId());
    
    if (isset($albuminfo->title)) {
        $album->settitle($albuminfo->title);
    }
    if (isset($albuminfo->summary)) {
        $album->setsummary($albuminfo->summary);
    }
    if (isset($albuminfo->description)) {
        $album->setdescription($albuminfo->description);
    }
    if (isset($albuminfo->keywords)) {
        $album->setkeywords($albuminfo->keywords);
    }
    
    $album->save();
    
    GalleryCoreApi::releaseLocks($lockid);
    
    gallery_r();
    return true;
}

?>
